<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $model wdmg\profiles\models\Profiles */

$this->title = Yii::t('app/modules/profiles', 'User profile: {user}', [
    'user' => $model->user->username,
]);
$this->params['breadcrumbs'][] = ['label' => $this->context->module->name, 'url' => ['profiles/index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$this->registerJs(<<< JS

    /* To initialize BS3 tooltips set this below */
    $(function () {
        $("[data-toggle='tooltip']").tooltip(); 
    });
    
    /* To initialize BS3 popovers set this below */
    /*$(function () {
        $("[data-toggle='popover']").popover(); 
    });*/

JS
);

?>
<style>
    table .custom-field {
        background-color: rgba(55, 20, 246, 0.06);
    }
</style>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="profiles-view">

    <?php
        $columns = [
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
        if ($fields = $model->getFields($model->locale, false)) {
            foreach ($fields as $field) {
                if (isset($field->name) && isset($field->label)) {
                    $attribute = $field->name;
                    $columns[] = [
                        'attribute' => $attribute,
                        'label' => $model->getAttributeLabel($attribute),
                        'captionOptions' => ['class' => 'custom-field'],
                        'contentOptions' => ['class' => 'custom-field'],
                    ];
                }
            }
        }

        $columns = \wdmg\helpers\ArrayHelper::merge($columns, [
            [
                'attribute' => 'locale',
                'label' => Yii::t('app/modules/profiles', 'Language'),
            ],
            [
                'attribute' => 'time_zone',
                'format' => 'html',
                'value' => function($data) {
                    $formatter = Yii::$app->getFormatter();
                    $formatter->defaultTimeZone = $data->time_zone;
                    return $data->time_zone . ' ' .
                        Html::tag('div', Yii::t('app/modules/profiles', 'User date/time:') . " " .
                            Html::tag('code', $formatter->asDateTime(date('Y-m-d H:i:s'))),
                            [
                                'class' => "pull-right"
                            ]
                        );
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
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
        ]);

        echo DetailView::widget([
            'model' => $model,
            'attributes' => $columns,
        ]);
    ?>
    <hr/>
    <div class="form-group">
        <?= Html::a(Yii::t('app/modules/profiles', '&larr; Back to list'), ['profiles/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
        <?= Html::a(Yii::t('app/modules/profiles', 'Edit'), ['profiles/update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app/modules/profiles', 'Delete'), ['profiles/delete', 'id' => $model->id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('app/modules/profiles', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>

</div>
