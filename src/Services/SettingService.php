<?php

namespace TelegramGithubNotify\App\Services;

use Telegram;

class SettingService extends AppService
{
    public function settingMarkup(Telegram $telegram): void
    {
        $keyboard = [
            [
                $telegram->buildInlineKeyBoardButton('🔔 Notification', '', '/notification'),
            ]
        ];

        if (enable_all_events()) {
            $eventSetting = $telegram->buildInlineKeyBoardButton('🔕 Disable All Events', '', '/disable_all_events');
        } else {
            $eventSetting = $telegram->buildInlineKeyBoardButton('🔔 Enable All Events', '', '/enable_all_events');
        }

        $keyboard[0][] = [
            $eventSetting,
            $telegram->buildInlineKeyBoardButton('Check Events', '', '/check_events'),
        ];

        $this->sendMessage(view('tools.settings'), ['reply_markup' => $keyboard]);
    }
}
