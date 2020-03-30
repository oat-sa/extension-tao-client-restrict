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

namespace oat\taoClientRestrict\model\useCase\import;

use oat\oatbox\service\ConfigurableService;

/**
 * Class DataProcessor
 *
 * @package oat\taoClientRestrict\model\useCase\import
 */
class DataProcessor extends ConfigurableService
{
    /**
     * @param array $item
     * @param array $names
     *
     * @return ClassDTO
     */
    public function process(array $item, array $names): ClassDTO
    {
        $item['name'] = $names[strtolower($item['name'])];

        return $this->toDTO($item);
    }

    /**
     * @param array $properties
     *
     * @return ClassDTO
     */
    private function toDTO(array $properties): ClassDTO
    {
        $dto = new ClassDTO();
        $dto
            ->setLabel($properties['label'])
            ->setName($properties['name'])
            ->setVersion($properties['version']);

        if (isset($properties['classMap'])) {
            $dto->setClassMap($properties['classMap']);
        }

        return $dto;
    }
}
