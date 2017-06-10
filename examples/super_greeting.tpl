{if 'fr_FR' == $contact.preferred_language}
  {if $contact.contact_type == 'Organization'}
    Monsieur/Madam
  {else}
    {if $contact.gender_id == '1'}
      {if $contact.formal_title == 'Prof.'}
        Cher Professeure {$contact.last_name}
      {elseif $contact.formal_title == 'Dr.'}
        Cher Docteure {$contact.last_name}
      {else}
        Cher Madame {$contact.last_name}
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
        Cher Monsieur/Madam {$contact.last_name}
      {/if}
    {/if}
  {/if}
{elseif 'es_ES' == $contact.preferred_language}
  {if $contact.contact_type == 'Organization'}
    Estimados Colegas
  {else}
    {if $contact.gender_id == '1'}
      {if $contact.formal_title == 'Prof.'}
        Estimada Profesora {$contact.last_name}
      {elseif $contact.formal_title == 'Dr.'}
        Estimada Doctora {$contact.last_name}
      {else}
        Estimada Señora {$contact.last_name}
      {/if}
    {elseif $contact.gender_id == '2'}
      {if $contact.formal_title == 'Prof.'}
        Estimado Profesor {$contact.last_name}
      {elseif $contact.formal_title == 'Dr.'}
        Estimado Doctor {$contact.last_name}
      {else}
        Estimado Señor {$contact.last_name}
      {/if}
    {else}
      {if $contact.formal_title == 'Prof.'}
        Estomado/a Profesor/a {$contact.last_name}
      {elseif $contact.formal_title == 'Dr.'}
        Estimado/a Doctor/a {$contact.last_name}
      {else}
        Estimados Colegas {$contact.last_name}
      {/if}
    {/if}
  {/if}
{else}{* 'en_US' *}
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
{/if}
