<?php

return [
    'app_name' => 'Telegram Github Notify',
    'telegram-bot' => [
        'token' => $_ENV['TELEGRAM_BOT_TOKEN'] ?? 'TELEGRAM_BOT_TOKEN',
        'chat_ids' => explode('|', $_ENV['TELEGRAM_BOT_CHAT_IDS'] ?? 'TELEGRAM_BOT_CHAT_IDS'),
    ],
    'timezone' => $_ENV['TIMEZONE'] ?? 'Asia/Ho_Chi_Minh',
];
