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
 * onlyoffice restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
require_once($CFG->dirroot . '/mod/onlyoffice/backup/moodle2/restore_onlyoffice_stepslib.php'); // Because it exists (must)

class restore_onlyoffice_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Choice only has one structure step
        $this->add_step(new restore_onlyoffice_activity_structure_step('onlyoffice_structure',
                        'onlyoffice.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('onlyoffice', array('intro'),
                'onlyoffice');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('ONLYOFFICEVIEWBYID',
                '/mod/onlyoffice/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('ONLYOFFICEINDEX',
                '/mod/onlyoffice/index.php?id=$1', 'course');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * onlyoffice logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('onlyoffice', 'add',
                'view.php?id={course_module}', '{onlyoffice}');
        $rules[] = new restore_log_rule('onlyoffice', 'update',
                'view.php?id={course_module}', '{onlyoffice}');
        $rules[] = new restore_log_rule('onlyoffice', 'view',
                'view.php?id={course_module}', '{onlyoffice}');
        $rules[] = new restore_log_rule('onlyoffice', 'choose',
                'view.php?id={course_module}', '{onlyoffice}');
        $rules[] = new restore_log_rule('onlyoffice', 'choose again',
                'view.php?id={course_module}', '{onlyoffice}');
        $rules[] = new restore_log_rule('onlyoffice', 'report',
                'report.php?id={course_module}', '{onlyoffice}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        // Fix old wrong uses (missing extension)
        $rules[] = new restore_log_rule('onlyoffice', 'view all',
                'index?id={course}', null, null, null, 'index.php?id={course}');
        $rules[] = new restore_log_rule('onlyoffice', 'view all',
                'index.php?id={course}', null);

        return $rules;
    }
}