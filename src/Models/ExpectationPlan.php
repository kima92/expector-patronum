<?php

namespace Kima92\ExpectorPatronum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * Kima92\ExpectorPatronum\Models\ExpectationPlan
 *
 * @property int $id
 * @property string $name
 * @property string $schedule
 * @property int $group_id
 * @property array $rules
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property ?string $notification_email_address
 * @property ?string $notification_phone_number
 * @property ?string $notification_slack_webhook
 * @property ?string $notification_webhook
 * @property ?string $notification_pager_duty
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Kima92\ExpectorPatronum\Models\Expectation> $expectations
 * @property-read int|null $expectations_count
 * @property-read \Kima92\ExpectorPatronum\Models\Group $group
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectationPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectationPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectationPlan query()
 * @mixin \Eloquent
 */
class ExpectationPlan extends Model
{
    use Notifiable;

    protected $table = 'ep_expectation_plans';
    protected static $unguarded = true;
    protected $casts = [
        'rules' => 'array',
    ];

    public function expectations()
    {
        return $this->hasMany(Expectation::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail(): string
    {
        return $this->notification_email_address;
    }

    /**
     * Route notifications for the Nexmo channel.
     *
     * @return string
     */
    public function routeNotificationForNexmo(): string
    {
        return $this->notification_phone_number;
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @return string
     */
    public function routeNotificationForSlack(): string
    {
        return $this->notification_slack_webhook;
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @return string
     */
    public function routeNotificationForWebhook()
    {
        return $this->notification_webhook;
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @return string
     */
    public function routeNotificationForPagerDuty()
    {
        return $this->notification_pager_duty;
    }
}
