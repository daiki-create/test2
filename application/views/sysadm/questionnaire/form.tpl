
<div class="card w1100">
    <div class="card-header amber darken-4">
        {$questionnaire.title|escape|default:'新規登録'}
    </div>
    <div class="card-body amber lighten-5 px-2">

        {if isset($validation_errors)}
        <div class="alert alert-warning p-2">
        {foreach $validation_errors as $q => $errors}
            <div class="row">
                <div class="col-1 text-center px-0"><span class="small">{$q}</span></div>
                <div class="col-11 px-0">
                {foreach $errors as $q => $error_message}
                    <span class="small">{$error_message|escape}</span>
                {/foreach}
                </div>
            </div>
            {if ! $errors@last}<hr class="my-1">{/if}
        {/foreach}
        </div>
        {/if}

        <form id="questionnaire-form" method="POST" action="/{$module}/{$class}/{if $questionnaire_id}update/{$questionnaire_id}{else}create{/if}/">
        <div class="d-flex">
            <div class="form-group mr-2 w800">
                <div class="input-group">
                    <div class="input-group-prepend"><div class="input-group-text pr-1">アンケート名：</div></div>
                    <input type="text" class="form-control" name="title" value="{$questionnaire.title|escape|default:''}" maxlength="50" required>
                </div>
            </div>
            <div class="form-group ml-3">
                <div class="input-group">
                    <div class="input-group-prepend"><div class="input-group-text pr-1">アンケート種別：</div></div>
                    <select class="custom-select" name="type" required>
                        <option value="hair_salon"{if $questionnaire.type == 'hair_salon'} selected{/if}>ヘアサロン</option>
                        <option value="esthetic_salon"{if $questionnaire.type == 'esthetic_salon'} selected{/if}>エステサロン</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend"><div class="input-group-text pr-1">説 明：</div></div>
                <input type="text" class="form-control" name="note" value="{$questionnaire.note|escape}" maxlength="100">
            </div>
        </div>
        <table id="questions-table" class="table table-sm table-fixed table-input table-bordered">
        <thead>
        <tr class="thead-light">
            <th class="text-center w60">
                No.
            </th>
            <th class="">
                質問
            </th>
            <th class="text-center w100">
                回答種別
            </th>
            <th class="text-center px-1 w60">
                ﾌﾘｰ入力
            </th>
            <th colspan="2" class="text-center px-1 w60">
                順番
            </th>
            <th class="text-center px-1 w60">
                削除
            </th>
        </tr>
        </thead>
        {foreach $questionnaire.questions as $question_id => $question}
        <tbody class="" data-has-sub-question="{$question.has_sub_question}">

        <tr class="middle white question-tr">
            <td{if $question.type != 'message' && ! empty($question.has_sub_question)} rowspan="3"{elseif  $question.type != 'text'} rowspan="2"{/if} class="text-center py-1">
                {if $question.number > 0}
                <i class="fab fa-quora"></i><span class="question-number">{$question.number}</span>
                {else}
                Intro
                {/if}
                <input type="hidden" name="questions[{$question_id}][number]" class="question-number-input" value="{$question.number}">
                <input type="hidden" name="questions[{$question_id}][id]" value="{$question.id}">
            </td>
            <td class="p-0">
                <div class="input-group">
                    {if $question.number > 0}
                    <div class="input-group-prepend"><div class="input-group-text w110">質 問</div></div>
                    {/if}
                    <input type="text" class="form-control" name="questions[{$question_id}][question]" value="{$question.question|escape|default:''}" maxlength="100">
                </div>
            </td>
            <td{if $question.type != 'message' && ! empty($question.has_sub_question)} rowspan="3"{elseif $question.type != 'text'} rowspan="2"{/if} class="h6 text-center py-1">
                {if $question.number > 0}
                <div class="dropdown mb-2">
                    <div id="select-type-{$question.number}" class="select-type" data-toggle="dropdown">
                    {if $question.type == 'level'} <span class="badge aqua-gradient py-1">&nbsp 評 価 &nbsp</span>
                    {elseif $question.type == 'select_one'} <span class="badge purple-gradient py-1">選択(一つ)</span>
                    {elseif $question.type == 'select_multi'} <span class="badge peach-gradient py-1">選択(複数)</span>
                    {elseif $question.type == 'text'} <span class="badge blue-gradient py-1">テキスト</span>
                    {elseif $question.type == 'message'} <span class="badge blue-gradient py-1">メッセージ</span>
                    {/if}
                    </div>
                    <div class="dropdown-menu dropdown-warning select-type-menu px-1">
                        <a class="dropdown-item py-1" href="#" data-type="level">
                            <span class="badge aqua-gradient py-1">&nbsp 評 価 &nbsp</span>
                        </a>
                        <a class="dropdown-item py-1" href="#" data-type="select_one">
                            <span class="badge purple-gradient py-1">選択(一つ)</span>
                        </a>
                        <a class="dropdown-item py-1" href="#" data-type="select_multi">
                            <span class="badge peach-gradient py-1">選択(複数)</span>
                        </a>
                        <a class="dropdown-item py-1" href="#" data-type="text">
                            <span class="badge blue-gradient py-1">テキスト</span>
                        </a>
                        <a class="dropdown-item py-1" href="#" data-type="message">
                            <span class="badge blue-gradient py-1">メッセージ</span>
                        </a>
                    </div>
                    <input type="hidden" class="select-type-input" name="questions[{$question_id}][type]" value="{$question.type}">
                </div>
                <div class="custom-control custom-radio nps-flag-radio"{if $question.type != 'level'} style="display:none;"{/if}>
                    <input type="radio" class="custom-control-input nps-flag" id="nps-flag-{$question.number}"
                           name="nps_flag" value="{$question_id}"{if ! empty($question.nps_flag)} checked{/if}>
                    <label class="custom-control-label" for="nps-flag-{$question.number}">NPS</label>
                </div>
                <div class="custom-control custom-checkbox nps-correlation-checkbox"{if $question.type != 'level'} style="display:none;"{/if}>
                    <input type="checkbox" class="custom-control-input nps-correlation" id="nps-correlation-{$question.number}"
                           name="questions[{$question_id}][nps_correlation_flag]" value="1"{if ! empty($question.nps_correlation_flag)} checked{/if}>
                    <label class="custom-control-label" for="nps-correlation-{$question.number}">相関</label>
                </div>
                {else}
                <span class="badge blue-gradient py-1">メッセージ</span>
                <input type="hidden" name="questions[{$question_id}][type]" value="{$question.type}">
                {/if}

            </td>
            <td{if  $question.type != 'message' && ! empty($question.has_sub_question)} rowspan="3"{elseif $question.type != 'text'} rowspan="2"{/if} class="h5 text-center py-1">
                {if $question.number > 0}
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input has-sub-question" id="has-sub-question-{$question.number}"
                           name="questions[{$question_id}][has_sub_question]" value="1"{if $question.has_sub_question} checked{/if}>
                    <label class="custom-control-label" for="has-sub-question-{$question.number}"> </label>
                </div>
                {/if}
            </td>
            <td{if $question.type != 'message' && ! empty($question.has_sub_question)} rowspan="3"{elseif  $question.type != 'text'} rowspan="2"{/if} class="text-center py-1 border-right-0">
                {if $question.number > 0}
                <a class="priority-down-btn" href="#" title="Down"{if $question@last} style="display:none;"{/if}>
                    <i class="fas fa-long-arrow-alt-down fa-lg text-info"></i>
                </a>
                {/if}
            </td>
            <td{if $question.type != 'message' && ! empty($question.has_sub_question)} rowspan="3"{elseif  $question.type != 'text'} rowspan="2"{/if} class="text-center py-1 border-left-0">
                {if $question.number > 0}
                <a class="priority-up-btn" href="#" title="Up"{if $question.number == 1} style="display:none;"{/if}>
                    <i class="fas fa-long-arrow-alt-up fa-lg text-danger"></i>
                </a>
                {/if}
            </td>
            <td {if  $question.type != 'message' && ! empty($question.has_sub_question)} rowspan="3"{elseif $question.type != 'text'} rowspan="2"{/if} class="text-center">
                {if $question.number > 0}
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input remove-question" id="remove-{$question.number}"
                           name="remove[{$question_id}]" value="1">
                    <label class="custom-control-label" for="remove-{$question.number}"> </label>
                </div>
                {/if}
            </td>
        </tr>
        <tr class="middle white level-tr"{if $question.type != 'level'} style="display:none;"{/if}>
            <td class="p-0">
                <div class="d-flex align-items-center">
                    <div class="input-group mr-2 w300 item-name"{if empty($question.nps_correlation_flag)} style="display:none;"{/if}>
                        <div class="input-group-prepend">
                            <div class="input-group-text px-1 w60">項目名</div>
                        </div>
                        <input type="text" class="form-control" name="questions[{$question_id}][item]" value="{$question.item|escape|default:''}" maxlength="20">
                    </div>
                    <div class="input-group w300">
                        <div class="input-group-prepend">
                            <div class="input-group-text px-1 w110">最小値ラベル</div>
                        </div>
                        <input type="text" class="form-control text-center"
                               name="questions[{$question_id}][min_label]" value="{$question.min_label|escape|default:''}" maxlength="20">
                    </div>
                    <div class="input-group w140 min-level"{if ! empty($question.nps_correlation_flag)} style="display:none;"{/if}>
                        <div class="input-group-prepend">
                            <div class="input-group-text px-2">最小値</div>
                        </div>
                        <input type="number" class="form-control text-center" name="questions[{$question_id}][min_level]" value="{$question.min_level|default:'0'}" min="0" max="10" required>
                    </div>
                    <div class="text-center w60">
                        ～
                    </div>
                    <div class="input-group w300">
                        <div class="input-group-prepend">
                            <div class="input-group-text px-2">最大値ラベル</div>
                        </div>
                        <input type="text" class="form-control text-center" name="questions[{$question_id}][max_label]" value="{$question.max_label|escape|default:''}" maxlength="20">
                    </div>
                    <div class="input-group w140 max-level"{if ! empty($question.nps_correlation_flag)} style="display:none;"{/if}>
                        <div class="input-group-prepend">
                            <div class="input-group-text px-2">最大値</div>
                        </div>
                        <input type="number" class="form-control text-center" name="questions[{$question_id}][max_level]" value="{$question.max_level|default:'5'}" min="0" max="10" required>
                    </div>
                </div>
            </td>
        </tr>
        <tr class="middle white selection-tr"{if $question.type != 'select_one' && $question.type != 'select_multi'} style="display:none;"{/if}>
            <td class="p-0">
                <ul class="list-group mb-0">
                    {foreach $question.selections as $i => $selection}
                    <li class="list-group-item d-flex list-selection pl-0">
                        <div class="input-group input-group-sm mr-1 w260">
                            <div class="input-group-prepend"><div class="input-group-text">ラベル</div></div>
                            <input type="text" class="form-control" name="questions[{$question_id}][selections][{$i}][label]"
                                   value="{$selection.label|escape}" maxlength="20">
                        </div>
                        <div class="input-group input-group-sm mx-1 w260">
                            <div class="input-group-prepend"><div class="input-group-text">値</div></div>
                            <input type="text" class="form-control" name="questions[{$question_id}][selections][{$i}][selection]"
                                   value="{$selection.selection|escape}" maxlength="20">
                        </div>
                        <div class="custom-control custom-{if $question.type == 'select_one'}radio{elseif $question.type == 'select_multi'}checkbox{/if} mx-1 pt-1">
                            <input type="{if $question.type == 'select_one'}radio{elseif $question.type == 'select_multi'}checkbox{/if}" id="selection-default-flag-{$i}-{$question.number}"
                                   class="custom-control-input" name="default_flag[{$question_id}]"
                                   value="{$i}"{if $selection.default_flag} checked{/if}>
                            <label class="custom-control-label small pt-1" for="selection-default-flag-{$i}-{$question.number}">
                                デフォルト
                            </label>
                        </div>
                        {if $question.type == 'select_one' OR $question.type == 'select_multi'} 
                        <div class="ml-2">
                            <input type="text" class="colorpicker" name="questions[{$question_id}][selections][{$i}][color]" value="{$selection.color|escape|default:'{if isset($default_colors.selections[$i])}{$default_colors.selections[$i]}{/if}'}">
                        </div>
                        {/if}
                        {if $i > 0}
                        <button type="button" class="btn btn-sm btn-outline-danger ml-auto m-0 waves-effect remove-selection-btn">
                            <i class="fas fa-times"></i>
                        </button>
                        {/if}
                    </li>
                    {foreachelse}
                    <li class="list-group-item d-flex list-selection pl-0">
                        <div class="input-group input-group-sm mr-1 w260">
                            <div class="input-group-prepend"><div class="input-group-text">ラベル</div></div>
                            <input type="text" class="form-control" name="questions[{$question_id}][selections][0][label]" value="" maxlength="20">
                        </div>
                        <div class="input-group input-group-sm mx-1 w260">
                            <div class="input-group-prepend"><div class="input-group-text">値</div></div>
                            <input type="text" class="form-control" name="questions[{$question_id}][selections][0][selection]" value="" maxlength="20">
                        </div>
                        <div class="custom-control mx-1 custom-radio pt-1">
                            <input type="radio" id="selection-default-flag-0-{$question.number}"
                                   class="custom-control-input" name="default_flag[{$question_id}]"
                                   value="0"{if $selection.default_flag} checked{/if}>
                            <label class="custom-control-label small pt-1" for="selection-default-flag-0-{$question.number}">
                                デフォルト
                            </label>
                        </div>
                        <div class="ml-2">
                            <input type="text" class="colorpicker" name="questions[{$question_id}][selections][0][color]" value="{$default_colors.selections[0]}">
                        </div>
                    </li>
                    {/foreach}
                    <li class="list-group-item px-2">
                        <button type="button" class="btn btn-sm btn-warning m-0 pb-1 w140 append-select-one-btn"
                                data-question-id="{$question_id}"{if count($question.selections) > 9 OR $question.type == 'select_multi'} style="display:none;"{/if}>
                            <i class="far fa-plus-square fa-lg"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-warning m-0 pb-1 w140 append-select-multi-btn"
                                data-question-id="{$question_id}"{if count($question.selections) > 9 OR $question.type == 'select_one'} style="display:none;"{/if}>
                            <i class="far fa-plus-square fa-lg"></i>
                        </button>
                    </li>
                </ul>
            </td>
        </tr>
        <tr class="middle white text-tr"{if $question.type != 'text'} style="display:none;"{/if}>
            <td class="p-0"></td>
        </tr>
        <tr class="middle white message-tr"{if $question.type != 'message'} style="display:none;"{/if}>
            <td class="p-0">
                <textarea class="form-control" name="questions[{$question_id}][message]" maxlength="100">{$question.message|escape|default:''}</textarea>
            </td>
        </tr>
        <tr class="middle white sub-question-label-tr"{if $question.type == 'message' OR empty($question.has_sub_question)} style="display:none;"{/if}>
            <td class="p-0">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text px-1 w110">フリー入力ラベル</div>
                    </div>
                    <input type="text" class="form-control" name="questions[{$question_id}][sub_question]" value="{$question.sub_question|escape|default:''}" maxlength="100">
                </div>
            </td>
        </tr>

        <tr class="space-tr"><td colspan="7"></td></tr>
        </tbody>
        {foreachelse}
        <tbody>
        <tr class="middle white question-tr">
            <td rowspan="2" class="text-center py-1">
                Intro
            </td>
            <td class="p-0">
                <div class="input-group">
                    <input type="text" class="form-control" name="questions[intro][question]"
                           value="{$questionnaire.questions.intro.question|escape|default:''}" maxlength="100">
                </div>
            </td>
            <td rowspan="2" class="text-center py-1">
                <span class="badge blue-gradient py-1">メッセージ</span>
            </td>
            <td rowspan="2" colspan="3" class="text-center py-1">
                <input type="hidden" name="questions[intro][type]" value="message">
            </td>
            <td rowspan="2" class="text-center">
            </td>
        </tr>
        <tr class="middle white message-tr">
            <td class="p-0">
                <textarea class="form-control" name="questions[intro][message]" maxlength="100">{$questionnaire.questions.intro.message|escape|default:''}</textarea>
            </td>
        </tr>
        <tr class="space-tr"><td colspan="7"></td></tr>
        </tbody>
        {/foreach}
        <tfoot>
            <tr class="middle">
                <td colspan="7" class="text-right">
                    <button type="button" id="append-question-btn" class="btn btn-md btn-outline-warning w180">
                        <i class="far fa-plus-square fa-lg"></i>
                        質問追加
                    </button>
                </td>
            </tr>
        </tfoot>

        </table>
        <div class="text-center">
            <button type="submit" class="btn btn-md btn-warning w180">
                <i class="far fa-check-circle"></i> &nbsp;
                保 存
            </button>
        </div>
        </form>

    </div>
