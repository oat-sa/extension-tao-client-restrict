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
use oat\taoClientRestrict\model\useCase\import\DataValidator;

/**
 * Class DataValidatorTest
 *
 * @package oat\taoClientRestrict\test\unit\useCase\import
 */
class DataValidatorTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     *
     * @param array $data
     * @param array $expected
     */
    public function testDataValidator(array $data, array $expected): void
    {
        $dataValidator = new DataValidator();
        $isValid = $dataValidator->isValid($data['item'], $data['names']);

        $this->assertEquals($expected['isValid'], $isValid);
        $this->assertEquals($expected['error'], $dataValidator->getError());
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        $validName = 'validName';
        $lowercaseValidName = strtolower($validName);

        return [
            [
                'data' => [
                    'item' => [
                        'label' => 'Test label',
                        'name' => $validName,
                        'version' => 'Test version',
                    ],
                    'names' => [
                        $lowercaseValidName => 'Name 1',
                    ],
                ],
                'expected' => [
                    'isValid' => true,
                    'error' => null,
                ],
            ],
            [
                'data' => [
                    'item' => [
                        'name' => $validName,
                        'version' => 'Test version',
                    ],
                    'names' => [],
                ],
                'expected' => [
                    'isValid' => false,
                    'error' => 'Required property `label` is missing.',
                ],
            ],
            [
                'data' => [
                    'item' => [
                        'label' => 'Test label',
                        'version' => 'Test version',
                    ],
                    'names' => [],
                ],
                'expected' => [
                    'isValid' => false,
                    'error' => 'Required property `name` is missing.',
                ],
            ],
            [
                'data' => [
                    'item' => [
                        'label' => 'Test label',
                        'name' => $validName,
                    ],
                    'names' => [],
                ],
                'expected' => [
                    'isValid' => false,
                    'error' => 'Required property `version` is missing.',
                ],
            ],
            [
                'data' => [
                    'item' => [
                        'label' => 'Test label',
                        'name' => $validName,
                        'version' => 'Test version',
                    ],
                    'names' => [
                        $lowercaseValidName => 'Name 1',
                    ],
                ],
                'expected' => [
                    'isValid' => true,
                    'error' => null,
                ],
            ],
            [
                'data' => [
                    'item' => [
                        'label' => 'Test label',
                        'name' => $validName,
                        'version' => 'Test version',
                    ],
                    'names' => [],
                ],
                'expected' => [
                    'isValid' => false,
                    'error' => 'Property `name` is invalid.',
                ],
            ],
        ];
    }
}
