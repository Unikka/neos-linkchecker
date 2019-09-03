<?php

namespace Unikka\LinkChecker\Service;

/*
 * This file is part of the Unikka LinkChecker package.
 *
 * (c) unikka
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * @Flow\Scope("singleton")
 */
class UriService
{
    /**
     * Generates a string for a uri that implements the psr 7 uri interface.
     *
     * @param UriInterface $uri
     *
     * @return string
     */
    public static function uriToString($uri): string
    {
        if ($uri === null) {
            return '';
        }

        return Uri::composeComponents(
            $uri->getScheme(),
            $uri->getAuthority(),
            $uri->getPath(),
            $uri->getQuery(),
            $uri->getFragment()
        );
    }
}
