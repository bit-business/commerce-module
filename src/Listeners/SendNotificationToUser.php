<?php

namespace NadzorServera\Skijasi\Module\Commerce\Listeners;

use Illuminate\Support\Facades\Mail;
use NadzorServera\Skijasi\Models\Notification;
use NadzorServera\Skijasi\Models\User;
use NadzorServera\Skijasi\Module\Commerce\Events\OrderStateWasChanged;
use NadzorServera\Skijasi\Module\Commerce\Mail\MailNotificationOrder;

use Illuminate\Support\Facades\Log;

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
                $content = "Čestitamo broj Vaše narudžbe je: {$event->order->id}. Podaci za plaćanje su na uplatnici u prilogu.\n"
                    . "\nMoguće je uplatiti:\n"
                    . "\n1. Internet bankarstvom\n"
                    . "2. Skeniranjem koda na uplatnici mobilnim bankarstvom\n"
                    . "3. Uplatnicom u poslovnici banke ili pošte\n\n"
                    . "Prije plaćanja molimo provjerite da li su ispravni podaci.";
                break;
            case 'waitingSellerConfirmation':
                $title = 'Čeka se potvrda plaćanja';
                $content = "Čestitamo broj Vaše narudžbe je: {$event->order->id}. Provjeravamo Vašu uplatu, te ćemo Vam se ubrzo javiti čim potvrdimo uplatu.";
                break;
            case 'process':
                $title = 'Vaša kupnja je u obradi';
                $content = "Vaša narudžba sa brojem: {$event->order->id} je u obradi. ";
                break;
            case 'delivering':
                $title = 'Informacije o vašoj narudžbi';
                $content = "Imamo poruku vezanu za vašu narudžbu broj: {$event->order->id}. ";
                break;
            case 'done':
                $title = 'Vaša kupnja je potvrđena';
                $content = "Vaša narudžba na HZUTS web stranici pod brojem: {$event->order->id} je upješno potvrđena. Hvala Vam na kupnji!";
                break;
            case 'cancel':
                $title = 'Narudžba je odbijena';
                $content = "Vaša narudžba broj: {$event->order->id} je odbijena. {$event->order->cancel_message} Ukoliko mislite da je ovo greška, molimo Vas kontaktirajte nas.";
                break;
            default:
                return;
        }

  
            if ($status === 'waitingBuyerPayment' && $event->pdfPath) {
              
                Mail::to($event->user->email)->send(new MailNotificationOrder($event->user, $title, $content, $event->pdfPath));
              
            } else {
                Mail::to($event->user->email)->send(new MailNotificationOrder($event->user, $title, $content));
            }


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
