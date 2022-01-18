<div class="card mb-3">
    <div class="card-header page-title">
        <i class="fas fa-users fa-lg"></i>&nbsp;
        スタイリスト 一覧
    </div>
</div>
<div class="card">

    {include file="../../common/_alert.tpl"}

    <table id="stylist-list-table" class="table table-fixed border-bottom">
        <thead>
            <tr class="thead-light">
                <th class="middle w180">
                    スタイリスト名
                </th>
                <th class="middle text-center w160 d-none d-sm-table-cell">
                    電話番号
                </th>
                <th class="middle d-none d-sm-table-cell">
                    ログインID
                </th>
                <th class="text-center px-1 w60 d-none d-sm-table-cell">
                    管理者
                </th>
                <th class="w80">
                </th>
            </tr>
        </thead>
        <tbody id="stylist-list-tbody">
            {foreach $stylists as $stylist}

            <tr class="clickable" data-href="/{$module}/{$class}/detail/{$stylist.id}/">
                <td class="stylist-name clickable" data-toggle="tooltip" title="{$stylist.kana|escape|default:''}">
                    <a href="/{$module}/{$class}/detail/{$stylist.id}/">{$stylist.name|escape|default:''}</a>
                </td>
                <td class="text-center stylist-phone d-none d-sm-table-cell">
                    <a href="/{$module}/{$class}/detail/{$stylist.id}/">{$stylist.phone|escape|default:''}</a>
                </td>
                <td class="text-truncate stylist-loginid d-none d-sm-table-cell">
                    <a href="/{$module}/{$class}/detail/{$stylist.id}/">{$stylist.loginid|escape|default:''}</a>
                </td>
                <td class="text-center manager-flag d-none d-sm-table-cell">
                    <a href="/{$module}/{$class}/detail/{$stylist.id}/">
                        {if $stylist.manager_flag}
                        <i class="far fa-circle fa-lg text-warning"></i>
                        {/if}
                    </a>
                </td>
                <td class="text-center h5">
                    <a href="/{$module}/{$class}/detail/{$stylist.id}/">
                        {if empty($stylist.agreement_flag)}
                            <span class="badge badge-light status-inactive">仮登録</span>
                        {elseif empty($stylist.status)}
                            <span class="badge grey darken-1 text-white status-inactive">無効</span>
                        {/if}
                    </a>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>

</div>

