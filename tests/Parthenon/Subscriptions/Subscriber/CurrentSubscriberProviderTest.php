<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Subscriber;

use Parthenon\Subscriptions\Entity\Subscription;
use Parthenon\Subscriptions\Exception\InvalidSubscriberException;
use Parthenon\User\Entity\Team;
use Parthenon\User\Entity\User;
use Parthenon\User\Team\CurrentTeamProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

class CurrentSubscriberProviderTest extends TestCase
{
    public function testRturnsUser()
    {
        $security = $this->createMock(Security::class);
        $currentTeamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $user = new class() extends User implements SubscriberInterface {
            public function setSubscription(Subscription $subscription)
            {
                // TODO: Implement setSubscription() method.
            }

            public function getSubscription(): ?Subscription
            {
                // TODO: Implement getSubscription() method.
            }

            public function hasActiveSubscription(): bool
            {
                return false;
            }

            public function getIdentifier(): string
            {
                return '';
            }
        };

        $security->method('getUser')->willReturn($user);

        $provider = new CurrentSubscriberProvider(SubscriberInterface::TYPE_USER, $security, $currentTeamProvider);
        $this->assertSame($user, $provider->getSubscriber());
    }

    public function testThrowsExceptionNoUser()
    {
        $this->expectException(InvalidSubscriberException::class);
        $security = $this->createMock(Security::class);
        $currentTeamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $user = new class() extends User {};

        $security->method('getUser')->willReturn($user);

        $provider = new CurrentSubscriberProvider(SubscriberInterface::TYPE_USER, $security, $currentTeamProvider);
        $provider->getSubscriber();
    }

    public function testRturnsTeam()
    {
        $security = $this->createMock(Security::class);
        $currentTeamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $team = new class() extends Team implements SubscriberInterface {
            public function setSubscription(Subscription $subscription)
            {
                // TODO: Implement setSubscription() method.
            }

            public function getSubscription(): ?Subscription
            {
                // TODO: Implement getSubscription() method.
            }

            public function hasActiveSubscription(): bool
            {
                return false;
            }

            public function getIdentifier(): string
            {
                return '';
            }
        };
        $currentTeamProvider->method('getCurrentTeam')->willReturn($team);

        $provider = new CurrentSubscriberProvider(SubscriberInterface::TYPE_TEAM, $security, $currentTeamProvider);
        $this->assertSame($team, $provider->getSubscriber());
    }

    public function testThrowsExceptionInvalidTeam()
    {
        $this->expectException(InvalidSubscriberException::class);
        $security = $this->createMock(Security::class);
        $currentTeamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $team = new class() extends Team {};
        $currentTeamProvider->method('getCurrentTeam')->willReturn($team);

        $provider = new CurrentSubscriberProvider(SubscriberInterface::TYPE_TEAM, $security, $currentTeamProvider);
        $provider->getSubscriber();
    }

    public function testThrowsExceptionInvalidType()
    {
        $this->expectException(InvalidSubscriberException::class);
        $security = $this->createMock(Security::class);
        $currentTeamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $provider = new CurrentSubscriberProvider('', $security, $currentTeamProvider);
        $provider->getSubscriber();
    }
}
