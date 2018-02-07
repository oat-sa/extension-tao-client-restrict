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
 * Copyright (c) 2016-2017 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'util/url',
    'taoClientDiagnostic/tools/getConfig',
    'taoClientDiagnostic/tools/getLabels',
    'taoClientDiagnostic/tools/getPlatformInfo'
], function ($, _, __, url, getConfig, getLabels, getPlatformInfo) {
    'use strict';

    /**
     * Some default values
     * @type {Object}
     * @private
     */
    var _defaults = {
        id: 'os_version',
        osVersionAction: 'diagnose',
        osVersionController: 'OS',
        osVersionExtension: 'taoClientRestrict'
    };

    /**
     * Placeholder variables for custom messages
     * @type {Object}
     * @private
     */
    var _placeHolders = {
        CURRENT_OS: '%CURRENT_OS%',
        APPROVED_OS: '%APPROVED_OS%'
    };

    /**
     * List of translated texts per level.
     * The level is provided through the config as a numeric value, starting from 1.
     * @type {Object}
     * @private
     */
    var _messages = [
        // level 1
        {
            title: __('Operating system version'),
            status: __('Checking the os version...'),
            success: __('Pass – Your Operating System is approved'),
            failure: __('Issue'),
            compatible: __('Compatible'),
            notCompatible: __('Not Compatible')
        }
    ];

    /**
     * Performs a browser support test
     *
     * @param {Object} config - Some optional configs
     * @param {String} [config.id] - The identifier of the test
     * @param {String} [config.action] - The name of the action to call to get the browser checker
     * @param {String} [config.controller] - The name of the controller to call to get the browser checker
     * @param {String} [config.extension] - The name of the extension containing the controller to call to get the browser checker
     * @param {String} [config.level] - The intensity level of the test. It will aim which messages list to use.
     * @returns {Object}
     */
    function osTester(config) {
        var initConfig = getConfig(config, _defaults);
        var labels = getLabels(_messages, initConfig.level);

        return {
            /**
             * Performs a browser support test, then call a function to provide the result
             * @param {Function} done
             */
            start: function start(done) {
                var testerUrl = url.route(initConfig.osVersionAction, initConfig.osVersionController, initConfig.osVersionExtension);
                var self = this;

                getPlatformInfo(window)
                    .then(function(platformInfo) {
                        $.ajax({
                            url: testerUrl,
                            success: function (data) {
                                var percentage = data.success ? 100 : 0;
                                var status = self.getFeedback(percentage, data);
                                var summary = self.getSummary(platformInfo, data);
                                var currentOs = platformInfo.os + ' ' + platformInfo.osVersion;
                                var approvedOs = [];

                                if (_.isObject(data.approvedOs)) {
                                    _.forOwn(data.approvedOs, function (versions, osName) {
                                        if (_.isArray(versions) && versions.length > 0) {
                                            osName += ' (' + versions.join(', ') + ')';
                                        }
                                        approvedOs.push(osName);
                                    });
                                }

                                status.customMsgRenderer = function(customMsg) {
                                    return (customMsg || '')
                                        .replace(_placeHolders.CURRENT_OS, currentOs)
                                        .replace(_placeHolders.APPROVED_OS, approvedOs.join(', '));
                                };

                                done(status, summary, {compatible: data.success});
                            }
                        });
                    });
            },

            /**
             * Gets the labels loaded for the tester
             * @returns {Object}
             */
            get labels() {
                return labels;
            },

            /**
             * Builds the results summary
             * @param {Object} results
             * @param {Object} data
             * @returns {Object}
             */
            getSummary: function getSummary(results, data) {
                return {
                    os: {
                        message: labels.title,
                        value: data.success ? labels.compatible : labels.notCompatible
                    }
                };
            },

            /**
             * Gets the feedback status for the provided result value
             * @param {Number} result
             * @param {Object} data
             * @returns {Object}
             */
            getFeedback: function getFeedback(result, data) {
                var status = {
                    percentage: result,
                    quality: {},
                    feedback: {
                        message: data.success ? labels.success : labels.failure,
                        threshold: 100,
                        type: data.success ? 'success' : 'error'
                    }
                };

                status.id = initConfig.id;
                status.title =  labels.title;

                return status;
            }
        };
    }

    return osTester;
});
