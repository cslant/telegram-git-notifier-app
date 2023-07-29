<?php

namespace TelegramGithubNotify\App\Services;

class SettingService extends AppService
{
    public function settingMarkup(): array
    {
        $settings = setting_config();

        if ($settings['is_notified']) {
            $notificationSetting = $this->telegram->buildInlineKeyBoardButton('🔕 Disable Notification', '', 'setting.disable_notification');
        } else {
            $notificationSetting = $this->telegram->buildInlineKeyBoardButton('🔔 Enable Notification', '', 'setting.enable_notification');
        }

        if ($settings['enable_all_event']) {
            $eventSetting = $this->telegram->buildInlineKeyBoardButton('🔕 Disable All Events', '', 'setting.disable_all_events');
        } else {
            $eventSetting = $this->telegram->buildInlineKeyBoardButton('🔔 Enable All Events', '', 'setting.enable_all_events');
        }

        return [
            [
                $notificationSetting,
            ], [
                $eventSetting,
                $this->telegram->buildInlineKeyBoardButton('Check Events', '', 'setting.check_events'),
            ],
        ];
    }
}
