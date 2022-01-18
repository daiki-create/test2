<div class="row">
    <div class="col-12 col-sm-12 col-md-10 col-lg-10 offset-sm-0 offset-md-1 offset-lg-1">
        <div class="text-center mb-50">
            <p class="title-en fs-18">
                Login to
            </p>
            <img class="text-center" src="/img/logo_hairlogy.png" alt="ロゴ">
            <p class="title-ja color-orange fs-20">
                ログイン
            </p>
        </div>
        <div class="card">
            <div class="card-body">
                {include file="../../common/_alert.tpl"}
                <form id="login-form" class="text-center p-sm-4" method="POST" action="/{$module}/{$class}/auth/">
                    <div class="form-group text-center">
                        <input type="email" class="form-control mb-4 mx-auto" name="loginid" value="{$loginid|escape|default:''}"
                            placeholder="ログインID" maxlength="50" required autofocus>
                        <input type="password" class="form-control mb-4 mx-auto" name="loginpw" placeholder="ログインパスワード" maxlength="100" required>
                    </div>
                    <div class="form-group text-center">
                        <div class="mt-50">
                            <input type="image" src="/img/salon/btn-login.png">
                        </div> 
                        <div class="mt-10">
                            <input type="image" src="/img/salon/btn-fb.png" onclick="location.href='{$facebook_login_url|htmlspecialchars}'">
                        </div> 
                        <div class="mt-10 mb-30">
                            <input type="image" src="/img/salon/btn-line.png" onclick="location.href='{$line_login_url|htmlspecialchars}'">
                        </div> 
                        <div class="mt-10">
                            <input type="image" src="/img/salon/btn-new.png" onclick="location.href='/salon/trial/form'">
                        </div>                  
                    </div>
                </form>
                <p class="text-center pt-4 small">
                    <a class="color-light-black" href="/{$module}/{$class}/request/">パスワードをお忘れの方はコチラ</a>
                </p>
            </div>
            <div class="text-center mb-50">
                <input type="image" src="/img/salon/btn-term.png" onclick="location.href='/salon/tos/'">
            </div> 
        </div>
    </div>
</div>

<div class="mb-3">
<div id="tos-collapse"  class="row collapse">
    <div class="col-12 col-md-10 col-lg-6 offset-md-1 offset-lg-3">
{include file="../../www/index/tos.tpl"}
    </div>
</div>
</div>
