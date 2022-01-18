
<div class="row py-5">
    <div class="col-8 col-lg-6 offset-2 offset-lg-3">
        <div class="card">
            <div class="card-header peach-gradient text-center">
                <h5 class="card-title text-white">Welcome to `hairlogy Admin Page`.</h5>
            </div>
            <div class="card-body">
                <form id="login-form" class="text-center p-4" method="POST" action="/{$module}/{$class}/update_admin/">
                    <input type="hidden" class="form-control mb-4" name="admin_id" value="{$sysadmin.admin_id|escape|default:''}" required>
                    <p class="alert alert-info">
                        ようこそ {$sysadmin.name|escape|default:''}さん
		    </p>
                    <input class="form-control mb-4" value="{$sysadmin.loginid|escape|default:''}" readonly>
                    <p class="small">ログインパスワードを登録してください。</p>
                    <input type="password" class="form-control mb-4" name="loginpw" value="" placeholder="ログインパスワード" maxlength="100">
                    <input type="password" class="form-control mb-4" name="confirm_loginpw" value="" placeholder="ログインパスワード確認" maxlength="100">
                    <button type="submit" class="btn btn-warning btn-block p-2 mb-3 w-50 mx-auto" form="login-form">パスワード登録</button>
                </div>
                </form>
                {include file="../../common/_alert.tpl"}
            </div>
        </div>
    </div>
</div>

