<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Tests\Model\User\Data;

use AppBundle\Model\User\Data\DTOBuilder;
use AppBundle\Model\User\Registration\DTO\CreateUserDTO;
use FOS\RestBundle\Request\ParamFetcher;

class DTOBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildRegistrationDTO()
    {
        $aggregator   = new DTOBuilder();
        $paramFetcher = $this->getMockBuilder(ParamFetcher::class)->disableOriginalConstructor()->getMock();

        $paramFetcher
            ->expects($this->at(0))
            ->method('get')
            ->with('username')
            ->will($this->returnValue('Ma27'));

        $paramFetcher
            ->expects($this->at(1))
            ->method('get')
            ->with('password')
            ->will($this->returnValue('123456'));

        $paramFetcher
            ->expects($this->at(2))
            ->method('get')
            ->with('email')
            ->will($this->returnValue('Ma27@sententiaregum.dev'));

        $paramFetcher
            ->expects($this->at(3))
            ->method('get')
            ->with('locale')
            ->will($this->returnValue('en'));

        $result = $aggregator->buildRegistrationDTO($paramFetcher);
        $this->assertInstanceOf(CreateUserDTO::class, $result);
    }
}
