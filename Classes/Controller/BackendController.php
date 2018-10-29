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
    /**
     * @var int
     * @Flow\InjectConfiguration(path="notifications.minimumStatusCode")
     */
    protected $minimumStatusCode;

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
        $statusCodes = $this->getStatusCodes();
        $statusCodePreSelect = $this->getStatusCodePreSelect($statusCode, $statusCodes);
        $resultItems = $statusCodePreSelect > 0 ? $this->resultItemRepository->findByStatusCode($statusCodePreSelect)->toArray() : [];

        $this->view->assignMultiple([
            'resultItems' => $resultItems,
            'statusCodes' => $statusCodes,
            'activeStatusCode' => $statusCodePreSelect
        ]);
    }

    /**
     * If we have a minimal status code configured and the given status code is invalid, then we try to select the
     * first possible status code that matches the configuration for "notifications.minimumStatusCode".
     *
     * @param int $statusCode
     * @param array $allStatusCodes
     * @return int
     */
    protected function getStatusCodePreSelect($statusCode, array $allStatusCodes) {
        $minimumStatusCode = (int)$this->minimumStatusCode;
        if ($statusCode > 0 || $minimumStatusCode <= 0) {
            return (int) $statusCode;
        }

        $preSelect = 0;
        foreach ($allStatusCodes as $statusCodeNumber) {
            if ($statusCodeNumber <= $minimumStatusCode) {
                continue;
            }

            $preSelect = $statusCodeNumber;
            break;
        }

        return $preSelect;
    }

    /**
     * Return the existing status codes as array. If we have no initial code we also add the default value.
     * This is 404 for error pages.
     *
     * @param int $statusCode
     * @return array
     */
    protected function getStatusCodes(): array
    {
        $existingStatusCodes = $this->resultItemRepository->findAllStatusCodes();
        sort($existingStatusCodes);
        return $existingStatusCodes;
    }
}
