<?php
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

/**
 *
 * @package     mod_onlyoffice
 * @subpackage
 * @copyright   2018 Olumuyiwa Taiwo <muyi.taiwo@logicexpertise.com>
 * @author      Olumuyiwa Taiwo {@link https://moodle.org/user/view.php?id=416594}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_onlyoffice\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;

/**
 * @todo Custom module instance display, similar to https://api.onlyoffice.com/editors/alfresco
 */
class renderer extends plugin_renderer_base {

    /**
     * Returns html to display the content of mod_folder
     */
    public function render_summary($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('mod_onlyoffice/summary', $data);
    }

}