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
    public function indexAction($statusCode = 404): void
    {
        $resultItems = $this->resultItemRepository->findByStatusCode($statusCode)->toArray();
        $existingStatusCodes = [200, 301, 404, 500];
        $this->view->assignMultiple([
            'resultItems' => $resultItems,
            'statusCodes' => $existingStatusCodes,
            'activeStatusCode' => $statusCode
        ]);
    }
}
