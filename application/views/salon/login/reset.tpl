<div class="row py-5">
    <div class="col-12 col-md-10 col-lg-6 offset-md-1 offset-lg-3">
        <div id="login-card" class="card">
            <div class="card-header amber darken-4 text-center">
                <h5 class="card-title text-white mb-0">hairlogy パスワード再登録</h5>
            </div>
            <div class="card-body px-2">
                {include file="../../common/_alert.tpl"}
                {if ! $error_messages}
                <p class="description">
                    下記フォームよりパスワードの再登録を行なってください。<br/>（※パスワードは６文字以上２０文字以内の半角英数字、及び記号。）<br/>
                </p>
                <form id="reset-loginpw-form" class="text-center p-1 p-sm-4" method="POST" action="/{$module}/{$class}/update_pw/{$stylist['salon_id']}/{$stylist['id']}/{$reset_pw_md5}">

                    <div class="form-group">
                        <input type="password" class="form-control" name="new_password" placeholder="新しいパスワード" maxlength="20" minlength="6" value="" required>
                    </div>

                    <div class="form-group">
                        <input type="password" class="form-control" name="confirm_password" placeholder="新しいパスワード確認用" maxlength="20" minlength="6" value="" required>
                    </div>

                    <div class="p-3">
                        <div class="">
                            <button type="submit" class="btn red darken-4 text-white btn-block p-2 mx-auto w240" form="reset-loginpw-form">パスワード再登録</button>
                        </div>
                    </div>
                </form>
                {/if}
            </div>
        </div>
    </div>
</div>
