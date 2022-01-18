
<div class="card w1200">
    <div class="card-header amber darken-4">
        Onlineサロンユーザ
    </div>
    <div class="card-body">

        {include file="../../common/_alert.tpl"}

        <div class="tab-content" >
            <div class="card-body" >
                <form id="search-form" method="POST" action="/{$module}/{$class}/search/">
                    <div class="form-inline mb-2 p-2 border rounded grey lighten-4" style="font-size:90%;" >

                        <label class="ml-2">氏名：</label>
                        <input type="tel" class="form-control mr-4 w130" name="name" value="{$search.name|default:''}" >

                        <label>ログインID：</label>
                        <input type="tel" class="form-control mr-4 w130" name="loginid" value="{$search.loginid|default:''}" >

                        <label>状態：</label>
                        <div class="custom-control custom-checkbox mr-2">
                            <input type="checkbox" id="status-new" class="custom-control-input"
                                   name="status[]" value="new" {if isset($search) && is_array($search.status) && in_array('new', $search.status)} checked{/if} >
                            <label class="custom-control-label" for="status-new">仮登録</label>
                        </div>
                        <div class="custom-control custom-checkbox mr-2">
                            <input type="checkbox" id="status-active" class="custom-control-input"
                                   name="status[]" value="active" {if isset($search) && is_array($search.status) && in_array('active', $search.status)} checked{/if} >
                            <label class="custom-control-label" for="status-active">有効</label>
                        </div>
                        <div class="custom-control custom-checkbox mr-2">
                            <input type="checkbox" id="status-inactive" class="custom-control-input"
                                   name="status[]" value="inactive" {if isset($search) && is_array($search.status) && in_array('inactive', $search.status)} checked{/if} >
                            <label class="custom-control-label" for="status-inactive">無効</label>
                        </div>
                        <div class="custom-control custom-checkbox mr-2">
                            <input type="checkbox" id="status-left" class="custom-control-input"
                                   name="status[]" value="left" {if isset($search) && is_array($search.status) && in_array('left', $search.status)} checked{/if} >
                            <label class="custom-control-label" for="status-left">退会</label>
                        </div>

                        <label class="ml-2" >課金免除：</label>
                        <select class="custom-select" name="charge_ignore" >
                            <option></option>
                            <option {if isset($search.charge_ignore) && $search.charge_ignore == 1}selected{/if} value="1">のみ</option>
                            <option {if isset($search.charge_ignore) && $search.charge_ignore == 2}selected{/if} value="2">除く</option>
                        </select>

                        <button type="submit" class="btn btn-sm btn-info waves-effect ml-3">
                            <i class="fas fa-search"></i> 検索
                        </button>

                    </div>
                </form>
            </div>
        </div>

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
                <th class="w200">
                    入会日時
                </th>
                <th class="w100 text-center">
                    当月決済
                </th>
                <th class="w100 text-center">
                    状態
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach $online_salon_users as $online_salon_user}
            <tr class="clickable middle">
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
                        {if $online_salon_user.online_salon_activate_at}
                            {$online_salon_user.online_salon_activate_at|date_format:'%Y/%m/%d %H:%M'}
                        {/if}
                    </a>
                </td>
                <td class="text-center h6">
                    {if $online_salon_user.current_receipt_status == 'free'}
                    <span class="badge badge-success px-3">無 料</span>
                    {elseif $online_salon_user.current_receipt_status == 'paid'}
                    <span class="badge badge-primary px-3">成 功</span>
                    {elseif $online_salon_user.current_receipt_status == 'ignore'}
                    <span class="badge badge-info px-3">免 除</span>
                    {elseif $online_salon_user.current_receipt_status == 'failed'}
                    <span class="badge badge-danger px-3">失 敗</span>
                    {/if}
                </td>
                <td class="text-center h6">
                    {if !empty($online_salon_user.online_salon_charge_ignore_flag) }
                    <i class="fa fa-star text-info" ></i>
                    {/if}
                    {if $online_salon_user.online_salon_status == 'active'}
                    <span class="badge badge-primary px-3">有 効</span>
                    {elseif $online_salon_user.online_salon_status == 'inactive'}
                    <span class="badge badge-warning px-3">無 効</span>
                    {elseif $online_salon_user.online_salon_status == 'new'}
                    <span class="badge badge-info px-2" >仮登録</span>
                    {elseif $online_salon_user.online_salon_status == 'checking'}
                    <span class="badge badge-secondary px-2">審査中</span>
                    {elseif $online_salon_user.online_salon_status == 'left'}
                    <span class="badge badge-light px-3">退 会</span>
                    {/if}
                </td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="6" class="text-muted"> ユーザが見つかりません。 </td>
            </tr>
            {/foreach}
        </tbody>
        </table>

        <div class="text-center">
            {include file="../../common/_pagination.tpl"}
        </div>


        {*if $smarty.const.ENVIRONMENT != 'production' *}
        <a href="#" id="subscription-dev-bt" class="font-small" >定期課金</a>
        <div id="subscription-dev" >
            <form id="subscription-dev-form" method="POST" action="/{$module}/{$class}/subscription_dev/">
                <input type="text" name="ym" class="orm-control w100 text-center" />
                <input type="submit" class="orm-control" value="実行" />
            </form>
            <div class="font-small text-danger" >
                ※定期課金バッチ起動対象の年月を yyyy-mm で入力
            </div>
        </div>
        {*/if*}

    </div>
</div>

