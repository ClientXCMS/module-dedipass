<?php


namespace App\Dedipass\Actions;


use ClientX\Actions\Action;
use ClientX\Renderer\RendererInterface;

class DedipassIndexAction extends Action
{

    private string $dedipassPublic;

    public function __construct(RendererInterface $renderer, string $dedipassPublic)
    {
        $this->renderer = $renderer;
        $this->dedipassPublic = $dedipassPublic;
    }

    public function __invoke()
    {
        return $this->render("@Dedipass/index", ['public' => $this->dedipassPublic]);
    }
}