
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  @yield('title')
  <meta content="" name="description">
  <meta content="" name="keywords">
  <meta charset="utf-8" />
  <meta name="description" content="MIKBAM" />
  <meta name="keywords" content="MIKBAM" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta property="og:locale" content="en_US" />
  <meta property="og:type" content="article" />
  <meta property="og:title" content="MIKBAM" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="{{ asset('assets/css/opensansfont.css') }}" rel="stylesheet">
  {{-- <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet"> --}}

  <!-- Vendor CSS Files -->
  <link href="{{ url('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ url('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ url('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ url('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ url('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ url('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
  <!-- <link href="{{-- url('assets/vendor/simple-datatables/style.css') --}}" rel="stylesheet"> -->
  <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendor/datatables/datatables.min.css') }}" />

  <!-- Template Main CSS File -->
  <link href="{{ url('assets/css/style.css') }}" rel="stylesheet">

  <link href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/sweetalert2/dist/toast.css') }}" rel="stylesheet">

  <script src="{{ asset('assets/vendor/jquery/jquery-3.7.1.min.js') }}"></script>   
  <script src="{{ asset('assets/vendor/block-ui/jquery.blockUI.js') }} "></script>

  <!-- =======================================================
  * Template Name: NiceAdmin - v2.1.0
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
  @stack('css')
</head>

<body>
  <input type="hidden" name="baseL" id="baseL" value="{{ url('') }}">
  <input type="hidden" id="token"  value="{{ csrf_token() }}">

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="{{ url('') }}" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">MIKBAM</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        {{-- <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon--> --}}

      

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            {{-- <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle"> --}}
            <span class="d-none d-md-block dropdown-toggle ps-2 router-name"></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6 class="router-name"></h6>
              <span id="router-host"></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{ url('/setting') }}">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                <i class="bi bi-question-circle"></i>
                <span>Need Help?</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">
      @php($uri = isset(explode('/', request()->url())[3]) ?  (explode('/', request()->url())[3] == 'demo' ? url('').'/demo' : url('')) : url(''))
      @php($sideBarMenu = \App\Helpers\AnyHelper::sideBarMenu($uri))

      @foreach($sideBarMenu as $menu)
        @if(empty($menu['sub-menu']))
          <li class="nav-item">
            <a class="nav-link {{ $menu['menu-link'] == request()->url() ? '' : 'collapsed' }}" href="{{ $menu['menu-link'] }}">
              {!! $menu['menu-icon'] !!}
              <span>{{ $menu['menu-name'] }}</span>
            </a>
          </li><!-- End Dashboard Nav -->
        @else
          <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#{{ explode(' ', $menu['menu-name'])[0] }}-nav" data-bs-toggle="collapse" href="#">
              {!! $menu['menu-icon'] !!}<span>{{ $menu['menu-name'] }}</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="{{ explode(' ', $menu['menu-name'])[0] }}-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
              @foreach ($menu['sub-menu'] as $submenu)
                <li>
                  <a href="{{ $submenu['menu-link'] }}" class="{{ $submenu['menu-link'] == request()->url() ? 'active' : '' }}">
                    <i class="bi bi-circle"></i><span>{{ $submenu['menu-name'] }}</span>
                  </a>
                </li>
              @endforeach
            </ul>
          </li><!-- End Components Nav -->
        @endif
        @endforeach
    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      @yield('pagetitle')
    </div><!-- End Page Title -->

    <section class="section dashboard">
      @yield('main')
    </section>

    <div id="modal-placement"></div>
    

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Syayidul Muwahhid</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{ url('assets/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
  <script src="{{ url('assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ url('assets/vendor/quill/quill.min.js') }}"></script>
  <script src="{{ url('assets/vendor/tinymce/tinymce.min.js') }}"></script>
  {{-- <script src="{{-- url('assets/vendor/simple-datatables/simple-datatables.js') --}}"></script> --}}
  <script src="{{ url('assets/vendor/chart.js/chart.min.js') }}"></script>
  <script src="{{ url('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ url('assets/vendor/echarts/echarts.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('/assets/vendor/datatables/datatables.min.js') }}"></script>

  <!-- Template Main JS File -->
  <script src="{{ url('assets/js/main.js') }}"></script>

  <script>
    const baseL = document.getElementById('baseL').value;
    const token = document.getElementById('token').value;
  </script>


  <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.all.min.js')}}"></script>
  <script src="{{ asset('assets/js/global.js')}}"></script>
  

  <script>
    @if(isset(explode('/', request()->url())[3]))
    @if(explode('/', request()->url())[3] != 'demo')
      if (loginData==null) { 
        window.location.href=`${baseL}/login`;
      }
      isDemo=false;
    @else
      isDemo=true;
    @endif
    @endif
    @if(request()->url() == url(''))
      if (loginData==null) { 
        window.location.href=`${baseL}/login`;
      }
    @endif
  </script>

  @stack('js')

</body>

</html>