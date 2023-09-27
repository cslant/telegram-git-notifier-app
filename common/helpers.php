<?php

use LbilTech\TelegramGitNotifierApp\Helpers\ConfigHelper;

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
