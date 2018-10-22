<?php

namespace Noerdisch\LinkChecker\Service;

/*
 * This file is part of the Noerdisch.LinkChecker package.
 *
 * (c) Noerdisch - Digital Solutions www.noerdisch.com
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

/**
 * Class AbstractNotificationService
 * @package Noerdisch\LinkChecker\Service
 */
interface NotificationServiceInterface
{
    /**
     * @param string $subject
     * @param array $variables
     * @return void
     */
    public function sendNotification($subject, array $variables = []): void;
}
