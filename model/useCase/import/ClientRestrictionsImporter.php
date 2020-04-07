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
use oat\generis\model\OntologyRdfs;
use oat\oatbox\service\ConfigurableService;
use oat\taoClientRestrict\model\detection\DetectorClassService;

/**
 * Class ClientRestrictionsImporter
 *
 * @package oat\taoClientRestrict\model\useCase\import
 */
class ClientRestrictionsImporter extends ConfigurableService
{
    /** @var DetectorClassService */
    private $classService;

    /**
     * @param DetectorClassService $classService
     */
    public function setClassService(DetectorClassService $classService): void
    {
        $this->classService = $classService;
    }

    /**
     * @param array $items
     */
    public function import(array $items): void
    {
        $structure = [];

        /** @var ClientRestrictionDTO $item */
        foreach ($items as $item) {
            $class = $this->createClassesStructure($item->getClassMap(), $structure);
            $this->createInstance($class, $item);
        }
    }

    /**
     * @param array $classMap
     * @param array $structure
     *
     * @return core_kernel_classes_Class
     */
    private function createClassesStructure(array $classMap, array &$structure): core_kernel_classes_Class
    {
        $class = $this->classService->getRootClass();

        /** @var string $label */
        foreach ($classMap as $label) {
            $lowercaseLabel = strtolower($label);
            /** @var string|bool $parentUri */
            $parentUri = $structure[$lowercaseLabel] ?? false;

            if ($parentUri === false) {
                $class = $class->createSubClass($label);
                $structure[$lowercaseLabel] = $class->getUri();
            } else {
                $class = $this->classService->getClass($parentUri);
            }
        }

        return $class;
    }

    /**
     * @param core_kernel_classes_Class $class
     * @param ClientRestrictionDTO $dto
     *
     * @return core_kernel_classes_Resource
     */
    private function createInstance(
        core_kernel_classes_Class $class,
        ClientRestrictionDTO $dto
    ): core_kernel_classes_Resource {
        return $class->createInstanceWithProperties([
            OntologyRdfs::RDFS_LABEL => $dto->getLabel(),
            $this->classService->getNamePropertyUri() => $dto->getName(),
            $this->classService->getVersionPropertyUri() => $dto->getVersion(),
        ]);
    }
}
