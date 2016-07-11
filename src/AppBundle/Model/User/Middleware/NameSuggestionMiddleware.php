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

namespace AppBundle\Model\User\Middleware;

use AppBundle\Model\User\DTO\CreateUserDTO;
use AppBundle\Model\User\Util\NameSuggestion\Suggestor\SuggestorInterface;
use AppBundle\Validator\Constraints\UniqueProperty;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Middleware which is responsible for the name suggestion generation if the DTO validation failed.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class NameSuggestionMiddleware implements MessageBusMiddleware
{
    /**
     * @var SuggestorInterface
     */
    private $suggestor;

    /**
     * Constructor.
     *
     * @param SuggestorInterface $suggestor
     */
    public function __construct(SuggestorInterface $suggestor)
    {
        $this->suggestor = $suggestor;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($message, callable $next)
    {
        if (!$message instanceof CreateUserDTO
            || ($message->getInfo()->isValid())
        ) {
            $next($message);

            return;
        }

        // don't continue with execution if data is invalid, but run the suggestion generator if username error exists
        $info = $message->getInfo();
        if ((!$info->isValid() && $this->isUsernameNonUnique($info->violationList))) {
            // avoid propagation after that as data is still invalid
            $info->extra[CreateUserDTO::SUGGESTIONS] = $this->suggestor->getPossibleSuggestions($message->username);
        }
    }

    /**
     * Checks if the username is non-unique.
     *
     * @param ConstraintViolationListInterface $violations
     *
     * @return bool
     */
    private function isUsernameNonUnique(ConstraintViolationListInterface $violations): bool
    {
        /** @var \Symfony\Component\Validator\ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            if ($this->isUniquePropertyViolation($violation)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the given violation is the appropriate one.
     *
     * @param ConstraintViolationInterface $violation
     *
     * @return bool
     */
    private function isUniquePropertyViolation(ConstraintViolationInterface $violation): bool
    {
        return UniqueProperty::NON_UNIQUE_PROPERTY === $violation->getCode() // compare validation codes
            && 'username' === $violation->getPropertyPath(); // the username property is always `username` in the `CreateUserDTO`
    }
}
