    <form method="get" action="index.php" accept-charset="utf-8" class="search">
        <input type="text" name="search" class="searchfield" id="searchfield"
               value="{$smarty.request.search|h}" />
        <input type="submit" value="{$lang.search}" class="button" />
    </form>
    <form method="get" action="index.php" accept-charset="utf-8" class="search">
        Account:
        <!--<input type="text" name="manager" class="searchfield" id="searchfield"
               value="{$smarty.request.manager|h}" /> -->
	<select name="manager" class="input">
          <option value="">--- {$lang.select} ---</option>
          {html_options options=$managers selected=$entry.manager}
	</select>
        <input type="submit" value="{$lang.search}" class="button" />
    </form>
{if $fields._marker}
    <form method="get" action="index.php" accept-charset="utf-8" class="tags">
        <a href="tags.php" class="tag">{$lang.marker}</a>:

        <input name="marker" class="searchfield" type="text" id="taglookup"
               value="{$smarty.request.marker|h}" />
        <input type="submit" value="{$lang.search}" class="button" />
    </form>
{/if}

