
<div class="pagination-block">
{if ! empty($pagination)}
    {if ! empty($pagination.link)}
    <div class="pagination pagination-links">
        {$pagination.link}
    </div>
    {/if}
    <div class="pagination-page-number">
        <span class="text-primary">
            {if $pagination.total > 0}{$pagination.offset+1}～{$pagination.offset+$pagination.count} / {$pagination.total|number_format}件中{else}0 件{/if}
        </span>
    </div>
{/if}
</div>

