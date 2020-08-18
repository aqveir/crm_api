<?php

namespace Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Exception;

class UserEmailVerification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        try {
            //Build activation link
            $activationUrl = url(config('user.settings.new_user.email.url'));
            $activationUrl = str_replace('{activation_token}', $notifiable['verification_token'], $activationUrl);
            $activationUrl = str_replace('{org_hash}', $notifiable['organization']['hash'], $activationUrl);
            $activationUrl = str_replace('{user_email}', $notifiable['email'], $activationUrl);

            return (new MailMessage)
                ->subject('Action Required: CRM Omni - Please verify your email address')
                ->greeting('Dear ' . $notifiable['full_name'] . ',')
                ->line('Greetings from the CRMOmni Platform.')
                ->line('You will need to click on the link below to activate the account.')
                ->action('Activate', $activationUrl)
                ->line('Thank you for using our application!');

        } catch (Exception $e) {
            throw new Exception(500);
        } //Try-catch ends

    } //Function ends


    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
