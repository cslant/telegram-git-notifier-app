<?php

namespace LbilTech\TelegramGitNotifierApp\Http\Actions;

use LbilTech\TelegramGitNotifier\Bot;
use LbilTech\TelegramGitNotifier\Constants\SettingConstant;
use LbilTech\TelegramGitNotifier\Exceptions\MessageIsEmptyException;
use LbilTech\TelegramGitNotifierApp\Services\CallbackService;

class CallbackAction
{
    protected Bot $bot;

    protected CallbackService $callbackService;

    public function __construct(
        Bot $bot,
    ) {
        $this->bot = $bot;
        $this->callbackService = new CallbackService($bot);
    }

    /**
     * @return void
     * @throws MessageIsEmptyException
     */
    public function __invoke(): void
    {
        $callback = $this->bot->telegram->Callback_Data();

        if (str_contains($callback, SettingConstant::SETTING_CUSTOM_EVENTS)) {
            $this->bot->eventHandle($callback);
            return;
        }

        if (str_contains($callback, SettingConstant::SETTING_BACK)) {
            $this->callbackService->answerBackButton($callback);
            return;
        }

        $callback = str_replace(SettingConstant::SETTING_PREFIX, '', $callback);

        if ($this->bot->setting->updateSetting($callback, !$this->bot->setting->getSettings()[$callback])) {
            $this->bot->editMessageReplyMarkup([
                'reply_markup' => $this->bot->settingMarkup(),
            ]);
        } else {
            $this->bot->answerCallbackQuery('Something went wrong!');
        }
    }
}
