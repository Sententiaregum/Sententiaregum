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

namespace AppBundle\Validator\Middleware;

use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Middleware which is responsible for DTO validation.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class DTOValidationMiddleware implements MessageBusMiddleware
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($message, callable $next): void
    {
        if (!$message instanceof ValidatableDTO || !$message->shouldValidate()) {
            $next($message);

            return;
        }

        $result = $this->validator->validate($message);
        $info   = new ValidationInfo();

        $info->violationList = $result;

        $message->setValidationInfo($info);

        if ($message->getInfo()->isValid()
            || ($message->shouldContinueOnInvalid() && !$message->getInfo()->isValid())
        ) {
            $next($message);
        }
    }
}
