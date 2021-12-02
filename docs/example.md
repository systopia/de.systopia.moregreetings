# Example of a user-defined greeting field created with SMARTY

Here are some notes for a better understanding of the example. You can find a detailed documentation and reference [here](https://www.smarty.net)).

- SMARTY tags are usually enclosed by delimiters. By default, these are { and }.
- Template comments are surrounded by asterisks. They are not displayed in the output.
- In SMARTY variables start normally with a $ dollar sign.
- SMARTY can make use of {If} statements. They must be closed by a matching {/if}. There are the options of adding {else} if the condition is not satisfied or {elseif} for specifying another test condition. 
- The logical operator ! states a logical Not and means in the context of a variable that the condition is satisfied if the content of the variable will not be empty.
- The logical operator && states that both expressions must be true to satisfy the condition.
- The | character means that SMARY combines several functions and executes them one after the other.

In the example here, SMARTY first checks the contact type (Household, Organization of Individual) of the respective contact. Then it automatically generates the contents of the greeting form field according to the check conditions. 

Especially in the case of an Individual contact, Smarty checks accurately several conditions. Using the Gender ID field and the Prefix-ID field, SMARTY retrieves the gender of the respective contact and adjusts the greeting formula accordingly. Then in each case an if-condition is used to check the academic title and to add it at the beginning of the greeting formula accordingly. 

```
{*prefix IDs: 5 --&gt; weiblich, 6 --&gt; maennlich*}
{*gender IDs: 1 --&gt; weiblich, 2 --&gt; maennlich, 3 --&gt;
unbestimmt, NULL*}
{*---------------------------------------------------*}

{if $contact.contact_type == 'Household'}
    Sehr geehrte Familie {$contact.last_name}

{elseif $contact.contact_type == 'Organization'}
    Sehr geehrte Damen und Herren

{else}
    
    {if !$contact.first_name && !$contact.last_name}
        {if $contact.formal_title|mg_startswith:'Prof'}
            Sehr geehrte/r Professor {$contact.first_name} {$contact.last_name}
        {elseif $contact.formal_title|mg_startswith:'Dr'}
      	     Sehr geehrte/r Dr. {$contact.first_name} {$contact.last_name}
        {elseif $contact.formal_title|mg_startswith:'PD Dr'}
            Sehr geehrte/r Dr. {$contact.first_name} {$contact.last_name}
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
                Sehr geehrte Herr Dr. {$contact.last_name}
            {else}
                Sehr geehrter Herr {$contact.formal_title} {$contact.last_name}
            {/if}
    
	      {else}
            {if $contact.formal_title|mg_startswith:'Prof'}
                Sehr geehrte/r Professor/in {$contact.first_name}
{$contact.last_name}
            {elseif $contact.formal_title|mg_startswith:'Dr'}
                Sehr geehrte/r Dr. {$contact.first_name} {$contact.last_name}
            {elseif $contact.formal_title|mg_startswith:'PD Dr'}
                Sehr geehrte/r Dr. {$contact.first_name} {$contact.last_name}
            {else}
                Sehr geehrte/r {$contact.formal_title} {$contact.first_name}
{$contact.last_name}
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
            Sehr geehrte Herr Dr. {$contact.last_name}
        {else}
            Sehr geehrter Herr {$contact.formal_title} {$contact.last_name}
        {/if}

{*All other cases*}
    {else}
        {if $contact.formal_title|mg_startswith:'Prof'}
            Sehr geehrte/r Professor/in {$contact.first_name}
{$contact.last_name}
        {elseif $contact.formal_title|mg_startswith:'Dr'}
            Sehr geehrte/r Dr. {$contact.first_name} {$contact.last_name}
        {elseif $contact.formal_title|mg_startswith:'PD Dr'}
            Sehr geehrte/r Dr. {$contact.first_name} {$contact.last_name}
        {else}
            Sehr geehrte/r {$contact.formal_title} {$contact.first_name}
{$contact.last_name}
        {/if}
    {/if}
{/if}
```

To better understand this greeting form field, the structure of the field has been visually represented here in the form of a graphic: ![graphic](/Images/Example_MoreGreetings.png)
