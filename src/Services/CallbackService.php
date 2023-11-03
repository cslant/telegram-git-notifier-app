<?php

namespace CSlant\TelegramGitNotifierApp\Services;

use CSlant\TelegramGitNotifier\Bot;
use CSlant\TelegramGitNotifier\Constants\SettingConstant;
use CSlant\TelegramGitNotifier\Exceptions\MessageIsEmptyException;
use CSlant\TelegramGitNotifierApp\Traits\Markup;

class CallbackService
{
    use Markup;

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
                $markup = $this->menuMarkup($this->bot->telegram);
                break;
            default:
                $this->bot->answerCallbackQuery('Unknown callback');
                return;
        }

        $this->bot->editMessageText($view, [
            'reply_markup' => $markup,
        ]);
    }
}
