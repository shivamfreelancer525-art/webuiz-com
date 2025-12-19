@section('title')
    {{ $page->title }}
@endsection
@if(auth()->check())
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Draggify.com - {{ $page->title }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .simple-header {
            background: #fff;
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .simple-header .logo img {
            width: 150px;
            max-width: 180px;
            height: auto;
        }
        /* Mobile (max-width: 480px) */
        @media (max-width: 480px) {
            .simple-header {
                padding: 10px 0;
            }
            .simple-header .logo img {
                width: 120px !important;
                max-width: 120px;
            }
        }
        /* Tablet (481px to 768px) */
        @media (min-width: 481px) and (max-width: 768px) {
            .simple-header .logo img {
                width: 140px !important;
                max-width: 140px;
            }
        }
        /* Desktop (769px and up) */
        @media (min-width: 769px) {
            .simple-header .logo img {
                width: 150px !important;
                max-width: 180px;
            }
        }
        body.logged-in-page .hero-area-three {
            padding-top: 100px !important;
        }
        @media (max-width: 768px) {
            body.logged-in-page .hero-area-three {
                padding-top: 80px !important;
            }
        }
    </style>
</head>
<body class="logged-in-page">
    <header class="simple-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <a href="/"><img src="{{ asset('assets/images/logos/draggify_black.png') }}" alt="Draggify Logo" class="logo"></a>
            </div>
        </div>
    </header>
@else
@include('marketing.header')
@endif
<section class="hero-area-three bgs-cover bgc-lighter {{ auth()->check() ? 'pt-100' : 'pt-210 rpt-150' }} pb-100"
    style="background-image: url({{ asset('assets/images/hero/hero-three-bg.png') }});">
    <div class="container">
        <div class="banner-inner pt-70 rpt-60 text-dark">
            <h1 class="page-title" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">{{ $page->title }}
            </h1>
        </div>
    </div>
</section>
<section class="pt-130 rpt-100 pb-95 rpb-65">
    <div class="container">
        {!! $page->body !!}
    </div>
</section>
@if(!auth()->check())
@include('marketing.footer')
@else
<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/appear.min.js') }}"></script>
<script src="{{ asset('assets/js/slick.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.nice-select.min.js') }}"></script>
<script src="{{ asset('assets/js/imagesloaded.pkgd.min.js') }}"></script>
<script src="{{ asset('assets/js/circle-progress.min.js') }}"></script>
<script src="{{ asset('assets/js/skill.bars.jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/isotope.pkgd.min.js') }}"></script>
<script src="{{ asset('assets/js/aos.js') }}"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
</body>
</html>
@endif

