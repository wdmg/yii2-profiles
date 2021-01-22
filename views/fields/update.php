<?php

use wdmg\helpers\StringHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\vendor\wdmg\forms\models\Fields */

$this->title = Yii::t('app/modules/profiles', 'Update field: {label}', [
    'label' => $model->label,
]);
$this->params['breadcrumbs'][] = ['label' => $this->context->module->name, 'url' => ['profiles/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/profiles', 'All fields'), 'url' => ['fields/index']];
$this->params['breadcrumbs'][] = ['label' => StringHelper::stringShorter($model->label, 64), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/modules/profiles', 'Update');
?>
<?php if (Yii::$app->authManager && $this->context->module->moduleExist('rbac') && Yii::$app->user->can('updatePosts', [
        'created_by' => $model->created_by,
        'updated_by' => $model->updated_by
    ])) : ?>
    <div class="page-header">
        <h1>
            <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
        </h1>
    </div>
    <div class="profiles-fields-update">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
<?php else: ?>
    <div class="page-header">
        <h1 class="text-danger"><?= Yii::t('app/modules/profiles', 'Error {code}. Access Denied', [
                'code' => 403
            ]) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
    </div>
    <div class="profiles-fields-update-error">
        <blockquote>
            <?= Yii::t('app/modules/profiles', 'You are not allowed to view this page.'); ?>
        </blockquote>
    </div>
<?php endif; ?>
<?php echo $this->render('../_debug'); ?>