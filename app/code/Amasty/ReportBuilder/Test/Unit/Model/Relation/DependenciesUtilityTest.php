<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Test\Unit\Model\Relation;

use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Model\Relation\DependenciesUtility;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class DependenciesUtilityTest extends TestCase
{
    /**
     * @var DependenciesUtility
     */
    private $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = (new ObjectManager($this))->getObject(DependenciesUtility::class);
    }

    /**
     * @dataProvider dataProvider
     * @covers DependenciesUtility::injectRelationsByPath
     */
    public function testInjectRelationsByPath(array $relations, array $dependenciesPath, array $expectedResult)
    {
        $result = $this->model->injectRelationsByPath($relations, $dependenciesPath);

        self::assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            'Init Insert' => [
                [],
                ['main', 'ch', 'ch_ch'],
                [
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'main', ReportInterface::SCHEME_ENTITY => 'ch'],
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'ch', ReportInterface::SCHEME_ENTITY => 'ch_ch']
                ]
            ],
            'Add' => [
                [
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'main0', ReportInterface::SCHEME_ENTITY => 'ch0']
                ],
                ['main', 'ch', 'ch_ch'],
                [
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'main0', ReportInterface::SCHEME_ENTITY => 'ch0'],
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'main', ReportInterface::SCHEME_ENTITY => 'ch'],
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'ch', ReportInterface::SCHEME_ENTITY => 'ch_ch']
                ]
            ],
            'Add with diff' => [
                [
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'main', ReportInterface::SCHEME_ENTITY => 'ch0'],
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'main', ReportInterface::SCHEME_ENTITY => 'ch']
                ],
                ['main', 'ch', 'ch_ch'],
                [
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'main', ReportInterface::SCHEME_ENTITY => 'ch0'],
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'main', ReportInterface::SCHEME_ENTITY => 'ch'],
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'ch', ReportInterface::SCHEME_ENTITY => 'ch_ch']
                ]
            ],
            'already processed' => [
                [
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'main', ReportInterface::SCHEME_ENTITY => 'ch'],
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'ch', ReportInterface::SCHEME_ENTITY => 'ch_ch']
                ],
                ['main', 'ch', 'ch_ch'],
                [
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'main', ReportInterface::SCHEME_ENTITY => 'ch'],
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'ch', ReportInterface::SCHEME_ENTITY => 'ch_ch']
                ]
            ],
            'add same entity with another parent' => [
                [
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'main', ReportInterface::SCHEME_ENTITY => 'ch'],
                ], // relations
                ['main2', 'ch'], // path
                [
                    [ReportInterface::SCHEME_SOURCE_ENTITY => 'main', ReportInterface::SCHEME_ENTITY => 'ch']
                ] // result
            ]
        ];
    }
}
