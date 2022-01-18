<div class="modal fade" id="select-salon-modal" tabindex="-1" role="dialog" aria-labelledby="label1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header amber darken-3">
          <h5 class="modal-title text-white" id="">所属サロン登録</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div id="select-salon-body" class="modal-body">

            <table class="table table-sm table-fixed table-bordered">
            <thead>
            <tr class="thead-light">
                <th class="w240">
                    サロン名
                </th>
                <th class="w140">
                    電話番号
                </th>
                <th>
                    住所
                </th>
                <th class="w60">
                </th>
            </tr>
            </thead>
            <tbody id="salons">
            </tbody>
            <tbody id="dummy-salon" style="display:none;">
            <tr class="middle">
                <td class="text-truncate salon-name">
                </td>
                <td class="salon-phone">
                </td>
                <td class="text-truncate salon-address">
                </td>
                <td class="text-center px-0">
                    <button type="button" class="btn btn-sm text-white amber darken-4 px-3 m-0 select-salon-btn" data-salon-id="">
                        選択
                    </button>
                </td>
            </tr>
            </tbody>
            </table>
            <div id="salons-pagination"></div>

        </div>
      </div>
    </div>
</div>

