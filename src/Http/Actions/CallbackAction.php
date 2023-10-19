<?php

namespace LbilTech\TelegramGitNotifierApp\Http\Actions;

use LbilTech\TelegramGitNotifier\Bot;
use LbilTech\TelegramGitNotifierApp\Services\CallbackService;

class CallbackAction
{
    protected Bot $bot;

    protected CallbackService $callbackService;

    public function __construct(
        Bot $bot,
    ) {
        $this->bot = $bot;
        $this->callbackService = new CallbackService();
    }

    public function __invoke()
    {
    }
}
