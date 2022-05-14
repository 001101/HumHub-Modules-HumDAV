<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

namespace humhub\modules\humdav;

use humhub\modules\humdav\controllers\AdminController;
use Yii;
use yii\helpers\Url;
use humhub\modules\humdav\definitions\RouteDefinitions;

class Events {
    public static function onBeforeModuleDisable($event) {
        if ($event->moduleId === 'humdav') {
            AdminController::restoreHtaccess();
        }
    }

    public static function onBeforeRequest($event) {
        if (substr(Yii::$app->request->pathInfo, 0, 7) != 'humdav/') {
            return;
        }

        Yii::$app->urlManager->addRules(RouteDefinitions::getDefinitions(), true);
    }

    public static function onTopMenuInit($event) {
        try {
            $settings = Yii::$app->getModule('humdav')->settings;
            if ((boolean)$settings->get('active', false) !== true) {
                return;
            }
            if ($settings->get('instruction_location') !== 'top_menu') {
                return;
            }

            $currentIdentity = Yii::$app->user->identity;
            if ($currentIdentity === null || Yii::$app->user->isGuest) {
                return;
            }
            $allowedUsers = array_filter((array)$settings->getSerialized('enabled_users'));
            if (!in_array($currentIdentity->guid, $allowedUsers) && !empty($allowedUsers)) {
                return;
            }
            
            $event->sender->addItem([
                'label' => 'HumDAV',
                'url' => Url::to(['/humdav/accessinfo/index']),
                'htmlOptions' => [],
                'icon' => '<i class="fa far fa-address-card"></i>',
                'isActive' => (Yii::$app->controller->module
                    && Yii::$app->controller->module->id === 'humdav'
                    && Yii::$app->controller->id === 'accessinfo'),
                'sortOrder' => $settings->get('instruction_location_sort_order', 400),
            ]);
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onAfterUpdate($event) {
        if ($event->moduleId === 'humdav'){
            try {
                $settings = Yii::$app->getModule('humdav')->settings;
                if ($settings->get('instruction_location') === 'directory_menu' && version_compare(Yii::$app->version, '1.9.0', 'ge')) {
                    $settings->set('instruction_location', 'top_menu');
                }
            } catch (\Throwable $e) {
                Yii::error($e);
            }
        }
    }
}
