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
                <div class="panel-heading"><i class="fa far fa-address-card"></i> <span><strong>HumDAV</strong> Token Info</span></div>
                <div class="panel-body">
                    <?php
                    $form = ActiveForm::begin();
                    //<?= $form->field($model, 'active')->checkbox();

                    foreach (array_intersect($userToken->fields(), $viewFields) as $fieldName) {
                        echo $form->field($userToken, $fieldName)->textInput(['readonly' => !in_array($fieldName, ['name'])]);
                    }
                    echo Html::submitButton("Save and back", ['class' => 'btn btn-primary', 'data-ui-loader' => '']);
                    ?>
                    <a class="btn btn-default" href="<?= Url::to(['index']); ?>">Back</a>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