</div>

<table id="dummy-questions-table" style="display:none;">
<tbody class="">
<tr class="middle white question-tr">
    <td rowspan="2" class="text-center py-1">
        <i class="fab fa-quora"></i><span class="question-number"></span>
        <input type="hidden" class="question-number-input" name="questions[new][number]" value="0">
        <input type="hidden" name="questions[new][id]" value="0">
    </td>
    <td class="p-0">
        <div class="input-group">
            <div class="input-group-prepend"><div class="input-group-text w110">質 問</div></div>
            <input type="text" class="form-control" name="questions[new][question]" value="" maxlength="100">
        </div>
    </td>
    <td rowspan="2" class="h6 text-center py-1">
        <div class="dropdown mb-2">
            <div id="select-type-{*$question_id*}" class="select-type" data-toggle="dropdown">
                <span class="badge aqua-gradient py-1">&nbsp 評 価 &nbsp</span>
            </div>
            <div class="dropdown-menu dropdown-warning select-type-menu px-1">
                <a class="dropdown-item py-1" href="#" data-type="level">
                    <span class="badge aqua-gradient py-1">&nbsp 評 価 &nbsp</span>
                </a>
                <a class="dropdown-item py-1" href="#" data-type="select_one">
                    <span class="badge purple-gradient py-1">選択(一つ)</span>
                </a>
                <a class="dropdown-item py-1" href="#" data-type="select_multi">
                    <span class="badge peach-gradient py-1">選択(複数)</span>
                </a>
                <a class="dropdown-item py-1" href="#" data-type="text">
                    <span class="badge blue-gradient py-1">テキスト</span>
                </a>
                <a class="dropdown-item py-1" href="#" data-type="message">
                    <span class="badge blue-gradient py-1">メッセージ</span>
                </a>
            </div>
            <input type="hidden" class="select-type-input" name="questions[new][type]" value="level">
        </div>
        <div class="custom-control custom-radio nps-flag-radio">
            <input type="radio" class="custom-control-input nps-flag" id="nps-flag-{*$question.number*}" name="nps_flag" value="">
            <label class="custom-control-label" for="nps-flag-{*$question.number*}">NPS</label>
        </div>
        <div class="custom-control custom-checkbox nps-correlation-checkbox">
            <input type="checkbox" class="custom-control-input nps-correlation" id="nps-correlation-{$question.number}"
                   name="questions[new][nps_correlation_flag]" value="1">
            <label class="custom-control-label" for="nps-correlation-{*$question.number*}">相関</label>
        </div>
    </td>
    <td rowspan="2" class="h5 text-center py-1">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input has-sub-question" id="has-sub-question-{* $question_id *}"
                   name="questions[new][has_sub_question]" value="1">
            <label class="custom-control-label" for="has-sub-question-{* $question_id *}"> </label>
        </div>
    </td>
    <td rowspan="2" class="text-center py-1 border-right-0">
        <a class="priority-down-btn" href="#" title="Down" style="display:none;">
            <i class="fas fa-long-arrow-alt-down fa-lg text-info"></i>
        </a>
    </td>
    <td rowspan="2" class="text-center py-1 border-left-0">
        <a class="priority-up-btn" href="#" title="Up">
            <i class="fas fa-long-arrow-alt-up fa-lg text-danger"></i>
        </a>
    </td>
    <td rowspan="2" class="text-center">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input remove-question" id="remove-{* $question_id *}"
                   name="questions[new][remove_question]" value="1">
            <label class="custom-control-label" for="remove-"> </label>
        </div>
    </td>
