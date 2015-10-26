<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Request\ParamConverter;

use Doctrine\Instantiator\Instantiator;
use JMS\DiExtraBundle\Annotation as DI;
use ReflectionProperty;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Symfony2 param converter that transforms request attributes to data transfer objects.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @DI\Service
 * @DI\Tag("request.param_converter", attributes={"converter"="dto"})
 */
class DTOConverter implements ParamConverterInterface
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccess;

    /**
     * Constructor.
     *
     * @param PropertyAccessorInterface $propertyAccess
     *
     * @DI\InjectParams({
     *     "propertyAccess" = @DI\Inject("property_accessor")
     * })
     */
    public function __construct(PropertyAccessorInterface $propertyAccess)
    {
        $this->propertyAccess = $propertyAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $instance = $this->getDTOInstance($configuration->getClass());
        foreach ($this->getDTOParametersByClass($configuration->getClass()) as $property) {
            $propertyName = $property->getName();
            if (!$this->propertyAccess->isWritable($instance, $propertyName)) {
                throw new \RuntimeException($this->getInvalidPropertyExceptionMessage($configuration->getClass(), $propertyName));
            }

            $this->propertyAccess->setValue(
                $instance,
                $propertyName,
                $this->findAttributeInRequest($request, $property)
            );
        }

        $request->attributes->set($configuration->getName(), $instance);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return $this->getClassSuffix($configuration->getClass()) === 'DTO';
    }

    /**
     * Creates a list of all dto parameters.
     *
     * @param string $class
     *
     * @return \ReflectionProperty[]
     */
    private function getDTOParametersByClass($class)
    {
        return (new \ReflectionClass($class))->getProperties();
    }

    /**
     * Creates an instance of the data transfer object.
     *
     * @param string $class
     *
     * @return object
     */
    private function getDTOInstance($class)
    {
        static $instantiator;
        if (!$instantiator) {
            $instantiator = new Instantiator();
        }

        return $instantiator->instantiate($class);
    }

    /**
     * Searches for properties inside the request object.
     *
     * @param Request            $request
     * @param ReflectionProperty $property
     *
     * @throws \InvalidArgumentException If the property cannot be found in the request stack
     *
     * @return mixed
     */
    private function findAttributeInRequest(Request $request, ReflectionProperty $property)
    {
        $propertyPath = $property->getName();
        if ($value = $request->get($propertyPath)) {
            return $value;
        }

        if ($request->files->has($propertyPath)) {
            return $request->files->get($propertyPath);
        }

        throw new \InvalidArgumentException(sprintf('Property "%s" not found in request stack!', $propertyPath));
    }

    /**
     * Gets the class suffix of a class name.
     *
     * @param string $className
     *
     * @return string
     */
    private function getClassSuffix($className)
    {
        return substr($className, -3);
    }

    /**
     * Generates an exception message by class and property name for invalid properties.
     *
     * @param string $class
     * @param string $propertyName
     *
     * @return string
     */
    private function getInvalidPropertyExceptionMessage($class, $propertyName)
    {
        return sprintf('Cannot attach property "%s" on object instance "%s"!', $propertyName, $class);
    }
}
