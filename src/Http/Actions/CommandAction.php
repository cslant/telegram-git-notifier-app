<?php

namespace LbilTech\TelegramGitNotifierApp\Http\Actions;

use LbilTech\TelegramGitNotifier\Exceptions\EntryNotFoundException;
use LbilTech\TelegramGitNotifier\Exceptions\MessageIsEmptyException;
use LbilTech\TelegramGitNotifier\Models\Setting;
use LbilTech\TelegramGitNotifier\Services\TelegramService;
use LbilTech\TelegramGitNotifierApp\Services\AppService;
use LbilTech\TelegramGitNotifierApp\Services\CommandService;

class CommandAction
{
    protected AppService $appService;

    protected CommandService $commandService;

    protected TelegramService $telegramService;

    protected Setting $setting;

    public function __construct(
        AppService $appService,
        TelegramService $telegramService,
        Setting $setting
    ) {
        $this->appService = $appService;
        $this->setting = $setting;
        $this->telegramService = $telegramService;
        $this->commandService = new CommandService();
    }

    /**
     * @return void
     * @throws EntryNotFoundException
     * @throws MessageIsEmptyException
     */
    public function __invoke(): void
    {
        $text = $this->appService->telegram->Text();

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
//            case '/settings':
//                $this->settingService->settingHandle();
//                break;
            case '/set_menu':
                $this->telegramService->setMyCommands(CommandService::MENU_COMMANDS);
                break;
            default:
                $this->appService->sendMessage('🤨 Invalid Request!');
        }
    }
}
