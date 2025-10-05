<?php

namespace Modules\ServiceRequest\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendMailToContactNotification extends Notification
{
    use Queueable;

    /**
     * Model variable
     */
    private $communication;


    /**
     * Sender (User) object
     */
    private $sender;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($communication, $user)
    {
        $this->communication = $communication;
        $this->sender = $user;
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
        //Organization
        $organization = $this->communication->organization;

        //ServiceRequest
        $requestrequest = $this->communication->servicerequest;

        //Contact
        $contact = $requestrequest->contact;


        //dd(json_encode($this->sender));


        return (new MailMessage)
            ->from('someemail@ellaisys.com', 'EllaiSys - TBD')
            ->replyTo('agent@ellaisys.com', $this->sender['full_name'])
            ->subject($this->communication['email_subject'])
            ->greeting('Hello ' . $contact['first_name'] . ',')
            ->line($this->communication['email_body']);
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
