
<div class="card w800 mx-auto">
    <div class="card-header text-truncate py-3 amber darken-4">
        <ul class="nav nav-tabs" id="report-tab" role="tablist">
            <li class="nav-item waves-effect waves-light">
                <a class="nav-link active" id="stylist-detail-tab" data-toggle="tab" href="#stylist-detail" role="tab" aria-selected="false">
                    スタイリスト情報
                </a>
                <div class="w30 mx-auto"></div>
            </li>
            <li class="nav-item waves-effect waves-light">
                <a class="nav-link" id="answer-history-tab" data-toggle="tab" href="#answer-history" role="tab" aria-selected="false">
                    アンケート回答履歴 ({$stylist.reply_count|default:'0'})
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
                        {if $stylist.agreement_flag}
                            {if $stylist.status}
                            <span class="badge badge-primary px-2">有 効</span>
                            {else}
                            <span class="badge badge-light px-2">停 止</span>
                            {/if}
                        {else}
                            <span class="badge badge-warning">仮登録</span>
                        {/if}
                    </div>

                    <table class="table table-fixed table-bordered">
                    <thead></thead>
                    <tbody>
                        <tr class="thead-light middle">
                            <th class="text-center w100">
                                <p class="kana">カナ</p>
                                氏名
                            </th>
                            <td colspan="5" class="">
                                <p class="kana">{$stylist.kana|escape|default:''}</p>
                                <span id="stylist-name">{$stylist.name|escape}</span>
                            </td>
                        </tr>
                        <tr class="thead-light middle">
                            <th class="text-center ">
                                ログインID
                            </th>
                            <td colspan="5" class="">
                                {$stylist.loginid|escape}
                            </td>
                        </tr>
                        <tr class="thead-light middle">
                            <th class="text-center ">
                                電話番号
                            </th>
                            <td colspan="2" class="">
                                {$stylist.phone|escape|default:''}
                            </td>
                            <th class="text-center ">
                                ﾄﾗｲｱﾙ終了日
                            </th>
                            <td colspan="2" class="">
                                {if $stylist.trial_limited_on}
                                {$stylist.trial_limited_on|date_format:'%Y/%-m/%-d'}
                                {/if}
                            </td>
                        </tr>
                        <tr class="thead-light middle">
                            <th class="text-center ">
                                最終ｱﾝｹｰﾄ回答日時
                            </th>
                            <td class="">
                                {if $stylist.last_replied_at}
                                {$stylist.last_replied_at|date_format:'%Y/%-m/%-d %H:%M'}
                                {/if}
                            </td>
                            <th class="text-center ">
                                最終ﾛｸﾞｲﾝ日時
                            </th>
                            <td class="">
                                {if $stylist.last_login_at}
                                {$stylist.last_login_at|date_format:'%Y/%-m/%-d %H:%M'}
                                {/if}
                            </td>
                            <th class="text-center ">
                                仮登録日時
                            </th>
                            <td class="">
                                {$stylist.created_at|date_format:'%Y/%-m/%-d %H:%M'}
                            </td>
                        </tr>
                        <tr class="thead-light middle">
                            <th class="text-center ">
                                備考
                            </th>
                            <td colspan="5" class="p-0">
                                <textarea class="form-control form-control-sm" readonly>{$stylist.note|escape|default:''}</textarea>
                            </td>
                        </tr>
                    </tbody>
                    </table>

                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            {if ! empty($stylist.status)}
                            <button type="button" class="btn btn-md btn-secondary m-0" data-toggle="modal" data-target="#select-salon-modal">
                                所属サロン設定
                            </button>
                            {/if}
                        </div>
                        <div class="col-4 text-center">
                            <a class="btn btn-md amber darken-4 text-white m-0 w140" href="/{$module}/{$class}/form/{$stylist.id}/">
                                編 集
                            </a>
                        </div>
                        <div class="col-2 text-center px-1">
                            {if empty($stylist.status)}
                            <button id="enable-stylist-btn" type="button" class="btn btn-md btn-outline-primary m-0 waves-effect">
                                有効にする
                            </button>
                            {else}
                            <button id="disable-stylist-btn" type="button" class="btn btn-md btn-outline-warning m-0 waves-effect">
                                無効にする
                            </button>
                            {/if}
                        </div>
                        <div class="col-2 text-right">
                            {if empty($stylist.status)}
                            <button id="delete-stylist-btn" type="button" class="btn btn-md btn-outline-danger m-0 waves-effect">
                                削 除
                            </button>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="answer-history">
                <div class="card-body">

                    <table class="table table-sm table-hover table-fixed border w260">
                    <thead>
                        <tr class="thead-light">
                            <th class="text-center w130">
                                日付
                            </th>
                            <th class="">
                                時刻
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $replies as $reply}
                        <tr class="middle clickable">
                            <td class="text-center">
                                {$reply.created_at|date_format:'%Y年%-m月%-d日'}
                            </td>
                            <td>
                                {$reply.created_at|date_format:'%H:%M'}
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                    </table>

                    <div class="alert alert-warning small">
                        ページャー未実装です。ごめんなさい。
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-stylist-form" method="POST" action="/{$module}/{$class}/delete/">
<input type="hidden" name="stylist_id" value="{$stylist.id}">
</form>
<form id="enable-stylist-form" method="POST" action="/{$module}/{$class}/update_status/{$stylist.id}/">
<input type="hidden" name="status" value="1">
</form>
<form id="disable-stylist-form" method="POST" action="/{$module}/{$class}/update_status/{$stylist.id}/">
<input type="hidden" name="status" value="0">
</form>
<form id="belon-to-salon-form" method="POST" action="/{$module}/{$class}/belong/{$stylist.id}/">
<input type="hidden" name="salon_id" value="">
</form>


