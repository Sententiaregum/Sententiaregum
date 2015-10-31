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
use Doctrine\ORM\Mapping as ORM;

/**
 * Model that represents the internal activation life cycle during the user approval.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @ORM\Entity
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
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="activation_date", type="datetime")
     */
    private $activationDate;

    /**
     * Getter for the id.
     *
     * @return int
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
