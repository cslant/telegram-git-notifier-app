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
