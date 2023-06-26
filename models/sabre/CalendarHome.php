<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

namespace humhub\modules\humdav\models\sabre;

class CalendarHome extends \Sabre\CalDAV\CalendarHome {
    /**
     * @inheritdoc
     */
    function getACL() {
        return [
            [
                'privilege' => '{DAV:}read',
                'principal' => $this->principalInfo['uri'],
                'protected' => true
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    function getChildren() {
        $calendars = $this->caldavBackend->getCalendarsForUser($this->principalInfo['uri']);
        $objs = [];
        foreach ($calendars as $calendar) {
            $objs[] = new Calendar($this->caldavBackend, $calendar);
        }
        return $objs;
    }
}