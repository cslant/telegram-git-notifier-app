<?php

use LbilTech\TelegramGitNotifierApp\Services\CommandService;

$menuCommands = CommandService::MENU_COMMANDS ?? [];
?>

<b>BOT MENU</b> 🤖

<?php foreach ($menuCommands as $menuCommand) : ?>
<b><?= $menuCommand['command'] ?></b> - <?= $menuCommand['description'] ?>

<?php endforeach; ?>
