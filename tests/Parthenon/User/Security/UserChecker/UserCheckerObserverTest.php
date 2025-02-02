<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Security\UserChecker;

use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

class UserCheckerObserverTest extends TestCase
{
    public function testCallsPreAuth()
    {
        $checkerOne = $this->createMock(UserCheckerInterface::class);
        $checkerTwo = $this->createMock(UserCheckerInterface::class);
        $user = new User();

        $checkerOne->expects($this->once())->method('checkPreAuth')->with($this->equalTo($user));
        $checkerTwo->expects($this->once())->method('checkPreAuth')->with($this->equalTo($user));

        $userChecker = new UserCheckerObserver();
        $userChecker->add($checkerOne);
        $userChecker->add($checkerTwo);

        $userChecker->checkPreAuth($user);
    }

    public function testCallsPostAuth()
    {
        $checkerOne = $this->createMock(UserCheckerInterface::class);
        $checkerTwo = $this->createMock(UserCheckerInterface::class);
        $user = new User();

        $checkerOne->expects($this->once())->method('checkPostAuth')->with($this->equalTo($user));
        $checkerTwo->expects($this->once())->method('checkPostAuth')->with($this->equalTo($user));

        $userChecker = new UserCheckerObserver();
        $userChecker->add($checkerOne);
        $userChecker->add($checkerTwo);

        $userChecker->checkPostAuth($user);
    }
}
