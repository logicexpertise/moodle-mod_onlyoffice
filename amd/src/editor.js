// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/* @package    mod_onlyoffice
 * @copyright  2018 Olumuyiwa Taiwo <muyi.taiwo@logicexpertise.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define(['jquery'], function ($) {
    var displayError = function (error) {
        require(['core/str'], function (str) {
            var errorIsAvailable = str.get_string(error, 'onlyoffice');
            $.when(errorIsAvailable).done(function (localizedStr) {
                $("#onlyoffice-editor").text = localizedStr;
                $("#onlyoffice-editor").text(localizedStr).addClass("error");
            });
        });
    };
    return {
        init: function (config) {
            if (typeof DocsAPI === "undefined") {
                displayError(('docserverunreachable'));
                return;
            }
            if (config.errors) {
                displayError(config.errors);
            }
            var docEditor = new DocsAPI.DocEditor("onlyoffice-editor", config);
        }
    };
});