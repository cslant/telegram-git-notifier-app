<?php

namespace TelegramNotificationBot\App\Services;

use TelegramNotificationBot\App\Models\Setting;

class SettingService extends AppService
{
    protected Setting $setting;

    public function __construct()
    {
        parent::__construct();
        $this->setting = new Setting();
    }

    /**
     * Send a setting message
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
        $markup = [
            [
                $this->telegram->buildInlineKeyBoardButton(
                    $this->setting->settings['is_notified'] ? '✅ Allow notifications' : 'Allow notifications',
                    '',
                    $this->setting::SETTING_IS_NOTIFIED
                ),
            ], [
                $this->telegram->buildInlineKeyBoardButton(
                    $this->setting->settings['all_events_notify'] ? '✅ Enable All Events Notify' : 'Enable All Events Notify',
                    '',
                    $this->setting::SETTING_ALL_EVENTS_NOTIFY
                ),
            ]
        ];

        $markup = $this->customEventMarkup($markup);

        $markup[] = [$this->telegram->buildInlineKeyBoardButton('🔙 Back to menu', '', $this->setting::SETTING_BACK . 'menu'),];

        return $markup;
    }

    /**
     * @param array $markup
     * @return array
     */
    public function customEventMarkup(array $markup): array
    {
        if (!$this->setting->settings['all_events_notify']) {
            $markup[] = [
                $this->telegram->buildInlineKeyBoardButton(
                    '🦑 Custom github events',
                    '',
                    $this->setting::SETTING_GITHUB_EVENTS
                ),
                $this->telegram->buildInlineKeyBoardButton(
                    '🦊 Custom gitlab events',
                    '',
                    $this->setting::SETTING_GITLAB_EVENTS
                ),
            ];
        }

        return $markup;
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

        if ($this->setting->updateSettingItem($callback, !$this->setting->settings[$callback])) {
            $this->editMessageReplyMarkup([
                'reply_markup' => $this->settingMarkup(),
            ]);
        } else {
            $this->answerCallbackQuery('Something went wrong!');
        }
    }

    /**
     * Answer the back button
     *
     * @param string $callback
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
            case 'settings.custom_events.github':
                $view = view('tools.custom_events', ['platform' => 'github']);
                $markup = (new EventService())->eventMarkup();
                break;
            case 'settings.custom_events.gitlab':
                $view = view('tools.custom_events', ['platform' => 'gitlab']);
                $markup = (new EventService())->eventMarkup(null, 'gitlab');
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
