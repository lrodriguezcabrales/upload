<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher;

/**
 * The EventDispatcherInterface is the central point of Symfony's event listener system.
 *
 * Listeners are registered on the manager and events are dispatched through the
 * manager.
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author Jonathan Wage <jonwage@gmail.com>
 * @author Roman Borschel <roman@code-factory.org>
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Jordan Alliot <jordan.alliot@gmail.com>
 *
 * @api
 */
class EventDispatcher implements EventDispatcherInterface
{
    private $listeners = array();
    private $sorted = array();

    /**
<<<<<<< HEAD
     * {@inheritdoc}
=======
     * @see EventDispatcherInterface::dispatch()
     *
     * @api
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
     */
    public function dispatch($eventName, Event $event = null)
    {
        if (null === $event) {
            $event = new Event();
        }

        $event->setDispatcher($this);
        $event->setName($eventName);

<<<<<<< HEAD
        if ($listeners = $this->getListeners($eventName)) {
            $this->doDispatch($listeners, $eventName, $event);
        }

=======
        if (!isset($this->listeners[$eventName])) {
            return $event;
        }

        $this->doDispatch($this->getListeners($eventName), $eventName, $event);

>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
        return $event;
    }

    /**
<<<<<<< HEAD
     * {@inheritdoc}
=======
     * @see EventDispatcherInterface::getListeners()
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
     */
    public function getListeners($eventName = null)
    {
        if (null !== $eventName) {
<<<<<<< HEAD
            if (!isset($this->listeners[$eventName])) {
                return array();
            }

=======
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }

            return $this->sorted[$eventName];
        }

        foreach ($this->listeners as $eventName => $eventListeners) {
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }
        }

        return array_filter($this->sorted);
    }

    /**
<<<<<<< HEAD
     * {@inheritdoc}
=======
     * @see EventDispatcherInterface::hasListeners()
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
     */
    public function hasListeners($eventName = null)
    {
        return (bool) count($this->getListeners($eventName));
    }

    /**
<<<<<<< HEAD
     * {@inheritdoc}
=======
     * @see EventDispatcherInterface::addListener()
     *
     * @api
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sorted[$eventName]);
    }

    /**
<<<<<<< HEAD
     * {@inheritdoc}
=======
     * @see EventDispatcherInterface::removeListener()
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
     */
    public function removeListener($eventName, $listener)
    {
        if (!isset($this->listeners[$eventName])) {
            return;
        }

        foreach ($this->listeners[$eventName] as $priority => $listeners) {
            if (false !== ($key = array_search($listener, $listeners, true))) {
                unset($this->listeners[$eventName][$priority][$key], $this->sorted[$eventName]);
            }
        }
    }

    /**
<<<<<<< HEAD
     * {@inheritdoc}
=======
     * @see EventDispatcherInterface::addSubscriber()
     *
     * @api
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) {
                $this->addListener($eventName, array($subscriber, $params));
            } elseif (is_string($params[0])) {
                $this->addListener($eventName, array($subscriber, $params[0]), isset($params[1]) ? $params[1] : 0);
            } else {
                foreach ($params as $listener) {
                    $this->addListener($eventName, array($subscriber, $listener[0]), isset($listener[1]) ? $listener[1] : 0);
                }
            }
        }
    }

    /**
<<<<<<< HEAD
     * {@inheritdoc}
=======
     * @see EventDispatcherInterface::removeSubscriber()
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_array($params) && is_array($params[0])) {
                foreach ($params as $listener) {
                    $this->removeListener($eventName, array($subscriber, $listener[0]));
                }
            } else {
                $this->removeListener($eventName, array($subscriber, is_string($params) ? $params : $params[0]));
            }
        }
    }

    /**
     * Triggers the listeners of an event.
     *
     * This method can be overridden to add functionality that is executed
     * for each listener.
     *
     * @param callable[] $listeners The event listeners.
     * @param string     $eventName The name of the event to dispatch.
     * @param Event      $event     The event object to pass to the event handlers/listeners.
     */
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        foreach ($listeners as $listener) {
            call_user_func($listener, $event, $eventName, $this);
            if ($event->isPropagationStopped()) {
                break;
            }
        }
    }

    /**
     * Sorts the internal list of listeners for the given event by priority.
     *
     * @param string $eventName The name of the event.
     */
    private function sortListeners($eventName)
    {
        $this->sorted[$eventName] = array();

<<<<<<< HEAD
        krsort($this->listeners[$eventName]);
        $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
=======
        if (isset($this->listeners[$eventName])) {
            krsort($this->listeners[$eventName]);
            $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
        }
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
    }
}
