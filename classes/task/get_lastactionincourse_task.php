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

namespace lifecycletrigger_lastactionincourse\task;

/**
 * Scheduled task for lifecycletrigger_lastactionincourse.
 */
class get_lastactionincourse_task extends \core\task\scheduled_task {
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'lifecycletrigger_lastactionincourse');
    }

    /**
     * Returns log table name of preferred reader.
     *
     * @return string table name
     */
    private function get_log_table_name() {
        $logtable = false;
        $logdb = false;
        $logmanager = get_log_manager();
        $readers = $logmanager->get_readers();
        if (empty($readers)) {
            // No readers, no processing.
            return false;
        }
        foreach ($readers as $reader) {
            if ($reader instanceof \core\log\sql_internal_table_reader) {
                $logtable = $reader->get_internal_log_table_name();
            } else if ($reader instanceof \logstore_database\log\store) {
                $logtable = get_config('logstore_database', 'dbtable');
                $store = new \logstore_database\log\store($logmanager);
                $logdb = $store->get_extdb();
            }
        }
        return array($logtable, $logdb);
    }

    /**
     * Execute the task.
     */
    public function execute() {
        mtrace("Task get_lastactionincourse_task started... ");
        global $DB;

        // Get internal log reader.
        list($logtable, $logdb) = $this->get_log_table_name();
        mtrace('Logtable '.serialize($logtable));
        if (false == $logdb) {
            mtrace('Using Standard DB... ');
            $logdb = $DB;
        }
        $logdb->set_debug(true);

        $courses = get_courses('all', 'c.sortorder ASC', 'c.id');
        // mtrace("List all courses: ".serialize($courses));

        foreach ($courses as $course) {
            // Get last log entry for each course.
            if (1 == $course->id) {
                continue;
            }
            // AND userid NOT IN (0,1,2).
            $rs = $logdb->get_recordset_select($logtable, 'courseid=:courseid AND action != "viewed"
                    AND userid NOT IN (0,1)', array('courseid' => $course->id), 'timecreated DESC', 'timecreated',
                    '0', '1');
            mtrace("Last action in course ".$course->id.": ".serialize($rs));

            foreach ($rs as $value) {
                $dataobject = new \stdClass();
                $dataobject->courseid = $course->id;
                $dataobject->timemodified = $value->timecreated;
                $dataobject->lastupdated = time();

                if ($DB->record_exists_select('lifecycletrigger_lastaction', 'courseid = :courseid',
                        array('courseid' => $course->id))) {
                    $id = $DB->get_field_select('lifecycletrigger_lastaction', 'id', 'courseid = :courseid',
                            array('courseid' => $course->id), MUST_EXIST);
                    $dataobject->id = $id;
                    $result = $DB->update_record('lifecycletrigger_lastaction', $dataobject, true);
                } else {
                    $result = $DB->insert_record('lifecycletrigger_lastaction', $dataobject, true);
                }

                mtrace("get_lastactionincourse_task finished with result $result");
            }
            $rs->close();
        }

    }
}
