<div class="short pdg-btm-400px pdg-top-100px">
    <div class="contents-title">
        <div class="title-60px">Change</div>
        <div class="title-30px">支払い情報変更</div>
    </div>
    <div class="contents">
        {include file="../_alert.tpl"}
        <div class="creditcard-box" >
            <form id="regster-form" class="text-center p-4" method="POST" action="/{$module}/{$class}/update" >
                <input type="hidden" name="asubi_creditcard_token" value="{$asubi_creditcard_token}" >
                <script
                        type="text/javascript"
                        src="https://checkout.pay.jp/"
                        class="payjp-button"
                        data-text="新しいカードを登録"
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


