<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Tests\Request\ParamConverter;

use AppBundle\Model\User\Registration\DTO\CreateUserDTO;
use AppBundle\Request\ParamConverter\DTOConverter;
use AppBundle\Tests\Fixtures\DTO\FileUploadDTO;
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
     * @expectedExceptionMessage Cannot attach property "username" on object instance "AppBundle\Model\User\Registration\DTO\CreateUserDTO"!
     */
    public function testImmutableProperty()
    {
        $accessor = $this->getMockBuilder(PropertyAccessor::class)->disableOriginalConstructor()->getMock();
        $accessor
            ->expects($this->any())
            ->method('isWritable')
            ->will($this->returnValue(false));

        $converter = new DTOConverter($accessor);
        $request   = Request::create('/');

        $converter->apply($request, new ParamConverter(['class' => CreateUserDTO::class]));
    }

    public function testAttachValuesOnDTO()
    {
        $converter = new DTOConverter(new PropertyAccessor());
        $request   = Request::create('/');

        $request->attributes->set('username', 'Ma27');
        $request->attributes->set('password', '123456');
        $request->attributes->set('email', 'Ma27@sententiaregum.dev');
        $request->attributes->set('locale', 'de');

        $this->assertFalse($request->attributes->has('dto'));
        $this->assertTrue($converter->apply($request, new ParamConverter(['class' => CreateUserDTO::class, 'name' => 'dto'])));
        $this->assertInstanceOf(CreateUserDTO::class, $request->attributes->get('dto'));
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

        $request->files->set('file', $this->getMockBuilder(UploadedFile::class)->disableOriginalConstructor()->getMock());
        $this->assertTrue($converter->apply($request, new ParamConverter(['class' => FileUploadDTO::class, 'name' => 'dto'])));

        $this->assertInstanceOf(UploadedFile::class, $request->attributes->get('dto')->getFile());
    }
}
