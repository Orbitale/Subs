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

namespace Tests\Orbitale\Subs;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\MetadataInterface;

class ExecutionContextStubFactory implements ExecutionContextInterface
{
    public function addViolation(string $message, array $params = []): void
    {
    }

    public function buildViolation(string $message, array $parameters = []): void
    {
    }

    public function getValidator(): void
    {
    }

    public function getObject(): void
    {
    }

    public function setNode($value, ?object $object, MetadataInterface $metadata = null, string $propertyPath): void
    {
    }

    public function setGroup(?string $group): void
    {
    }

    public function setConstraint(Constraint $constraint): void
    {
    }

    public function markGroupAsValidated(string $cacheKey, string $groupHash): void
    {
    }

    public function isGroupValidated(string $cacheKey, string $groupHash): void
    {
    }

    public function markConstraintAsValidated(string $cacheKey, string $constraintHash): void
    {
    }

    public function isConstraintValidated(string $cacheKey, string $constraintHash): void
    {
    }

    public function markObjectAsInitialized(string $cacheKey): void
    {
    }

    public function isObjectInitialized(string $cacheKey): void
    {
    }

    public function getViolations(): void
    {
    }

    public function getRoot(): void
    {
    }

    public function getValue(): void
    {
    }

    public function getMetadata(): void
    {
    }

    public function getGroup(): void
    {
    }

    public function getClassName(): void
    {
    }

    public function getPropertyName(): void
    {
    }

    public function getPropertyPath(string $subPath = ''): void
    {
    }
}
