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
 * Life Cycle Last Action in Course Trigger
 *
 * @package lifecycletrigger_lastactionincourse
 * @subpackage lastactionincourse
 * @copyright  2022 Melanie Treitinger, Ruhr-Universität Bochum <melanie.treitinger@ruhr-uni-bochum.de>
 *             based on 2017 Tobias Reischmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lifecycle\trigger;

use tool_lifecycle\local\manager\settings_manager;
use tool_lifecycle\local\response\trigger_response;
use tool_lifecycle\settings_type;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../lib.php');

/**
 * Class which implements the basic methods necessary for a cleanyp courses trigger subplugin
 * @package lifecycletrigger_lastactionincourse
 */
class lastactionincourse extends base_automatic {

    /**
     * Checks the course and returns a repsonse, which tells if the course should be further processed.
     * @param object $course to be processed.
     * @param int $triggerid id of the trigger instance.
     * @return trigger_response
     */
    public function check_course($course, $triggerid) {
        return trigger_response::trigger();
    }

    /**
     * Specifies the condition on which the trigger is called.
     * @param int $triggerid
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_course_recordset_where($triggerid) {
        global $DB;
        $delay = settings_manager::get_settings($triggerid, settings_type::TRIGGER)['lastactionincoursedelay'];
        $where = "{course}.id IN
                    (SELECT courseid FROM {lifecycletrigger_lastaction} WHERE timemodified < :lastactionincoursedelay)";
        $params = array(
                "lastactionincoursedelay" => time() - $delay,
        );

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

    /**
     * Return the subplugin name.
     * @return string
     */
    public function get_subpluginname() {
        return 'lastactionincourse';
    }

}
