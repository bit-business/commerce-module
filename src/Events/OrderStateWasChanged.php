<?php

namespace NadzorServera\Skijasi\Module\Commerce\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NadzorServera\Skijasi\Models\User;
use NadzorServera\Skijasi\Module\Commerce\Models\Order;


class OrderStateWasChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $order;
    public $status;
    public $pdfPath;

    public function __construct(User $user, Order $order, string $status, ?string $pdfPath = null)
    {
        $this->user = $user;
        $this->order = $order;
        $this->status = $status;
        $this->pdfPath = $pdfPath;
    }
}