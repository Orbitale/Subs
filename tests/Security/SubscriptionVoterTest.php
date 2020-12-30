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

namespace Tests\Orbitale\Subs\Security;

use Orbitale\Subs\Entity\Subscription;
use Orbitale\Subs\Repository\SubscriptionRepository;
use Orbitale\Subs\Voter\SubscriptionVoter;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SubscriptionVoterTest extends TestCase
{
    public function test smoke valid permissions(): void
    {
        $user = $this->createMock(UserInterface::class);

        $token = $this->createMock(TokenInterface::class);
        $token->expects(static::once())
            ->method('getUser')
            ->willReturn($user)
        ;

        $subscription = $this->createMock(Subscription::class);
        $subscription->expects(static::once())
            ->method('getType')
            ->willReturn('subscription.something')
        ;

        $repo = $this->createMock(SubscriptionRepository::class);
        $repo->expects(static::once())
            ->method('getUserActiveSubscriptions')
            ->with($user)
            ->willReturn([$subscription])
        ;

        $logger = $this->getLogger();

        $result = (new SubscriptionVoter([
            'subscription.something' => ['SUBSCRIBED_TO_SOMETHING'],
        ], $repo, $logger))->vote($token, null, ['SUBSCRIBED_TO_SOMETHING']);

        // Access granted
        static::assertSame(1, $result);

        static::assertSame([], $logger->cleanLogs());
    }

    /**
     * @group unit
     */
    public function test invalid permissions caused by wrong subscription type in database are logged as error(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user->expects(static::exactly(2)) // First call in voter, second in assertions
            ->method('getUsername')
            ->willReturn(1)
        ;

        $token = $this->createMock(TokenInterface::class);
        $token->expects(static::once())
            ->method('getUser')
            ->willReturn($user)
        ;

        $subscription = $this->createMock(Subscription::class);
        $subscription->expects(static::exactly(2)) // First call in voter, second in assertions
            ->method('getId')
            ->willReturn(10)
        ;

        // First call: for permissions check in the const
        // Second call: in the logger
        // Third call: here in the assertions
        $subscription->expects(static::exactly(3))
            ->method('getType')
            ->willReturn('_INVALID_SUBSCRIPTION_TYPE')
        ;

        $repo = $this->createMock(SubscriptionRepository::class);
        $repo->expects(static::once())
            ->method('getUserActiveSubscriptions')
            ->with($user)
            ->willReturn([$subscription])
        ;

        $logger = $this->getLogger();

        $voter = new SubscriptionVoter([], $repo, $logger);

        $result = $voter->vote($token, null, ['SUBSCRIBED_TO_ANYTHING']);

        // Access denied
        static::assertSame(-1, $result);

        static::assertSame(
            [
                [
                    'error',
                    'Subscription type could not be found in hardcoded types for current user.',
                    [
                        'user' => $user->getUsername(),
                        'subscription_id' => $subscription->getId(),
                        'subscription_type' => $subscription->getType(),
                    ],
                ],
            ],
            $logger->cleanLogs()
        );
    }

    private function getLogger(): AbstractLogger
    {
        return new class() extends AbstractLogger {
            private array $logs = [];

            public function log($level, $message, array $context = []): void
            {
                $this->logs[] = [$level, $message, $context];
            }

            public function cleanLogs(): array
            {
                $logs = $this->logs;
                $this->logs = [];

                return $logs;
            }
        };
    }
}
