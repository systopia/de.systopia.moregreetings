{*-------------------------------------------------------+
| SYSTOPIA - MORE GREETINGS EXTENSION                    |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
|         P. Batroff (batroff@systopia.de)               |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+-------------------------------------------------------*}

{crmScope extensionKey='de.systopia.moregreetings'}
<div class="crm-section">
  {capture assign=help_title}{ts}Number of Extra Greetings{/ts}{/capture}
  <div class="label">{$form.greeting_count.label} {help id="id-count-help" title=$help_title}</div>
  <div class="content">{$form.greeting_count.html}</div>
  <div class="clear"></div>
</div>


{foreach from=$greetings_count item=i}
<div class="crm-section">
  {capture assign=greetings_key}greeting_smarty_{$i}{/capture}
  {capture assign=help_title}{ts}Instructions{/ts}{/capture}
  <div class="label">{$form.$greetings_key.label} {help id="id-token-help" title=$help_title}</div>
  <div class="content">{$form.$greetings_key.html}</div>
  <div class="clear"></div>
</div>
{/foreach}


{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

<br/>
<div class="crm-section">
  <p>{ts 1=$group_url}If you want to change the labels of the indivdual greetings, you can do so <b><a href="%1">here</a></b> (admin permissions required).{/ts}
</div>
{/crmScope}
