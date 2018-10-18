<?php

namespace Noerdisch\LinkChecker\Command;

/*
 * This file is part of the Noerdisch.LinkChecker package.
 *
 * (c) Noerdisch - Digital Solutions www.noerdisch.com
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use GuzzleHttp\RequestOptions;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Noerdisch\LinkChecker\Profile\CheckAllLinks;
use Noerdisch\LinkChecker\Reporter\LogBrokenLinks;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlObserver;

/**
 * Class CheckLinksCommandController
 * @package Noerdisch\LinkChecker\Command
 */
class CheckLinksCommandController extends CommandController
{
    /**
     * @Flow\InjectConfiguration(package="Noerdisch.LinkChecker")
     * @var array
     */
    protected $settings;

    /**
     *
     * @param string $url
     * @param int $concurrency
     */
    public function crawlCommand($url, $concurrency)
    {
        $crawlProfile = new CheckAllLinks();
        $clientOptions = [
            RequestOptions::TIMEOUT => 100,
            RequestOptions::ALLOW_REDIRECTS => false,
        ];

        $crawler = Crawler::create($clientOptions)
            ->setConcurrency($this->getConcurrency($concurrency))
            ->setCrawlObserver(new LogBrokenLinks())
            ->setCrawlProfile($crawlProfile)
            ->ignoreRobots();

        try {
            $crawlingUrl = $this->getCrawlingUrl($url);
            $this->outputLine("Start scanning {$url}");
            $this->outputLine('');
            $crawler->startCrawling($crawlingUrl);
        } catch(\InvalidArgumentException $exception) {
            $this->outputLine('ERROR:  ' . $exception->getMessage());
        }

    }

    /**
     * Determine the url to be crawled.
     *
     * @param string $url
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getCrawlingUrl($url): string
    {
        $crawlingUrl = trim($url) !== '' ? $url : '';
        if (isset($this->settings['url']) && $this->settings['url'] !== '') {
            $crawlingUrl = trim($this->settings['url']);
        }

        if ($crawlingUrl === '') {
            throw new \InvalidArgumentException('Could not determine which url to be crawled.');
        }

        return $crawlingUrl;
    }

    /**
     * Returns concurrency. If not found, simply returns a default value like
     * 10 (default).
     *
     * @param int $concurrency
     *
     * @return int
     */
    protected function getConcurrency($concurrency): int
    {
        if ((int)$concurrency >= 0) {
            return (int)$concurrency;
        }

        if (isset($this->settings['concurrency']) && (int)$this->settings['concurrency'] >= 0) {
            return (int)$this->settings['concurrency'];
        }

        return 10;
    }
}
