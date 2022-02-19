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
 * @copyright   2022 Olumuyiwa Taiwo <muyi.taiwo@logicexpertise.com>
 * @author      Olumuyiwa Taiwo {@link https://moodle.org/user/view.php?id=416594}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete onlyoffice structure for backup, with file and id annotations
 */
class backup_onlyoffice_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');
        
        // Define each element separated
        $onlyoffice = new backup_nested_element('onlyoffice', ['id'],
                [
            'course', 'name', 'intro', 'introformat', 'timecreated', 'timemodified',
            'display', 'displayoptions', 'permissions', 'documentkey',
        ]);

        // Define sources
        $onlyoffice->set_source_table(
                'onlyoffice', ['id' => backup::VAR_ACTIVITYID]
        );

        // Define file annotations
        $onlyoffice->annotate_files('mod_onlyoffice', 'content', null);

        // Return the root element (onlyoffice), wrapped into standard activity structure
        return $this->prepare_activity_structure($onlyoffice);
    }
}