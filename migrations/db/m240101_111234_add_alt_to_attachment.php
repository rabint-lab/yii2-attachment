<?php

use yii\db\Migration;

class m240101_111234_add_alt_to_attachment extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->addColumn('{{%attachment}}', 'alt', $this->string(190)->null()->comment('عنوان'));
        $this->addColumn('{{%attachment}}', 'description', $this->text()->null()->comment('توضیحات'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%attachment}}','alt');
        $this->dropColumn('{{%attachment}}','description');
    }

}
