<?php

namespace Kima92\ExpectorPatronum\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Kima92\ExpectorPatronum\Models\Expectation;
use Kima92\ExpectorPatronum\Models\ExpectationPlan;
use Kima92\ExpectorPatronum\Notifications\Channels\PagerDuty\PagerDutyChannel;
use Kima92\ExpectorPatronum\Notifications\Channels\PagerDuty\PagerDutyMessage;
use NotificationChannels\Webhook\WebhookChannel;
use NotificationChannels\Webhook\WebhookMessage;

class ExpectationNotStarted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly Expectation $expectation)
    {}


    public function via(ExpectationPlan $notifiable): array
    {
        $channels = [];
        if ($notifiable->notification_email_address) {
            $channels[] = 'mail';
        }
        if ($notifiable->notification_phone_number) {
            $channels[] = 'nexmo';
        }
        if ($notifiable->notification_slack_webhook) {
            $channels[] = 'slack';
        }
        if ($notifiable->notification_webhook) {
            $channels[] = [WebhookChannel::class];
        }
        if ($notifiable->notification_pager_duty) {
            $channels[] = [PagerDutyChannel::class];
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail(ExpectationPlan $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject("{$notifiable->name} Not Started In Time")
                    ->greeting('Hi,')
                    ->line("Expectation {$this->expectation->id} for plan {$this->expectation->expectationPlan->id}. {$this->expectation->expectationPlan->name} Not started")
                    ->line("Expected start time: {$this->expectation->expected_start_date}");
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack(ExpectationPlan $notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->error()
            ->from("ExpectorPatronum")
            ->content("Expectation {$this->expectation->id} for plan {$this->expectation->expectationPlan->id}. {$this->expectation->expectationPlan->name} of {$this->expectation->expected_start_date} not started in time!");
    }

    public function toWebhook(ExpectationPlan $notifiable)
    {
        return WebhookMessage::create()
            ->data([
                'payload' => [
                    'type' => 'expectation-not-started',
                    'id'   => $this->expectation->id,
                    'scheduled_to' => $this->expectation->expected_start_date,
                    'plan' =>  $this->expectation->expectationPlan->only(["id", 'name', 'schedule']),
                ]
            ]);
    }

    public function toPagerDuty(ExpectationPlan $notifiable)
    {
        return PagerDutyMessage::create()
            ->setClass($notifiable->group->name)
            ->setSummary("Expectation {$this->expectation->id} for plan {$this->expectation->expectationPlan->id}. {$this->expectation->expectationPlan->name} Not started");
    }
}
