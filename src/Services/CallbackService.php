<?php

namespace LbilTech\TelegramGitNotifierApp\Services;

use LbilTech\TelegramGitNotifier\Bot;
use LbilTech\TelegramGitNotifier\Constants\SettingConstant;
use LbilTech\TelegramGitNotifier\Exceptions\MessageIsEmptyException;

class CallbackService
{
    private Bot $bot;

    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
    }

    /**
     * Answer the back button
     *
     * @param string $callback
     *
     * @return void
     * @throws MessageIsEmptyException
     */
    public function answerBackButton(string $callback): void
    {
        $callback = str_replace(SettingConstant::SETTING_BACK, '', $callback);

        switch ($callback) {
            case 'settings':
                $view = view('tools.settings');
                $markup = $this->bot->settingMarkup();
                break;
            case 'settings.custom_events.github':
                $view = view('tools.custom_event', ['platform' => 'github']);
                $markup = $this->bot->eventMarkup();
                break;
            case 'settings.custom_events.gitlab':
                $view = view('tools.custom_event', ['platform' => 'gitlab']);
                $markup = $this->bot->eventMarkup(null, 'gitlab');
                break;
            case 'menu':
                $view = view('tools.menu');
                $markup = $this->menuMarkup();
                break;
            default:
                $this->bot->answerCallbackQuery('Unknown callback');
                return;
        }

        $this->bot->editMessageText($view, [
            'reply_markup' => $markup,
        ]);
    }

    /**
     * @return array[]
     */
    public function menuMarkup(): array
    {
        return [
            [
                $this->bot->telegram->buildInlineKeyBoardButton('🗨 Discussion', config('telegram-git-notifier.author.discussion'))
            ], [
                $this->bot->telegram->buildInlineKeyBoardButton('💠 Source Code', config('telegram-git-notifier.author.source_code'))
            ]
        ];
    }
}
