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

namespace mod_onlyoffice;

defined('MOODLE_INTERNAL') || die();

use mod_onlyoffice\crypt;
use mod_onlyoffice\document;
use mod_onlyoffice\JWT\JWT;

class editor {

    var $course;
    var $context;
    var $cm;
    var $modconfig;
    var $file;

    public function __construct($course, $context, $cm, $modconfig) {
        $this->course = $course;
        $this->context = $context;
        $this->cm = $cm;
        $this->modconfig = $modconfig;

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'mod_onlyoffice', 'content', 0, 'sortorder DESC, id ASC', false, 0, 0, 1);

        if (count($files) >= 1) {
            $this->file = reset($files);
        }
    }

    public function config() {
        /*
         * Note: It is important to preserv the case (camelCase) of the $config 
         * array keys, as they are used in the config passed to JS
         * 
         * Note: Error "too many parameters passed to js_init_call()" occurs in DEBUG_DEVELOPER. See MDL-57614, MDL-62468
         */

        global $CFG, $OUTPUT, $USER;

        if (!isset($this->file) || empty($this->file)) {
            return null;
        }

        $file = $this->file;

        // top level config object
        $config = [];
        $crypt = new \mod_onlyoffice\crypt();

        // document
        $document = [];
        $filename = $file->get_filename();
        $path = '/' . $this->context->id . '/mod_onlyoffice/content' . $file->get_filepath() . $filename;
        $hash = $crypt->get_hash(['userid' => $USER->id, 'contenthash' => $file->get_contenthash()]);
        $documenturl = $CFG->wwwroot . '/pluginfile.php' . $path . '?doc=' . $hash;

        $document['url'] = $documenturl;
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $document['fileType'] = $ext;
        $document['title'] = $filename;
        $document['key'] = document::get_key($this->cm);
        $document['permissions'] = document::get_permissions($this->context, $this->cm);

        // editorconfig
        $editorconfig = [];
        $filename = !empty($filename) ? $filename : '';
        $hash = $crypt->get_hash(['userid' => $USER->id, 'pathnamehash' => $file->get_pathnamehash(), 'cm' => $this->cm]);
        $editorconfig['callbackUrl'] = $CFG->wwwroot . '/mod/onlyoffice/callback.php?doc=' . $hash;

        // user
        $user = [];
        $user['id'] = $USER->id;
        $user['name'] = \fullname($USER);
        $editorconfig['user'] = $user;

        // customization
        $customization = [];
        $customization['goback']['text'] = get_string('returntodocument', 'onlyoffice');
        $customization['goback']['url'] = $CFG->wwwroot . '/course/view.php?id=' . $this->course->id;
        $customization['forcesave'] = true;
        $customization['commentAuthorOnly'] = true;
        $editorconfig['customization'] = $customization;

        // device type
        $devicetype = \core_useragent::get_device_type();
        $devicetype = $devicetype == 'tablet' || $devicetype == 'mobile' ? 'mobile' : 'desktop';


        // package config object from parts
        $config['type'] = $devicetype;
        $config['document'] = $document;
        $config['editorConfig'] = $editorconfig;

        // add token
        if (!empty($this->modconfig->documentserversecret)) {
            $token = JWT::encode($config, $this->modconfig->documentserversecret);
            $config['token'] = $token;
        }
        return $config;
    }

}
