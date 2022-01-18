
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

        <p class="small mb-0">{$error_message|escape|default:''|nl2br}</p>
        {/foreach}
        {else}
        <p class="small mb-0">{$error_messages|escape|default:''|nl2br}</p>
        {/if}

    </div>

{elseif $validation_errors}

    <div class="alert alert-warning text-center p-2">
        {foreach $validation_errors as $error_message}

        <p class="small mb-0">{$error_message|escape|default:''|nl2br}</p>
        {/foreach}

    </div>

{/if}

