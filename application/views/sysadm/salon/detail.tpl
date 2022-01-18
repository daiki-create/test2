
<div class="card">
    <div class="card-header amber darken-4">
        <ul class="nav nav-tabs" id="salon-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="salon-detail-tab" data-toggle="tab" href="#salon-detail" role="tab" aria-selected="true">
                    サロン 詳細情報
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="stylists-tab" data-toggle="tab" href="#stylists" role="tab" aria-selected="false">
                    スタイリスト 情報
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="salon-detail">
                <div class="card-body">

                    {include file="../../common/_alert.tpl"}

                    <table class="table table-fixed border">
                    <thead></thead>
                    <tbody class="">
                        <tr>
                            <td>
                                <div class="d-flex">
                                    <div class="form-group flex-fill mb-0">
                                        <label>サロン名</label>
                                        <input type="text" class="form-control" name="name" value="{$salon.name|escape|default:''}" maxlength="50" readonly>
                                    </div>
                                    <div class="form-group mb-0 text-center w140">
                                        <label>ステータス</label>
                                        <div class="h3">
                                            <span class="badge{if empty($salon.status)} badge-light">停止{else} badge-success">有効{/if}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="d-flex">
                                <div class="form-group mb-0 mr-3">
                                    <label>電話番号</label>
                                    <div class="d-flex align-items-center mb-1">
                                        <input type="tel" class="form-control text-center mr-1 w100{if isset($validation_errors['phone'])} is-invalid{/if}" name="phone[0]" value="{$salon.phone[0]|default:''}" maxlength="4" readonly>
                                        <span>-</span>
                                        <input type="tel" class="form-control text-center mx-1 w100{if isset($validation_errors['phone'])} is-invalid{/if}" name="phone[1]" value="{$salon.phone[1]|default:''}" maxlength="4" readonly>
                                        <span>-</span>
                                        <input type="tel" class="form-control text-center ml-1 w100{if isset($validation_errors['phone'])} is-invalid{/if}" name="phone[2]" value="{$salon.phone[2]|default:''}" maxlength="4" readonly>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <label>FAX番号</label>
                                    <div class="d-flex align-items-center mb-1">
                                        <input type="tel" class="form-control text-center mr-1 w100{if isset($validation_errors['fax'])} is-invalid{/if}" name="fax[0]" value="{$salon.fax[0]|default:''}" maxlength="4" readonly>
                                        <span>-</span>
                                        <input type="tel" class="form-control text-center mx-1 w100{if isset($validation_errors['fax'])} is-invalid{/if}" name="fax[1]" value="{$salon.fax[1]|default:''}" maxlength="4" readonly>
                                        <span>-</span>
                                        <input type="tel" class="form-control text-center ml-1 w100{if isset($validation_errors['fax'])} is-invalid{/if}" name="fax[2]" value="{$salon.fax[2]|default:''}" maxlength="4" readonly>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group mb-0">
                                    <label>住所</label>
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="input-group input-group-sm mr-1 w100">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text px-2">&#x3012;</div>
                                            </div>
                                            <input type="tel" class="form-control form-control-sm text-center" name="postcode1" value="{$salon.postcode1|escape|default:''}" maxlength="3" readonly>
                                        </div>
                                        <span>-</span>
                                        <input type="tel" class="form-control form-control-sm text-center ml-1 w100" name="postcode2" value="{$salon.postcode2|escape|default:''}" maxlength="4" readonly>
                                    </div>
                                    <div class="d-flex">
                                        <input class="form-control w120" name="prefecture" value="{$prefectures[$salon.prefecture]|default:''}" readonly>
                                        <select class="custom-select w120" name="prefecture" style="display:none;">
                                        {strip}
                                        <option></option>
                                        {foreach $prefectures as $code => $name}

                                        <option value="{$code}"{if $code == $salon.prefecture} selected{/if}>{$name}</option>
                                        {/foreach}
                                        {/strip}
                                        </select>
                                        <input type="text" class="form-control" name="address" value="{$salon.address|escape|default:''}" maxlength="50" readonly>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group mb-0">
                                    <label>備考</label>
                                    <textarea class="form-control form-control-sm" name="note" maxlength="250" readonly>{$salon.note|escape|default:''}</textarea>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    </table>

                </div>
                <div class="card-body">
                    <table class="table table-fixed table-bordered">
                    <thead>
                        <tr class="amber darken-4 text-white">
                            <th class="">
                                アンケート名
                            </th>
                            <th class="">
                                状態
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $salon.questionnaires as $questionnaire}
                        <tr class="middle">
                            <td class="text-truncate">
                                {$questionnaire.title|escape}
                            </td>
                            <td class="text-center h5">
                                {if $questionnaire.status == '1'} <span class="badge badge-primary">稼働</span>
                                {elseif $questionnaire.status == '0'} <span class="badge badge-light">停止</span>
                                {/if}
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                    </table>
                </div>
                <div class="card-footer text-center">
                    <div class="row">
                        <div class="col-6">
                            <a class="btn btn-warning w140" href="/{$module}/{$class}/form/{$salon_id}"> 編 集 </a>
                        </div>
                        <div class="col-3">
                            {if empty($salon.status)}
                            <button id="enable-btn" class="btn btn-success"> 有効にする </button>
                            {else}
                            <button id="disable-btn" class="btn btn-light"> 停止にする </button>
                            {/if}
                        </div>
                        <div class="col-3">
                            {if empty($salon.status)}
                            <button id="delete-btn" class="btn btn-outline-danger"> 削 除 </button>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>

            <div id="stylists" class="tab-pane fade">
                <div id="salon-info" class="card-body pb-0">

                    <table class="table table-fixed border mb-0">
                    <thead></thead>
                    <tbody class="">
                        <tr>
                            <td>
                                <div class="form-group mb-0">
                                    <label>サロン名</label>
                                    <input type="text" class="form-control" name="name" value="{$salon.name|escape|default:''}" maxlength="50" readonly>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    </table>

                </div>
                <div id="stylist-list" class="card-body">

                    <div class="d-flex align-items-center">
                        <div id="ajax-alert" class="alert small py-1 mb-0" style="display:none;"></div>
                        <div class="form-group text-right ml-auto mb-2">
                            <button type="button" id="stylist-form-btn" class="btn btn-sm btn-warning m-0 w140 waves-effect"
                                    data-target="#stylist-form-block" data-toggle="collapse" aria-controls="stylist-form-block">
                                スタイリスト登録
                            </button>
                        </div>
                    </div>
                    <div id="stylist-form-block" class="card collapse mb-2">
                        <div class="card-body">

                            <div class="alert alert-danger small" style="display:none;">登録に失敗しました。</div>

                            <form id="stylist-form" class="mb-0">
                            <input type="hidden" name="salon_id" value="{$salon_id}">
                            <div class="row">
                                <div class="col-8 col-lg-6">
                                    <div class="form-group">
                                        <label class="required">スタイリスト名</label>
                                        <input type="text" class="form-control form-control-sm mb-1" name="kana" value="" maxlength="50" placeholder="カナ">
                                        <input type="text" class="form-control" name="name" value="" maxlength="30" placeholder="氏名">
                                    </div>
                                    <div class="form-group">
                                        <label class="required">ログインID</label>
                                        <input type="email" class="form-control" name="loginid" value="" maxlength="50">
                                    </div>
                                    <div class="form-group">
                                        <label>電話番号</label>
                                        <div class="d-flex align-items-center">
                                            <input type="tel" class="form-control text-center w60 mr-1" name="phone1" value="" maxlength="3">
                                            <span>-</span>
                                            <input type="tel" class="form-control text-center w80 mx-1" name="phone2" value="" maxlength="4">
                                            <span>-</span>
                                            <input type="tel" class="form-control text-center w80 ml-1" name="phone3" value="" maxlength="4">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>トライアル終了日</label>
                                        <div class="d-flex align-items-center">
                                            <input type="date" class="form-control w160" name="trial_limited_on" value="">
                                            <div class="custom-control custom-checkbox ml-auto mt-3">
                                                <input type="checkbox" class="custom-control-input" id="create-manager-flag-check" name="manager_flag" value="1">
                                                <label class="custom-control-label" for="create-manager-flag-check">サロン管理者</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group mb-5">
                                        <label>メモ</label>
                                        <textarea rows="7" class="form-control form-control-sm" name="note" maxlength="250"></textarea>
                                    </div>
                                    <div class="form-group pt-5">
                                        <label>　</label>
                                        <div class="d-flex align-items-center">
                                            <button id="create-stylist-btn" class="btn btn-md btn-warning mr-3">
                                                スタイリスト登録
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>

                    <table id="stylist-list-table" class="table table-fixed border">
                    <thead>
                    <tr class="thead-light">
                        <th class="middle w180">
                            スタイリスト名
                        </th>
                        <th class="middle text-center w160">
                            電話番号
                        </th>
                        <th class="middle">
                            ログインID
                        </th>
                        <th class="text-center px-1 w60">
                            回答数
                        </th>
                        <th class="text-center px-1 w60">
                            管理者
                        </th>
                        <th class="w80">
                        </th>
                    </tr>
                    </thead>
                    <tbody id="stylist-list-tbody">
                    {foreach $stylists as $stylist}

                    <tr class="clickable" data-stylist-id="{$stylist.id}">
                        <td class="stylist-name" data-toggle="tooltip" title="{$stylist.kana|escape|default:''}">
                            <a href="#">{$stylist.name|escape|default:''}</a>
                        </td>
                        <td class="text-center stylist-phone">
                            <a href="#">{$stylist.phone|escape|default:''}</a>
                        </td>
                        <td class="text-truncate stylist-loginid">
                            <a href="#">{$stylist.loginid|escape|default:''}</a>
                        </td>
                        <td class="text-center">
                            {$stylist.reply_count|default:'0'}
                        </td>
                        <td class="text-center manager-flag">
                            {if $stylist.manager_flag}
                            <i class="far fa-circle fa-lg text-warning"></i>
                            {/if}
                        </td>
                        <td class="text-center h6">
                            {if $stylist.status}<span class="badge badge-primary status-active">有効</span>
                            {elseif $stylist.agreement_flag}<span class="badge badge-light status-inactive">無効</span>
                            {else}<span class="badge badge-light status-inactive">仮登録</span>{/if}

                        </td>
                    </tr>
                    {/foreach}

                    </tbody>
                    <tbody id="stylist-dummy-tbody" hidden>
                    <tr class="clickable" data-stylist-id="">
                        <td class="stylist-name" data-toggle="tooltip" title="">
                            <a href="#"></a>
                        </td>
                        <td class="text-center stylist-phone">
                            <a href="#"></a>
                        </td>
                        <td class="text-truncate stylist-loginid">
                            <a href="#"></a>
                        </td>
                        <td class="text-center manager-flag">
                        </td>
                        <td class="text-center h6">
                            <span class="badge badge-primary status-active">有効</span>
                            <span class="badge badge-light status-inactive">無効</span>
                            <span class="badge badge-light status-temporary">仮登録</span>
                        </td>
                    </tr>
                    </tbody>
                    </table>

                </div>
                <div id="stylist-detail" class="card-body" style="display:none;">

                    <div class="form-group text-right mb-2">
                        <button type="button" id="stylist-list-btn" class="btn btn-sm btn-warning m-0 w140  waves-effect">
                            スタイリスト一覧
                        </button>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">

                            <div class="alert alert-danger small" style="display:none;">更新に失敗しました。</div>

                            <form id="stylist-update-form" class="mb-0">
                            <input type="hidden" name="salon_id" value="{$salon_id}">
                            <input type="hidden" name="stylist_id" value="">
                            <div class="row">
                                <div class="col-8 col-lg-6">
                                    <div class="form-group stylist-name">
                                        <div>
                                            <label class="mr-2">スタイリスト名</label>
                                            <span class="small text-muted">
                                            {if $stylist.last_login_at}
                                                (最終ログイン：{$stylist.last_login_at|date_format:'%Y/%-m/%-d %H:%M'})
                                            {/if}
                                            </span>
                                            <span class="h5">
                                                <span class="badge badge-info float-right" style="display:none;">有 効</span>
                                                <span class="badge badge-light float-right" style="display:none;">仮登録</span>
                                                <span class="badge badge-danger float-right" style="display:none;">停 止</span>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control form-control-sm mb-1" name="kana" value="" maxlength="50" placeholder="カナ" readonly>
                                        <input type="text" class="form-control" name="name" value="" maxlength="30" placeholder="氏名" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>ログインID</label>
                                        <input type="email" class="form-control" name="loginid" value="" maxlength="50" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>電話番号</label>
                                        <div class="d-flex align-items-center">
                                            <input type="tel" class="form-control text-center w60 mr-1" name="phone1" value="" maxlength="3" readonly>
                                            <span>-</span>
                                            <input type="tel" class="form-control text-center w80 mx-1" name="phone2" value="" maxlength="4" readonly>
                                            <span>-</span>
                                            <input type="tel" class="form-control text-center w80 ml-1" name="phone3" value="" maxlength="4" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>トライアル終了日</label>
                                        <div class="d-flex align-items-center">
                                            <input type="date" class="form-control w160" name="trial_limited_on" value="">
                                            <label class="ml-auto mt-3" id="manager-flag-label">
                                                <i class="far fa-check-square fa-lg text-info manager-flag-1" style="display:none;"></i>
                                                <i class="far fa-square fa-lg text-light manager-flag-0" style="display:none;"></i>
                                                サロン管理者
                                            </label>
                                            <div class="custom-control custom-checkbox ml-auto mt-3" id="update-manager-flag" style="display:none">
                                                <input type="checkbox" class="custom-control-input" id="update-manager-flag-check" name="manager_flag" value="1">
                                                <label class="custom-control-label" for="update-manager-flag-check">サロン管理者</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label>メモ</label>
                                        <textarea rows="7" class="form-control form-control-sm" name="note" maxlength="250" readonly></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>アンケート回答情報</label>
                                        <div class="d-flex">
                                            <div class="w260 mr-2">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><div class="input-group-text">回答数</div></div>
                                                    <div id="reply-count" class="form-control text-center"></div>
                                                </div>
                                            </div>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">最終回答日<span class="d-xl-inline d-lg-none">時</span></div>
                                                </div>
                                                <div class="form-control text-center">
                                                    <span id="last-replied-date"></span>
                                                    <span id="last-replied-hour" class="d-xl-inline d-lg-none"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>　</label>
                                        <div class="d-flex align-items-center">
                                            <button type="button" id="edit-stylist-btn" class="btn btn-md btn-warning mr-2 w120">
                                                編 集
                                            </button>
                                            <button type="button" id="update-stylist-btn" class="btn btn-md btn-secondary mr-2 w120" style="display:none;">
                                                更 新
                                            </button>
                                            <button type="button" id="disable-stylist-btn" class="btn btn-md btn-outline-warning px-2 mx-2 waves-effect w80" style="display:none;">
                                               停 止 
                                            </button>
                                            <button type="button" id="enable-stylist-btn" class="btn btn-md btn-outline-info px-2 mx-2 waves-effect w80" style="display:none;">
                                               有 効 
                                            </button>
                                            <button type="button" id="delete-stylist-btn" class="btn btn-md btn-outline-danger px-2 mx-2 waves-effect w80" style="display:none;">
                                               削 除 
                                            </button>
                                            <button type="button" id="select-salon-btn" class="btn btn-md btn-outline-secondary px-2 mx-2 waves-effect" data-toggle="modal" data-target="#select-salon-modal" style="display:none;">
                                                サロン変更
                                            </button>
                                            <button type="button" id="detail-stylist-btn" class="btn btn-md btn-light ml-auto" style="display:none;">
                                                キャンセル
                                            </button>
                                            <button type="button" id="sendmail-stylist-btn" class="btn btn-md btn-outline-success ml-auto  waves-effect px-3" data-toggle="tooltip" title="パスワードをリセットして通知メールを送信。">
                                                通知メール送信
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>

                        </div>
                    </div>

                    <table class="table table-fixed border">
                    <thead>
                    <tr class="thead-light">
                        <th class="w200">
                            アンケート名
                        </th>
                        <th class="">
                            URL
                        </th>
                        <th class="text-center w100">
                            QRコード
                        </th>
                    </tr>
                    </thead>
                    <tbody id="questionnaire-list-tbody">
                    </tbody>
                    <tbody id="questionnaire-dummy-tbody" hidden>
                    <tr class="clickable">
                        <td class="text-truncate questionnaire-title middle">
                        </td>
                        <td class="questionnaire-url middle">
                        </td>
                        <td class="questionnaire-qr text-center p-2">
                            <img class="img-thumbnail" alt="" src="">
                        </td>
                    </tr>
                    </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>
</div>

<form id="update-status-form" method="POST" action="/{$module}/{$class}/update_status/{$salon_id}/">
<input type="hidden" name="status" value="{if empty($salon.status)}1{else}0{/if}">
</form>
<form id="delete-form" method="POST" action="/{$module}/{$class}/delete/">
<input type="hidden" name="salon_id" value="{$salon_id}">
</form>
<form id="change-salon-form" method="POST" action="/{$module}/{$class}/change_salon/{$salon_id}/">
<input type="hidden" name="stylist_id" value="">
<input type="hidden" name="new_salon_id" value="">
</form>

{include file="../stylist/detail/_modal.tpl"}

