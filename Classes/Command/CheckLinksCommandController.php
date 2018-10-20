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
    public function crawlCommand($url = '', $concurrency = 10): void
    {
        $crawlProfile = new CheckAllLinks();
        $clientOptions = $this->getClientOptions();

        $crawler = Crawler::create($clientOptions)
            ->setConcurrency($this->getConcurrency($concurrency))
            ->setCrawlObserver(new LogBrokenLinks())
            ->setCrawlProfile($crawlProfile);

        if ($this->shouldIgnoreRobots()) {
            $crawler->ignoreRobots();
        }

        try {
            $crawlingUrl = $this->getCrawlingUrl($url);
            $this->outputLine("Start scanning {$crawlingUrl}");
            $this->outputLine('');
            $crawler->startCrawling($crawlingUrl);
        } catch (\InvalidArgumentException $exception) {
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
        if ($crawlingUrl === '' && isset($this->settings['url']) && $this->settings['url'] !== '') {
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

    /**
     * Returns true by default and can be changed by the setting Noerdisch.LinkChecker.ignoreRobots
     *
     * @return bool
     */
    protected function shouldIgnoreRobots(): bool
    {
        return isset($this->settings['ignoreRobots']) ? (bool)$this->settings['ignoreRobots'] : true;
    }

    /**
     * Get client options for the guzzle client from the settings. If no settings are configured we just set
     * timeout and allow_redirect.
     *
     * @return array
     */
    protected function getClientOptions()
    {
        $clientOptions = [
            RequestOptions::TIMEOUT => 100,
            RequestOptions::ALLOW_REDIRECTS => false,
        ];

        $optionsSettings = $this->settings['clientOptions'] ?? [];
        if (isset($optionsSettings['cookies']) &&\is_bool($optionsSettings['cookies'])) {
            $clientOptions[RequestOptions::COOKIES] = $optionsSettings['cookies'];
        }

        if (isset($optionsSettings['connectionTimeout']) && \is_numeric($optionsSettings['connectionTimeout'])) {
            $clientOptions[RequestOptions::CONNECT_TIMEOUT] = $optionsSettings['connectionTimeout'];
        }

        if (isset($optionsSettings['timeout']) && \is_numeric($optionsSettings['timeout'])) {
            $clientOptions[RequestOptions::TIMEOUT] = $optionsSettings['timeout'];
        }

        if (isset($optionsSettings['allowRedirects']) && \is_bool($optionsSettings['allowRedirects'])) {
            $clientOptions[RequestOptions::ALLOW_REDIRECTS] = $optionsSettings['allowRedirects'];
        }

        if (
            isset($optionsSettings['auth']) && \is_array($optionsSettings['auth']) &&
            \count($optionsSettings['auth']) > 1
        ) {
            $clientOptions[RequestOptions::AUTH] = $optionsSettings['auth'];
        }

        return $clientOptions;
    }
}
