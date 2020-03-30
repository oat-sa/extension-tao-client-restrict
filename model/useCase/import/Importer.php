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

use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\oatbox\service\ConfigurableService;
use oat\taoClientRestrict\model\detection\DetectorClassService;

/**
 * Class Importer
 *
 * @package oat\taoClientRestrict\model\useCase\import
 */
class Importer extends ConfigurableService
{
    /** @var DetectorClassService */
    private $classService;

    /** @var array */
    private $classMap = [];

    /**
     * @param DetectorClassService $classService
     */
    public function setClassService(DetectorClassService $classService): void
    {
        $this->classService = $classService;
    }

    /**
     * @param ClassDTO $dto
     */
    public function import(ClassDTO $dto): void
    {
        $class = $this->createClassesStructure($dto->getClassMap());
        $this->createInstance($class, $dto);
    }

    /**
     * @param array $classMap
     *
     * @return core_kernel_classes_Class
     */
    private function createClassesStructure(array $classMap): core_kernel_classes_Class
    {
        $class = $this->classService->getRootClass();

        /** @var string $label */
        foreach ($classMap as $label) {
            $lowercaseLabel = strtolower($label);
            /** @var string|bool $parentUri */
            $parentUri = $this->classMap[$lowercaseLabel] ?? false;

            if ($parentUri === false) {
                $class = $class->createSubClass($label);
                $this->classMap[$lowercaseLabel] = $class->getUri();
            } else {
                $class = $this->classService->getClass($parentUri);
            }
        }

        return $class;
    }

    /**
     * @param core_kernel_classes_Class $class
     * @param ClassDTO $dto
     *
     * @return core_kernel_classes_Resource
     */
    private function createInstance(core_kernel_classes_Class $class, ClassDTO $dto): core_kernel_classes_Resource
    {
        $instance = $class->createInstance($dto->getLabel());
        $instance->setPropertiesValues([
            $this->classService->getNamePropertyUri() => $dto->getName(),
            $this->classService->getVersionPropertyUri() => $dto->getVersion(),
        ]);

        return $instance;
    }
}
