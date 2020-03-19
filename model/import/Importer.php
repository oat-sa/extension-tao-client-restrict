<?php

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
            $folder = $this->createFolderStructure($properties[self::PROPERTY_CLASS_MAP] ?? []);

            $instance = $this->getClassService()->createInstance($folder, $properties[self::PROPERTY_LABEL]);
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
    private function createFolderStructure(array $classMap): \core_kernel_classes_Class
    {
        $parent = $this->getClassService()->getRootClass();

        /** @var string $label */
        foreach ($classMap as $label) {
            $lowercaseLabel = strtolower($label);
            $parentUri = array_search($lowercaseLabel, $this->classMap, true);

            if ($parentUri === false) {
                $parent = $this->getClassService()->createSubClass($parent, $label);
                $this->classMap[$parent->getUri()] = $lowercaseLabel;
            } else {
                $parent = $this->getClass($parentUri);
            }
        }

        return $parent;
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
