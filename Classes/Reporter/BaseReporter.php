<?php

namespace Noerdisch\LinkChecker\Reporter;

/*
 * This file is part of the Noerdisch.LinkChecker package.
 *
 * (c) Noerdisch - Digital Solutions www.noerdisch.com
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Cli\ConsoleOutput;
use Spatie\Crawler\CrawlObserver;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

abstract class BaseReporter extends CrawlObserver
{
    const UNRESPONSIVE_HOST = 'Host did not respond';

    /**
     * @var ConsoleOutput
     */
    protected $output;

    /**
     * @var array
     */
    protected $urlsGroupedByStatusCode = [];

    public function __construct()
    {
        $this->output = new ConsoleOutput();
    }

    /**
     * Outputs specified text to the console window and appends a line break
     *
     * @param string $text Text to output
     * @param array $arguments Optional arguments to use for sprintf
     * @return void
     * @see output()
     * @see outputLines()
     */
    protected function outputLine(string $text = '', array $arguments = [])
    {
        $this->output->outputLine($text, $arguments);
    }

    /**
     * Called when the crawler has crawled the given url successfully.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param null|\Psr\Http\Message\UriInterface $foundOnUrl
     *
     * @return int|string
     */
    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null
    )
    {
        $statusCode = (int)$response->getStatusCode();
        $this->outputLine("{$url}  ||  {$statusCode}");

        if (!$this->isExcludedStatusCode($statusCode)) {
            $this->urlsGroupedByStatusCode[$statusCode][] = $url;
        }

        return $statusCode;
    }

    /**
     * Called when the crawler had a problem crawling the given url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \GuzzleHttp\Exception\RequestException $requestException
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     *
     * @return int
     */
    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    )
    {
        $statusCode = (int)$requestException->getCode();

        if (!$this->isExcludedStatusCode($statusCode)) {
            $this->urlsGroupedByStatusCode[$statusCode][] = $url;
        }

        return $statusCode;
    }

    /**
     * Determine if the statuscode concerns a successful or
     * redirect response.
     *
     * @param int|string $statusCode
     *
     * @return bool
     */
    protected function isSuccessOrRedirect($statusCode): bool
    {
        return \in_array((int)$statusCode, [200, 201, 301], true);
    }

    /**
     * Determine if the crawler saw some bad urls.
     */
    protected function crawledBadUrls(): bool
    {
        return collect($this->urlsGroupedByStatusCode)->keys()->filter(function ($statusCode) {
                return !$this->isSuccessOrRedirect($statusCode);
            })->count() > 0;
    }

    /**
     * Determine if the statuscode should be excluded'
     * from the reporter.
     *
     * @todo make the exclude configurable
     * @param int|string $statusCode
     *
     * @return bool
     */
    protected function isExcludedStatusCode($statusCode): bool
    {
        $excludedStatusCodes = [];
        return \in_array($statusCode, $excludedStatusCodes, true);
    }
}
