<?php

declare(strict_types=1);

/*
 * This file is part of the Orbitale Subs package.
 *
 * (c) Alexandre Rock Ancelet <alex@orbitale.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Subscription;

final class SubscriptionType
{
    public const ESTEREN_MAPS = 'subscription.esteren_maps';

    public const TYPES = [
        self::ESTEREN_MAPS => self::ESTEREN_MAPS,
    ];

    public const TYPES_PERMISSIONS = [
        self::ESTEREN_MAPS => [
            'SUBSCRIBED_TO_MAPS',
        ],
    ];
}
