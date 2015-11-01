<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Doctrine\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;

/**
 * Marker interface that tells doctrine that that's a special interface made for the prod environment only.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
interface ProductionFixtureInterface extends FixtureInterface
{
}
