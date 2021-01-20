<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model wdmg\profiles\models\Profiles */

$this->title = $model->user;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/profiles', 'User profiles'), 'url' => ['profiles/index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="profiles-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',


        ],
    ]) ?>

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
