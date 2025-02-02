<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Decider\ChoiceDecider;

use Parthenon\AbTesting\Entity\Experiment;
use Parthenon\AbTesting\Entity\Variant;
use Parthenon\AbTesting\Repository\ExperimentRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CacheGeneratorTest extends TestCase
{
    public const DECISION = 'control';
    public const DECISION_ID = 'decision_id';

    public function testIsSetCache()
    {
        $experimentRepository = $this->createMock(ExperimentRepositoryInterface::class);

        $redis = $this->createMock(\Redis::class);

        $experiment = new Experiment();
        $experiment->setName(self::DECISION_ID);
        $variant = new Variant();
        $variant->setName(self::DECISION);
        $variant->setIsDefault(true);
        $experiment->setVariants([$variant]);

        $experimentTwo = new Experiment();

        $experimentRepository->method('findAll')->will($this->generate([$experiment, $experimentTwo]));

        $redis->expects($this->once())
            ->method('set')
            ->with($this->equalTo('abtesting_decision_cache'), $this->equalTo(json_encode([self::DECISION_ID => self::DECISION])));

        $cacheGenerator = new CacheGenerator($experimentRepository, $redis);
        $cacheGenerator->generate();
    }

    protected function generate(array $yield_values)
    {
        return $this->returnCallback(function () use ($yield_values) {
            foreach ($yield_values as $value) {
                yield $value;
            }
        });
    }
}
