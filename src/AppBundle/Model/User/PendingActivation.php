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
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(
 *     name="pending_activation",
 *     indexes={
 *         @ORM\Index(name="pendingActivation_activationDate", columns={"activation_date"})
 *     }
 * )
 */
class PendingActivation
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="id", type="guid")
     */
    private $id;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="activation_date", type="datetime")
     */
    private $activationDate;

    /**
     * Set id.
     *
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Getter for the id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the activation date.
     *
     * @param DateTime $dateTime
     *
     * @return $this
     */
    public function setActivationDate(DateTime $dateTime)
    {
        $this->activationDate = $dateTime;

        return $this;
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
        if (!$this->activationDate) {
            throw new \LogicException('Missing activation date!');
        }

        return time() - $this->activationDate->getTimestamp() >= 3600 * 2;
    }
}
