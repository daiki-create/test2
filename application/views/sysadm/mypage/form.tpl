
<div class="row py-5">
    <div class="col-8 col-lg-6 offset-2 offset-lg-3">
        <div class="card">
            <div class="card-header amber darken-4 text-center">
                <h5 class="card-title text-white">アカウント</h5>
            </div>
            <div class="card-body">
                <form id="login-form" class="text-center p-4" method="POST" action="/{$module}/{$class}/update_admin/">
                    <input type="hidden" class="form-control mb-4" name="admin_id" value="{$sysadmin.admin_id|escape|default:''}" required>
                    <input type="text" class="form-control mb-4" name="name" value="{$sysadmin.name|escape|default:''}" maxlength="50" required>
                    <input type="email" class="form-control mb-4" name="loginid" value="{$sysadmin.loginid|escape|default:''}" maxlength="50" required>
                    <input type="password" class="form-control mb-4" name="loginpw" value="" placeholder="ログインパスワード" maxlength="100">
                    <button type="submit" class="btn btn-warning btn-block p-2 mb-3 w-50 mx-auto" form="login-form">ログイン</button>
                </div>
                </form>
                {include file="../../common/_alert.tpl"}
            </div>
        </div>
    </div>
</div>

