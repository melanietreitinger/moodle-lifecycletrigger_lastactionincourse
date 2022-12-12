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

defined('MOODLE_INTERNAL') || die();

$tasks = [
        [
                'classname' => 'lifecycletrigger_lastactionincourse\task\get_lastactionincourse_task',
                'blocking' => 0,
                'minute' => '0',
                'hour' => '3',
                'day' => '*',
                'month' => '*',
                'dayofweek' => '*',
        ],
];
