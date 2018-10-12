# CiviCRM Extension for additional greetings: More Greetings

The extension will allow you to set up additional greetings (i.e. for mailings
or data exports) that are automatically created and updated depending on
configuration and data in your CiviCRM installation.


## Features

- Provides up to 9 additional contact greeting fields
- Allows to configure the rules for the creation of the greetings using
  SMARTY-based logic on the data already in CiviCRM
- Gives the option to update added greeting fields for all contacts in the
  database


## Installation

Simply download and install this extension.


## Usage

- Go to Administer -> Administration Console (under Communications section)
- You can decide how many additional greeting fields will be created by
  changing the number on the top.
- Once the fields are created you can change the names in Administer ->
  Customize data and screens -> Custom fields.
- The newly added fields are visible on the contact summary screen as a custom
  field set.
- To define the rules which the creation of the greatings will be based on, you
  need to use SMARTY (examples can be found in the
  [repository](https://github.com/systopia/de.systopia.moregreetings/tree/master/examples)).
- The extension will control the correctness of the SMARTY code - if you make a
  mistake it will not allow you to save the changes.
- Clicking "Save & apply to all contacts" will update all of the additional
  greeting fields in CiviCRM.
- You can change a greeting manually by clicking "Edit" on the "More greetings"
  data set on the contact summary screen.
- Manual changes will be overwritten the next time someone will use
  "Save & apply to all contacts".
- Also, every time the fields that are involved in the greeting creation rule
  are changed (i.e. prefix, first name, last name), the appropriate greeting
  will be updated.
- If you want to prevent the overwrite, check the padlock next to the greeting
  you want to protect on the contact summary screen.
