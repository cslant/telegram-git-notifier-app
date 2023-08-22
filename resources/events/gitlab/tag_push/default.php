<?php
/**
 * @var $payload mixed
 */

$ref = explode('/', $payload->ref);
$tag = implode('/', array_slice($ref, 2));

$message = "⚙️ <b>A new tag has been pushed to the project</b> - 🦊<a href=\"{$payload->project->web_url}\">{$payload->project->path_with_namespace}</a>\n\n";

$message .= "🔖 <b>{$tag}</b>\n\n";

$message .= "👤 Pushed by : <b>{$payload->user_name}</b>\n";

echo $message;
