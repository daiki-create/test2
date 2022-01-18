<div class="short pdg-btm-400px pdg-top-100px">
    <div class="contents-title">
        <div class="title-60px">inquiry</div>
        <div class="title-30px">内容のご確認</div>
    </div>
    <div class="contents">

        <form id="inquiry-form" action="/{$module}/inquiry/send/" method="post" class="inquiry-form">
            {if !isset($login)}
                <div class="form-item">
                    <label for=""><div class="label-name font-20px">お名前</div></label><br class="sp-br">
                    <input required name="name" id="name" class="inquiry-input" type="text" value="{if ! empty($login)}{$login.name|escape}{else}{$inquiry.name|escape}{/if}" readonly>
                </div>
                <div id="name-error"></div>

                <div class="form-item">
                    <label for=""><div class="label-name font-20px">メールアドレス</div></label><br class="sp-br">
                    <input required name="mail" id="mail" class="inquiry-input" type="email" value="{if ! empty($login)}{$login.loginid|escape}{else}{$inquiry.mail|escape}{/if}" readonly>
                </div>
                <div id="mail-error"></div>
            {/if}

            <div>
                <label for=""><div class="label-name font-20px">ご質問・お問い合わせ</div></label><br class="sp-br">
                <textarea required name="inquiry" id="inquiry" class="inquiry-textarea" cols="30" rows="10" maxlength="1000" readonly>{$inquiry.inquiry|escape|default:''}</textarea>
            </div>
            <div id="inquiry-error"></div>
            <div>
                <button type="submit" class="btn-inquiry-submit">送信する</button>
            </div>
        </form>
    </div>
</div>
<div class="fixed-btn" id="fixed-btn">
    <img src="/img/asubi/asubi-lp-design/material-img-parts/JPEG/1x/86-100.jpg" alt="">
</div>

