

<div class="row mt-50">
    <div class="col-md-12 col-lg-10  offset-lg-1 px-0">
        {include file="../../common/_alert.tpl"}
        <ul class="nav nav-pills mypage-nav" id="report-tab" role="tablist">
            <li class="nav-item text-center">
                <a class="nav-link" id="terms-of-service-tab" href="/{$module}/questionnaire/">
                    アンケートQRコード
                </a>
                <div class="w-75 mx-auto"></div>
            </li>
            <li class="nav-item text-center">
                <a class="nav-link active" id="privacy-policy-tab" href="#conf">
                    アンケートページ設定
                </a>
                <div class="w-75 mx-auto"></div>
            </li>
        </ul>
        <div class="tab-content">

            <div id="conf" class="card mypage-card pt-50 pb-50 tab-pane text-center px-3 active">            
                <div class="pb-30 fw-600">
                    アンケート終了後に、ジャンプさせる任意のWebページを登録します。
                </div>
                <table class="table table-sm table-hover table-fixed text-center2">
                    <thead>
                        <tr class="orange text-white">
                            <th class="text-center w110">
                                適用期間
                            </th>
                            <th>
                                Webページアドレス(URL)
                            </th>
                            <th class="text-center w80">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $landing_pages as $landing_page}
                        <tr class="clickable" data-landing-page-id="{$landing_page.id}">
                            <td class="text-center middle clickable period{if (! empty($landing_page.since_date) && $landing_page.since_date >= $today) OR ( ! empty($landing_page.until_date) && $landing_page.until_date <= $today)} text-muted{/if}">
                                <span class="since-date">{if ! empty($landing_page.since_date)}{$landing_page.since_date|date_format:'%Y/%-m/%-d'}<br>{/if}</span>
                                ～
                                <span class="until-date">{if ! empty($landing_page.until_date)}<br>{$landing_page.until_date|date_format:'%Y/%-m/%-d'}{/if}</span>
                            </td>
                            <td class="text-truncate middle clickable" data-toggle="modal">
                                <span class="lp-url">{$landing_page.lp_url|escape}</span>
                            </td>
                            <td class="text-truncate middle clickable" data-toggle="modal" data-target="#update-lp-modal">
                                <button class="btn-out-orange">
                                    編 集 ▶
                                </button>
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
                <div class="text-center">
                    <button type="button" id="create-lp-btn" class="btn btn-md btn-hairlogy m-0 w200" data-toggle="modal" data-target="#lp-modal">
                        新規登録
                    </button>
                </div>
            </div>  
        </div>    

    </div>
</div>
