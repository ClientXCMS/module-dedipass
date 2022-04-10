<?php

use App\Dedipass\Actions\DedipassApiAction;
use App\Dedipass\DedipassCustomerItem;
use App\Dedipass\DedipassPaymentBoard;
use App\Dedipass\DedipassPaymentType;
use App\Dedipass\DedipassSettings;
use App\Dedipass\Items\DedipassFundItem;
use function ClientX\setting;
use function DI\add;
use function DI\autowire;
use function DI\get;

return [
    'csrf.except' => add(['dedipass.api']),
    'admin.settings' => add(get(DedipassSettings::class)),
    'admin.customer.items' => add(get(DedipassCustomerItem::class)),
    'payments.type' => add(get(DedipassPaymentType::class)),
    'addfund.types' => add(get(DedipassFundItem::class)),
    'payment.boards' => add(get(DedipassPaymentBoard::class)),
    DedipassFundItem::class => autowire()->constructorParameter('dedipassPublic', setting('dedipass_public', '')),
    DedipassApiAction::class => autowire()
        ->constructorParameter('publicKey', setting('dedipass_public', ''))
        ->constructorParameter('privateKey', setting('dedipass_secret', '')),
];
