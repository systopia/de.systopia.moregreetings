{* Example to get related contact person of an organization with crmAPI *}
{* in this case we search for relationship_type_id 15                   *}

{if $contact.contact_type == 'Organization'}
{strip}
  {crmAPI var='result' entity='Relationship' action='get' return="contact_id_b" relationship_type_id=15 contact_id_a=$contact.contact_id}
  {foreach from=$result.values item=relationship}
    {if $smarty.foreach.relationship.first}
      {assign var='b' value=$relationship.contact_id_b}
    {/if}
  {/foreach}
  {* if we have a contact person *}
  {if $b != ''}
    {crmAPI var='contacts' entity='Contact' action='get' return="first_name,last_name,formal_title,individual_prefix,communication_style" id=$b}
    {foreach from=$contacts.values item=val}
      {if $val.individual_prefix == 'Frau'}Frau
        {if $val.formal_title} {$val.formal_title}{/if} {$val.last_name}
      {elseif $val.individual_prefix == 'Herr'}Herrn
        {if $val.formal_title} {$val.formal_title}{/if} {$val.last_name}
      {/if}
    {/foreach}
  {/if}
{/strip}
{/if}

