
{if $questionnaire.questions}
<form id="answer-questionnaire-form" method="POST" action="/{$module}/{$class}/complete/">
<input type="hidden" name="code" value="{$code}">
<section class="pt-30">
    <!-- No.の数字を入力してください -->
    <div class="card-header page-title back-black">
        <span class="mypage-name">No.</span>{date('Y/m/d h:i')|escape}
    </div>
    {foreach $questionnaire.questions as $i => $question}
    <div class="card question-card faster active">
        <div class="card-body pt-4 pb-1">
            <h6 class="card-header-title border-orange deep-green-text">
                {if ! empty($question.number)}Ｑ{$question.number}. &nbsp;{/if}
                <span class="color-orange">{$question.question|escape}</span>
            </h6>

            {if $question.type == 'level'}
            <div class="row">
                <div class="col-6 text-left py-3 deep-green-text small">
                    {$question.min_label|escape}
                </div>
                <div class="col-6 text-right py-3 deep-green-text small">
                    <strong>
                    {$question.max_label|escape}
                    </strong>
                </div>
            </div>
            <div class="answer-value deep-green-text px-2 pb-5">
                <input type="text" name="level[{$question.id}]" value=""
                        data-slider-ticks="[{for $level=$question.min_level to $question.max_level}{$level}{if $level < $question.max_level},{/if}{/for}]"
                        data-slider-ticks-labels="[{for $level=$question.min_level to $question.max_level}{$level}{if $level < $question.max_level},{/if}{/for}]"
                        data-slider-handle="square"
                        data-slider-min="{$question.min_level}"
                        data-slider-max="{$question.max_level}"
                        data-slider-step="1"
                        data-slider-value="{$question.min_level}" style="display:none">
            </div>

            {elseif $question.type == 'select_one'}
            <div class="btn-group-toggle answer-choices" data-toggle="buttons">
                {foreach $question.selections as $choice}
                <label class="btn m-3{if ! empty($choice.default_flag)} active{/if}">
                    <input type="radio" name="select_one[{$question.id}]" value="{$choice.selection|escape}" autocomplete="off"{if ! empty($choice.default_flag)} checked{/if}>
                    {$choice.label|escape}
                </label>
                {/foreach}
            </div>

            {elseif $question.type == 'select_multi'}
            <div class="btn-group-toggle answer-choices" data-toggle="buttons">
                {foreach $question.selections as $choice}
                <label class="btn m-3{if ! empty($choice.default_flag)} active{/if}">
                    <input type="checkbox" name="select_multi[{$question.id}][]" value="{$choice.selection|escape}" autocomplete="off"{if ! empty($choice.default_flag)} checked{/if}>
                    {$choice.label|escape}
                </label>
                {/foreach}
            </div>

            {elseif $question.type == 'text'}
            <textarea rows="3" class="form-control" name="answer[{$question.id}]" maxlength="250"></textarea>
            {/if}

            {if ! empty($question.sub_question)}
            <h6 class="sub-question mt-3">
                {$question.sub_question|escape}
            </h6>

            {if $question.type !== 'message'}
            <textarea rows="3" class="form-control" name="sub_answer[{$question.id}]" maxlength="250"></textarea>
            {/if}

            {/if}

        </div>
    </div>

    {/foreach}

    <!-- <div class="card question-card"> -->
        <!-- <div class="card-body text-center px-0 py-5"> -->
            <div class="row mt-100">
                <div class="col-10 offset-1 col-sm-8 offset-sm-2 col-md-6 offset-md-3">
                    <div class="form-group">
                        <div id="spinner" class="rounded p-2 mx-auto text-center" style="display:none;">
                            <div class="spinner-border" role="status"></div>
                        </div>
                        <button type="button" id="complete-btn" class="w-100 px-1" form="answer-questionnaire-form">
                            <i class="fas fa-paper-plane"></i>&nbsp;
                            送 信
                        </button>
                    </div>
                </div>
            </div>
        <!-- </div> -->
    <!-- </div> -->
</section>
</form>
<!-- {else}

<div class="card">
    <div class="card-body text-center pt-4">
        このアンケートは終了しました。
    </div>
</div> -->
{/if}

<br>
<br>
