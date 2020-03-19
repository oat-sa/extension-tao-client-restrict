<?php

namespace oat\taoClientRestrict\scripts\tools\import;

use oat\oatbox\action\Action;
use common_report_Report as Report;
use oat\taoClientRestrict\model\import\Importer;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class ImportScript
 *
 * @package oat\taoClientRestrict\scripts\tools\import
 */
abstract class ImportScript implements Action, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    private const EXT_JSON = 'json';
    private const EXT_CSV = 'csv';

    /** @var Importer */
    private $service;

    /** @var Report */
    private $report;

    /**
     * @param $params
     *
     * @throws \common_exception_Error
     *
     * @return Report
     */
    public function __invoke($params)
    {
        $this->report = Report::createInfo('Running script ' . static::class);

        $data = $this->parseData($params);
        $this->import($data);

        return $this->report;
    }

    /**
     * @param array $params
     *
     * @throws \common_exception_Error
     *
     * @return array
     */
    protected function parseData(array $params): array
    {
        if (!empty($params) || isset($params['list'])) {
            if (is_array($params['list'])) {
                $data = $params['list'];
            } elseif (is_string($params['list']) && file_exists($params['list'])) {
                $data = $this->parseFile($params['list']);
            }
        } else {
            $this->report->add(Report::createWarning(
                'Required parameter `list` is missing. Nothing to import.'
            ));
        }

        return $this->getValidData($data ?? []);
    }

    /**
     * @return string
     */
    abstract protected function getServiceId(): string;

    /**
     * @return Importer
     */
    private function getService(): Importer
    {
        if (!$this->service) {
            $this->service = $this->getServiceLocator()->get($this->getServiceId());
        }

        return $this->service;
    }

    /**
     * @param string $filename
     *
     * @return array
     */
    private function parseFile(string $filename): array
    {
        switch (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            case self::EXT_JSON:
                $data = json_decode(file_get_contents($filename), true);
                break;
            case self::EXT_CSV:
                $lines = array_filter(explode("\n", file_get_contents($filename)));
                $items = array_map('str_getcsv', $lines);
                $keys = $items[0];

                $data = array_map(static function ($item) use ($keys) {
                    return array_combine($keys, $item);
                }, array_slice($items, 1));
                break;
            default:
                $data = [];
                break;
        }

        return is_array($data) ? $data : [];
    }

    /**
     * @param array $data
     *
     * @throws \common_exception_Error
     *
     * @return array
     */
    private function getValidData(array $data): array
    {
        $validData = [];
        $service = $this->getService();

        foreach ($data as $index => $item) {
            if (!isset($item[Importer::PROPERTY_LABEL])) {
                $this->report->add(Report::createWarning(sprintf(
                    'Required property `label` for item %s is missing. The item will not be imported...',
                    $index
                )));
                continue;
            }

            if (isset($item[Importer::PROPERTY_NAME])) {
                if ($service->nameExists($item[Importer::PROPERTY_NAME])) {
                    $item[Importer::PROPERTY_NAME] = $service->getNameUri($item[Importer::PROPERTY_NAME]);
                } else {
                    $this->report->add(Report::createWarning(sprintf(
                        'Property `name` for item %s is invalid. The item will not be imported...',
                        $index
                    )));
                    continue;
                }
            }

            $validData[] = $item;
        }

        return $validData;
    }

    /**
     * @param array $data
     */
    private function import(array $data): void
    {
        $this->getService()->import($data);
    }
}
