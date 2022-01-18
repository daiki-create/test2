<div class="bg-1">
    <div class="short pdg-btm-400px pdg-top-100px">
        <div class="contents-title">
            <div class="title-60px">Withdrawal</div>
            <div class="title-30px">退会</div>
        </div>
        <div class="contents">
            {include file="../../common/_alert.tpl"}
            <div class="textalign-center">
                退会後は、会員向けサービスが利用できなくなります。<br>下記より「退会する」ボタンをクリックすると退会手続きが完了します。
            </div>
            <div>
                <button type="button" class="btn-referer" onclick="history.back()">
                    戻る
                </button>
                <button type="submit" form="withdrawal-form" class="btn-left">
                    退会する
                </button>
            </div>
        </div>
    </div>
    <div class="fixed-btn" id="fixed-btn">
        <img src="/img/asubi/asubi-lp-design/material-img-parts/JPEG/1x/86-100.jpg" alt="">
    </div>
</div>

<form id="withdrawal-form" method="post" action="/{$module}/{$class}/leave/"></form>
