<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

namespace humhub\modules\humdav\components\sabre;

use humhub\modules\humdav\definitions\VCalDefinitions;
use humhub\modules\space\models\Membership;
use Yii;
use Sabre\Uri;
use Sabre\CalDAV\Backend\AbstractBackend;
use humhub\modules\user\models\User;

class CalDavBackend extends AbstractBackend {
    /**
     * @inheritdoc
     */
    public function getCalendarsForUser($principalUri) {
        if (Yii::$app->getModule('calendar') === null) return [];

        list($prefix, $guid) = Uri\split($principalUri);
        if ($prefix !== 'principals') return [];

        $user = User::findByGuid($guid);
        if ($user === null) return [];

        $results = [];

        if ($user->moduleManager->isEnabled('calendar')) {
            $results[] = [
                'id' => '0',
                'uri' => 'personal',
                'principaluri' => $principalUri,
                '{DAV:}displayname' => 'Profile calendar',
                '{http://sabredav.org/ns}read-only' => 1
            ];
        }

        foreach (Membership::getUserSpaces($user->id) as $space) {
            if (!$space->moduleManager->isEnabled('calendar')) {
                continue;
            }

            $results[] = [
                'id' => 'space_'.$space->id,
                'uri' => 'space_'.$space->url,
                'principaluri' => $principalUri,
                '{DAV:}displayname' => $space->getDisplayName(),
                '{http://sabredav.org/ns}read-only' => 1
            ];
        }

        return $results;
    }

    /**
     * @inheritdoc
     */
    public function createCalendar($principalUri, $calendarUri, array $properties) {
        throw new \Sabre\DAV\Exception\BadRequest('Not implemented yet!');
    }

    /**
     * @inheritdoc
     */
    public function updateCalendar($calendarId, \Sabre\DAV\PropPatch $propPatch) {
        throw new \Sabre\DAV\Exception\BadRequest('Not implemented yet!');
    }

    /**
     * @inheritdoc
     */
    public function deleteCalendar($calendarId) {
        throw new \Sabre\DAV\Exception\BadRequest('Not implemented yet!');
    }

    /**
     * @inheritdoc
     */
    public function getCalendarObjects($calendarId) {
        if (Yii::$app->getModule('calendar') === null) return [];

        $username = Yii::$app->request->getAuthUser();
        $currentUser = User::findOne(['username' => $username]);
        if ($currentUser === null) {
            return [];
        }
        
        $calendarEntries = VCalDefinitions::getCalendarObjects($calendarId, $currentUser);
        if ($calendarEntries === null) return [];
        
        $results = [];
        foreach ($calendarEntries as $calendarEntry) {
            $results[] = VCalDefinitions::getVCalDefinition($calendarEntry, $calendarId);
        }

        return $results;
    }

    /**
     * @inheritdoc
     */
    public function getCalendarObject($calendarId, $objectUri) {
        $objects = $this->getCalendarObjects($calendarId);
        foreach ($objects as $object) {
            if ($object['uri'] === $objectUri) {
                return $object;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function createCalendarObject($calendarId, $objectUri, $calendarData) {
        throw new \Sabre\DAV\Exception\BadRequest('Not implemented yet!');
    }

    /**
     * @inheritdoc
     */
    public function updateCalendarObject($calendarId, $objectUri, $calendarData) {
        throw new \Sabre\DAV\Exception\BadRequest('Not implemented yet!');
    }

    /**
     * @inheritdoc
     */
    public function deleteCalendarObject($calendarId, $objectUri) {
        throw new \Sabre\DAV\Exception\BadRequest('Not implemented yet!');
    }
}