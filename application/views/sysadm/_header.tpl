
    <!-- Navbar -->
    <nav class="navbar fixed-top navbar-expand-lg scrolling-navbar d-none d-md-block bg-white">
    <div class="container-fluid">

        <a class="navbar-brand waves-effect" href="/{$module}/">
            <img src="/img/logo-black.png" width="120" height="40">
        </a>

        <!-- Navbar links -->
        <ul class="navbar-nav mr-auto">
            <!-- Dropdown -->
            <li class="nav-item dropdown notifications-nav">
                <a class="nav-link dropdown-toggle waves-effect" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-users"></i>
                    <span class="d-none d-md-inline-block">システム管理者</span>
                </a>
                <div class="dropdown-menu dropdown-primary">
                    <a class="dropdown-item waves-effect waves-light" href="/{$module}/administrator/">
                        <i class="fas fa-list"></i>
                        <span>一覧</span>
                    </a>
                    <a class="dropdown-item waves-effect waves-light" href="/{$module}/administrator/form/">
                        <i class="far fa-plus-square"></i>
                        <span>新規登録</span>
                    </a>
                </div>
            </li>
            <li class="nav-item dropdown notifications-nav">
                <a class="nav-link dropdown-toggle waves-effect" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-store-alt"></i>
                    <span class="d-none d-md-inline-block">サロン管理</span>
                </a>
                <div class="dropdown-menu dropdown-primary">
                    <a class="dropdown-item waves-effect waves-light" href="/{$module}/salon/">
                        <i class="fas fa-list"></i>
                        <span>サロン一覧</span>
                    </a>
                    <a class="dropdown-item waves-effect waves-light" href="/{$module}/stylist/">
                        <i class="fas fa-list"></i>
                        <span>スタイリスト一覧</span>
                    </a>
                    <a class="dropdown-item waves-effect waves-light" href="/{$module}/salon/form/">
                        <i class="far fa-plus-square"></i>
                        <span>新規登録</span>
                    </a>
                </div>
            </li>
            <li class="nav-item dropdown notifications-nav">
                <a class="nav-link dropdown-toggle waves-effect" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fab fa-wpforms"></i>
                    <span class="d-none d-md-inline-block">アンケート管理</span>
                </a>
                <div class="dropdown-menu dropdown-primary">
                    <a class="dropdown-item waves-effect waves-light" href="/{$module}/questionnaire/">
                        <i class="fas fa-list"></i>
                        <span>一覧</span>
                    </a>
                    <a class="dropdown-item waves-effect waves-light" href="/{$module}/questionnaire/form/">
                        <i class="far fa-plus-square"></i>
                        <span>新規登録</span>
                    </a>
                </div>
            </li>
            <li class="nav-item dropdown notifications-nav">
                <a class="nav-link dropdown-toggle waves-effect" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fab fa-facebook-square"></i>
                    <span class="d-none d-md-inline-block">Onlineサロン管理</span>
                </a>
                <div class="dropdown-menu dropdown-primary">
                    <a class="dropdown-item waves-effect waves-light" href="/{$module}/online_salon_user/">
                        <i class="fas fa-user-friends"></i>
                        <span>Onlineサロンユーザ</span>
                    </a>
                    <a class="dropdown-item waves-effect waves-light" href="/{$module}/online_salon_user/checking">
                        <i class="far fa-address-card" aria-hidden="true"></i>
                        <span>入会審査待ち</span>
                    </a>
                </div>
            </li>
            {*
            <li class="nav-item">
            <a class="nav-link waves-effect"><i class="far fa-comments"></i> <span class="clearfix d-none d-sm-inline-block">Support</span></a>
            </li>
            *}
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle waves-effect" href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user"></i> <span class="clearfix d-none d-sm-inline-block">Profile</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item waves-effect waves-light" href="/{$module}/login/logout/">Log Out</a>
                    <a class="dropdown-item waves-effect waves-light" href="/{$module}/administrator/mypage/">My account</a>
                </div>
            </li>
        </ul>
        <!-- Navbar links -->

    </div>
    </nav>
    <!-- Navbar -->

