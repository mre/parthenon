<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Validator;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Entity\User;
use Parthenon\User\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UniqueUserValidatorTest extends TestCase
{
    public function testUnique()
    {
        $userRepostiory = $this->createMock(UserRepositoryInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);

        $userRepostiory->method('findByEmail')->with($this->equalTo('email@example.org'))->will($this->throwException(new NoEntityFoundException()));

        $context->expects($this->never())->method('buildViolation');

        $validator = new UniqueUserValidator($userRepostiory);
        $validator->initialize($context);
        $validator->validate('email@example.org', new UniqueUser());
    }

    public function testNotValid()
    {
        $userRepostiory = $this->createMock(UserRepositoryInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $userRepostiory->method('findByEmail')->with($this->equalTo('email@example.org'))->will($this->returnValue(new User()));

        $constraint = new UniqueUser();
        $context->expects($this->once())->method('buildViolation')->with($this->equalTo($constraint->message))->will($this->returnValue($violationBuilder));

        $violationBuilder->expects($this->once())->method('setParameter')->will($this->returnSelf());
        $violationBuilder->expects($this->once())->method('setCode')->with($this->anything())->will($this->returnSelf());
        $violationBuilder->expects($this->once())->method('addViolation');

        $validator = new UniqueUserValidator($userRepostiory);
        $validator->initialize($context);
        $validator->validate('email@example.org', $constraint);
    }
}
