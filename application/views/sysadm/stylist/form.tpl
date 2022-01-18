
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

                    <form id="stylist-form" method="POST" action="/{$module}/{$class}/update/{$stylist.id}/">

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

                    <table class="table table-fixed table-bordered table-input">
                    <thead></thead>
                    <tbody>
                        <tr class="thead-light middle">
                            <th rowspan="2" class="text-center p-0 w100 required">
                                <p class="kana">カナ</p>
                                氏名
                            </th>
                            <td colspan="5" class="p-0">
                                <input type="text" class="form-control form-control-sm" name="kana" value="{$stylist.kana|escape|default:''}" maxlength="50">
                            </td>
                        </tr>
                        <tr class="thead-light middle">
                            <td colspan="5" class="p-0">
                                <input type="text" class="form-control" name="name" value="{$stylist.name|escape}" maxlength="30" required>
                            </td>
                        </tr>
                        <tr class="thead-light middle">
                            <th class="text-center p-0 required">
                                ログインID
                            </th>
                            <td colspan="5" class="p-0">
                                <input type="email" class="form-control" name="loginid" value="{$stylist.loginid|escape}" maxlength="50" required>
                            </td>
                        </tr>
                        <tr class="thead-light middle">
                            <th class="text-center p-0">
                                電話番号
                            </th>
                            <td colspan="2" class="p-0">
                                <input type="tel" class="form-control" name="phone" value="{$stylist.phone|escape|default:''}" maxlength="13">
                            </td>
                            <th class="text-center p-0">
                                トライアル終了日
                            </th>
                            <td colspan="2" class="p-0">
                                <div id="date-picker-trial-limited-on" class="">
                                    <input type="date" id="trial-limited-on" class="form-control" name="trial_limited_on" value="{$stylist.trial_limited_on|default:''}">
                                    <label for="trial-limited-on" class="d-inline m-0"></label>
                                </div>
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
                                <textarea class="form-control form-control-sm" name="note" maxlength="200">{$stylist.note|escape|default:''}</textarea>
                            </td>
                        </tr>
                    </tbody>
                    </table>

                    </form>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                            <button type="submit" class="btn btn-md amber darken-4 m-0 text-white w140" form="stylist-form">
                                更 新
                            </button>
                        </div>
                        <div class="col-4 text-right">
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

