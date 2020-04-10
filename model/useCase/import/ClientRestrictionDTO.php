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

/**
 * Class ClientRestrictionDTO
 *
 * @package oat\taoClientRestrict\model\useCase\import
 */
class ClientRestrictionDTO
{
    /** @var array */
    private $classMap;

    /** @var string */
    private $label;

    /** @var string */
    private $name;

    /** @var string */
    private $version;

    /**
     * ClientRestrictionDTO constructor.
     *
     * @param string $label
     * @param string $name
     * @param string $version
     * @param array $classMap
     */
    public function __construct(string $label, string $name, string $version, array $classMap = [])
    {
        $this->label = $label;
        $this->name = $name;
        $this->version = $version;
        $this->classMap = $classMap;
    }

    /**
     * @param array $properties
     *
     * @return static
     */
    public static function createFromArray(array $properties): self
    {
        return new self(
            $properties['label'],
            $properties['name'],
            $properties['version'],
            $properties['classMap'] ?? []
        );
    }

    /**
     * @return array
     */
    public function getClassMap(): array
    {
        return $this->classMap;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
