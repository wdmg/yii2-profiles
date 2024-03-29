<?php

namespace wdmg\profiles;

/**
 * Yii2 User profiles
 *
 * @category        Module
 * @version         1.1.0
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-profiles
 * @copyright       Copyright (c) 2021 - 2023 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use wdmg\helpers\ArrayHelper;
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
     * @var array, the list of support locales for multi-language versions of profiles data and custom fields.
     * @note This variable will be override if you use the `wdmg\yii2-translations` module.
     */
    public $supportLocales = ['ru-RU', 'uk-UA', 'en-US'];

    /**
     * List of reserved stop words for fields validation
     *
     * @var array
     * @note This variable will be extended by public properties of models, like `id`, `name`, `created_at` etc.
     * Therefore, there is no need to list them here.
     */
    public $reservedFields = [
        'admin',
        'administrator',
        'root',
        'superuser',
        'supervisor',
        'timezone',
    ];

    /**
     * @var string the module version
     */
    private $version = "1.1.0";

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
    public function dashboardNavItems($options = null)
    {
        $items = [
            'label' => $this->name,
            'url' => '#',
            'icon' => 'fa fa-fw fa-id-card',
            'active' => in_array(\Yii::$app->controller->module->id, [$this->id]),
            'items' => [
                [
                    'label' => Yii::t('app/modules/profiles', 'Profiles list'),
                    'url' => [$this->routePrefix . '/profiles/profiles/'],
                    'active' => (in_array(\Yii::$app->controller->module->id, ['profiles']) &&  Yii::$app->controller->id == 'profiles'),
                ], [
                    'label' => Yii::t('app/modules/profiles', 'Custom fields'),
                    'url' => [$this->routePrefix . '/profiles/fields/'],
                    'active' => (in_array(\Yii::$app->controller->module->id, ['profiles']) &&  Yii::$app->controller->id == 'fields'),
                ]
            ]
        ];

	    if (!is_null($options)) {

		    if (isset($options['count'])) {
			    $items['label'] .= '<span class="badge badge-default float-right">' . $options['count'] . '</span>';
			    unset($options['count']);
		    }

		    if (is_array($options))
			    $items = ArrayHelper::merge($items, $options);

	    }

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