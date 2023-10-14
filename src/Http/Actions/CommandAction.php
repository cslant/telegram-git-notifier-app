<?php

namespace LbilTech\TelegramGitNotifierApp\Http\Actions;

use LbilTech\TelegramGitNotifier\Exceptions\EntryNotFoundException;
use LbilTech\TelegramGitNotifier\Exceptions\MessageIsEmptyException;
use LbilTech\TelegramGitNotifier\Services\TelegramService;
use LbilTech\TelegramGitNotifierApp\Services\AppService;
use LbilTech\TelegramGitNotifierApp\Services\CommandService;
use LbilTech\TelegramGitNotifierApp\Services\SettingService;

class CommandAction
{
    protected AppService $appService;

    protected CommandService $commandService;

    protected TelegramService $telegramService;

    public SettingService $settingService;

    public function __construct(
        AppService $appService,
        TelegramService $telegramService,
        SettingService $settingService
    ) {
        $this->appService = $appService;
        $this->telegramService = $telegramService;
        $this->commandService = new CommandService();
        $this->settingService = $settingService;
    }

    /**
     * @return void
     * @throws EntryNotFoundException
     * @throws MessageIsEmptyException
     */
    public function __invoke(): void
    {
        $text = $this->appService->getCommandMessage();

        switch ($text) {
            case '/start':
                $this->commandService->sendStartMessage($this->appService);
                break;
            case '/menu':
                $this->appService->sendMessage(
                    view('tools.menu'),
                    ['reply_markup' => $this->commandService->menuMarkup($this->appService->telegram)]
                );
                break;
            case '/token':
            case '/id':
            case '/usage':
            case '/server':
                $this->appService->sendMessage(view('tools.' . trim($text, '/')));
                break;
            case '/settings':
                $this->settingService->settingHandle();
                break;
            case '/set_menu':
                $this->telegramService->setMyCommands(CommandService::MENU_COMMANDS);
                break;
            default:
                $this->appService->sendMessage('🤨 Invalid Request!');
        }
    }
}
