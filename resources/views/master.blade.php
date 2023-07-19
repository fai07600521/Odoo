<!doctype html>
<html class="no-focus">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title> {{ config('app.name') }} | @yield('title')</title>
    <meta name="description" content="GIST - Multibrand Store System">
    <meta name="author" content="Jirapat Hangjaraon">
    <meta property="og:title" content="GIST - Multibrand Store System">
    <meta property="og:site_name" content="GIST - Multibrand Store System">
    <meta property="og:description" content="GIST - Multibrand Store System for manage brand and sale system.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://shop.castlec.in.th">
    <link rel="shortcut icon" href="/favicon.png">
    <link href="//fonts.googleapis.com/css?family=Kanit&display=swap" rel="stylesheet">
    @yield('style')
    <link rel="stylesheet" href="/assets/js/plugins/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" id="css-main" href="/assets/css/codebase.min.css">
    <link rel="stylesheet" id="css-main" href="/assets/css/nobaddays.css">
</head>
<body>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-fixed main-content-narrow">
        <aside id="side-overlay">
            <div id="side-overlay-scroll">
                <div class="content-header content-header-fullrow">
                    <div class="content-header-section align-parent">
                        <button type="button" class="btn btn-circle btn-dual-secondary align-v-r" data-toggle="layout" data-action="side_overlay_close">
                            <i class="fa fa-times text-danger"></i>
                        </button>
                        <div class="content-header-item">
                            <a class="img-link mr-5" href="be_pages_generic_profile.html">
                                <img class="img-avatar img-avatar32" src="/assets/media/avatars/avatar15.jpg" alt="">
                            </a>
                            <a class="align-middle link-effect text-primary-dark font-w600" href="/admin/profile">{{Auth::user()->name}}</a>
                        </div>
                    </div>
                </div>
                <!-- END Side Header -->

            </div>
            <!-- END Side Overlay Scroll Container -->
        </aside>
        <!-- END Side Overlay -->


        <nav id="sidebar">
            <!-- Sidebar Scroll Container -->
            <div id="sidebar-scroll">
                <!-- Sidebar Content -->
                <div class="sidebar-content">
                    <!-- Side Header -->
                    <div class="content-header content-header-fullrow px-15">
                        <!-- Mini Mode -->
                        <div class="content-header-section sidebar-mini-visible-b">
                            <!-- Logo -->
                            <span class="content-header-item font-w700 font-size-xl float-left animated fadeIn">
                                <span class="text-dual-primary-dark">c</span><span class="text-primary">b</span>
                            </span>
                            <!-- END Logo -->
                        </div>
                        <!-- END Mini Mode -->

                        <!-- Normal Mode -->
                        <div class="content-header-section text-center align-parent sidebar-mini-hidden">
                            <!-- Close Sidebar, Visible only on mobile screens -->
                            <!-- Layout API, functionality initialized in Codebase() -> uiApiLayout() -->
                            <button type="button" class="btn btn-circle btn-dual-secondary d-lg-none align-v-r" data-toggle="layout" data-action="sidebar_close">
                                <i class="fa fa-times text-danger"></i>
                            </button>
                            <!-- END Close Sidebar -->

                            <!-- Logo -->
                            <div class="content-header-item">
                             <a href="/">
                               <img id="logo-nav" src="/assets/logo.png" style="width: 50%;">
                           </a>
                       </div>
                       <!-- END Logo -->
                   </div>
                   <!-- END Normal Mode -->
               </div>
               <!-- END Side Header -->

               <!-- END Side User -->

               <!-- Side Navigation -->
               <div class="content-side content-side-full" style="padding-top: 150px;">
