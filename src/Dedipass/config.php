<?php

use App\Dedipass\Actions\DedipassApiAction;
use App\Dedipass\Actions\DedipassIndexAction;
use App\Dedipass\DedipassCustomerItem;
use App\Dedipass\DedipassPaymentType;
use App\Dedipass\DedipassSettings;
use ClientX\Navigation\DefaultMainItem;
use function ClientX\setting;
use function DI\add;
use function DI\autowire;
use function DI\get;

return [
    'csrf.except' => add(['dedipass.api']),
    'admin.settings' => add(get(DedipassSettings::class)),
    'admin.customer.items' => add(get(DedipassCustomerItem::class)),
    'payments.type' => add(get(DedipassPaymentType::class)),
    'payment.boards' => add(get(\App\Dedipass\DedipassPaymentBoard::class)),
    'navigation.main.items' => add([new DefaultMainItem([DefaultMainItem::makeItem('Dedipass', 'dedipass', 'fa fa-mobile', false, true)], 80)]),
    DedipassIndexAction::class => autowire()->constructorParameter('dedipassPublic', setting('dedipass_public', '')),
    DedipassApiAction::class => autowire()
        ->constructorParameter('publicKey', setting('dedipass_public', ''))
        ->constructorParameter('privateKey', setting('dedipass_secret', '')),
];
