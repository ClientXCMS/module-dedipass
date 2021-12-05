<?php


namespace App\Dedipass\Actions;

use App\Auth\Database\UserTable;
use App\Dedipass\Database\DedipassTable;
use App\Shop\Services\TransactionService;
use Carbon\Carbon;
use ClientX\Actions\Action;
use ClientX\Database\NoRecordException;
use ClientX\Exception\JsonEncoderException;
use ClientX\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class DedipassApiAction extends Action
{

    private TransactionService $service;
    private string $publicKey;
    private string $privateKey;
    /**
     * @var \App\Dedipass\Database\DedipassTable
     */
    private DedipassTable $table;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var \App\Auth\Database\UserTable
     */
    private UserTable $userTable;

    /**
     * DedipassApiAction constructor.
     * @param \ClientX\Router $router
     * @param \App\Dedipass\Database\DedipassTable $table
     * @param \Psr\Log\LoggerInterface $logger
     * @param string $publicKey
     * @param string $privateKey
     */
    public function __construct(Router $router, DedipassTable $table, UserTable $userTable, LoggerInterface $logger, string $publicKey, string $privateKey)
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->router = $router;
        $this->table = $table;
        $this->logger = $logger;
        $this->userTable = $userTable;
    }

    /**
     * @throws JsonEncoderException
     */
    public function __invoke(ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
    {
        $params = $request->getParsedBody();
        $code = isset($params['code']) ? preg_replace('/[^a-zA-Z0-9]+/', '', $params['code']) : '';
        if (empty($code) || array_key_exists('custom', $params) == false) {
            return $this->json(['status' => 'error', 'message' => 'No code or no custom'], 500);
        }
        if ($this->existCode($code)) {
            return $this->json(['status' => 'error', 'message' => 'Payment already completed'], 500);
        }
        if ($params['private_key'] != $this->privateKey) {
            return $this->json(['status' => 'error', 'message' => 'Invalid private key'], 500);
        }
        $this->makeTransaction($params);
        return $this->json(['success' => true, 'params' => $params]);
    }

    private function existCode(string $code): bool
    {
        try {
            /** @var \App\Shop\Entity\Transaction $result */
            $this->table->findBy("code", $code);
            return true;
        } catch (NoRecordException $e) {
            return false;
        }
    }

    public function json($data, int $status = 200, array $headers = []): ResponseInterface
    {
        if ($status == 500) {
            $this->logger->critical("Dedipass : " . $data['message']);
        } else {
            $this->logger->info("Dedipass : " . $data['message']);
        }
        return parent::json($data, $status, $headers);
    }

    private function makeTransaction(array $params)
    {
        if ($params['status'] == 'success'){
            $money = $params['virtual_currency'];
            /** @var \App\Account\User $user */
            $user = $this->userTable->find($params['custom']);
            $user->addFund($money);
            $this->userTable->updateWallet($user);
        }
        $this->table->insert([
            'user_id' => $params['custom'],
            'code' => $params['code'],
            'payout' => $params['payout'],
            'amount' => $params['money'],
            'status' => $params['status'],
            'identifier' => $params['identifier']
        ]);
    }
}
