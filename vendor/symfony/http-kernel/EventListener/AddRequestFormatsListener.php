<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
<<<<<<< HEAD
 * Adds configured formats to each request.
=======
 * Adds configured formats to each request
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
 *
 * @author Gildas Quemener <gildas.quemener@gmail.com>
 */
class AddRequestFormatsListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    protected $formats;

    /**
     * @param array $formats
     */
    public function __construct(array $formats)
    {
        $this->formats = $formats;
    }

    /**
<<<<<<< HEAD
     * Adds request formats.
=======
     * Adds request formats
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        foreach ($this->formats as $format => $mimeTypes) {
            $event->getRequest()->setFormat($format, $mimeTypes);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => 'onKernelRequest');
    }
}
