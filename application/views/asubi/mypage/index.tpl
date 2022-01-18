<div class="bg-1">
    <div id="mask"></div>
    <div id="name-modal"><p>{$login.name|escape}</p></div>
    <div id="mail-modal"><p>{$login.loginid}</p></div>
    <div class="short pdg-btm-400px pdg-top-100px">
        <div class="contents-title">
            <div class="title-60px">MyPage</div>
            <div class="title-30px">マイページ</div>
        </div>
        <div class="contents">
                <div class="icon-img">
                    <img src="https://graph.facebook.com/{$login.sns_id}/picture?type=large" alt="プロフィール画像">
                </div>
                <div class="my-page-content font-24px">
                    <div class="my-page-item" id='my-page-name'>ユーザー名：<span>{$login.name|escape}</span></div>
                    <div class="my-page-item" id="my-page-mail">メールアドレス：<span>{$login.loginid}</span></div>
                </div>

                {if $login.online_salon_status == 'new'}
                    <div class="mypage-msg" >
                        お支払い情報が登録されていません。<br />
                        <a href="/{$module}/creditcard/form/" >こちら</a>
                        より登録をお願い致します。
                    </div>
                {elseif $login.online_salon_status == 'checking'}
                    <div class="mypage-msg">
                        只今、ご登録情報を確認しています。<br />
                        確認が完了しましたらFacebookのグループ「ASuBi」への招待メールをお送り致しますので今しばらくお待ちください。
                    </div>
                {elseif $login.online_salon_status == 'inactive'}
                    <div class="mypage-msg" >
                        ご登録されたお支払い情報で決済できませんでした。<br />
                        クレジットカードの状態をご確認の上、
                        <a href="/{$module}/creditcard/form/" >こちら</a>
                        よりお支払い情報の再登録をお願い致します。
                    </div>
                {elseif $login.online_salon_status == 'left'}
                    <div class="mypage-msg" >
                        既に退会済みです。<br />
                        再度入会される場合は、
                        <a href="/{$module}/creditcard/form/" >こちら</a>
                        よりお支払い情報の再登録をお願い致します。
                    </div>
                {else}
                    <button class="fb-btn" onclick="location='{$facebook_group_url}'">Facebookにログイン</button>
                {/if}


        </div>
    </div>
    <div class="fixed-btn" id="fixed-btn">
        <img src="/img/asubi/asubi-lp-design/material-img-parts/JPEG/1x/86-100.jpg" alt="">
    </div>
</div>



