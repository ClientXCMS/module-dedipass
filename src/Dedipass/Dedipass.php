<?php

namespace App\Dedipass;

class Dedipass
{
    public int $id;
    public string $code;
    public int $userId;
    public string $status;
    public string $identifier;
    public \DateTime $createdAt;

    /**
     * @throws \Exception
     */
    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = new \DateTime($createdAt);
    }

    public function statusBadge():string{
        $status = strtoupper($this->status);
        $color = $this->status == 'success' ? 'success' : 'danger';
        return "<span class='badge badge-$color'>$status</span>";
    }

}