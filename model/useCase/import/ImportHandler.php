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
use oat\taoClientRestrict\model\detection\DetectorClassService;

/**
 * Class ImportHandler
 *
 * @package oat\taoClientRestrict\model\useCase\import
 */
class ImportHandler extends ConfigurableService
{
    /** @var DataValidator */
    private $validator;

    /**
     * @param array $data
     * @param DetectorClassService $classService
     *
     * @return array
     */
    public function handle(array $data, DetectorClassService $classService): array
    {
        $errors = [];

        if (!empty($data)) {
            $names = $classService->getNames();
            $importer = $this->getImporter($classService);

            foreach ($data as $index => $item) {
                if ($this->getValidator()->isValid($item, $names) === false) {
                    $errors[] = sprintf(
                        'Item %s is invalid (%s). The item will not be imported.',
                        $index,
                        $this->getValidator()->getError()
                    );
                    continue;
                }

                $importer->import($this->getDataProcessor()->process($item, $names));
            }

            $importer->resetClassMap();
        }

        return $errors;
    }

    /**
     * @return DataValidator
     */
    private function getValidator(): DataValidator
    {
        if (!$this->validator) {
            $this->validator = $this->getServiceLocator()->get(DataValidator::class);
        }

        return $this->validator;
    }

    /**
     * @return DataProcessor
     */
    private function getDataProcessor(): DataProcessor
    {
        return $this->getServiceLocator()->get(DataProcessor::class);
    }

    /**
     * @param DetectorClassService $classService
     *
     * @return Importer
     */
    private function getImporter(DetectorClassService $classService): Importer
    {
        /** @var Importer $importer */
        $importer = $this->getServiceLocator()->get(Importer::class);
        $importer->setClassService($classService);

        return $importer;
    }
}
