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

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Serializable;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Custom role model.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 *
 * @ORM\Entity(readOnly=true, repositoryClass="AppBundle\Service\Doctrine\Repository\RoleRepository")
 * @ORM\Table(name="Role")
 * @ORM\Cache(region="non_strict")
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
     * @param string $role
     */
    public function __construct(string $role)
    {
        $this->role = (string) $role;
        $this->id   = Uuid::uuid4()->toString();
    }

    /**
     * Get role id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * Serializes the entity.
     *
     * @return string
     */
    public function serialize(): string
    {
        return serialize([$this->id, $this->role]);
    }

    /**
     * Re-creates the entity by the unserialized data.
     *
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        [$this->id, $this->role] = unserialize($serialized);
    }
}
