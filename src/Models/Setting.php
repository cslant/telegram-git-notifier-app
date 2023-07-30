<?php

namespace TelegramGithubNotify\App\Models;

class Setting
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
    public function allEventsNotify(): bool
    {
        if (!empty($this->settings) && $this->settings['all_events_notify'] === true) {
            return true;
        }

        return false;
    }
}
