
var _dev_subscription_exec = false;

$(function() {

    // デバック用：定期課金バッチ手動起動
    $("#subscription-dev-bt").on('click', function(){
        $("#subscription-dev").slideToggle();
    });
    $("#subscription-dev-form").on('submit', function(){
        if( _dev_subscription_exec ){
            alert( '只今実行中・・・');
            return false;
        }
        var ym = $( 'input[name=ym]', this ).val();
        if( !ym || !ym.match( /^\d{4}-(0?[1-9]|1[0-2])$/ ) ){
            alert( 'yyyy-mm の形式で入力して下さい');
            return false;
        }
        if( !window.confirm( ym + ' の定期課金を実行します。宜しいですか？' ) ){
            return false;
        }
        _dev_subscription_exec = true;
        $("input[type=submit]", this ).attr('disabled',true);
        return true;
    });

});
