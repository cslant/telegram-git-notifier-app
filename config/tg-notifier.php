<?php

return [
    'app_name' => $_ENV['APP_NAME'] ?? 'Telegram Github Notify',
    'app_url' => $_ENV['APP_URL'] ?? 'APP_URL',

    'telegram-bot' => [
        'token' => $_ENV['TELEGRAM_BOT_TOKEN'] ?? 'TELEGRAM_BOT_TOKEN',
        'chat_id' => $_ENV['TELEGRAM_BOT_CHAT_ID'] ?? 'TELEGRAM_BOT_CHAT_ID',
        'gr_chat_ids' => explode(',', $_ENV['TELEGRAM_BOT_GR_CHAT_IDS'] ?? 'TELEGRAM_BOT_GR_CHAT_IDS'),
        'set_webhook_url' => $_ENV['SET_WEBHOOK_URL'] ?? $_ENV['APP_URL'] . '/setWebhook',
    ],

    'timezone' => $_ENV['TIMEZONE'] ?? 'Asia/Ho_Chi_Minh',
];
