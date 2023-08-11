<?php

use TelegramGithubNotify\App\Helpers\ConfigHelper;
use TelegramGithubNotify\App\Models\Event;
use TelegramGithubNotify\App\Models\Setting;

if (!function_exists('config')) {
    /**
     * Return config value by string
     *
     * @param string $string
     * @return mixed
     */
    function config(string $string): mixed
    {
        return (new ConfigHelper())->execConfig($string);
    }
}

if (!function_exists('urlencoded_message')) {
    /**
     * Urlencoded message
     *
     * @param string $message
     * @return array|string|string[]
     */
    function urlencoded_message(string $message): array|string
    {
        return str_replace(["\n"], ['%0A'], urlencode($message));
    }
}

if (!function_exists('view')) {
    /**
     * Get view template
     *
     * @param string $partialPath
     * @param array $data
     * @return bool|string
     */
    function view(string $partialPath, array $data = []): bool|string
    {
        return (new ConfigHelper())->getTemplateData($partialPath, $data);
    }
}

if (!function_exists('singularity')) {
    /**
     * The reverse of pluralize, returns the singular form of a word in a string.
     *
     * @param $word
     * @return bool|string
     */
    function singularity($word): bool|string
    {
        $singular_rules = [
            '/(quiz)zes$/i' => '$1',
            '/(matr)ices$/i' => '$1ix',
            '/(vert|ind)ices$/i' => '$1ex',
            '/^(ox)en$/i' => '$1',
            '/(alias|status)es$/i' => '$1',
            '/([octop|vir])i$/i' => '$1us',
            '/(cris|ax|test)es$/i' => '$1is',
            '/(shoe)s$/i' => '$1',
            '/(o)es$/i' => '$1',
            '/(bus)es$/i' => '$1',
            '/([m|l])ice$/i' => '$1ouse',
            '/(x|ch|ss|sh)es$/i' => '$1',
            '/(m)ovies$/i' => '$1ovie',
            '/(s)eries$/i' => '$1eries',
            '/([^aeiouy]|qu)ies$/i' => '$1y',
            '/([lr])ves$/i' => '$1f',
            '/(tive)s$/i' => '$1',
            '/(hive)s$/i' => '$1',
            '/([^f])ves$/i' => '$1fe',
            '/(^analy)ses$/i' => '$1sis',
            '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '$1$2sis',
            '/([ti])a$/i' => '$1um',
            '/(n)ews$/i' => '$1ews',
            '/(.)s$/i' => '$1'
        ];
        return preg_replace(array_keys($singular_rules), array_values($singular_rules), $word);
    }
}

if (!function_exists('event_config')) {
    /**
     * Return event config
     *
     * @return array
     */
    function event_config(): array
    {
        return (new Event())->getEventConfig();
    }
}

if (!function_exists('all_events_notify')) {
    /**
     * Return all events notify status
     *
     * @return bool
     */
    function all_events_notify(): bool
    {
        return (new Setting())->allEventsNotifyStatus();
    }
}

if (!function_exists('setting_config')) {
    /**
     * Return setting config
     *
     * @return array
     */
    function setting_config(): array
    {
        return (new Setting())->getSettingConfig();
    }
}
