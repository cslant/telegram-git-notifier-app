<?php

namespace CSlant\TelegramGitNotifierApp\Http\Actions;

use CSlant\TelegramGitNotifier\Bot;
use CSlant\TelegramGitNotifier\Exceptions\EntryNotFoundException;
use CSlant\TelegramGitNotifier\Exceptions\MessageIsEmptyException;
use CSlant\TelegramGitNotifierApp\Services\CommandService;

class CommandAction
{
    protected Bot $bot;

    protected CommandService $commandService;

    public function __construct(
        Bot $bot,
    ) {
        $this->bot = $bot;
        $this->commandService = new CommandService();
    }

    /**
     * @return void
     * @throws EntryNotFoundException
     * @throws MessageIsEmptyException
     */
    public function __invoke(): void
    {
        $text = $this->bot->telegram->Text();

        switch ($text) {
            case '/start':
                $this->commandService->sendStartMessage($this->bot);
                break;
            case '/menu':
                $this->bot->sendMessage(
                    view('tools.menu'),
                    ['reply_markup' => $this->commandService->menuMarkup($this->bot->telegram)]
                );
                break;
            case '/token':
            case '/id':
            case '/usage':
            case '/server':
                $this->bot->sendMessage(view('tools.' . trim($text, '/')));
                break;
            case '/settings':
                $this->bot->settingHandle();
                break;
            case '/set_menu':
                $this->bot->setMyCommands(CommandService::MENU_COMMANDS);
                break;
            default:
                $this->bot->sendMessage('🤨 Invalid Request!');
        }
    }
}
