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

namespace Parthenon\Subscriptions\Plan;

use Parthenon\Subscriptions\Exception\NoPlanFoundException;

interface PlanManagerInterface
{
    /**
     * @return Plan[]
     */
    public function getPlans(): array;

    /**
     * @throws NoPlanFoundException
     */
    public function getPlanForUser(LimitedUserInterface $limitedUser): Plan;

    /**
     * @throws NoPlanFoundException
     */
    public function getPlanByName(string $planName): Plan;
}
