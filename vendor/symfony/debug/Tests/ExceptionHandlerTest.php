<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug\Tests;

use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Debug\Exception\OutOfMemoryException;
<<<<<<< HEAD
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

require_once __DIR__.'/HeaderMock.php';

class ExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        testHeader();
    }

    protected function tearDown()
    {
        testHeader();
    }

    public function testDebug()
    {
        $handler = new ExceptionHandler(false);

        ob_start();
        $handler->sendPhpResponse(new \RuntimeException('Foo'));
        $response = ob_get_clean();

        $this->assertContains('<h1>Whoops, looks like something went wrong.</h1>', $response);
        $this->assertNotContains('<h2 class="block_exception clear_fix">', $response);

        $handler = new ExceptionHandler(true);

        ob_start();
        $handler->sendPhpResponse(new \RuntimeException('Foo'));
        $response = ob_get_clean();

        $this->assertContains('<h1>Whoops, looks like something went wrong.</h1>', $response);
        $this->assertContains('<h2 class="block_exception clear_fix">', $response);
=======
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testDebug()
    {
        $handler = new ExceptionHandler(false);
        $response = $handler->createResponse(new \RuntimeException('Foo'));

        $this->assertContains('<h1>Whoops, looks like something went wrong.</h1>', $response->getContent());
        $this->assertNotContains('<h2 class="block_exception clear_fix">', $response->getContent());

        $handler = new ExceptionHandler(true);
        $response = $handler->createResponse(new \RuntimeException('Foo'));

        $this->assertContains('<h1>Whoops, looks like something went wrong.</h1>', $response->getContent());
        $this->assertContains('<h2 class="block_exception clear_fix">', $response->getContent());
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
    }

    public function testStatusCode()
    {
<<<<<<< HEAD
        $handler = new ExceptionHandler(false, 'iso8859-1');

        ob_start();
        $handler->sendPhpResponse(new NotFoundHttpException('Foo'));
        $response = ob_get_clean();

        $this->assertContains('Sorry, the page you are looking for could not be found.', $response);

        $expectedHeaders = array(
            array('HTTP/1.0 404', true, null),
            array('Content-Type: text/html; charset=iso8859-1', true, null),
        );

        $this->assertSame($expectedHeaders, testHeader());
=======
        $handler = new ExceptionHandler(false);

        $response = $handler->createResponse(new \RuntimeException('Foo'));
        $this->assertEquals('500', $response->getStatusCode());
        $this->assertContains('Whoops, looks like something went wrong.', $response->getContent());

        $response = $handler->createResponse(new NotFoundHttpException('Foo'));
        $this->assertEquals('404', $response->getStatusCode());
        $this->assertContains('Sorry, the page you are looking for could not be found.', $response->getContent());
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
    }

    public function testHeaders()
    {
<<<<<<< HEAD
        $handler = new ExceptionHandler(false, 'iso8859-1');

        ob_start();
        $handler->sendPhpResponse(new MethodNotAllowedHttpException(array('POST')));
        $response = ob_get_clean();

        $expectedHeaders = array(
            array('HTTP/1.0 405', true, null),
            array('Allow: POST', false, null),
            array('Content-Type: text/html; charset=iso8859-1', true, null),
        );

        $this->assertSame($expectedHeaders, testHeader());
=======
        $handler = new ExceptionHandler(false);

        $response = $handler->createResponse(new MethodNotAllowedHttpException(array('POST')));
        $this->assertEquals('405', $response->getStatusCode());
        $this->assertEquals('POST', $response->headers->get('Allow'));
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
    }

    public function testNestedExceptions()
    {
        $handler = new ExceptionHandler(true);
<<<<<<< HEAD
        ob_start();
        $handler->sendPhpResponse(new \RuntimeException('Foo', 0, new \RuntimeException('Bar')));
        $response = ob_get_clean();

        $this->assertStringMatchesFormat('%A<span class="exception_message">Foo</span>%A<span class="exception_message">Bar</span>%A', $response);
=======
        $response = $handler->createResponse(new \RuntimeException('Foo', 0, new \RuntimeException('Bar')));
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
    }

    public function testHandle()
    {
        $exception = new \Exception('foo');

<<<<<<< HEAD
        $handler = $this->getMock('Symfony\Component\Debug\ExceptionHandler', array('sendPhpResponse'));
        $handler
            ->expects($this->exactly(2))
            ->method('sendPhpResponse');
=======
        if (class_exists('Symfony\Component\HttpFoundation\Response')) {
            $handler = $this->getMock('Symfony\Component\Debug\ExceptionHandler', array('createResponse'));
            $handler
                ->expects($this->exactly(2))
                ->method('createResponse')
                ->will($this->returnValue(new Response()));
        } else {
            $handler = $this->getMock('Symfony\Component\Debug\ExceptionHandler', array('sendPhpResponse'));
            $handler
                ->expects($this->exactly(2))
                ->method('sendPhpResponse');
        }
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a

        $handler->handle($exception);

        $that = $this;
        $handler->setHandler(function ($e) use ($exception, $that) {
            $that->assertSame($exception, $e);
        });

        $handler->handle($exception);
    }

    public function testHandleOutOfMemoryException()
    {
        $exception = new OutOfMemoryException('foo', 0, E_ERROR, __FILE__, __LINE__);

<<<<<<< HEAD
        $handler = $this->getMock('Symfony\Component\Debug\ExceptionHandler', array('sendPhpResponse'));
        $handler
            ->expects($this->once())
            ->method('sendPhpResponse');
=======
        if (class_exists('Symfony\Component\HttpFoundation\Response')) {
            $handler = $this->getMock('Symfony\Component\Debug\ExceptionHandler', array('createResponse'));
            $handler
                ->expects($this->once())
                ->method('createResponse')
                ->will($this->returnValue(new Response()));
        } else {
            $handler = $this->getMock('Symfony\Component\Debug\ExceptionHandler', array('sendPhpResponse'));
            $handler
                ->expects($this->once())
                ->method('sendPhpResponse');
        }
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a

        $that = $this;
        $handler->setHandler(function ($e) use ($that) {
            $that->fail('OutOfMemoryException should bypass the handler');
        });

        $handler->handle($exception);
    }
}
