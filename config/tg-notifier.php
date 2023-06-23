<?php

return [
    'app' => [
        'name' => $_ENV['APP_NAME'] ?? 'Telegram Github Notify',
        'url' => $_ENV['APP_URL'] ?? 'APP_URL',
        'timezone' => $_ENV['TIMEZONE'] ?? 'Asia/Ho_Chi_Minh',
    ],

    'telegram-bot' => [
        'token' => $_ENV['TELEGRAM_BOT_TOKEN'] ?? '',
        'chat_id' => $_ENV['TELEGRAM_BOT_CHAT_ID'] ?? '',
        'gr_chat_ids' => explode(',', $_ENV['TELEGRAM_BOT_GR_CHAT_IDS'] ?? ''),

        'set_webhook_url' => $_ENV['SET_WEBHOOK_URL'] ?? $_ENV['APP_URL'] . '/setWebhook',
    ],
];
