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
 * Class ImportClientRestrictionsHandler
 *
 * @package oat\taoClientRestrict\model\useCase\import
 */
class ImportClientRestrictionsHandler extends ConfigurableService
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
            $itemsToImport = [];

            $names = $classService->getExistingNames();
            $importer = $this->getImporter($classService);

            foreach ($data as $index => $item) {
                if ($this->getValidator()->isValid($item, $names) === false) {
                    foreach ($this->getValidator()->getErrors() as $error) {
                        $errors[] = sprintf('Item %s is invalid (%s).', $index, $error);
                    }

                    $errors[] = sprintf('Item %s will not be imported.', $index);
                    continue;
                }

                $item['name'] = $names[strtolower($item['name'])] ?? $item['name'];
                $itemsToImport[] = ClientRestrictionDTO::createFromArray($item);
            }

            $importer->import($itemsToImport);
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
     * @param DetectorClassService $classService
     *
     * @return ClientRestrictionsImporter
     */
    private function getImporter(DetectorClassService $classService): ClientRestrictionsImporter
    {
        /** @var ClientRestrictionsImporter $importer */
        $importer = $this->getServiceLocator()->get(ClientRestrictionsImporter::class);
        $importer->setClassService($classService);

        return $importer;
    }
}
