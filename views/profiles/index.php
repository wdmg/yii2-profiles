<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel wdmg\likes\models\LikesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/profiles', 'User profiles');
$this->params['breadcrumbs'][] = $this->title;

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
            'user_id',
        ];

        // Add custom fields to columns list
        if ($fields = $searchModel->getFields()) {
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
            'locale',
            'time_zone',
            'status',
            'created_at',
            'updated_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/modules/profiles','Actions'),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                //'visibleButtons' => []
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
        <?= Html::a(Yii::t('app/modules/profiles', 'Add profile'), ['profiles/create'], ['class' => 'btn btn-success pull-right']) ?>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>
