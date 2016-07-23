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

namespace AppBundle\Service\Notification;

use AppBundle\Event\MailerEvent;
use AppBundle\Model\Core\Provider\NotificatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Customized notificator.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
final class EventDispatchingNotificator implements NotificatorInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $templateMap;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param array                    $map
     */
    public function __construct(EventDispatcherInterface $dispatcher, array $map)
    {
        $this->dispatcher  = $dispatcher;
        $this->templateMap = $map;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException If the name is invalid.
     */
    public function publishNotification(string $name, MailerEvent $event)
    {
        if (!isset($this->templateMap[$name])) {
            throw new \LogicException(sprintf(
                'Cannot generate template name for notification with name "%s"!',
                $name
            ));
        }

        $event->setTemplateSource($this->templateMap[$name]);
        $this->dispatcher->dispatch(MailerEvent::EVENT_NAME, $event);
    }
}
