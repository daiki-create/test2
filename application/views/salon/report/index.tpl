<div class="row">
    <div class="col-lg-10 offset-lg-1 col-xl-8 offset-xl-2">
        <div class="card mb-3">
            <div class="card-header page-title">
                <i class="fas fa-chart-line"></i> &nbsp;
                アンケート 一覧
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-10 offset-lg-1 col-xl-8 offset-xl-2">
        <div class="card">
            <table class="table table-sm table-fixed border-bottom mb-0">
                <thead>
                    <tr class="thead-light">
                        <th class="pl-3">アンケート名</th>
                        <th class="w70 px-1 text-center">今月</th>
                        <th class="w70 px-1 text-center">3ヵ月</th>
                        <th class="w70 px-1 text-center">6ヵ月</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $questionnaires as $questionnaire}

                    <tr class="clickable middle">
                        <td class="pl-1 pl-sm-2 pl-md-3 text-truncate middle">
                            <a href="/{$module}/{$class}/detail/{$questionnaire.questionnaire_id}/">
                                {$questionnaire.title|escape}
                            </a>
                        </td>
                        <td class="text-center px-1 px-sm-2">
                            <a href="/{$module}/{$class}/detail/{$questionnaire.questionnaire_id}/">
                            {$questionnaire.this_month_total|number_format}
                            </a>
                        </td>
                        <td class="text-center px-1 px-sm-2">
                            <a href="/{$module}/{$class}/detail/{$questionnaire.questionnaire_id}/">
                            {$questionnaire.three_month_total|number_format}
                            </a>
                        </td>
                        <td class="text-center px-1 px-sm-2">
                            <a href="/{$module}/{$class}/detail/{$questionnaire.questionnaire_id}/">
                            {$questionnaire.six_month_total|number_format}
                            </a>
                        </td>
                    </tr>
                    {/foreach}

                </tbody>
            </table>
        </div>
    </div>
</div>
