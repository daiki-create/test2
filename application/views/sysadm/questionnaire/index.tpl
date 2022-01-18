
<div class="card">
    <div class="card-header amber darken-4">
        アンケート一覧 (MC作成)
    </div>
    <div class="card-body">

        {include file="../../common/_alert.tpl"}

        <table class="table table-fixed border">
        <thead>
        <tr class="thead-light">
            <th class="">
                アンケート名
            </th>
            <th class="w80 text-center">
            </th>
        </tr>
        </thead>
        <tbody>
        {foreach $questionnaires as $questionnaire}

        <tr class="clickable">
            <td class="text-truncate">
                <a href="/{$module}/{$class}/detail/{$questionnaire.id}/">{$questionnaire.title|escape|default:''}</a>
            </td>
            <td class="text-center">
            </td>
        </tr>
        {/foreach}

        </body>
        </table>

    </div>
</div>

