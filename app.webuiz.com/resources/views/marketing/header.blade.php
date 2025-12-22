<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Draggify.com - @yield('title')</title>
    <meta name="description"
        content="Build professional websites easily with Draggify.com. Our platform offers AI-powered tools, intuitive drag-and-drop builder, and responsive design for optimal performance. Start your website creation journey today!">
    <meta name="keywords" content="Draggify, Website Builder, Best Website Builder, Drag-and-Drop Website Builder, Website Creator, Responsive Website Builder, SEO-Friendly Website Builder, Easy Website Builder, Website Design Tool, Customizable Website Builder, Landing Page Builder, No-Code Website Builder, Web Design Platform, Fast Website Builder, User-Friendly Website Builder, Mobile-Friendly Website Builder, Website Builder Software">
    <meta name="author" content="draggify.com">
    <meta property="og:title" content="draggify.com - Build Professional Websites Easily">
    <meta property="og:description"
        content="Build professional websites easily with Draggify.com. Our platform offers AI-powered tools, intuitive drag-and-drop builder, and responsive design for optimal performance. Start your website creation journey today!">
    <meta property="og:image" content="https://www.draggify.com/assets/images/logos/draggify_black.png">
    <meta property="og:url" content="https://www.draggify.com">
    <meta property="og:type" content="website">
    <meta name="twitter:title" content="Draggify.com - Build Professional Websites Easily">
    <meta name="twitter:description"
        content="Build professional websites easily with Draggify.com. Our platform offers AI-powered tools, intuitive drag-and-drop builder, and responsive design for optimal performance. Start your website creation journey today!">
    <meta name="twitter:image" content="https://www.draggify.com/assets/images/logos/draggify_black.png">
    <meta name="twitter:card" content="https://www.draggify.com/assets/images/hero/hero-three-bg.png">
    <link rel="canonical" href="https://www.draggify.com">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicon-io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon-io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon-io/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/images/favicon-io/site.webmanifest') }}">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Work+Sans:wght@400;500;600&amp;display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/flaticon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome-5.14.0.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/nice-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/slick.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        /* Responsive logo styles */
        .logo-outer .logo img {
            width: 150px !important;
            max-width: 180px;
            height: auto;
        }
        .mobile-logo img {
            width: 150px !important;
            max-width: 150px;
            height: auto;
        }
        /* Tablet (768px and up) */
        @media (min-width: 768px) {
            .logo-outer .logo img {
                width: 150px !important;
            }
            .mobile-logo img {
                width: 150px !important;
            }
        }
        /* Desktop (992px and up) */
        @media (min-width: 992px) {
            .logo-outer .logo img {
                width: 180px !important;
            }
        }
        /* Small mobile (max-width: 480px) */
        @media (max-width: 480px) {
            .logo-outer .logo img {
                width: 150px !important;
            }
            .mobile-logo img {
                width: 150px !important;
            }
        }
        /* Disable chat widget and icon-bar globally - COMPREHENSIVE */
        /* Tidio-specific */
        #tidio-chat-iframe,
        .tidio-chat,
        .tidio-chat-button,
        [id*="tidio"],
        [id*="Tidio"],
        [class*="tidio"],
        [class*="Tidio"],
        iframe[src*="tidio"],
        iframe[src*="code.tidio.co"],
        /* Tidio widget button (from dev tools) */
        #button-body,
        [id="button-body"],
        button[id="button-body"],
        [data-testid="widgetButtonBody"],
        button[data-testid="widgetButtonBody"],
        /* Tidio containers */
        #tidio-chat,
        #tidio-chat-container,
        [id*="tidio-chat"],
        [class*="tidio-chat"],
        [data-tidio],
        [data-tidio-id] {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
            position: absolute !important;
            left: -9999px !important;
            width: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
            z-index: -9999 !important;
        }
        .icon-bar,
        .navbar-toggle {
            display: none !important;
            visibility: hidden !important;
        }
    </style>
    {{-- Chat widget disabled temporarily --}}
    {{-- <script src="//code.tidio.co/y6omcxkigngkhz3s2hwdrpuih7cf97ar.js" async></script> --}}
    
    <!-- CRITICAL: Block Tidio IMMEDIATELY - Must be first script -->
    <script>
        (function() {
            'use strict';
            // Block Tidio API immediately
            try {
                Object.defineProperty(window, 'tidioChatApi', {value: undefined, writable: false, configurable: false});
            } catch(e) {}
            
            // Block script creation BEFORE any other scripts run
            var origCreateElement = document.createElement;
            document.createElement = function(tag) {
                var el = origCreateElement.call(document, tag);
                if (tag.toLowerCase() === 'script') {
                    var origSetAttr = el.setAttribute;
                    var origSetAttrNS = el.setAttributeNS;
                    el.setAttribute = function(name, value) {
                        if (name === 'src' && typeof value === 'string' && (value.indexOf('tidio') !== -1 || value.indexOf('code.tidio.co') !== -1)) {
                            return; // Block Tidio
                        }
                        return origSetAttr.call(this, name, value);
                    };
                    el.setAttributeNS = function(ns, name, value) {
                        if (name === 'src' && typeof value === 'string' && (value.indexOf('tidio') !== -1 || value.indexOf('code.tidio.co') !== -1)) {
                            return; // Block Tidio
                        }
                        return origSetAttrNS.call(this, ns, name, value);
                    };
                }
                return el;
            };
            
            // Hide Tidio elements aggressively - COMPREHENSIVE
            var hide = function() {
                var selectors = [
                    '[id*="tidio"]', '[id*="Tidio"]',
                    '[class*="tidio"]', '[class*="Tidio"]',
                    'iframe[src*="tidio"]', 'iframe[src*="Tidio"]', 'iframe[src*="code.tidio.co"]',
                    '#tidio-chat-iframe', '.tidio-chat', '.tidio-chat-button',
                    // Tidio widget button (from dev tools - id="button-body")
                    '#button-body',
                    '[id="button-body"]',
                    'button[id="button-body"]',
                    '[data-testid="widgetButtonBody"]',
                    'button[data-testid="widgetButtonBody"]',
                    // Tidio containers
                    '#tidio-chat',
                    '#tidio-chat-container',
                    '[id*="tidio-chat"]',
                    '[class*="tidio-chat"]',
                    '[data-tidio]',
                    '[data-tidio-id]'
                ];
                selectors.forEach(function(sel) {
                    try {
                        document.querySelectorAll(sel).forEach(function(el) {
                            el.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; pointer-events: none !important; position: absolute !important; left: -9999px !important; width: 0 !important; height: 0 !important;';
                            el.remove();
                        });
                    } catch(e) {}
                });
            };
            hide();
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', hide);
            }
            window.addEventListener('load', hide);
            setInterval(hide, 100); // Check every 100ms
        })();
    </script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-413PKVHE59"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-413PKVHE59');
    </script>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3651280986760709"
     crossorigin="anonymous"></script>