</tr>
<tr class="middle white level-tr">
    <td class="p-0">
        <div class="d-flex align-items-center">
            <div class="input-group mr-2 w300 item-name" style="display:none;">
                <div class="input-group-prepend">
                    <div class="input-group-text px-1 w60">項目名</div>
                </div>
                <input type="text" class="form-control" name="questions[new][item]" value="" maxlength="20">
            </div>
            <div class="input-group w300">
                <div class="input-group-prepend">
                    <div class="input-group-text px-1 w110">最小値ラベル</div>
                </div>
                <input type="text" class="form-control text-center" name="questions[new][min_label]" value="いいえ" maxlength="20">
            </div>
            <div class="input-group w140 min-level">
                <div class="input-group-prepend">
                    <div class="input-group-text px-2">最小値</div>
                </div>
                <input type="number" class="form-control text-center" name="questions[new][min_level]" value="0" min="0" max="10" required>
            </div>
            <div class="text-center w60">
                ～
            </div>
            <div class="input-group w300">
                <div class="input-group-prepend">
                    <div class="input-group-text px-2">最大値ラベル</div>
                </div>
                <input type="text" class="form-control text-center" name="questions[new][max_label]" value="はい" maxlength="20">
            </div>
            <div class="input-group w140 max-level">
                <div class="input-group-prepend">
                    <div class="input-group-text px-2">最大値</div>
                </div>
                <input type="number" class="form-control text-center" name="questions[new][max_level]" value="5" min="0" max="10" required>
            </div>
        </div>
    </td>
