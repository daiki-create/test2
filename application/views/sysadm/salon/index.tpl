
<div class="card">
    <div class="card-header amber darken-4">
        サロン一覧
    </div>
    <div class="card-body">

        <table class="table table-hover table-fixed border">
        <thead class="thead-light">
            <tr>
                <th class="w280">
                    サロン名
                </th>
                <th class="w140">
                    電話番号
                </th>
                <th>
                    住所
                </th>
                <th class="w100 text-center">
                    状態
                </th>
            </tr>
        </thead>
        <tbody>
        {foreach $salons as $salon}

        <tr class="clickable" data-salon-id="{$salon.id}">
            <td>
                <a href="/{$module}/{$class}/detail/{$salon.id}/">
                {$salon.name|escape}
                </a>
            </td>
            <td>
                <a href="/{$module}/{$class}/detail/{$salon.id}/">
                {$salon.phone|escape|default:''}
                </a>
            </td>
            <td class="text-truncate">
                <a href="/{$module}/{$class}/detail/{$salon.id}/">
                {$prefecture[$salon.prefecture]|default:''} {$salon.address|escape|default:''}
                </a>
            </td>
            <td class="text-center h6">
                {if $salon.status == '1'} <span class="badge badge-primary">有効</span>
                {else}<span class="badge badge-light">無効</span>
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

