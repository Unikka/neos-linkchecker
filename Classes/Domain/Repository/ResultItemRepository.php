<?php

namespace Noerdisch\LinkChecker\Domain\Repository;

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
use Neos\Flow\Persistence\Doctrine\Repository;
use Noerdisch\LinkChecker\Domain\Model\ResultItem;

/**
 * @Flow\Scope("singleton")
 */
class ResultItemRepository extends Repository
{
    /**
     * Adds or updates the given result item to the database.
     * Existing items should have the same url and origin url.
     *
     * @param ResultItem $object
     * @return void
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function addOrUpdate($object): void
    {
        $existingResultItem = $this->findExistingItems($object->getUrl(), $object->getOriginUrl());
        if ($existingResultItem instanceof ResultItem) {
            $existingResultItem->setCheckedAt($object->getCheckedAt());
            $existingResultItem->setStatusCode($object->getStatusCode());
            $this->update($existingResultItem);
        } else {
            $this->add($object);
        }
    }

    /**
     * Returns ResultItem if the combination of url and origin url already exists.
     *
     * @param string $url
     * @param string $originUrl
     * @return object
     */
    public function findExistingItems($url, $originUrl)
    {
        $constraints = [];
        $query = $this->createQuery();

        $constraints[] = $query->equals('url', trim($url));
        $constraints[] = $query->equals('originUrl', trim($originUrl));
        return $query->matching($query->logicalAnd($constraints))->execute()->getFirst();
    }

    /**
     * Returns array of existing status codes in the result item table.
     *
     * @return array
     */
    public function findAllStatusCodes()
    {
        $dql = "SELECT DISTINCT t.statusCode FROM Noerdisch\LinkChecker\Domain\Model\ResultItem t";
        $statusCodes = $this->createDqlQuery($dql)->getArrayResult();

        $existingStatusCodes = [];
        foreach ($statusCodes as $statusCode) {
            if (!isset($statusCode['statusCode'])) {
                continue;
            }

            $existingStatusCodes[] = (int)$statusCode['statusCode'];
        }
        return $existingStatusCodes;
    }
}
