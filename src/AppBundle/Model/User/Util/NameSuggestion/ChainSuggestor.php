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

namespace AppBundle\Model\User\Util\NameSuggestion;

use AppBundle\Model\User\UserReadRepositoryInterface;
use AppBundle\Model\User\Util\NameSuggestion\Suggestor\SuggestorInterface;

/**
 * Class that builds suggestions for usernames.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class ChainSuggestor implements ChainSuggestorInterface
{
    /**
     * @var SuggestorInterface[]
     */
    private $suggestors = [];

    /**
     * @var UserReadRepositoryInterface
     */
    private $userRepository;

    /**
     * Constructor.
     *
     * @param UserReadRepositoryInterface $userRepository
     */
    public function __construct(UserReadRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getPossibleSuggestions(string $name): array
    {
        $suggestions = array_merge(
            [],
            [], // array_merge expects at least 2 parameters
            ...array_map(function (SuggestorInterface $suggestor) use ($name) {
                return $suggestor->getPossibleSuggestions($name);
            }, $this->suggestors)
        );

        if (count($suggestions) === 0) {
            return [];
        }

        return $this->userRepository->filterUniqueUsernames($suggestions);
    }

    /**
     * {@inheritdoc}
     */
    public function register(SuggestorInterface $suggestor): ChainSuggestorInterface
    {
        $this->suggestors[] = $suggestor;

        return $this;
    }
}
