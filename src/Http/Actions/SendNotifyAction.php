<?php

namespace TelegramNotificationBot\App\Http\Actions;

use Symfony\Component\HttpFoundation\Request;
use TelegramNotificationBot\App\Services\EventService;
use TelegramNotificationBot\App\Services\NotificationService;
use TelegramNotificationBot\App\Services\TelegramService;

class SendNotifyAction
{
    protected TelegramService $telegramService;

    protected NotificationService $notificationService;

    protected EventService $eventService;

    protected Request $request;

    protected array $chatIds = [];

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->telegramService = new TelegramService();
        $this->notificationService = new NotificationService();
        $this->eventService = new EventService();

        $this->chatIds = config('telegram-bot.notify_chat_ids');
    }

    /**
     * Handle send notify to telegram action
     *
     * @return void
     */
    public function __invoke(): void
    {
        // Send an event result to all chat ids in env
        foreach ($this->notificationService::WEBHOOK_EVENT_HEADER as $platform => $header) {
            $event = $this->request->server->get($header);
            if (!is_null($event)) {
                $this->notificationService->platform = $platform;
                $this->sendNotification($event);
                return;
            }
        }

        // Telegram bot handler
        $chatMessageId = $this->telegramService->messageData['message']['chat']['id'] ?? '';
        if (!empty($chatMessageId)) {
            $this->handleEventInTelegram($chatMessageId);
            return;
        }

        $this->telegramService->checkCallback();
    }

    /**
     * @param string $chatMessageId
     * @return void
     */
    private function handleEventInTelegram(string $chatMessageId): void
    {
        // Send a result to only the bot owner
        if ($chatMessageId == $this->telegramService->chatId) {
            $this->telegramService->telegramToolHandler($this->telegramService->messageData['message']['text']);
            return;
        }

        // Notify access denied to other/invalid chat ids
        if (!in_array($chatMessageId, $this->chatIds)) {
            $this->notificationService->accessDenied($this->telegramService);
        }
    }

    /**
     * @param string $event
     * @return void
     */
    private function sendNotification(string $event): void
    {
        $payload = $this->notificationService->setPayload($this->request, $event);
        if (empty($payload) || !$this->eventService->validateAccessEvent($this->notificationService->platform, $event, $payload)) {
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
