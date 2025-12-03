@section('title')
    About
@endsection
@include('header')
<section class="hero-area-three bgs-cover bgc-lighter pt-210 rpt-150 pb-100"
    style="background-image: url(assets/images/hero/hero-three-bg.png);">
    <div class="container">
        <div class="banner-inner pt-70 rpt-60 text-dark">
            <h1 class="page-title" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">About Us
            </h1>
        </div>
    </div>
</section>
<section class="about-area py-90 rpy-60">
    <div class="container">
        <div class="row gap-90 align-items-center">
            <div class="col-lg-6">
                <div class="about-images my-40 aos-init aos-animate" data-aos="fade-left" data-aos-duration="1500"
                    data-aos-offset="50">
                    <img src="assets/images/about/about-page.png" alt="About">
                    <div class="about-over">
                        <img src="assets/images/about/about2.png" alt="About">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content mt-40 rmt-15 aos-init aos-animate" data-aos="fade-right"
                    data-aos-duration="1500" data-aos-offset="50">
                    <div class="section-title mb-30">
                        <h2>Who we are?</h2>
                    </div>
                    <p>At Webuiz, we are dedicated to empowering businesses of all sizes to create a strong online
                        presence effortlessly. Our innovative website builder is designed to be user-friendly, fast, and
                        highly customizable, allowing you to build stunning, responsive websites without the need for
                        coding.</p>
                    <div class="row pt-30">
                        <div class="col-sm-12">
                            <div class="counter-item counter-text-wrap counted">
                                <h5 class="counter-title">Our Mission</h5>
                                <div class="text">We believe that every business deserves a website that reflects its
                                    unique identity and goals. Our mission is to provide the tools and support you need
                                    to create a website that not only looks great but also performs exceptionally well
                                    in terms of speed, SEO, and user experience.</div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="counter-item counter-text-wrap counted">
                                <h5 class="counter-title">Our Commitment</h5>
                                <div class="text">We are committed to continuous improvement, ensuring that our
                                    platform
                                    stays at the forefront of technology and design trends. Whether you're a small
                                    business or a large enterprise, Webuiz is here to help you achieve your online
                                    objectives with ease and efficiency.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('footer')
