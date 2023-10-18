<?php

namespace LbilTech\TelegramGitNotifierApp\Http\Actions;

use GuzzleHttp\Client;
use LbilTech\TelegramGitNotifier\Bot;
use LbilTech\TelegramGitNotifier\Exceptions\EntryNotFoundException;
use LbilTech\TelegramGitNotifier\Exceptions\InvalidViewTemplateException;
use LbilTech\TelegramGitNotifier\Exceptions\MessageIsEmptyException;
use LbilTech\TelegramGitNotifier\Exceptions\SendNotificationException;
use LbilTech\TelegramGitNotifier\Notifier;
use Symfony\Component\HttpFoundation\Request;
use Telegram;

class IndexAction
{
    protected Client $client;

    protected Bot $bot;

    protected Notifier $notifier;

    protected Request $request;

    public function __construct()
    {
        $this->client = new Client();

        $telegram = new Telegram(config('telegram-git-notifier.bot.token'));
        $this->bot = new Bot($telegram);
        $this->notifier = new Notifier();
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
//        if ($this->bot->isCallback()) {
//            $callbackAction = new CallbackAction($this->appService, $this->settingService);
//            $callbackAction();
//            return;
//        }
//
        if ($this->bot->isMessage()) {
            $commandAction = new CommandAction($this->bot);
            $commandAction();
            return;
        }

        $sendNotificationAction = new SendNotificationAction($this->notifier, $this->bot->setting);
        $sendNotificationAction();
    }
}
