<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model wdmg\profiles\models\Profiles */

$this->title = Yii::t('app/modules/profiles', 'Add new profile');
$this->params['breadcrumbs'][] = ['label' => $this->context->module->name, 'url' => ['profiles/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="profiles-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
