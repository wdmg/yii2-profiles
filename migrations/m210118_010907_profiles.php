<?php

use yii\db\Migration;

/**
 * Class m210118_010907_profiles
 */
class m210118_010907_profiles extends Migration
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

        $this->createTable('{{%profiles}}', [
            'id'=> $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            // ...
            'locale' => $this->string(10)->defaultValue($defaultLocale),
            'time_zone' => $this->string(64),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            '{{%idx_profiles}}',
            '{{%profiles}}',
            [
                'id',
                'user_id',
                'locale',
                'time_zone'
            ]
        );

        // If module `Translations` exist setup foreign key `locale` to `trans_langs.locale`
        $this->createIndex('{{%idx-profiles-locale}}', '{{%profiles}}', ['locale']);
        if (class_exists('\wdmg\translations\models\Languages')) {
            $langsTable = \wdmg\translations\models\Languages::tableName();
            $this->addForeignKey(
                'fk_profiles_to_langs',
                '{{%profiles}}',
                'locale',
                $langsTable,
                'locale',
                'NO ACTION',
                'CASCADE'
            );
        }

        // If exist module `Users` set foreign key `user_id` to `users.id`
        if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users'])) {
            $userTable = \wdmg\users\models\Users::tableName();
            $this->addForeignKey(
                'fk_profiles_to_users',
                '{{%profiles}}',
                'user_id',
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
        $this->dropIndex('idx_profiles', '{{%profiles}}');

        $this->dropIndex('{{%idx-profiles-locale}}', '{{%profiles}}');
        if (class_exists('\wdmg\translations\models\Languages')) {
            $this->dropForeignKey(
                'fk_profiles_to_langs',
                '{{%profiles}}'
            );
        }

        if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users'])) {
            $userTable = \wdmg\users\models\Users::tableName();
            if (!(Yii::$app->db->getTableSchema($userTable, true) === null)) {
                $this->dropForeignKey(
                    'fk_profiles_to_users',
                    '{{%profiles}}'
                );
            }
        }

        $this->truncateTable('{{%profiles}}');
        $this->dropTable('{{%profiles}}');
    }

}
