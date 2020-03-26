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

use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\service\ConfigurableService;

/**
 * Class ImportBrowsersService
 *
 * @package oat\taoClientRestrict\model\import
 */
abstract class Importer extends ConfigurableService
{
    use OntologyAwareTrait;

    public const PROPERTY_LABEL = 'label';
    public const PROPERTY_CLASS_MAP = 'classMap';
    public const PROPERTY_NAME = 'name';
    public const PROPERTY_VERSION = 'version';

    /** @var array */
    private $names = [];

    /** @var array */
    private $classMap = [];

    /**
     * @param array $data
     */
    public function import(array $data): void
    {
        /** @var array $properties */
        foreach ($data as $properties) {
            $class = $this->createClassesStructure($properties[self::PROPERTY_CLASS_MAP] ?? []);

            $instance = $class->createInstance($properties[self::PROPERTY_LABEL]);
            $this->setProperties($instance, $properties);
        }
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function nameExists(string $name): bool
    {
        return array_key_exists(strtolower($name), $this->getNames());
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getNameUri(string $name): string
    {
        return $this->getNames()[strtolower($name)];
    }

    /**
     * @return \tao_models_classes_Service
     */
    abstract protected function getClassService();

    /**
     * @return string
     */
    abstract protected function getPropertyName(): string;

    /**
     * @return string
     */
    abstract protected function getPropertyVersion(): string;

    /**
     * @return array
     */
    private function getNames(): array
    {
        if (!$this->names) {
            $nameInstances = $this->getClassService()->getNameProperty()->getRange()->getInstances();

            /** @var \core_kernel_classes_Resource $nameInstance */
            foreach ($nameInstances as $nameInstance) {
                $this->names[strtolower($nameInstance->getLabel())] = $nameInstance->getUri();
            }
        }

        return $this->names;
    }

    /**
     * @param array $classMap
     *
     * @return \core_kernel_classes_Class
     */
    private function createClassesStructure(array $classMap): \core_kernel_classes_Class
    {
        /** @var \core_kernel_classes_Class $class */
        $class = $this->getClassService()->getRootClass();

        /** @var string $label */
        foreach ($classMap as $label) {
            $lowercaseLabel = strtolower($label);
            $parentUri = $this->classMap[$lowercaseLabel] ?? false;

            if ($parentUri === false) {
                $class = $class->createSubClass($label);
                $this->classMap[$lowercaseLabel] = $class->getUri();
            } else {
                $class = $this->getClass($parentUri);
            }
        }

        return $class;
    }

    /**
     * @param \core_kernel_classes_Resource $instance
     * @param array $properties
     */
    private function setProperties(\core_kernel_classes_Resource $instance, array $properties): void
    {
        $propertiesToSet = [];

        if (isset($properties[self::PROPERTY_NAME])) {
            $propertiesToSet[$this->getPropertyName()] = $properties[self::PROPERTY_NAME];
        }

        if (isset($properties[self::PROPERTY_VERSION])) {
            $propertiesToSet[$this->getPropertyVersion()] = $properties[self::PROPERTY_VERSION];
        }

        if (!empty($propertiesToSet)) {
            $instance->setPropertiesValues($propertiesToSet);
        }
    }
}
