<div class="row">
    <div class="col-md-8 col-lg-6 offset-md-2  offset-lg-3 px-3 px-sm-5">
        <div class="card mb-3">
            <div class="card-header page-title">
                <i class="fas fa-user-circle text-white"></i>
                {$stylist.name|escape} 様
            </div>
        </div>
        <div class="card">

            {include file="../../common/_alert.tpl"}

            <form id="" method="POST" action="/{$module}/{$class}/update/{$stylist.salon_id}/{$stylist.id}/">
            <div class="card-header border-bottom">
                <h6>氏 名</h6>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm mb-1{if isset($validation_errors.kana)} validation-error{/if}" name="kana" value="{$stylist.kana|escape|default:''}" maxlength="50" placeholder="カナ">
                    <input type="text" class="form-control{if isset($validation_errors.name)} validation-error{/if}" name="name" value="{$stylist.name|escape}" maxlength="30" required>
                </div>
            </div>
            <div class="card-header border-bottom">
                <h6>
                    ログインID
                    <a class="float-right" data-toggle="tooltip" data-title="ログイン用のメールアドレスを登録してください。">
                        <i class="far fa-question-circle text-primary"></i>
                    </a>
                </h6>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <input type="email" class="form-control" name="loginid" value="{$stylist.loginid|escape}" maxlength="50" readonly>
                </div>
            </div>
            <div class="card-header border-bottom">
                <h6>
                    パスワード
                    <a class="float-right" data-toggle="tooltip" data-title="任意のパスワードを登録してください。 （６文字以上２０文字以内の半角英数字、及び記号）">
                        <i class="far fa-question-circle text-primary"></i>
                    </a>
                </h6>
            </div>
            <div class="card-body pb-2">
                <div class="form-group">
                    <input type="password" class="form-control{if isset($validation_errors.loginpw)} validation-error{/if}" name="loginpw" value="" maxlength="20">
                </div>
            </div>
            <div class="card-body pt-2">
                <div class="form-group text-center">
                    <button type="submit" class="btn btn-md btn-hairlogy w220">
                        更 新
                    </button>
                </div>
            </div>
            </form>

        </div>
    </div>
</div>
