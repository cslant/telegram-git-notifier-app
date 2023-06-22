<?php

namespace TelegramGithubNotify\App\Helpers;

class ConfigHelper
{
    public array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/tg-notifier.php';
    }

    /**
     * @return array|mixed
     */
    public function getConfig(): mixed
    {
        return $this->config;
    }

    /**
     * @param string $string
     * @return array|mixed
     */
    public function config(string $string): mixed
    {
        $config = explode('.', $string);
        $result = $this->config;
        foreach ($config as $value) {
            $result = $result[$value];
        }
        return $result;
    }
}
