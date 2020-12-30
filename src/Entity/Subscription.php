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

namespace Orbitale\Subs\Entity;

use Doctrine\ORM\Mapping as ORM;
use Orbitale\Subs\Constraint\UniqueSubscription;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Orbitale\Subs\Repository\SubscriptionRepository")
 * @ORM\Table(name="user_subscriptions")
 * @UniqueSubscription
 */
class Subscription
{
    /**
     * @var null|\DateTimeInterface
     *
     * @ORM\Column(name="starts_at", type="date_immutable")
     *
     * @Assert\NotBlank
     * @Assert\Type("DateTimeInterface")
     * @Assert\GreaterThanOrEqual("today")
     */
    protected $startsAt;

    /**
     * @var null|\DateTimeInterface
     *
     * @ORM\Column(name="ends_at", type="date_immutable")
     *
     * @Assert\NotBlank
     * @Assert\Type("DateTimeInterface")
     * @Assert\GreaterThanOrEqual("tomorrow")
     * @Assert\GreaterThanOrEqual(propertyPath="startsAt")
     */
    protected $endsAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="cancelled_manually", type="boolean", nullable=false, options={"default" = "0"})
     */
    protected $cancelledManually = false;

    /**
     * @var null|int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var null|UserInterface
     *
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     *
     * @Assert\NotBlank
     */
    private $user;

    /**
     * @var null|string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     *
     * @Assert\NotBlank
     * @Assert\Choice(Orbitale\Subs\SubscriptionType::TYPES)
     */
    private $type;

    public static function create(UserInterface $user, string $type, \DateTimeImmutable $endsAt): self
    {
        $subscription = new self();

        $subscription->user = $user;
        $subscription->type = $type;
        $subscription->startsAt = new \DateTimeImmutable();
        $subscription->endsAt = $endsAt;

        return $subscription;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getStartsAt(): ?\DateTimeInterface
    {
        return $this->startsAt;
    }

    public function getEndsAt(): ?\DateTimeInterface
    {
        return $this->endsAt;
    }
}
