
<div class="card">
    <form id="salon-form" method="POST" action="/{$module}/{$class}/{if empty($salon_id)}create{else}update/{$salon_id}{/if}/">
    <div class="card-header amber darken-4">
        サロン {if empty($salon_id)}新規登録{else}情報編集{/if}
    </div>
    <div class="card-body">

        {include file="../../common/_alert.tpl"}

        <table class="table table-fixed border">
        <thead></thead>
        <tbody class="">
            <tr>
                <td>
                    <div class="form-group mb-0">
                        <label class="required">サロン名</label>
                        <input type="text" class="form-control{if isset($validation_errors['name'])} is-invalid{/if}" name="name" value="{$salon.name|escape|default:''}" maxlength="50" required>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="d-flex">
                    <div class="form-group mb-0 mr-3">
                        <label>電話番号</label>
                        <div class="d-flex align-items-center mb-1">
                            <input type="tel" class="form-control text-center mr-1 w100{if isset($validation_errors['phone'])} is-invalid{/if}" name="phone[0]" value="{$salon.phone[0]|default:''}" maxlength="4">
                            <span>-</span>
                            <input type="tel" class="form-control text-center mx-1 w100{if isset($validation_errors['phone'])} is-invalid{/if}" name="phone[1]" value="{$salon.phone[1]|default:''}" maxlength="4">
                            <span>-</span>
                            <input type="tel" class="form-control text-center ml-1 w100{if isset($validation_errors['phone'])} is-invalid{/if}" name="phone[2]" value="{$salon.phone[2]|default:''}" maxlength="4">
                        </div>
                        {*<input type="tel" class="form-control form-control-sm text-center ml-1 w100" name="postcode2" value="" maxlength="4">*}
                    </div>
                    <div class="form-group mb-0">
                        <label>FAX番号</label>
                        <div class="d-flex align-items-center mb-1">
                            <input type="tel" class="form-control text-center mr-1 w100{if isset($validation_errors['fax'])} is-invalid{/if}" name="fax[0]" value="{$salon.fax[0]|default:''}" maxlength="4">
                            <span>-</span>
                            <input type="tel" class="form-control text-center mx-1 w100{if isset($validation_errors['fax'])} is-invalid{/if}" name="fax[1]" value="{$salon.fax[1]|default:''}" maxlength="4">
                            <span>-</span>
                            <input type="tel" class="form-control text-center ml-1 w100{if isset($validation_errors['fax'])} is-invalid{/if}" name="fax[2]" value="{$salon.fax[2]|default:''}" maxlength="4">
                        </div>
                        {*<input type="tel" class="form-control text-center w140{if isset($validation_errors['fax'])} is-invalid{/if}" name="fax" value="{$salon.fax|escape|default:''}" maxlength="13">*}
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div id="address-group">
                        <div class="form-group mb-1">
                            <label class="text-primary">サロン住所</label>
                            <div class="input-group input-group-sm ">
                                <div class="input-group-prepend"><div class="input-group-text">&#12306;</div></div>
                                <input type="tel" class="form-control text-center border-right-0 px-1 w40{if isset($validation_errors['postcode1'])} is-invalid{/if}" name="postcode1"
                                       value="{$salon.postcode1|escape|default:''}" maxlength="3">
                                <div class="form-control text-center px-0 border-right-0 border-left-0 w15">-</div>
                                <input type="tel" class="form-control text-center border-left-0 px-1 w50{if isset($validation_errors['postcode2'])} is-invalid{/if}" name="postcode2"
                                       value="{$salon.postcode2|escape|default:''}" maxlength="4">
                                <div class="input-group-append">
                                    <button type="button" id="search-address-btn" class="btn btn-md btn-info m-0 px-2">
                                        <i class="fas fa-search-location"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-1">
                            <div class="input-group w190">
                                <div class="input-group-prepend"><div class="input-group-text px-2"><span class="small">都道府県</small></div></div>
                                <select class="form-control prefecture" name="prefecture">
                                    {strip}
                                    <option></option>
                                    {foreach $prefectures as $code => $name}

                                    <option value="{$code}"{if $code == $salon.prefecture} selected{/if}>{$name}</option>
                                    {/foreach}
                                    {/strip}
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control address" name="address" value="{$salon.address|escape|default:''}" maxlength="50">
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group mb-0">
                        <label>備考</label>
                        <textarea class="form-control form-control-sm{if isset($validation_errors['note'])} is-invalid{/if}" name="note" value="" maxlength="250">{$salon.note|escape|default:''}</textarea>
                    </div>
                </td>
            </tr>
        </tbody>
        </table>

    </div>
    <div class="card-body">
        <table class="table table-fixed table-bordered">
        <thead>
            <tr class="amber darken-4 text-white">
                <th class="text-center px-2 w80">
                    利用可否
                </th>
                <th class="">
                    アンケート名
                </th>
                <th class="">
                    状態
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach $questionnaires as $questionnaire}
            <tr class="middle">
                <td class="text-center">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="questionnaire-{$questionnaire.id}" name="questionnaire_id[]" value="{$questionnaire.id}"
                               class="custom-control-input"{if $questionnaire.status == 0} disabled{/if} {if isset($salon.questionnaires[$questionnaire.id])} checked{/if}>
                        {if $questionnaire.status == 0 && isset($salon.questionnaires[$questionnaire.id])}
                        <input type="hidden" id="questionnaire-{$questionnaire.id}" name="questionnaire_id[]" value="{$questionnaire.id}">
                        {/if}
                        <label class="custom-control-label" for="questionnaire-{$questionnaire.id}"> </label>
                    </div>
                </td>
                <td class="text-truncate">
                    {$questionnaire.title|escape}
                </td>
                <td class="text-center h5">
                    {if $questionnaire.status == '1'} <span class="badge badge-primary">稼働</span>
                    {elseif $questionnaire.status == '0'} <span class="badge badge-light">停止</span>
                    {/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
        </table>
    </div>
    <div class="card-footer text-center">
        <button type="submit" class="btn btn-warning w160" form="salon-form">
            {if empty($salon_id)}
                登 録
            {else}
                更 新
            {/if}
        </button>
    </div>
    </form>
</div>

