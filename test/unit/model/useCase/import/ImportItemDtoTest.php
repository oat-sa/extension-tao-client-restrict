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
use oat\taoClientRestrict\model\useCase\import\ImportItemDTO;

/**
 * Class ImportItemDtoTest
 *
 * @package oat\taoClientRestrict\test\unit\useCase\import
 */
class ImportItemDtoTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     *
     * @param array $data
     * @param array $expected
     */
    public function testClassDto(array $data, array $expected): void
    {
        $classDto = ImportItemDTO::createFromArray($data);

        $this->assertEquals($expected['classMap'], $classDto->getClassMap());
        $this->assertEquals($expected['label'], $classDto->getLabel());
        $this->assertEquals($expected['name'], $classDto->getName());
        $this->assertEquals($expected['version'], $classDto->getVersion());
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            'With class map' => [
                'data' => [
                    'classMap' => ['Test class map'],
                    'label' => 'Test label',
                    'name' => 'Test name',
                    'version' => 'Test version',
                ],
                'expected' => [
                    'classMap' => ['Test class map'],
                    'label' => 'Test label',
                    'name' => 'Test name',
                    'version' => 'Test version',
                ],
            ],
            'Without class map' => [
                'data' => [
                    'label' => 'Test label',
                    'name' => 'Test name',
                    'version' => 'Test version',
                ],
                'expected' => [
                    'classMap' => [],
                    'label' => 'Test label',
                    'name' => 'Test name',
                    'version' => 'Test version',
                ],
            ],
        ];
    }
}
