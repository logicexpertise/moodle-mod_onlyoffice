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

namespace mod_onlyoffice\output\courseformat;

/**
 * Activity badge forum class, used for rendering unread messages.
 *
 * @package    mod_forum
 * @copyright  2023 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activitybadge extends \core_courseformat\output\activitybadge {

    /**
     * This method will be called before exporting the template.
     */
    protected function update_content(): void {
        global $CFG;

        require_once($CFG->dirroot . '/mod/onlyoffice/lib.php');

        $context = \context_module::instance($this->cminfo->id);
        // See if there is at least one file.
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_onlyoffice', 'content', 0,
                'sortorder DESC, id ASC', false, 0, 0, 1);
        if (count($files) >= 1) {
            $file = reset($files);
            $this->content = pathinfo($file->get_filename(), PATHINFO_EXTENSION);
            $this->style = 'file-ext';
        }
    }
}
