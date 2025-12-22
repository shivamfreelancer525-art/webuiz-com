@php
    use Illuminate\Support\Js;
    use Sentry\Laravel\Integration;
@endphp

<!DOCTYPE html>
<html
    lang="{{ $bootstrapData->get('language') }}"
    style="{{ $bootstrapData->getSelectedTheme()->getCssVariables() }}"
    @class(['dark' => $bootstrapData->getSelectedTheme('is_dark')])
>
    <head>
        <base href="{{ $htmlBaseUri }}" />

        @if (isset($seoTagsView))
            @include($seoTagsView, $pageData)
        @elseif (isset($meta))
            @include('common::prerender.meta-tags')
        @else
            <title>{{ settings('branding.site_name') }}</title>
        @endif

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, maximum-scale=5"
            data-keep="true"
        />
        <link
            rel="icon"
            type="image/x-icon"
            href="favicon/icon-144x144.png"
            data-keep="true"
        />
        <link
            rel="apple-touch-icon"
            href="favicon/icon-192x192.png"
            data-keep="true"
        />
        <link rel="manifest" href="manifest.json" data-keep="true" />
        <meta
            name="theme-color"
            content="rgb({{ $bootstrapData->getSelectedTheme()->getHtmlThemeColor() }})"
            data-keep="true"
        />

        <!-- CRITICAL: Block Tidio IMMEDIATELY - Must be first script -->
        <script>
            (function() {
                'use strict';
                // Block Tidio API immediately
                try {
                    Object.defineProperty(window, 'tidioChatApi', {
                        value: undefined,
                        writable: false,
                        configurable: false
                    });
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
            })();
        </script>

        <style>
            /* Disable chat widget globally - Load early to prevent rendering - COMPREHENSIVE */
            /* Tidio-specific selectors */
            #tidio-chat-iframe,
            .tidio-chat,
            .tidio-chat-button,
            [id*="tidio"],
            [id*="Tidio"],
            [class*="tidio"],
            [class*="Tidio"],
            iframe[src*="tidio"],
            iframe[src*="Tidio"],
            iframe[src*="code.tidio.co"],
            div[id*="tidio"],
            div[class*="tidio"],
            button[id*="tidio"],
            button[class*="tidio"],
            a[id*="tidio"],
            a[class*="tidio"],
            span[id*="tidio"],
            span[class*="tidio"],
            *[id*="tidio"],
            *[class*="tidio"],
            /* Tidio widget button body (from dev tools inspection) */
            #button-body,
            [data-testid="widgetButtonBody"],
            [id="button-body"],
            button[id="button-body"],
            button[data-testid="widgetButtonBody"],
            /* Tidio container elements */
            #tidio-chat,
            #tidio-chat-container,
            [id*="tidio-chat"],
            [class*="tidio-chat"],
            /* Any element with Tidio-related attributes */
            [data-tidio],
            [data-tidio-id],
            /* Hide all chat widgets generically */
            button[class*="chat"],
            div[class*="chat-widget"],
            iframe[title*="chat"],
            iframe[title*="Chat"] {
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
        </style>

        @if ($fontFamily = $bootstrapData->getSelectedTheme()->getFontFamily())
            @if($bootstrapData->getSelectedTheme()->isGoogleFont())
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link href="https://fonts.googleapis.com/css2?family={{$fontFamily}}:wght@400;500;600;700&display=swap" rel="stylesheet">
            @endif
        @endif

        <script>
            window.bootstrapData = {!! json_encode($bootstrapData->get()) !!};
        </script>
        
        <script>
            // AGGRESSIVE: Prevent Tidio chat widget from loading - Run immediately
            (function() {
                'use strict';
                
                // Block Tidio API (already blocked above, but ensure it stays blocked)
                try {
                    Object.defineProperty(window, 'tidioChatApi', {
                        value: undefined,
                        writable: false,
                        configurable: false
                    });
                } catch(e) {}
                
                // Hide any Tidio elements - COMPREHENSIVE with case-insensitive matching
                var hideTidio = function() {
                    var selectors = [
                        // Tidio-specific
                        '[id*="tidio"]', '[id*="Tidio"]', '[id*="TIDIO"]',
                        '[class*="tidio"]', '[class*="Tidio"]', '[class*="TIDIO"]',
                        'iframe[src*="tidio"]', 'iframe[src*="Tidio"]', 'iframe[src*="code.tidio.co"]',
                        'div[id*="tidio"]', 'div[class*="tidio"]',
                        'button[id*="tidio"]', 'button[class*="tidio"]',
                        'a[id*="tidio"]', 'a[class*="tidio"]',
                        'span[id*="tidio"]', 'span[class*="tidio"]',
                        '#tidio-chat-iframe', '.tidio-chat', '.tidio-chat-button',
                        '*[id*="tidio"]', '*[class*="tidio"]',
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
                        // Tidio data attributes
                        '[data-tidio]',
                        '[data-tidio-id]',
                        // Generic chat widgets
                        'button[class*="chat"][id*="button"]',
                        'div[class*="chat-widget"]',
                        'iframe[title*="chat"]',
                        'iframe[title*="Chat"]'
                    ];
                    
                    selectors.forEach(function(selector) {
                        try {
                            var elements = document.querySelectorAll(selector);
                            elements.forEach(function(el) {
                                el.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; pointer-events: none !important; position: absolute !important; left: -9999px !important; width: 0 !important; height: 0 !important; overflow: hidden !important; z-index: -9999 !important;';
                                el.remove();
                            });
                        } catch(e) {}
                    });
                    
                    // Also check all iframes and remove Tidio ones
                    try {
                        document.querySelectorAll('iframe').forEach(function(iframe) {
                            if (iframe.src && (iframe.src.indexOf('tidio') !== -1 || iframe.src.indexOf('code.tidio.co') !== -1)) {
                                iframe.remove();
                            }
                        });
                    } catch(e) {}
                };
                
                // Run immediately
                hideTidio();
                
                // Run after DOM loads
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', hideTidio);
                } else {
                    hideTidio();
                }
                
                // Run after page loads
                window.addEventListener('load', hideTidio);
                
                // Run periodically to catch dynamically loaded elements - More frequent
                setInterval(hideTidio, 100);
            })();
        </script>

        @if (isset($devCssPath))
            <link rel="stylesheet" href="{{ $devCssPath }}" />
        @endif

        @viteReactRefresh
        @vite('resources/client/main.tsx')

        @if (file_exists($customCssPath))
            @if ($content = file_get_contents($customCssPath))
                <style>
                    {!! $content !!}
                </style>
            @endif
        @endif

        <style>
            /* Responsive logo styles for auth pages (login/register) */
            .auth-logo img {
                width: 150px !important;
                max-width: 150px;
                height: auto;
            }
            /* Tablet and Desktop (768px and up) - same as current 60px */
            @media (min-width: 768px) {
                .auth-logo img {
                    width: 180px !important;
                    max-width: 180px;
                }
            }
            
            /* Responsive logo styles for navbar (main app after login) - same as landing page */
            .navbar-logo img {
                width: 150px !important;
                max-width: 180px;
                height: auto;
            }
            /* Tablet (768px and up) */
            @media (min-width: 768px) {
                .navbar-logo img {
                    width: 130px !important;
                }
            }
            /* Desktop (992px and up) */
            @media (min-width: 992px) {
                .navbar-logo img {
                    width: 180px !important;
                }
            }
            /* Small mobile (max-width: 480px) */
            @media (max-width: 480px) {
                .navbar-logo img {
                    width: 130px !important;
                }
            }
        </style>

        @if (file_exists($customHtmlPath))
            @if ($content = file_get_contents($customHtmlPath))
                {!! $content !!}
            @endif
        @endif

        @if ($code = settings('analytics.tracking_code'))
            <!-- Google tag (gtag.js) -->
            <script
                async
                src="https://www.googletagmanager.com/gtag/js?id={{ settings('analytics.tracking_code') }}"
            ></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', "{{ settings('analytics.tracking_code') }}");
            </script>
        @endif

        @yield('head-end')
    </head>

    <body>
        <div id="root">{!! $ssrContent ?? '' !!}</div>

        @if (! isset($ssrContent))
            <noscript>
                You need to have javascript enabled in order to use
                <strong>{{ config('app.name') }}</strong>
                .
            </noscript>
        @endif

        @yield('body-end')
        
        <script>
            // Final cleanup - hide any Tidio elements that loaded after page load - COMPREHENSIVE
            (function() {
                var observer = new MutationObserver(function(mutations) {
                    var selectors = [
                        '[id*="tidio"]', '[id*="Tidio"]',
                        '[class*="tidio"]', '[class*="Tidio"]',
                        'iframe[src*="tidio"]', 'iframe[src*="code.tidio.co"]',
                        '#button-body',
                        '[id="button-body"]',
                        'button[id="button-body"]',
                        '[data-testid="widgetButtonBody"]',
                        'button[data-testid="widgetButtonBody"]',
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
                });
                if (document.body) {
                    observer.observe(document.body, {
                        childList: true,
                        subtree: true,
                        attributes: true,
                        attributeFilter: ['id', 'class', 'data-testid']
                    });
                }
            })();
        </script>
    </body>
</html>