@if(Auth::user()->role=="2")
                <h5 class="text-center">ผู้ดูแลระบบ</h5>
                <ul class="nav-main">
                   <li>
                    <a id="dashboardbtn" href="/"><i class="si si-cup"></i><span class="sidebar-mini-hide">ภาพรวมระบบ</span></a>
                </li>

                <li>
                    <a id="posbtn" href="/admin/pos"><i class="si si-screen-desktop"></i><span class="sidebar-mini-hide">POS</span></a>
                </li>
                               <li>
                    <a id="posstartbtn" href="/admin/posstart"><i class="si si-briefcase"></i><span class="sidebar-mini-hide">กำหนดเงินทอน</span></a>
                </li>
                <li>
                    <a id="printbarcodebtn" href="/products/barcodeprint"><i class="si si-printer"></i>
                        <span class="sidebar-mini-hide">พิมพ์บาร์โค๊ดแบบกำหนดเอง</span>
                    </a> 
                </li>
                <li>
                    <a id="printpromotionbtn" href="/products/promotionprint"><i class="si si-printer"></i>
            <span class="sidebar-mini-hide">พิมพ์ป้ายราคา</span>
        </a> 
                </li>


                <li class="nav-main-heading"><span class="sidebar-mini-hidden">การจัดการการขาย</span></li>
                <li>    
                  <li>
                   <a id="reportbrandbtn" href="/admin/brandsales"><i class="si si-list"></i>
                    <span class="sidebar-mini-hide">ยอดขายรายแบรนด์</span>
                </a>
            </li>
            <li>
                <a id="reportorderbtn" href="/admin/order"><i class="si si-list"></i>
                    <span class="sidebar-mini-hide">รายการขาย</span>
                </a>
            </li>
            <li>
                <a id="reportdaybtn" href="/admin/brand/report"><i class="si si-bar-chart"></i>
                    <span class="sidebar-mini-hide">สรุปยอดขาย</span>
                </a>
            </li>
            <li>
                <a id="reportbrandsumbtn" href="/admin/report"><i class="si si-list"></i>
                <span class="sidebar-mini-hide">สรุปยอดขายแบรนด์</span>
            </a>
        </li>
        <li>       
            <a id="productbtn" href="/products"><i class="si si-bar-chart"></i>
                <span class="sidebar-mini-hide">การจัดการสินค้า</span>
            </a>
        </li>
         <li>       
            <a id="promotionbtn" href="/admin/promotions"><i class="si si-badge"></i>
                <span class="sidebar-mini-hide">การจัดการโปรโมชั่น <font style="color:red;">(อัตโนมัติ)</font></span>
            </a>
        </li>
                 <li>       
            <a id="promotionnewbtn" href="/admin/promonotification"><i class="si si-badge"></i>
                <span class="sidebar-mini-hide">การจัดการโปรโมชั่น <font style="color:red;">(แจ้งเตือน)</font></span>
            </a>
        </li>
    </li>

    <li class="nav-main-heading"><span class="sidebar-mini-hidden">การจัดสต๊อกสินค้า</span></li>
    <li>    
      <li>
        <a id="reportstockbtn" href="/admin/stock/report"><i class="si si-social-dropbox"></i>
            <span class="sidebar-mini-hide">รายงานสต๊อกสินค้า</span>
        </a>
        <a id="stockadjustbtn" href="/admin/stock/adjust"><i class="si si-share-alt"></i>
            <span class="sidebar-mini-hide">ปรับปรุงยอดสต๊อกสินค้า</span>
        </a>        
        <a id="transferbtn" href="/admin/stock/transfer"><i class="si si-share-alt"></i>
            <span class="sidebar-mini-hide">ย้ายคลังสินค้า</span>
        </a>  
        <a id="poallbtn" href="/purchase"><i class="si si-login"></i>
            <span class="sidebar-mini-hide">จัดการใบ PO</span>
        </a>  
    </li>

    <li class="nav-main-heading"><span class="sidebar-mini-hidden">การจัดการระบบ</span></li>
    <li>    
      <li>
        <a id="brandmanagebtn" href="/admin/brand"><i class="si si-user"></i>
            <span class="sidebar-mini-hide">การจัดการแบรนด์</span>
        </a>
        <a id="adminmanagebtn" href="/admin/admin"><i class="si si-user"></i>
            <span class="sidebar-mini-hide">การจัดการผู้ดูแลระบบ</span>
        </a>        
        <a id="membermanagebtn" href="/admin/member"><i class="si si-user"></i>
            <span class="sidebar-mini-hide">การจัดการสมาชิกร้านค้า</span>
        </a>  
        <a id="branchmanagebtn" href="/admin/branch"><i class="si si-home"></i>
            <span class="sidebar-mini-hide">การจัดการสาขา</span>
        </a>               
    </li>
