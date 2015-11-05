<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests\File\MimeType;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\FileBinaryMimeTypeGuesser;

<<<<<<< HEAD
/**
 * @requires extension fileinfo
 */
=======
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
class MimeTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $path;

    public function testGuessImageWithoutExtension()
    {
<<<<<<< HEAD
        $this->assertEquals('image/gif', MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/test'));
=======
        if (extension_loaded('fileinfo')) {
            $this->assertEquals('image/gif', MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/test'));
        } else {
            $this->assertNull(MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/test'));
        }
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
    }

    public function testGuessImageWithDirectory()
    {
        $this->setExpectedException('Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException');

        MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/directory');
    }

    public function testGuessImageWithFileBinaryMimeTypeGuesser()
    {
        $guesser = MimeTypeGuesser::getInstance();
        $guesser->register(new FileBinaryMimeTypeGuesser());
<<<<<<< HEAD
        $this->assertEquals('image/gif', MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/test'));
=======
        if (extension_loaded('fileinfo')) {
            $this->assertEquals('image/gif', MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/test'));
        } else {
            $this->assertNull(MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/test'));
        }
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
    }

    public function testGuessImageWithKnownExtension()
    {
<<<<<<< HEAD
        $this->assertEquals('image/gif', MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/test.gif'));
=======
        if (extension_loaded('fileinfo')) {
            $this->assertEquals('image/gif', MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/test.gif'));
        } else {
            $this->assertNull(MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/test.gif'));
        }
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
    }

    public function testGuessFileWithUnknownExtension()
    {
<<<<<<< HEAD
        $this->assertEquals('application/octet-stream', MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/.unknownextension'));
=======
        if (extension_loaded('fileinfo')) {
            $this->assertEquals('application/octet-stream', MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/.unknownextension'));
        } else {
            $this->assertNull(MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/.unknownextension'));
        }
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
    }

    public function testGuessWithIncorrectPath()
    {
        $this->setExpectedException('Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException');
        MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/not_here');
    }

    public function testGuessWithNonReadablePath()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('Can not verify chmod operations on Windows');
        }

<<<<<<< HEAD
        if (!getenv('USER') || 'root' === getenv('USER')) {
=======
        if ('root' === get_current_user()) {
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
            $this->markTestSkipped('This test will fail if run under superuser');
        }

        $path = __DIR__.'/../Fixtures/to_delete';
        touch($path);
        @chmod($path, 0333);

<<<<<<< HEAD
        if (substr(sprintf('%o', fileperms($path)), -4) == '0333') {
=======
        if (get_current_user() != 'root' && substr(sprintf('%o', fileperms($path)), -4) == '0333') {
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
            $this->setExpectedException('Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException');
            MimeTypeGuesser::getInstance()->guess($path);
        } else {
            $this->markTestSkipped('Can not verify chmod operations, change of file permissions failed');
        }
    }

    public static function tearDownAfterClass()
    {
        $path = __DIR__.'/../Fixtures/to_delete';
        if (file_exists($path)) {
            @chmod($path, 0666);
            @unlink($path);
        }
    }
}
