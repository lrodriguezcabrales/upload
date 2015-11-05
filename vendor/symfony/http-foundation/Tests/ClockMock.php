<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation;

<<<<<<< HEAD
function time()
=======
function time($asFloat = false)
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
{
    return Tests\time();
}

namespace Symfony\Component\HttpFoundation\Tests;

<<<<<<< HEAD
function with_clock_mock($enable = null)
{
    static $enabled;

    if (null === $enable) {
        return $enabled;
    }

    $enabled = $enable;
}

function time()
{
    if (!with_clock_mock()) {
        return \time();
    }

=======
function time()
{
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
    return $_SERVER['REQUEST_TIME'];
}
