<?php

namespace CSlant\TelegramGitNotifierApp\Http\Actions;

use CSlant\TelegramGitNotifier\Bot;
use CSlant\TelegramGitNotifier\Exceptions\BotException;
use CSlant\TelegramGitNotifier\Exceptions\CallbackException;
use CSlant\TelegramGitNotifier\Exceptions\ConfigFileException;
use CSlant\TelegramGitNotifier\Exceptions\EntryNotFoundException;
use CSlant\TelegramGitNotifier\Exceptions\InvalidViewTemplateException;
use CSlant\TelegramGitNotifier\Exceptions\MessageIsEmptyException;
use CSlant\TelegramGitNotifier\Exceptions\SendNotificationException;
use CSlant\TelegramGitNotifier\Notifier;
use CSlant\TelegramGitNotifierApp\Services\CallbackService;
use CSlant\TelegramGitNotifierApp\Services\CommandService;
use CSlant\TelegramGitNotifierApp\Services\NotificationService;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Telegram;

class IndexAction
{
    protected Client $client;

    protected Bot $bot;

    protected Notifier $notifier;

    protected Request $request;

    /**
     * @throws ConfigFileException
     */
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
     * @throws EntryNotFoundException
     * @throws InvalidViewTemplateException
     * @throws MessageIsEmptyException
     * @throws SendNotificationException
     * @throws BotException
     * @throws CallbackException
     */
    public function __invoke(): void
    {
        if ($this->bot->isCallback()) {
            $callbackAction = new CallbackService($this->bot);
            $callbackAction->handle();

            return;
        }

        if ($this->bot->isMessage() && $this->bot->isOwner()) {
            $commandAction = new CommandService($this->bot);
            $commandAction->handle();

            return;
        }

        $sendNotification = new NotificationService($this->notifier, $this->bot->setting);
        $sendNotification->handle();
    }
}
