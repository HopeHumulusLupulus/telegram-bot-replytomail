<?php
namespace Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Actions;
use Base\DB;

class RegisterCommand extends Command
{

    /**
     *
     * @var string Command Name
     */
    protected $name = "register";

    /**
     *
     * @var string Command Description
     */
    protected $description = "Register your duolingo username in this group";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        // This will update the chat status to typing...
        $this->replyWithChatAction(Actions::TYPING);

        if (! $arguments) {
            $this->replyWithMessage('Enter your duolingo username, example:');
            $this->replyWithMessage('/register MyUsername');
        } else {
            $profile = json_decode(file_get_contents('https://www.duolingo.com/users/' . $arguments));
            if ($profile) {
                $update = $this->telegram->getWebhookUpdates()->all();
                $this->replyWithMessage($update,true);
return;
                $db = DB::getInstance();
                try {
                    $db->perform("INSERT INTO users (username, registered_by, created) VALUES (:username, :registered_by, :created)", [
                        'username' => $profile->username,
                        'registered_by' => $update['message']['from']['id'],
                        'chat_id' => $update['message']['chat']['id'],
                        'created' => date('Y-m-d H:i:s', $update['message']['date'])
                    ]);
                    $this->replyWithMessage('Welcome ' . ($profile->fullname ?  : $profile->username) . '!');
                } catch (\Exception $e) {
                    if($update['message']['from']['id'] == 37900977) {
                        $this->replyWithMessage($update,true);
                        $this->replyWithMessage(print_r($e->getMessage(),true));
                    }
                    $this->replyWithMessage(($profile->fullname ?  : $profile->username) . ' already registered.');
                }
            } else {
                $this->replyWithMessage('Invalid username');
            }
        }
    }
}