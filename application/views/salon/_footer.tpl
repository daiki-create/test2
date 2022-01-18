<div class="text-center py-1">
    {if $class != 'login' && $class != 'trial' && $class != 'tos'}
    <img src="/img/salon/salon-footer.png" alt="フッター画像">
    {/if}
    <div class="d-flex align-items-center">
        {if $class != 'login' && $class != 'trial' && $class != 'tos'}
        <div id="agreement-link" class="pl-2 mr-3"><a href="/salon/tos/">ご利用規約</a></div>
        {/if}
        <div class="text-center flex-fill fs-12"> &copy; 2019 montecampo co.,ltd </div>
    </div>
</div>
