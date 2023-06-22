<?php

use TelegramGithubNotify\App\Http\Actions\SetWebhookAction;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$setWebhookAction = new SetWebhookAction();
echo $setWebhookAction();
