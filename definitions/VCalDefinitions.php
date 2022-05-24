<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

namespace humhub\modules\humdav\definitions;

use humhub\modules\calendar\helpers\CalendarUtils;
use humhub\modules\calendar\interfaces\VCalendar;
use humhub\modules\calendar\models\CalendarEntry;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;

class VCalDefinitions {
    public static function getVCalDefinition(CalendarEntry $entry, $calendarId) {
        $vCal = strval(VCalendar::withEvents($entry, CalendarUtils::getSystemTimeZone(true))->serialize());
        return [
            'id' => $entry->id,
            'uri' => $entry->getUid().'.ics',
            'calendarid' => $calendarId,
            'calendardata' => $vCal,
            'size' => strlen($vCal),
            'etag' => '"'.md5($vCal).'"',
            'lastmodified' => $entry->getLastModified() !== null ? $entry->getLastModified()->getTimestamp() : (new \DateTime())->setTime(0, 0)->getTimestamp(),
            'component' => 'vevent'
        ];
    }
    public static function getICalFormat($items) {
        return strval(VCalendar::withEvents($items, CalendarUtils::getSystemTimeZone(true))->serialize());
    }

    // ==================================================================================================================

    /**
     * @return CalendarEntry[]|null
     */
    public static function getCalendarObjects(string $calendarId, User $currentUser) {
        $calendarEntries = [];

        if ($calendarId === '0') {
            if (!$currentUser->isModuleEnabled('calendar')) return null;
            $calendarEntries = CalendarEntry::find()->contentContainer($currentUser)->all();
        }
        else if (str_starts_with($calendarId, 'space_')) {
            $spaceId = substr($calendarId, strpos($calendarId, '_') + 1);
            $space = Space::findOne(['id' => $spaceId]);
            if ($space === null) return null;
            if (!$space->isModuleEnabled('calendar')) return null;
            if (!$space->isMember($currentUser->id)) return null;
            $calendarEntries = CalendarEntry::find()->contentContainer($space)->readable($currentUser)->all();
        }
        else {
            return null;
        }

        return $calendarEntries;
    }
}
