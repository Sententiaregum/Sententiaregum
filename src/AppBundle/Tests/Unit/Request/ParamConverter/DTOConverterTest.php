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

declare(strict_types=1);

namespace AppBundle\Tests\Unit\Request\ParamConverter;

use AppBundle\Model\User\DTO\CreateUserDTO;
use AppBundle\Request\ParamConverter\DTOConverter;
use AppBundle\Tests\Unit\Fixtures\FileUploadDTO;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DTOConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testSupportClassesEndingWithDTO()
    {
        $configuration = new ParamConverter(['class' => CreateUserDTO::class]);
        $converter     = new DTOConverter(new PropertyAccessor());
        $this->assertTrue($converter->supports($configuration));
        $this->assertFalse($converter->supports(new ParamConverter(['class' => \stdClass::class])));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Cannot attach property "username" on object instance "AppBundle\Model\User\DTO\CreateUserDTO"!
     */
    public function testImmutableProperty()
    {
        $accessor = $this->getMockWithoutInvokingTheOriginalConstructor(PropertyAccessor::class);
        $accessor
            ->expects($this->any())
            ->method('isWritable')
            ->willReturn(false);

        $converter = new DTOConverter($accessor);
        $request   = Request::create('/');

        $converter->apply($request, new ParamConverter(['class' => CreateUserDTO::class]));
    }

    public function testAttachValuesOnDTO()
    {
        $converter = new DTOConverter(new PropertyAccessor());
        $request   = Request::create('/');

        $request->attributes->set('username', 'Ma27');
        $request->query->set('password', null);
        $request->request->set('email', 'Ma27@sententiaregum.dev');
        $request->attributes->set('locale', 'de');

        $this->assertFalse($request->attributes->has('dto'));
        $this->assertTrue($converter->apply($request, new ParamConverter(['class' => CreateUserDTO::class, 'name' => 'dto'])));
        $this->assertInstanceOf(CreateUserDTO::class, $request->attributes->get('dto'));

        $this->assertEmpty($request->get('dto')->getPassword());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Property "username" not found in request stack!
     */
    public function testMissingProperties()
    {
        $converter = new DTOConverter(new PropertyAccessor());
        $request   = Request::create('/');

        $converter->apply($request, new ParamConverter(['class' => CreateUserDTO::class, 'name' => 'dto']));
    }

    public function testFileUpload()
    {
        $converter = new DTOConverter(new PropertyAccessor());
        $request   = Request::create('/');

        $request->files->set('file', $this->getMockWithoutInvokingTheOriginalConstructor(UploadedFile::class));
        $this->assertTrue($converter->apply($request, new ParamConverter(['class' => FileUploadDTO::class, 'name' => 'dto'])));

        $this->assertInstanceOf(UploadedFile::class, $request->attributes->get('dto')->getFile());
    }
}
