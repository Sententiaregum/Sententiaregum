<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Event;

use AppBundle\Model\User\User;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event base class of the notification system
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
     * Adds a user
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
     * Sets the template source
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
     * Adds a templating parameter
     *
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function addParameter($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * Gets all users
     *
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Gets the template source
     *
     * @return string
     */
    public function getTemplateSource()
    {
        return $this->templateSource;
    }

    /**
     * Gets all parameters
     *
     * @return mixed[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
