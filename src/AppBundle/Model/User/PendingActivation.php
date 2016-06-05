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

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Model that represents the internal activation life cycle during the user approval.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * @ORM\Embeddable
 */
class PendingActivation implements \Serializable
{
    /**
     * @var DateTime
     *
     * @ORM\Column(name="activation_date", type="datetime")
     */
    private $activationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="key", type="string")
     */
    private $key;

    /**
     * Constructor.
     *
     * @param DateTime $activationDate
     * @param string   $key
     */
    public function __construct(DateTime $activationDate, $key = null)
    {
        $this->activationDate = $activationDate;
        $this->key            = $key;
    }

    /**
     * Checks if the activation is expired.
     *
     * @throws \LogicException If the activation is missing
     *
     * @return bool
     */
    public function isActivationExpired()
    {
        return time() - $this->activationDate->getTimestamp() >= 3600 * 2;
    }

    /**
     * Set key.
     *
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = (string) $key;
        return $this;
    }

    /**
     * Get key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->key,
            $this->activationDate,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->key, $this->activationDate) = unserialize($serialized);
    }
}
