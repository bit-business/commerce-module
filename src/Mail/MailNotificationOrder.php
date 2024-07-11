<?php

namespace NadzorServera\Skijasi\Module\Commerce\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailNotificationOrder extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $title;
    public $content;
    public $pdfPath;

    public function __construct($user, $title, $content, $pdfPath = null)
    {
        $this->user = $user;
        $this->title = $title;
        $this->content = $content;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        $mail = $this->subject($this->title)
            ->view('skijasi_commerce::mail.order-notification')
            ->with([
                'user' => $this->user,
                'title' => $this->title,
                'content' => $this->content,
            ]);

        if ($this->pdfPath && file_exists($this->pdfPath)) {
            $mail->attach($this->pdfPath, [
                'as' => 'uplatnica.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
