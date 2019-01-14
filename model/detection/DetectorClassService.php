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

use oat\generis\model\OntologyAwareTrait;
use oat\generis\model\OntologyRdfs;

abstract class DetectorClassService extends \tao_models_classes_ClassService
{
    use OntologyAwareTrait;

    protected $detector;

    /**
     * Get the detector for Web Browser
     *
     * @return mixed
     */
    abstract public function getDetector();

    /**
     * Get the name property
     *
     * @return \core_kernel_classes_Property
     */
    abstract public function getNameProperty();

    /**
     * Get the version property
     *
     * @return \core_kernel_classes_Property
     */
    abstract public function getVersionProperty();

    /**
     * Get the parent class
     *
     * @return \core_kernel_classes_Class
     */
    abstract protected function getMakeClass();

    /**
     * @return \core_kernel_classes_Resource|null
     */
    public function getClientNameResource()
    {
        $detectedName = $this->getDetector()->getName();

        $results = $this->getMakeClass()->searchInstances(
            [ OntologyRdfs::RDFS_LABEL => $detectedName ],
            [ 'like' => false ]
        );

        $result = array_pop($results);

        return $result;
    }
}