<div class="row">
    <div class="col-12 col-md-10 col-lg-10 offset-md-1 offset-lg-1">
        <div class="text-center mb-50">
            <p class="title-en fs-18">
                Terms
            </p>
            <img src="/img/logo_hairlogy.png" alt="ロゴ">
            <p class="title-ja color-orange fs-20">
                ご利用登録
            </p>
        </div>
        <div id="login-card" class="card">
            <div class="card-body px-2">
                {include file="../../common/_alert.tpl"}
                <form id="login-form" class="text-center p-1 p-sm-4" method="POST" action="/{$module}/{$class}/join/{$auth_provider}">
                    <p class="text-left fw-600">ログインID</p>
                    <input type="email" class="form-control mb-4" name="loginid" value="{$loginid|escape|default:''}" autocomplete="off"
                        placeholder="ログインID" maxlength="50" required autofocus{if ! empty($auth_provider)} readonly{/if}>
                    <p class="mt-50 text-left fw-600">氏名</p>
                    <div class="row">
                        <div class="col-12">
                            <input type="text" class="form-control mb-4" name="" value="" autocomplete="off"
                            placeholder="フリガナ" maxlength="50" required>
                        </div>
                        <div class="col-12">
                            <input type="text" class="form-control mb-4" name="" value="" autocomplete="off"
                            placeholder="お名前" maxlength="50" required>
                        </div>
                    </div>
                    {if empty($auth_provider)}
                    <p class="mt-50 text-left fw-600">仮パスワード</p>
                    <input type="password" class="form-control mb-4" name="loginpw" placeholder="仮パスワード" maxlength="100" required autocomplete="off">
                    {else}
                    <input type="text" name="tmp_loginpw" value="{$tmp_loginpw}">
                    {/if}
                    <label>ご利用規約</label>
                    <div class="form-group">
                        <div id="agreement-text" class="border mb-4">
                        {include file="../../www/index/tos.tpl"}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="agreement-check" name="agreement"
                                value="1">
                            <label class="custom-control-label" for="agreement-check">ご利用規約に同意する</label>
                        </div>
                    </div>
                    <div class="p-3">
                        <button type="submit" id="submit-btn"
                            class="btn btn-lg deep-orange darken-2 text-white btn-block p-2 mb-3 w-50 mx-auto" form="login-form" disabled>
                            登 録
                        </button>
                    </div>
                </form>
                {include file="../../common/_alert.tpl"}
            </div>
        </div>
    </div>
</div>
