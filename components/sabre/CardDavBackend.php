<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

namespace humhub\modules\humdav\components\sabre;

use Yii;
use Sabre\DAV\PropPatch;
use Sabre\CardDAV\Backend\AbstractBackend;
use humhub\modules\humdav\definitions\VCardDefinitions;
use humhub\modules\user\models\User;
use humhub\modules\user\models\Follow;
use humhub\modules\space\models\Space;
use humhub\modules\space\models\Membership;

class CardDavBackend extends AbstractBackend {
    /**
     * @inheritdoc
     */
    public function getAddressBooksForUser($principalUri) {
        list($username) = Yii::$app->request->getAuthCredentials();
        $currentUser = User::findOne(['username' => $username]);
        if ($currentUser === null) {
            return [];
        }

        $results = [
            [
                'id' => '0',
                'uri' => 'allusers',
                'principaluri' => $principalUri,
                '{DAV:}displayname' => 'All Users',
                '{urn:ietf:params:xml:ns:carddav}addressbook-description' => 'All Users from "'.Yii::$app->settings->get('name').'"'
            ],
            [
                'id' => '1',
                'uri' => 'following',
                'principaluri' => $principalUri,
                '{DAV:}displayname' => 'Following',
                '{urn:ietf:params:xml:ns:carddav}addressbook-description' => 'All users you are following'
            ]
        ];

        foreach (Membership::getUserSpaces($currentUser->id) as $space) {
            $results[] = [
                'id' => 'space_'.$space->id,
                'uri' => 'space_'.$space->url,
                'principaluri' => $principalUri,
                '{DAV:}displayname' => $space->getDisplayName(),
                '{urn:ietf:params:xml:ns:carddav}addressbook-description' => $space->getDisplayNameSub()
            ];
        }
        
        return $results;
    }

    /**
     * @inheritdoc
     */
    public function updateAddressBook($addressBookId, PropPatch $propPatch) {
        throw new \Sabre\DAV\Exception\BadRequest('Not implemented yet!');
    }

    /**
     * @inheritdoc
     */
    public function createAddressBook($principalUri, $url, array $properties) {
        throw new \Sabre\DAV\Exception\BadRequest('Not implemented yet!');
    }

    /**
     * @inheritdoc
     */
    public function deleteAddressBook($addressBookId) {
        throw new \Sabre\DAV\Exception\BadRequest('Not implemented yet!');
    }

    /**
     * @inheritdoc
     */
    public function getCards($addressBookId) {
        $results = [];
        $usersForCards = [];

        list($username) = Yii::$app->request->getAuthCredentials();
        $currentUser = User::findOne(['username' => $username]);
        if ($currentUser === null) {
            return [];
        }

        if ($addressBookId === '0') {
            $usersForCards = $this->getAllUsers();
        }
        else if ($addressBookId === '1') {
            $usersForCards = $this->getFollowingUsers($currentUser);
        }
        else if (str_starts_with($addressBookId, 'space_')) {
            $usersForCards = $this->getSpaceUsers(substr($addressBookId, strpos($addressBookId, '_') + 1), $currentUser);
        }

        foreach ($usersForCards as $user) {
            $results[] = VCardDefinitions::getVCardDefinition($user, $addressBookId);
        }

        return $results;
    }

    /**
     * @inheritdoc
     */
    public function getCard($addressBookId, $cardUri) {
        $cards = $this->getCards($addressBookId);
        foreach ($cards as $card) {
            if ($card['uri'] === $cardUri) {
                return $card;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function createCard($addressBookId, $cardUri, $cardData) {
        throw new \Sabre\DAV\Exception\BadRequest('Not implemented yet!');
    }

    /**
     * @inheritdoc
     */
    public function updateCard($addressBookId, $cardUri, $cardData) {
        throw new \Sabre\DAV\Exception\BadRequest('Not implemented yet!');
    }

    /**
     * @inheritdoc
     */
    public function deleteCard($addressBookId, $cardUri) {
        throw new \Sabre\DAV\Exception\BadRequest('Not implemented yet!');
    }

    // ==================================================================================================================

    private function getAllUsers() {
        return User::findAll(['user.status' => User::STATUS_ENABLED]);
    }

    private function getFollowingUsers(User $currentUser) {
        return Follow::getFollowedUserQuery($currentUser)->active()->visible()->all();
    }

    private function getSpaceUsers(string $spaceId, User $currentUser) {
        if (!in_array($spaceId, Membership::getUserSpaceIds($currentUser->id))) {
            return [];
        }
        
        $space = Space::findOne(['id' => $spaceId]);
        if ($space === null) {
            return [];
        }

        return Membership::getSpaceMembersQuery($space)->active()->visible()->all();
    }
}