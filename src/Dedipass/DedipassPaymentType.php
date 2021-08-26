<?php


namespace App\Dedipass;


use ClientX\Payment\PaymentManagerInterface;

class DedipassPaymentType implements \ClientX\Payment\PaymentTypeInterface
{

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return "dedipass";
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): ?string
    {
        return "Dedipass";
    }

    /**
     * @inheritDoc
     */
    public function getManager(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getLogPath(): string
    {
        return "dedipass.admin.index";
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): string
    {
        return "fas fa-mobile-alt";
    }

    /**
     * @inheritDoc
     */
    public function canPayWith(): bool
    {
        return false;
    }
}