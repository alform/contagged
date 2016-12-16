{if $entry.type}
<tr class="{cycle values="even,odd"}">
    <td width="25">
        <img src="pix/{$entry.type|h}.png" border="0" width="16" height="16" align="middle"
         alt="{$entry.type|h}" title="{$entry.type|h}" />
    </td>
    <td>
        <b><a href="entry.php?dn={$entry.dn|escape:url}">{$entry.name|h}, {$entry.givenname|h}</a></b>
    </td>
    <td>
        <a href="index.php?org={$entry.organization|escape:url}">{$entry.organization|h}</a>&nbsp;
    </td>
    <td>
        <a href="callto://{$entry.phone|escape:phone}">{$entry.phone|h}</a>&nbsp;
    </td>
    <td>
        <a href="mailto:{$entry.mail[0]|h}">{$entry.mail[0]|h}</a>&nbsp;
    </td>
    <td>
{if $entry.manager}
	{assign var=managerdn value=$entry.manager}
	<!-- ALF: insert key account -->
	<a href="index.php?manager={$entry.manager|escape:url}" class="manager">{$managers.$managerdn|h}</a> 
{/if}
  </td>
{if $entry.marker }
{* if $fields._marker *}
            <td>
              <span id="taglist">
                {foreach from=$entry.marker item=marker}
                  <a href="index.php?marker={$marker|escape:url}" class="tag">{$marker|h}</a>
                {/foreach}
                &nbsp;
              </span>
            </td>
{/if}
    <td width="16">
        {if $entry.photo}
            <a href="img.php?dn={$entry.dn|escape:url}&amp;.jpg" rel="imagebox" target="_blank"
               title="{$entry.givenname|escape} {$entry.name|escape}"><img src="pix/image.png"
               border="0" width="16" height="16" align="middle" alt="{$lang.photo|h}" /></a>
        {else}
            &nbsp;
        {/if}
    </td>
</tr>
{/if}
