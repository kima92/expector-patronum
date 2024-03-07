<?php

namespace Kima92\ExpectorPatronum\Notifications\Channels\PagerDuty;

use GuzzleHttp\Client;
use Illuminate\Notifications\Notification;
use Kima92\ExpectorPatronum\Notifications\Channels\PagerDuty\Exceptions\ApiError;
use Kima92\ExpectorPatronum\Notifications\Channels\PagerDuty\Exceptions\CouldNotSendNotification;

/**
 *  Copied from   https://github.com/laravel-notification-channels/pagerduty
 *  With updates: https://github.com/arnebr/laravel-notification-pagerduty
 */
class PagerDutyChannel
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \Kima92\ExpectorPatronum\Notifications\Channels\PagerDuty\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $routing_key = $notifiable->routeNotificationFor('PagerDuty')) {
            return;
        }

        /** @var PagerDutyMessage $data */
        $data = $notification->toPagerDuty($notifiable);
        $data->setRoutingKey($routing_key);

        try {
            $response = $this->client->post('https://events.pagerduty.com/v2/enqueue', [
                'body' => json_encode($data->toArray()),
            ]);
        } catch (\Exception $e) {
            throw CouldNotSendNotification::create($e);
        }

        $this->handleResponse($response);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @throws ApiError
     */
    public function handleResponse($response)
    {
        switch ($response->getStatusCode()) {
            case 200:
            case 201:
            case 202:
                return;
            case 400:
                throw ApiError::serviceBadRequest($response->getBody());
            case 429:
                throw ApiError::rateLimit();
            default:
                throw ApiError::unknownError($response->getStatusCode());
        }
    }
}
