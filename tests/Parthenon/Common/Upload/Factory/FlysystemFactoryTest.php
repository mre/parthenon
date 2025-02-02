<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Upload\Factory;

use League\Flysystem\AsyncAwsS3\AsyncAwsS3Adapter;
use Parthenon\Common\Upload\FlysystemUploader;
use Parthenon\Common\Upload\Naming\Factory;
use Parthenon\Common\Upload\Naming\RandomTime;
use PHPUnit\Framework\TestCase;

class FlysystemFactoryTest extends TestCase
{
    public function testCallsAwsFactory()
    {
        $flysystemFactory = $this->createMock(FlySystemAdapterFactoryInterface::class);
        $namingFactory = $this->createMock(Factory::class);
        $awsAdapter = $this->createMock(AsyncAwsS3Adapter::class);

        $config = ['provider' => 's3',  'naming_strategy' => 'random_time', 'url' => 'url'];

        $flysystemFactory->method('getAdapter')->with($config)->willReturn($awsAdapter);
        $namingFactory->method('getStrategy')->with('random_time')->willReturn(new RandomTime());

        $factory = new FlysystemFactory($flysystemFactory, $namingFactory);
        $this->assertInstanceOf(FlysystemUploader::class, $factory->build($config));
    }
}
