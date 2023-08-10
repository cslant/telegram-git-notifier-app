<?php

namespace TelegramGithubNotify\App\Services;

use TelegramGithubNotify\App\Models\Setting;

class SettingService extends AppService
{
    protected Setting $setting;

    protected array $settingConfig = [];

    public function __construct()
    {
        parent::__construct();
        $this->setting = new Setting();
        $this->settingConfig = $this->setting->getSettingConfig();
    }

    /**
     * Send setting message
     *
     * @return void
     */
    public function settingHandle(): void
    {
        $this->sendMessage(
            view('tools.settings'),
            ['reply_markup' => $this->settingMarkup()]
        );
    }

    /**
     * Generate setting markup
     *
     * @return array[]
     */
    public function settingMarkup(): array
    {
        return [
            [
                $this->telegram->buildInlineKeyBoardButton(
                    $this->settingConfig['is_notified']
                        ? '✅ Github notification' : 'Github notification',
                    '',
                    $this->setting::SETTING_IS_NOTIFIED
                ),
            ],
            [
                $this->telegram->buildInlineKeyBoardButton(
                    $this->settingConfig['all_events_notify']
                        ? '✅ Enable All Events Notify'
                        : 'Enable All Events Notify',
                    '',
                    $this->setting::SETTING_ALL_EVENTS_NOTIFY
                ),
                $this->telegram->buildInlineKeyBoardButton(
                    '⚙ Custom individual events',
                    '',
                    $this->setting::SETTING_CUSTOM_EVENTS
                ),
            ],
            [
                $this->telegram->buildInlineKeyBoardButton(
                    '🔙 Back to menu',
                    '',
                    $this->setting::SETTING_BACK . 'menu'
                ),
            ]
        ];
    }

    /**
     * Setting callback handler
     *
     * @param string $callback
     * @return void
     */
    public function settingCallbackHandler(string $callback): void
    {
        if (str_contains($callback, $this->setting::SETTING_CUSTOM_EVENTS)) {
            (new EventService())->eventHandle($callback);
            return;
        }

        if (str_contains($callback, $this->setting::SETTING_BACK)) {
            $this->answerBackButton($callback);
            return;
        }

        $callback = str_replace($this->setting::SETTING_PREFIX, '', $callback);

        if ($this->updateSettingItem($callback, !$this->settingConfig[$callback])) {
            $this->editMessageReplyMarkup([
                'reply_markup' => $this->settingMarkup(),
            ]);
        } else {
            $this->answerCallbackQuery('Something went wrong!');
        }
    }

    /**
     * Update setting item value and save to file
     *
     * @param string $settingName
     * @param $settingValue
     * @return bool
     */
    public function updateSettingItem(string $settingName, $settingValue = null): bool
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
     * Save settings to json file
     *
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

    /**
     * @param string $callback
     *
     * @return void
     */
    public function answerBackButton(string $callback): void
    {
        $callback = str_replace($this->setting::SETTING_BACK, '', $callback);

        switch ($callback) {
            case 'settings':
                $view = view('tools.settings');
                $markup = $this->settingMarkup();
                break;
            case 'settings.custom_events':
                $view = view('tools.custom_events');
                $markup = (new EventService())->eventMarkup();
                break;
            default:
                $view = view('tools.menu');
                $markup = $this->menuMarkup();
                break;
        }

        $this->editMessageText($view, [
            'reply_markup' => $markup,
        ]);
    }
}
