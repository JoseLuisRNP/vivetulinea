<?php

namespace App\Support;

use Carbon\Exceptions\InvalidFormatException;
use DateTimeZone;
use Exception;
use Illuminate\Support\Carbon;

/**
 * Days in this app are calendar days in the user's local time, not instants.
 *
 * The browser stores its IANA timezone in the "tz" cookie, and every day
 * sent by the client is a local "YYYY-MM-DD" date. Days are stored as naive
 * dates in the app timezone (UTC), so a meal logged on 20/11 in Florida is
 * saved on 2026-11-20 no matter what time it is in UTC.
 */
class UserTime
{
    public const COOKIE = 'tz';

    public const DEFAULT_TIMEZONE = 'Europe/Madrid';

    public static function timezone(): string
    {
        $timezone = request()->cookie(self::COOKIE);

        if (! is_string($timezone) || $timezone === '' || strlen($timezone) > 64) {
            return self::DEFAULT_TIMEZONE;
        }

        try {
            new DateTimeZone($timezone);
        } catch (Exception) {
            return self::DEFAULT_TIMEZONE;
        }

        return $timezone;
    }

    /**
     * Current wall-clock time where the user is.
     */
    public static function now(): Carbon
    {
        return Carbon::now(self::timezone());
    }

    /**
     * The user's current calendar day, at midnight in the app timezone.
     */
    public static function today(): Carbon
    {
        return self::fromDateString(self::now()->toDateString());
    }

    /**
     * Resolve the calendar day the client asked for.
     *
     * Accepts a local "YYYY-MM-DD" date. ISO instants sent by clients still
     * running an older build are converted to the user's timezone first.
     */
    public static function day(?string $value): Carbon
    {
        if ($value === null || $value === '') {
            return self::today();
        }

        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $parts)) {
            return checkdate((int) $parts[2], (int) $parts[3], (int) $parts[1])
                ? self::fromDateString($value)
                : self::today();
        }

        try {
            $date = Carbon::parse($value)->setTimezone(self::timezone())->toDateString();
        } catch (InvalidFormatException) {
            return self::today();
        }

        return self::fromDateString($date);
    }

    /**
     * Moment to store for something consumed on the given day: that calendar
     * day with the user's current local time.
     */
    public static function consumedAt(?string $value): Carbon
    {
        return self::day($value)->setTimeFrom(self::now());
    }

    private static function fromDateString(string $date): Carbon
    {
        return Carbon::createFromFormat('!Y-m-d', $date, config('app.timezone'));
    }
}
