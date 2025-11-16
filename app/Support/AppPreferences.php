<?php

namespace App\Support;

use DateTime;
use DateTimeZone;

class AppPreferences
{
    /**
     * Return supported application languages.
     */
    public static function languageOptions(): array
    {
        return [
            'en' => 'English',
            'id' => 'Bahasa Indonesia',
        ];
    }

    /**
     * Generate timezone options with UTC offsets.
     */
    public static function timezoneOptions(): array
    {
        $options = [];
        $timezones = DateTimeZone::listIdentifiers();
        $now = new DateTime('now', new DateTimeZone('UTC'));

        foreach ($timezones as $timezone) {
            $currentTz = new DateTimeZone($timezone);
            $offset = $currentTz->getOffset($now);

            $formattedOffset = self::formatOffset($offset);
            $label = sprintf('(UTC%s) %s', $formattedOffset, str_replace('_', ' ', $timezone));

            $options[$timezone] = $label;
        }

        asort($options);

        return $options;
    }

    /**
     * Format offset seconds into +HH:MM / -HH:MM string.
     */
    protected static function formatOffset(int $offset): string
    {
        $sign = $offset >= 0 ? '+' : '-';
        $offset = abs($offset);

        $hours = floor($offset / 3600);
        $minutes = floor(($offset % 3600) / 60);

        return sprintf('%s%02d:%02d', $sign, $hours, $minutes);
    }
}

