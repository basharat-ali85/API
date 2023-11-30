<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetLink extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        $resetLink = url('/reset-password?token=' . $this->token . '&email=' . urlencode($this->user->email));

        return $this->view('emails.password_reset_link')
            ->with([
                'resetLink' => $resetLink,
                'userName' => $this->user->name ?? '',
            ])
            ->subject('Password reset link');
    }
}
