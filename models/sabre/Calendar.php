<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

namespace humhub\modules\humdav\models\sabre;

class Calendar extends \Sabre\CalDAV\Calendar {
    /**
     * @inheritdoc
     */
    function getACL() {
        return [
            [
                'privilege' => '{DAV:}read',
                'principal' => $this->getOwner(),
                'protected' => true
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    function getChildACL() {
        return [
            [
                'privilege' => '{DAV:}read',
                'principal' => $this->getOwner(),
                'protected' => true,
            ],
        ];
    }

    function getChild($name) {
        $obj = $this->caldavBackend->getCalendarObject($this->calendarInfo['id'], $name);
        if (!$obj) throw new \Sabre\DAV\Exception\NotFound('Calendar object not found');
        return new CalendarObject($this->caldavBackend, $this->calendarInfo, $obj);
    }

    function getChildren() {
        $objs = $this->caldavBackend->getCalendarObjects($this->calendarInfo['id']);
        $children = [];
        foreach ($objs as $obj) {
            $obj['acl'] = $this->getChildACL();
            $children[] = new CalendarObject($this->caldavBackend, $this->calendarInfo, $obj);
        }
        return $children;
    }

    function getMultipleChildren(array $paths) {
        $objs = $this->caldavBackend->getMultipleCalendarObjects($this->calendarInfo['id'], $paths);
        $children = [];
        foreach ($objs as $obj) {
            $obj['acl'] = $this->getChildACL();
            $children[] = new CalendarObject($this->caldavBackend, $this->calendarInfo, $obj);
        }
        return $children;
    }
}