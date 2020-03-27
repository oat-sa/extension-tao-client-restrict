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

use oat\oatbox\service\ConfigurableService;

/**
 * Class ImportDataProcessor
 *
 * @package oat\taoClientRestrict\model\import
 */
class ImportDataProcessor extends ConfigurableService
{
    /**
     * @param array $data
     * @param Importer $importer
     * @param bool $strictMode
     *
     * @throws \common_exception_Error
     *
     * @return array
     */
    public function process(array $data, Importer $importer, bool $strictMode = false): array
    {
        $validData = [];
        $errors = [];

        foreach ($data as $index => $item) {
            if (!isset($item[Importer::PROPERTY_LABEL])) {
                $errors[] = sprintf(
                    'Required property `label` for item %s is missing. The item will not be imported...',
                    $index
                );
                continue;
            }

            if (isset($item[Importer::PROPERTY_NAME])) {
                if ($importer->nameExists($item[Importer::PROPERTY_NAME])) {
                    $item[Importer::PROPERTY_NAME] = $importer->getNameUri($item[Importer::PROPERTY_NAME]);
                } else {
                    $errors[] = sprintf(
                        'Property `name` for item %s is invalid. The item will not be imported...',
                        $index
                    );
                    continue;
                }
            }

            $validData[] = $item;
        }

        if ($strictMode && !empty($errors)) {
            throw new \common_exception_Error(json_encode($errors));
        }

        return [
            'data' => $validData,
            'errors' => $errors,
        ];
    }
}
