<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-md">
    <div class="page-wrapper">
        <!-- BEGIN HEADER -->
        <div class="page-header navbar navbar-fixed-top">
            <!-- BEGIN HEADER INNER -->
            <div class="page-header-inner ">
                <!-- BEGIN LOGO -->
                <div class="page-logo">
                    <div class="menu-toggler sidebar-toggler">
                        <span></span>
                    </div>
                </div>
                <!-- END LOGO -->
                <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                    <span></span>
                </a>
                <!-- END RESPONSIVE MENU TOGGLER -->
                <!-- BEGIN TOP NAVIGATION MENU -->
                <div class="top-menu">
                    <ul class="nav navbar-nav pull-right">
                        <!-- BEGIN NOTIFICATION DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after "dropdown-extended" to change the dropdown styte -->
                        <!-- DOC: Apply "dropdown-hoverable" class after below "dropdown" and remove data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to enable hover dropdown mode -->
                        <!-- DOC: Remove "dropdown-hoverable" and add data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to the below A element with dropdown-toggle class -->
                        <!-- END INBOX DROPDOWN -->
                        <!-- BEGIN TODO DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->

                        <!-- END TODO DROPDOWN -->
                        <!-- BEGIN USER LOGIN DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <li class="dropdown dropdown-user">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <!-- <img alt="" class="img-circle" src="#" /> -->
                                <span class="username username-hide-on-mobile">
                                    @if(session('admin'))
                                        {{session('admin')->name}}
                                    @endif
                                </span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default">
                                <li>
                                    <a href="/admin/profile">
                                        <i class="icon-user"></i> My Profile </a>
                                </li>
                                <li>
                                    <a href="/admin/logout">
                                        <i class="icon-key"></i> Log Out </a>
                                </li>
                            </ul>
                        </li>
                        <!-- END USER LOGIN DROPDOWN -->
                        <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <li class="dropdown dropdown-quick-sidebar-toggler">
                            <a href="/admin/logout" class="dropdown-toggle">
                                <i class="icon-logout"></i>
                            </a>
                        </li>
                        <!-- END QUICK SIDEBAR TOGGLER -->
                    </ul>
                </div>
                <!-- END TOP NAVIGATION MENU -->
            </div>
            <!-- END HEADER INNER -->
        </div>
        <!-- END HEADER -->
        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <div class="clearfix"> </div>
        <!-- END HEADER & CONTENT DIVIDER -->


        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <!-- BEGIN SIDEBAR -->
            <div class="page-sidebar-wrapper">
                <!-- BEGIN SIDEBAR -->
                <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                <div class="page-sidebar navbar-collapse collapse" style="position: fixed; z-index: 99;">
                    <!-- BEGIN SIDEBAR MENU -->
                    <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
                    <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
                    <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
                    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                    <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
                    <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                    <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                        <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
                        <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                        <li class="sidebar-toggler-wrapper hide">
                            <div class="sidebar-toggler">
                                <span></span>
                            </div>
                        </li>
                        <!-- END SIDEBAR TOGGLER BUTTON -->
                        <!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
                        <li class="nav-item  ">
                            <a href="/admin/dashboard" class="nav-link ">
                                <i class="icon-home"></i><span class="title">Dashboard</span>
                            </a>

                        </li>

                        <li class="nav-item  ">
                            <a href="/admin/customers" class="nav-link">
                                <i class="icon-users"></i>
                                <span class="title">Customers</span>
                            </a>
                        </li>

                        <li class="nav-item  ">
                            <a href="/admin/cleaners" class="nav-link">
                                <i class="fa fa-tasks"></i>
                                <span class="title">Cleaners</span>
                            </a>
                        </li>

                        <li class="nav-item  ">
                            <a href="/admin/documents" class="nav-link">
                                <i class="icon-docs"></i>
                                <span class="title">Cleaner Documents</span>
                            </a>
                        </li>

                        <li class="nav-item  ">
                            <a href="/admin/orders" class="nav-link">
                                <i class="fa fa-database"></i>
                                <span class="title">Orders</span>
                            </a>
                        </li>
                        <li class="nav-item  ">
                            <a href="/admin/transactions" class="nav-link">
                                <i class="icon-speedometer"></i>
                                <span class="title">Transactions</span>
                            </a>
                        </li>

                        <li class="nav-item  ">
                            <a href="/admin/payouts" class="nav-link">
                                <i class="fa fa-clone"></i>
                                <span class="title">Payouts</span>
                            </a>
                        </li>

                        <li class="nav-item  ">
                            <a href="/admin/reviews" class="nav-link">
                                <i class="fa fa-comment-o"></i>
                                <span class="title">Reviews</span>
                            </a>
                        </li>

                        <!-- <li class="nav-item  ">
                            <a href="/admin/banners" class="nav-link">
                                <i class="fa fa-photo"></i>
                                <span class="title">Banners</span>
                            </a>
                        </li> -->

                        <!-- <li class="nav-item">
                            <a href="/admin/extraservices" class="nav-link nav-toggle">
                                <i class="fa fa-bullseye"></i>
                                <span class="title">Extra Services</span>
                            </a>
                        </li> -->

                        <!-- <li class="nav-item">
                            <a href="/admin/taxsettings" class="nav-link nav-toggle">
                                <i class="fa fa-dollar"></i>
                                <span class="title">Tax Settings</span>
                            </a>
                        </li> -->

                        <li class="nav-item">
                            <a href="/MailContent" class="nav-link nav-toggle">
                                <i class="fa fa-envelope"></i>
                                <span class="title">Mail Content</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/admin/settings" class="nav-link nav-toggle">
                                <i class="fa fa-cog"></i>
                                <span class="title">Settings</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/admin/push" class="nav-link nav-toggle">
                                <i class="fa fa-comment"></i>
                                <span class="title">Push Notifications</span>
                            </a>
                        </li>

                    </ul>
                    <!-- END SIDEBAR MENU -->
                    <!-- END SIDEBAR MENU -->
                </div>
                <!-- END SIDEBAR -->
            </div>
            <!-- END SIDEBAR -->
            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <!-- BEGIN CONTENT BODY -->
