<?php

namespace LbilTech\TelegramGitNotifierApp\Http\Actions;

use GuzzleHttp\Client;
use LbilTech\TelegramGitNotifier\Exceptions\InvalidViewTemplateException;
use LbilTech\TelegramGitNotifier\Exceptions\SendNotificationException;
use LbilTech\TelegramGitNotifier\Models\Event;
use LbilTech\TelegramGitNotifier\Models\Setting;
use LbilTech\TelegramGitNotifier\Services\TelegramService;
use LbilTech\TelegramGitNotifierApp\Services\AppService;
use Symfony\Component\HttpFoundation\Request;

class IndexAction
{
    protected AppService $appService;

    protected TelegramService $telegramService;

    protected Request $request;

    protected Client $client;

    public Setting $setting;

    public Event $event;

    public function __construct()
    {
        $this->appService = new AppService();
        $this->appService->setCurrentChatId();

        $this->telegramService = new TelegramService($this->appService->telegram);

        $this->client = new Client();
        $this->event = new Event();

        $this->setting = new Setting();
        $this->setting = $this->appService->setSettingFile($this->setting);
        $this->setting->setSettingConfig();
    }

    /**
     * Handle telegram git notifier app
     *
     * @return void
     * @throws InvalidViewTemplateException
     * @throws SendNotificationException
     */
    public function __invoke(): void
    {
        if ($this->telegramService->isCallback()) {
            $settingAction = new SettingAction();
            $settingAction();
            return;
        }

        if ($this->telegramService->isCommand()) {
            $commandAction = new CommandAction();
            $commandAction();
            return;
        }

        $sendNotificationAction = new SendNotificationAction(
            $this->client,
            $this->event,
            $this->setting,
            $this->appService
        );
        $sendNotificationAction();
    }
}
