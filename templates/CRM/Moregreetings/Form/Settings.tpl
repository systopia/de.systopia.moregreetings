{* HEADER *}

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div>

{* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}

{foreach from=$greetings_count item=i}
<div class="crm-section">
  {capture assign=greetings_key}greeting_smarty_{$i}{/capture}
  <div class="label">{$form.$greetings_key.label}</div>
  <div class="content">{$form.$greetings_key.html}</div>
  <div class="clear"></div>
</div>
{/foreach}


{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
