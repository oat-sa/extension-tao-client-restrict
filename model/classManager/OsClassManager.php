<?php

namespace oat\taoClientRestrict\model\classManager;

/**
 * Class OsClassManager
 *
 * @package oat\taoClientRestrict\model\classManager
 */
class OsClassManager extends ClassManager
{
    /**
     * @return string
     */
    public function getRootProperty(): string
    {
        return 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OS';
    }

    /**
     * @return string
     */
    public function getNameProperty(): string
    {
        return 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OSName';
    }

    /**
     * @return string
     */
    public function getVersionProperty(): string
    {
        return 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OSVersion';
    }
}
