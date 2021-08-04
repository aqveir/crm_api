<?php

namespace Modules\Contact\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ContactImportNotification extends Notification implements ShouldQueue
{
    use Queueable;


    /**
     * @param $model
     */
    private $model;


    /**
     * @param $data
     */
    private $data;


    /**
     * @param $action
     */
    private $action;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($model, $data, string $action='status_file_imported')
    {
        $this->model = $model;
        $this->data = $data;
        $this->action = $action;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }


    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', 'https://laravel.com')
                    ->line('Thank you for using our application!');
    }


    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'organization' => $this->model,
            'action' => $this->data,
            'data' => $this->data
        ];
    }

} //Class ends
