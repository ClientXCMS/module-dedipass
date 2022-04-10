<?php


namespace App\Dedipass;

use App\Dedipass\Actions\DedipassAdminAction;
use App\Dedipass\Actions\DedipassApiAction;
use App\Fund\FundModule;
use ClientX\Helpers\Str;
use ClientX\ModuleCache;
use ClientX\Renderer\RendererInterface;
use ClientX\Router;
use ClientX\Session\FlashService;
use ClientX\Session\SessionInterface;
use ClientX\Theme\ThemeInterface;
use Psr\Container\ContainerInterface;
use function ClientX\request;

class DedipassModule extends \ClientX\Module
{

    const DEFINITIONS = __DIR__ . '/config.php';
    const MIGRATIONS = __DIR__ . '/db/migrations';

    public function __construct(Router $router, RendererInterface $renderer, ThemeInterface $theme, ContainerInterface $container)
    {
        $renderer->addPath('Dedipass', $theme->getViewsPath() . '/Dedipass');
        $renderer->addPath('Dedipass_admin', __DIR__ . '/Views');
        $router->crud($container->get('admin.prefix') . '/dedipass', DedipassAdminAction::class, 'dedipass.admin');
        $router->post('/api/dedipass', DedipassApiAction::class, 'dedipass.api');
        $modules = (new ModuleCache())->getModulesEnabled();
        if (!in_array(FundModule::class, $modules)) {
            $session = $container->get(SessionInterface::class);
            if (Str::startsWith(request()->getUri()->getPath(), '/admin')) {
                (new FlashService($session))->error('The Dedipass Module require the Fund Module to work');
            }
        }
    }
}
