
<div class="card">
    <form id="answer-questionnaire-form">
        <section class="mb-0 py-0">
            <div class="py-5 px-1 px-md-3 px-lg-5 white">
                {if $questionnaire.questions}
                {foreach $questionnaire.questions as $i => $question}
    
                    <div class="card question-card fastera active">
                    <div class="card-body pt-4">
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
                                data-slider-step="1" data-slider-value="{$question.min_level}" style="display:none">
                        </div>
                        {elseif $question.type == 'select_one'}
    
                        <div class="btn-group-toggle answer-choices" data-toggle="buttons">
                            {foreach $question.selections as $choice}
                            <label class="btn m-3{if ! empty($choice.default_flag)} active{/if}">
                                <input type="radio" name="select_one[{$question.id}]" value="{$choice.selection|escape}" autocomplete="off" {if !empty($choice.default_flag)} checked{/if}> {$choice.label|escape}
                            </label>
                            {/foreach}
                        </div>
                        {elseif $question.type=='select_multi' }
    
                        <div class="btn-group-toggle answer-choices" data-toggle="buttons">
                            {foreach $question.selections as $choice}
                            <label class="btn m-3{if ! empty($choice.default_flag)} active{/if}">
                                <input type="checkbox" name="select_multi[{$question.id}][]" value="{$choice.selection|escape}" autocomplete="off" {if !empty($choice.default_flag)} checked{/if}> {$choice.label|escape}
                            </label>
                            {/foreach}
                        </div>
                        {elseif $question.type=='text' }
    
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
    
                <div class="card question-card">
                </div>
                {/if}
    
            </div>
        </section>
    </form>
</div>

