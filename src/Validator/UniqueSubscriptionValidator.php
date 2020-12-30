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

namespace Orbitale\Subs\Validator;

use Orbitale\Subs\Constraint\UniqueSubscription;
use Orbitale\Subs\Entity\Subscription;
use Orbitale\Subs\Repository\SubscriptionRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueSubscriptionValidator extends ConstraintValidator
{
    private $repository;

    public function __construct(SubscriptionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function validate($subscription, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueSubscription) {
            throw new UnexpectedTypeException($constraint, UniqueSubscription::class);
        }

        if (!$subscription instanceof Subscription) {
            throw new UnexpectedTypeException($subscription, Subscription::class);
        }

        if ($this->repository->hasSimilarActiveSubscriptions($subscription)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
