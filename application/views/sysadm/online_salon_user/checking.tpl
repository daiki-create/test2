
<div class="card w1200">
    <div class="card-header amber darken-4">
        Onlineサロン入会審査待ち
    </div>
    <div class="card-body">

        {include file="../../common/_alert.tpl"}

        <table class="table table-sm table-striped table-hover table-fixed border">
        <thead>
            <tr class="thead-light">
                <th class="w70">
                </th>
                <th class="w160">
                    氏名
                </th>
                <th class="">
                    ログインID (メールアドレス)
                </th>
                <th class="w160">
                    仮登録日時
                </th>
                <th class="">
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach $online_salon_checking_users as $online_salon_user}
            <tr class="middle">
                <td class="text-center h6">
                    <a href="/{$module}/{$class}/detail/{$online_salon_user.id}/">
                    {if $online_salon_user.sns_id}
                        <img src="https://graph.facebook.com/{$online_salon_user.sns_id}/picture" alt="">
                    {/if}
                    </a>
                </th>
                <td class="text-truncate">
                    <a href="/{$module}/{$class}/detail/{$online_salon_user.id}/">
                    {$online_salon_user.name|escape}
                    </a>
                </td>
                <td class="text-truncate">
                    <a href="/{$module}/{$class}/detail/{$online_salon_user.id}/">
                    {$online_salon_user.loginid|escape}
                    </a>
                </td>
                <td class="">
                    <a href="/{$module}/{$class}/detail/{$online_salon_user.id}/">
                    {$online_salon_user.created_at|date_format:'%Y/%m/%d %H:%M'}
                    {if $online_salon_user.online_salon_activate_at}
                        <span class="text-danger">再登録</span>
                    {/if}
                    </a>
                </td>
                <td class="text-center h6">
                    <div class="row">
                        <div class="col-8 text-center">
                            <button type="button" class="activate-btn btn btn-primary p-2" data-stylist-id="{$online_salon_user.id}" >
                                有効にする
                            </button>
                        </div>
                        <div class="col-4 text-center">
                            <button type="button" class="deactivate-btn btn btn-outline-danger p-2" data-stylist-id="{$online_salon_user.id}">
                                拒否
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="5" class="text-muted"> 入会審査待ちはありません。 </td>
            </tr>
            {/foreach}
        </tbody>
        </table>

    </div>
</div>

<form id="activate-form" method="POST" action="/{$module}/{$class}/activate">
    <input type="hidden" name="stylist_id" >
</form>
<form id="deactivate-form" method="POST" action="/{$module}/{$class}/deactivate">
    <input type="hidden" name="stylist_id" >
</form>


