<?php
namespace AppBundle\Handler;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;

class UserHandler extends HandlerBase
{
    use UserEncryptTrait;

    protected $formType = UserType::class;

    public function findOneByIdentity($identity, $type = null)
    {
        switch ($type) {
            case 1:
                $user = $this->getUserRepository()->findOneBy(['username' => $identity]);
                break;
            default:
                $user = null;
        }

        return $user;
    }

    /**
     * @param $id
     *
     * @return null|User
     */
    public function get($id)
    {
        return $this->getUserRepository()->find($id);
    }
}
