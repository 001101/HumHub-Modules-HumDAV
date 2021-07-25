<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

use humhub\components\Application;
use humhub\components\ModuleManager;
use humhub\modules\directory\widgets\Menu as DirectoryMenu;
use humhub\widgets\TopMenu;

return [
    'id' => 'humdav',
    'class' => 'humhub\modules\humdav\Module',
    'namespace' => 'humhub\modules\humdav',
    'events' => [
        [ModuleManager::class, ModuleManager::EVENT_BEFORE_MODULE_DISABLE, ['\humhub\modules\humdav\Events', 'onBeforeModuleDisable']],
        [Application::class, Application::EVENT_BEFORE_REQUEST, ['\humhub\modules\humdav\Events', 'onBeforeRequest']],
        [TopMenu::class, TopMenu::EVENT_INIT, ['\humhub\modules\humdav\Events', 'onTopMenuInit']],
        [DirectoryMenu::class, DirectoryMenu::EVENT_INIT, ['\humhub\modules\humdav\Events', 'onDirectoryMenuInit']]
    ]
];
