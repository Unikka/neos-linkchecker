<?php

namespace Noerdisch\LinkChecker\Controller;

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
use Neos\Neos\Controller\Module\AbstractModuleController;
use Noerdisch\LinkChecker\Domain\Repository\ResultItemRepository;

/**
 * Class BackendController
 * @package Noerdisch\LinkChecker\Controller
 */
class BackendController extends AbstractModuleController
{
    public const DEFAULT_STATUS_CODE = 404;

    /**
     * @Flow\Inject
     * @var ResultItemRepository
     */
    protected $resultItemRepository;

    /**
     * Index action of backend module
     * Lists the result items
     *
     * @param int $statusCode
     *
     * @return void
     */
    public function indexAction($statusCode = 0): void
    {
        $resultItems = $this->resultItemRepository->findByStatusCode($statusCode)->toArray();
        $this->view->assignMultiple([
            'resultItems' => $resultItems,
            'statusCodes' => $this->getStatusCodes($statusCode),
            'activeStatusCode' => $statusCode < 100 ? self::DEFAULT_STATUS_CODE : $statusCode
        ]);
    }

    /**
     * Return the existing status codes as array. If we have no initial code we also add the default value.
     * This is 404 for error pages.
     *
     * @param int $statusCode
     * @return array
     */
    protected function getStatusCodes($statusCode) {
        if ((int) $statusCode < 100) {
            $statusCode = self::DEFAULT_STATUS_CODE;
        }

        $existingStatusCodes = $this->resultItemRepository->findAllStatusCodes();
        $existingStatusCodes[] = $statusCode;
        return array_unique($existingStatusCodes);
    }
}
