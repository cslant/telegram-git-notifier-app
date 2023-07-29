<?php

namespace TelegramGithubNotify\App\Services;

use Telegram;

class SettingService
{
    public function settingMarkup(Telegram $telegram): array
    {
        $settings = setting_config();

        if ($settings['is_notified'] === true) {
            $notificationSetting = $telegram->buildInlineKeyBoardButton('🔕 Disable Notification', '', '/disable_notification');
        } else {
            $notificationSetting = $telegram->buildInlineKeyBoardButton('🔔 Enable Notification', '', '/enable_notification');
        }

        if ($settings['enable_all_event'] === true) {
            $eventSetting = $telegram->buildInlineKeyBoardButton('🔕 Disable All Events', '', '/disable_all_events');
        } else {
            $eventSetting = $telegram->buildInlineKeyBoardButton('🔔 Enable All Events', '', '/enable_all_events');
        }

        return [
            [
                $notificationSetting,
            ], [
                $eventSetting,
                $telegram->buildInlineKeyBoardButton('Check Events', '', '/check_events'),
            ],
        ];
    }
}
