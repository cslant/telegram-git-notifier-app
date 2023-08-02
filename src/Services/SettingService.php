<?php

namespace TelegramGithubNotify\App\Services;

use TelegramGithubNotify\App\Models\Setting;

class SettingService extends AppService
{
    public Setting $setting;

    public array $settingConfig = [];

    public function __construct()
    {
        parent::__construct();
        $this->setting = new Setting();
        $this->settingConfig = $this->setting->getSettingConfig();
    }

    /**
     * @return void
     */
    public function settingHandle(): void
    {
        if ($this->settingConfig['is_notified']) {
            $notificationSetting = $this->telegram->buildInlineKeyBoardButton('✅ Github notification', '', $this->setting::SETTING_IS_NOTIFIED);
        } else {
            $notificationSetting = $this->telegram->buildInlineKeyBoardButton('Github notification', '', $this->setting::SETTING_IS_NOTIFIED);
        }

        if ($this->settingConfig['all_events_notify']) {
            $eventSetting = $this->telegram->buildInlineKeyBoardButton('✅ Enable All Events Notify', '', $this->setting::SETTING_ALL_EVENTS_NOTIFY);
        } else {
            $eventSetting = $this->telegram->buildInlineKeyBoardButton('Enable All Events Notify', '', $this->setting::SETTING_ALL_EVENTS_NOTIFY);
        }

        $keyboard = [
            [
                $notificationSetting,
            ], [
                $eventSetting,
                $this->telegram->buildInlineKeyBoardButton('⚙ Custom individual events', '', $this->setting::SETTING_CUSTOM_EVENTS),
            ], [
                $this->telegram->buildInlineKeyBoardButton('🔙 Back to menu', '', 'back.menu'),
            ]
        ];

        $this->sendMessage(view('tools.settings'), ['reply_markup' => $keyboard]);
    }

    /**
     * @param string $callback
     * @return void
     */
    public function settingCallbackHandler(string $callback): void
    {
        if ($callback === $this->setting::SETTING_CUSTOM_EVENTS) {
            (new EventService())->eventHandle();
            return;
        }

        $callback = str_replace($this->setting::SETTING_PREFIX, '', $callback);

        $this->updateSetting($callback, !$this->settingConfig[$callback]);
        $this->settingHandle();
    }

    /**
     * @param string $settingName
     * @param $settingValue
     * @return bool
     */
    public function updateSetting(string $settingName, $settingValue = null): bool
    {
        $keys = explode('.', $settingName);
        $lastKey = array_pop($keys);
        $nestedSettings = &$this->settingConfig;

        foreach ($keys as $key) {
            if (!isset($nestedSettings[$key]) || !is_array($nestedSettings[$key])) {
                return false;
            }
            $nestedSettings = &$nestedSettings[$key];
        }

        if (isset($nestedSettings[$lastKey])) {
            $nestedSettings[$lastKey] = $settingValue ?? !$nestedSettings[$lastKey];
            if ($this->saveSettingsToFile()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    private function saveSettingsToFile(): bool
    {
        if (file_exists($this->setting::SETTING_FILE)) {
            $json = json_encode($this->settingConfig, JSON_PRETTY_PRINT);
            file_put_contents($this->setting::SETTING_FILE, $json, LOCK_EX);

            return true;
        }

        return false;
    }
}
