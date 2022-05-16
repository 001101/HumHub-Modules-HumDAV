<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

use humhub\modules\ui\form\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

\humhub\assets\JqueryKnobAsset::register($this);

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading"><i class="fa far fa-address-card"></i> <span><strong>HumDAV</strong> Generate Token</span></div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['enableClientValidation' => true]); ?>

                    <?= $form->field($userToken, 'name') ?>

                    <div class="well">
                        <p>Your token: <b><?= $token ?></b></p>
                        <hr>
                        <p><i class="fa fa-exclamation-triangle" style="color: <?= $this->theme->variable('danger')?>"></i> &nbsp;You cannot view or edit this token later.</p>
                    </div>
                    <br />
                    <?= Html::submitButton("Save, activate token and back", ['class' => 'btn btn-primary', 'data-ui-loader' => '']) ?>
                    <a class="btn btn-default" href="<?= Url::to(['index']); ?>">Back</a>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
