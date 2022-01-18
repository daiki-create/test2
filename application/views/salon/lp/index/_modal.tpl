
<div id="update-lp-modal" class="modal fade" data-backdrop="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="row">
                <div class="col-10  offset-1 px-0">
                    <div class="card pt-30">
                        <div class="pb-30 fw-600 text-center">
                            アンケート終了後に、ジャンプさせる任意のWebページを更新します。
                        </div>
                            
                        <div class="">
                            <div class="card mb-3">
                                <div class="card-header text-truncate page-title">
                                    ランディングページ情報編集
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body pb-3">
                                <form id="update-lp-form" method="POST" action="/{$module}/{$class}/update/">
                                    <input type="hidden" name="landing_page_id" value="">
                                    <div class="form-group">
                                        <label>適用期間</label>
                                        <div class="d-flex align-items-center">
                                            <div id="update-since-datepicker" class="input-group date w190" data-target-input="nearest">
                                                <input type="text" id="update-since-date" class="form-control form-control-date {if isset($validation_errors.since_date)} validation-error{/if}" name="since_date" value="" data-target="#update-since-datepicker" data-toggle="datetimepicker">
                                                <div class="input-group-append" data-target="#update-since-datepicker" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                            <div class="mx-2">～</div>
                                            <div id="update-until-datepicker" class="input-group date w190" data-target-input="nearest">
                                                <input type="text" id="update-until-date" class="form-control form-control-date {if isset($validation_errors.until_date)} validation-error{/if}" name="until_date" value="" data-target="#update-until-datepicker" data-toggle="datetimepicker">
                                                <div class="input-group-append" data-target="#update-until-datepicker" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Webサイトアドレス(URL)</label>
                                        <input type="url" class="form-control{if isset($validation_errors.lp_url)} validation-error{/if}" name="lp_url" value="" maxlength="255" required>
                                    </div>
                                </form>
                                </div>
                                <div class="text-center">
                                    <button id="update-lp-btn" type="submit"
                                        class="btn btn-md btn-hairlogy w180 m-0" form="update-lp-form">
                                        更 新
                                    </button>
                                    <button id="delete-landing-page mt-50" type="button"
                                        class="btn btn-md btn-outline-danger px-3 float-right mt-100"
                                        data-toggle="modal" data-target="#modal1">
                                        削除
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>    
            </div> 
            
            <div class="modal fade text-center" id="modal1" tabindex="-1"
                role="dialog" aria-labelledby="label1" aria-hidden="true">
                <div class="modal-dialog pt-100" role="document">
                    <div class="modal-content pt-50 pb-100">
                        <div class="modal-body">
                            <p>
                                削除しますか？
                            </p>
                            <div class="container">
                                <div class="row">
                                    <div class="col-4 offset-2 ws-nw">
                                        <button type="button" class="btn btn-white px-3 w-100" data-dismiss="modal">いいえ</button>
                                    </div>
                                    <div class="col-4 ws-nw">
                                        <form id="delete-lp-form" method="POST" action="/{$module}/{$class}/delete/">
                                            <input type="hidden" name="landing_page_id" value="">
                                            <button type="submit" class="btn btn-hairlogy px-3 w-100">
                                                はい
                                            </button> 
                                        </form>                        
                                    </div>
                                </div>
                            </div>
                        </div>              
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="lp-modal" class="modal fade" data-backdrop="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="row">
                <div class="col-10  offset-1 px-0">
                    <div class="card pt-30 pb-30">        
                        <div class="pb-30 fw-600 text-center">
                            アンケート終了後に、ジャンプさせる任意のWebページを登録します。
                        </div>
                            
                        <div class="">
                            <div class="card mb-3">
                                <div class="card-header text-truncate page-title">
                                    ランディングページ登録
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body pb-3">
                                <form id="lp-form" method="POST" action="/{$module}/{$class}/create/">
                                    <div class="form-group">
                                        <label>適用期間</label>
                                        <div class="d-flex align-items-center">
                                            <div id="since-datepicker" class="input-group date w190" data-target-input="nearest">
                                                <input type="text" id="since-date" class="form-control datetimepicker-input {if isset($validation_errors.since_date)} validation-error{/if}" name="since_date" value="" min="{$smarty.now|date_format:'%Y-%m-%d'}" data-target="#since-datepicker" data-toggle="datetimepicker">
                                                <div class="input-group-append" data-target="#since-datepicker" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                            <div class="mx-2">～</div>
                                            <div id="until-datepicker" class="input-group date w190" data-target-input="nearest">
                                                <input type="text" id="until-date" class="form-control datetimepicker-input {if isset($validation_errors.until_date)} validation-error{/if}" name="until_date" value="" min="{$smarty.now|date_format:'%Y-%m-%d'}"data-target="#until-datepicker" data-toggle="datetimepicker">
                                                <div class="input-group-append" data-target="#until-datepicker" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Webサイトアドレス(URL)</label>
                                        <input type="url" class="form-control{if isset($validation_errors.lp_url)} validation-error{/if}" name="lp_url" value="" maxlength="255" required>
                                    </div>
                                </form>
            
                            </div>
                            <div class="text-center">
                                <button id="create-lp-btn" type="submit" class="btn btn-md btn-hairlogy w180 m-0" form="lp-form">
                                    登 録
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
