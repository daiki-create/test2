
<div class="card">
    <div class="card-body pt-100 pb-100 px-0">
        <h3 class="text-center px-0 mb-4">
            <p class="thanks">
                <i class="fas fa-slash fa-lg"></i>
                ありがとうございました。
                <i class="fas fa-slash fa-lg"></i>
            </p>
        </h3>
        <h6 class="text-info text-center">
            {if ! empty($yyyymmdd)}

            <p class="py-3">ご回答日： {$yyyymmdd|date_format:'%Y年%-m月%-d日'}</p>
            {/if}

        </h6>
    </div>
    <div class="card-footer text-center">
        {if $lp_url}
        <a class="btn btn-lg btn-outline-light" href="{$lp_url|escape}">
            <span class="text-muted">サロンサイトへ</span>
        </a>
        {/if}
    </div>
</div>
