<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Model\User\Registration\NameSuggestion;

use AppBundle\Model\User\Registration\NameSuggestion\Suggestor\DotReplacementSuggestor;
use AppBundle\Model\User\Registration\NameSuggestion\Suggestor\SuggestorInterface;
use AppBundle\Model\User\Registration\NameSuggestion\Suggestor\YearPostfixSuggestor;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class that builds suggestions for usernames
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
     * Constructor
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

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('user.username')
            ->from('User:User', 'user')
            ->where($qb->expr()->in('user.username', ':nameList'))
            ->setParameter(':nameList', $suggestions);

        $result = $qb->getQuery()->getResult();

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
}
