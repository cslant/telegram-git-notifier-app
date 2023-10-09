<?php

use LbilTech\TelegramGitNotifierApp\Http\Actions\WebhookAction;

require __DIR__ . '/../init.php';

$webhookAction = new WebhookAction();
echo $webhookAction->set();
