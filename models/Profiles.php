<?php

namespace wdmg\profiles\models;

use function PHPSTORM_META\elementType;
use wdmg\helpers\ArrayHelper;
use wdmg\helpers\DateAndTime;
use Yii;
use wdmg\base\models\ActiveRecordML;
use yii\base\InvalidConfigException;


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
            if (isset($field->name)) {
                $name = $field->name;
                if ($this->hasAttribute($name)) {
                    $this->setAttribute($name, $this->$name);

                    // Set attribute label of custom field
                    $label = $field->label;
                    $this->_labels[$name] = $label;
                    $this->_rules[] = [$name, 'string'];
                }
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
            ['user_id', 'required', 'on' => self::SCENARIO_CREATE],
            ['user_id', 'in', 'range' => array_keys(self::getUsersList(false)), 'on' => self::SCENARIO_CREATE],
            [['user_id', 'status'], 'integer'],
            ['status', 'in', 'range' => array_keys(self::getStatusesList(false))],
            [['locale'], 'string', 'max' => 10],
            ['locale', 'in', 'range' => array_keys(self::getLanguagesList(false))],
            [['time_zone'], 'string', 'max' => 64],
            ['time_zone', 'in', 'range' => array_keys(self::getTimezonesList(false))],
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

    public function getUsersList($allUsers = false) {
        if (class_exists('\wdmg\users\models\Users')) {

            $usersTable = \wdmg\users\models\Users::tableName();
            $users = \wdmg\users\models\Users::find()->where(["$usersTable.`status`" => \wdmg\users\models\Users::USR_STATUS_ACTIVE]);
            $users->select(["$usersTable.`id`", "$usersTable.`username`"]);
            if (!$allUsers) {
                $profilesTable = self::tableName();
                $users->leftJoin($profilesTable, "$usersTable.`id` = $profilesTable.`user_id`")
                    ->andWhere(['is', "$profilesTable.`user_id`", null]);
            } else {
                $users = \wdmg\users\models\Users::find()->where(['status' => \wdmg\users\models\Users::USR_STATUS_ACTIVE]);
            }

            return ArrayHelper::map($users->asArray()->all(), 'id', 'username');
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
    public function getFields($locale = null, $caching = true)
    {
        if (is_null($locale)) {
            $locale = Yii::$app->sourceLanguage;
            if (isset(Yii::$app->language))
                $locale = Yii::$app->language;
        }

        if ($fields = Fields::find()->where(['!=', 'status', Fields::STATUS_DELETED])) {

            if (isset($locale))
                $fields->andWhere(['locale' => $locale]);

            $list = $fields->orderBy('sort_order')->all();
            if (!empty($list)) {
                if (!$caching)
                    return $list;
                else
                    $this->_fields = $list;
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


    public static function addFieldColumn($columnName = null, $columnType = null, $maxLength = null) {

        if (!is_string($columnName)) {
            throw new InvalidConfigException("Method property `columnName` must be a string.");
        }

        if (!is_string($columnType)) {
            throw new InvalidConfigException("Method property `columnType` must be a string.");
        }

        if (!empty($maxLength) && !is_int($maxLength)) {
            throw new InvalidConfigException("Method property `maxLength` must be a integer.");
        }

        $db = Yii::$app->getDb();
        $schema = $db->getSchema();
        $table = self::tableName();
        $column = trim($columnName);
        if (is_null($schema->getTableSchema($table)->getColumn($column))) {

            $length = null;
            if (in_array($columnType, ['textarea'])) {
                $schemaType = $schema::TYPE_TEXT;
            } elseif (in_array($columnType, ['checkbox'])) {
                $schemaType = $schema::TYPE_BOOLEAN;

                if (!$maxLength)
                    $length = 1;

            } elseif (in_array($columnType, ['number', 'range'])) {
                $schemaType = $schema::TYPE_INTEGER;

                if (!$maxLength)
                    $length = 4;

            } elseif (in_array($columnType, ['date'])) {
                $schemaType = $schema::TYPE_DATE;
            } elseif (in_array($columnType, ['time'])) {
                $schemaType = $schema::TYPE_TIME;
            } elseif (in_array($columnType, ['datetime', 'datetime-local'])) {
                $schemaType = $schema::TYPE_DATETIME;
            } else {
                $schemaType = $schema::TYPE_STRING;

                if (!$maxLength)
                    $length = 255;
            }

            if ($maxLength)
                $length = intval($maxLength);

            $type = $schema->createColumnSchemaBuilder($schemaType, $length)->after('status');
            $results = $db->createCommand()->addColumn(self::tableName(), $column, $type)->execute();

            return $results;
        }

        return false;
    }

    public static function dropFieldColumn($columnName = null) {
        if (!is_string($columnName)) {
            throw new InvalidConfigException("Method property `columnName` must be a string.");
        }

        $db = Yii::$app->getDb();
        $schema = $db->getSchema();
        $table = self::tableName();
        $column = trim($columnName);
        if (!is_null($schema->getTableSchema($table)->getColumn($column))) {
            $results = $db->createCommand()->dropColumn(self::tableName(), $column)->execute();
            return $results;
        }

        return false;
    }
}
