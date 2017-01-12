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

namespace AppBundle\Service\Notification;

use AppBundle\Model\Core\Provider\NotificatorInterface;
use AppBundle\Service\Notification\Channel\NotificationChannelInterface;

/**
 * Customized notificator.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
final class ChannelDelegatingNotificator implements NotificatorInterface
{
    /**
     * @var string[]
     */
    private $templateMap;

    /**
     * @var NotificationChannelInterface[]
     */
    private $channels;

    /**
     * Constructor.
     *
     * @param string[]                       $map
     * @param NotificationChannelInterface[] $notificationChannels
     */
    public function __construct(array $map, array $notificationChannels)
    {
        $this->templateMap = $map;
        $this->channels    = $notificationChannels;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException If the name is invalid.
     */
    public function publishNotification(string $name, NotificationInput $event, array $channels, string $template = null): void
    {
        if (null === $template) {
            if (!array_key_exists($name, $this->templateMap)) {
                throw new \LogicException(sprintf(
                    'Cannot generate template name for notification with name "%s"!',
                    $name
                ));
            }

            $template = $this->templateMap[$name];
        }

        $event->setTemplateSource($template);

        $enabledChannels = array_filter(
            $this->channels,
            function (string $name) use ($channels): bool {
                return in_array($name, $channels, true);
            },
            ARRAY_FILTER_USE_KEY
        );

        array_walk(
            $enabledChannels,
            function (NotificationChannelInterface $channel) use ($event) {
                $channel->publish($event);
            }
        );
    }
}
