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

use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Custom role model.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="Role")
 */
class Role implements RoleInterface, Serializable
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
     * @var string
     *
     * @ORM\Column(name="role", length=255, unique=true)
     */
    private $role;

    /**
     * Constructor.
     *
     * @param $role
     */
    public function __construct($role)
    {
        $this->role = (string) $role;
    }

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
     * Get role id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Serializes the entity.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize([$this->id, $this->role]);
    }

    /**
     * Re-creates the entity by the unserialized data.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list($this->id, $this->role) = unserialize($serialized);
    }
}
