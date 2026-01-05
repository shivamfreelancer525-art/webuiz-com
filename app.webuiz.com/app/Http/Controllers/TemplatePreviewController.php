<?php

namespace App\Http\Controllers;

use App\Services\TemplateLoader;
use Common\Core\AppUrl;

class TemplatePreviewController
{
    public function __construct(protected TemplateLoader $loader)
    {
    }

    public function __invoke(string $templateName, ?string $pageName = 'index')
    {
        // Ensure AppUrl is initialized before use
        app(AppUrl::class)->init();

        $name = slugify($templateName);
        $pageName = $pageName ?: 'index';

        if (!$this->loader->exists($name)) {
            abort(404);
        }

        $template = $this->loader->load($name, loadPages: true);

        foreach ($template['pages'] as $page) {
            if ($page['name'] === $pageName) {
                // Get the base path for assets - use absolute URL to ensure assets load correctly
                $appUrl = config('app.url');
                
                // Build the full base URL for the template
                // Static files are in public/builder/templates/, so they're accessible at /builder/templates/
                // Example: https://app.draggify.com/builder/templates/bootslander/
                // Don't use htmlBaseUri here as static files are always at root-relative paths
                $templateBasePath = rtrim($appUrl, '/') . '/builder/templates/' . $template['name'] . '/';

                // Remove any existing base tag first to avoid conflicts
                $page['html'] = preg_replace('/<base[^>]*>/i', '', $page['html']);

                // Insert <base> tag into html - handle <head> with or without attributes
                // Match: <head>, <head >, <head lang="en">, etc.
                if (preg_match('/<head\s*[^>]*>/i', $page['html'])) {
                    $page['html'] = preg_replace(
                        '/(<head\s*[^>]*>)/i',
                        "$1\n\t<base href=\"{$templateBasePath}\">",
                        $page['html'],
                        1,
                    );
                } else {
                    // If no <head> tag found, try to insert before <body> or at the start
                    if (preg_match('/<body[^>]*>/i', $page['html'])) {
                        $page['html'] = preg_replace(
                            '/(<body[^>]*>)/i',
                            "<head>\n\t<base href=\"{$templateBasePath}\">\n</head>\n$1",
                            $page['html'],
                            1,
                        );
                    } else {
                        // Insert at the very beginning if no body tag
                        $page['html'] = "<head>\n\t<base href=\"{$templateBasePath}\">\n</head>\n" . $page['html'];
                    }
                }

                // Fix for navigation links in preview mode: Prevent hash-based links from causing 404s
                // This intercepts links like href="#about", href="#contact" and handles them within the iframe
                $navigationFixScript = <<<'SCRIPT'
<script>
(function() {
    function fixNavigationLinks() {
        // Find all anchor links with hash-based hrefs (e.g., #about, #contact, #projects)
        var links = document.querySelectorAll("a[href^='#']");
        
        links.forEach(function(link) {
            // Skip if already processed or if it's an empty hash
            if (link.dataset.previewFixed === "true" || link.getAttribute("href") === "#") {
                return;
            }
            
            link.dataset.previewFixed = "true";
            
            // Prevent default navigation and handle smooth scrolling within iframe
            link.addEventListener("click", function(e) {
                var href = link.getAttribute("href");
                
                // Only handle hash links (starting with #)
                if (href && href.startsWith("#") && href.length > 1) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var targetId = href.substring(1); // Remove the #
                    var targetElement = document.getElementById(targetId) || document.querySelector("[name='" + targetId + "']");
                    
                    if (targetElement) {
                        // Smooth scroll to target element within iframe
                        targetElement.scrollIntoView({
                            behavior: "smooth",
                            block: "start"
                        });
                        
                        // Also trigger any existing scroll handlers (for templates with smooth scroll libraries)
                        if (typeof jQuery !== "undefined" && typeof jQuery.fn.scrollTo !== "undefined") {
                            jQuery("html, body").animate({
                                scrollTop: jQuery(targetElement).offset().top - 100
                            }, 500);
                        }
                    }
                }
            }, true); // Use capture phase to intercept early
        });
    }
    
    // Run immediately if DOM is ready, otherwise wait
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", fixNavigationLinks);
    } else {
        fixNavigationLinks();
    }
    
    // Also run after a short delay to catch dynamically added links
    setTimeout(fixNavigationLinks, 500);
})();
</script>
SCRIPT;

                // Fix for templates with preloader: Hide preloader immediately to prevent loading spinner
                // This is needed because window.onload might not fire correctly in iframes
                $preloaderFixScript = '';
                if (strpos($page['html'], 'id="preloader"') !== false || strpos($page['html'], "id='preloader'") !== false) {
                    // Inject script to hide preloader immediately after DOM loads
                    $preloaderFixScript = <<<'SCRIPT'
<script>
(function() {
    // Hide preloader immediately on DOMContentLoaded (faster than window.onload)
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", hidePreloader);
    } else {
        hidePreloader();
    }
    
    // Also hide on window.load as fallback
    window.addEventListener("load", hidePreloader);
    
    // Immediate fallback if already loaded
    setTimeout(hidePreloader, 100);
    
    function hidePreloader() {
        var preloader = document.getElementById("preloader");
        if (preloader) {
            // Aggressively hide the preloader
            preloader.style.display = "none";
            preloader.style.visibility = "hidden";
            preloader.style.opacity = "0";
            preloader.style.zIndex = "-1";
            preloader.style.pointerEvents = "none";
            // Also try jQuery if available
            if (typeof jQuery !== "undefined" && jQuery("#preloader").length) {
                jQuery("#preloader").fadeOut(0).remove();
            }
        }
    }
})();
</script>
SCRIPT;
                }
                
                // Combine both scripts
                $combinedScript = $navigationFixScript . $preloaderFixScript;
                
                // Insert scripts before closing </body> tag, or before first script tag, or at end of head
                if (preg_match('/<\/body>/i', $page['html'])) {
                    $page['html'] = preg_replace(
                        '/(<\/body>)/i',
                        $combinedScript . "\n$1",
                        $page['html'],
                        1,
                    );
                } else if (preg_match('/<script/i', $page['html'])) {
                    $page['html'] = preg_replace(
                        '/(<script[^>]*>)/i',
                        $combinedScript . "\n$1",
                        $page['html'],
                        1,
                    );
                } else {
                    // Insert before closing </head> or at end of head
                    if (preg_match('/<\/head>/i', $page['html'])) {
                        $page['html'] = preg_replace(
                            '/(<\/head>)/i',
                            $combinedScript . "\n$1",
                            $page['html'],
                            1,
                        );
                    }
                }

                return $page['html'];
            }
        }

        abort(404);
    }
}
