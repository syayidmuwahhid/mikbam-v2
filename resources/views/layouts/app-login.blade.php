<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta content="width=device-width, initial-scale=1.0" name="viewport">

   <title>Login - MIKBAM</title>
   <meta content="" name="description">
   <meta content="" name="keywords">

   <!-- Favicons -->
   <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
   <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

   <!-- Google Fonts -->
   <link href="{{ asset('assets/css/opensansfont.css') }}" rel="stylesheet">

   <!-- Vendor CSS Files -->
   <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
   <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
   <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
   <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
   <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
   <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
   <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

   <link href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
   <link href="{{ asset('assets/vendor/sweetalert2/dist/toast.css') }}" rel="stylesheet">

   <!-- Template Main CSS File -->
   <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

   <script src="{{asset('assets/vendor/jquery/jquery-3.7.1.min.js')}}"></script>
   <script src="{{ asset('assets/vendor/block-ui/jquery.blockUI.js') }} "></script>

   <!-- =======================================================
  * Template Name: NiceAdmin - v2.1.0
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>
   <input type="hidden" name="baseL" id="baseL" value="{{ url('') }}">
   <input type="hidden" id="token"  value="{{ csrf_token() }}">

   <main>
      <div class="container">

         <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
                <div>
                    @yield('main')
                </div>
            </div>

         </section>

      </div>
   </main><!-- End #main -->

   <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

   <!-- Vendor JS Files -->
   <!-- <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script> -->
   <!-- <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script> -->
   <!-- <script src="{{ asset('assets/vendor/quill/quill.min.js') }}"></script> -->
   <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
   <!-- <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script> -->
   <!-- <script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script> -->
   <!-- <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script> -->
   <!-- <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script> -->

   <!-- Template Main JS File -->
   <script src="{{ asset('assets/js/main.js') }}"></script>
   <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.all.min.js')}}"></script>
   <script src="{{ asset('assets/js/global.js')}}"></script>

   <script>
      const baseL = document.getElementById('baseL').value;
      if (loginData !== null) { 
         window.location.href=`${baseL}`;
      }
    </script>

   @stack('js')

</body>

</html>