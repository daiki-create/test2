<div class="short pdg-btm-400px pdg-top-100px">
    <div class="contents-title">
        {if $login.online_salon_status == 'new' || $login.online_salon_status == 'left'}
            <div class="title-60px">Input</div>
            <div class="title-30px">支払い情報入力</div>
        {elseif $login.online_salon_status == 'active' || $login.online_salon_status == 'inactive'}
            <div class="title-60px">Change</div>
            <div class="title-30px">支払い情報変更</div>
        {/if}
    </div>
    <div class="contents">
        {include file="../_alert.tpl"}

        <div class="precaution">
            <ul>
                <li>料金（毎月 ¥1,000 初月無料 )</li>
                <li>お申し込み内容を元に確定をするまで数日間頂く場合がございます</li>
                <li>万が一お申し込みをお断りさせて頂く場合は決済は行われません</li>
                <li>決済はトライアル終了後に開始されます</li>
                <li>ASuBiのグループ招待にはお時間を頂く場合がございます</li>
            </ul>
        </div>
        <div class="creditcard-box" >
            <form id="regster-form" class="text-center p-4" method="POST" action="/{$module}/{$class}/{if $login.online_salon_status == 'new'}register{else}re_register{/if}" >
                <input type="hidden" name="asubi_creditcard_token" value="{$asubi_creditcard_token}" >
                <script
                        type="text/javascript"
                        src="https://checkout.pay.jp/"
                        class="payjp-button"
                        data-text="{if $login.online_salon_status == 'new' || $login.online_salon_status == 'left'}カードで支払う{else}新しいカードを登録{/if}"
                        data-key="{$payjp_config.public_key}"
                        data-submit-text="登録"
                        data-partial="false"
                        data-token-name="payjp_token"
                        data-lang="ja"
                        data-name-placeholder="ICHIRO SUZUKI"
                    >
                </script>
            </form>
        </div>
    </div>
</div>

