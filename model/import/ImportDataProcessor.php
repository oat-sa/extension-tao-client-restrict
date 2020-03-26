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

namespace oat\taoClientRestrict\model\import;

use common_report_Report as Report;
use oat\oatbox\service\ConfigurableService;

/**
 * Class ImportDataProcessor
 *
 * @package oat\taoClientRestrict\model\import
 */
class ImportDataProcessor extends ConfigurableService
{
    /** @var array */
    private $errors;

    /**
     * @param array $data
     * @param Importer $importer
     *
     * @return array
     */
    public function process(array $data, Importer $importer): array
    {
        $validData = [];
        $this->errors = [];

        foreach ($data as $index => $item) {
            if (!isset($item[Importer::PROPERTY_LABEL])) {
                $this->fail(sprintf(
                    'Required property `label` for item %s is missing. The item will not be imported...',
                    $index
                ));
                continue;
            }

            if (isset($item[Importer::PROPERTY_NAME])) {
                if ($importer->nameExists($item[Importer::PROPERTY_NAME])) {
                    $item[Importer::PROPERTY_NAME] = $importer->getNameUri($item[Importer::PROPERTY_NAME]);
                } else {
                    $this->fail(sprintf(
                        'Property `name` for item %s is invalid. The item will not be imported...',
                        $index
                    ));
                    continue;
                }
            }

            $validData[] = $item;
        }

        return $validData;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $message
     *
     * @return Report
     */
    private function fail(string $message): Report
    {
        $this->errors[] = Report::createFailure($message);
    }
}
