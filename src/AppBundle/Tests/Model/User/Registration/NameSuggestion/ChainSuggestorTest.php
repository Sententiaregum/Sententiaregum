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
