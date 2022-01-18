
<div class="card">
    <div class="card-header amber darken-4">
        システム管理者一覧
    </div>
    <div class="card-body">

        <table class="table table-hover table-fixed border">
        <thead class="thead-light">
            <tr>
                <th class="w160">
                    氏名
                </th>
                <th>
                    ログインID
                </th>
                <th class="w100 text-center">
                    状態
                </th>
            </tr>
        </thead>
        <tbody>
        {foreach $administrators as $admin}

        <tr class="clickable" data-admin-id="{$admin.id}" data-name="{$admin.name|escape}" data-loginid="{$admin.loginid|escape}" data-status="{$admin.status}">
            <td>
                <a data-toggle="modal" data-target="#administrator-modal">
                    {$admin.name|escape}
                </a>
            </td>
            <td>
                <a data-toggle="modal" data-target="#administrator-modal">
                    {$admin.loginid|escape}
                </a>
            </td>
            <td class="text-center h5">
                {if $admin.status == '1'} <span class="badge badge-primary">有効</span>
                {elseif $admin.status == '-1'} <span class="badge badge-default">仮登録</span>
                {else}<span class="badge badge-light">無効</span>
                {/if}
            </td>
        </tr>
        {/foreach}

        </tbody>
        </table>

    </div>
</div>

<form id="delete-administrator-form" method="POST" action="/{$module}/{$class}/delete/">
<input type="hidden" name="admin_id" value="">
<input type="hidden" name="csrf" value="{$csrf}">
</form>
<form id="reset-password-form" method="POST" action="/{$module}/{$class}/reset_pw/">
<input type="hidden" name="admin_id" value="">
<input type="hidden" name="csrf" value="{$csrf}">
</form>

