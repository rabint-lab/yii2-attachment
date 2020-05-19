<?php

use yii\db\Migration;

class m190101_111234_create_table_attachment extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%attachment}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY')->comment('شناسه'),
            'user_id' => $this->integer(11)->comment('کاربر'),
            'component' => $this->string(32)->comment('زیر سیستم'),
            'path' => $this->string(190)->comment(''),
            'title' => $this->string(190)->comment(''),
            'name' => $this->string(190)->comment(''),
            'size' => $this->integer(11)->comment('حجم فایل'),
            'extension' => $this->char(10)->comment('پسوند'),
            'type' => $this->string(20)->comment('نوع فایل'),
            'mime' => $this->string(100)->comment('نوع مایم'),
            'created_at' => $this->integer(4)->unsigned()->comment('تاریخ ایجاد'),
            'updated_at' => $this->integer(4)->unsigned()->comment('تاریخ بروزرسانی'),
            'ip' => $this->string(48)->comment('آی پی آپلود کننده'),
            'weight' => $this->integer(11)->comment('ترتیب'),
            'protected' => $this->tinyInteger(1)->notNull()->comment('محافظت شده'),
            'storage' => $this->string(32)->notNull()->defaultValue('local')->comment('محل ذخیره سازی'),
            'download_count' => $this->integer(10)->unsigned()->comment('تعداد دانلود'),
            'meta' => $this->text()->comment('متا'),
        ], $tableOptions);

        $this->createIndex('fk_file_user_id1_idx', '{{%attachment}}', 'user_id');
        $this->addForeignKey('fk_attachment_user1', '{{%attachment}}', 'user_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%attachment}}');
    }
}
