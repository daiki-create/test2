
<div class="row mt-50 fw-900">
    <div class="col-md-10 col-lg-10 offset-md-1  offset-lg-1 px-0">
        <ul class="nav nav-pills mypage-nav" id="report-tab" role="tablist">
            <li class="nav-item text-center">
                <a class="nav-link active" id="terms-of-service-tab" data-toggle="tab" href="#report-nps-tab" role="tab" aria-selected="false">
                    総合的レポート
                </a>
                <div class="w-75 mx-auto"></div>
            </li>
            <li class="nav-item text-center">
                <a class="nav-link" id="privacy-policy-tab" data-toggle="tab" href="#report-graph-tab" role="tab" aria-selected="false">
                    質問別レポート
                </a>
                <div class="w-75 mx-auto"></div>
            </li>
            <li class="nav-item text-center">
                <a class="nav-link" id="privacy-policy-tab" data-toggle="tab" href="#report-history-tab" role="tab" aria-selected="false">
                    回答履歴
                </a>
                <div class="w-75 mx-auto"></div>
            </li>
        </ul>
        <div class="tab-content">
            <!-- {include file="../../common/_alert.tpl"} -->
            <div id="report-nps-tab" class="card mypage-card py-3 px-lg-5 tab-pane active">
                <div class="card mb-3" style="padding:1em">

                    <!-- <div class="card-body grey lighten-4">
                        {if $login.manager_flag}
                        <div class="form-group dropdown mb-0">
                            <button type="button" id="nps-selected-stylist" class="form-control dropdown-toggle mr-3 text-truncate text-left w180"
                                data-toggle="dropdown" data-stylist-id="{$login.stylist_id}" aria-haspopup="true" aria-expanded="false">
                                {$login.name|escape}
                            </button>
                            <div id="nps-select-stylist" class="dropdown-menu select-stylist">
                                {foreach $stylists as $stylist}
                                <a class="dropdown-item py-3" data-stylist-id="{$stylist.id}">{$stylist.name|escape}</a>
                                {/foreach}
                            </div>
                        </div>
                        {/if}
                    </div> -->

                    <div class="card-header page-title">
                        <span class="mypage-name">アンケート名</span>{$questionnaire.title|escape}
                    </div>
                    <!-- <div class="comprehensive-top border-orange text-center mt-50">
                        <div class="row">
                            <div class="col-2">
                                <img class="comprehensive-top-img" src="/img/salon//report-woman.png" alt="女性の画像">
                            </div>
                            <div class="comprehensive-top-txt col-10">
                                <p class="fs-18">
                                    集計結果から分かるお客様との「きずな」を強くする為の
                                </p>
                                <p class="fs-24 color-orange">
                                    優先的課題とあなたの強み
                                </p>
                            </div>
                        </div>
                    </div> -->
                    <div class="comprehensive-top border-orange mt-100">
                        <div class="row">
                            <div class="comprehensive-top-txt">
                                <p class="">
                                    集計結果から分かるお客様との「きずな」を強くする為の
                                </p>
                                <p class="color-orange text-center">
                                    優先的課題とあなたの強み
                                </p>
                            </div>
                            <div class="">
                                <img class="comprehensive-top-img" src="/img/salon//report-woman.png" alt="女性の画像">
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-50">
                        <img src="/img/salon/report-strangth.png" alt="王冠アイコン">
                        <p class="fs-21 mt-10">
                            現在のあなたの「強み」は
                        </p>
                        <p class="fs-21">
                            <span class="question-item color-orange marker fs-35"></span>です
                        </p>
                    </div>

                    <div class="card-body px-1 px-md-3 border-top">
                        <div class="pt-0">
                            <div class="text-center scatter-chart">
                                <canvas id="nps-current-scatter-chart" class="px-1 px-sm-3 pt-3 border mb-3"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="comprehensive-bottom border-orange mt-50">
                        <div class="row">
                            <div class="comprehensive-bottom-txt">
                                <p class="">
                                    お客様がお店やあなたに対してどれくらいの愛着や信頼があるかが分かる
                                </p>
                                <p class="color-orange text-center">
                                    ファン度
                                </p>
                            </div>
                            <div class="">
                                <img class="comprehensive-bottom-img" src="/img/salon//report-man.png" alt="女性の画像">
                            </div>
                        </div>
                    </div>
                    <div id="what-fan-frequency" class="card-body collapse show">
                        <div class="alert grey lighten-3 fs-18 fw-100">
                        　ファン度とはお客様がどれだけスタイリストを特別視しているかを数値化したものになります。<br><br>
                        　折れ線グラフのポップアップ <i class="fas fa-crown average-fa"></i> は、アンケート項目の中でどれが一番評価が高かったかを表し、<br>
                        　折れ線グラフのポップアップ <i class="fas fa-crown nps-fa"></i> は意識して伸ばすとリピート率向上につながりやすいアンケート項目になります。<br>
                        <br>
                        　また、顧客愛着率はアンケートに回答してくださったお客様全体の中のリピーターにつながる可能性のあるお客様の割合となっております。
                        </div>
                    </div>

                    <div class="card-body px-1 px-md-3">
                        <div class="pt-0">
                            <div class="text-center">
                                <canvas id="nps-line-chart" class="p-3 border"></canvas>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div id="report-graph-tab" class="card mypage-card py-3 px-lg-5 tab-pane">
                <div class="card mb-3" style="padding:1em">
                    <div class="card-header page-title">
                        <span class="mypage-name">アンケート名</span>{$questionnaire.title|escape}
                    </div>
                    <div class="card-body border-top">
                        <div class="d-flex">

                            <!-- {if $login.manager_flag}
                            <div class="form-group dropdown mb-0 mr-2 mr-sm-3">
                                <button type="button" id="selected-stylist" class="form-control dropdown-toggle text-truncate text-left w160"
                                    data-toggle="dropdown" data-stylist-id="{$login.stylist_id}" aria-haspopup="true"
                                    aria-expanded="false">
                                    {$login.name|escape}
                                </button>
                                <div id="select-stylist" class="dropdown-menu select-stylist">
                                    {foreach $stylists as $stylist}
                                    <a class="dropdown-item py-3" data-stylist-id="{$stylist.id}">{$stylist.name|escape}</a>
                                    {/foreach}
                                </div>
                            </div>
                            {/if} -->

                            <div class="form-group dropdown mb-0">
                                <button type="button" id="selected-term" class="form-control dropdown-toggle text-center w140" data-toggle="dropdown"
                                    data-term="30days" aria-haspopup="true" aria-expanded="false">
                                    30日間
                                </button>
                                <div id="select-term" class="dropdown-menu" data-questionnaire-id="{$questionnaire.id}">
                                    <a class="dropdown-item py-3" data-term="30days">30日間</a>
                                    <a class="dropdown-item py-3" data-term="this_month">今月</a>
                                    <a class="dropdown-item py-3" data-term="last_month">先月</a>
                                    <a class="dropdown-item py-3" data-term="half_year">6ヵ月間</a>
                                    <a class="dropdown-item py-3" data-term="one_year">1年間</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="nps-result" class="card-body pt-0 px-0" style="display:none;">
                        <!-- <h4 class="border-bottom text-center">
                            <i class="far fa-file-alt fa-lg"></i>
                            結 果
                        </h4> -->
                        <!-- <div class="alert text-center orange lighten-5 small px-2 px-sm-3 result-alert">
                            <span class="stylist-name"></span>
                                あなたが、ロイヤルカスタマーに重視されているのは<br> 
                            <span class="question-item"></span>です。
                        </div> -->
                        <!-- <div class="text-center mt-50　result-alert">
                            <img src="/img/salon/report-strangth.png" alt="王冠アイコン">
                            <p class="fs-21 mt-10">
                                あなたの「強み」は
                            </p>
                            <p class="fs-21">
                                <span class="question-item color-orange marker fs-35"></span>です
                            </p>
                        </div> -->
                        <div class="alert text-center orange lighten-5 small not-recomend-err-alert" style="display:none;">
                            アンケート項目の変更をお勧め致します。
                        </div>
                        <div class="alert text-center orange lighten-5 small result-err-alert" style="display:none;">
                            アンケート回答数が少ないため、正確な結果が出ておりません。
                        </div>
                        <div class="text-center scatter-chart">
                            <canvas id="nps-scatter-chart" class="p-2 p-sm-3 border mb-3"></canvas>
                        </div>
                        <div class="alert grey lighten-4 small">
                            <div class="row">
                                <div class="col-12 col-md-11 offset-md-1 col-lg-9 offset-lg-2">
                                    　この象現グラフはあなたの強みと弱みを視覚的にわかるようにしたグラフです。<br>
                                    　四つのゾーンのうち色の濃いゾーンに打点されたアンケート項目があなたの強みになります。<br>
                                    　最も色の濃い強みゾーンに打点されるよう頑張りましょう！
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        {foreach $questionnaire.questions as $question}
                        {if $question.type == 'level'}
                        <div class="col-12">
                            <div class="card-body">
                                <div class="border-orange py-3 mb-30">
                                    <span class="d-inline-block mb-2 mr-2 fw-900">Ｑ{$question.number}</span>
                                    <span class="small color-orange fw-900">{$question.question|escape}</span>
                                </div>
                                <!-- <p>
                                    <span class="badge badge-pill badge-primary d-inline-block mb-2 mr-2">Ｑ{$question.number}</span>
                                    <span class="small">{$question.question|escape}</span>
                                    <a class="collapse-btn" data-toggle="collapse" data-target="#question-collapse-{$question.number}">
                                        <i class="fas fa-chevron-circle-down fa-lg"></i>
                                    </a>
                                </p> -->
                                <div id="question-collapse-{$question.number}" class="">
                                    <div class="jumbotron text-center p-2 mb-0">
                                        <div class="row">
                                            <div class="col-12 col-lg-6 count-answer-chart">
                                                <label>回答数</label>
                                                <canvas id="level-chart-question-{$question.number}"
                                                    data-question-number="{$question.number}"></canvas>
                                            </div>
                                            <div class="col-12 col-lg-6 average-transition-chart">
                                                <label>平均値推移</label>
                                                <canvas id="line-chart-question-{$question.number}"
                                                    data-question-number="{$question.number}"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    {if $question.sub_question}
                                    <div class="form-group border p-3 my-2">
                                        <label class="small">【 {$question.sub_question|escape} 】</label>
                                        <div id="sub-question-{$question.id}" class="carousel slide sub-question" data-ride="carousel" data-question-id="{$question.id}">
                                            <div class="carousel-inner">
                                            </div>
                                            <a class="carousel-control-prev" href="#sub-question-{$question.id}" role="button" data-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            </a>
                                            <a class="carousel-control-next" href="#sub-question-{$question.id}" role="button" data-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            </a>
                                        </div>
                                    </div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        {elseif $question.type == 'select_one'}
                        <div class="col-12">
                            <div class="card-body">
                                <div class="border-orange py-3  mb-30">
                                    <span class="d-inline-block mb-2 mr-2 fw-900">Ｑ{$question.number}</span>
                                    <span class="small color-orange fw-900">{$question.question|escape}</span>
                                </div>
                                <!-- <p>
                                    <span class="badge badge-pill badge-primary d-inline-block mb-2 mr-2">Ｑ{$question.number}</span>
                                    <span class="small">{$question.question|escape}</span>
                                    <a class="collapse-btn" data-toggle="collapse" data-target="#question-collapse-{$question.number}">
                                        <i class="fas fa-chevron-circle-down fa-lg"></i>
                                    </a>
                                </p> -->
                                <div id="question-collapse-{$question.number}" class="">
                                    <ul class="list-inline text-center mt-1"></ul>
                                </div>
                            </div>
                        </div>
                        {/if}
                        {/foreach}
                    </div>
                </div>
            </div>

            <div id="report-history-tab" class="card mypage-card py-3 px-lg-5 tab-pane">
                <div class="card mb-3" style="padding:1em">
                    <div class="card-header">
                        <span class="receipt-name">アンケート名</span>{$questionnaire.title|escape}
                    </div>
                    <table id="report-history-table" class="table table-fixed border-bottom">
                        <thead>
                            <tr class="orange">
                                <th class="text-center receipt-th">No. </th>
                                <th class="receipt-th">日付</th>
                                <th class="receipt-th">時刻</th>
                                <th class=""></th>
                            </tr>
                        </thead>
                        <tbody id="replies">
                        </tbody>
                        <tbody id="replies-dummy" style="display:none;">
                            <tr data-reply-id="" data-answer-datetime="">
                                <td class="text-center">
                                    <p class="reply-id"></p>
                                </td>
                                <td>
                                    <p class="reply-date"></p>
                                </td>
                                <td>
                                    <p class="reply-time"></p>
                                </td>
                                <td>
                                    <button class="clickable btn-out-orange color-orange w-100">
                                        詳細 ▶
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="report-history-pagination" class="text-center">
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>