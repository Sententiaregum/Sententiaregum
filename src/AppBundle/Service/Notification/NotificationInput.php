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

namespace AppBundle\Service\Notification;

use AppBundle\Model\User\User;
use Symfony\Component\EventDispatcher\Event;

/**
 * Simple DTO containing all necessary information for the notification system.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class NotificationInput extends Event
{
    /**
     * @var User[]
     */
    private $users = [];

    /**
     * @var string
     */
    private $templateSource;

    /**
     * @var mixed[]
     */
    private $parameters = [];

    /**
     * @var string
     */
    private $language;

    /**
     * Adds a user.
     *
     * @param User $user
     *
     * @return NotificationInput
     */
    public function addUser(User $user): self
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Sets the template source.
     *
     * @param string $templateSource
     *
     * @return NotificationInput
     */
    public function setTemplateSource(string $templateSource): self
    {
        $this->templateSource = $templateSource;

        return $this;
    }

    /**
     * Adds a templating parameter.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @throws \InvalidArgumentException Cannot use paameter locale as it is reserved
     *
     * @return NotificationInput
     */
    public function addParameter(string $name, $value): self
    {
        if ('locale' === $name) {
            throw new \InvalidArgumentException('Cannot apply parameter locale since this parameter is reserved!');
        }
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * Gets all users.
     *
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * Gets the template source.
     *
     * @return string
     */
    public function getTemplateSource(): string
    {
        return $this->templateSource;
    }

    /**
     * Gets all parameters.
     *
     * @return mixed[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Get language.
     *
     * @return string
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Set language.
     *
     * @param string $language
     *
     * @return NotificationInput
     */
    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }
}
