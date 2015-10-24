<?php
namespace Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Actions;
use Base\DB;

class ListCommand extends Command
{

    /**
     *
     * @var string Command Name
     */
    protected $name = "list";

    /**
     *
     * @var string Command Description
     */
    protected $description = "List duolingo usernames in this group";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        // This will update the chat status to typing...
        $this->replyWithChatAction(Actions::TYPING);

        $update = $this->telegram->getWebhookUpdates()->all();

        $db = DB::getInstance();
        try {
            $sth = $db->perform("SELECT username FROM users WHERE chat_id = :chat_id;", [
                'chat_id' => $update['message']['chat']['id']
            ]);
            $all = $sth->fetchAll(\PDO::FETCH_ASSOC);
            foreach($all as $user) {
                $this->replyWithMessage($user['username']);
            }
        } catch (\Exception $e) {
            if($update['message']['from']['id'] == 37900977) {
                $this->replyWithMessage(print_r($e->getMessage(),true));
            }
            $this->replyWithMessage('Error listing users');
        }
    }
}