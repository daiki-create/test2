<div class="modal fade" id="change-password-modal" tabindex="-1" role="dialog" aria-labelledby="label1" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="label1">パスワードを変更</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="update-password-form" method="POST" action="/{$module}/{$class}/update_pw/">
                <div class="form-group">
                    <label>新しいパスワード</label>
                    <input type="password" class="form-control" name="loginpw" value="" maxlength="20">
                    <label>新しいパスワード(確認)</label>
                    <input type="password" class="form-control" name="confirm_loginpw" value="" maxlength="20">
                </div>
            </form>
        </div>
        <div class="modal-footer">
          <button id="submit-change-password-btn" type="button" class="btn btn-primary mr-2 w120" disabled>O K</button>
          <button type="button" class="btn btn-outline-light ml-2" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>

