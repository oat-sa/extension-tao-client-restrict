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
    'lodash',
    'i18n',
    'util/url',
    'taoClientDiagnostic/tools/getconfig',
    'taoClientDiagnostic/tools/getPlatformInfo'
], function ($, _, __, url, getConfig, getPlatformInfo) {
    'use strict';

    /**
     * Some default values
     * @type {Object}
     * @private
     */
    var _defaults = {
        browserVersionAction: 'diagnose',
        browserVersionController: 'WebBrowsers',
        browserVersionExtension: 'taoClientRestrict'
    };

    /**
     * Placeholder variables for custom messages
     * @type {Object}
     * @private
     */
    var _placeHolders = {
        CURRENT_BROWSER: '%CURRENT_BROWSER%',
        APPROVED_BROWSERS: '%APPROVED_BROWSERS%'
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

                getPlatformInfo(window)
                    .then(function(platformInfo) {
                        $.ajax({
                            url: testerUrl,
                            success: function (data) {
                                var percentage = data.success ? 100 : 0;
                                var status = {
                                    percentage: percentage,
                                    quality: {},
                                    feedback: {
                                        message: data.success ? __('Pass – Your browser is approved') : __('Issue'),
                                        threshold: 100,
                                        type: data.success ? 'success' : 'error'
                                    }
                                };
                                var summary = {
                                    browser: {
                                        message: __('Web browser version'),
                                        value: data.success ? __('Compatible') : __('Not Compatible')
                                    }
                                };
                                var currentBrowser = platformInfo.browser + ' ' + platformInfo.browserVersion;
                                var customMsg = diagnosticTool.getCustomMsg('diagBrowserCheckResult') || '';
                                var approvedBrowsers = [];

                                if (_.isObject(data.approvedBrowsers)) {
                                    _.forOwn(data.approvedBrowsers, function (versions, browserName) {
                                        if (_.isArray(versions) && versions.length > 0) {
                                            browserName += ' (' + versions.join(', ') + ')';
                                        }
                                        approvedBrowsers.push(browserName);
                                    });
                                }

                                status.id = 'browser_version';
                                status.title = __('Web browser version');

                                customMsg = customMsg
                                    .replace(_placeHolders.CURRENT_BROWSER, currentBrowser)
                                    .replace(_placeHolders.APPROVED_BROWSERS, approvedBrowsers.join(', '));
                                diagnosticTool.addCustomFeedbackMsg(status, customMsg);

                                done(status, summary, {compatible: data.success});
                            }
                        });
                    });
            }
        };
    }

    return browserTester;
});
