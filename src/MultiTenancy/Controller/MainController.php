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

namespace Parthenon\MultiTenancy\Controller;

use Parthenon\MultiTenancy\RequestProcessor\TenantSignup;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController
{
    #[Route('/tenant/signup', name: 'parthenon_multi_tenancy_signup')]
    public function tenantSignup(Request $request, TenantSignup $tenantSignup)
    {
        return $tenantSignup->process($request);
    }
}
