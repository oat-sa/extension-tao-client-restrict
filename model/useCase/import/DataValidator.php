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

    /** @var string|null */
    private $error;

    /**
     * @param array $item
     * @param array $names
     *
     * @return bool
     */
    public function isValid(array $item, array $names): bool
    {
        if ($this->checkRequiredFields($item) === false || $this->nameExists($item, $names) === false) {
            return false;
        }

        return true;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @param array $item
     *
     * @return bool
     */
    private function checkRequiredFields(array $item): bool
    {
        foreach ($this->requiredFields as $field) {
            if (array_key_exists($field, $item) === false) {
                $this->error = sprintf('Required property `%s` is missing.', $field);

                return false;
            }
        }

        return true;
    }

    /**
     * @param array $item
     * @param array $names
     *
     * @return bool
     */
    private function nameExists(array $item, array $names): bool
    {
        $isValid = array_key_exists(strtolower($item['name']), $names);

        if ($isValid === false) {
            $this->error = 'Property `name` is invalid.';
        }

        return $isValid;
    }
}
