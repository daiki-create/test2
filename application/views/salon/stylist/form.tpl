<form id="stylist-form" method="POST" action="/{$module}/{$class}/update/{$stylist_id}/" class="mb-0">
<div class="row">
    <div class="col-lg-10 col-xl-8 offset-lg-1 offset-xl-2">
        <div class="card">
            <div class="card-header page-title">
                <i class="fas fa-users"></i>&nbsp;
                スタイリスト情報 編集
            </div>
        </div>
        <div class="card">
            <div class="card-body grey lighten-5">
                <div class="d-flex h4">
                    <div class="form-group ml-auto mb-0 mr-4">
                        <div class="btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-sm m-0 red darken-1 text-white manager-flag {if $stylist.manager_flag} active{/if}">
                                <input type="checkbox" name="manager_flag" value="1"{if $stylist.manager_flag} checked{/if}>
                                管理者
                            </label>
                        </div>
                    </div>
                    {if $stylist.status}
                    <div class="btn-group" data-toggle="buttons">
                        <label id="status-active" class="btn btn-sm btn-hairlogy m-0">
                            有効
                        </label>
                        <label id="status-inactive" class="btn btn-sm btn-light grey darken-1 text-white m-0" style="display:none;">
                            無効
                        </label>
                    </div>
                    {elseif $stylist.agreement_flag}
                    <div class="btn-group" data-toggle="buttons">
                        <label id="status-inactive" class="btn btn-sm btn-light grey darken-1 text-white m-0">
                            無効
                        </label>
                        <label id="status-active" class="btn btn-sm btn-hairlogy m-0" style="display:none;">
                            有効
                        </label>
                    </div>
                    {else}
                    <span class="badge badge-light status-inactive">仮登録</span>
                    {/if}
                </div>
            </div>
        </div>
        <div class="card">
            {include file="../../common/_alert.tpl"}

                <input type="hidden" name="status" value="{if $stylist.status}1{else}0{/if}">

                <div class="card-header border-bottom">
                    <h6 class="text-dark">氏名</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <input type="text" class="form-control form-control-sm mb-1{if isset($validation_errors.kana)} validation-error{/if}"
                               name="kana" value="{$stylist.kana|escape|default:''}" maxlength="50" placeholder="カナ">
                        <input type="text" class="form-control{if isset($validation_errors.name)} validation-error{/if}"
                               name="name" value="{$stylist.name|escape}" maxlength="30" required>
                    </div>
                </div>
                <div class="card-header border-bottom">
                    <h6 class="text-dark">電話番号</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <input type="tel" class="form-control phone w200{if isset($validation_errors.phone)} validation-error{/if}" name="phone" value="{$stylist.phone|escape|default:''}" maxlength="13">
                    </div>
                </div>
                <div class="card-header border-bottom">
                    <h6 class="text-dark">ログインID</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <input type="email" class="form-control{if isset($validation_errors.loginid)} validation-error{/if}" name="loginid" value="{$stylist.loginid|escape}" maxlength="50" required>
                    </div>
                </div>
                <div class="card-header border-bottom">
                    <h6 class="text-dark">メモ</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <textarea rows="4" class="form-control form-control-sm{if isset($validation_errors.note)} validation-error{/if}" name="note" maxlength="200">{$stylist.note|escape|default:''}</textarea>
                    </div>
                </div>
        </div>
        <div class="card-body text-center pt-2">
            <button type="submit" class="btn btn-md btn-hairlogy mb-3 w240" form="stylist-form">
                更 新
            </button>
            <p class="small">※スタイリストのステータスを無効にした場合、次回ログイン時からログインできなくなります。</p>
        </div>
    </div>
</div>
</form>
