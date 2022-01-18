<!DOCTYPE html>
<html lang="ja">
<head>
{include file="_head.tpl"}
{if file_exists("{$smarty.current_dir}/{$class}/_head.tpl")}
{include file="{$class}/_head.tpl"}
{/if}
{if file_exists("{$smarty.current_dir}/{$class}/{$action}/_head.tpl")}
{include file="{$class}/{$action}/_head.tpl"}
{/if}
{$option_head}
</head>
<body>
<header>
{include file="_header.tpl"}
</header>
<main class="container">
{if file_exists("{$smarty.current_dir}/{$class}/{$action}.tpl")}
{include file="{$class}/{$action}.tpl"}
{/if}
</main>
<footer>
{include file="_footer.tpl"}
</footer>
{include file="_foot.tpl"}
{if file_exists("{$smarty.current_dir}/{$class}/_foot.tpl")}
{include file="{$class}/_foot.tpl"}
{/if}
{if file_exists("{$smarty.current_dir}/{$class}/{$action}/_foot.tpl")}
{include file="{$class}/{$action}/_foot.tpl"}
{/if}
{$option_foot}
</body>
</html>
