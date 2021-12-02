# Basics

By default, CiviCRM offers greetings fields for mailings or postal items in the contact data. With the extension MoreGreetings (greeting form editor) the number of greeting fields can be extended up to nine. In addition, user-defined rules and formulas can be specified in these fields. After creating the new greeting form fields, custom field names can be assigned for them.

With the extension MoreGreetings the self-defined rules or functions are created with the help of SMARTY (more information and help about Smarty you will find [here](https://www.smarty.net)).

MoreGreetings automatically checks whether these have been entered correctly or whether there is a syntax error. 
In addition, the default limitation of 240/255 characters for a default greeting field does not apply to user-defined rules or functions.

Once the rule for a new greeting form field has been correctly created, all contacts in CiviCRM can be updated accordingly. If desired, you can also manually overwrite a greeting form field for a contact. However, this interactive change will be deleted during the next data update and set to the automatically generated value. To prevent this, you can set a checkmark next to the respective greeting form field: This activates the write protection.
