

<div class="row mt-50">
    <div class="col-md-12 col-lg-10  offset-lg-1 px-0">
        {include file="../../common/_alert.tpl"}
        <ul class="nav nav-pills mypage-nav" id="report-tab" role="tablist">
            <li class="nav-item text-center">
                <a class="nav-link active" id="terms-of-service-tab" href="#qr">
                    アンケートQRコード
                </a>
                <div class="w-75 mx-auto"></div>
            </li>
            <li class="nav-item text-center">
                <a class="nav-link" id="privacy-policy-tab" href="/{$module}/lp/">
                    アンケートページ設定
                </a>
                <div class="w-75 mx-auto"></div>
            </li>
        </ul>
        <div class="tab-content">
            <div id="qr" class="card mypage-card  py-3 px-lg-5 tab-pane active">
                <div class="card mb-3" style="padding:1em">
                    <div class="row justify-content-center">
                        {foreach $questionnaires as $questionnaire}
                        
                        <div class="mb-4 qr-desc text-center mt-30 fw-600">
                            お客様にこのＱＲコードをスキャンしていただくか、<br>
                            直接アドレスにアクセスいただくと<br class="d-md-none">
                            アンケートページを表示することができます。<br>
                            <p>
                                ※ご回答後{$reply_interval_days}日間は、<br class="d-md-none">
                                再度回答できないようになっておりますのでご注意下さい。
                            </p>
                        </div>
                        <div class="col-11 col-md-7 col-lg-7 mb-4">
                            <div class="card mb-3 mt-30">
                                <div class="row questionnaire-title-parent">
                                    <div class="col-2 questionnaire-icon text-center">
                                        <img class="" src="/img/salon/questionnaire.png" alt="アンケートアイコン">
                                    </div>
                                    <div class="col-10 card-header text-truncate page-title">
                                        {$questionnaire.title|escape}
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3 qr-codes">
                                <div class="card-body text-center pb-3 questionnaire">
                                    <div class="print-area text-center{if $login.manager_flag} hairlogy-salon-manager{/if}">
                                        <div class="form-group text-center">
                                            <input type="text" class="questionnaire-url border-0 text-center w-100" value="{$questionnaire.url}" readonly>
                                        </div>
                                    {if $questionnaire.status}
                                        <div class="form-group text-center">
                                            <img src="/dl/image/qr_code/{$salon_id}/{$questionnaire.code}" alt="{$questionnaire.url}" class="img-fluid img-thumbnail qr-code-img">
                                        </div>
                                    </div>
                                    
                                    <div class="row action-buttons">
                                        <div class="col-4 text-center">
                                            <a class="clipboard text-black-50" href="#" data-url="{$questionnaire.url}">
                                                <i class="far fa-copy fa-3x"></i>
                                                <p class="small py-2">
                                                    クリップボード<br>コピー
                                                </p>
                                            </a>
                                        </div>
                                        <div class="col-4 text-center">
                                            <a class="preview text-black-50" href="/{$module}/{$class}/preview/{$questionnaire.questionnaire_id}/" target="_preview">
                                                <i class="far fa-window-restore fa-3x"></i>
                                                <p class="small py-2">
                                                    アンケート<br>
                                                    プレビュー
                                                </p>
                                            </a>
                                        </div>
                                        <div class="col-4 text-center">
                                            <a class="printout-btn text-black-50" href="#">
                                                <i class="fas fa-print fa-3x"></i>
                                                <p class="small py-2">
                                                    ＱＲコード<br>
                                                    印刷
                                                </p>
                                            </a>
                                        </div>
                                    {else}
                                        このアンケートは現在無効になっています。
                                    {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {/foreach}
                    
                    </div>

                </div>
            </div>

            <div id="conf" class="card mypage-card pt-50 pb-50 tab-pane text-center px-3">            
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
                            <td class="text-center middle">
                                <button class="btn-out-orange" onclick="location.href='/{$module}/{$class}/form_update'">
                                    編 集 ▶
                                </button>
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
                <div class="text-center">
                    <button type="button" id="create-lp-btn" class="btn btn-md btn-hairlogy m-0 w200" onclick="location.href='/{$module}/{$class}/form_create'">
                        新規登録
                    </button>
                </div>
            </div>  
        </div>    

    </div>
</div>
