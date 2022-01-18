<!DOCTYPE html>
<html lang="ja">
<head>
<link rel="icon" href="/img/favicon_orange.ico">
{include file="../common/_head.tpl"}
<script>
var MC = {
    module: '{$module}',
    class: '{$class}',
    action: '{$action}'

};
{if ! empty($js_data)}
{($js_data)}
{/if}
</script>
{if file_exists("{$smarty.current_dir}/{$class}/_head.tpl")}
{include file="{$class}/_head.tpl"}
{/if}
{if file_exists("{$smarty.current_dir}/{$class}/{$action}/_head.tpl")}
{include file="{$class}/{$action}/_head.tpl"}
{/if}
{$option_head}
<title>{$title|escape|default:''}</title>
</head>
<body class="white-skin grey lighten-3">
<header>
{if $class != 'login'}{include file="_header.tpl"}{/if}
</header>
<main class="mt-5 pb-4">
    <div class="container pt-5 mb-5{if $module == 'sysadm'}  d-none d-md-block{/if}">

    {include file="{$class}/{$action}.tpl"}

    </div>
    {if $module == 'sysadm'}
    <div class="container pt-5 mb-5 d-md-none">
	<h3 class="text-center text-warning">
            <strong>PCでアクセスしてね！</strong>
        </h3>
    </div>
    {/if}

</main>
<br></br>
<footer class="page-footer amber darken-4 fixed-bottom">
{include file="_footer.tpl"}
</footer>
{include file="../common/_foot.tpl"}
{include file="../common/_confirm.tpl"}
{if file_exists("{$smarty.current_dir}/{$class}/_foot.tpl")}
{include file="{$class}/_foot.tpl"}
{/if}
{if file_exists("{$smarty.current_dir}/{$class}/{$action}/_foot.tpl")}
{include file="{$class}/{$action}/_foot.tpl"}
{/if}
{if file_exists("{$smarty.current_dir}/{$class}/{$action}/_modal.tpl")}
{include file="{$class}/{$action}/_modal.tpl"}
{/if}
{$option_foot}
</body>
</html>
