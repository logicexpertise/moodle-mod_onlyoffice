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
use Firebase\JWT\JWT;

class editor {

    private $courseid;
    private $context;
    private $cm;
    private $modconfig;
    private $file;

    public function __construct($courseid, $context, $cm, $modconfig) {
        $this->courseid = $courseid;
        $this->context = $context;
        $this->cm = $cm;
        $this->modconfig = $modconfig;

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'mod_onlyoffice', 'content', 0, 'sortorder DESC, id ASC', false, 0, 0, 1);

        if (count($files) >= 1) {
            $this->file = reset($files);
        }
    }

    /**
     * @todo Warn if document is in format needing conversion and send to ONLYOFFICE conversion service for conversion and overwrite current version before opening in editor
     * @todo Give user option to continue or abort
     */
    public function config() {
        /*
         * Note: It is important to preserve the case (camelCase) of the $config array keys, as they are used in the config passed to JS
         * Note: Error "too many parameters passed to js_init_call()" occurs in DEBUG_DEVELOPER. See MDL-57614, MDL-62468
         */

        global $CFG, $OUTPUT, $USER;

        if (!isset($this->file) || empty($this->file)) {
            return null;
        }

        $file = $this->file;

        // top level config object
        $config = [];
        $crypt = new crypt();

        // document
        $document = [];
        $filename = $file->get_filename();
        $path = '/' . $this->context->id . '/mod_onlyoffice/content' . $file->get_filepath() . $filename;
        $contenthash = $crypt->get_hash(['userid' => $USER->id, 'contenthash' => $file->get_contenthash()]);
        $documenturl = $CFG->wwwroot . '/pluginfile.php' . $path . '?doc=' . $contenthash;

        $document['url'] = $documenturl;
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $document['fileType'] = $ext;
        $document['title'] = $filename;
        $document['key'] = document::get_key($this->cm);
        $document['permissions'] = document::get_permissions($this->context, $this->cm);

        // editorconfig
        $editorconfig = [];
        $pathnamehash = $crypt->get_hash(['userid' => $USER->id, 'pathnamehash' => $file->get_pathnamehash(), 'cm' => $this->cm]);
        $editorconfig['callbackUrl'] = $CFG->wwwroot . '/mod/onlyoffice/callback.php?doc=' . $pathnamehash;

        // user
        $user = [];
        $user['id'] = $USER->id;
        $user['name'] = \fullname($USER);
        $editorconfig['user'] = $user;
        $editorconfig['lang'] = stristr($USER->lang, '_', true) !== false ? stristr($USER->lang, '_', true) : $USER->lang;

        // customization
        $customization = [];
        $customization['goback']['blank'] = false;
        $customization['goback']['text'] = get_string('returntodocument', 'onlyoffice');
        $customization['goback']['url'] = $CFG->wwwroot . '/course/view.php?id=' . $this->courseid;
        $customization['forcesave'] = true;
        $customization['commentAuthorOnly'] = true;
        $editorconfig['customization'] = $customization;

        // device type
        $devicetype = \core_useragent::get_device_type();
        if ($devicetype == 'tablet' || $devicetype == 'mobile') {
            $devicetype = 'mobile';
        } else {
            $devicetype = 'desktop';
        }

        // package config object from parts
        $config['type'] = $devicetype;
        $config['document'] = $document;
        $config['editorConfig'] = $editorconfig;

        // add token
        if (!empty($this->modconfig->documentserversecret)) {
            $token = JWT::encode($config, $this->modconfig->documentserversecret, 'HS256');
            $config['token'] = $token;
        }
        return $config;
    }

}