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

namespace AppBundle\Request\ParamConverter;

use ReflectionProperty;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Symfony param converter that transforms request attributes to data transfer objects.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
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
     */
    public function __construct(PropertyAccessorInterface $propertyAccess)
    {
        $this->propertyAccess = $propertyAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $class                 = $configuration->getClass();
        $constant              = sprintf('%s::EMPTY_PROPERTIES', $class);
        $propertiesToBeSkipped = [];
        $instance              = new $class();
        $declaredProperties    = array_filter(
            (new \ReflectionClass($class))->getProperties(),
            function (ReflectionProperty $property) use ($class) {
                return $property->getDeclaringClass()->name === $class;
            }
        );

        // fetch result properties that are optional and to be skipped as those must not be processed
        if (defined($constant)) {
            $propertiesToBeSkipped = constant($constant);
        }

        /** @var ReflectionProperty $property */
        foreach ($declaredProperties as $property) {
            $propertyName = $property->getName();
            if (in_array($propertyName, $propertiesToBeSkipped, true)) {
                continue;
            }

            // non-writable properties cause issues with the DTO creation
            if (!$this->propertyAccess->isWritable($instance, $propertyName)) {
                throw new \RuntimeException($this->getInvalidPropertyExceptionMessage($class, $propertyName));
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
    public function supports(ParamConverter $configuration): bool
    {
        return $this->getClassSuffix($configuration->getClass()) === 'DTO';
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

        foreach ([true, false] as $camelCase) {
            $key = $camelCase ? $propertyPath : Container::underscore($propertyPath);

            // optimization: don't call against Request#get() to avoid duplicated code lookups
            if ($request->query->has($key)) {
                return $request->query->get($key);
            }
            if ($request->attributes->has($key)) {
                return $request->attributes->get($key);
            }
            if ($request->request->has($key)) {
                return $request->request->get($key);
            }
            if ($request->files->has($key)) {
                return $request->files->get($key);
            }
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
    private function getClassSuffix(string $className): string
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
    private function getInvalidPropertyExceptionMessage(string $class, string $propertyName): string
    {
        return sprintf('Cannot attach property "%s" on object instance "%s"!', $propertyName, $class);
    }
}
