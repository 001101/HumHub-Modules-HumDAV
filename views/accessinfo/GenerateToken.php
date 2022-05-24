<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

use humhub\modules\humdav\models\UserToken;
use humhub\modules\ui\form\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

if (class_exists('humhub\assets\ClipboardJsAsset')) {
    humhub\assets\ClipboardJsAsset::register($this);
}
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
                    <?= $form->field($userToken, 'used_for')->dropDownList([
                        UserToken::USED_FOR_DAV => 'Used for WebDAV access',
                        UserToken::USED_FOR_ICAL => 'Used for iCal access'
                    ]) ?>

                    <div class="well">
                        <p>Your token: <b><?= $token ?></b></p>
                        <?php if (class_exists('humhub\assets\ClipboardJsAsset')) { ?>
                            <a href="#" onClick="clipboard.writeText('<?= $token ?>')"><i class="fa fa-clipboard" aria-hidden="true"></i> Copy to clipboard</a>
                        <?php } ?>
                        <hr>
                        <p><i class="fa fa-exclamation-triangle" style="color: <?= $this->theme->variable('danger')?>"></i> &nbsp;You cannot view or edit this token later.</p>
                        <p><i class="fa fa-info-circle" style="color: <?= $this->theme->variable('info')?>"></i> &nbsp;Please use this token for one device or application only.</p>
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
