<?php

use TelegramGithubNotify\App\Http\Actions\WebhookAction;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$webhookAction = new WebhookAction();
echo $webhookAction->delete();
