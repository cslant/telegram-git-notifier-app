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

    protected array $chatIds = [];

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->telegramService = new TelegramService();
        $this->notificationService = new NotificationService();

        $this->chatIds = config('telegram-bot.gr_chat_ids');
        $this->chatIds[] = $this->telegramService->chatId;
    }

    /**
     * @return void
     * @throws GuzzleException
     */
    public function handle(): void
    {
        $this->checkCallback();

        // Send a result to only the bot owner
        if (!empty($this->telegramService->messageData['message']['chat']['id'])
            && $this->telegramService->messageData['message']['chat']['id'] == $this->telegramService->chatId) {
            $this->telegramService->telegramToolHandler($this->telegramService->messageData['message']['text']);

            return;
        }

        // Send deny access to other chat ids
        if (!in_array($this->telegramService->messageData['message']['chat']['id'], $this->chatIds)) {
            $this->notificationService->accessDenied($this->telegramService);

            return;
        }

        // Send notify to all chat ids
        $this->notificationService->setPayload($this->request);
        foreach ($this->chatIds as $chatId) {
            $this->notificationService->sendNotify($chatId);
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
