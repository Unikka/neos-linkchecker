<?php

namespace Unikka\LinkChecker\Reporter;

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
use Neos\Flow\Cli\ConsoleOutput;
use Neos\Flow\Persistence\Exception\IllegalObjectTypeException;
use Unikka\LinkChecker\Domain\Model\ResultItem;
use Unikka\LinkChecker\Domain\Repository\ResultItemRepository;
use Spatie\Crawler\CrawlObserver;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Class BaseReporter
 * @package Unikka\LinkChecker\Reporter
 */
abstract class BaseReporter extends CrawlObserver
{
    /**
     * @Flow\Inject
     * @var ResultItemRepository
     */
    protected $resultItemRepository;

    /**
     * @var ConsoleOutput
     */
    protected $output;

    /**
     * @var array
     * @Flow\InjectConfiguration(path="excludeStatusCodes")
     */
    protected $excludeStatusCodes;

    /**
     * @var array
     */
    protected $resultItemsGroupedByStatusCode = [];

    /**
     * @return array
     */
    public function getResultItemsGroupedByStatusCode(): array
    {
        return $this->resultItemsGroupedByStatusCode;
    }

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
    protected function outputLine(string $text = '', array $arguments = []): void
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
        if (!$this->isExcludedStatusCode($statusCode)) {
            $this->addCrawlingResultToStore($url, $foundOnUrl, $statusCode);
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
    ): int
    {
        $statusCode = (int)$requestException->getCode();
        if (!$this->isExcludedStatusCode($statusCode)) {
            $this->addCrawlingResultToStore($url, $foundOnUrl, $statusCode);
        }

        return $statusCode;
    }

    /**
     * We collect the crawling results in the class variable urlsGroupedByStatusCode.
     * We store the crawled url, the status code for this url and if a origin url exists also the location where
     * we got the crawling url from.
     *
     * @param UriInterface $crawlingUrl
     * @param UriInterface $originUrl
     * @param int $statusCode
     * @return void
     */
    protected function addCrawlingResultToStore($crawlingUrl, $originUrl, $statusCode): void
    {
        $cliMessage = "Checked {$crawlingUrl} from {$originUrl} with status {$statusCode}";
        if ($originUrl === null) {
            $cliMessage = "Checked {$crawlingUrl} with status {$statusCode}";
        }

        $this->outputLine($cliMessage);
        $linkCheckItem = new ResultItem($crawlingUrl, $originUrl, $statusCode);

        try {
            $this->resultItemRepository->addOrUpdate($linkCheckItem);
        } catch (IllegalObjectTypeException $e) {
            $this->outputLine("Could not persist entry for the url {$crawlingUrl}");
        }

        $this->resultItemsGroupedByStatusCode[$statusCode][] = $linkCheckItem;
    }

    /**
     * Determine if the status code concerns a successful or
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
        return collect($this->resultItemsGroupedByStatusCode)->keys()->filter(function ($statusCode) {
                return !$this->isSuccessOrRedirect($statusCode);
            })->count() > 0;
    }

    /**
     * Determine if the status code should be excluded'
     * from the reporter.
     *
     * @param int|string $statusCode
     *
     * @return bool
     */
    protected function isExcludedStatusCode($statusCode): bool
    {
        $excludedStatusCodes = \is_array($this->excludeStatusCodes) ? $this->excludeStatusCodes : [];
        return \in_array($statusCode, $excludedStatusCodes, true);
    }
}
