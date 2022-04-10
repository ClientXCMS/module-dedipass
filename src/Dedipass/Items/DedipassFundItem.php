<?php

namespace App\Dedipass\Items;

use App\Auth\DatabaseUserAuth;
use App\Dedipass\Database\DedipassTable;
use ClientX\Navigation\NavigationItemInterface;
use ClientX\Renderer\RendererInterface;
use ClientX\Session\SessionInterface;

class DedipassFundItem implements NavigationItemInterface
{
    private DedipassTable $table;
    private SessionInterface $session;
    private DatabaseUserAuth $auth;
    private string $dedipassPublic;

    public function __construct(
        DedipassTable $table,
        DatabaseUserAuth $auth,
        SessionInterface $session,
        string $dedipassPublic
    )
    {
        $this->auth = $auth;
        $this->table = $table;
        $this->session = $session;
        $this->dedipassPublic = $dedipassPublic;
    }
    public function render(RendererInterface $renderer):string
    {
        $items = $this->table->findForUser($this->auth->getUser()->getId());
        $public = $this->dedipassPublic;
        return $renderer->render("@Dedipass/index", compact('items', 'public'));
    }

    public function getPosition():int
    {
        return 80;
    }
}