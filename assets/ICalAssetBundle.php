<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

namespace humhub\modules\humdav\assets;

use yii\web\AssetBundle;

class ICalAssetBundle extends AssetBundle {
    /**
     * v1.5 compatibility defer script loading
     *
     * Migrate to HumHub AssetBundle once minVersion is >=1.5
     *
     * @var bool
     */
    public $defer = true;

    public $sourcePath = '@humdav/resources';
    
    public $js = [
        'ical/address_generator.js',
    ];
}
