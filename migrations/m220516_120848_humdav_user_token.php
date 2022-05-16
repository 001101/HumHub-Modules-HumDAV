<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

use humhub\components\Migration;

/**
 * Class m220516_120848_humdav_user_token
 */
class m220516_120848_humdav_user_token extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->safeCreateTable('humdav_user_token', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'name' => $this->string(),
            'algorithm' => $this->string(20),
            'token' => $this->text(),
            'salt' => $this->text(),
            'access_scopes' => $this->text(),
            'last_time_used' => $this->dateTime(),
            'last_time_used_by_ip' => $this->string(),
            'last_time_used_by_user_agent' => $this->string(),
            'created_at' => $this->dateTime(),
            'created_by_ip' => $this->string(),
            'created_by_user_agent' => $this->string()
        ]);

        $this->safeAddForeignKey('fk_humdav_user_token_user_id', 'humdav_user_token', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->safeDropTable('humdav_user_token');
    }
}
