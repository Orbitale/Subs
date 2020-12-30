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

namespace Orbitale\Subs\Repository;

use Doctrine\ORM\EntityRepository;
use Orbitale\Subs\Entity\Subscription;
use Symfony\Component\Security\Core\User\UserInterface;

class SubscriptionRepository extends EntityRepository
{
    public function hasSimilarActiveSubscriptions(Subscription $subscription): bool
    {
        $count = $this->_em->createQuery(
            <<<DQL
            SELECT COUNT(subscription.id) as count
            FROM {$this->_entityName} as subscription
            WHERE subscription.user = :user
            AND subscription.type = :type
            AND subscription.startsAt <= :now
            AND subscription.endsAt >= :now
            DQL
        )
            ->setParameters([
                'user' => $subscription->getUser(),
                'type' => $subscription->getType(),
                'now' => new \DateTimeImmutable(),
            ])
            ->getScalarResult()
        ;

        return $count > 0;
    }

    /**
     * @return Subscription[]
     */
    public function getUserActiveSubscriptions(UserInterface $user): array
    {
        return $this->_em->createQuery(
            <<<DQL
            SELECT subscription
            FROM {$this->_entityName} as subscription
            WHERE subscription.user = :user
            AND subscription.startsAt <= :now
            AND subscription.endsAt >= :now
            DQL
        )
            ->setParameters([
                'user' => $user,
                'now' => new \DateTimeImmutable(),
            ])
            ->getResult()
        ;
    }
}
