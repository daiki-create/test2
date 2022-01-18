<!DOCTYPE html>
<html lang="ja">

<head>
    {include file="../common/_head.tpl"}
    <link rel="icon" href="/img/favicon_{if $login.manager_flag}green{else}orange{/if}.ico">
    <script>
        var MC = {
            module: '{$module}',
            class: '{$class}',
            action: '{$action}'{if ! empty($login.stylist_id)},
            login: {
                stylist_id: {$login.stylist_id}
            }
            {/if}
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

<body class="{if $login.manager_flag}hairlogy-salon-manager{/if}">
    <header>
        {include file="_header.tpl"}
    </header>
    <main class="mt-5 pb-4">
        <div class="container pt-5 mb-5 px-3 {if $module == 'sysadm'}  d-none d-md-block{/if}">

            {include file="{$class}/{$action}.tpl"}

        </div>
    </main>
    <br></br>
    <footer class="page-footer fixed-bottom">
        {include file="_footer.tpl"}
    </footer>
    {if file_exists("{$smarty.current_dir}/{$class}/{$action}/_modal.tpl")}
    {include file="{$class}/{$action}/_modal.tpl"}
    {/if}
    {include file="../common/_confirm.tpl"}
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
