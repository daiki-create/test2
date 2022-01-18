
<div class="card">
    <div class="card-header amber darken-4">
        <div class="d-flex">
            <h5 class="mb-0">{$questionnaire.title|escape|default:''}</h5>
            {if empty($questionnaire.status)}
            <span class="badge badge-light ml-auto py-2">
                停止中
            </span>
            {/if}
        </div>
    </div>
    <div class="card-body">

        {include file="../../common/_alert.tpl"}

        <table class="table table-sm table-fixed border">
        <thead>
        <tr class="thead-light">
            <th class="text-center w80">
                No.
            </th>
            <th class="">
                質問
            </th>
            <th class="text-center w120">
                回答種別
            </th>
            <th class="text-center w60">
                NPS
            </th>
            <th class="text-center w60">
                ﾌﾘｰ入力
            </th>
        </tr>
        </thead>
        <tbody class="">
        {foreach $questions as $question}

        <tr>
            <td class="text-center middle">
                {if empty($question.number)}
                Intro
                {else}
                <i class="fab fa-quora"></i>{$question.number}
                {/if}
            </td>
            <td class="">
                {$question.question|escape|default:''}
            </td>
            <td class="h6 text-center middle">
                {if $question.type == 'level'} <span class="badge aqua-gradient">評価 ({$question.min_level}-{$question.max_level})</span>
                {elseif $question.type == 'select_one'} <span class="badge purple-gradient">選択(一つ)</span>
                {elseif $question.type == 'select_multi'} <span class="badge peach-gradient">選択(複数)</span>
                {elseif $question.type == 'text'} <span class="badge blue-gradient">テキスト</span>
                {/if}

            </td>
            <td class="h5 text-center middle">
                {if $question.nps_flag}<span class="badge badge-primary">NPS</span>
                {elseif $question.nps_correlation_flag}<span class="badge badge-light" data-toggle="tooltip" data-title="{$question.item|escape}">相関</span>{/if}
            </td>
            <td class="h5 text-center middle">
                {if ! empty($question.number) && $question.sub_question}
                <i class="far fa-check-square text-info"></i>
                {/if}
            </td>
        </tr>
        {/foreach}

        </tbody>
        </table>

    </div>
    <div class="card-body text-center">
        <div class="row">
            <div class="col-4 offset-4">
                <a class="btn btn-sm btn-warning w160 my-0" href="/{$module}/{$class}/form/{$questionnaire_id}/">
                    アンケート 編 集
                </a>
            </div>
            {if empty($questionnaire.status)}
            <div class="col-2 text-center">
                <button id="enable-questionnaire-btn" type="button" class="btn btn-sm btn-primary w100 m-0">
                    有効化
                </button>
            </div>
            <div class="col-2 text-right">
                <button id="remove-questionnaire-btn" type="button" class="btn btn-sm btn-outline-danger w100 m-0">
                    削 除
                </button>
            </div>
            {else}
            <div class="col-2 text-center">
                <button id="disable-questionnaire-btn" type="button" class="btn btn-sm btn-outline-default w100 m-0">
                    停 止
                </button>
            </div>
            {/if}
        </div>
    </div>
</div>

<form id="enable-questionnaire-form" method="POST" action="/{$module}/{$class}/enable/">
    <input type="hidden" name="questionnaire_id" value="{$questionnaire_id}">
</form>
<form id="disable-questionnaire-form" method="POST" action="/{$module}/{$class}/disable/">
    <input type="hidden" name="questionnaire_id" value="{$questionnaire_id}">
</form>
<form id="remove-questionnaire-form" method="POST" action="/{$module}/{$class}/delete/">
    <input type="hidden" name="questionnaire_id" value="{$questionnaire_id}">
</form>
