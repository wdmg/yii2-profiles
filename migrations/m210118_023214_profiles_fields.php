<?php

use yii\db\Migration;

/**
 * Class m210118_023214_profiles_fields
 */
class m210118_023214_profiles_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql')
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $defaultLocale = null;
        if (isset(Yii::$app->sourceLanguage))
            $defaultLocale = Yii::$app->sourceLanguage;

        $this->createTable('{{%profiles_fields}}', [
            'id' => $this->primaryKey(11),
            'source_id' => $this->integer(11)->null(),
            'label' => $this->string(64),
            'name' => $this->string(64),
            'placeholder' => $this->string(124),
            'description' => $this->string(255),
            'type' => $this->smallInteger(2)->notNull(),
            'sort_order' => $this->smallInteger(3)->defaultValue(10),
            'params' => $this->text(),
            'is_required' => $this->boolean(),
            'status' => $this->tinyInteger(1)->null()->defaultValue(0),
            'locale' => $this->string(10)->defaultValue($defaultLocale),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->integer(11)->null(),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer(11)->null(),
        ], $tableOptions);

        $this->createIndex(
            '{{%idx_profiles_fields}}',
            '{{%profiles_fields}}',
            [
                'id',
                'label',
                'name',
                'type',
                'sort_order',
                'status'
            ]
        );

        // Setup foreign key to source id
        $this->createIndex('{{%idx-profiles_fields-source}}', '{{%profiles_fields}}', ['source_id']);
        $this->addForeignKey(
            'fk_profiles_fields_to_source',
            '{{%profiles_fields}}',
            'source_id',
            '{{%profiles_fields}}',
            'id',
            'NO ACTION',
            'CASCADE'
        );

        // If module `Translations` exist setup foreign key `locale` to `trans_langs.locale`
        $this->createIndex('{{%idx-profiles_fields-locale}}', '{{%profiles_fields}}', ['locale']);
        if (class_exists('\wdmg\translations\models\Languages')) {
            $langsTable = \wdmg\translations\models\Languages::tableName();
            $this->addForeignKey(
                'fk_profiles_fields_to_langs',
                '{{%profiles_fields}}',
                'locale',
                $langsTable,
                'locale',
                'NO ACTION',
                'CASCADE'
            );
        }

        // If exist module `Users` set foreign key `created_by`, `updated_by` to `users.id`
        if (class_exists('\wdmg\users\models\Users')) {
            $this->createIndex('{{%idx-profiles_fields-created}}','{{%profiles_fields}}', ['created_by'],false);
            $this->createIndex('{{%idx-profiles_fields-updated}}','{{%profiles_fields}}', ['updated_by'],false);
            $userTable = \wdmg\users\models\Users::tableName();
            $this->addForeignKey(
                'fk_profiles_fields_to_users1',
                '{{%profiles_fields}}',
                'created_by',
                $userTable,
                'id',
                'NO ACTION',
                'CASCADE'
            );
            $this->addForeignKey(
                'fk_profiles_fields_to_users2',
                '{{%profiles_fields}}',
                'updated_by',
                $userTable,
                'id',
                'NO ACTION',
                'CASCADE'
            );
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx_profiles_fields}}', '{{%profiles_fields}}');
        $this->dropIndex('{{%idx-profiles_fields-source}}', '{{%profiles_fields}}');

        $this->dropForeignKey(
            'fk_profiles_fields_to_source',
            '{{%profiles_fields}}'
        );

        $this->dropIndex('{{%idx-profiles_fields-locale}}', '{{%profiles_fields}}');
        if (class_exists('\wdmg\translations\models\Languages')) {
            $this->dropForeignKey(
                'fk_profiles_fields_to_langs',
                '{{%profiles_fields}}'
            );
        }

        if (class_exists('\wdmg\users\models\Users')) {
            $this->dropIndex('{{%idx-profiles_fields-created}}', '{{%profiles_fields}}');
            $this->dropIndex('{{%idx-profiles_fields-updated}}', '{{%profiles_fields}}');
            $userTable = \wdmg\users\models\Users::tableName();
            if (!(Yii::$app->db->getTableSchema($userTable, true) === null)) {
                $this->dropForeignKey(
                    'fk_profiles_fields_to_users1',
                    '{{%profiles_fields}}'
                );
                $this->dropForeignKey(
                    'fk_profiles_fields_to_users2',
                    '{{%profiles_fields}}'
                );
            }
        }

        $this->truncateTable('{{%profiles_fields}}');
        $this->dropTable('{{%profiles_fields}}');
    }

}
