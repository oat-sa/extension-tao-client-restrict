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
use oat\taoClientRestrict\controller\OS;
use oat\taoClientRestrict\install\RegisterAuthProvider;
use oat\taoClientRestrict\controller\Error;
use oat\taoClientRestrict\scripts\update\Updater;
use oat\tao\model\accessControl\func\AccessRule;
use oat\taoClientRestrict\controller\WebBrowsers;
use oat\taoClientRestrict\install\RegisterClientDiagTester;

return array(
    'name' => 'taoClientRestrict',
    'label' => 'Client Restrictions',
    'description' => '',
    'license' => 'GPL-2.0',
    'version' => '3.3.1',
    'author' => 'Open Assessment Technologies SA',
    'requires' => array(
        'tao' => '>=14.3.1',
        'taoDelivery' => '>=7.0.0',
        'taoClientDiagnostic' => '>=2.5.0',
        'taoBackOffice' => '>=0.8'
    ),
    'managementRole' => 'http://www.tao.lu/Ontologies/generis.rdf#taoClientRestrictManager',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#taoClientRestrictManager', array('ext'=>'taoClientRestrict')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#DeliveryRole', array('controller'=>Error::class)),
        array(AccessRule::GRANT, 'http://www.tao.lu/Ontologies/TAO.rdf#BaseUserRole', WebBrowsers::class),
        array(AccessRule::GRANT, 'http://www.tao.lu/Ontologies/TAO.rdf#BaseUserRole', OS::class),
    ),
    'install' => array(
        'rdf' => array(
            __DIR__ . '/install/ontology/WebBrowsersList.rdf',
            __DIR__ . '/install/ontology/OSsList.rdf',
            __DIR__ . '/install/ontology/taodelivery.rdf',
        ),
        'php' => array(
            RegisterAuthProvider::class,
            RegisterClientDiagTester::class,
        )
    ),
    'uninstall' => array(
    ),
    'update' => Updater::class,
    'routes' => array(
        '/taoClientRestrict' => 'oat\\taoClientRestrict\\controller'
    ),
    'constants' => array(
        # views directory
        "DIR_VIEWS" => __DIR__.DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR,

            #BASE URL (usually the domain root)
            'BASE_URL' => ROOT_URL.'taoClientRestrict/',
    ),
    'extra' => array(
        'structures' => __DIR__.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'structures.xml',
    )
);
