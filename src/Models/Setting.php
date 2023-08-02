<?php

namespace TelegramGithubNotify\App\Models;

class Setting
{
    public const SETTING_FILE = __DIR__ . '/../../storage/tg-setting.json';
    public const SETTING_PREFIX = 'setting.';

    public const SETTING_IS_NOTIFIED = self::SETTING_PREFIX . 'is_notified';
    public const SETTING_ALL_EVENTS_NOTIFY = self::SETTING_PREFIX . 'all_events_notify';
    public const SETTING_CUSTOM_EVENTS = self::SETTING_PREFIX . 'custom_events';

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
    public function allEventsNotifyStatus(): bool
    {
        if (!empty($this->settings) && $this->settings['all_events_notify'] === true) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isNotified(): bool
    {
        if (!empty($this->settings) && $this->settings['is_notified'] === true) {
            return true;
        }

        return false;
    }
}
