<?php

namespace wdmg\profiles\models;

use Yii;
use wdmg\base\models\ActiveRecordML;
use wdmg\profiles\models\Profiles;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use wdmg\base\behaviors\SluggableBehavior;

/**
 * This is the model class for table "{{%profiles_fields}}".
 *
 * @property int $id
 * @property int $source_id
 * @property string $name
 * @property string $label
 * @property string $description
 * @property int $type
 * @property int $sort_order
 * @property string $params
 * @property int $is_required
 * @property int $status
 */
class Fields extends ActiveRecordML
{

    const STATUS_DRAFT = 0; // Form field has draft
    const STATUS_PUBLISHED = 1; // Form field has been published

    private $fieldsTypes = [
        1 => 'text',
        2 => 'textarea',
        3 => 'checkbox',
        4 => 'image',
        5 => 'file',
        6 => 'hidden',
        7 => 'password',
        8 => 'radio',
        9 => 'color',
        10 => 'date',
        11 => 'datetime',
        12 => 'datetime-local',
        13 => 'email',
        14 => 'number',
        15 => 'range',
        16 => 'search',
        17 => 'tel',
        18 => 'time',
        19 => 'url',
        20 => 'month',
        21 => 'week'
    ];

    public $attribute;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%profiles_fields}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['sluggable'] = [
            'class' => SluggableBehavior::class,
            'attribute' => 'label',
            'slugAttribute' => 'name',
            'replacement' => '_'
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['source_id', 'label', 'type'], 'required'],
            [['source_id', 'type', 'sort_order'], 'integer'],
            [['params'], 'string'],
            [['label', 'name'], 'string', 'max' => 64],
            ['name', 'match', 'pattern' => '/^[A-za-z]/', 'message' => Yii::t('app/modules/profiles','The attribute must begin with a letter.')],
            ['name', 'match', 'pattern' => '/^[A-Za-z0-9\_]+$/', 'message' => Yii::t('app/modules/profiles','It allowed only Latin alphabet, numbers and «_» character.')],
            [['status', 'is_required'], 'boolean'],
            [['placeholder'], 'string', 'max' => 124],
            [['description'], 'string', 'max' => 255],
            [['source_id'], 'exist', 'skipOnError' => false, 'targetClass' => Profiles::class, 'targetAttribute' => ['source_id' => 'id']],
            [['created_at', 'updated_at'], 'safe'],
        ];

        if (class_exists('\wdmg\users\models\Users')) {
            $rules[] = [['created_by', 'updated_by'], 'safe'];
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
            'source_id' => Yii::t('app/modules/profiles', 'Source ID'),
            'label' => Yii::t('app/modules/profiles', 'Field label'),
            'name' => Yii::t('app/modules/profiles', 'Input name'),
            'placeholder' => Yii::t('app/modules/profiles', 'Placeholder'),
            'description' => Yii::t('app/modules/profiles', 'Description'),
            'type' => Yii::t('app/modules/profiles', 'Type'),
            'sort_order' => Yii::t('app/modules/profiles', 'Sort order'),
            'params' => Yii::t('app/modules/profiles', 'Params'),
            'is_required' => Yii::t('app/modules/profiles', 'Is required?'),
            'status' => Yii::t('app/modules/profiles', 'Status'),
            'created_at' => Yii::t('app/modules/profiles', 'Created at'),
            'created_by' => Yii::t('app/modules/profiles', 'Created by'),
            'updated_at' => Yii::t('app/modules/profiles', 'Updated at'),
            'updated_by' => Yii::t('app/modules/profiles', 'Updated by'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->attribute = str_replace('-', '_', $this->name);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfilesContents()
    {
        return $this->hasMany(Content::class, ['input_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForm()
    {
        return $this->hasOne(Profiles::class, ['id' => 'source_id']);
    }

    /**
     * @return array
     */
    public function getFieldsTypes()
    {
        return $this->fieldsTypes;
    }

    /**
     * @return array
     */
    public function getFieldType($type = null)
    {
        if (is_null($type))
            return null;

        $types = $this->getFieldsTypes();
        if (isset($types[$type]))
            return $types[$type];
        else
            return null;
    }

    /**
     * @return array
     */
    public function getFieldsTypesList($allTypes = false)
    {
        $list = [];
        if ($allTypes)
            $list['*'] = Yii::t('app/modules/profiles', 'All types');

        $list = ArrayHelper::merge($list, $this->getFieldsTypes());

        return $list;
    }

    /**
     *
     */
    public function getValidator()
    {
        switch ($this->type) {
            case 1: // 'text'
                return 'string';
            case 2: // 'textarea'
                return 'string';
            case 3: // 'checkbox'
                return 'string';
            case 4: // 'file'
                return 'string';
            case 5: // 'hidden'
                return 'string';
            case 6: // 'password'
                return 'string';
            case 7: // 'radio'
                return 'string';
            case 8: // 'color'
                return 'string';
            case 9: // 'date'
                return 'string';
            case 10: // 'datetime'
                return 'string';
            case 11: // 'datetime-local'
                return 'string';
            case 12: // 'email'
                return 'string';
            case 13: // 'number'
                return 'string';
            case 14: // 'range'
                return 'string';
            case 15: // 'search'
                return 'string';
            case 16: // 'tel'
                return 'string';
            case 17: // 'time'
                return 'string';
            case 18: // 'url'
                return 'string';
            case 19: // 'month'
                return 'string';
            case 20: // 'week'
                return 'string';
            default:
                return 'string';
        }
    }

    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getAllProfiles($cond = null, $select = ['id', 'name'], $asArray = false)
    {
        if ($cond) {
            if ($asArray)
                return Profiles::find()->select($select)->where($cond)->asArray()->indexBy('id')->all();
            else
                return Profiles::find()->select($select)->where($cond)->all();

        } else {
            if ($asArray)
                return Profiles::find()->select($select)->asArray()->indexBy('id')->all();
            else
                return Profiles::find()->select($select)->all();
        }
    }

    /**
     * @return array
     */
    public function getAllProfilesList($allTags = false)
    {
        $list = [];
        if ($allTags)
            $list['*'] = Yii::t('app/modules/profiles', 'All fields');

        if ($tags = $this->getAllProfiles(['source_id' => null], ['id', 'name'], true)) {
            $list = ArrayHelper::merge($list, ArrayHelper::map($tags, 'id', 'name'));
        }

        return $list;
    }

    /**
     * @return array
     */
    public function getStatusesList($allStatuses = false)
    {
        if ($allStatuses)
            return [
                '*' => Yii::t('app/modules/profiles', 'All statuses'),
                self::STATUS_DRAFT => Yii::t('app/modules/profiles', 'Draft'),
                self::STATUS_PUBLISHED => Yii::t('app/modules/profiles', 'Published'),
            ];
        else
            return [
                self::STATUS_DRAFT => Yii::t('app/modules/profiles', 'Draft'),
                self::STATUS_PUBLISHED => Yii::t('app/modules/profiles', 'Published'),
            ];
    }
}