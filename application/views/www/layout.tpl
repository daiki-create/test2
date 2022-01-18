<!DOCTYPE html>
<html lang="ja">
<head>
<script>
</script>
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
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M6GJMGC" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
<header class="container-fluid sticky-top hairlogy_header">
{include file="_header.tpl"}
</header>
<main class="container-fluid px-0">

    {include file="{$class}/{$action}.tpl"}

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
