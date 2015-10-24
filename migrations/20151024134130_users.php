<?php

use Phinx\Migration\AbstractMigration;

class Users extends AbstractMigration
{
    public function up()
    {
        $user_account = $this->table('users')
            ->addColumn('username', 'string', array('limit' => 150))
            ->addColumn('registered_by', 'integer')
            ->addColumn('chat_id', 'integer')
            ->addColumn('created', 'datetime', array('default' => 'CURRENT_TIMESTAMP'))
            ->addIndex(array('username', 'chat_id'), array('unique' => true));
        $user_account->save();
    }

    public function down()
    {
        $this->table('users')->drop();
    }
}
