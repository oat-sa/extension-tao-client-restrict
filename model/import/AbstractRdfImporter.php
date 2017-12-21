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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoClientRestrict\model\import;


use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;

abstract class AbstractRdfImporter extends \tao_models_classes_import_RdfImporter {


    /**
     * Imports the rdf file into the selected class
     *
     * @param string $content
     * @param \core_kernel_classes_Class $class
     * @return \common_report_Report
     */
    protected function flatImport($content, \core_kernel_classes_Class $class)
    {
        $report = \common_report_Report::createSuccess(__('Data imported successfully'));
        $graph = new \EasyRdf_Graph();
        $graph->parse($content);

        $correctClass = true;
        foreach ($this->getMandatoryProperties() as $mandatoryProperty){
            if(empty($graph->resourcesMatching($mandatoryProperty))){
                $correctClass = false;
                break;
            }
        }

        if($correctClass){
            // keep type property
            $map = array(
                OntologyRdf::RDF_PROPERTY => OntologyRdf::RDF_PROPERTY
            );

            foreach ($graph->resources() as $resource) {
                if($class->getUri() !== $resource->getUri()){
                    $map[$resource->getUri()] = \common_Utils::getNewUri();
                }
            }

            $format = \EasyRdf_Format::getFormat('php');
            $data = $graph->serialise($format);

            foreach ($data as $subjectUri => $propertiesValues){
                if($class->getUri() !== $subjectUri){
                    $resource = new \core_kernel_classes_Resource($map[$subjectUri]);
                    $subreport = $this->importProperties($resource, $propertiesValues, $map, $class);
                    if(!is_null($subreport)){
                        $report->add($subreport);
                    }
                }
            }
        } else {
            $report = \common_report_Report::createFailure($this->getErrorMessage());
        }
        return $report;
    }

    /**
     * Import the properties of the resource
     *
     * @param \core_kernel_classes_Resource $resource
     * @param array $propertiesValues
     * @param array $map
     * @param \core_kernel_classes_Class $class
     * @return \common_report_Report
     */
    protected function importProperties(\core_kernel_classes_Resource $resource, $propertiesValues, $map, $class) {
        if (isset($propertiesValues[OntologyRdf::RDF_TYPE])) {
            // assuming single Type
            if (count($propertiesValues[OntologyRdf::RDF_TYPE]) > 1) {
                return new \common_report_Report(\common_report_Report::TYPE_ERROR, __('Resource not imported due to multiple types'));
            } else {
                foreach ($propertiesValues[OntologyRdf::RDF_TYPE] as $k => $v) {
                    if($v['value'] === OntologyRdf::RDF_PROPERTY){
                        return null;
                    }
                    $classType = isset($map[$v['value']])
                        ? new \core_kernel_classes_Class($map[$v['value']])
                        : $class;
                        $classType->createInstance(null, null, $resource->getUri());
                }
            }
            unset($propertiesValues[OntologyRdf::RDF_TYPE]);
        }

        if (isset($propertiesValues[OntologyRdfs::RDFS_SUBCLASSOF])) {
            $resource = new \core_kernel_classes_Class($resource);
            // assuming single subclass
            if (isset($propertiesValues[OntologyRdf::RDF_TYPE]) && count($propertiesValues[OntologyRdf::RDF_TYPE]) > 1) {
                return new \common_report_Report(\common_report_Report::TYPE_ERROR, __('Resource not imported due to multiple super classes'));
            }
            foreach ($propertiesValues[OntologyRdfs::RDFS_SUBCLASSOF] as $k => $v) {
                $classSup = isset($map[$v['value']])
                    ? new \core_kernel_classes_Class($map[$v['value']])
                    : $class;
                $resource->setSubClassOf($classSup);
            }

            unset($propertiesValues[OntologyRdfs::RDFS_SUBCLASSOF]);
        }

        foreach ($propertiesValues as $prop=>$values){
            $property = new \core_kernel_classes_Property($prop);
            foreach ($values as $k => $v) {
                $value = $v['value'];
                if (isset($v['lang'])) {
                    $resource->setPropertyValueByLg($property, $value, $v['lang']);
                } else {
                    $resource->setPropertyValue($property, $value);
                }
            }
        }
        $msg = $resource instanceof \core_kernel_classes_Class
            ? __('Successfully imported class "%s"', $resource->getLabel())
            : __('Successfully imported "%s"', $resource->getLabel());

        return new \common_report_Report(\common_report_Report::TYPE_SUCCESS, $msg, $resource);
    }

    /**
     * Get the list of properties that should be part of the resource
     * @return array
     */
    abstract protected function getMandatoryProperties();

    /**
     * get the message to display to user if it isn't the right resource
     * @return string
     */
    abstract  protected function getErrorMessage();


}
