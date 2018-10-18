<?php

namespace Noerdisch\LinkChecker\Domain\Model;

/*
 * This file is part of the Noerdisch.LinkChecker package.
 *
 * (c) Noerdisch - Digital Solutions www.noerdisch.com
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Noerdisch\LinkChecker\Service\UriService;
use Psr\Http\Message\UriInterface;

/**
 * Model ResultItem
 *
 * @package Noerdisch\LinkChecker\Domain\Model
 * @Flow\Entity
 */
class ResultItem
{
    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $checkedAt;

    /**
     * @var string
     */
    protected $originUrl = '';

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty")
     */
    protected $url = '';

    /**
     * @var int
     */
    protected $statusCode = 0;

    /**
     * ResultItem constructor.
     * @param UriInterface $url
     * @param UriInterface $originUrl
     * @param int $statusCode
     */
    public function __construct($url, $originUrl, $statusCode)
    {
        $this->createdAt = new \DateTime();
        $this->checkedAt = new \DateTime();
        $this->setUrl(UriService::uriToString($url));
        $this->setOriginUrl(UriService::uriToString($originUrl));
        $this->setStatusCode($statusCode);
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCheckedAt(): \DateTime
    {
        return $this->checkedAt;
    }

    /**
     * @param \DateTime $checkedAt
     */
    public function setCheckedAt(\DateTime $checkedAt): void
    {
        $this->checkedAt = $checkedAt;
    }

    /**
     * @return string
     */
    public function getOriginUrl(): string
    {
        return $this->originUrl;
    }

    /**
     * @param string $originUrl
     */
    public function setOriginUrl(string $originUrl): void
    {
        $this->originUrl = $originUrl;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }
}
