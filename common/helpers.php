<?php

use JetBrains\PhpStorm\NoReturn;
use TelegramGithubNotify\App\Helpers\ConfigHelper;

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

if (!function_exists('dd')) {
    /**
     * Dump and die
     *
     * @param mixed ...$args
     */
    #[NoReturn]
    function dd(mixed ...$args): void
    {
        foreach ($args as $arg) {
            print_r('<pre>');
            var_dump($arg);
            print_r('</pre>');
        }
        die();
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

if (!function_exists('get_template')) {
    /**
     * Get template
     *
     * @param string $partialPath
     * @param array $data
     * @return bool|string
     */
    function get_template(string $partialPath, array $data = []): bool|string
    {
        return (new ConfigHelper())->getTemplateData($partialPath, $data);
    }
}

if (!function_exists('get_event_template')) {
    /**
     * Get event template
     *
     * @param string $partialPath
     * @param array $data
     * @return bool|string
     */
    function get_event_template(string $partialPath, array $data = []): bool|string
    {
        return (new ConfigHelper())->getTemplateData('events/' . $partialPath, $data);
    }
}

if (!function_exists('get_tool_template')) {
    /**
     * Get tool template
     *
     * @param string $partialPath
     * @param array $data
     * @return bool|string
     */
    function get_tool_template(string $partialPath, array $data = []): bool|string
    {
        return (new ConfigHelper())->getTemplateData('tools/' . $partialPath, $data);
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
        $singular = $word;
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

        foreach ($singular_rules as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                $singular = preg_replace($rule, $replacement, $word);
                break;
            }
        }

        return $singular;
    }
}
