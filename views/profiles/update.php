<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model wdmg\profiles\models\Profiles */

$this->title = Yii::t('app/modules/profiles', 'Update user profile: {user}', [
    'user' => $model->user->username,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/profiles', 'User profiles'), 'url' => ['profiles/index']];
$this->params['breadcrumbs'][] = $model->user->username;
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="profiles-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>