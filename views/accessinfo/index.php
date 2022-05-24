<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

use humhub\libs\ActionColumn;
use humhub\libs\Html;
use humhub\modules\humdav\assets\ICalAssetBundle;
use humhub\modules\humdav\models\UserToken;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\Button;
use humhub\widgets\GridView;
use yii\helpers\Url;

\humhub\assets\JqueryKnobAsset::register($this);
if ($calendarActivated) ICalAssetBundle::register($this);
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading"><i class="fa far fa-address-card"></i> <span><strong>HumDAV</strong> Access Infos</span></div>
                <div class="panel-body">


                    <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
                        <li class="active">
                            <a href="#login-options" data-toggle="tab">Login Options</a>
                        </li>
                        <li>
                            <a href="#auto-config-files" data-toggle="tab">Automatic Configuration Files</a>
                        </li>
                        <li>
                            <a href="#settings" data-toggle="tab">Settings</a>
                        </li>
                        <li>
                            <a href="#troubleshooting" data-toggle="tab">Troubleshooting</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="login-options">
                            <div class="">
                                <div class="row">
                                    <div class="col-md-7">
                                        <h4>Default login options:</h4>
                                        <p>
                                            You can enter the following configuration details into your device (<a href="https://wiki.davical.org/index.php/CardDAV_Clients" target="_blank" rel="noopener noreferrer">List with some CardDAV clients</a>):
                                            <dl>
                                                <dt>Type:</dt>
                                                <dd><?=$calendarActivated ? 'CardDAV, CalDAV and iCal' : 'CardDAV and iCal'?></dd>

                                                <hr>

                                                <dt>CardDAV Url:</dt>
                                                <dd><?=Url::to(['/humdav/remote/addressbooks/'.Yii::$app->user->identity->guid.'/'], true)?></dd>

                                                <?php if ($calendarActivated) { ?>
                                                    <dt>CalDAV Url:</dt>
                                                    <dd><?=Url::to(['/humdav/remote/calendars/'.Yii::$app->user->identity->guid.'/'], true)?></dd>
                                                <?php } ?>

                                                <dt>Principal URL (=<?=$calendarActivated ? 'CardDAV/CalDAV' : 'CardDAV'?> Url for iOS and macOS):</dt>
                                                <dd><?=Url::to(['/humdav/remote/principals/'.Yii::$app->user->identity->guid.'/'], true)?></dd>

                                                <dt>Username:</dt>
                                                <dd><?=Yii::$app->user->identity->username?></dd>

                                                <dt>Password:</dt>
                                                <dd><i><?=$enablePasswordAuth ? 'Your HumHub password or a valid token' : 'A valid token'?></i></dd>

                                                <dt>Auth Type:</dt>
                                                <dd>Basic</dd>

                                                <?php if ($calendarActivated) { ?>
                                                    <hr>

                                                    <dt>iCal Address for <?=Html::dropDownList('Addressbook', null, $iCalCalendars, ['id' => 'ical_addressbook_selector'])?> <?=Html::button('Get URL', ['onclick' => 'updateAddressbookAddress()'])?>:</dt>
                                                    <dd id='ical_addressbook_address' data-default-url="<?=Url::to(['/humdav/remote/ical/'.Yii::$app->user->identity->guid.'/[ADDRESSBOOK-ID]/[ICAL-TOKEN]'], true)?>"></dd>
                                                <?php } ?>
                                            </dl>
                                        </p>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="pull-right">
                                            <?= Button::success('Add new Token')->icon('add')->sm()->link(['generate-token']) ?>
                                        </div>
                                        <h4>Login tokens:</h4>
                                        <?= GridView::widget([
                                            'dataProvider' => $dataProvider,
                                            'filterModel' => $searchModel,
                                            'tableOptions' => ['class' => 'table table-hover'],
                                            'columns' => [
                                                ['attribute' => 'name'],
                                                [
                                                    'attribute' => 'used_for',
                                                    'value' => function (UserToken $userToken) {
                                                        switch ($userToken->used_for) {
                                                            case UserToken::USED_FOR_DAV: return 'WebDAV';
                                                            case UserToken::USED_FOR_ICAL: return 'iCal';
                                                            
                                                            default: return 'Nothing';
                                                        }
                                                    }
                                                ],
                                                [
                                                    'attribute' => 'last_time_used',
                                                    'value' => function (UserToken $userToken) {
                                                        if (empty($userToken->last_time_used))
                                                            return 'Never';
                                                        else
                                                            return $userToken->last_time_used;
                                                    }
                                                ],
                                                [
                                                    'class' => ActionColumn::class,
                                                    'actions' => function (UserToken $userToken, $key, $index) {
                                                        if (!$userToken->canEdit()) return [];
                                                    
                                                        return [
                                                            'More Information' => ['token-info'],
                                                            '---',
                                                            'Revoke Access' => ['revoke-token'],
                                                        ];
                                                    }
                                                ],
                                            ],
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="auto-config-files">
                            <p>
                                If you visit this page with iOS or macOS, you can download an automatically generated configuration file here:
                                <br>
                                <a class="btn btn-default" href="<?= Url::to(['/humdav/accessinfo/mobileconfig', 'target' => 'ios']); ?>" target="_blank">Download iOS Configuration File</a>
                                <a class="btn btn-default" href="<?= Url::to(['/humdav/accessinfo/mobileconfig', 'target' => 'osx']); ?>" target="_blank">Download macOS Configuration File</a>
                            </p>
                        </div>
                        <div class="tab-pane" id="settings">
                            <?php $form = ActiveForm::begin(['enableClientValidation' => true]); ?>

                            <?= $form->field($userSettingsModel, 'provide_groups_as_separate_addressbooks')->checkbox(); ?>

                            <?= Html::submitButton("Save", ['class' => 'btn btn-primary', 'data-ui-loader' => '']); ?>
                            
                            <?php ActiveForm::end(); ?>
                        </div>
                        <div class="tab-pane" id="troubleshooting">
                            <p>
                                If authentication is not possible, this may have the following reasons:
                                <ul>
                                    <li>The HumDAV module has not been activated yet.</li>
                                    <li>The Calendar module has not been activated yet.</li>
                                    <li>You have not been authorized for access.</li>
                                    <li>You have a typo somewhere.</li>
                                    <li>Also check the upper and lower case of your username.</li>
                                    <li>You are using a URL from an old version of this module.</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
