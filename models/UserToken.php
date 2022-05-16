<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

namespace humhub\modules\humdav\models;

use Exception;
use humhub\components\ActiveRecord;
use humhub\libs\UUID;
use Yii;

class UserToken extends ActiveRecord {
    public $defaultAlgorithm = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'humdav_user_token';
    }

    /**
     * {@inheritdoc}
     */
    public function init() {
        parent::init();

        $this->defaultAlgorithm = 'sha1md5';
        if (function_exists('hash_algos')) {
            $algos = hash_algos();
            if (in_array('sha512', $algos) && in_array('whirlpool', $algos))
                $this->defaultAlgorithm = 'sha512whirlpool';
            elseif (in_array('sha512', $algos))
                $this->defaultAlgorithm = 'sha512';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert) {
        if ($insert) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->created_by_ip = Yii::$app->request->getUserIP();
            $this->created_by_user_agent = Yii::$app->request->getUserAgent();
        }

        return parent::beforeSave($insert);
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id'], 'integer'],
            [['algorithm'], 'string', 'max' => 20],
            [['name', 'token', 'salt'], 'string'],
            [['name'], 'safe'],
            [['user_id', 'name'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'algorithm' => 'Algorithm',
            'token' => 'Token',
            'salt' => 'Salt',
            'last_time_used' => 'Last Time Used',
            'last_time_used_by_ip' => 'Last Time Used by IP',
            'last_time_used_by_user_agent' => 'Last Time Used by User Agent',
            'created_at' => 'Created At',
            'created_by_ip' => 'Created by IP',
            'created_by_user_agent' => 'Created by User Agent'
        ];
    }

    public static function getViewableFields() {
        return [
            'name',
            'last_time_used',
            'last_time_used_by_ip',
            'last_time_used_by_user_agent',
            'created_at',
            'created_by_ip',
            'created_by_user_agent'
        ];
    }

    /**
     * Validates a given token against database record
     *
     * @param string $token unhashed
     * @return boolean Success
     */
    public function validateToken($token) {
        return Yii::$app->security->compareString($this->token, $this->hashToken($token)) === true;
    }

    /**
     * Hashes a token
     *
     * @param type $token
     * @return string Hashed token
     */
    private function hashToken($token) {
        $token .= $this->salt;

        switch ($this->algorithm) {
            case 'sha1md5':
                return sha1(md5($token));
            case 'sha512whirlpool':
                return hash('sha512', hash('whirlpool', $token));
            case 'sha512':
                return hash('sha512', $token);
            
            default:
                throw new Exception('Invalid Hashing Algorithm!');
        }
    }

    /**
     * Generates a token and hash it
     *
     * @return null|string The token if successful, otherwise null
     */
    public function generateToken() {
        $newToken = UUID::v4();
        return $this->setToken($newToken) ? $newToken : null;
    }

    /**
     * Sets a token and hash it
     *
     * @return boolean Success
     */
    public function setToken(string $newToken) {
        if (!empty($this->token)) {
            return false;
        }

        $this->salt = UUID::v4();
        $this->algorithm = $this->defaultAlgorithm;
        $this->token = $this->hashToken($newToken);

        return true;
    }

    public function updateLastTimeUsed() {
        $this->last_time_used = date('Y-m-d H:i:s');
        $this->last_time_used_by_ip = Yii::$app->request->getUserIP();
        $this->last_time_used_by_user_agent = Yii::$app->request->getUserAgent();
    }

    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function canEdit() {
        return $this->user_id === Yii::$app->user->id;
    }
}
