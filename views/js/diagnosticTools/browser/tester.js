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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define([
    'jquery',
    'i18n',
    'util/url',
    'taoClientDiagnostic/tools/getconfig'

], function ($, __, url, getConfig) {
    'use strict';

    /**
     * Some default values
     * @type {Object}
     * @private
     */
    var _defaults = {
        browserVersionAction: 'diagnose',
        browserVersionController: 'WebBrowsers',
        browserVersionExtension: 'taoClientRestrict',
    };


    /**
     * Performs a browser support test
     *
     * @param {Object} [config] - Some optional configs
     * @param {String} [config.action] - The name of the action to call to get the browser checker
     * @param {String} [config.controller] - The name of the controller to call to get the browser checker
     * @param {String} [config.extension] - The name of the extension containing the controller to call to get the browser checker
     * @returns {Object}
     */
    function browserTester(config, diagnosticTool) {
        var initConfig = getConfig(config || {}, _defaults);

        return {
            /**
             * Performs a browser support test, then call a function to provide the result
             * @param {Function} done
             */
            start: function start(done) {
                var testerUrl = url.route(initConfig.browserVersionAction, initConfig.browserVersionController, initConfig.browserVersionExtension);

                diagnosticTool.changeStatus(__('Checking the browser version...'));
                $.ajax({
                    url : testerUrl,
                    success : function(data) {
                        var percentage = data.success ? 100 : 0;
                        var status = {
                            percentage: percentage,
                            quality: {},
                            feedback: {
                                message: data.success ? __('Compatible') : __('Not Compatible'),
                                threshold: 100,
                                type: data.success ? 'success' : 'error',
                            }
                        };
                        var summary = {
                            browser: {
                                message: __('Web browser version'),
                                value: data.success ? __('Compatible') : __('Not Compatible')
                            }
                        };

                        status.id = 'browser_version';
                        status.title = __('Web browser version');

                        done(status, summary, {compatible: data.success});
                    }
                });
            }
        };
    }

    return browserTester;
});
