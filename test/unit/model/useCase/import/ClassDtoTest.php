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

/**
 * Class ClassDtoTest
 *
 * @package oat\taoClientRestrict\test\unit\useCase\import
 */
class ClassDtoTest extends TestCase
{
    public function testClassDto(): void
    {
        $properties = [
            'classMap' => ['Test class map'],
            'label' => 'Test label',
            'name' => 'Test name',
            'version' => 'Test version',
        ];

        $classDto = new ClassDTO();

        $this->assertEquals([], $classDto->getClassMap());

        $classDto
            ->setClassMap($properties['classMap'])
            ->setLabel($properties['label'])
            ->setName($properties['name'])
            ->setVersion($properties['version']);

        $this->assertEquals($properties['classMap'], $classDto->getClassMap());
        $this->assertEquals($properties['label'], $classDto->getLabel());
        $this->assertEquals($properties['name'], $classDto->getName());
        $this->assertEquals($properties['version'], $classDto->getVersion());
    }
}
