
<div class="card mb-3">
    <div class="card-header text-truncate page-title">
        {$questionnaire.title|escape}
        プレビュー
    </div>
</div>

<div class="card">
    <div class="grey darken-3">
        <div class="row">
            <div class="col-4">
                <div class="card-body grey darken-3 text-white py-1">
                    <i class="fas fa-signal"></i>
                </div>
            </div>
            <div class="col-4 text-center">
                <div class="card-body grey darken-3 text-white py-1">
                    {$smarty.now|date_format:'%H:%M'}
                </div>
            </div>
            <div class="col-4 text-right">
                <div class="card-body grey darken-3 text-white py-1">
                    <i class="fas fa-battery-three-quarters"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-5">
        <h3 class="text-center pl-2 mb-4">
            <p>
                <i class="fas fa-slash fa-lg"></i>
                ありがとうございました。
                <i class="fas fa-slash fa-lg"></i>
            </p>
        </h3>
        <h6 class="text-info text-center">
            {if ! empty($yyyymmdd)}

            <p class="p-3">ご回答日： {$yyyymmdd|date_format:'%Y年%-m月%-d日'}</p>
            {/if}

        </h6>
    </div>
    <div class="card-footer text-center">
        {if $lp_url}
        <a class="btn btn-lg btn-outline-light" href="{$lp_url|escape}">
            <span class="text-muted">サロンページへ</span>
        </a>
        {/if}
    </div>
</div>
