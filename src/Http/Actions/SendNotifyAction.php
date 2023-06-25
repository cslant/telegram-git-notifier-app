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

        $this->chatIds = config('telegram-bot.notify_chat_ids');
    }

    /**
     * Handle send notify to telegram action
     *
     * @return void
     */
    public function __invoke(): void
    {
        $this->telegramService->checkCallback();
        $chatMessageId = $this->telegramService->messageData['message']['chat']['id'] ?? '';

        // Send a result to only the bot owner
        if (!empty($chatMessageId) && $chatMessageId == $this->telegramService->chatId) {
            $this->telegramService->telegramToolHandler($this->telegramService->messageData['message']['text']);
            return;
        }

        // Send a result to all chat ids in config
        try {
            // check github event
            if (is_null($this->request->server->get('HTTP_X_GITHUB_EVENT'))) {
                $this->notificationService->sendNotify($this->telegramService->chatId, 'invalid request');
                return;
            }

            $this->notificationService->setPayload($this->request);
            foreach ($this->chatIds as $chatId) {
                $this->notificationService->sendNotify($chatId);
            }
            return;
        } catch (GuzzleException $e) {
            error_log($e->getMessage());
            $this->notificationService->accessDenied($this->telegramService); // Notify access denied to other chat ids
        }
    }
}
