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

namespace Parthenon\Common\Upload\Naming;

final class RandomTime implements NamingStrategyInterface
{
    public function getName(string $filename): string
    {
        $parts = explode('.', $filename);
        $fileType = end($parts);

        return bin2hex(random_bytes(32)).'-'.time().'.'.$fileType;
    }
}
