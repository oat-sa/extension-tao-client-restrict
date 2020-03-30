<?php

namespace oat\taoClientRestrict\model\classManager;

/**
 * Class BrowserClassManager
 *
 * @package oat\taoClientRestrict\model\classManager
 */
class BrowserClassManager extends ClassManager
{
    /**
     * @return string
     */
    public function getRootProperty(): string
    {
        return 'http://www.tao.lu/Ontologies/TAODelivery.rdf#WebBrowser';
    }

    /**
     * @return string
     */
    public function getNameProperty(): string
    {
        return 'http://www.tao.lu/Ontologies/TAODelivery.rdf#BrowserName';
    }

    /**
     * @return string
     */
    public function getVersionProperty(): string
    {
        return 'http://www.tao.lu/Ontologies/TAODelivery.rdf#BrowserVersion';
    }
}
