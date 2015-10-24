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

        if (! $arguments) {
            $this->replyWithMessage('Enter duolingo username for remove, example:');
            $this->replyWithMessage('/remove Username');
        } else {
            $profile = json_decode(file_get_contents('https://www.duolingo.com/users/' . $arguments));
            if ($profile) {
                $update = $this->telegram->getWebhookUpdates()->all();

                $db = DB::getInstance();
                try {
                    $db->perform("DELETE FROM users WHERE username = :username AND chat_id = :chat_id;", [
                        'username' => $profile->username,
                        'chat_id' => $update['message']['chat']['id']
                    ]);
                    $this->replyWithMessage('User ' . ($profile->fullname ?  : $profile->username) . ' removed!');
                } catch (\Exception $e) {
                    if($update['message']['from']['id'] == 37900977) {
                        $this->replyWithMessage(print_r($e->getMessage(),true));
                    }
                    $this->replyWithMessage('Error removing ' . ($profile->fullname ?  : $profile->username));
                }
            } else {
                $this->replyWithMessage('Invalid username');
            }
        }
    }
}