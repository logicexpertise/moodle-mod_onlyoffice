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
/**
 * @todo Log disconnection (editor close) for respective user. note, editor open (connection) is logged in view.php
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

define('AJAX_SCRIPT', true);

$doc = required_param('doc', PARAM_RAW);
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Robots-Tag: noindex');
header('Content-Encoding: UTF-8');
header("Last-Modified: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
header('Expires: ' . gmdate('D, d M Y H:i:s', 0) . 'GMT');
header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
header("Pragma: no-cache");

$response = [];
$response['status'] = 'success';
$response['error'] = 1;

$response['error'] = 1;
if (empty($doc)) {
    $response['error'] = 'Bad request';
    die(json_encode($response));
}

$crypt = new \mod_onlyoffice\crypt();
list($hash, $error) = $crypt->read_hash($doc);

if ($error || $hash == null) {
    die(json_encode($response));
}

$request = file_get_contents('php://input');
if ($request === false) {
    die(json_encode($response));
}

$data = json_decode($request, true);

if ($data === null) {
    die(json_encode($response));
}

if (isset($data['status'])) {
    $status = (int) $data['status'];
    switch ($status) {
        case mod_onlyoffice\util::STATUS_NOTFOUND:
            $response['error'] = 1;
            break;

        case mod_onlyoffice\util::STATUS_MUSTSAVE:
        case mod_onlyoffice\util::STATUS_CORRUPTED:
            // Save to Moodle.
            $downloadurl = $data['url'];
            $fs = get_file_storage();
            if ($file = $fs->get_file_by_hash($hash->pathnamehash)) {
                $fr = array(
                    'contextid' => $file->get_contextid(),
                    'component' => $file->get_component(),
                    'filearea' => 'draft',
                    'itemid' => $file->get_itemid(),
                    'filename' => $file->get_filename(),
                    'filepath' => '/',
                    'userid' => $file->get_userid(),
                    'timecreated' => $file->get_timecreated(),
                    'timemodified' => time());
                $newfile = $fs->create_file_from_url($fr, $downloadurl);

                $file->replace_file_with($newfile);
                $newfile->delete();
                // Generate new key.
                try {
                    \mod_onlyoffice\document::set_key($hash->cm);
                    $response['error'] = 0;
                } catch (\moodle_exception $e) {
                    $response['error'] = 1;
                }
                // TODO: Log document saved with new key
            } else {
                $response['error'] = 1;
            }
            break;

        case mod_onlyoffice\util::STATUS_FORCESAVE:
        case mod_onlyoffice\util::STATUS_ERRORFORCESAVE:

            $response['error'] = 0;

            break;

        case mod_onlyoffice\util::STATUS_EDITING:
        case mod_onlyoffice\util::STATUS_CLOSEDNOCHANGES:
            $response['error'] = 0;
            break;

        default:
            $response['error'] = 1;
    }
}
die(json_encode($response));
