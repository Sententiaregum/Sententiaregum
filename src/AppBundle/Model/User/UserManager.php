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

use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\DiExtraBundle\Annotation as DI;
use Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * Manager which is responsible for the business logic of the user.
 *
 * @DI\Service(id="app.user.user_manager")
 */
class UserManager extends BaseEntityManager implements UserManagerInterface
{
    /**
     * Constructor.
     * This constructor is responsible for.
     *
     * @param string          $class
     * @param ManagerRegistry $registry
     *
     * @DI\InjectParams({
     *     "class" = @DI\Inject("%app.model.user%"),
     *     "registry" = @DI\Inject("doctrine")
     * })
     */
    public function __construct($class, ManagerRegistry $registry)
    {
        parent::__construct($class, $registry);
    }
}
