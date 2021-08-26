<?php


namespace App\Dedipass;

use App\Dedipass\Actions\DedipassAdminAction;
use App\Dedipass\Actions\DedipassApiAction;
use App\Dedipass\Actions\DedipassIndexAction;
use ClientX\Renderer\RendererInterface;
use ClientX\Router;
use ClientX\Theme\ThemeInterface;
use Psr\Container\ContainerInterface;

class DedipassModule extends \ClientX\Module
{

    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(Router $router, RendererInterface $renderer, ThemeInterface $theme, ContainerInterface $container)
    {
        $renderer->addPath('Dedipass', $theme->getViewsPath() . '/dedipass');
        $renderer->addPath('Dedipass_admin', __DIR__ . '/Views');
        $router->get($container->get('clientarea.prefix') . '/dedipass', DedipassIndexAction::class, 'dedipass');
        $router->crud($container->get('admin.prefix') . '/dedipass', DedipassAdminAction::class, 'dedipass.admin');
        $router->post('/api/dedipass', DedipassApiAction::class, 'dedipass.api');
    }
}
