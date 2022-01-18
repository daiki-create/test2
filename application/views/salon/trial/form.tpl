<div class="row">
    <div class="col-12 col-md-10 col-lg-10 offset-md-1 offset-lg-1">
        <div class="text-center mb-50">
            <p class="title-en fs-18">
                Trial
            </p>
            <img src="/img/logo_hairlogy.png" alt="ロゴ">
            <p class="title-ja color-orange fs-20">
                トライアル会員登録<br>
                （仮登録）
            </p>
        </div>
        <div id="login-card" class="card">
            <div class="card-body px-4">
                {include file="../../common/_alert.tpl"}

                <form id="trial-form" class="p-1 p-sm-4" method="POST" action="/{$module}/{$class}/create/">
                    <div class="form-group">
                        <input type="email" class="form-control" name="loginid" value="{$trial.loginid|escape|default:''}" autocomplete="off"
                            placeholder="メールアドレス" maxlength="50" required autofocus>
                    </div>
                    <div class="form-group p-3 text-center">
                        <div class="mt-10">
                            <input type="image" src="/img/salon/btn-send.png">
                        </div> 
                    </div>
                    <div class="form-group text-center">
                        <p>または</p>
                        <div class="mt-10">
                            <input type="image" src="/img/salon/btn-fb.png" onclick="location.href='{$facebook_login_url|htmlspecialchars}'">
                        </div> 
                        <div class="mt-10 mb-30">
                            <input type="image" src="/img/salon/btn-line.png" onclick="location.href='{$line_login_url|htmlspecialchars}'">
                        </div> 
                        <a href="/salon/login/">アカウントをお持ちの方はこちら</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
