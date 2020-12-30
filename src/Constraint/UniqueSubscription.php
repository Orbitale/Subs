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

namespace Orbitale\Subs\Constraint;

use Orbitale\Subs\Validator\UniqueSubscriptionValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class UniqueSubscription extends Constraint
{
    public $message = 'subscriptions.similar_exists';

    public function getTargets()
    {
        return [self::CLASS_CONSTRAINT];
    }

    public function validatedBy()
    {
        return UniqueSubscriptionValidator::class;
    }
}
