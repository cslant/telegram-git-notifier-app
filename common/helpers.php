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
            var_dump($arg);
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
