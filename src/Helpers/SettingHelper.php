<?php

namespace TelegramGithubNotify\App\Helpers;

class SettingHelper
{
    public const SETTING_FILE = __DIR__ . '/../../storage/tg-setting.json';

    public array $settings = [];

    public function __construct()
    {
        if (file_exists(self::SETTING_FILE)) {
            $this->setSettingConfig();
        }
    }

    /**
     * Set settings
     *
     * @return void
     */
    public function setSettingConfig(): void
    {
        $json = file_get_contents(self::SETTING_FILE);
        $this->settings = json_decode($json, true);
    }

    /**
     * Get settings
     *
     * @return array
     */
    public function getSettingConfig(): array
    {
        return $this->settings;
    }

    /**
     * @return bool
     */
    public function enableAllEvents(): bool
    {
        if (!empty($this->settings) && $this->settings['enable_all_event'] === true) {
            return true;
        }

        return false;
    }
}
