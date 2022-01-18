
<div class="row py-5">
    <div class="col-8 col-lg-6 offset-2 offset-lg-3">
        <div class="card border">
            <div class="card-header amber darken-4 text-center">
                <h5 class="card-title text-white">ログイン</h5>
            </div>
            <div class="card-body">
                <form id="login-form" class="text-center p-4" method="POST" action="/{$module}/{$class}/auth/">
                    <input type="email" class="form-control mb-4" name="loginid" value="{$loginid|escape|default:''}" placeholder="ログインID" maxlength="50" required autofocus>
                    <input type="password" class="form-control mb-4" name="loginpw" placeholder="ログインパスワード" maxlength="100" required>
                    <button type="submit" class="btn btn-warning btn-block p-2 mb-3 w-50 mx-auto" form="login-form">ログイン</button>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="remember-me" class="custom-control-input" name="remember_me" value="1"{if ! empty($remember_me)} checked{/if}>
                        <label class="custom-control-label text-warning" for="remember-me">Remember me</label>
                    </div>
                </form>

                {include file="../../common/_alert.tpl"}

            </div>
        </div>
    </div>
</div>

