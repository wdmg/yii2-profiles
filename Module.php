<?php

namespace wdmg\profiles;

/**
 * Yii2 User profiles
 *
 * @category        Module
 * @version         0.0.1
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-profiles
 * @copyright       Copyright (c) 2021 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use wdmg\base\BaseModule;
use wdmg\profiles\components\Profiles;

/**
 * Profiles module definition class
 */
class Module extends BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'wdmg\profiles\controllers';

    /**
     * {@inheritdoc}
     */
    public $defaultRoute = "profiles/index";

    /**
     * @var string, the name of module
     */
    public $name = "Profiles";

    /**
     * @var string, the description of module
     */
    public $description = "System of managing user profiles";

    /**
     * @var string the module version
     */
    private $version = "0.0.1";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 8;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Set version of current module
        $this->setVersion($this->version);

        // Set priority of current module
        $this->setPriority($this->priority);
    }

    /**
     * {@inheritdoc}
     */
    public function dashboardNavItems($options = false)
    {
        $items = [
            'label' => $this->name,
            'url' => '#',
            'icon' => 'fa fa-fw fa-user',
            'active' => in_array(\Yii::$app->controller->module->id, [$this->id]),
            'items' => [
                [
                    'label' => Yii::t('app/modules/profiles', 'User profiles'),
                    'url' => [$this->routePrefix . '/profiles/profiles/'],
                    'active' => (in_array(\Yii::$app->controller->module->id, ['profiles']) &&  Yii::$app->controller->id == 'profiles'),
                ], [
                    'label' => Yii::t('app/modules/profiles', 'Custom fields'),
                    'url' => [$this->routePrefix . '/profiles/fields/'],
                    'active' => (in_array(\Yii::$app->controller->module->id, ['profiles']) &&  Yii::$app->controller->id == 'fields'),
                ]
            ]
        ];

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);

        // Configure user profiles component
        $app->setComponents([
            'profiles' => [
                'class' => Profiles::class
            ]
        ]);
    }
}