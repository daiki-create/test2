<div class="row mt-50">
    <div class="col-md-10 col-lg-10 offset-md-1  offset-lg-1 px-0">
        {include file="../../common/_alert.tpl"}
        <ul class="nav nav-pills mypage-nav" id="report-tab" role="tablist">
            <li class="nav-item text-center">
                <a class="nav-link active" id="terms-of-service-tab" data-toggle="tab" href="#mypage" role="tab" aria-selected="false">
                    マイページ
                </a>
                <div class="w-75 mx-auto"></div>
            </li>
            <li class="nav-item text-center">
                <a class="nav-link" id="privacy-policy-tab" data-toggle="tab" href="#card" role="tab" aria-selected="false">
                    支払い情報登録
                </a>
                <div class="w-75 mx-auto"></div>
            </li>
            <li class="nav-item text-center">
                <a class="nav-link" id="privacy-policy-tab" data-toggle="tab" href="#left" role="tab" aria-selected="false">
                    退会手続き
                </a>
                <div class="w-75 mx-auto"></div>
            </li>
        </ul>
        <div class="tab-content">
            <div id="mypage" class="card mypage-card  py-3 px-lg-5 tab-pane active">
                <div class="card mb-3" style="padding:1em">
                    <div class="card-header page-title">
                        <span class="mypage-name">お名前</span>{$stylist.name|escape} 様
                    </div>
                    <form id="" method="POST" action="/{$module}/{$class}/update/{$stylist.salon_id}/{$stylist.id}/">
                        <div class="pt-30">
                            <h6 class="">氏 名</h6>
                            <div class="form-group">
                                <div class="row">
                                    <!-- valueを入れてください -->
                                    <div class="col-12">
                                        <input type="text" class="form-control form-control-sm mb-1{if isset($validation_errors.kana)} validation-error{/if}" name="kana" value="{$stylist.kana|escape|default:''}" maxlength="50" placeholder="カナ">
                                    </div>
                                    <div class="col-12">
                                        <input type="text" class="form-control{if isset($validation_errors.name)} validation-error{/if}" name="name" value="{$stylist.name|escape}" maxlength="30" required>
                                    </div>
                                </div>
                            </div>
                        </div>
        
                        <div class="pt-30">
                            <h6 class="">ログインID</h6>
                            <div class="form-group">
                                <input type="email" class="form-control" name="loginid" value="{$stylist.loginid|escape}" maxlength="50" readonly>
                            </div>
                        </div>
                    
                        <div class="pt-30">
                            <h6 class="">パスワード</h6>
                            <div class="form-group">
                                <input type="password" class="form-control{if isset($validation_errors.loginpw)} validation-error{/if}" name="loginpw" value="" maxlength="20">
                            </div>
                        </div>
                        
                        <div class="form-group text-center mb-0">
                            <button type="submit" class="btn btn-md btn-hairlogy w220">
                                更 新
                            </button>
                        </div>
                        {if $stylist.trial_limited_on && $today < $stylist.trial_limited_on}
                        <div class="input-group">
                            <div class="input-group-prepend"><div class="input-group-text text-primary">トライアル終了日</div></div>
                            <div class="form-control text-center w150">{$stylist.trial_limited_on|date_format:'%Y年%-m月%-d日'}</div>
                        </div>
                        {/if}
                    </form>
                </div>
            </div>

            <div id="card" class="card mypage-card pt-200 pb-200 tab-pane text-center">
                <script
                type="text/javascript"
                src="https://checkout.pay.jp/"
                class="payjp-button"
                data-key="pk_test_0383a1b8f91e8a6e3ea0e2a9"
                data-submit-text="トークンを作成する"
                data-partial="true">
                </script>
            </div>

            <div id="left" class="card mypage-card pt-200 pb-200 tab-pane text-center fs-18">
                <p>下記ボタンを押すと退会手続きを実行します。</p>
                <div class="row">
                    <div class="col-4 offset-2">
                        <button class="btn btn-md btn-hairlogy w-100 ws-nw" onclick="location.href='/{$module}/{$class}/'">戻る</button>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-md btn-white w-100 ws-nw" data-toggle="modal" data-target="#modal1">退会する</button>
                    </div>
                </div>
            </div>

            <div class="modal fade text-center" id="modal1" tabindex="-1"
                role="dialog" aria-labelledby="label1" aria-hidden="true">
                <div class="modal-dialog pt-100" role="document">
                    <div class="modal-content pt-50 pb-100">
                        <div class="modal-body">
                            <img src="/img/salon/alert.png" alt="警告マーク">
                            <p class="text-danger mt-30 fs-18">
                                ユーザ情報やレポートなどの保存されているデータは<br>
                                全て失われます。
                            </p>
                            <p class="mb-30 fs-18">退会しますか？</p>
                            <div class="row">
                                <div class="col-4 offset-2">
                                    <button class="btn btn-md btn-hairlogy w-100 ws-nw" onclick="location.href='/{$module}/{$class}/left/'">はい</button>
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-md btn-white w-100 ws-nw" onclick="location.href='/{$module}/{$class}/'">戻る</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>