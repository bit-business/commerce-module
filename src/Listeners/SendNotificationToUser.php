<?php

namespace NadzorServera\Skijasi\Module\Commerce\Listeners;

use Illuminate\Support\Facades\Mail;
use NadzorServera\Skijasi\Models\Notification;
use NadzorServera\Skijasi\Models\User;
use NadzorServera\Skijasi\Module\Commerce\Events\OrderStateWasChanged;
use NadzorServera\Skijasi\Module\Commerce\Mail\MailNotificationOrder;

class SendNotificationToUser
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(OrderStateWasChanged $event)
    {
        $sender_user = User::when(env('NOTIFICATION_SENDER_EMAIL'), function ($query, $email) {
            return $query->where('email', $email);
        }, function ($query) {
            return $query;
        })->firstOrFail();

        $status = $event->status;
        $title = null;
        $content = null;
        $is_read = 0;

        switch ($status) {
            case 'waitingBuyerPayment':
                $title = 'Uspješno ste napravili narudžbu';
                $content = "Čestitamo broj Vaše narudžbe je: #{$event->order->id}. Podaci za plaćanje su ispod:";
                break;
            case 'waitingSellerConfirmation':
                $title = 'Čeka se potvrda plaćanja';
                $content = "Čestitamo broj Vaše narudžbe je:  #{$event->order->id}. Provjeravamo Vašu uplatu, te ćemo Vam ubrzo javiti čim potvrdimo uplatu.";
                break;
            case 'process':
                $title = 'Order is being processed';
                $content = "Your order with number #{$event->order->id} is being processed. The seller will immediately send your package.";
                break;
            case 'delivering':
                $title = 'Order has been sent';
                $content = "Your order with number #{$event->order->id} is on the way. You can check the tracking number on Order Detail page";
                break;
            case 'done':
                $title = 'Order has arrived';
                $content = "Your order with number #{$event->order->id} has shipped. Thanks so much for shopping with us.";
                break;
            case 'cancel':
                $title = 'Order has been cancelled';
                $content = "Your order with number #{$event->order->id} has been cancelled.";
                break;
            default:
                return;
        }

        Mail::to($event->user->email)->send(new MailNotificationOrder($event->user, $title, $content));
        Notification::create([
            'receiver_user_id' => $event->user->id,
            'type' => 'orderNotification',
            'title' => $title,
            'content' => $content,
            'is_read' => $is_read,
            'sender_user_id' => $sender_user->id,
        ]);
    }
}
