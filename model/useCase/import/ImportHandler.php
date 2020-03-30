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
use oat\taoClientRestrict\model\classManager\ClassManager;

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
     * @param ClassManager $classManager
     *
     * @return array
     */
    public function handle(array $data, ClassManager $classManager): array
    {
        $errors = [];

        if (!empty($data)) {
            $names = $classManager->getNames();

            foreach ($data as $index => $item) {
                if ($this->getValidator()->isValid($item, $names) === false) {
                    $errors[] = sprintf(
                        'Item %s is invalid (%s). The item will not be imported.',
                        $index,
                        $this->getValidator()->getError()
                    );
                    continue;
                }

                $this->getImporter($classManager)->import($this->getDataProcessor()->process($item, $names));
            }
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
     * @param ClassManager $classManager
     *
     * @return Importer
     */
    private function getImporter(ClassManager $classManager): Importer
    {
        /** @var Importer $importer */
        $importer = $this->getServiceLocator()->get(Importer::class);
        $importer->setClassManager($classManager);

        return $importer;
    }
}
