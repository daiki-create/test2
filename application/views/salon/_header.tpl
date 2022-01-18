<!-- Navbar -->
{if $class != 'login' && $class != 'trial' && $class != 'tos'}
<nav class="navbar fixed-top navbar-expand-lg navbar-light scrolling-navbar">
    <div class="container">
        <div class="header-left">
            <img class="header-back" src="/img/salon/header-back.png" alt="">
            <a class="navbar-brand waves-effect" href="/{$module}/">
                <img src="/img/logo-white.png" width="120" height="40" alt="hairlogy logo">
            </a>
        </div>
        {if $class != 'login' && $class != 'trial'}
        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse"
            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-collapse collapse" id="navbarSupportedContent">
            <!-- Left -->
            <ul class="navbar-nav mr-auto">
                <!-- {if $login.manager_flag}
                <li class="nav-item nav-item-stylists{if $class == 'stylist'} active{/if}">
                    <a class="nav-link waves-effect" href="/{$module}/stylist/">
                        <i class="fas fa-users"></i>
                        スタイリスト
                    </a>
                </li>
                {/if} -->
                <li class="nav-item nav-item{if $class == 'report'} active{/if}">
                    <a class="nav-link waves-effect" href="/{$module}/report/detail/1/">
                        <div class="row">  
                            <div class="col-4">
                                <img src="/img/salon/icon-report.png" alt="ヘッダーアイコン1">
                            </div>
                            <div class="col-8">
                                <span class="fs-14 fw-900 color-light-black2">Report</span><br>
                                <span class="fs-11 color-light-black2">レポート</span>
                            </div>
                        </div>
                    </a>
                </li>
                <!-- {if $login.manager_flag}
                <li class="nav-item dropdown notifications-nav nav-item-qr">
                    <a class="nav-link dropdown-toggle waves-effect" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-qrcode"></i> 
                        {*<span class="d-none d-md-inline-block">アンケート管理</span>*}
                        <span class="">アンケート管理</span>
                    </a>
                    <div class="dropdown-menu dropdown-primary">
                        <a class="dropdown-item waves-effect waves-light" href="/{$module}/questionnaire/">
                            <i class="fas fa-qrcode"></i> &nbsp;
                            <span>一覧</span>
                        </a>
                        <a class="dropdown-item waves-effect waves-light" href="/{$module}/lp/">
                            <i class="fas fa-qrcode"></i> &nbsp;
                            <span>設定</span>
                        </a>
                    </div>
                </li>
                {else} -->
                <li class="nav-item nav-item{if $class == 'questionnaire'} active{/if}">
                    <a class="nav-link waves-effect" href="/{$module}/questionnaire/">
                        <div class="row">  
                            <div class="col-4">
                                <img src="/img/salon/icon-enqueue.png" alt="ヘッダーアイコン2">
                            </div>
                            <div class="col-8">
                                <span class="fs-14 fw-900 color-light-black2">Enqueue</span><br>
                                <span class="fs-11 color-light-black2">アンケート設定</span>
                            </div>
                        </div>
                    </a>
                </li>
                <!-- {/if} -->
                <li class="nav-item nav-item{if $class == 'mypage'} active{/if}">
                    <a class="nav-link waves-effect" href="/{$module}/mypage/">
                        <div class="row">  
                            <div class="col-4">
                                <img src="/img/salon/icon-mypage.png" alt="ヘッダーアイコン3">
                            </div>
                            <div class="col-8">
                                <span class="fs-14 fw-900 color-light-black2">My Page</span><br>
                                <span class="fs-11 color-light-black2">マイページ</span>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="nav-item nav-item{if $class == 'nps'} active{/if}">
                    <a class="nav-link waves-effect" href="/{$module}/nps/">
                        <div class="row">  
                            <div class="col-4">
                                <img src="/img/salon/icon-manual.png" alt="ヘッダーアイコン4">
                            </div>
                            <div class="col-8">
                                <span class="fs-14 fw-900 color-light-black2">Manual</span><br>
                                <span class="fs-11 color-light-black2">使い方</span>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <img onclick="location.href='/{$module}/login/logout/'" class="w120" src="/img/salon/btn-logout.png" alt="ログアウト">
                </li>
            </ul>
        </div>
        {/if}

    </div>
</nav>
<!-- Navbar -->
{/if}