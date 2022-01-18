
<div class="row">
<div class="col-8 col-lg-6 offset-2 offset-lg-3">
<div class="card">
    <div class="card-header amber darken-4">
        {if empty($admin_id)}システム管理者新規登録{else}システム管理者情報編集{/if}
    </div>
    <div class="card-body">

        {include file="../../common/_alert.tpl"}

        <form method="POST" action="/{$module}/{$class}/{if empty($admin_id)}create{else}update{/if}/">
        <div class="form-group">
            <label>名前</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-signature"></i>
                    </div>
                </div>
                <input type="text" class="form-control" name="name" value="" maxlength="20" required>
            </div>
        </div>
        <div class="form-group">
            <label>ログインID</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="far fa-user"></i>
                    </div>
                </div>
                <input type="email" class="form-control" name="loginid" value="" maxlength="50" required>
            </div>
        </div>
        <div class="form-group text-center">
            <button type="submit" class="btn btn-warning">
            {if empty($admin_id)}登 録{else}更 新{/if}
            </button>
        </div>
        </form>

    </div>
</div>
</div>
</div>

