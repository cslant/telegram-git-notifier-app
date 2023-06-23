<?php

namespace TelegramGithubNotify\App\Http\Actions;

use GuzzleHttp\Exception\GuzzleException;
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

    /**
     * @return void
     * @throws GuzzleException
     */
    public function handle(): void
    {
        $this->checkCallback();

        $grChat = config('telegram-bot.gr_chat_ids');

        if ($this->telegramService->chatId) {
            $this->notificationService->setPayload($this->request);
            $this->notificationService->sendNotify($this->telegramService);
        } elseif ($this->telegramService->chatId || in_array($this->telegramService->chatId, $grChat)) {
            $this->telegramService->telegramToolHandler($this->telegramService->messageData['message']['text']);
        } else {
            $this->notificationService->accessDenied($this->telegramService);
        }
    }

    /**
     * @return bool
     */
    public function checkCallback(): bool
    {
        if (!is_null($this->telegramService->telegram->Callback_ChatID())) {
            $callback = $this->telegramService->telegram->Callback_Data();
            $this->telegramService->sendCallbackResponse($callback);

            return true;
        }

        return false;
    }
}
