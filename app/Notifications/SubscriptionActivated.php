<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionActivated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $subscription)
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $plan = $this->subscription->plan;
        $endDate = $this->subscription->end_date
            ? $this->subscription->end_date->format('d M Y')
            : 'Lifetime Access';

        return (new MailMessage)
            ->subject('🎉 Your ElimuHub Premium Subscription is Active!')
            ->greeting('Hi ' . $notifiable->first_name . ' 👋')
            ->line('Great news! Your payment was successful and your subscription has been activated.')
            ->line('### 📦 Subscription Details')
            ->line('**Plan:** ' . $plan->name)
            ->line('**Valid Until:** ' . $endDate)
            ->line('**M-Pesa Receipt:** ' . $this->subscription->mpesa_receipt_number)
            ->action('Start Learning Now', env('APP_FRONTEND_URL') . '/dashboard')
            ->line('You now have full access to all premium lessons, classes, and content.')
            ->line('Happy learning 🚀')
            ->salutation('— The ElimuHub Team');
    }
}
