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

namespace AppBundle\Model\User\Registration\NameSuggestion;

use AppBundle\Model\User\Registration\NameSuggestion\Suggestor\DotReplacementSuggestor;
use AppBundle\Model\User\Registration\NameSuggestion\Suggestor\SuggestorInterface;
use AppBundle\Model\User\Registration\NameSuggestion\Suggestor\YearPostfixSuggestor;
use AppBundle\Model\User\UserRepository;

/**
 * Class that builds suggestions for usernames.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class ChainSuggestor implements ChainSuggestorInterface
{
    /**
     * @var SuggestorInterface[]
     */
    private $suggestors = [];

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

        $this->register(new YearPostfixSuggestor());
        $this->register(new DotReplacementSuggestor());
    }

    /**
     * {@inheritdoc}
     */
    public function getPossibleSuggestions(string $name): array
    {
        $suggestions = array_merge(
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
    public function register(SuggestorInterface $suggestor): self
    {
        $this->suggestors[] = $suggestor;

        return $this;
    }
}
