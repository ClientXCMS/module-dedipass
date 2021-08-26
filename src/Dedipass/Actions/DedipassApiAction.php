<?php


namespace App\Dedipass\Actions;


use App\Account\User;
use App\Auth\Database\UserTable;
use App\Dedipass\Dedipass;
use App\Shop\Database\TransactionTable;
use App\Shop\Entity\Transaction;
use App\Shop\Entity\TransactionItem;
use App\Shop\Services\TransactionService;
use Carbon\Carbon;
use ClientX\Actions\Action;
use ClientX\Database\NoRecordException;
use ClientX\Exception\JsonEncoderException as JsonEncoderExceptionAlias;
use ClientX\Router;
use GuzzleHttp\Client;
use Psr\Http\Message\ServerRequestInterface;

class DedipassApiAction extends Action
{

    private TransactionTable $table;
    private TransactionService $service;
    private string $publicKey;
    private string $privateKey;
    /**
     * @var \App\Auth\Database\UserTable
     */
    private UserTable $userTable;

    /**
     * DedipassApiAction constructor.
     * @param \App\Shop\Services\TransactionService $service
     * @param \ClientX\Router $router
     * @param \App\Auth\Database\UserTable $userTable
     * @param string $publicKey
     * @param string $privateKey
     */
    public function __construct(TransactionService $service, Router $router, UserTable $userTable, string $publicKey, string $privateKey)
    {
        $this->service = $service;
        $this->table = $service->getTable();
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->userTable = $userTable;
        $this->router = $router;
    }

    /**
     * @throws JsonEncoderExceptionAlias
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $code = isset($params['code']) ? preg_replace('/[^a-zA-Z0-9]+/', '', $params['code']) : '';
        if (empty($code) || array_key_exists('custom', $params) == false) {
            return $this->json(['status' => 'error', 'message' => 'No code or no custom'], 500);
        }
        if ($this->existCode($code)) {
            return $this->json(['status' => 'error', 'message' => 'Payment already completed'], 500);
        }
        $fetch = (new Client())->post("http://api.dedipass.com/v1/pay/?public_key=" . $this->publicKey . "&private_key=" . $this->privateKey . "&code=" . $code . "'");
        $response = json_decode($fetch->getBody()->__toString(), true);
        $response['custom'] = $params['custom'];
        $transactionId = $this->makeTransaction($response);
        if ($response['status'] == 'success') {
            $this->accept($transactionId);
        } else {
            $this->reject($transactionId, $response['message']);
        }
        return $this->redirectToRoute('shop.transaction.show', ['id' => $transactionId]);

    }

    private function existCode(string $code)
    {
        try {
            /** @var \App\Shop\Entity\Transaction $result */
            $result = $this->table->findBy("transaction_id", $code);
            if (Carbon::createFromTimestamp($result->getCreatedAt()->format('U'))->subMinute()->isPast()) {
                return false;
            }
            return true;
        } catch (NoRecordException $e) {
            return false;
        }
    }

    private function makeTransaction(array $params)
    {
        $dedipass = new Dedipass($params['rate'] === 'TEST-MODE', $params['payout']);
        $transaction = new Transaction();
        $item = new TransactionItem();
        $item->setOrderable($dedipass);
        $item->typeId = 0;
        $item->setName($dedipass->getName());
        $item->setPrice($dedipass->getPrice());
        $item->setVat($this->service->getPayment()->getFromName("dedipass")->taxAdded);
        $transaction->setCurrency($this->service->getCurrency())
            ->setPrice($params['virtual_currency'] ?? 0)
            ->setUserId($params['custom'])
            ->setTransactionId($params['code'])
            ->setPaymentType("dedipass")
            ->setItems([$item]);
        return $this->table->insertTransaction($transaction);
    }

    private function accept(int $transactionId)
    {
        try {
            $transaction = $this->service->findTransaction($transactionId);
            /** @var User */
            $user = $this->userTable->find($transaction->getUserId());
            $user->addFund($transaction->getPrice());
            $this->userTable->updateWallet($user);
            $transaction->setState(Transaction::COMPLETED);
            $item = $transaction->getItems()[0];
            $item->delivre();
            $this->service->changeState($transaction);
            return true;
        } catch (NoRecordException $e) {
            return false;
        }
    }

    private function reject(int $transactionId, $message)
    {

        try {
            $transaction = $this->service->findTransaction($transactionId);
            $transaction->setState(Transaction::REFUSED);
            $transaction->setReason($message);
            $item = $transaction->getItems()[0];
            $item->cancel();
            $this->service->changeState($transaction);
            return true;
        } catch (NoRecordException $e) {
            return false;
        }
    }

}