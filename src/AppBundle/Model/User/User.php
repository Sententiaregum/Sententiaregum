<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Model\User;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ma27\ApiKeyAuthenticationBundle\Model\User\UserInterface;
use Serializable;

/**
 * User.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @ORM\Entity()
 * @ORM\Table(name="User")
 */
class User implements UserInterface, Serializable
{
    const STATE_NEW      = 'new';
    const STATE_APPROVED = 'approved';

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=50, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=500)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="api_key", type="string", length=200, unique=true, nullable=true)
     */
    private $apiKey;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_action", type="datetime")
     */
    private $lastAction;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registration_date", type="datetime")
     */
    private $registrationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string")
     */
    private $state = self::STATE_NEW;

    /**
     * @var bool
     *
     * @ORM\Column(name="locked", type="boolean")
     */
    private $locked = false;

    /**
     * @var string
     *
     * @ORM\Column(name="about_text", type="string", nullable=true)
     */
    private $aboutText;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Model\User\Role")
     * @ORM\JoinTable(
     *     name="User2Role",
     *     joinColumns={@ORM\JoinColumn(name="userId")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="roleId")}
     * )
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="activation_key", type="string", nullable=true, unique=true, length=255)
     */
    private $activationKey;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Model\User\User")
     * @ORM\JoinTable(
     *     name="Follower",
     *     joinColumns={@ORM\JoinColumn(name="userId")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="followerId")}
     * )
     */
    private $following;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", length=2)
     */
    private $locale = 'en';

    /**
     * Factory that fills the required fields of the user.
     *
     * @param string $username
     * @param string $password
     * @param string $email
     *
     * @return User
     */
    public static function create($username, $password, $email)
    {
        $user = new self();
        $user->setUsername($username);
        $user->setPassword($password);
        $user->setEmail($email);

        return $user;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->roles            = new ArrayCollection();
        $this->following        = new ArrayCollection();
        $this->registrationDate = new DateTime();
        $this->lastAction       = new DateTime();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = (string) $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = (string) $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = (string) $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set apiKey.
     *
     * @param string $apiKey
     *
     * @return User
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = (string) $apiKey;

        return $this;
    }

    /**
     * Get apiKey.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function removeApiKey()
    {
        $this->apiKey = null;

        return $this;
    }

    /**
     * Set lastAction.
     *
     * @param \DateTime $lastAction
     *
     * @return User
     */
    public function setLastAction(\DateTime $lastAction)
    {
        $this->lastAction = $lastAction;

        return $this;
    }

    /**
     * Get lastAction.
     *
     * @return \DateTime
     */
    public function getLastAction()
    {
        return $this->lastAction;
    }

    /**
     * Get registrationDate.
     *
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Set state.
     *
     * @param string $state
     *
     * @return User
     */
    public function setState($state)
    {
        if (!in_array($state, [self::STATE_NEW, self::STATE_APPROVED])) {
            throw new \InvalidArgumentException(sprintf('Invalid state!'));
        }

        $this->state = (string) $state;

        if (self::STATE_APPROVED === $this->state) {
            $this->removeActivationKey();
        }

        return $this;
    }

    /**
     * Get state.
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Locks the user.
     *
     * @return $this
     */
    public function lock()
    {
        $this->locked = true;

        return $this;
    }

    /**
     * Unlocks the user.
     *
     * @return $this
     */
    public function unlock()
    {
        $this->locked = false;

        return $this;
    }

    /**
     * Get locked.
     *
     * @return bool
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * Set aboutText.
     *
     * @param string $aboutText
     *
     * @return User
     */
    public function setAboutText($aboutText)
    {
        $this->aboutText = (string) $aboutText;

        return $this;
    }

    /**
     * Get aboutText.
     *
     * @return string
     */
    public function getAboutText()
    {
        return $this->aboutText;
    }

    /**
     * @return string
     */
    public function getActivationKey()
    {
        return $this->activationKey;
    }

    /**
     * Set activationKey.
     *
     * @param string $activationKey
     *
     * @return $this
     */
    public function setActivationKey($activationKey)
    {
        if (self::STATE_APPROVED === $this->getState()) {
            throw new \LogicException('Approved users cannot have an activation key!');
        }

        if (empty($activationKey)) {
            $problem = 'Cannot set empty activation key! Please call "removeActivationKey()" instead for the removal of the activation key!';

            throw new \LogicException($problem);
        }

        $this->activationKey = (string) $activationKey;

        return $this;
    }

    /**
     * Removes the activation key.
     *
     * @return $this
     */
    public function removeActivationKey()
    {
        if (self::STATE_APPROVED !== $this->getState()) {
            throw new \LogicException('Only approved users can remove activation keys!');
        }

        $this->activationKey = null;

        return $this;
    }

    /**
     * Adds a role.
     *
     * @param Role $role
     *
     * @return $this
     */
    public function addRole(Role $role)
    {
        if ($this->hasRole($role)) {
            throw new \LogicException(
                sprintf('Role "%s" already in user by user "%s"!', $role->getRole(), $this->getUsername())
            );
        }

        $this->roles->add($role);

        return $this;
    }

    /**
     * Removes a  role.
     *
     * @param Role $role
     *
     * @return $this
     */
    public function removeRole(Role $role)
    {
        if (!$this->hasRole($role)) {
            throw new \LogicException(sprintf('Cannot remove not existing role "%s"!', $role->getRole()));
        }

        $this->roles->removeElement($role);

        return $this;
    }

    /**
     * Checks whether the user has a role.
     *
     * @param Role $role
     *
     * @return bool
     */
    public function hasRole(Role $role)
    {
        foreach ($this->getRoles() as $storedRole) {
            if ($storedRole->getRole() === $role->getRole()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the roles.
     *
     * @return Role[]
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * Add follower.
     *
     * @param User $user
     *
     * @return $this
     */
    public function addFollowing(User $user)
    {
        $this->following->add($user);

        return $this;
    }

    /**
     * Removes a follower.
     *
     * @param User $user
     *
     * @return $this
     */
    public function removeFollowing(User $user)
    {
        if (!$this->follows($user)) {
            throw new \LogicException('Cannot remove relation with invalid user "%s"!', $user->getUsername());
        }

        $this->following->removeElement($user);

        return $this;
    }

    /**
     * Checks whether the current user follows a specific user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function follows(User $user)
    {
        foreach ($this->following as $following) {
            if ($following->getUsername() === $user->getUsername()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get followers.
     *
     * @return Role[]
     */
    public function getFollowing()
    {
        return $this->following->toArray();
    }

    /**
     * Get locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set locale.
     *
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = (string) $locale;

        return $this;
    }

    /**
     * Serializes the internal dataset.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->email,
            $this->lastAction->getTimestamp(),
            $this->registrationDate->getTimestamp(),
            $this->apiKey,
            $this->activationKey,
            $this->state,
            $this->locked,
            $this->aboutText,
            $this->getRoles(),
            $this->getFollowing(),
        ]);
    }

    /**
     * Deserializes the data and re-creates the model.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->id               = $data[0];
        $this->username         = $data[1];
        $this->password         = $data[2];
        $this->email            = $data[3];
        $this->lastAction       = new DateTime(sprintf('@%s', $data[4]));
        $this->registrationDate = new DateTime(sprintf('@%s', $data[5]));
        $this->apiKey           = $data[6];
        $this->activationKey    = $data[7];
        $this->state            = $data[8];
        $this->locked           = $data[9];
        $this->aboutText        = $data[10];
        $this->roles            = new ArrayCollection($data[11]);
        $this->following        = new ArrayCollection($data[12]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
}
