<?php

namespace NadzorServera\Skijasi\Module\Commerce\Listeners;

use Illuminate\Support\Facades\Mail;
use NadzorServera\Skijasi\Models\Notification;
use NadzorServera\Skijasi\Models\User;
use NadzorServera\Skijasi\Module\Commerce\Events\OrderStateWasChanged;
use NadzorServera\Skijasi\Module\Commerce\Mail\MailNotificationOrder;

use App\Models\AdminMessage; 

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
                $content = "Vaša narudžba na HZUTS web stranici pod brojem: {$event->order->id} je uspješno potvrđena. Hvala Vam na kupnji!";
                // Send admin message
                $this->sendAdminMessage($event->user->id, $title, $content);
                break;
            case 'cancel':
                $title = 'Narudžba je odbijena';
                $content = "Vaša narudžba broj: {$event->order->id} je odbijena. {$event->order->cancel_message} Ukoliko mislite da je ovo greška, molimo Vas kontaktirajte nas.";
                $this->sendAdminMessage($event->user->id, $title, $content);
                break;
            default:
                return;
        }
    
        // // Check for existing notifications within the last 2 minutes
        // $existingNotification = Notification::where('receiver_user_id', $event->user->id)
        //     ->where('type', 'orderNotification')
        //     ->where('title', $title)
        //     ->where('content', $content)
        //     ->where('created_at', '>=', now()->subMinutes(1)) // Only check notifications sent within the last 2 minutes
        //     ->first();
    
        // // If a notification was sent within the last 2 minutes, do not send another email
        // if ($existingNotification) {
        //     return;
        // }
    
        // Send the email
        if ($status === 'waitingBuyerPayment' && $event->pdfPath) {
            Mail::to($event->user->email)->send(new MailNotificationOrder($event->user, $title, $content, $event->pdfPath));
        } else {
            Mail::to($event->user->email)->send(new MailNotificationOrder($event->user, $title, $content));
        }
    
        // Log the notification to the database
        Notification::create([
            'receiver_user_id' => $event->user->id,
            'type' => 'orderNotification',
            'title' => $title,
            'content' => $content,
            'is_read' => $is_read,
            'sender_user_id' => $sender_user->id,
        ]);
    }
    


    private function sendAdminMessage($userId, $title, $content)
    {
        AdminMessage::create([
            'message' => $content,
            'sent_by' => 1, // Assuming 1 is the ID of the system admin or use appropriate ID
            'sent_to' => ["$userId"],
            'url' => null, // Add URL if needed
            'slika' => null, // Add image URL if needed
        ]);
    }

}
