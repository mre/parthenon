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

namespace Parthenon\Invoice;

use Brick\Money\Money;

interface ItemInterface
{
    public function setVatRate(float $vatRate);

    public function getSubTotal(): Money;

    public function getVat(): Money;

    public function getSingleVat(): Money;

    public function getTotal(): Money;

    public function getName(): string;

    public function getType(): string;
}
