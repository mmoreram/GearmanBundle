<?php

/**
 * Gearman Bundle for Symfony2
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Dominic Luechinger <dol+symfony@cyon.ch>
 */

namespace Mmoreram\GearmanBundle\EventListener;

use Mmoreram\GearmanBundle\GearmanEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Sends emails for the memory spool.
 *
 * Emails are sent on the gearman.client.callback.complete event.
 *
 * @author Dominic Luechinger <dol+symfony@cyon.ch>
 */
class SwiftmailerEmailSenderListener implements EventSubscriberInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Set container
     *
     * @param ContainerInterface $container Container
     *
     * @return GearmanExecute self Object
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    public function onComplete()
    {
        if (!$this->container->has('swiftmailer.email_sender.listener')) {
            return;
        }
        $this->container->get('swiftmailer.email_sender.listener')->onTerminate();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            GearmanEvents::GEARMAN_CLIENT_CALLBACK_COMPLETE => 'onComplete',
        );
    }
}
