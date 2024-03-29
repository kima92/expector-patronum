<?php

namespace Kima92\ExpectorPatronum\Notifications\Channels\PagerDuty\Exceptions;

class ApiError extends \Exception
{
    public static function serviceBadRequest($response)
    {
        $response = json_decode($response, true);

        $message = $response['message'] ?? '';
        $errors = isset($response['errors']) ? implode(',', $response['errors']) : '';

        return new static("PagerDuty returned 400 Bad Request: $message - $errors");
    }

    public static function rateLimit()
    {
        // https://v2.developer.pagerduty.com/docs/errors
        return new static('PagerDuty returned 429 Too Many Requests');
    }

    public static function unknownError($code)
    {
        return new static("PagerDuty responded with an unexpected HTTP Status: $code");
    }
}
