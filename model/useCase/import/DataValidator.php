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
 * Class DataValidator
 *
 * @package oat\taoClientRestrict\model\useCase\import
 */
class DataValidator extends ConfigurableService
{
    /** @var array */
    protected $requiredFields = [
        'label',
        'name',
        'version',
    ];

    /** @var array */
    private $errors;

    /**
     * @param array $item
     * @param array $names
     *
     * @return bool
     */
    public function isValid(array $item, array $names): bool
    {
        $this->errors = [];

        $this->checkRequiredFields($item);
        $this->checkIfNameExists($item, $names);

        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $item
     */
    private function checkRequiredFields(array $item): void
    {
        foreach ($this->requiredFields as $field) {
            if (isset($item[$field]) === false) {
                $this->errors[] = sprintf('Required property `%s` is missing.', $field);
            }
        }
    }

    /**
     * @param array $item
     * @param array $names
     */
    private function checkIfNameExists(array $item, array $names): void
    {
        if (isset($item['name']) === false || array_key_exists(strtolower($item['name']), $names) === false) {
            $this->errors[] = 'Property `name` is invalid.';
        }
    }
}