</tr>
<tr class="middle white selection-tr" style="display:none;">
    <td class="p-0">
        <ul class="list-group mb-0">
            <li class="list-group-item d-flex align-items-center list-selection pl-0">
                <div class="input-group input-group-sm mr-1 w260">
                    <div class="input-group-prepend"><div class="input-group-text">ラベル</div></div>
                    <input type="text" class="form-control" name="questions[new][selections][index][label]" value="" maxlength="20">
                </div>
                <div class="input-group input-group-sm mx-1 w260">
                    <div class="input-group-prepend"><div class="input-group-text">値</div></div>
                    <input type="text" class="form-control" name="questions[new][selections][index][selection]" value="" maxlength="20">
                </div>
                <div class="custom-control default-flag-radio mx-1 pt-1{* custom-radio custom-checkbox *}">
                    <input type="radio{* checkbox *}" id="selection-default-flag-0-{* $question.number *}"
                           class="custom-control-input" name="default_flag[NEW]"
                           value="0" checked>
                    <label class="custom-control-label small pt-1" for="selection-default-flag-0-{* $question.number *}">
                        デフォルト
                    </label>
                </div>
                <div class="ml-2">
                    <input type="text" class="_colorpicker" name="questions[new][selections][index][color]" value="{$default_colors.selections[0]}">
                </div>
            </li>
            <li class="list-group-item px-2">
                <button type="button" class="btn btn-sm btn-warning m-0 pb-1 w140 append-select-one-btn">
                    <i class="far fa-plus-square fa-lg"></i>
                </button>
                <button type="button" class="btn btn-sm btn-warning m-0 pb-1 w140 append-select-multi-btn" style="display:none">
                    <i class="far fa-plus-square fa-lg"></i>
                </button>
            </li>
        </ul>
    </td>
