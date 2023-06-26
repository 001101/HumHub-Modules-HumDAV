<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

namespace humhub\modules\humdav\models\sabre;

class CalendarObject extends \Sabre\CalDAV\CalendarObject {
    /**
     * @inheritdoc
     */
    function getACL() {
        // An alternative acl may be specified through the objectData array.
        if (isset($this->objectData['acl'])) {
            return $this->objectData['acl'];
        }

        return [
            [
                'privilege' => '{DAV:}read',
                'principal' => $this->calendarInfo['principaluri'],
                // 'principal' => $this->calendarInfo['principaluri'].'/calendar-proxy-read',
                'protected' => true,
            ],
        ];
    }
}