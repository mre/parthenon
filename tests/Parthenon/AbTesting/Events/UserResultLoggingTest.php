<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Events;

use Parthenon\AbTesting\Experiment\ResultLogger;
use Parthenon\User\Entity\User;
use Parthenon\User\Event\PostUserConfirmEvent;
use Parthenon\User\Event\PostUserSignupEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserResultLoggingTest extends TestCase
{
    public function testDoesNotCallResultLoggerSignup()
    {
        $resultLogger = $this->createMock(ResultLogger::class);
        $event = $this->createMock(PostUserSignupEvent::class);
        $user = new User();

        $resultLogger->expects($this->never())->method('log')->with($this->equalTo('user_signup'), $this->equalTo($user));

        $userResultLogging = new UserResultsLogging($resultLogger, false);
        $userResultLogging->handleUserSignup($event);
    }

    public function testDoesCallResultLoggerSignup()
    {
        $resultLogger = $this->createMock(ResultLogger::class);
        $event = $this->createMock(PostUserSignupEvent::class);
        $user = new User();

        $event->method('getUser')->willReturn($user);

        $resultLogger->expects($this->once())->method('log')->with($this->equalTo('user_signup'), $this->equalTo($user));

        $userResultLogging = new UserResultsLogging($resultLogger, true);
        $userResultLogging->handleUserSignup($event);
    }

    public function testDoesNotCallResultLoggerSignupEmail()
    {
        $resultLogger = $this->createMock(ResultLogger::class);
        $event = $this->createMock(PostUserConfirmEvent::class);
        $user = new User();

        $resultLogger->expects($this->never())->method('log')->with($this->equalTo('user_email_confirmed'), $this->equalTo($user));

        $userResultLogging = new UserResultsLogging($resultLogger, false);
        $userResultLogging->handleUserConfirm($event);
    }

    public function testDoesCallResultLoggerSignupEmail()
    {
        $resultLogger = $this->createMock(ResultLogger::class);
        $event = $this->createMock(PostUserConfirmEvent::class);
        $user = new User();

        $event->method('getUser')->willReturn($user);

        $resultLogger->expects($this->once())->method('log')->with($this->equalTo('user_email_confirmed'), $this->equalTo($user));

        $userResultLogging = new UserResultsLogging($resultLogger, true);
        $userResultLogging->handleUserConfirm($event);
    }

    public function testHandleLogins()
    {
        $resultLogger = $this->createMock(ResultLogger::class);
        $event = $this->createMock(InteractiveLoginEvent::class);
        $user = new User();
        $token = $this->createMock(TokenInterface::class);

        $event->method('getAuthenticationToken')->willReturn($token);
        $token->method('getUser')->willReturn($user);

        $resultLogger->expects($this->once())->method('log')->with($this->equalTo('user_login'), $this->equalTo($user));

        $userResultLogging = new UserResultsLogging($resultLogger, true);
        $userResultLogging->handleUserLogin($event);
    }
}
