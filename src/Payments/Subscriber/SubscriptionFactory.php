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

namespace Parthenon\Payments\Subscriber;

use Parthenon\Payments\PriceProviderInterface;
use Parthenon\Subscriptions\Entity\Subscription;
use Parthenon\Subscriptions\Plan\Plan;
use Parthenon\Subscriptions\Subscriber\SubscriptionFactoryInterface;

final class SubscriptionFactory implements SubscriptionFactoryInterface
{
    public function __construct(private PriceProviderInterface $priceProvider)
    {
    }

    public function createFromPlanAndPaymentSchedule(Plan $plan, string $paymentSchedule): Subscription
    {
        $priceId = $this->priceProvider->getPriceId($plan, $paymentSchedule);

        $subscription = new Subscription();
        $subscription->setPlanName($plan->getName());
        $subscription->setPaymentSchedule($paymentSchedule);
        $subscription->setPriceId($priceId);

        return $subscription;
    }
}
