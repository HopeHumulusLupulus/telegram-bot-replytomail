<?php
use Telegram\Bot\Api;
use Commands;
require 'vendor/autoload.php';
require 'config.php';

$telegram = new Api($config['token']);

// Standalone
$telegram->addCommands([
    Telegram\Bot\Commands\HelpCommand::class,
    Commands\StartCommand::class,
    Commands\RegisterCommand::class,
    Commands\RemoveCommand::class,
    Commands\RankingCommand::class,
    Commands\ListCommand::class
]);

$telegram->commandsHandler(true);
