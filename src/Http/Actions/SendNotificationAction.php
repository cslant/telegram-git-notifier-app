<?php

namespace LbilTech\TelegramGitNotifierApp\Http\Actions;

use GuzzleHttp\Client;
use LbilTech\TelegramGitNotifier\Constants\EventConstant;
use LbilTech\TelegramGitNotifier\Exceptions\InvalidViewTemplateException;
use LbilTech\TelegramGitNotifier\Exceptions\SendNotificationException;
use LbilTech\TelegramGitNotifier\Models\Event;
use LbilTech\TelegramGitNotifier\Models\Setting;
use LbilTech\TelegramGitNotifier\Services\EventService;
use LbilTech\TelegramGitNotifier\Services\NotificationService;
use LbilTech\TelegramGitNotifierApp\Services\AppService;
use Symfony\Component\HttpFoundation\Request;

class SendNotificationAction
{
    protected AppService $appService;

    protected NotificationService $notificationService;

    protected EventService $eventService;

    protected Request $request;

    protected array $chatIds = [];

    protected Client $client;

    protected Setting $setting;

    protected Event $event;

    public function __construct(
        Client $client,
        Event $event,
        Setting $setting,
        AppService $appService
    ) {
        $this->request = Request::createFromGlobals();
        $this->client = $client;
        $this->event = $event;
        $this->chatIds = config('telegram-git-notifier.bot.notify_chat_ids');
        $this->appService = $appService;
        $this->setting = $setting;

        $this->notificationService = new NotificationService($this->client);
    }

    /**
     * Handle to send notification from webhook event to telegram
     *
     * @return void
     * @throws InvalidViewTemplateException
     * @throws SendNotificationException
     */
    public function __invoke(): void
    {
        foreach (EventConstant::WEBHOOK_EVENT_HEADER as $platform => $header) {
            $event = $this->request->server->get($header);
            if (!is_null($event)) {
                $this->notificationService->platform = $platform;
                $this->event = $this->appService->setEventByFlatForm($this->event, $platform);
                $this->sendNotification($event);
                return;
            }
        }
    }

    /**
     * @param string $event
     *
     * @return void
     * @throws InvalidViewTemplateException
     * @throws SendNotificationException
     */
    private function sendNotification(string $event): void
    {
        if (!$this->validateAccessEvent($event)) {
            return;
        }

        foreach ($this->chatIds as $chatId) {
            if (empty($chatId)) {
                continue;
            }

            $this->notificationService->sendNotify($chatId);
        }
    }

    /**
     * Validate access event
     *
     * @param string $event
     *
     * @return bool
     * @throws InvalidViewTemplateException
     */
    private function validateAccessEvent(string $event): bool
    {
        $payload = $this->notificationService->setPayload($this->request, $event);
        $this->eventService = new EventService($this->setting, $this->event);

        if (empty($payload)
            || !$this->eventService->validateAccessEvent(
                $this->notificationService->platform,
                $event,
                $payload
            )
        ) {
            return false;
        }

        return true;
    }
}
