<?php


namespace App\Dedipass\Actions;

use App\Auth\DatabaseUserAuth;
use App\Dedipass\Database\DedipassTable;
use ClientX\Actions\Action;
use ClientX\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class DedipassIndexAction extends Action
{

    private string $dedipassPublic;
    /**
     * @var \App\Dedipass\Database\DedipassTable
     */
    private DedipassTable $dedipassTable;

    /**
     * @param \ClientX\Renderer\RendererInterface $renderer
     * @param \App\Dedipass\Database\DedipassTable $dedipassTable
     * @param \App\Auth\DatabaseUserAuth $auth
     * @param string $dedipassPublic
     */
    public function __construct(RendererInterface $renderer, DedipassTable $dedipassTable, DatabaseUserAuth $auth, string $dedipassPublic)
    {
        $this->renderer = $renderer;
        $this->dedipassPublic = $dedipassPublic;
        $this->dedipassTable = $dedipassTable;
        $this->auth = $auth;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $page = $request->getQueryParams()['p'] ?? 1;
        $items = $this->dedipassTable->findForUser($this->getUserId())->paginate(12, $page);
        return $this->render("@Dedipass/index", ['public' => $this->dedipassPublic, 'items' => $items]);
    }
}
