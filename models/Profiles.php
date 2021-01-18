<?php

namespace wdmg\Profiles\models;

use Yii;

/**
 * This is the model class for table "{{%profiles}}".
 *
 * @property int $id
 * @property int $user_id
 *
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Users $user
 */

class Profiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%profiles}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['locale', 'time_zone'], 'required'],
            [['user_id'], 'integer'],
            [['time_zone'], 'string', 'max' => 64],
            [['created_at', 'updated_at'], 'safe'],
        ];

        if (class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users'])) {
            $rules[] = [['user_id'], 'required'];
            $rules[] = [['user_id'], 'unique'];
            $rules[] = [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \wdmg\users\models\Users::class, 'targetAttribute' => ['user_id' => 'id']];
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/profiles', 'ID'),
            'user_id' => Yii::t('app/modules/profiles', 'User ID'),
            // ...
            'locale' => Yii::t('app/modules/profiles', 'Locale'),
            'time_zone' => Yii::t('app/modules/profiles', 'Time zone'),
            'created_at' => Yii::t('app/modules/profiles', 'Created at'),
            'updated_at' => Yii::t('app/modules/profiles', 'Updated at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users']))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'user_id']);
        else
            return null;
    }
}
