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

namespace Parthenon\Athena\Repository;

use Parthenon\Athena\Entity\DeletableInterface;
use Parthenon\Athena\Filters\DoctrineFilterInterface;
use Parthenon\Athena\Filters\OdmFilterInterface;
use Parthenon\Athena\ResultSet;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\OdmRepository;

class OdmCrudRepository extends OdmRepository implements CrudRepositoryInterface
{
    public function getList(array $filters = [], string $sortKey = 'id', string $sortType = 'ASC', int $limit = self::LIMIT, $lastId = null): ResultSet
    {
        $sortKey = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $sortKey))));

        $parts = explode('\\', $this->documentRepository->getClassName());
        $name = end($parts);
        $qb = $this->documentRepository->createQueryBuilder();
        $qb
            ->sort($sortKey, $sortType)
            ->limit($limit + 1); // Fetch one more than required for pagination.

        $direction = 'DESC' === $sortType ? '<' : '>';
        $sortKey = preg_replace('/[^A-Za-z0-9_]+/', '', $sortKey);
        $dm = $this->documentRepository->getDocumentManager();
        $columns = $dm->getClassMetadata($this->documentRepository->getClassName())->getFieldNames();

        if (!in_array($sortKey, $columns)) {
            throw new \InvalidArgumentException("Sort key doesn't exist");
        }

        if ($lastId) {
            $qb->where($sortKey.' '.$direction.' '.$lastId);
        }

        if (is_a($this->documentRepository->getClassName(), DeletableInterface::class, true)) {
            $qb->field('isDeleted')->equals(false);
        }

        foreach ($filters as $filter) {
            if ($filter instanceof OdmFilterInterface && $filter->hasData()) {
                $filter->modifiyOdmQueryBuilder($qb);
            }
        }

        /** @var DoctrineFilterInterface $filter */
        $query = $qb->getQuery();

        return new ResultSet($query->toArray(), $sortKey, $sortType, $limit);
    }

    public function getById($id, $includeDeleted = false)
    {
        $entity = $this->documentRepository->findOneBy(['id' => $id]);

        if (!$entity || (false == $includeDeleted && is_a($this->documentRepository->getClassName(), DeletableInterface::class, true) && $entity->isDeleted())) {
            throw new NoEntityFoundException();
        }

        return $entity;
    }

    public function delete($entity)
    {
        if (!$entity instanceof DeletableInterface) {
            throw new \InvalidArgumentException('Excepted deletable entity non given. Must implement '.DeletableInterface::class);
        }
        $entity->markAsDeleted();
        $this->save($entity);
    }

    public function undelete($entity)
    {
        if (!$entity instanceof DeletableInterface) {
            throw new \InvalidArgumentException('to deletable entity non given. Must implement '.DeletableInterface::class);
        }
        $entity->unmarkAsDeleted();
        $this->save($entity);
    }
}
