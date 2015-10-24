### Installation
    git clone --recursive https://github.com/vitormattos/telegram-bot-duolingo.git && cd telegram-bot-duolingo
    composer install

### Create Telegram Bot and receive a API Token

Add @BotFather to yours contacts and start the help with command
    /help
Create a bot with
    /newbot
Create a directory called config and config.php inside this directory and put
your api token into config.php:
```shell
    mkdir config
    echo "<?php\n\$config['token'] = <your_token>"
```
