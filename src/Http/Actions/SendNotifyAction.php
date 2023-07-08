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

        if (!empty($chatMessageId)) {
            // Send a result to only the bot owner
            if ($chatMessageId == $this->telegramService->chatId) {
                $this->telegramService->telegramToolHandler($this->telegramService->messageData['message']['text']);
                return;
            }

            // Notify access denied to other chat ids
            if (!in_array($chatMessageId, $this->chatIds)) {
                $this->notificationService->accessDenied($this->telegramService);
                return;
            }
        }

        // Send a GitHub event result to all chat ids in env
        if (!is_null($this->request->server->get('HTTP_X_GITHUB_EVENT')) && empty($chatMessageId)) {
            $this->notificationService->setPayload($this->request);
            $this->sendNotification();
        }
    }

    /**
     * @return void
     */
    protected function sendNotification(): void
    {
        foreach ($this->chatIds as $chatId) {
            if (empty($chatId)) {
                continue;
            }

            $this->notificationService->sendNotify($chatId);
        }
    }
}
