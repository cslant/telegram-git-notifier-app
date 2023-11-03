<?php

use CSlant\TelegramGitNotifierApp\Http\Actions\WebhookAction;

require __DIR__ . '/../init.php';

$webhookAction = new WebhookAction();
echo $webhookAction->delete();
