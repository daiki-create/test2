<div class="card">
    <div class="card-header amber darken-4">
        マイページ
    </div>
    <div class="card-body">

        {include file="../../common/_alert.tpl"}

        <table class="table table-fixed border-bottom w500 mx-auto">
        <thead></thead>
        <tbody>
            <tr class="">
                <th class="text-right w120"> 氏名： </th>
                <td class="text-truncate">{$administrator.name|escape}</td>
            </tr>
            <tr class="">
                <th class="text-right">ログインID：</th>
                <td class="text-truncate">{$administrator.loginid|escape}</td>
            </tr>
        </tbody>
        </table>
        <div class="form-group text-center w500 mx-auto">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#change-password-modal">
                パスワード変更
            </button>
        </div>

    </div>
</div>
