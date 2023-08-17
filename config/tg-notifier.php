<?php

return [
    'app' => [
        'name' => $_ENV['APP_NAME'] ?? 'Telegram Bot Notify',
        'url' => $_ENV['APP_URL'] ?? 'http://localhost:3000',
        'timezone' => $_ENV['TIMEZONE'] ?? 'Asia/Ho_Chi_Minh',
        'image' => $_ENV['APP_IMAGE'] ?? 'public/images/github.jpeg',
    ],

    'telegram-bot' => [
        'token' => $_ENV['TELEGRAM_BOT_TOKEN'] ?? '',
        'chat_id' => $_ENV['TELEGRAM_BOT_CHAT_ID'] ?? '',
        'notify_chat_ids' => explode(',', $_ENV['TELEGRAM_NOTIFY_CHAT_IDS'] ?? ''),

        'set_webhook_url' => $_ENV['SET_WEBHOOK_URL'] ?? $_ENV['APP_URL'] . '/webhook/set.php',
    ],

    'author' => [
        'contact' => $_ENV['AUTHOR_CONTACT'] ?? 'https://t.me/tannp27',
        'source_code' => $_ENV['AUTHOR_SOURCE_CODE'] ?? 'https://github.com/lbiltech/telegram-bot-github-notify',
    ],
];
