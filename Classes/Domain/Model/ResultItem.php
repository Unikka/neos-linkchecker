<?php

namespace Unikka\LinkChecker\Domain\Model;

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
use Unikka\LinkChecker\Service\UriService;
use Psr\Http\Message\UriInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Model ResultItem
 *
 * @package Unikka\LinkChecker\Domain\Model
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
     * @ORM\Column(length=2048)
     */
    protected $originUrl = '';

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty")
     * @ORM\Column(length=2048)
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
