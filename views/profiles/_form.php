<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Alert;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $model wdmg\profiles\models\Profiles */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="profiles-form">
    <?php $form = ActiveForm::begin(); ?>
    <?php
        // Build custom fields form inputs
        if ($fields = $model->getFields()) {
            foreach ($fields as $field) {
                if (isset($field->name) && isset($field->label)) {
                    $attribute = $field->name;
                    $label = $field->label;
                    if ($model->hasAttribute($attribute)) {
                        echo $form->field($model, $attribute, ['options' => ['class' => 'form-group custom-field']])->textInput();
                    }
                }
            }
        } else {
            Alert::begin([
                'options' => [
                    'class' => 'alert-warning',
                ]
            ]);
            echo Yii::t(
                'app/modules/profiles',
                'Not found any custom field available for editing. You can create {link}.',
                [
                    'link' => Html::a(Yii::t('app/modules/profiles', 'it here'), ['fields/create'])
                ]
            );
            Alert::end();
        }
    ?>
    <?= $form->field($model, 'user_id')->widget(SelectInput::class, [
        'items' => $model->getUsersList(),
        'options' => [
            'class' => 'form-control'
        ]
    ]); ?>
    <?= $form->field($model, 'locale')->widget(SelectInput::class, [
        'items' => $model->getLanguagesList(false),
        'options' => [
            'class' => 'form-control'
        ]
    ]); ?>
    <?= $form->field($model, 'time_zone')->widget(SelectInput::class, [
        'items' => $model->getTimezonesList(),
        'options' => [
            'class' => 'form-control'
        ]
    ]); ?>
    <?= $form->field($model, 'status')->widget(SelectInput::class, [
        'items' => $model->getStatusesList(),
        'options' => [
            'class' => 'form-control'
        ]
    ]); ?>
    <hr/>
    <div class="form-group">
        <?= Html::a(Yii::t('app/modules/profiles', '&larr; Back to list'), ['profiles/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
        <?= Html::submitButton(Yii::t('app/modules/profiles', 'Save'), ['class' => 'btn btn-success pull-right']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
