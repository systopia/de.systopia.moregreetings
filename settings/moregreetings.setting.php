<?php
/*-------------------------------------------------------+
| SYSTOPIA - MORE GREETINGS EXTENSION                    |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
|         P. Batroff (batroff@systopia.de)               |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

return array(
  'moregreetings_job_status' => array(
    'group_name' => 'moregreetings',
    'group' => 'moregreetings',
    'name' => 'moregreetings_job_status',
    'type' => 'String',
    'html_type' => 'text',
    'default' => '0',
    'title' => 'MoreGreetings Job Status',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => "Defines the current status of the MoreGreetings cronjob",
  ),
  'moregreetings_templates' => array(
    'group_name' => 'moregreetings',
    'group' => 'moregreetings',
    'name' => 'moregreetings_templates',
    'default' => '0',
    'title' => 'MoreGreetings Templates',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => "Stores the templates",
  )
);