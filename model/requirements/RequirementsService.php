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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */
namespace oat\taoClientRestrict\model\requirements;

use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\generis\model\OntologyAwareTrait;

class RequirementsService extends ConfigurableService implements RequirementsServiceInterface
{
    use OntologyAwareTrait;

    const PROPERTY_DELIVERY_APPROVED_BROWSER = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ApprovedBrowser';
    const PROPERTY_DELIVERY_RESTRICT_BROWSER_USAGE = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#RestrictBrowserUsage';

    const PROPERTY_DELIVERY_APPROVED_OS = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ApprovedOS';
    const PROPERTY_DELIVERY_RESTRICT_OS_USAGE = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#RestrictOSUsage';

    const URI_DELIVERY_COMPLY_ENABLED = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ComplyEnabled';
    
    /**
     * Whether client complies to the delivery execution
     * @param string $deliveryId
     * @return boolean
     */
    public function compliesToDelivery($deliveryId)
    {
        $delivery = $this->getResource($deliveryId);
        $isBrowserApproved = true;
        $isOSApproved = true;

        $isBrowserRestricted = $delivery->getOnePropertyValue(new \core_kernel_classes_Property(self::PROPERTY_DELIVERY_RESTRICT_BROWSER_USAGE));
        if (!is_null($isBrowserRestricted) && self::URI_DELIVERY_COMPLY_ENABLED == $isBrowserRestricted->getUri()) {
            //@TODO property caching  - anyway we are operating with complied
            $browsers = $delivery->getPropertyValuesCollection(new \core_kernel_classes_Property(self::PROPERTY_DELIVERY_APPROVED_BROWSER));
            $isBrowserApproved = $this->complies($browsers->toArray(), WebBrowserService::class);
        }

        $isOSRestricted = $delivery->getOnePropertyValue(new \core_kernel_classes_Property(self::PROPERTY_DELIVERY_RESTRICT_OS_USAGE));
        if (!is_null($isOSRestricted) && self::URI_DELIVERY_COMPLY_ENABLED == $isOSRestricted->getUri()) {
            //@TODO property caching  - anyway we are operating with complied
            $OS = $delivery->getPropertyValuesCollection(new \core_kernel_classes_Property(self::PROPERTY_DELIVERY_APPROVED_OS));
            $isOSApproved = $this->complies($OS->toArray(), OSService::class);
        }

        return $isBrowserApproved && $isOSApproved;
    }

    /**
     * @param array $conditions
     * @param string $conditionService
     * @return bool
     */
    protected function complies(array $conditions, $conditionService)
    {
        $clientName = $conditionService::singleton()->getClientName();
        $clientVersion = $conditionService::singleton()->getClientVersion();
        $clientResource = $conditionService::singleton()->getClientNameResource();
        \common_Logger::i("Detected client: ${clientName} @ ${clientVersion}");

        $result = false;
        /** @var \core_kernel_classes_Property $browser */
        foreach ($conditions as $condition) {
            // Make sure there is still a resource in database for the Resource's URI.
            if ($condition->exists() === true) {
                /** @var \core_kernel_classes_Resource $requiredName */
                $requiredName = $condition->getOnePropertyValue(new \core_kernel_classes_Property($conditionService::PROPERTY_NAME));

                if (!($clientResource->equals($requiredName))) {
                    \common_Logger::i("Client rejected. Client name ${clientName} doesn't match ".$requiredName->getUri());
                    continue;
                }

                $requiredVersion = $condition->getOnePropertyValue(new \core_kernel_classes_Property($conditionService::PROPERTY_VERSION));
                if (-1 !== version_compare($conditionService::singleton()->getClientVersion(), $requiredVersion)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

}
