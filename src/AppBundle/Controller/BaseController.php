<?php

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Model\User\User;
use AppBundle\View\I18nResponseFormatBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Abstract controller that contains some basic utilities.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
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
        string $domain = 'validators'
    ): array {
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
        string $domain = 'validators'
    ): array {
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
    protected function getCurrentUser(): User
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
     * Getter for the locale keys.
     *
     * @return string[]
     */
    protected function getLocaleShortNames(): array
    {
        return $this->getParameter('app.locale_keys');
    }

    /**
     * Handles a message.
     *
     * @param mixed $message
     */
    protected function handle($message): void
    {
        $this->get('command_bus')->handle($message);
    }
}
