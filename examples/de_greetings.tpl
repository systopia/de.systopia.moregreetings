{if $contact.contact_type == 'Organization'}
  Sehr geehrte Damen und Herren,
{else}
  {* preifx_id 3 = "Herr" *}
  {if $contact.prefix_id == '3'}
    {if $contact.formal_title == 'Prof.'}
      Sehr geehrter Herr  Professor {$contact.last_name},
    {elseif $contact.formal_title == 'Prof. Dr.'}
      Sehr geehrter Herr Professor {$contact.last_name},
    {elseif $contact.formal_title == 'Prof. Dr. Dr.'}
      Sehr geehrter Herr Professor {$contact.last_name},
    {elseif $contact.formal_title == 'Prof. Dr.-Ing.'}
      Sehr geehrter Herr Professor {$contact.last_name},
    {elseif $contact.formal_title == 'Dr.'}
      Sehr geehrter Herr Dr. {$contact.last_name},
    {elseif $contact.formal_title == 'Prof. Dr.'}
      Sehr geehrter Herr Professor {$contact.last_name},
    {elseif $contact.formal_title ne ""}
Sehr geehrter Herr  {$contact.formal_title} {$contact.last_name},
    {else}
      Sehr geehrter Herr {$contact.last_name},
    {/if}
  {* preifx_id 1 = "Frau" *}
  {elseif $contact.prefix_id == '1'}
    {if $contact.formal_title == 'Prof.'}
      Sehr geehrte Frau Professorin {$contact.last_name},
    {elseif $contact.formal_title == 'Prof. Dr.'}
      Sehr geehrte Frau Professorin {$contact.last_name},
    {elseif $contact.formal_title == 'Prof. Dr. Dr.'}
      Sehr geehrte Frau Professorin {$contact.last_name},
    {elseif $contact.formal_title == 'Prof. Dr.-Ing.'}
      Sehr geehrte Frau Professorin {$contact.last_name},
    {elseif $contact.formal_title == 'Dr.'}
      Sehr geehrte Frau Dr. {$contact.last_name},
    {elseif $contact.formal_title ne ""}
Sehr geehrte Frau {$contact.formal_title} {$contact.last_name},
    {else}
      Sehr geehrte Frau {$contact.last_name},
    {/if}
  {else}
    {if $contact.formal_title == 'Prof.'}
      Guten Tag Professor/in {$contact.last_name},
    {elseif $contact.formal_title == 'Prof. Dr.'}
      Guten Tag Professor/in {$contact.last_name},
    {elseif $contact.formal_title == 'Dr.'}
      Guten Tag Dr. {$contact.last_name},
    {else}
      Guten Tag {$contact.first_name} {$contact.last_name},
    {/if}
  {/if}
{/if}
