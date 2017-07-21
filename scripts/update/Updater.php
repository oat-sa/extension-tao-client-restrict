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

        $this->skip('2.2.0', '3.1.1');
    }

}
