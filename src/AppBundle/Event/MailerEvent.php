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

namespace AppBundle\Event;

use AppBundle\Model\User\User;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event base class of the notification system.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class MailerEvent extends Event
{
    const EVENT_NAME = 'app.events.notification';

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
     * @return $this
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Sets the template source.
     *
     * @param $templateSource
     *
     * @return $this
     */
    public function setTemplateSource($templateSource)
    {
        $this->templateSource = (string) $templateSource;

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
     * @return $this
     */
    public function addParameter($name, $value)
    {
        if ('locale' === $name) {
            throw new \InvalidArgumentException('Cannot apply parameter locale!');
        }
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * Gets all users.
     *
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Gets the template source.
     *
     * @return string
     */
    public function getTemplateSource()
    {
        return $this->templateSource;
    }

    /**
     * Gets all parameters.
     *
     * @return mixed[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get language.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set language.
     *
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }
}
