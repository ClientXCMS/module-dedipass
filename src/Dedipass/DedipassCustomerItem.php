<?php

namespace App\Dedipass;

use App\Auth\User;
use App\Dedipass\Database\DedipassTable;
use ClientX\Navigation\NavigationItemInterface;
use ClientX\Renderer\RendererInterface;

class DedipassCustomerItem implements \ClientX\Navigation\NavigationItemInterface
{

    /**
     * @var \App\Auth\User
     */
    private User $user;
    private DedipassTable $dedipassTable;

    public function __construct(DedipassTable $dedipassTable)
    {
        $this->dedipassTable = $dedipassTable;
    }

    public function getPosition(): int
    {
        return 12;
    }

    public function render(RendererInterface $renderer): string
    {
        $items = $this->dedipassTable->findForUser($this->user->getId())->fetchAll();
        return $renderer->render("@Dedipass_admin/customer", ['items' => $items]);
    }

    public function setUser(User $user){
        $this->user = $user;
    }
}