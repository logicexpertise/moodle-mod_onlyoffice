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
require_once($CFG->dirroot.'/mod/onlyoffice/vendor/firebase/php-jwt/src/JWT.php');
use Firebase\JWT\JWT;

defined('AJAX_SCRIPT') or define('AJAX_SCRIPT', true);

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

$modconfig = get_config('onlyoffice');
if (!empty($modconfig->documentserversecret)) {
    $inHeader = false;
    if (!empty($data["token"])) {
        $token = JWT::decode( $data["token"], $modconfig->documentserversecret, ['HS256', 'HS512', 'HS384',  'RS256', 'RS384',  'RS512']);
    } elseif (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
        $token = JWT::decode(substr($_SERVER['HTTP_AUTHORIZATION'], strlen("Bearer ")), $modconfig->documentserversecret );
        $inHeader = true;
    } else {
        $result["error"] = "Expected JWT";
        return $result;
    }

    if (empty($token)) {
        $result["error"] = "Invalid JWT signature";
        return $result;
    }

    $data = (array)$token;

    if ($inHeader) $data = $data["payload"];
}

if (isset($data['status'])) {
    $status = (int) $data['status'];
    switch ($status) {
        case mod_onlyoffice\util::STATUS_NOTFOUND:
            $response['error'] = 1;
            break;

        case mod_onlyoffice\util::STATUS_MUSTSAVE:
        case mod_onlyoffice\util::STATUS_FORCESAVE:
            // Save to Moodle.
            if (mod_onlyoffice\util::save_document_to_moodle($data, $hash)) {
                $response['error'] = 0;
            } else {
                $response['error'] = 1;
            }
            break;

        case mod_onlyoffice\util::STATUS_ERRORSAVING:
        case mod_onlyoffice\util::STATUS_ERRORFORCESAVE:
            $response['error'] = 1;
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
