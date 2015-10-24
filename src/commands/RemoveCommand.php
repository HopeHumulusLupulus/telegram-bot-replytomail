<?php
namespace Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Actions;
use Base\DB;

class RemoveCommand extends Command
{

    /**
     *
     * @var string Command Name
     */
    protected $name = "remove";

    /**
     *
     * @var string Command Description
     */
    protected $description = "Remove duolingo username from this group";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        // This will update the chat status to typing...
        $this->replyWithChatAction(Actions::TYPING);
        $this->replyWithMessage(print_r($this->telegram->getWebhookUpdates()->all(), true));

        return;
        if (! $arguments) {
            $this->replyWithMessage('Enter your duolingo username, example:');
            $this->replyWithMessage('/register MyUsername');
        } else {
            $profile = json_decode(file_get_contents('https://www.duolingo.com/users/' . $arguments));
            if ($profile) {
                $update = $this->telegram->getWebhookUpdates()->all();

                $db = DB::getInstance();
                try {
                    $db->perform("INSERT INTO users (username, registered_by, chat_id, created) VALUES (:username, :registered_by, :chat_id, :created)", [
                        'username' => $profile->username,
                        'registered_by' => $update['message']['from']['id'],
                        'created' => date('Y-m-d H:i:s', $update['message']['date'])
                    ]);
                    $this->replyWithMessage('Welcome ' . ($profile->fullname ?  : $profile->username) . '!');
                } catch (\Exception $e) {
                    $this->replyWithMessage(($profile->fullname ?  : $profile->username) . ' already registered.');
                }
            } else {
                $this->replyWithMessage('Invalid username');
            }
        }
    }
}