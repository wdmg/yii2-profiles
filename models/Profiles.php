<?php

namespace wdmg\profiles\models;

use wdmg\helpers\ArrayHelper;
use wdmg\helpers\DateAndTime;
use Yii;
use wdmg\base\models\ActiveRecordML;



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

class Profiles extends ActiveRecordML
{

    const STATUS_DRAFT = 0; // Profile has draft
    const STATUS_PUBLISHED = 1; // Profile has been published
    const STATUS_AWAITING = 2; // Profile has been awaiting
    const STATUS_SUSPENDED = 3; // Profile has been suspended

    private $_rules = [];
    private $_fields = [];
    private $_labels = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%profiles}}';
    }

    public function init()
    {
        parent::init();

        $fields = $this->getFields();
        foreach($fields as $field) {
            $name = $field->name;
            if ($this->hasAttribute($name)) {
                $this->setAttribute($name, $this->$name);

                // Set attribute label of custom field
                $label = $field->label;
                $this->_labels[$name] = $label;
                $this->_rules[] = [$name, 'string'];
                //$this->$name = '';
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = ArrayHelper::merge($rules, [
            [['locale', 'time_zone', 'status'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['time_zone'], 'string', 'max' => 64],
            [['created_at', 'updated_at'], 'safe'],
        ]);

        if (class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users'])) {
            $rules[] = [['user_id'], 'required'];
            $rules[] = [['user_id'], 'unique'];
            $rules[] = [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \wdmg\users\models\Users::class, 'targetAttribute' => ['user_id' => 'id']];
        }

        $rules = ArrayHelper::merge($rules, $this->_rules);
        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels = ArrayHelper::merge($labels, [
            'id' => Yii::t('app/modules/profiles', 'ID'),
            'user_id' => Yii::t('app/modules/profiles', 'User ID'),
            'locale' => Yii::t('app/modules/profiles', 'Locale'),
            'time_zone' => Yii::t('app/modules/profiles', 'Time zone'),
            'status' => Yii::t('app/modules/profiles', 'Status'),
            'created_at' => Yii::t('app/modules/profiles', 'Created at'),
            'updated_at' => Yii::t('app/modules/profiles', 'Updated at'),
        ]);

        $labels = ArrayHelper::merge($labels, $this->_labels);
        return $labels;
    }

    public function getUsersList() {
        if (class_exists('\wdmg\users\models\Users')) {
            $list = \wdmg\users\models\Users::findAll(['status' => \wdmg\users\models\Users::USR_STATUS_ACTIVE]);
            return ArrayHelper::map($list, 'id', 'username');
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    public function getTimezonesList($allZones = false, $notSet = false) {

        $list = [];
        if ($allZones) {
            $list = [
                '*' => Yii::t('app/modules/profiles', 'All zones')
            ];
        }

        if ($notSet) {
            $list = [
                'null' => Yii::t('app/modules/profiles', 'Not set')
            ];
        }

        return ArrayHelper::merge($list, DateAndTime::getTimezones() ?: []);
    }

    /**
     * @return array
     */
    public function getStatusesList($allStatuses = false)
    {
        $list = [];
        if ($allStatuses) {
            $list = [
                '*' => Yii::t('app/modules/profiles', 'All statuses')
            ];
        }

        return ArrayHelper::merge($list, [
            self::STATUS_DRAFT => Yii::t('app/modules/profiles', 'Draft'),
            self::STATUS_PUBLISHED => Yii::t('app/modules/profiles', 'Published'),
            self::STATUS_AWAITING => Yii::t('app/modules/profiles', 'Awaiting'),
            self::STATUS_SUSPENDED => Yii::t('app/modules/profiles', 'Suspended'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFields()
    {
        $locale = Yii::$app->sourceLanguage;
        if (isset(Yii::$app->language))
            $locale = Yii::$app->language;

        if (!$this->_fields) {
            $fields = Fields::find(['!=', 'status' => Fields::STATUS_DELETED])->andWhere(['locale' => $locale])->all();
            if (!empty($fields)) {
                $this->_fields = $fields;
                return $this->_fields;
            } else {
                return null;
            }
        }

        return $this->_fields;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        if (class_exists('\wdmg\users\models\Users'))
            return $this->hasMany(\wdmg\users\models\Users::class, ['id' => 'user_id']);
        else
            return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        if (class_exists('\wdmg\users\models\Users'))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'user_id']);
        else
            return null;
    }

}
