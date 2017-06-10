{if $contact.contact_type == 'Organization'}
  Dear Co-operators
{else}
  {if $contact.gender_id == '1'}
    {if $contact.formal_title == 'Prof.'}
      Dear Prof. {$contact.last_name}
    {elseif $contact.formal_title == 'Dr.'}
      Dear Dr. {$contact.last_name}
    {else}
      Dear Ms. {$contact.last_name}
    {/if}
  {elseif $contact.gender_id == '2'}
    {if $contact.formal_title == 'Prof.'}
      Dear Prof. {$contact.last_name}
    {elseif $contact.formal_title == 'Dr.'}
      Dear Dr. {$contact.last_name}
    {else}
      Dear Mr. {$contact.last_name}
    {/if}
  {else}
    {if $contact.formal_title == 'Prof.'}
      Dear Prof. {$contact.last_name}
    {elseif $contact.formal_title == 'Dr.'}
      Dear Dr. {$contact.last_name}
    {else}
      Dear Mr./Ms. {$contact.last_name}
    {/if}
  {/if}
{/if}
