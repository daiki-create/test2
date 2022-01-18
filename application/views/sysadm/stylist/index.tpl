
<div class="card w1200">
    <div class="card-header amber darken-4">
        スタイリスト一覧 (サロン無)
    </div>
    <div class="card-body">

        {include file="../../common/_alert.tpl"}

        <table class="table table-sm table-striped table-hover table-fixed border">
        <thead>
            <tr class="thead-light">
                <th class="w160">
                    氏名
                </th>
                <th class="">
                    ログインID
                </th>
                <th class="w120">
                    電話番号
                </th>
                <th class="w80 text-center">
                    回答数
                </th>
                <th class="w110 small px-0">
                    最終ｱﾝｹｰﾄ回答日時
                </th>
                <th class="w110 small px-1">
                    最終ﾛｸﾞｲﾝ日時
                </th>
                <th class="w110">
                    仮登録日時
                </th>
                <th class="w80 text-center">
                    状態
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach $stylists as $stylist}
            <tr class="clickable middle">
                <td class="text-truncate">
                    <a href="/{$module}/{$class}/detail/{$stylist.id}/">
                    <p class="kana">{$stylist.kana|escape|default:''}</p>
                    {$stylist.name|escape}
                    </a>
                </td>
                <td class="text-truncate">
                    <a href="/{$module}/{$class}/detail/{$stylist.id}/">
                    {$stylist.loginid|escape}
                    </a>
                </td>
                <td class="">
                    <a href="/{$module}/{$class}/detail/{$stylist.id}/">
                    {$stylist.phone|escape|default:''}
                    </a>
                </td>
                <td class="text-center">
                    <a href="/{$module}/{$class}/detail/{$stylist.id}/">
                    {$stylist.reply_count|default:'0'}
                    </a>
                </td>
                <td class="">
                    <a href="/{$module}/{$class}/detail/{$stylist.id}/">
                    {if $stylist.last_replied_at}
                    {$stylist.last_replied_at|date_format:'%Y/%-m/%-d<br>%H:%M'}
                    {/if}
                    </a>
                </td>
                <td class="">
                    <a href="/{$module}/{$class}/detail/{$stylist.id}/">
                    {if $stylist.last_login_at}
                    {$stylist.last_login_at|date_format:'%Y/%-m/%-d<br>%H:%M'}
                    {/if}
                    </a>
                </td>
                <td class="">
                    <a href="/{$module}/{$class}/detail/{$stylist.id}/">
                    {$stylist.created_at|date_format:'%Y/%-m/%-d<br>%H:%M'}
                    </a>
                </td>
                <td class="text-center h6">
                    {if $stylist.agreement_flag}
                        {if $stylist.status}
                        <span class="badge badge-primary px-2">有 効</span>
                        {else}
                        <span class="badge badge-light px-2">停 止</span>
                        {/if}
                    {else}
                        <span class="badge badge-warning">仮登録</span>
                    {/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
        </table>

        <div class="text-center">
            {include file="../../common/_pagination.tpl"}
        </div>

    </div>
</div>

