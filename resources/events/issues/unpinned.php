<?php
/**
 * @var $payload mixed
 */

$message = "🔔 <b>Issue Unpinned</b> from <a href=\"{$payload->repository->html_url}\">{$payload->repository->full_name} </a> by <a href=\"{$payload->sender->html_url}\">@{$payload->sender->login}</a>\n\n";

$message .= "📢 <b>{$payload->issue->title}</b>\n";

if (isset($payload->issue->assignee)) {
    $message .= "🙋 Assignee: <a href=\"{$payload->issue->assignee->html_url}\">@{$payload->issue->assignee->login}</a>\n\n";
}

$message .= require __DIR__ . '/partials/_body.php';

echo $message;
