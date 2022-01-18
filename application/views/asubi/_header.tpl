
<div class="header-img">
    <img src="/img/asubi/asubi-lp-design/material-img-parts/PNG/1x/85.png" alt="">
</div>
<div class="header-nav">
    {if !isset($login)}
    <nav id="nav" >
        <ul>
            <li class="header-nav-list"><a href="/asubi/">TOP</a></li>
            <li class="header-nav-list"><a href="/asubi/info/guidance">お申し込み</a></li>
            <li class="header-nav-list"><a href="/asubi/inquiry/form/">お問合わせ</a></li>
            <li class="header-nav-list"><a href="/asubi/login/">ログイン</a></li>
        </ul>
    </nav>
    {else}
    <nav id="my-page-nav" >
        <ul>
            <li class="header-nav-list2"><a href="/asubi/">TOP</a></li>
            <li class="header-nav-list2"><a href="/asubi/inquiry/form/">お問合わせ</a></li>
            <li class="header-nav-list2" id="icon-block">
                <img src="https://graph.facebook.com/{$login.sns_id}/picture" alt="">
                <span class="name">
                    {$login.name|escape} <span>▼</span>
                </span>
            </li>
            <div class="my-menu" id="my-menu">
                <ul>
                    <li><a href="/asubi/mypage/">マイページ</a></li>
                    {if $login.online_salon_status == 'new' }
                        <li><a href="/asubi/creditcard">支払い情報登録</a></li>
                    {elseif $login.online_salon_status == 'left' }
                        <li><a href="/asubi/creditcard/change">支払い情報再登録</a></li>
                    {elseif $login.online_salon_status == 'active' || $login.online_salon_status == 'inactive'}
                        <li><a href="/asubi/creditcard/change">支払い情報変更</a></li>
                    {/if}
                    <li><a href="/asubi/login/logout">ログアウト</a></li>
                    {if $login.online_salon_status == 'active' || $login.online_salon_status == 'inactive' }
                        <li><a href="/asubi/leaving/confirm/">退会</a></li>
                    {/if}
                </ul>
            </div>
        </ul>
    </nav>
    {/if}
</div>
<div class="hamburger-menu">
    <div id="menu-btn" class="menu-btn"><span></span></div>
    <div class="menu-content" id="menu-content">
        {if !isset($login)}
        <ul id="menu">
            <li>
                <a href="/asubi/">TOP</a>
            </li>
            <li>
                <a href="/asubi/info/guidance/">お申し込み</a>
            </li>
            <li>
                <a href="/asubi/inquiry/form/">お問合わせ</a>
            </li>
            <li>
                <a href="/asubi/login/">ログイン</a>
            </li>
        </ul>
        {else}
        <ul id="my-page-menu">
            <li>
                <a href="/asubi/">TOP</a>
            </li>
            <li>
                <a href="/asubi/inquiry/form/">お問合わせ</a>
            </li>
            <li id="sp-icon-block">
                <img src="https://graph.facebook.com/{$login.sns_id}/picture" alt="">
                <span class="name">
                    {$login.name|escape} ▾
                </span>
            </li>
            <div class="sp-my-menu" id="sp-my-menu">
                <li>
                    <a href="/asubi/mypage/">マイページ</a>
                </li>
                {if $login.online_salon_status == 'new'}
                    <li>
                        <a href="/asubi/creditcard">支払い情報登録</a>
                    </li>
                {elseif $login.online_salon_status == 'left'}
                    <li>
                        <a href="/asubi/creditcard/change">支払い情報再登録</a>
                    </li>
                {elseif $login.online_salon_status == 'active' || $login.online_salon_status == 'inactive'}
                    <li>
                        <a href="/asubi/creditcard/change">支払い情報変更</a>
                    </li>
                {/if}
                    <li>
                        <a href="/asubi/login/logout">ログアウト</a>
                    </li>
                {if $login.online_salon_status == 'active' || $login.online_salon_status == 'inactive'}
                    <li>
                        <a href="/asubi/leaving/confirm/">退会</a>
                    </li>
                {/if}
            </div>
        </ul>
        {/if}
    </div>
</div>
