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
 * @package     mod_onlyoffice
 * @subpackage
 * @copyright   2019 Olumuyiwa Taiwo <muyi.taiwo@logicexpertise.com>
 * @author      Olumuyiwa Taiwo {@link https://moodle.org/user/view.php?id=416594}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

$courseid = required_param('courseid', PARAM_INT);
$cmid = required_param('cmid', PARAM_INT);

$context = CONTEXT_MODULE::instance($cmid);
require_capability('mod/onlyoffice:view', $context);

$modconfig = get_config('onlyoffice');
$modinfo = get_fast_modinfo($courseid);
$cm = $modinfo->get_cm($cmid)->get_course_module_record();
$editor = new \mod_onlyoffice\editor($courseid, $context, $cm, $modconfig);
$editorconfig = $editor->config();
echo json_encode($editorconfig);
die();