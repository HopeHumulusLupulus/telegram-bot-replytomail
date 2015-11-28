<?php
use Telegram\Bot\Api;
use Commands;
require 'vendor/autoload.php';
require 'config.php';

$telegram = new Api($config['token']);

// Standalone
$telegram->addCommands([
    Telegram\Bot\Commands\HelpCommand::class
]);

$telegram->commandsHandler(true);