</head>

<body class="home-two">
    <div class="page-wrapper">
        <div class="preloader">
            <div class="custom-loader"></div>
        </div>
        <header class="main-header menu-absolute">
            <div class="header-upper">
                <div class="container container-1520 clearfix">
                    <div class="header-inner py-20 rpy-10 rel d-flex align-items-center">
                        <div class="logo-outer">
                            <div class="logo"><a href="/"><img src="{{ asset('assets/images/logos/draggify_black.png') }}"
                                        alt="Logo" title="Logo" class="logo"></a></div>
                        </div>
                        <div class="nav-outer ms-lg-auto clearfix">
                            <nav class="main-menu navbar-expand-lg">
                                <div class="navbar-header py-10">
                                    <div class="mobile-logo">
                                        <a href="/">
                                            <img src="{{ asset('assets/images/logos/draggify_black.png') }}" alt="Logo" title="Logo">
                                        </a>
                                    </div>
                                    {{-- Mobile menu toggle (icon-bar) disabled temporarily --}}
                                    {{-- <button type="button" class="navbar-toggle" data-bs-toggle="collapse"
                                        data-bs-target=".navbar-collapse">
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                    </button> --}}
                                </div>
                                <div class="navbar-collapse collapse clearfix">
                                    <ul class="navigation clearfix">
                                        <li><a href="/">Home</a></li>
                                        <li><a href="/features">Features</a></li>
                                        <li><a href="/pricing">Pricing</a></li>
                                        <li><a href="/faq">FAQ</a></li>
                                        <li><a href="/contact">Contact Us</a></li>
                                    </ul>
                                </div>
                            </nav>
                        </div>
                        <div class="menu-btns ms-lg-auto">
                            <a href="{{ url('/login') }}" class="light-btn">Log in</a>
                            <a href="{{ url('/register') }}" class="theme-btn">Register <i
                                    class="far fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