</ul>
@else
<h5>แบรนด์: {{Auth::user()->brand_name}}</h5>
<ul class="nav-main">
    <li>
        <a id="dashboardbtn" href="/"><i class="si si-cup"></i><span class="sidebar-mini-hide">ภาพรวมระบบ</span></a>
    </li>
    <!-- <li>
        <a id="productbtn" href="/products"><i class="si si-social-dropbox"></i><span class="sidebar-mini-hide">การจัดการสินค้า</span></a>
    </li> -->

    <!-- <li class="nav-main-heading"><span class="sidebar-mini-hidden">นำสินค้าเข้าสต๊อก</span></li>
    <li>    
      <li>
        <a id="pocreatebtn" href="/purchase/add"><i class="si si-note"></i>
            <span class="sidebar-mini-hide">สร้างใบนำเข้าสินค้า</span>
        </a>
        <a id="poallbtn" href="/purchase"><i class="si si-login"></i>
            <span class="sidebar-mini-hide">ตรวจสอบสถานะนำเข้าสินค้า</span>
        </a>        
        <a id="printbarcodebtn" href="/products/barcodeprint"><i class="si si-printer"></i>
            <span class="sidebar-mini-hide">พิมพ์บาร์โค๊ดแบบกำหนดเอง</span>
        </a> 
        
    </li> -->
    <li class="nav-main-heading"><span class="sidebar-mini-hidden">รายงานการขาย</span></li>
    <li>    
      <li>
        <a id="reportdaybtn" href="/report"><i class="si si-bar-chart"></i>
            <span class="sidebar-mini-hide">สรุปยอดขาย Brand</span>
        </a>        
        <!-- <a id="reportstockbtn" href="/stock"><i class="si si-bar-chart"></i>
            <span class="sidebar-mini-hide">รายงานสต๊อกสินค้า</span>
        </a> -->
    </li>
    <!-- <li class="nav-main-heading animated infinite pulse"><span class="sidebar-mini-hidden">คู่มือการใช้งาน</span></li>
    <li>    
      <li>
        <a id="help1btn" href="/help/1"><i class="si si-book-open"></i>
            <span class="sidebar-mini-hide">การเพิ่มสินค้า</span>
        </a>
        <a id="help2btn" href="/help/2"><i class="si si-book-open"></i>
            <span class="sidebar-mini-hide">การสร้างใบนำเข้าสินค้า</span>
        </a>        
        <a id="help3btn" href="/help/3"><i class="si si-book-open"></i>
            <span class="sidebar-mini-hide">การพิมพ์บาร์โค๊ด</span>
        </a>
    </li> -->
</ul>
@endif
</div>
<!-- END Side Navigation -->
</div>
<!-- Sidebar Content -->
</div>
<!-- END Sidebar Scroll Container -->
</nav>
<!-- END Sidebar -->

