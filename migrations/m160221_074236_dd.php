<?php

use yii\db\Schema;
use yii\db\Migration;

class m160221_074236_dd extends Migration
{
	public $tableName = 'test_dd';
	
    public function safeUp()
    {
        if ($this->db->driverName === 'mysql')
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $schema = $this->db->getSchema();

        $this->createTable('{{%'.$this->tableName.'}}',[
            'id' => $this->primaryKey(),
            'name' => $this->string(127)->notNull(),
            'deleted' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'created_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' => $this->integer(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%'.$this->tableName.'}}');
    }
}
