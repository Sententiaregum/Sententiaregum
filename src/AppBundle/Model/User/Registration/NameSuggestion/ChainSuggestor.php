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

namespace AppBundle\Model\User\Registration\NameSuggestion;

use AppBundle\Model\User\Registration\NameSuggestion\Suggestor\DotReplacementSuggestor;
use AppBundle\Model\User\Registration\NameSuggestion\Suggestor\SuggestorInterface;
use AppBundle\Model\User\Registration\NameSuggestion\Suggestor\YearPostfixSuggestor;
use Doctrine\ORM\EntityManagerInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class that builds suggestions for usernames.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @DI\Service("app.user.registration.name_suggestor")
 */
class ChainSuggestor implements ChainSuggestorInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SuggestorInterface[]
     */
    private $suggestors = [];

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager
     *
     * @DI\InjectParams({
     *     "entityManager" = @DI\Inject("doctrine.orm.default_entity_manager")
     * })
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->register(new YearPostfixSuggestor());
        $this->register(new DotReplacementSuggestor());
    }

    /**
     * {@inheritdoc}
     */
    public function getPossibleSuggestions($name)
    {
        $suggestions = [];
        foreach ($this->suggestors as $suggestor) {
            $suggestions = array_merge($suggestions, $suggestor->getPossibleSuggestions($name));
        }

        $result = $this->queryExistingUsersBySuggestedNames($suggestions);

        return array_filter(
            $suggestions,
            function ($username) use ($result) {
                return !in_array($username, $result);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function register(SuggestorInterface $suggestor)
    {
        $this->suggestors[] = $suggestor;

        return $this;
    }

    /**
     * Queries for existing users.
     *
     * @param string[] $suggestions
     *
     * @return string[]
     */
    private function queryExistingUsersBySuggestedNames(array $suggestions)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('user.username')
            ->from('Account:User', 'user')
            ->where($qb->expr()->in('user.username', ':nameList'))
            ->setParameter(':nameList', $suggestions);

        $result = $qb->getQuery()->getResult();

        return array_map(function ($row) {
            return $row['username'];
        }, $result);
    }
}
