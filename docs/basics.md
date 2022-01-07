# Basics

By default, CiviCRM allows you to define a syntax for generating personalized
greetings per contact (postal greeting and email greeting). With MoreGreetings
the number of available greetings can be extended up to nine. The additional
greetings for each contact will be stored in custom fields that can be labelled
as required.

In addition, more complex logic to generate the greetings can be specified in
these fields using Smarty (See the [Smarty documentation]
(https://www.smarty.net)). MoreGreetings automatically checks whether the
syntax has been entered correctly or whether there is an error. The default
limitation of 240/255 characters for default greetings does not apply.

Once the rule for a new greeting has been correctly created, all contacts in
CiviCRM can be updated accordingly. If desired, one can also manually overwrite
a greeting form field for a specific contact and protect that manual change
(write protection).
