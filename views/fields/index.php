<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel wdmg\likes\models\LikesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/profiles', 'Custom fields');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="profiles-fields-index">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'label',
            'name',
            'placeholder',
            'description',
            'type',
            'sort_order',
            'params',
            'is_required',
            'status',
            'locale',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ],
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
    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>
