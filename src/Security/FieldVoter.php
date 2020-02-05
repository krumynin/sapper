<?php


namespace App\Security;

use App\Entity\Field;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class FieldVoter extends Voter
{
    // these strings are just invented: you can use anything
    const EDIT = 'edit';
    const EDIT_SETTINGS = 'edit_settings';

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [
            self::EDIT,
            self::EDIT_SETTINGS,
        ])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Field) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var Field $field */
        $field = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($field, $user);
            case self::EDIT_SETTINGS:
                return $this->canEditSettings($field, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param Field $field
     * @param User  $user
     *
     * @return bool
     */
    private function canEdit(Field $field, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $field->getUser();
    }

    /**
     * @param Field $field
     * @param User  $user
     *
     * @return bool
     */
    private function canEditSettings(Field $field, User $user)
    {
        $data = $field->getData();
        for ($j = 0; $j < count($data); $j++) {
            for ($i = 0; $i < count($data[$j]); $i++) {
                if ($data[$i][$j]['clicked']) {
                    return false;
                }
            }
        }

        return $user === $field->getUser();
    }
}