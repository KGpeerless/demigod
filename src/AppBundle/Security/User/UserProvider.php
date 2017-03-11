<?php
namespace AppBundle\Security\User;

use AppBundle\Entity\User;
use AppBundle\Handler\UserHandler;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider implements UserProviderInterface
{
    private $userHandler;

    public function __construct(UserHandler $userHandler)
    {
        $this->userHandler  = $userHandler;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->userHandler->findOneByIdentity($username, 1);

        if (!$user)
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $username)
            );

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'AppBundle\Entity\User';
    }
}