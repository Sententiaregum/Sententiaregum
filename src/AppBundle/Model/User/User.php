<?php

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Model\User;

use AppBundle\Model\User\Util\DateTimeComparison;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ma27\ApiKeyAuthenticationBundle\Annotation as Auth;
use Ramsey\Uuid\Uuid;
use Serializable;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @ORM\Entity(repositoryClass="AppBundle\Model\User\UserRepository")
 * @ORM\Table(name="User", indexes={
 *     @ORM\Index(name="user_lastAction", columns={"last_action"}),
 *     @ORM\Index(name="user_locale", columns={"locale"})
 * })
 */
class User implements UserInterface, Serializable
{
    const STATE_NEW                   = 'new';
    const STATE_APPROVED              = 'approved';
    const MAX_FAILED_ATTEMPTS_FROM_IP = 3;
    const FAILED_AUTH_POOL            = 'failed_auths';
    const AUTH_ATTEMPT_POOL           = 'auth_attempts';

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="id", type="guid")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=50, unique=true)
     * @Auth\Login
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=500)
     * @Auth\Password
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
     * @Auth\ApiKey
     */
    private $apiKey;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="last_action", type="datetime")
     * @Auth\LastAction
     */
    private $lastAction;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="registration_date", type="datetime")
     */
    private $registrationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string")
     */
    private $state;

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
     * @ORM\ManyToMany(targetEntity="AppBundle\Model\User\Role", indexBy="role")
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Model\User\User", indexBy="username", fetch="EXTRA_LAZY")
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
     * @var PendingActivation
     *
     * @ORM\OneToOne(
     *     targetEntity="AppBundle\Model\User\PendingActivation",
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     * @ORM\JoinColumn(name="pending_activation", nullable=true)
     */
    private $pendingActivation;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="AppBundle\Model\User\AuthenticationAttempt",
     *     indexBy="username",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(
     *     name="FailedAuthAttempt2User",
     *     joinColumns={@ORM\JoinColumn(name="userId")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="attemptId")}
     * )
     */
    private $failedAuthentications;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="AppBundle\Model\User\AuthenticationAttempt",
     *     indexBy="username",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(
     *     name="Auth2User",
     *     joinColumns={@ORM\JoinColumn(name="userId")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="attemptId")}
     * )
     */
    private $authentications;

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
        $this->id = Uuid::uuid4()->toString();

        $this->roles                 = new ArrayCollection();
        $this->following             = new ArrayCollection();
        $this->failedAuthentications = new ArrayCollection();
        $this->authentications       = new ArrayCollection();

        $this->registrationDate      = new DateTime();
        $this->lastAction            = new DateTime();

        $this->setState(self::STATE_NEW);
    }

    /**
     * Get id.
     *
     * @return string
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
     * @param DateTime $lastAction
     *
     * @return User
     */
    public function setLastAction(DateTime $lastAction)
    {
        $this->lastAction = $lastAction;

        return $this;
    }

    /**
     * Get lastAction.
     *
     * @return DateTime
     */
    public function getLastAction()
    {
        return $this->lastAction;
    }

    /**
     * Get registrationDate.
     *
     * @return DateTime
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
        if (!in_array($state, [self::STATE_NEW, self::STATE_APPROVED], true)) {
            throw new \InvalidArgumentException('Invalid state!');
        }

        $this->state = (string) $state;

        if (self::STATE_APPROVED === $this->state) {
            $this->removeActivationKey();
        } else {
            $this->pendingActivation = new PendingActivation();
            $this->pendingActivation->setActivationDate($this->getRegistrationDate());
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

        $this->activationKey     = null;
        $this->pendingActivation = null;

        return $this;
    }

    /**
     * Adds a role.
     *
     * @param Role $role
     *
     * @throws \InvalidArgumentException If the user is not approved.
     * @throws \LogicException           If the role is already attached.
     *
     * @return $this
     */
    public function addRole(Role $role)
    {
        if ($this->getState() === static::STATE_NEW) {
            throw new \InvalidArgumentException('Cannot attach role on non-approved user!');
        }

        if ($this->hasRole($role)) {
            throw new \LogicException(sprintf(
                'Role "%s" already attached at user "%s"!',
                $role->getRole(),
                $this->getUsername()
            ));
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
     * Get pendingActivation.
     *
     * @return PendingActivation
     */
    public function getPendingActivation()
    {
        return $this->pendingActivation;
    }

    /**
     * Set pendingActivation.
     *
     * @param PendingActivation $pendingActivation
     *
     * @return $this
     */
    public function setPendingActivation(PendingActivation $pendingActivation)
    {
        $this->pendingActivation = $pendingActivation;

        return $this;
    }

    /**
     * Checks whether the user ip is new and if true it will be persisted.
     *
     * @param string             $ip
     * @param DateTimeComparison $comparison
     *
     * @return bool
     */
    public function addAndValidateNewUserIp($ip, DateTimeComparison $comparison)
    {
        if (!($isKnown = $this->isKnownIp($ip))) {
            $attempt = new AuthenticationAttempt();
            $attempt
                ->setIp($ip)
                ->increaseAttemptCount();

            $this->authentications->add($attempt);
            $this->eraseKnownIpFromBadIPList($ip, $comparison);
        }

        return !$isKnown;
    }

    /**
     * Adds one ip of a failed authentication unless its authentication succeeded previously (users may mistype some
     * times).
     *
     * @param string $ip
     *
     * @return $this
     */
    public function addFailedAuthenticationWithIp($ip)
    {
        if (!$this->isKnownIp($ip)) {
            if (!($attempt = $this->getAuthAttemptModelByIp($ip, self::FAILED_AUTH_POOL))) {
                $attempt = new AuthenticationAttempt();
                $attempt->setIp($ip);

                $this->failedAuthentications->add($attempt);
            }

            $attempt->increaseAttemptCount();
        }

        return $this;
    }

    /**
     * Checks if one ip exceeds the attempt count.
     *
     * @param string             $ip
     * @param DateTimeComparison $comparison
     *
     * @return bool
     */
    public function exceedsIpFailedAuthAttemptMaximum($ip, DateTimeComparison $comparison)
    {
        if (!$this->isKnownIp($ip, self::FAILED_AUTH_POOL)) {
            return false;
        }

        return $this->needsAuthWarning($this->getAuthAttemptModelByIp($ip, self::FAILED_AUTH_POOL), $comparison);
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
            $this->authentications->toArray(),
            $this->failedAuthentications->toArray(),
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

        $this->id                    = $data[0];
        $this->username              = $data[1];
        $this->password              = $data[2];
        $this->email                 = $data[3];
        $this->lastAction            = new DateTime(sprintf('@%s', $data[4]));
        $this->registrationDate      = new DateTime(sprintf('@%s', $data[5]));
        $this->apiKey                = $data[6];
        $this->activationKey         = $data[7];
        $this->state                 = $data[8];
        $this->locked                = $data[9];
        $this->aboutText             = $data[10];
        $this->roles                 = new ArrayCollection($data[11]);
        $this->following             = new ArrayCollection($data[12]);
        $this->authentications       = new ArrayCollection($data[13]);
        $this->failedAuthentications = new ArrayCollection($data[14]);
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

    /**
     * Checks whether the following ip is known.
     *
     * @param string $ip
     * @param string $dataSource
     *
     * @return bool
     */
    private function isKnownIp($ip, $dataSource = self::AUTH_ATTEMPT_POOL)
    {
        return !empty($this->getAuthAttemptModelByIp($ip, $dataSource));
    }

    /**
     * Gets the auth model by the given ip.
     *
     * @param string $ip
     * @param string $dataSource
     *
     * @return AuthenticationAttempt|null
     */
    private function getAuthAttemptModelByIp($ip, $dataSource = self::AUTH_ATTEMPT_POOL)
    {
        $authAttempt = null;
        $pool        = $dataSource === self::AUTH_ATTEMPT_POOL
            ? $this->authentications->toArray()
            : $this->failedAuthentications->toArray();

        /** @var AuthenticationAttempt $authenticationAttempt */
        foreach ($pool as $authenticationAttempt) {
            if ((string) $ip === $authenticationAttempt->getIp()) {
                $authAttempt = $authenticationAttempt;
                break;
            }
        }

        return $authAttempt;
    }

    /**
     * Checks if enough failed authentications are done in the past to rise an auth warning.
     *
     * @param AuthenticationAttempt $attempt
     * @param DateTimeComparison    $comparison
     *
     * @return bool
     */
    private function needsAuthWarning(AuthenticationAttempt $attempt, DateTimeComparison $comparison)
    {
        $count = $attempt->getAttemptCount();
        if (self::MAX_FAILED_ATTEMPTS_FROM_IP <= $count) {
            if (($count - 3) === 0) {
                return true;
            }

            return !$this->isPreviouslyLoginFailed('-6 hours', $comparison, true, $attempt->getIp());
        }

        return false;
    }

    /**
     * Erases user IPs from the blacklist.
     *
     * @param string             $ip
     * @param DateTimeComparison $comparison
     */
    private function eraseKnownIpFromBadIPList($ip, DateTimeComparison $comparison)
    {
        if ($this->isPreviouslyLoginFailed('-10 minutes', $comparison)) {
            // if it failed before the login, the login may be corrupted,
            // there the information should be kept. The next login won't erase it although there may
            // be a corruption since this will be called at the first login with a new IP only
            return;
        }

        /** @var AuthenticationAttempt $model */
        foreach ($this->failedAuthentications->toArray() as $model) {
            if ($ip === $model->getIp()) {
                $this->failedAuthentications->removeElement($model);
                break;
            }
        }
    }

    /**
     * Checks if the auth failed previously.
     *
     * @param string             $diff
     * @param bool               $ignoreLastAttempts
     * @param DateTimeComparison $comparison
     * @param string             $ip
     *
     * @return bool
     */
    private function isPreviouslyLoginFailed($diff, DateTimeComparison $comparison, $ignoreLastAttempts = false, $ip = null)
    {
        return $this->failedAuthentications->exists(
            function ($index, AuthenticationAttempt $failedAttempt) use ($diff, $ignoreLastAttempts, $ip, $comparison) {
                $ipRange = $failedAttempt->getLastFailedAttemptTimesInRange();

                return $comparison(
                    $diff,
                    $failedAttempt->getIp() && $ignoreLastAttempts ? end($ipRange) : $failedAttempt->getLatestFailedAttemptTime()
                );
            }
        );
    }
}
