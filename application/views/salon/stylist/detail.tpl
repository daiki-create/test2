<div class="row">
    <div class="col-lg-10 col-xl-8 offset-lg-1 offset-xl-2">
        <div class="card">
            <div class="card-header page-title">
                <i class="fas fa-users"></i>&nbsp;
                スタイリスト情報
            </div>
        </div>
        <div class="card">
            <div class="card-body grey lighten-5">
                <div class="d-flex align-items-baseline">
                    <div class="form-group ml-auto mb-1 h4">
                        {if $stylist.manager_flag}
                        <span class="badge red darken-1 text-white mr-2">
                            管理者
                        </span>
                        {/if}
                        {if empty($stylist.agreement_flag)}
                            <span class="badge badge-light status-inactive">仮登録</span>
                        {elseif empty($stylist.status)}
                            <span class="badge grey darken-1 text-white status-inactive">無効</span>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            {include file="../../common/_alert.tpl"}
            <div class="card-header border-bottom">
                <h6 class="text-dark">氏名</h6>
            </div>
            <div class="card-body px-1 px-md-3">
                <div class="form-group">
                    <input type="text" class="form-control form-control-sm mb-1" value="{$stylist.kana|escape|default:''}" readonly>
                    <input type="text" class="form-control" value="{$stylist.name|escape}" readonly>
                </div>
            </div>
            <div class="card-header border-bottom">
                <h6 class="text-dark">電話番号</h6>
            </div>
            <div class="card-body px-1 px-md-3">
                <div class="form-group">
                    <div class="form-control w160">{$stylist.phone|escape|default:''}</div>
                </div>
            </div>
            <div class="card-header border-bottom">
                <h6 class="text-dark">ログインID</h6>
            </div>
            <div class="card-body px-1 px-md-3">
                <div class="form-group">
                    <input type="text" class="form-control" value="{$stylist.loginid|escape}" readonly>
                </div>
            </div>
            <div class="card-header border-bottom">
                <h6 class="text-dark">メモ</h6>
            </div>
            <div class="card-body px-1 px-md-3">
                <div class="form-group">
                    <textarea rows="4" class="form-control form-control-sm" readonly>{$stylist.note|escape|default:''}</textarea>
                </div>
            </div>
            <div class="card-body pt-2">
                <div class="row mb-3">
                    <div class="col-6 offset-3 text-center">
                        {if $stylist.agreement_flag}
                        <a class="btn btn-md btn-hairlogy px-3 w160" href="/{$module}/{$class}/form/{$stylist.id}/">
                            スタイリスト情報編集
                        </a>
                        {/if}
                    </div>
                    <div class="col-3 text-right">
                        <button id="delete-stylist-btn" type="button" class="btn btn-md btn-outline-danger">
                            削除
                        </button>
                    </div>
                </div>
                <p class="small text-right">※サロン・スタイリストが削除された場合、次回ログイン時からログインできなくなります。</p>
            </div>
        </div>
    </div>
</div>

<form id="delete-stylist-form" method="POST" action="/{$module}/{$class}/delete/">
    <input type="hidden" name="stylist_id" value="{$stylist.id}">
</form>