</tr>
<tr class="middle white text-tr" style="display:none;">
    <td class="p-0"></td>
</tr>
<tr class="middle white message-tr" style="display:none;">
    <td class="p-0">
        <textarea class="form-control" name="questions[new][sub_question]" maxlength="100"></textarea>
    </td>
</tr>
<tr class="middle white sub-question-label-tr" style="display:none;">
    <td class="p-0">
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text px-1 w110">フリー入力ラベル</div>
            </div>
            <input type="text" class="form-control" name="questions[new][sub_question]" value="" maxlength="100">
        </div>
    </td>
</tr>
<tr class="space-tr"><td colspan="7"></td></tr>
</tbody>
</table>

<div style="display:none;">
<ul id="dummy-selection-one">
    <li class="list-group-item d-flex list-selection pl-0">
        <div class="input-group input-group-sm mr-1 w260">
            <div class="input-group-prepend"><div class="input-group-text">ラベル</div></div>
            <input type="text" class="form-control" name="questions[new][selections][index][label]" value="" maxlength="20">
        </div>
        <div class="input-group input-group-sm mx-1 w260">
            <div class="input-group-prepend"><div class="input-group-text">値</div></div>
            <input type="text" class="form-control" name="questions[new][selections][index][selection]" value="" maxlength="20">
        </div>
        <div class="custom-control custom-radio mx-1 default-flag-radio pt-1">
            <input type="radio" id="{*selection-default-flag-0-$question.number*}"
                   class="custom-control-input" name="default_flag[new]" value="0">
            <label class="custom-control-label small pt-1" for="{*selection-default-flag-0-$question.number*}">
                デフォルト
            </label>
        </div>
        <div class="ml-2">
            <input type="text" class="_colorpicker" name="questions[new][selections][index][color]" value="">
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger ml-auto m-0 waves-effect remove-selection-btn">
            <i class="fas fa-times"></i>
        </button>
    </li>
</ul>

<ul id="dummy-selection-multi">
    <li class="list-group-item d-flex list-selection pl-0">
        <div class="input-group input-group-sm mr-1 w260">
            <div class="input-group-prepend"><div class="input-group-text">ラベル</div></div>
            <input type="text" class="form-control" name="questions[new][selections][index][label]" value="" maxlength="20">
        </div>
        <div class="input-group input-group-sm mx-1 w260">
            <div class="input-group-prepend"><div class="input-group-text">値</div></div>
            <input type="text" class="form-control" name="questions[new][selections][index][selection]" value="" maxlength="20">
        </div>
        <div class="custom-control custom-checkbox mx-1 pt-1">
            <input type="checkbox" id="" class="custom-control-input" name="default_flag[new]" value="0">
            <label class="custom-control-label small pt-1" for="">
                デフォルト
            </label>
        </div>
        <div class="ml-2">
            <input type="text" class="_colorpicker" name="questions[new][selections][index][color]" value="">
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger ml-auto m-0 waves-effect remove-selection-btn">
            <i class="fas fa-times"></i>
        </button>
    </li>
</ul>
</div>

