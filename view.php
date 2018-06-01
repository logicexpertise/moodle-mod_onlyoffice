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
 * Load document into ONLYOFFICE editor
 *
 * @package    mod_onlyoffice
 * @copyright  2018 Olumuyiwa Taiwo <muyi.taiwo@logicexpertise.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // Resource instance ID
$redirect = optional_param('redirect', 0, PARAM_BOOL);

if ($id) {
    $cm = get_coursemodule_from_id('onlyoffice', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $onlyoffice = $DB->get_record('onlyoffice', array('id' => $cm->instance), '*', MUST_EXIST);
}
else if ($n) {
    $onlyoffice = $DB->get_record('onlyoffice', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $onlyoffice->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('onlyoffice', $onlyoffice->id, $course->id, false, MUST_EXIST);
}
else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = CONTEXT_MODULE::instance($cm->id);
require_capability('mod/onlyoffice:view', $context);

$event = \mod_onlyoffice\event\course_module_viewed::create(array(
            'objectid' => $PAGE->cm->instance,
            'context' => $PAGE->context,
        ));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $onlyoffice);
$event->trigger();

$PAGE->set_url('/mod/onlyoffice/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($onlyoffice->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
echo $OUTPUT->heading($cm->name);
echo html_writer::start_div('', array('class' => 'onlyoffice-container')); // onlyoffice-container
$modconfig = get_config('onlyoffice');
$documentserverurl = $modconfig->documentserverurl;
$editor = new \mod_onlyoffice\editor($course, $context, $cm, $modconfig);
$editorconfig = $editor->config();
/**
 * @todo warn if document is in format needing conversion. send to ONLYOFFICE conversion service for conversion and overwrite current version before opening in editor
 */
if (empty($editorconfig)) {
    echo $OUTPUT->notification('missing or invalid file');
} else if (!isset($documentserverurl) || empty($documentserverurl)) { // TODO: Test for document server connectivity
    echo $OUTPUT->notification(get_string('docserverunreachable', 'onlyoffice'), 'error');
} else {
    echo html_writer::div('', '', array('id' => 'onlyoffice-editor'));
    echo html_writer::tag('script', '', ['type' => 'text/javascript', 'src' => $documentserverurl. '/web-apps/apps/api/documents/api.js']);
    $PAGE->requires->js_call_amd('mod_onlyoffice/editor', 'init', [$editorconfig]);
}
echo html_writer::end_div(); // onlyoffice-container

echo $OUTPUT->footer();
