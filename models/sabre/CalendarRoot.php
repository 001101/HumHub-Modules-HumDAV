<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

namespace humhub\modules\humdav\models\sabre;

use Sabre\DAVACL\ACLTrait;
use Sabre\DAVACL\IACL;

class CalendarRoot extends \Sabre\CalDAV\CalendarRoot implements IACL {
    
    use ACLTrait;

    /**
     * @inheritdoc
     */
    function getACL() {
        return [
            [
                'privilege' => '{DAV:}read',
                'principal' => '{DAV:}authenticated',
                'protected' => true
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    function getChildForPrincipal(array $principal) {
        return new CalendarHome($this->caldavBackend, $principal);
    }
}