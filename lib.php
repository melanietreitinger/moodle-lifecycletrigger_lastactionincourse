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
 * Interface for the subplugintype trigger
 * It has to be implemented by all subplugins.
 *
 * @package lifecycletrigger_lastactionincourse
 * @subpackage lastactionincourse
 * @copyright  2022 Melanie Treitinger, Ruhr-Universität Bochum <melanie.treitinger@ruhr-uni-bochum.de>
 *             based on 2017 Tobias Reischmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lifecycle\trigger;

use tool_lifecycle\response\trigger_response;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../lib.php');

/**
 * Class which implements the basic methods necessary for a cleanyp courses trigger subplugin
 * @package lifecycletrigger_lastactionincourse
 */
class lastactionincourse extends base_automatic {

    /**
     * Checks the course and returns a repsonse, which tells if the course should be further processed.
     * @param $course object to be processed.
     * @param $triggerid int id of the trigger instance.
     * @return trigger_response
     */
    public function check_course($course, $triggerid) {
        return trigger_response::trigger();
    }

    public function get_course_recordset_where($triggerid) {
        global $DB;

        return array($where, $params);
    }

    /**
     * Defines which settings each instance of the subplugin offers for the user to define.
     * @return instance_setting[] containing settings keys and PARAM_TYPES
     */
    public function instance_settings() {
        return array(
                new instance_setting('lastactionincoursedelay', PARAM_INT, true)
        );
    }

    /**
     * Add the lastactionincoursedelay since the start date of a course.
     * @param \MoodleQuickForm $mform
     * @throws \coding_exception
     */
    public function extend_add_instance_form_definition($mform) {
        $mform->addElement('duration', 'lastactionincoursedelay', get_string('lastactionincoursedelay',
                'lifecycletrigger_lastactionincourse'));
        $mform->addHelpButton('lastactionincoursedelay', 'lastactionincoursedelay',
                'lifecycletrigger_lastactionincourse');
    }

    /**
     * Reset the lastactionincoursedelay at the add instance form initializiation.
     * @param \MoodleQuickForm $mform
     * @param array $settings array containing the settings from the db.
     */
    public function extend_add_instance_form_definition_after_data($mform, $settings) {
        if (is_array($settings) && array_key_exists('lastactionincoursedelay', $settings)) {
            $default = $settings['lastactionincoursedelay'];
        } else {
            $default = 36 * 31 * 24 * 3600; // Approx. 36 months.
        }
        $mform->setDefault('lastactionincoursedelay', $default);
    }

    public function get_subpluginname() {
        return 'lastactionincourse';
    }

}
