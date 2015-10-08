<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Tests\Model\User\Registration\NameSuggestion;

use AppBundle\Test\KernelTestCase;
use AppBundle\Tests\Fixtures\Doctrine\NameSuggestionUserFixture;

class ChainSuggestorTest extends KernelTestCase
{
    protected function setUp()
    {
        $this->loadDataFixtures([NameSuggestionUserFixture::class]);
    }

    public function testGetSuggestions()
    {
        /** @var \AppBundle\Model\User\Registration\NameSuggestion\ChainSuggestor $service */
        $service = $this->getService('app.user.registration.name_suggestor');

        $result = $service->getPossibleSuggestions('Ma27');
        $this->assertNotContains('Ma27'.(string) date('Y'), $result);
    }
}
