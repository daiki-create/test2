

<div class="card">
    <div class="card-header text-truncate py-3 px-2 px-sm-3 page-title{if $class == 'login'} orange accent-4{/if}">
        <ul class="nav nav-tabs" id="report-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="terms-of-service-tab" data-toggle="tab" href="#terms-of-service" role="tab" aria-selected="false">
                    ご利用規約
                </a>
                <div class="w-75 mx-auto"></div>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="privacy-policy-tab" data-toggle="tab" href="#privacy-policy" role="tab" aria-selected="false">
                    プライバシーポリシー
                </a>
                <div class="w-75 mx-auto"></div>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane fade active show" id="terms-of-service">
                <div class="card-body py-4 px-2 px-sm-4">

                    {include file="./tos/_term_of_service.tpl"}

                </div>
            </div>
            <div class="tab-pane fade" id="privacy-policy">
                <div class="card-body p-4">

                    {include file="./tos//_privacy_policy.tpl"}

                </div>
            </div>
        </div>
        <hr>
        <h5>2019 年 10 月 1 日制定</h5>
    </div>
</div>


