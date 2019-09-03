<?php

namespace Unikka\LinkChecker\Service;

/*
 * This file is part of the Unikka LinkChecker package.
 *
 * (c) unikka
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

/**
 * Class AbstractNotificationService
 * @package Unikka\LinkChecker\Service
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
