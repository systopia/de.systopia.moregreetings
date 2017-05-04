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


/**
 * Adjusts the create/edit form
 *
 * CAUTION: similar algorithm than 'render_moregreetings_edit.js'.
 *   please propagate fixes to that file
 */
cj(document).ready(function() {
  var group = cj("#more_greetings_group");

  // first: hide the original values
  group.find("input.crm-form-radio").parent().parent().hide();

  // then: add protection checkboxes
  group.find("input.crm-form-text").each(function() {
    cj(this).after('&nbsp;<span style="display: inline-block" class="ui-icon ui-icon-locked"/><input title="WRITE_PROTECTION_TS" class="crm-form-checkbox moregreetings-protector" type="checkbox"><span>WRITE_PROTECTION_TS</span>');
  });


  // set them all to the correct value and add a listener
  group.find("input.moregreetings-protector").each(function() {
    // copy protection value to checkbox
    var protected = cj(this).closest("tr").next().find("input:checked").val();
    if (protected == '1') {
      cj(this).attr("checked", "checked");
    }

    // copy events to the radio buttons
    cj(this).change(function() {
      var new_value = cj(this).prop("checked");
      if (new_value) {
        cj(this).closest("tr").next().find("input.crm-form-radio[value=1]").prop('checked', true);
      } else {
        cj(this).closest("tr").next().find("input.crm-form-radio[value=0]").prop('checked', true);
      }
    });
  });
});
