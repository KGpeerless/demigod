<?php
namespace AppBundle\Handler;

use GKL\Helper\Des\Des;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;

trait UserEncryptTrait
{
    /**
     * @var UserPasswordEncoder
     */
    protected $userPasswordEncoder;

    /**
     * @var string
     */
    protected $apiSecretKey;

    public function userEncrypt(UserInterface $user)
    {
        $user
            ->setPassword($this->userPasswordEncoder->encodePassword($user, $user->getPassword()))
            ->setApiKey(Des::getInstance(Des::MODE_3DES, $this->apiSecretKey)->encrypt($user->getUsername()));

        return $user;
    }

    public function encryptUserPassword(UserInterface $user, $password)
    {
        return $this->userPasswordEncoder->encodePassword($user, $password);
    }

    public function setUserPasswordEncoder(UserPasswordEncoder $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function setApiSecretKey($key)
    {
        $this->apiSecretKey = $key;
    }
}