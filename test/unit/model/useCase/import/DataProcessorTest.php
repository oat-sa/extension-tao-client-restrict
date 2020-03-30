<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoClientRestrict\test\unit\useCase\import;

use oat\generis\test\TestCase;
use oat\taoClientRestrict\model\useCase\import\ClassDTO;
use oat\taoClientRestrict\model\useCase\import\DataProcessor;

/**
 * Class DataProcessorTest
 *
 * @package oat\taoClientRestrict\test\unit\useCase\import
 */
class DataProcessorTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     *
     * @param array $data
     * @param array $expected
     */
    public function testDataProcessor(array $data, array $expected): void
    {
        $dataProcessor = new DataProcessor();
        $result = $dataProcessor->process($data['item'], $data['names']);

        $this->assertInstanceOf(ClassDTO::class, $result);
        $this->assertEquals($expected['name'], $result->getName());
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        $validName = 'validName';
        $item = [
            'label' => 'Test label',
            'name' => $validName,
            'version' => 'Test version',
        ];

        return [
            [
                'data' => [
                    'item' => $item,
                    'names' => [
                        strtolower($validName) => 'Name 1',
                    ],
                ],
                'expected' => [
                    'name' => 'Name 1',
                ],
            ],
            [
                'data' => [
                    'item' => $item,
                    'names' => [],
                ],
                'expected' => [
                    'name' => $validName,
                ],
            ],
        ];
    }
}
