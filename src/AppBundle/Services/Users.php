<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseFOSUBProvider;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Entity\Users as UserEntity;

/**
 * Class: Users
 * @author A.Kravchuk
 */
class Users extends BaseFOSUBProvider
{
    /**
     * Todo test
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        // get property from provider configuration by provider name
        // , it will return `facebook_id` in that case (see service definition below)
        $property = $this->getProperty($response);
        $username = $response->getUsername(); // get the unique user identifier

        //we "disconnect" previously connected users
        $existingUser = $this->userManager->findUserBy(array($property => $username));
        if (null !== $existingUser) {
            // set current user id and token to null for disconnect
            // ...

            $this->userManager->updateUser($existingUser);
        }
        //we connect current user, set current user id and token
        // ...
        $this->userManager->updateUser($user);
    }

    /**
     * Load user and store in database
     *
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $user = $this->userManager->findUserBy([$this->getProperty($response) => $response->getUsername()]);

        $serviceName = $response->getResourceOwner()->getName();

        // if null just create new user and set it properties
        if (null === $user) {
            $user = new UserEntity();
            $name = $response->getFirstName();
            $surname = $response->getLastName();
            $email = $response->getEmail() ? : $response->getUsername() . '@' . $response->getResourceOwner()->getName() . 'com';
            if(!$name) {
                list($name, $surname) = explode(' ', $response->getRealName());
            }
            $user->setUsername($response->getUsername());
            $user->setName($name);
            $user->setSurname($surname);
            $user->setEmail($email);
            $user->setPassword($response->getUsername()); // Set wrong not hashed password, user can change it
            $user->setEnabled(true);
        }

        // else update access token of existing user
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';
        $user->$setter($response->getAccessToken());//update access token
        $setter = 'set' . ucfirst($serviceName) . 'Id';
        $user->$setter($response->getUsername());//update user social id

        $this->userManager->updateUser($user);
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function updateProfile($user)
    {
        $this->userManager->updateUser($user);
        return $user;
    }

    /**
     * @param UserEntity $user
     */
    public function toGrayList(\AppBundle\Entity\Users $user)
    {
        $user->addRole('ROLE_GRAY_LIST');
        
        $this->userManager->updateUser($user);
    }
}
