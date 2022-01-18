<div class="short pdg-btm-400px pdg-top-100px">
    <div class="contents-title">
        <div class="title-60px">Inquiry</div>
        <div class="title-30px">お問い合わせ</div>
    </div>
    <div class="contents">
        <div>
            {include file="../_alert.tpl"}
        </div>
        {if !isset($login)}
        <div class="inquiry-top font-20px">入会に関する質問など
        {else}
        <div class="inquiry-top font-20px">本サービスに関する質問など
        {/if}
        <br class="sp-br">下記よりお気軽にご相談ください。</div>
        <form id="inquiry-form" action="/{$module}/inquiry/confirm" method="post" class="inquiry-form">
            {if !isset($login)}
            <div class="form-item">
                <label for=""><div class="must">必須</div><div class="label-name font-20px">お名前</div></label><br class="sp-br">
                <input required name="name" id="name" class="inquiry-input" type="text" placeholder="例）門手 太郎" value="{$inquiry.name|escape}">
            </div>
            <div id="name-error"></div>

            <div class="form-item">
                <label for=""><div class="must">必須</div><div class="label-name font-20px">メールアドレス</div></label><br class="sp-br">
                <input required name="mail" id="mail" class="inquiry-input" type="email" placeholder="例）mail@montecampo.com" value="{$inquiry.mail|escape}">
            </div>
            <div id="mail-error"></div>
            {/if}

            <div>
                <label for=""><div class="must">必須</div><div class="label-name font-20px">ご質問・お問い合わせ</div></label><br class="sp-br">
                <textarea required name="inquiry" id="inquiry" class="inquiry-textarea" cols="30" rows="10">{$inquiry.inquiry|escape|default:''}</textarea>
            </div>
            <div id="inquiry-error"></div>

            <button type="submit" class="inq-btn">
                <img id="btn-submit" src="/img/asubi/asubi-lp-design/material-img-parts/JPEG/1x/88-100.jpg" alt="">
            </button>
        </form>
    </div>
</div>
<div class="fixed-btn" id="fixed-btn">
    <img src="/img/asubi/asubi-lp-design/material-img-parts/JPEG/1x/86-100.jpg" alt="">
</div>

