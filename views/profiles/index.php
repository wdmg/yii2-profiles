<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use wdmg\widgets\SelectInput;
use wdmg\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel wdmg\likes\models\LikesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/profiles', 'User profiles');
$this->params['breadcrumbs'][] = $this->context->module->name;

$bundle = false;
if (isset(Yii::$app->translations) && class_exists('\wdmg\translations\FlagsAsset')) {
    $bundle = \wdmg\translations\FlagsAsset::register(Yii::$app->view);
}

?>
<style>
    table .custom-field {
        background-color: rgba(55, 20, 246, 0.06);
    }
</style>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="profiles-index">
    <?php Pjax::begin(); ?>
    <?php
        $columns = [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'user_id',
                'label' => Yii::t('app/modules/profiles','User'),
                'format' => 'html',
                'value' => function($data) {
                    $output = "";
                    if ($user = $data->user) {
                        $output = Html::a($user->username, ['../admin/users/view/?id='.$user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->user_id) {
                        $output = $data->user_id;
                    }

                    return $output;
                }
            ],
        ];

        // Add custom fields to columns list
        if ($fields = $searchModel->getFields(null, false)) {
            foreach ($fields as $field) {
                if (isset($field->name) && isset($field->label)) {
                    $attribute = $field->name;
                    $label = $field->label;
                    $columns[] = [
                        'attribute' => $attribute,
                        'label' => $label,
                        'headerOptions' => ['class' => 'custom-field'],
                        'filterOptions' => ['class' => 'custom-field'],
                        'contentOptions' => ['class' => 'custom-field'],
                        'footerOptions' => ['class' => 'custom-field'],
                        'format' => 'html',
                        'filter' => true
                    ];
                }
            }
        }

        $columns = \wdmg\helpers\ArrayHelper::merge($columns, [

            [
                'attribute' => 'locale',
                'label' => Yii::t('app/modules/profiles','Language'),
                'format' => 'raw',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'locale',
                    'items' => $searchModel->getLanguagesList(true),
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center',
                    'style' => 'min-width:96px;'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) use ($bundle) {
                    $output = [];
                    $separator = ", ";
                    if (isset(Yii::$app->translations)) {
                        $locale = Yii::$app->translations->parseLocale($data->locale, Yii::$app->language);
                        if ($data->locale === $locale['locale']) { // Fixing default locale from PECL intl

                            if (!($country = $locale['domain']))
                                $country = '_unknown';

                            $output[] = \yii\helpers\Html::img($bundle->baseUrl . '/flags-iso/flat/24/' . $country . '.png', [
                                'alt' => $locale['name'],
                                'title' => $locale['name'],
                                'data-toggle' => 'tooltip'
                            ]);
                        }
                        $separator = "";
                    } else {
                        if (!empty($data->locale)) {

                            if (extension_loaded('intl'))
                                $output[] = mb_convert_case(trim(\Locale::getDisplayLanguage($data->locale, Yii::$app->language)), MB_CASE_TITLE, "UTF-8");
                            else
                                $output[] = $data->locale;
                        } else {
                            return $data->locale;
                        }
                    }


                    if (is_array($output)) {
                        if (count($output) > 0) {
                            $onMore = false;
                            if (count($output) > 3)
                                $onMore = true;

                            if ($onMore)
                                return join(array_slice($output, 0, 3), $separator) . "&nbsp;…";
                            else
                                return join($separator, $output);

                        }
                    }

                    return null;
                }
            ],
            [
                'attribute' => 'time_zone',
                'format' => 'html',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'time_zone',
                    'items' => $searchModel->getTimezonesList(true),
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'status',
                    'items' => $searchModel->getStatusesList(true),
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) {
                    if ($data->status == $data::STATUS_PUBLISHED) {
                        return '<span class="label label-success">' . Yii::t('app/modules/profiles', 'Published') . '</span>';
                    } elseif ($data->status == $data::STATUS_DRAFT) {
                        return '<span class="label label-default">' . Yii::t('app/modules/profiles', 'Draft') . '</span>';
                    } elseif ($data->status == $data::STATUS_AWAITING) {
                        return '<span class="label label-warning">' . Yii::t('app/modules/profiles', 'Awaiting') . '</span>';
                    } elseif ($data->status == $data::STATUS_SUSPENDED) {
                        return '<span class="label label-danger">' . Yii::t('app/modules/profiles', 'Suspended') . '</span>';
                    } else {
                        return $data->status;
                    }
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/modules/profiles','Actions'),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
        ]);
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => $columns,
        'pager' => [
            'options' => [
                'class' => 'pagination',
            ],
            'maxButtonCount' => 5,
            'activePageCssClass' => 'active',
            'prevPageCssClass' => 'prev',
            'nextPageCssClass' => 'next',
            'firstPageCssClass' => 'first',
            'lastPageCssClass' => 'last',
            'firstPageLabel' => Yii::t('app/modules/profiles', 'First page'),
            'lastPageLabel'  => Yii::t('app/modules/profiles', 'Last page'),
            'prevPageLabel'  => Yii::t('app/modules/profiles', '&larr; Prev page'),
            'nextPageLabel'  => Yii::t('app/modules/profiles', 'Next page &rarr;')
        ],
    ]); ?>
    <hr/>
    <div>
        <?= Html::a(Yii::t('app/modules/profiles', 'Add new profile'), ['profiles/create'], ['class' => 'btn btn-add btn-success pull-right']) ?>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>
