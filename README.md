[![Yii2](https://img.shields.io/badge/required-Yii2_v2.0.33-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Downloads](https://img.shields.io/packagist/dt/wdmg/yii2-profiles.svg)](https://packagist.org/packages/wdmg/yii2-profiles)
[![Packagist Version](https://img.shields.io/packagist/v/wdmg/yii2-profiles.svg)](https://packagist.org/packages/wdmg/yii2-profiles)
![Progress](https://img.shields.io/badge/progress-in_development-red.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-profiles.svg)](https://github.com/wdmg/yii2-profiles/blob/master/LICENSE)

# Yii2 User profiles
System of managing user profiles

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.40 and newest
* [Yii2 Base](https://github.com/wdmg/yii2-base) module (required)
* [Yii2 Users](https://github.com/wdmg/yii2-users) module (required)

# Installation
To install the module, run the following command in the console:

`$ composer require "wdmg/yii2-profiles"`

After configure db connection, run the following command in the console:

`$ php yii profiles/init`

And select the operation you want to perform:
  1) Apply all module migrations
  2) Revert all module migrations

# Migrations
In any case, you can execute the migration and create the initial data, run the following command in the console:

`$ php yii migrate --migrationPath=@vendor/wdmg/yii2-profiles/migrations`

# Configure
To add a module to the project, add the following data in your configuration file:

    'modules' => [
        ...
        'profiles' => [
            'class' => 'wdmg\profiles\Module',
            'routePrefix' => 'admin',
            'supportLocales' => [
                'ru-RU',
                'uk-UA',
                'en-US'
            ],
            'reservedFields' => [
                'admin',
                'administrator',
                'root',
                'superuser',
                'supervisor',
                'timezone'
            ]
        ],
        ...
    ],

# Routing
Use the `Module::dashboardNavItems()` method of the module to generate a navigation items list for NavBar, like this:

    <?php
        echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
            'label' => 'Modules',
            'items' => [
                Yii::$app->getModule('profiles')->dashboardNavItems(),
                ...
            ]
        ]);
    ?>

# Status and version [in progress development]
* v.1.1.0 - Update copyrights, fix menu dashboard
* v.1.0.0 - Added CRUD for profiles and custom fields
* v.0.0.1 - Module, models, component, translations and migrations