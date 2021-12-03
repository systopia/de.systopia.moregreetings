# Example of a user-defined greeting field created with Smarty

Here are some notes for a better understanding of the example. You can find a
detailed documentation and reference of the Smarty templating
language [here](https://www.smarty.net).

- Smarty tags are usually enclosed by delimiters. By default, these are `{`
  and `}`.
- Template comments are surrounded by asterisks. They are not displayed in the
  output.
- In Smarty, variables normally start with a `$` dollar sign.
- Smarty can make use of `{if}` statements. They must be closed by a matching
  `{/if}`. There are the options of adding `{else}` if the condition is not
  satisfied or `{elseif}` for specifying another test condition.
- The logical operator `!` states a logical *Not* and means, in the context of a
  variable, that the condition is satisfied if the content of the variable is
  empty (or has a corresponding value of another type, e.g. `0` or `false`).
- The logical operator `&&` states that both expressions must be true to satisfy
  the condition.
- The `|` character means that Smarty combines several functions and executes
  them one after the other.

In the example here, Smarty first checks the contact type (*Household*,
*Organization*, or *Individual*) of the respective contact. It then
automatically generates the contents of the greeting form field according to the
check conditions.

Especially in the case of an *Individual* contact, Smarty accurately checks
several conditions. Using the *Gender ID* field and the *Prefix ID* field,
Smarty retrieves the gender of the respective contact and adjusts the greeting
formula accordingly. Then, in each case an `if` condition is used to check the
academic title and to add it at the beginning of the greeting formula
accordingly.

Note that this carries out a German example, since there's a lot of
differentiating necessary between male and female salutations, and also
alternating endings for different genders.

```smarty
{* prefix IDs: 5 --> female, 6 --> male *}
{* gender IDs: 1 --> female, 2 --> male, 3 --> undefined, NULL *}
{* -------------------------------------------------------------------------- *}

{if $contact.contact_type == 'Household'}
  Sehr geehrte Familie {$contact.last_name}

{elseif $contact.contact_type == 'Organization'}
  Sehr geehrte Damen und Herren

{else}

    {if !$contact.first_name && !$contact.last_name}
        {if $contact.formal_title|mg_startswith:'Prof'}
          Sehr geehrte/r Professor/in
        {elseif $contact.formal_title|mg_startswith:'Dr'}
          Sehr geehrte/r Dr.
        {elseif $contact.formal_title|mg_startswith:'PD Dr'}
          Sehr geehrte/r Dr.
        {else}
          Sehr geehrte Damen und Herren
        {/if}

    {elseif !$contact.gender_id}
        {if $contact.prefix_id == '5'}
            {if $contact.formal_title|mg_startswith:'Prof'}
              Sehr geehrte Frau Professorin {$contact.last_name}
            {elseif $contact.formal_title|mg_startswith:'Dr'}
              Sehr geehrte Frau Dr. {$contact.last_name}
            {elseif $contact.formal_title|mg_startswith:'PD Dr'}
              Sehr geehrte Frau Dr. {$contact.last_name}
            {else}
              Sehr geehrte Frau {$contact.formal_title} {$contact.last_name}
            {/if}

        {elseif $contact.prefix_id == '6'}
            {if $contact.formal_title|mg_startswith:'Prof'}
              Sehr geehrter Herr Professor {$contact.last_name}
            {elseif $contact.formal_title|mg_startswith:'Dr'}
              Sehr geehrter Herr Dr. {$contact.last_name}
            {elseif $contact.formal_title|mg_startswith:'PD Dr'}
              Sehr geehrter Herr Dr. {$contact.last_name}
            {else}
              Sehr geehrter Herr {$contact.formal_title} {$contact.last_name}
            {/if}

        {else}
            {if $contact.formal_title|mg_startswith:'Prof'}
              Sehr geehrte/r Professor/in {$contact.first_name} {$contact.last_name}
            {elseif $contact.formal_title|mg_startswith:'Dr'}
              Sehr geehrte/r Dr. {$contact.first_name} {$contact.last_name}
            {elseif $contact.formal_title|mg_startswith:'PD Dr'}
              Sehr geehrte/r Dr. {$contact.first_name} {$contact.last_name}
            {else}
              Sehr geehrte/r {$contact.formal_title} {$contact.first_name} {$contact.last_name}
            {/if}
        {/if}

    {elseif $contact.gender_id == '1'}
        {if $contact.formal_title|mg_startswith:'Prof'}
          Sehr geehrte Frau Professorin {$contact.last_name}
        {elseif $contact.formal_title|mg_startswith:'Dr'}
          Sehr geehrte Frau Dr. {$contact.last_name}
        {elseif $contact.formal_title|mg_startswith:'PD Dr'}
          Sehr geehrte Frau Dr. {$contact.last_name}
        {else}
          Sehr geehrte Frau {$contact.formal_title} {$contact.last_name}
        {/if}

    {elseif $contact.gender_id == '2'}
        {if $contact.formal_title|mg_startswith:'Prof'}
          Sehr geehrter Herr Professor {$contact.last_name}
        {elseif $contact.formal_title|mg_startswith:'Dr'}
          Sehr geehrter Herr Dr. {$contact.last_name}
        {elseif $contact.formal_title|mg_startswith:'PD Dr'}
          Sehr geehrter Herr Dr. {$contact.last_name}
        {else}
          Sehr geehrter Herr {$contact.formal_title} {$contact.last_name}
        {/if}

    {else}
        {* All other cases* }
        
        {if $contact.formal_title|mg_startswith:'Prof'}
          Sehr geehrte/r Professor/in {$contact.first_name} {$contact.last_name}
        {elseif $contact.formal_title|mg_startswith:'Dr'}
          Sehr geehrte/r Dr. {$contact.first_name} {$contact.last_name}
        {elseif $contact.formal_title|mg_startswith:'PD Dr'}
          Sehr geehrte/r Dr. {$contact.first_name} {$contact.last_name}
        {else}
          Sehr geehrte/r {$contact.formal_title} {$contact.first_name} {$contact.last_name}
        {/if}
    {/if}
{/if}
```

To better understand this greeting form field, the structure of the field is
being visually represented here in the form of an
image: ![graphic](/Images/Example_MoreGreetings.png)
