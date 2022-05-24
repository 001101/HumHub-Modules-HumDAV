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

\humhub\assets\JqueryKnobAsset::register($this);
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading"><i class="fa far fa-address-card"></i> <span><strong>HumDAV</strong> Token Info</span></div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($userToken, 'name')->textInput() ?>
                    
                    <dl>
                        <dt><?= $userToken->getAttributeLabel('used_for') ?>:</dt>
                        <dd>
                            <?php
                            switch ($userToken->used_for) {
                                case UserToken::USED_FOR_DAV:
                                    echo 'Used for WebDAV access';
                                    break;

                                case UserToken::USED_FOR_ICAL:
                                    echo 'Used for iCal access';
                                    break;
                                
                                default:
                                    echo '-';
                                    break;
                            }
                            ?>
                        </dd>

                        <dt><?= $userToken->getAttributeLabel('last_time_used') ?>:</dt>
                        <dd><?= $userToken->last_time_used ?? '-' ?></dd>

                        <dt><?= $userToken->getAttributeLabel('last_time_used_by_ip') ?>:</dt>
                        <dd><?= $userToken->last_time_used_by_ip ?? '-' ?></dd>

                        <dt><?= $userToken->getAttributeLabel('last_time_used_by_user_agent') ?>:</dt>
                        <dd><?= $userToken->last_time_used_by_user_agent ?? '-' ?></dd>

                        <dt><?= $userToken->getAttributeLabel('created_at') ?>:</dt>
                        <dd><?= $userToken->created_at ?></dd>

                        <dt><?= $userToken->getAttributeLabel('created_by_ip') ?>:</dt>
                        <dd><?= $userToken->created_by_ip ?></dd>

                        <dt><?= $userToken->getAttributeLabel('created_by_user_agent') ?>:</dt>
                        <dd><?= $userToken->created_by_user_agent ?></dd>
                    </dl>
                    
                    <?= Html::submitButton("Save and back", ['class' => 'btn btn-primary', 'data-ui-loader' => '']) ?>
                    <a class="btn btn-default" href="<?= Url::to(['index']); ?>">Back</a>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
