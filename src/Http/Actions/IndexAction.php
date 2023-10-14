<?php

namespace LbilTech\TelegramGitNotifierApp\Http\Actions;

use GuzzleHttp\Client;
use LbilTech\TelegramGitNotifier\Exceptions\EntryNotFoundException;
use LbilTech\TelegramGitNotifier\Exceptions\InvalidViewTemplateException;
use LbilTech\TelegramGitNotifier\Exceptions\MessageIsEmptyException;
use LbilTech\TelegramGitNotifier\Exceptions\SendNotificationException;
use LbilTech\TelegramGitNotifier\Models\Event;
use LbilTech\TelegramGitNotifier\Models\Setting;
use LbilTech\TelegramGitNotifier\Services\TelegramService;
use LbilTech\TelegramGitNotifierApp\Services\AppService;
use LbilTech\TelegramGitNotifierApp\Services\SettingService;
use Symfony\Component\HttpFoundation\Request;
use Telegram;

class IndexAction
{
    protected AppService $appService;

    protected TelegramService $telegramService;

    protected SettingService $settingService;

    protected Request $request;

    protected Client $client;

    public Setting $setting;

    public Event $event;

    public function __construct()
    {
        $telegram = new Telegram(config('telegram-git-notifier.bot.token'));
        $this->appService = new AppService($telegram);
        $this->appService->setCurrentChatId();

        $this->telegramService = new TelegramService($this->appService->telegram, $this->appService->chatId);

        $this->client = new Client();
        $this->event = new Event();

        $this->setting = new Setting();
        $this->setting = $this->appService->setSettingFile($this->setting);
        $this->setting->setSettingConfig();

        $this->settingService = new SettingService($this->appService->telegram, $this->setting, $this->event, $this->appService->chatId);
    }

    /**
     * Handle telegram git notifier app
     *
     * @return void
     * @throws InvalidViewTemplateException
     * @throws SendNotificationException
     * @throws EntryNotFoundException
     * @throws MessageIsEmptyException
     */
    public function __invoke(): void
    {
        if ($this->telegramService->isCallback()) {
            $callbackAction = new CallbackAction($this->appService, $this->telegramService, $this->settingService);
            $callbackAction();
            return;
        }

        if ($this->telegramService->isMessage()) {
            $commandAction = new CommandAction($this->appService, $this->telegramService, $this->settingService);
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
