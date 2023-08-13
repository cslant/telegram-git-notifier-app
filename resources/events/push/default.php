<?php
/**
 * @var $payload mixed
 */

$count = count($payload->commits);
$noun = ($count > 1) ? "commits" : "commit";

$ref = explode('/', $payload->ref);
$branch = end($ref);

$message = "⚙️ <b>{$count}</b> new {$noun} to <b>{$payload->repository->full_name}:<code>{$branch}</code></b>\n\n";

foreach ($payload->commits as $commit) {
    $commitId = substr($commit->id, -7);
    $message .= "<a href=\"{$commit->url}\">{$commitId}</a>: {$commit->message} - by <i>{$commit->author->name}</i>\n";
}

$message .= "\n👤 Pushed by : <b>{$payload->pusher->name}</b>\n";

echo $message;