<!-- Header -->
<header id="page-header">
    <!-- Header Content -->
    <div class="content-header">
        <!-- Left Section -->
        <div class="content-header-section">
            <!-- Toggle Sidebar -->
            <!-- Layout API, functionality initialized in Codebase() -> uiApiLayout() -->
            <button type="button" class="btn btn-circle btn-dual-secondary" data-toggle="layout" data-action="sidebar_toggle">
                <i class="fa fa-navicon"></i>
            </button>
            <!-- END Toggle Sidebar -->

        </div>
        <!-- END Left Section -->

        <!-- Right Section -->
        <div class="content-header-section">
            <!-- User Dropdown -->
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-rounded btn-dual-secondary" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-user d-sm-none"></i>
                    <span class="d-none d-sm-inline-block">{{Auth::user()->name}}</span>
                    <i class="fa fa-angle-down ml-5"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right min-width-200" aria-labelledby="page-header-user-dropdown">
                    <h5 class="h6 text-center py-10 mb-5 border-b text-uppercase">User</h5>
                    <a class="dropdown-item" href="/admin/profile">
                        <i class="si si-user mr-5"></i> โปรไฟล์
                    </a>

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" style="color:red;" href="/logout">
                        <i class="si si-logout mr-5"></i> ออกจากระบบ
                    </a>
                </div>
            </div>
            <!-- END User Dropdown -->

            <!-- Notifications -->
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-rounded btn-dual-secondary" id="page-header-notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-flag"></i>

                    <span class="badge badge-primary badge-pill"><!--notification count--></span>
                </button>
                <div class="dropdown-menu dropdown-menu-right min-width-300" aria-labelledby="page-header-notifications">
                    <h5 class="h6 text-center py-10 mb-0 border-b text-uppercase">Notifications</h5>
                    <ul class="list-unstyled my-20">
                     <li style="text-align: center;">No notifications</li>
                 </ul>
                 <div class="dropdown-divider"></div>
                 <a class="dropdown-item text-center mb-0" href="javascript:void(0)">
                    <i class="fa fa-flag mr-5"></i> View All
                </a>
            </div>
        </div>
        <!-- END Notifications -->
        <!-- END Toggle Side Overlay -->
    </div>
    <!-- END Right Section -->
</div>
<!-- END Header Content -->


<!-- Header Loader -->
<!-- Please check out the Activity page under Elements category to see examples of showing/hiding it -->
<div id="page-header-loader" class="overlay-header bg-primary">
    <div class="content-header content-header-fullrow text-center">
        <div class="content-header-item">
            <i class="fa fa-sun-o fa-spin text-white"></i>
        </div>
    </div>
</div>
<!-- END Header Loader -->
</header>
<!-- END Header -->

<!-- Main Container -->
<main id="main-container">

    @yield('content')


</main>
<!-- END Main Container -->

<!-- Footer -->
<footer id="page-footer" class="opacity-0">
    <div class="content py-20 font-size-xs clearfix"> 
        <div class="float-left">
            <a class="font-w600" href="https://shop.nobaddays.in.th" target="_blank">{{ config('app.name') }}</a> &copy; <span class="js-year-copy">2019</span>
        </div>
    </div>
</footer>
<!-- END Footer -->
</div>

</body>
<script src="/assets/js/codebase.core.min.js"></script>
<script src="/assets/js/codebase.app.min.js"></script>
<script src="/assets/js/plugins/chartjs/Chart.bundle.min.js"></script>
<script src="/assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js"></script>
<script src="/assets/js/plugins/es6-promise/es6-promise.auto.min.js"></script>
<script src="/assets/js/plugins/sweetalert2/sweetalert2.min.js"></script>
<script type="text/javascript">
    @if (\Session::has('sysmessage'))
    $.notify({
        message: '{{Session::get('sysmessage')["msg"]}}' 
    },{
        type: '{{ Session::get('sysmessage')["msgcode"]==500 ? 'danger' : 'success' }}'
    });
    @endif

    @if(isset($sysmessage))
    $.notify({
        message: '{{$sysmessage["msg"]}}' 
    },{
        type: '{{ $sysmessage["msgcode"]==500 ? 'danger' : 'success' }}'
    });
    @endif
    $( "form" ).submit(function( event ) {
        $( ".submitformbtn i " ).removeClass();
        $( ".submitformbtn i " ).addClass("fa fa-cog fa-spin");
    });
</script>
@yield('script')
</html>