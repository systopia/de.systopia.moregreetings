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
