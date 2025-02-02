<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Team;

use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\Team;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\User;
use Parthenon\User\Exception\CurrentUserNotATeamMemberException;
use Parthenon\User\Repository\TeamRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

class CurrentTeamProviderTest extends TestCase
{
    public function testCallsTeam()
    {
        $security = $this->createMock(Security::class);
        $teamRepository = $this->createMock(TeamRepositoryInterface::class);
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
        $team = new Team();

        $security->method('getUser')->will($this->returnValue($user));
        $teamRepository->method('getByMember')->with($this->equalTo($user))->will($this->returnValue($team));

        $currentTeamProvider = new CurrentTeamProvider($security, $teamRepository);
        $actual = $currentTeamProvider->getCurrentTeam();

        $this->assertSame($team, $actual);
    }

    public function testUserNotMember()
    {
        $this->expectException(CurrentUserNotATeamMemberException::class);

        $security = $this->createMock(Security::class);
        $teamRepository = $this->createMock(TeamRepositoryInterface::class);
        $user = new User();
        $team = new Team();

        $security->method('getUser')->will($this->returnValue($user));

        $currentTeamProvider = new CurrentTeamProvider($security, $teamRepository);
        $actual = $currentTeamProvider->getCurrentTeam();

        $this->assertSame($team, $actual);
    }
}
