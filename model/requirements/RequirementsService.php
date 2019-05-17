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
 * Copyright (c) 2016-2019 (original work) Open Assessment Technologies SA;
 *
 */
namespace oat\taoClientRestrict\model\requirements;

use oat\oatbox\service\ConfigurableService;
use oat\generis\model\OntologyAwareTrait;
use oat\taoClientRestrict\model\detection\BrowserClassService;
use oat\taoClientRestrict\model\detection\DetectorClassService;
use oat\taoClientRestrict\model\detection\OsClassService;

class RequirementsService extends ConfigurableService implements RequirementsServiceInterface
{
    use OntologyAwareTrait;

    const PROPERTY_DELIVERY_APPROVED_BROWSER = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ApprovedBrowser';
    const PROPERTY_DELIVERY_RESTRICT_BROWSER_USAGE = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#RestrictBrowserUsage';

    const PROPERTY_DELIVERY_APPROVED_OS = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ApprovedOS';
    const PROPERTY_DELIVERY_RESTRICT_OS_USAGE = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#RestrictOSUsage';

    const URI_DELIVERY_COMPLY_ENABLED = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ComplyEnabled';

    private $approvedOs = [];
    private $approvedBrowsers = [];

    /**
     * Whether client complies to the delivery execution
     * @param string $deliveryId
     * @return boolean
     */
    public function compliesToDelivery($deliveryId)
    {
        $delivery = $this->getResource($deliveryId);
        return $this->browserComplies($delivery) && $this->OSComplies($delivery);
    }

    /**
     * @param \core_kernel_classes_Resource $delivery
     * @return boolean
     */
    public function browserComplies(\core_kernel_classes_Resource $delivery = null)
    {
        $isBrowserApproved = true;
        if ($delivery !== null) {
            $isBrowserRestricted = $delivery->getOnePropertyValue($this->getProperty(self::PROPERTY_DELIVERY_RESTRICT_BROWSER_USAGE));
            if (!is_null($isBrowserRestricted) && self::URI_DELIVERY_COMPLY_ENABLED == $isBrowserRestricted->getUri()) {
                //@TODO property caching  - anyway we are operating with complied
                $browsers = $this->getApprovedBrowsers();
                $isBrowserApproved = $this->complies($browsers, BrowserClassService::singleton());
            }
        }
        return $isBrowserApproved;
    }

    public function getApprovedBrowsers(\core_kernel_classes_Resource $delivery = null)
    {
        if ($delivery !== null) {
            $deliveryUri = $delivery->getUri();
            if (empty($this->approvedBrowsers[$deliveryUri])) {
                $this->approvedBrowsers[$deliveryUri] = $delivery
                    ->getPropertyValuesCollection($this->getProperty(self::PROPERTY_DELIVERY_APPROVED_BROWSER))
                    ->toArray();
            }
            return $this->approvedBrowsers[$deliveryUri];
        }
        return [];
    }

    /**
     * @param \core_kernel_classes_Resource $delivery
     * @return boolean
     */
    public function OSComplies(\core_kernel_classes_Resource $delivery = null)
    {
        $isOSApproved = true;
        if ($delivery !== null) {
            $isOSRestricted = $delivery->getOnePropertyValue($this->getProperty(self::PROPERTY_DELIVERY_RESTRICT_OS_USAGE));
            if (!is_null($isOSRestricted) && self::URI_DELIVERY_COMPLY_ENABLED == $isOSRestricted->getUri()) {
                //@TODO property caching  - anyway we are operating with complied
                $approvedOs = $this->getApprovedOs($delivery);
                $isOSApproved = $this->complies($approvedOs, OsClassService::singleton());
            }
        }
        return $isOSApproved;
    }

    public function getApprovedOs(\core_kernel_classes_Resource $delivery = null)
    {
        if ($delivery !== null) {
            $deliveryUri = $delivery->getUri();
            if (empty($this->approvedOs[$deliveryUri])) {
                $this->approvedOs[$deliveryUri] = $delivery
                    ->getPropertyValuesCollection($this->getProperty(self::PROPERTY_DELIVERY_APPROVED_OS))
                    ->toArray();
            }
            return $this->approvedOs[$deliveryUri];
        }
        return [];
    }

    /**
     * @param array $conditions
     * @param DetectorClassService $conditionService
     * @return bool
     */
    protected function complies(array $conditions, DetectorClassService $conditionService)
    {
        $clientName = $conditionService->getDetector()->getName();
        $clientVersion = $conditionService->getDetector()->getVersion();
        $clientNameResource = $conditionService->getClientNameResource();
        $this->logDebug("Detected client: ${clientName} @ ${clientVersion}");

        $result = false;
        /** @var \core_kernel_classes_Property $browser */
        foreach ($conditions as $condition) {
            if ($condition->exists() === true) {
                /** @var \core_kernel_classes_Resource $requiredName */
                $requiredName = $condition->getOnePropertyValue($conditionService->getNameProperty());

                if ($clientNameResource && !($clientNameResource->equals($requiredName))) {
                    $this->logInfo("Client rejected. Required name is ${requiredName} but current name is ${clientName}.");
                    continue;
                } elseif ($clientNameResource === null) {
                    $this->logInfo("Client rejected. Unknown client.");
                    continue;
                }

                $requiredVersion = $condition->getOnePropertyValue($conditionService->getVersionProperty());
                if (-1 !== $this->versionCompare($conditionService->getDetector()->getVersion(), $requiredVersion)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Standard version_compare threats that  5.2 < 5.2.0, 5.2 < 5.2.1, ...
     *
     * @param $ver1
     * @param $ver2
     * @param null|string @see http://php.net/manual/en/function.version-compare.php
     * @return mixed
     */
    protected function versionCompare($ver1, $ver2, $operator = null)
    {
        $ver1 = preg_replace('#(\.0+)+($|-)#', '', $ver1);
        $ver2 = preg_replace('#(\.0+)+($|-)#', '', $ver2);
        if ($operator === null) {
            $result = version_compare($ver1, $ver2);
        } else {
            $result = version_compare($ver1, $ver2, $operator);
        }
        return $result;
    }

}
