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

namespace mod_onlyoffice\privacy;

use core_privacy\local\metadata\collection;

defined('MOODLE_INTERNAL') || die();

class provider implements \core_privacy\local\metadata\provider,
        \core_privacy\local\request\data_provider {

    public static function get_metadata(collection $collection): collection {

        // Here you will add more items into the collection.
        $collection->add_subsystem_link(
                'core_files', [], 'privacy:metadata:core_files'
        );

        $collection->add_external_location_link(
                'onlyoffice',
                [
                    'userid' => 'privacy:metadata:onlyoffice:userid',
                ], 'privacy:metadata:onlyoffice'
        );

        return $collection;
    }

}