<?php
use Telegram\Bot\Api;
require 'vendor/autoload.php';
require 'config.php';

$telegram = new Api($config['token']);

// Standalone
$telegram->addCommands([
    Telegram\Bot\Commands\HelpCommand::class,
    Commands\StartCommand::class,
    Commands\RegisterCommand::class,
    Commands\RankingCommand::class
    
]);

$telegram->commandsHandler(true);
