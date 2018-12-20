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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoClientRestrict\model\detection;

use Sinergi\BrowserDetector\Os;

/**
 * Class OSClassService
 *
 * Service to manage detected OS
 *
 * @package oat\taoClientRestrict\model\detection
 */
class OSClassService extends DetectorClassService
{
    const ROOT_CLASS = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OS';
    const MAKE_CLASS = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OSMake';
    const PROPERTY_NAME = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OSName';
    const PROPERTY_VERSION = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OSVersion';

    /**
     * Get the root class for Operating system
     *
     * @return \core_kernel_classes_Class
     */
    public function getRootClass()
    {
        return $this->getClass(self::ROOT_CLASS);
    }

    /**
     * Get the detector for Operating System
     *
     * @return Os
     */
    public function getDetector()
    {
        if (!$this->detector) {
            $this->detector = new Os();
        }
        return $this->detector;
    }

    /**
     * Get the name property of detected OS
     *
     * @return \core_kernel_classes_Property
     */
    public function getNameProperty()
    {
        return $this->getProperty(self::PROPERTY_NAME);
    }

    /**
     * Get the version property of detected OS
     *
     * @return \core_kernel_classes_Property
     */
    public function getVersionProperty()
    {
        return $this->getProperty(self::PROPERTY_VERSION);
    }

    /**
     * Get the parent class of detectable OS
     *
     * @return \core_kernel_classes_Class
     */
    protected function getMakeClass()
    {
        return $this->getClass(self::MAKE_CLASS);
    }

}