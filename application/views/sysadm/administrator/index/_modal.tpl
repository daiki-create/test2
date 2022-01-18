
<div id="administrator-modal" class="modal fade" data-backdrop="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-body pb-1">

                <form id="administrator-form" class="p-4 mb-0" method="POST" action="/{$module}/{$class}/update/">
                <input type="hidden" name="admin_id" value="">
                <div class="form-group">
                    <label>氏 名</label>
                    <input type="text" class="form-control" name="name" value="" maxlength="20" reauired>
                </div>
                <div class="form-group">
                    <label>ログインID</label>
                    <input type="email" class="form-control" name="loginid" value="" maxlength="50" reauired>
                </div>
                <div class="form-group mb-0">
                    <label>状態</label>
                    <div class="form-check pl-0">
                        <input type="checkbox" name="status" value="1" data-toggle="toggle" data-on="有効" data-off="無効">
                    </div>
                </div>
                </form>

            </div>
            <div class="modal-body pt-1">
                <div class="row">
                    <div class="col-4 text-center">
                        <button type="button" id="reset-password-btn" class="btn btn-md btn-outline-primary px-2">
                            <i class="fas fa-retweet"></i>
                            ﾊﾟｽﾜｰﾄﾞﾘｾｯﾄ 
                        </button>
                    </div>
                    <div class="col-4 text-center">
                        <button type="submit" class="btn btn-md btn-warning w-100" form="administrator-form" disabled>
                            <i class="fas fa-sync-alt"></i>
                            更 新
                        </button>
                    </div>
                    <div class="col-4 text-right">
                        <button type="button" id="delete-administrator-btn" class="btn btn-md btn-outline-danger">
                            <i class="far fa-times-circle"></i>
                            削 除
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

