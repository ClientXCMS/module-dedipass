<?php


namespace App\Dedipass;


use App\Shop\Entity\Recurring;
use DateTime;

class Dedipass implements \App\Shop\Entity\Orderable
{

    private bool $isTest;
    private int $payout;

    public function __construct(bool $isTest, int $payout)
    {
        $this->isTest = $isTest;
        $this->payout = $payout;
    }

    public function getId(): ?int
    {
        return 0;
    }

    public function getName(): ?string
    {
        if ($this->isTest) {
            return "Dedipass (testmode)";
        }
        return "Dedipass";
    }

    public function getDescription(): ?string
    {
        return null;
    }

    public function getPrice(string $recurring = Recurring::MONTHLY, bool $setup = false, array $options = [])
    {
        return $this->payout;
    }

    public function inStock(): bool
    {
        return true;
    }

    public function getRecurring(): Recurring
    {
        return Recurring::onetime();
    }

    public function getPaymentType(): string
    {
        return "onetime";
    }

    public function hasAutoterminate(): bool
    {
        return true;
    }

    public function canRecurring(): bool
    {
        return false;
    }

    public function getAutoTerminateAt(): ?DateTime
    {
        return null;
    }

    public function getExpireAt(): ?DateTime
    {
        return null;
    }

    public function getTable(): string
    {
        return "";
    }

    public function getType(): string
    {
        return "dedipass";
    }
}