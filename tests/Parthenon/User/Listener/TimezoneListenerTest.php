<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Partheon\User\Listener;

use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\Team;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\TimezoneAwareInterface;
use Parthenon\User\Entity\User;
use Parthenon\User\Listener\TimezoneListener;
use Parthenon\User\Team\CurrentTeamProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

class TimezoneListenerTest extends TestCase
{
    public function testNoUser()
    {
        $security = $this->createMock(Security::class);
        $teamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $security->method('getUser')->willReturn(null);

        date_default_timezone_set('UTC');

        $timezoneListener = new TimezoneListener($security, $teamProvider);
        $timezoneListener->onKernelRequest();

        $this->assertEquals('UTC', date_default_timezone_get());
    }

    public function testUserIsNotTimezoneAware()
    {
        $security = $this->createMock(Security::class);
        $teamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $user = new User();
        $security->method('getUser')->willReturn($user);

        date_default_timezone_set('UTC');

        $timezoneListener = new TimezoneListener($security, $teamProvider);
        $timezoneListener->onKernelRequest();

        $this->assertEquals('UTC', date_default_timezone_get());
    }

    public function testUserIsTimezoneAware()
    {
        $security = $this->createMock(Security::class);
        $teamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $user = new class() extends User implements TimezoneAwareInterface {
            public function getTimezone(): \DateTimeZone
            {
                return new \DateTimeZone('Europe/Berlin');
            }

            public function hasTimezone(): bool
            {
                return true;
            }
        };
        $security->method('getUser')->willReturn($user);

        date_default_timezone_set('UTC');

        $timezoneListener = new TimezoneListener($security, $teamProvider);
        $timezoneListener->onKernelRequest();

        $this->assertEquals('Europe/Berlin', date_default_timezone_get());
    }

    public function testTeamIsNotTimezoneAware()
    {
        $security = $this->createMock(Security::class);
        $teamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $user = new User();
        $security->method('getUser')->willReturn($user);

        $team = new Team();
        $teamProvider->method('getCurrentTeam')->willReturn($team);

        date_default_timezone_set('UTC');

        $timezoneListener = new TimezoneListener($security, $teamProvider);
        $timezoneListener->onKernelRequest();

        $this->assertEquals('UTC', date_default_timezone_get());
    }

    public function testTeamIsTimezoneAware()
    {
        $security = $this->createMock(Security::class);
        $teamProvider = $this->createMock(CurrentTeamProviderInterface::class);

        $user = new class() extends User implements MemberInterface {
            public function setTeam(TeamInterface $team): MemberInterface
            {
                // TODO: Implement setTeam() method.
            }

            public function getTeam(): TeamInterface
            {
                // TODO: Implement getTeam() method.
            }
        };
        $security->method('getUser')->willReturn($user);

        $team = new class() extends Team implements TimezoneAwareInterface {
            public function getTimezone(): \DateTimeZone
            {
                return new \DateTimeZone('Europe/Berlin');
            }

            public function hasTimezone(): bool
            {
                return true;
            }
        };
        $teamProvider->method('getCurrentTeam')->willReturn($team);

        date_default_timezone_set('UTC');

        $timezoneListener = new TimezoneListener($security, $teamProvider);
        $timezoneListener->onKernelRequest();

        $this->assertEquals('Europe/Berlin', date_default_timezone_get());
    }
}
