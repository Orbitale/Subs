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

namespace Tests\Orbitale\Subs\Validator;

use Orbitale\Subs\Constraint\UniqueSubscription;
use Orbitale\Subs\Entity\Subscription;
use Orbitale\Subs\Repository\SubscriptionRepository;
use Orbitale\Subs\Validator\UniqueSubscriptionValidator;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Tests\Orbitale\Subs\ExecutionContextStubFactory;

class UniqueSubscriptionValidatorTest extends TestCase
{
    /**
     * @group unit
     */
    public function test constraint must be UniqueSubscription instance(): void
    {
        $subscription = new class() extends Subscription {
        };

        $constraint = new class() extends Constraint {
        };

        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Orbitale\Subs\\Constraint\\UniqueSubscription", "Symfony\Component\Validator\Constraint@anonymous" given');

        $this->getValidator()->validate($subscription, $constraint);
    }

    /**
     * @group unit
     */
    public function test subject must be Subscription instance(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Orbitale\Subs\Entity\Subscription", "stdClass" given');

        $this->getValidator()->validate(new stdClass(), new UniqueSubscription());
    }

    /**
     * Legacy because extending the execution context is supposed to be internal.
     * When it fails, we'll know it.
     *
     * @group legacy
     * @group unit
     */
    public function test existing subscription returns violation(): void
    {
        $subscription = new class() extends Subscription {
        };

        $repo = new class() extends SubscriptionRepository {
            public array $executions = [];

            public function __construct()
            {
            }

            public function hasSimilarActiveSubscriptions(Subscription $subscription): bool
            {
                $this->executions[] = $subscription;

                return true;
            }
        };

        $context = new class() extends ExecutionContextStubFactory {
            public array $executions = [];

            public function addViolation(string $message, array $params = []): void
            {
                $this->executions[] = $message;
            }
        };

        $validator = $this->getValidator($repo);
        $validator->initialize($context);

        $validator->validate($subscription, new UniqueSubscription());

        static::assertCount(1, $repo->executions);
        static::assertSame($subscription, $repo->executions[0]);
        static::assertCount(1, $context->executions);
        static::assertSame('subscriptions.similar_exists', $context->executions[0]);
    }

    /**
     * @group unit
     */
    public function test existing subscription and custom constraint message returns violation(): void
    {
        $subscription = new class() extends Subscription {
        };

        $repo = $this->createMock(SubscriptionRepository::class);
        $repo->expects(static::once())
            ->method('hasSimilarActiveSubscriptions')
            ->with($subscription)
            ->willReturn(true)
        ;

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(static::once())
            ->method('addViolation')
            ->with('custom_message')
        ;

        $validator = $this->getValidator($repo);
        $validator->initialize($context);

        $validator->validate($subscription, new UniqueSubscription(['message' => 'custom_message']));
    }

    private function getValidator(SubscriptionRepository $repo = null): UniqueSubscriptionValidator
    {
        return new UniqueSubscriptionValidator($repo ?: $this->createMock(SubscriptionRepository::class));
    }
}
