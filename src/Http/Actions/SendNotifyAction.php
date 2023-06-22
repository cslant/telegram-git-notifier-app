<?php

namespace TelegramGithubNotify\App\Http\Actions;

use Telegram;
use TelegramGithubNotify\App\Services\TelegramNotifyService;

class SendNotifyAction
{
    protected TelegramNotifyService $telegramService;

    public function __construct()
    {
        $this->telegramService = new TelegramNotifyService();
        $this->telegramService->setToken();
        $this->telegramService->setChatId();
        $this->telegramService->getDataOfMessage();
        $this->telegramService->storeByToken();
    }
}
