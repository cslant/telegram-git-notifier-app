<?php

namespace TelegramGithubNotify\App\Http\Actions;

use Symfony\Component\HttpFoundation\Request;
use TelegramGithubNotify\App\Services\NotificationService;
use TelegramGithubNotify\App\Services\TelegramService;

class SendNotifyAction
{
    protected TelegramService $telegramService;

    protected NotificationService $notificationService;

    protected Request $request;

    public function __construct()
    {
        $this->telegramService = new TelegramService();
        $this->notificationService = new NotificationService();
        $this->request = Request::createFromGlobals();
    }

    public function handle(): void
    {
        $grChat = config('telegram-bot.gr_chat_ids');

        if (!$this->telegramService->chatId) {

        } elseif ($this->telegramService->chatId || in_array($this->telegramService->chatId, $grChat)) {
            $this->telegramService->telegramToolHandler($this->telegramService->messageData['message']['text']);
        } else {
            $this->notificationService->accessDenied($this->telegramService);
        }
    }


}
