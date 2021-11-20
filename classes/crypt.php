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

use mod_onlyoffice\util;

class crypt {

    /**
     * The secret key from the application configuration
     *
     * @var string
     */
    private $appkey;

    public function __construct() {
        $this->appkey = util::get_appkey();
    }

    public function get_hash($object) {
        $primarykey = json_encode($object);
        $hash = $this->signature_create($primarykey);
        return $hash;
    }

    public function read_hash($hash) {
        $result = null;
        $error = null;
        if ($hash === null) {
            return [$result, "hash is empty"];
        }
        try {
            $payload = base64_decode($hash);
            $payloadparts = explode("?", $payload, 2);

            if (count($payloadparts) === 2) {
                $encode = base64_encode(hash("sha256", ($payloadparts[1] . $this->appkey), true));

                if ($payloadparts[0] === $encode) {
                    $result = json_decode($payloadparts[1]);
                } else {
                    $error = "hash not equal";
                }
            } else {
                $error = "incorrect hash";
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        return [$result, $error];
    }

    private function signature_create($key) {
        $payload = base64_encode(hash("sha256", ($key . $this->appkey), true)) . "?" . $key;
        $base64str = base64_encode($payload);
        return $base64str;
    }

}