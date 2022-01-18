<!DOCTYPE html>
<html lang="ja">
<head>
{include file="../common/_head.tpl"}
<link rel="icon" href="/img/favicon_orange.ico">
<script>
var MC = {
    module: '{$module}',
    class: '{$class}',
    action: '{$action}'
};
</script>
{if file_exists("{$smarty.current_dir}/{$class}/_head.tpl")}
{include file="{$class}/_head.tpl"}
{/if}
{if file_exists("{$smarty.current_dir}/{$class}/{$action}/_head.tpl")}
{include file="{$class}/{$action}/_head.tpl"}
{/if}
{$option_head}
</head>
<body>
<!-- <header class="fixed-top">
{include file="_header.tpl"}
</header> -->
<main class="pt-50 pb-100">
    <div class="container">

    {include file="{$class}/{$action}.tpl"}

    </div>
</main>
<footer class="page-footer  orange accent-4 fixed-bottom">
{include file="_footer.tpl"}
</footer>
{include file="../common/_foot.tpl"}
{if file_exists("{$smarty.current_dir}/{$class}/_foot.tpl")}
{include file="{$class}/_foot.tpl"}
{/if}
{if file_exists("{$smarty.current_dir}/{$class}/{$action}/_foot.tpl")}
{include file="{$class}/{$action}/_foot.tpl"}
{/if}
{$option_foot}
</body>
</html>
