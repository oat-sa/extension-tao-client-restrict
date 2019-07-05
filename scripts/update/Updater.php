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
 *
 */
namespace oat\taoClientRestrict\scripts\update;

use \common_ext_ExtensionUpdater;
use oat\tao\scripts\update\OntologyUpdater;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\accessControl\func\AclProxy;
use oat\taoClientDiagnostic\model\ClientDiagnosticRoles;
use oat\taoClientRestrict\controller\WebBrowsers;
use oat\taoClientRestrict\controller\OS;
use oat\tao\model\user\TaoRoles;

/**
 *
 * @author Joel Bout <joel@taotesting.com>
 */
class Updater extends common_ext_ExtensionUpdater {

    /**
     * (non-PHPdoc)
     * @see common_ext_ExtensionUpdater::update()
     */
    public function update($initialVersion) {
        $this->skip('1.0.0', '1.0.6');

        if ($this->isVersion('1.0.6')) {
            OntologyUpdater::syncModels();
            $this->setVersion('1.0.7');
        }

        if ($this->isVersion('2.0.0')) {
            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoClientDiagnostic');
            $config = $extension->getConfig('clientDiag');

            $config['testers']['browserVersion'] = [
                'tester' => 'taoClientRestrict/diagnosticTools/browser/tester',
            ];

            $extension->setConfig('clientDiag', $config);

            AclProxy::applyRule(new AccessRule(AccessRule::GRANT, 'http://www.tao.lu/Ontologies/TAO.rdf#BaseUserRole', WebBrowsers::class));

            $this->setVersion('2.1.0');
        }

        if ($this->isVersion('2.1.0')) {
            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoClientDiagnostic');
            $config = $extension->getConfig('clientDiag');

            $config['testers']['osVersion'] = [
                'tester' => 'taoClientRestrict/diagnosticTools/os/tester',
            ];

            $extension->setConfig('clientDiag', $config);

            AclProxy::applyRule(new AccessRule(AccessRule::GRANT, 'http://www.tao.lu/Ontologies/TAO.rdf#BaseUserRole', OS::class));

            $this->setVersion('2.2.0');
        }

        $this->skip('2.2.0', '3.2.2');

        if ($this->isVersion('3.2.2')) {
            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoClientDiagnostic');
            $config = $extension->getConfig('clientDiag');

            $config['testers']['browserVersion'] = [
                'enabled' => true,
                'level' => 1,
                'tester' => 'taoClientRestrict/diagnosticTools/browser/tester',
                'customMsgKey' => 'diagBrowserCheckResult'
            ];

            $config['testers']['osVersion'] = [
                'enabled' => true,
                'level' => 1,
                'tester' => 'taoClientRestrict/diagnosticTools/os/tester',
                'customMsgKey' => 'diagOsCheckResult'
            ];

            $extension->setConfig('clientDiag', $config);

            $this->setVersion('3.3.0');
        }

        $this->skip('3.3.0', '3.3.2');

        if ($this->isVersion('3.3.2')) {
            AclProxy::revokeRule(new AccessRule(AccessRule::GRANT, TaoRoles::BASE_USER, OS::class));
            AclProxy::revokeRule(new AccessRule(AccessRule::GRANT, TaoRoles::BASE_USER, WebBrowsers::class));
            AclProxy::applyRule(new AccessRule(AccessRule::GRANT, TaoRoles::BASE_USER, OS::class . '@diagnose'));
            AclProxy::applyRule(new AccessRule(AccessRule::GRANT, TaoRoles::BASE_USER, WebBrowsers::class . '@diagnose'));
            AclProxy::applyRule(new AccessRule(AccessRule::GRANT, TaoRoles::TAO_MANAGER, OS::class));
            AclProxy::applyRule(new AccessRule(AccessRule::GRANT, TaoRoles::TAO_MANAGER, WebBrowsers::class));
            $this->setVersion('3.3.3');
        }


        $this->skip('3.3.3', '4.0.0');

        if ($this->isVersion('4.0.0')) {
            AclProxy::applyRule(new AccessRule(AccessRule::GRANT, TaoRoles::ANONYMOUS, OS::class . '@diagnose'));
            AclProxy::applyRule(new AccessRule(AccessRule::GRANT, TaoRoles::ANONYMOUS, WebBrowsers::class . '@diagnose'));
            $this->setVersion('4.0.1');
        }

        $this->skip('4.0.1', '5.0.1');

        if ($this->isVersion('5.0.1')) {
            $extension = $this->getServiceManager()
                ->get(\common_ext_ExtensionsManager::SERVICE_ID)
                ->getExtensionById('taoClientDiagnostic');
            $oldClientDiagConfig = $extension->getConfig('clientDiag');

            $oldClientDiagConfig['diagnostic']['testers']['browserVersion'] = [
                'enabled' => true,
                'level' => 1,
                'tester' => 'taoClientRestrict/diagnosticTools/browser/tester',
                'customMsgKey' => 'diagBrowserCheckResult'
            ];
            $oldClientDiagConfig['diagnostic']['testers']['osVersion'] = [
                'enabled' => true,
                'level' => 1,
                'tester' => 'taoClientRestrict/diagnosticTools/os/tester',
                'customMsgKey' => 'diagOsCheckResult'
            ];

            $extension->setConfig('clientDiag', $oldClientDiagConfig);

            $this->setVersion('5.0.2');
        }

        $this->skip('5.0.2', '5.0.3');
    }

}
