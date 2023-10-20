<?php

namespace LbilTech\TelegramGitNotifierApp\Services;

use LbilTech\TelegramGitNotifier\Bot;
use LbilTech\TelegramGitNotifier\Exceptions\EntryNotFoundException;
use Telegram;

class CommandService
{
    public const MENU_COMMANDS
        = [
            [
                'command'     => '/start',
                'description' => 'Welcome to the bot'
            ], [
                'command'     => '/menu',
                'description' => 'Show menu of the bot'
            ], [
                'command'     => '/token',
                'description' => 'Show token of the bot'
            ], [
                'command'     => '/id',
                'description' => 'Show the ID of the current chat'
            ], [
                'command'     => '/usage',
                'description' => 'Show step by step usage'
            ], [
                'command'     => '/server',
                'description' => 'To get Server Information'
            ], [
                'command'     => '/settings',
                'description' => 'Show settings of the bot'
            ],
        ];

    /**
     * Generate menu markup
     *
     * @return array[]
     */
    public function menuMarkup(Telegram $telegram): array
    {
        return [
            [
                $telegram->buildInlineKeyBoardButton("🗨 Discussion", config('telegram-git-notifier.author.discussion'))
            ], [
                $telegram->buildInlineKeyBoardButton("💠 Source Code", config('telegram-git-notifier.author.source_code'))
            ]
        ];
    }

    /**
     * @param Bot $bot
     *
     * @return void
     * @throws EntryNotFoundException
     */
    public function sendStartMessage(Bot $bot): void
    {
        $reply = view(
            'tools.start',
            ['first_name' => $bot->telegram->FirstName()]
        );
        $bot->sendPhoto(
            __DIR__ . '/../../resources/images/start.png',
            ['caption' => $reply]
        );
    }
}
