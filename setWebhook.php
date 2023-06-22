<?php

use TelegramGithubNotify\App\Http\Actions\SetWebhookAction;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

return new SetWebhookAction();
