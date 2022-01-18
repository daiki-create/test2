<div class="row">
    <div class="col-12 col-md-10 col-lg-6 offset-md-1 offset-lg-3">
        <div class="text-center mb-50">
            <p class="title-en fs-18">
                Request
            </p>
            <img class="text-center" src="/img/logo_hairlogy.png" alt="ロゴ">
            <p class="title-ja color-orange fs-20">
                パスワード再登録申請
            </p>
        </div>
        <div class="card">
            <div class="card-body">
                {include file="../../common/_alert.tpl"}
                <p class="description">
                    下記フォームよりご登録済みのログインIDを入力し、再登録申請をクリックしてください。<br/>
                    お客様のメールアドレス宛てにパスワード再設定用のURLが記載されたメールをお送りしますので、<u>24時間以内にメールに記載されたURLよりパスワードの再登録を行なってください</u>。
                </p>
                <form id="reregist-form" class="text-center p-1 p-sm-4" method="POST" action="/{$module}/{$class}/reregist/">

                    <div class="form-group">
                        <input type="text" class="form-control" name="loginid" placeholder="ログインID" value="{$loginid|default:''}" required>
                    </div>

                    <div class="form-group p-3">
                        <button type="submit" class="btn btn-hairlogy darken-4 text-white btn-block p-2 mx-auto w240" form="reregist-form">再登録申請</button>
                    </div>
                    <div class="form-group">
                        <a href="/{$module}/{$class}/">ログインページ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
