<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Silex\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\EventListener\LocaleListener as BaseLocaleListener;
<<<<<<< HEAD
use Symfony\Component\HttpKernel\Kernel;
=======
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RequestContextAwareInterface;
use Silex\Application;

/**
 * Initializes the locale based on the current request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class LocaleListener extends BaseLocaleListener
{
    protected $app;

    public function __construct(Application $app, RequestContextAwareInterface $router = null, RequestStack $requestStack = null)
    {
<<<<<<< HEAD
        if (Kernel::VERSION_ID >= 20800) {
            parent::__construct($requestStack, $app['locale'], $router);
        } else {
            parent::__construct($app['locale'], $router, $requestStack);
        }
=======
        parent::__construct($app['locale'], $router, $requestStack);
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a

        $this->app = $app;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        parent::onKernelRequest($event);

        $this->app['locale'] = $event->getRequest()->getLocale();
    }
}
