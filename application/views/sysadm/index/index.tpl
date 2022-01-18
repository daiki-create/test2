{if $messages}

<div class="alert alert-info text-center p-2">
    {if is_array($messages)}
    {foreach $messages as $message}

    <p class="small mb-0">{$message|escape|default:''|nl2br}</p>
    {/foreach}
    {else}
    <p class="small mb-0">{$messages|escape|default:''|nl2br}</p>
    {/if}
</div>
{/if}

{if $error_messages}
<div class="alert alert-danger text-center p-2">
     {if is_array($error_messages)}
     {foreach $error_messages as $error_message}

     <p class="small mb-0">{$error_message}</p>
     {/foreach}
     {else}
     <p class="small mb-0">{$error_messages}</p>
     {/if}
</div>
{/if}

<div class="card">
<div class="card-body">
<button type="submit" class="btn btn-outline-danger" form="update-youteube-btn">
    <i class="fab fa-youtube fa-2x"></i>
    YouTube動画更新
</button>
</div>
</div>

<form id="update-youteube-btn" method="POST" action="/sysadm/index/create_post"></form>
