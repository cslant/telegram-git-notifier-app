<?php

namespace TelegramGithubNotify\App\Http\Actions;

use Symfony\Component\HttpFoundation\Request;
use TelegramGithubNotify\App\Services\TelegramService;

class SendNotifyAction
{
    protected TelegramService $telegramService;

    protected Request $request;

    public function __construct()
    {
        $this->telegramService = new TelegramService();
        $this->request = Request::createFromGlobals();
    }

    public function handle(): void
    {
        $grChat = config('telegram-bot.gr_chat_ids');

        if (!$this->telegramService->chatId) {

        }
    }
}
