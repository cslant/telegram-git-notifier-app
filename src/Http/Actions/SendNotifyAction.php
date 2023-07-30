<?php

namespace TelegramGithubNotify\App\Http\Actions;

use Symfony\Component\HttpFoundation\Request;
use TelegramGithubNotify\App\Services\EventService;
use TelegramGithubNotify\App\Services\NotificationService;
use TelegramGithubNotify\App\Services\TelegramService;

class SendNotifyAction
{
    protected TelegramService $telegramService;

    protected NotificationService $notificationService;

    protected EventService $eventSettingService;

    protected Request $request;

    protected array $chatIds = [];

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->telegramService = new TelegramService();
        $this->notificationService = new NotificationService();
        $this->eventSettingService = new EventService();

        $this->chatIds = config('telegram-bot.notify_chat_ids');
    }

    /**
     * Handle send notify to telegram action
     *
     * @return void
     */
    public function __invoke(): void
    {
        $chatMessageId = $this->telegramService->messageData['message']['chat']['id'] ?? '';

        if (!empty($chatMessageId)) {
            $this->handleEventInTelegram($chatMessageId);
            return;
        }

        // Send a GitHub event result to all chat ids in env
        if (!is_null($this->request->server->get('HTTP_X_GITHUB_EVENT'))) {
            $this->sendNotification();
            return;
        }

        $this->telegramService->checkCallback();
    }

    /**
     * @param string $chatMessageId
     * @return void
     */
    public function handleEventInTelegram(string $chatMessageId): void
    {
        // Send a result to only the bot owner
        if ($chatMessageId == config('telegram-bot.chat_id')) {
            $this->telegramService->telegramToolHandler($this->telegramService->messageData['message']['text']);
            return;
        }

        // Notify access denied to other/invalid chat ids
        if (!in_array($chatMessageId, $this->chatIds)) {
            $this->notificationService->accessDenied($this->telegramService);
        }
    }

    /**
     * @return void
     */
    protected function sendNotification(): void
    {
        $payload = $this->notificationService->setPayload($this->request);

        if (!$this->eventSettingService->validateAccessEvent($this->request, $payload)) {
            return;
        }

        foreach ($this->chatIds as $chatId) {
            if (empty($chatId)) {
                continue;
            }

            $this->notificationService->sendNotify($chatId);
        }
    }
}
