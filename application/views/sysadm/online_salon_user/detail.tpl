
<div class="card w800 mx-auto">
    <div class="card-header text-truncate py-3 amber darken-4">
        <ul class="nav nav-tabs" id="report-tab" role="tablist">
            <li class="nav-item waves-effect waves-light">
                <a class="nav-link active" id="stylist-detail-tab" data-toggle="tab" href="#stylist-detail" role="tab" aria-selected="false">
                    会員情報
                </a>
                <div class="w30 mx-auto"></div>
            </li>
            <li class="nav-item waves-effect waves-light">
                <a class="nav-link" id="answer-history-tab" data-toggle="tab" href="#receipt-history" role="tab" aria-selected="false">
                    定額課金決済履歴
                </a>
                <div class="w30 mx-auto"></div>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane fade active show" id="stylist-detail">
                <div class="card-body">

                    {include file="../../common/_alert.tpl"}

                    <div class="form-group text-right h5">
                        {if !empty($online_salon_user.online_salon_charge_ignore_flag) }
                            <i class="fa fa-star text-info" >
                                <span class="text-sm-center">課金免除</span>
                            </i>
                        {/if}

                        {if $online_salon_user.online_salon_status == 'active'}
                            <span class="badge badge-primary px-3">有 効</span>
                        {elseif $online_salon_user.online_salon_status == 'inactive'}
                            <span class="badge badge-warning px-3">無 効</span>
                        {elseif $online_salon_user.online_salon_status == 'new'}
                            <span class="badge badge-info px-2">仮登録</span>
                        {elseif $online_salon_user.online_salon_status == 'checking'}
                            <span class="badge badge-secondary px-2">審査中</span>
                        {elseif $online_salon_user.online_salon_status == 'left'}
                            <span class="badge badge-light px-3">退 会</span>
                        {/if}
                    </div>

                    <table class="table table-fixed table-bordered">
                    <thead></thead>
                    <tbody>
                        <tr class="thead-light middle">
                            <th class="text-center w100">
                                氏名
                            </th>
                            <td colspan="5" class="">
                                {if $online_salon_user.sns_id}
                                <img src="https://graph.facebook.com/{$online_salon_user.sns_id}/picture" alt="">
                                {/if}
                                {$online_salon_user.name|escape}
                            </td>
                        </tr>
                        <tr class="thead-light middle">
                            <th class="text-center ">
                                ログインID
                            </th>
                            <td colspan="5" class="">
                                {$online_salon_user.loginid|escape}
                            </td>
                        </tr>
                        <tr class="thead-light middle">
                            <th class="text-center ">
                                仮登録日時
                            </th>
                            <td colspan="2" class="">
                                {$online_salon_user.created_at|date_format:'%Y/%-m/%-d %H:%M'}
                            </td>
                            <th class="text-center ">
                                入会日時
                            </th>
                            <td colspan="2" class="">
                                {if $online_salon_user.online_salon_activate_at}
                                {$online_salon_user.online_salon_activate_at|date_format:'%Y/%-m/%-d %H:%M'}
                                {/if}
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>

                {if !empty($online_salon_user.online_salon_status) && $online_salon_user.online_salon_status != 'checking' }
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 text-right">
                        {if $online_salon_user.online_salon_status == 'new' }
                        <button id="delete-btn" type="button" class="btn btn-sm btn-outline-danger m-0 waves-effect" >
                            削除する
                        </button>
                        {elseif $online_salon_user.online_salon_status == 'active' || $online_salon_user.online_salon_status == 'inactive'}
                            {if empty($online_salon_user.online_salon_charge_ignore_flag) }
                            <button id="charge-ignore-btn" type="button" class="btn btn-sm btn-outline-info mr-3 waves-effect" >
                                課金を免除する
                            </button>
                            {else}
                            <button id="charge-ignore-chancel-btn" type="button" class="btn btn-sm btn-outline-success mr-3 waves-effect" >
                                課金免除を解除する
                            </button>
                            {/if}
                            <button id="left-btn" type="button" class="btn btn-sm btn-outline-danger m-0 waves-effect" >
                                退会させる
                            </button>
                        {/if}
                        </div>
                    </div>
                </div>
                {/if}

            </div>

            <div class="tab-pane fade" id="receipt-history">
                <div class="card-body">

                    <table class="table table-sm table-hover border">
                    <thead>
                        <tr class="thead-light">
                            <th class="text-center w100">
                                対象月
                            </th>
                            <th class="text-center w200">
                                決済日時
                            </th>
                            <th class="text-center w100">
                                結果
                            </th>
                            <th class="">
                                決済ID
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $online_salon_user.receipts as $receipt }
                        <tr class="middle">
                            <td class="text-center">
                                {$receipt.month|date_format:'%Y/%m'}
                            </td>
                            <td class="text-center" >
                                {$receipt.created_at|date_format:'%Y/%m/%d %H:%M'}
                            </td>
                            <td class="text-center" >
                                {if $receipt.status == 'free'}
                                    <span class="badge badge-success px-3">無 料</span>
                                {elseif $receipt.status == 'paid'}
                                    <span class="badge badge-primary px-3">成 功</span>
                                {elseif $receipt.status == 'ignore'}
                                    <span class="badge badge-info px-3">免 除</span>
                                {elseif $receipt.status == 'failed'}
                                    <span class="badge badge-danger px-3">失 敗</span>
                                {/if}
                            </td>
                            <td>
                                {$receipt.charge_id|default:''}
                            </td>
                        </tr>
                        {foreachelse}
                        <tr>
                            <td colspan="4" class="text-muted"> 決済履歴はありません。 </td>
                        </tr>
                        {/foreach}
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-form" method="POST" action="/{$module}/{$class}/delete/">
    <input type="hidden" name="stylist_id" value="{$online_salon_user.id}">
</form>
<form id="left-form" method="POST" action="/{$module}/{$class}/left/" >
    <input type="hidden" name="stylist_id" value="{$online_salon_user.id}">
</form>
<form id="charge-ignore-form" method="POST" action="/{$module}/{$class}/charge_ignore/" >
    <input type="hidden" name="stylist_id" value="{$online_salon_user.id}">
    <input type="hidden" name="flag" >
</form>



