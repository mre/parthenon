<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Subscriptions\Plan\Security\Voter;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Subscriptions\Exception\NoPlanFoundException;
use Parthenon\Subscriptions\Plan\CounterManager;
use Parthenon\Subscriptions\Plan\LimitedUserInterface;
use Parthenon\Subscriptions\Plan\PlanManagerInterface;
use Parthenon\User\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class PlanFeatureVoter extends Voter
{
    use LoggerAwareTrait;

    public const SUPPORTED_ATTRIBUTES = ['feature_enabled'];
    private CounterManager $counterManager;
    private PlanManagerInterface $planManager;

    public function __construct(CounterManager $counterManager, PlanManagerInterface $planManager)
    {
        $this->counterManager = $counterManager;
        $this->planManager = $planManager;
    }

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, self::SUPPORTED_ATTRIBUTES)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!is_string($subject)) {
            return true;
        }

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$user instanceof LimitedUserInterface) {
            return true;
        }

        try {
            $plan = $this->planManager->getPlanForUser($user);
        } catch (NoPlanFoundException $exception) {
            $this->getLogger()->warning('No plan for user', ['plan_name' => $user->getPlanName()]);

            return true;
        }

        return $plan->hasFeature($subject);
    }
}
