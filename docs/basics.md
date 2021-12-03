# Basics

By default, CiviCRM offers greetings fields for mailings or postal items in the
contact data. With the extension MoreGreetings (greeting form editor) the number
of greeting fields can be extended up to nine. In addition, user-defined rules
and formulas can be specified in these fields. After creating the new greeting
form fields, custom field names can be assigned for them.

With the extension MoreGreetings the self-defined rules or functions are created
with the help of the Smarty templating language (See
the [Smarty documentation](https://www.smarty.net)
for more information and help about Smarty.

MoreGreetings automatically checks whether these have been entered correctly or
whether there is a syntax error. In addition, the default limitation of 240/255
characters for a default greeting field does not apply to user-defined rules or
functions.

Once the rule for a new greeting form field has been correctly created, all
contacts in CiviCRM can be updated accordingly. If desired, one can also
manually overwrite a greeting form field for a contact. However, as this
interactive change would be deleted during the next data update and set to the
automatically generated value, a checkmark next to the respective greeting form
field can be set to activate the write protection, excluding the greeting field
from re-calculating for this particular contact.
