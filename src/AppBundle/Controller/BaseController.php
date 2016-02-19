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

namespace AppBundle\Controller;

use AppBundle\Model\User\User;
use AppBundle\View\I18nResponseFormatBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Abstract controller that contains some basic utilities.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
abstract class BaseController extends Controller
{
    /**
     * Converts all validation errors to a flat array that can be serialized by the jms serializer.
     *
     * @param ConstraintViolationListInterface $constraintViolations
     * @param string                           $domain
     *
     * @return string[][]
     */
    protected function sortViolationMessagesByPropertyPath(
        ConstraintViolationListInterface $constraintViolations,
        $domain = 'validators'
    ) {
        /** @var I18nResponseFormatBuilderInterface $i18nResponseBuilder */
        $i18nResponseBuilder = $this->get('app.view.i18n_error_response_builder');

        return $i18nResponseBuilder->formatTranslatableViolationList(
            $constraintViolations,
            true,
            true,
            $this->getLocaleShortNames(),
            $domain
        );
    }

    /**
     * Converts all violations to a flat, translatable list which can be serialized easily.
     *
     * @param ConstraintViolationListInterface $constraintViolations
     * @param string                           $domain
     *
     * @return string[]
     */
    protected function getI18nErrorResponseWithoutSort(
        ConstraintViolationListInterface $constraintViolations,
        $domain = 'validators'
    ) {
        /** @var I18nResponseFormatBuilderInterface $i18nResponseBuilder */
        $i18nResponseBuilder = $this->get('app.view.i18n_error_response_builder');

        return $i18nResponseBuilder->formatTranslatableViolationList(
            $constraintViolations,
            false,
            true,
            $this->getLocaleShortNames(),
            $domain
        );
    }

    /**
     * Gets the current user authenticated against firewall.
     *
     * @return User
     */
    protected function getCurrentUser()
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \RuntimeException(sprintf(
                'Expect user object of instance "%s", but found "%s"!',
                User::class,
                get_class($user)
            ));
        }

        return $user;
    }

    /**
     * Getter for all available locales.
     *
     * @return string[]
     */
    protected function getLocales()
    {
        return $this->getParameter('app.locales');
    }

    /**
     * Getter for the locale keys.
     *
     * @return string[]
     */
    protected function getLocaleShortNames()
    {
        return $this->getParameter('app.locale_keys');
    }
}
