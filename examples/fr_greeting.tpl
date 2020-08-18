{if $contact.contact_type == 'Organization'}
  Monsieur/Madame
{else}
  {if $contact.gender_id == '1'}
    {if $contact.formal_title == 'Prof.'}
      Chère Professeure {$contact.last_name}
    {elseif $contact.formal_title == 'Dr.'}
      Chère Docteure {$contact.last_name}
    {else}
      Chère Madame {$contact.last_name}
    {/if}
  {elseif $contact.gender_id == '2'}
    {if $contact.formal_title == 'Prof.'}
      Cher Professeur {$contact.last_name}
    {elseif $contact.formal_title == 'Dr.'}
      Cher Docteur {$contact.last_name}
    {else}
      Cher Monsieur {$contact.last_name}
    {/if}
  {else}
    {if $contact.formal_title == 'Prof.'}
      Cher Professeur/Professeure {$contact.last_name}
    {elseif $contact.formal_title == 'Dr.'}
      Cher Docteur/Docteure {$contact.last_name}
    {else}
      Cher Monsieur/Madame {$contact.last_name}
    {/if}
  {/if}
{/if}
