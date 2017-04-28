<?php
namespace AppBundle\Handler;

use AppBundle\Entity\Merchant;
use AppBundle\Entity\User;
use AppBundle\Form\MerchantType;

class MerchantHandler extends HandlerBase
{
    use UserEncryptTrait;

    protected $formType = MerchantType::class;

    /**
     * @param $id
     *
     * @return null|User
     */
    public function get($id)
    {
        return $this->getUserRepository()->find($id);
    }

    /**
     * @param $entity
     *
     * @return Merchant
     */
    public function save($entity)
    {
        foreach ($entity->getUsers() as $user) {
            $user
                ->setRoles(["ROLE_USER"])
                ->setIsCreator(true)
                ->setMerchant($entity);
            $user = $this->userEncrypt($user->setMerchant($entity));

            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        return $entity;
    }
}
