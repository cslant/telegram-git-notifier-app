<?php

namespace LbilTech\TelegramGitNotifierApp\Http\Actions;

use GuzzleHttp\Client;
use LbilTech\TelegramGitNotifier\Constants\EventConstant;
use LbilTech\TelegramGitNotifier\Exceptions\InvalidViewTemplateException;
use LbilTech\TelegramGitNotifier\Exceptions\SendNotificationException;
use LbilTech\TelegramGitNotifier\Models\Event;
use LbilTech\TelegramGitNotifier\Models\Setting;
use LbilTech\TelegramGitNotifier\Services\AppService;
use LbilTech\TelegramGitNotifier\Services\EventService;
use LbilTech\TelegramGitNotifier\Services\NotificationService;
use LbilTech\TelegramGitNotifier\Services\TelegramService;
use LbilTech\TelegramGitNotifierApp\Services\SendNotificationService;
use Symfony\Component\HttpFoundation\Request;

class SendNotificationAction
{
    protected AppService $appService;

    protected TelegramService $telegramService;

    protected NotificationService $notificationService;

    protected SendNotificationService $sendNotificationService;

    protected EventService $eventService;

    protected Request $request;

    protected array $chatIds = [];

    protected Client $client;

    public Setting $setting;

    public Event $event;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->client = new Client();
        $this->event = new Event();
        $this->sendNotificationService = new SendNotificationService();

        $this->setting = new Setting();
        $this->setting = $this->sendNotificationService->setSettingFile($this->setting);
        $this->setting->setSettingConfig();

        $this->chatIds = config('telegram-git-notifier.bot.notify_chat_ids');

        $this->appService = new AppService();
        $this->appService->setCurrentChatId();

        $this->telegramService = new TelegramService($this->appService->telegram);
        $this->notificationService = new NotificationService($this->client);
    }

    /**
     * Handle send notify to telegram action
     *
     * @return void
     * @throws InvalidViewTemplateException
     * @throws SendNotificationException
     */
    public function __invoke(): void
    {
        // Send an event result to all chat ids in env
        foreach (EventConstant::WEBHOOK_EVENT_HEADER as $platform => $header) {
            $event = $this->request->server->get($header);
            if (!is_null($event)) {
                $this->notificationService->platform = $platform;
                $this->event = $this->sendNotificationService->setEventByFlatForm($this->event, $platform);
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
