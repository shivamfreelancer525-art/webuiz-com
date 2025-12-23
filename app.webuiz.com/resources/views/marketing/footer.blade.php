 <footer class="main-footer footer-three pt-100 rel z-1 bgc-navyblue">
     <div class="container">
         <div class="for-middle-border pb-50">
             <div class="row justify-content-between">
                 <div class="col-xl-6 col-lg-7">
                     <div class="footer-widget widget-about" data-aos="fade-up" data-aos-duration="1500"
                         data-aos-offset="50">
                        <div class="section-title text-white">
                            <h2>Create Stunning Websites Effortlessly</h2>
                            {{-- Disabled: No credit card requirement it's free for a month --}}
                            {{-- <p>No credit card requirement it's free for a month</p> --}}
                        </div>
                         <div class="footer-btns">
                             <a href="{{ url('/login') }}" class="theme-btn style-three">Login</a>
                             <a href="{{ url('/register') }}" class="theme-btn">Register <i
                                     class="far fa-arrow-right"></i></a>
                         </div>
                     </div>
                 </div>
                 <div class="col-lg-2 col-6 ms-lg-auto">
                     <div class="footer-widget widget-links" data-aos="fade-up" data-aos-delay="100"
                         data-aos-duration="1500" data-aos-offset="50">
                         <h6 class="footer-title">Quick Link</h6>
                         <ul>
                             <li><a href="/">Home</a></li>
                             <li><a href="/pricing">Pricing</a></li>
                             <li><a href="/faq">FAQ</a></li>
                             <li><a href="/features">Features</a></li>
                             <li><a href="{{ url('/login') }}">Login</a></li>
                             <li><a href="{{ url('/register') }}">Register</a></li>
                         </ul>
                     </div>
                 </div>
                 <div class="col-lg-2 col-6">
                     <div class="footer-widget widget-links" data-aos="fade-up" data-aos-delay="150"
                         data-aos-duration="1500" data-aos-offset="50">
                         <h6 class="footer-title">Our Policies</h6>
                         <ul>
                            <li><a href="{{ url('/terms-and-conditions') }}">Terms & Conditions</a></li>
                            <li><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></li>
                             <li><a href="/gdpr">GDPR</a></li>
                             <li><a href="/cookie-policy">Cookie Policy</a></li>
                             <li><a href="/refund-policy">Refund Policy</a></li>
                             <li><a href="/about">About Us</a></li>
                             <li><a href="/contact">Contact Us</a></li>
                         </ul>
                     </div>
                 </div>
             </div>
         </div>
         <div class="footer-bottom py-15">
             <div class="row align-items-center">
                 <div class="col-xl-4 col-lg-6">
                     <div class="copyright-text pt-10 text-lg-start text-center" data-aos="fade-left"
                         data-aos-duration="1500" data-aos-offset="50">
                         <p>Copyright © 2025, Draggify.com. All Rights Reserved.</p>
                     </div>
                 </div>
                 <div class="col-xl-8 col-lg-6">
                     <div class="footer-bottom-logo text-lg-end text-center" data-aos="fade-right"
                         data-aos-duration="1500" data-aos-offset="50">
                         <a href="/"><img src="{{ asset('assets/images/logos/draggify_white.png') }}" alt="Logo" style="width:180px;"></a>
                     </div>
                 </div>
             </div>
             <button class="scroll-top scroll-to-target" data-target="html"><span
                     class="far fa-angle-double-up"></span></button>
         </div>
     </div>
 </footer>
 </div>
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
<script>
    // Final cleanup: Remove any Tidio elements that loaded - COMPREHENSIVE
    (function() {
        var observer = new MutationObserver(function() {
            var selectors = [
                '[id*="tidio"]', '[id*="Tidio"]',
                '[class*="tidio"]', '[class*="Tidio"]',
                'iframe[src*="tidio"]', 'iframe[src*="code.tidio.co"]',
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

