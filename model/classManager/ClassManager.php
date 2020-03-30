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

namespace oat\taoClientRestrict\model\classManager;

use core_kernel_classes_Class;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\service\ConfigurableService;

/**
 * Class ClassManager
 *
 * @package oat\taoClientRestrict\model\classManager
 */
abstract class ClassManager extends ConfigurableService
{
    use OntologyAwareTrait;

    /** @var array */
    private $names;

    /**
     * @return array
     */
    public function getNames(): array
    {
        if (!$this->names) {
            $nameInstances = $this->getProperty($this->getNameProperty())->getRange()->getInstances();

            /** @var \core_kernel_classes_Resource $nameInstance */
            foreach ($nameInstances as $nameInstance) {
                $this->names[strtolower($nameInstance->getLabel())] = $nameInstance->getUri();
            }
        }

        return $this->names;
    }

    /**
     * @return core_kernel_classes_Class
     */
    public function getRootClass(): core_kernel_classes_Class
    {
        return $this->getClass($this->getRootProperty());
    }

    /**
     * @return string
     */
    abstract public function getRootProperty(): string;

    /**
     * @return string
     */
    abstract public function getNameProperty(): string;

    /**
     * @return string
     */
    abstract public function getVersionProperty(): string;
}
