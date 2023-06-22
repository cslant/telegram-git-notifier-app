<?php

return [
    'app_name' => 'Telegram Github Notify',
    'default' => [
        'tg_token' => getenv('TELEGRAM_BOT_TOKEN'),
        'tg_chat_ids' => explode('|', getenv('TELEGRAM_BOT_CHAT_IDS')),
    ],
    'timezone' => getenv('TIMEZONE', 'Asia/Ho_Chi_Minh'),
];
