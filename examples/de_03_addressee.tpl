{* capture fields from contact *}
{capture assign=contact_type}{$contact.contact_type}{/capture}
{capture assign=communication_style}{$contact.communication_style_id}{/capture}
{capture assign=addressee}{$contact.addressee_custom}{/capture}
{capture assign=display_name}{$contact.display_name}{/capture}
{if $contact_type == 'Individual'}
    {capture assign=first_name}{$contact.first_name}{/capture}
    {capture assign=last_name}{$contact.last_name}{/capture}
    {capture assign=gender}{$contact.gender_id}{/capture}

    {* possible values of field gender *}
    {assign var='female' value=1}
    {assign var='male' value=2}
    {assign var='neutral' value=3}

    {* default value of field gender, if empty *}
    {if !$gender}
        {assign var='gender' value=$neutral}
    {/if}
{elseif $contact_type == 'Household'}
    {capture assign=household_name}{$contact.household_name}{/capture}
{elseif $contact_type == 'Organization'}
    {capture assign=organization_name}{$contact.organization_name}{/capture}
    {capture assign=legal_name}{$contact.legal_name}{/capture}
{/if}

{* possible values of field communication_style *}
{assign var='formal' value=1}
{assign var='familiar' value=2}

{* default value of field communication_style, if empty *}
{if !$communication_style}
    {assign var='communication_style' value=$formal}
{/if}

{* Prefer individually configured addressee, if existent *}
{if $addressee}
    {$addressee}

{* Household *}
{elseif $contact_type == 'Household'}
    {* prefer household_name over display_name *}
    {if $household_name}
        {$household_name}
    {else}
        {$display_name}
    {/if}

{* Organization *}
{elseif $contact_type == 'Organization'}
    {* prefer legal_name over organization_name over display_name *}
    {if $legal_name}
        {$legal_name}
    {elseif $organization_name}
        {$organization_name}
    {else}
        {$display_name}
    {/if}

{* Individual with full name (first and last name) *}
{elseif ($contact_type == 'Individual') && $first_name && $last_name}
    {if ($communication_style == $formal) && ($gender == $female)}
        Frau {$prefix} {$first_name} {$last_name} {$suffix}

    {elseif ($communication_style == $formal) && ($gender == $male)}
        Herrn {$prefix} {$first_name} {$last_name} {$suffix}

    {elseif ($communication_style == $formal) && ($gender == $neutral)}
        {$prefix} {$first_name} {$last_name} {$suffix}

    {elseif ($communication_style == $familiar) && ($gender == $female)}
        {$first_name} {$last_name}

    {elseif ($communication_style == $familiar) && ($gender == $male)}
        {$first_name} {$last_name}

    {elseif ($communication_style == $familiar) && ($gender == $neutral)}
        {$first_name} {$last_name}

    {* should not happen *}
    {else}
        {$display_name}
    {/if}

{* Individual without first name *}
{elseif ($contact_type == 'Individual') && !$first_name && $last_name}
    {if ($communication_style == $formal) && ($gender == $female)}
        Frau {$prefix} {$last_name} {$suffix}

    {elseif ($communication_style == $formal) && ($gender == $male)}
        Herrn {$prefix} {$last_name} {$suffix}

    {elseif ($communication_style == $formal) && ($gender == $neutral)}
        {$prefix} {$last_name} {$suffix}

    {elseif ($communication_style == $familiar) && ($gender == $female)}
        Frau {$last_name}

    {elseif ($communication_style == $familiar) && ($gender == $male)}
        Herrn {$last_name}

    {elseif ($communication_style == $familiar) && ($gender == $neutral)}
        {$last_name}

    {* should not happen *}
    {else}
        {$display_name}
    {/if}

{* Individual without last name *}
{elseif ($contact_type == 'Individual') && $first_name && !$last_name}
    {if ($communication_style == $formal) && ($gender == $female)}
        {$first_name}

    {elseif ($communication_style == $formal) && ($gender == $male)}
        {$first_name}

    {elseif ($communication_style == $formal) && ($gender == $neutral)}
        {$first_name}

    {elseif ($communication_style == $familiar) && ($gender == $female)}
        {$first_name}

    {elseif ($communication_style == $familiar) && ($gender == $male)}
        {$first_name}

    {elseif ($communication_style == $familiar) && ($gender == $neutral)}
        {$first_name}

    {* should not happen *}
    {else}
        {$display_name}
    {/if}

{* Individual without first name and without last name *}
{elseif ($contact_type == 'Individual') && !$first_name && !$last_name}
    {if ($communication_style == $formal) && ($gender == $female)}
        {$display_name}

    {elseif ($communication_style == $formal) && ($gender == $male)}
        {$display_name}

    {elseif ($communication_style == $formal) && ($gender == $neutral)}
        {$display_name}

    {elseif ($communication_style == $familiar) && ($gender == $female)}
        {$display_name}

    {elseif ($communication_style == $familiar) && ($gender == $male)}
        {$display_name}

    {elseif ($communication_style == $familiar) && ($gender == $neutral)}
        {$display_name}

    {* should not happen *}
    {else}
        {$display_name}
    {/if}

{* should not happen *}
{else}
    {$display_name}
{/if}
