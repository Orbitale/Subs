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

namespace Orbitale\Subs\Voter;

use Orbitale\Subs\Repository\SubscriptionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SubscriptionVoter extends Voter
{
    /**
     * @var array<string, array<string>
     */
    private array $subscriptionTypesPermissions;
    private SubscriptionRepository $subscriptionRepository;
    private LoggerInterface $logger;

    public function __construct(array $subscriptionTypesPermissions, SubscriptionRepository $subscriptionRepository, LoggerInterface $logger)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->logger = $logger;
        $this->subscriptionTypesPermissions = $subscriptionTypesPermissions;
    }

    protected function supports($attribute, $subject): bool
    {
        return \is_string($attribute) && \str_starts_with($attribute, 'SUBSCRIBED_TO_');
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        $permissionsToCheck = [];

        foreach ($this->subscriptionRepository->getUserActiveSubscriptions($user) as $subscription) {
            $permissions = $this->subscriptionTypesPermissions[$subscription->getType()] ?? null;

            if (!$permissions) {
                $this->logger->error('Subscription type could not be found in hardcoded types for current user.', [
                    'user' => $user->getUsername(),
                    'subscription_id' => $subscription->getId(),
                    'subscription_type' => $subscription->getType(),
                ]);

                continue;
            }

            foreach ($permissions as $permission) {
                $permissionsToCheck[] = $permission;
            }
        }

        return \in_array($attribute, $permissionsToCheck, true);
    }
}
