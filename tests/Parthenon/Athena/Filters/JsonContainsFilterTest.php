<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena\Filters;

use DG\BypassFinals;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class JsonContainsFilterTest extends TestCase
{
    public function testItSetsTheFieldONQueryBuilder()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('getRootAliases')->willReturn(['f', 't']);
        $queryBuilder->expects($this->once())->method('andWhere')->with('JSON_CONTAINS(f.field_name, :field_name) = 1');

        $containsFilter = new JsonContainsFilter();
        $containsFilter->setFieldName('field_name');
        $containsFilter->modifyQueryBuilder($queryBuilder);
    }

    public function testSetParameterQuery()
    {
        BypassFinals::enable();
        $query = $this->createMock(Query::class);
        $query->expects($this->once())->method('setParameter')->with($this->equalTo(':field_name'), $this->equalTo('field_value'));

        $containsFilter = new JsonContainsFilter();
        $containsFilter->setFieldName('field_name')->setData('field_value');
        $containsFilter->modifyQuery($query);
    }
}
