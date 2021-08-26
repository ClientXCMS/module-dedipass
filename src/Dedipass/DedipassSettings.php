<?php


namespace App\Dedipass;


use ClientX\Renderer\RendererInterface;
use ClientX\Validator;

class DedipassSettings implements \App\Admin\Settings\SettingsInterface
{

    public function name(): string
    {
        return "dedipass";
    }

    public function title(): string
    {
        return "Dedipass";
    }

    public function icon(): string
    {
        return "fas fa-mobile";
    }

    public function render(RendererInterface $renderer)
    {
        return $renderer->render('@Dedipass_admin/settings');
    }

    public function validate(array $params): Validator
    {
        return (new Validator($params))->notEmpty('dedipass_public', 'dedipass_secret');
    }
}