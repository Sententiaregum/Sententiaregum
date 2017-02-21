<?php

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\Model\User;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Model that represents the internal activation life cycle during the user approval.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 *
 * @ORM\Embeddable
 */
class PendingActivation implements \Serializable
{
    /**
     * @var DateTime
     *
     * @ORM\Column(name="activation_date", type="datetime", nullable=true)
     */
    private $activationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="key", type="string", nullable=true)
     */
    private $key;

    /**
     * Constructor.
     *
     * @param DateTime $activationDate
     * @param string   $key
     */
    public function __construct(DateTime $activationDate, string $key = null)
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
    public function isActivationExpired(): bool
    {
        return time() - $this->activationDate->getTimestamp() >= 3600 * 2;
    }

    /**
     * Set key.
     *
     * @param string $key
     *
     * @return PendingActivation
     */
    public function setKey($key): self
    {
        $this->key = (string) $key;

        return $this;
    }

    /**
     * Get key.
     *
     * @return string
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([
            $this->key,
            $this->activationDate,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        [$this->key, $this->activationDate] = unserialize($serialized);
    }
}
