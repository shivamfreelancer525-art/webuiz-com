@section('title')
    Contact
@endsection
@include('header')
<section class="hero-area-three bgs-cover bgc-lighter pt-210 rpt-150 pb-100"
    style="background-image: url(assets/images/hero/hero-three-bg.png);">
    <div class="container">
        <div class="banner-inner pt-70 rpt-60 text-dark">
            <h1 class="page-title" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">Contact Us
            </h1>
        </div>
    </div>
</section>
<section class="contact-page py-130 rpy-100">
    <div class="container">
        <div class="row gap-100 align-items-center">
            <div class="col-lg-5">
                <div class="contact-info-part">
                    <div class="section-title mb-50 aos-init aos-animate" data-aos="fade-up" data-aos-duration="1500"
                        data-aos-offset="50">
                        <h2>Feel Free to Contact Us, Get In Touch</h2>
                        <p>We're here to assist you in any way we can. Whether you have questions, feedback, or just
                            want to say hello</p>
                    </div>
                    <div class="contact-info-item style-two aos-init" data-aos="fade-up" data-aos-delay="50"
                        data-aos-duration="1500" data-aos-offset="50">
                        <div class="icon">
                            <i class="fal fa-map-marker-alt"></i>
                        </div>
                        <div class="content">
                            <span class="title">Live Chat</span>
                            <span class="text">55 Main Street, 2nd block Melbourne, Australia</span>
                        </div>
                    </div>
                    <div class="contact-info-item style-two aos-init" data-aos="fade-up" data-aos-delay="100"
                        data-aos-duration="1500" data-aos-offset="50">
                        <div class="icon">
                            <i class="far fa-envelope-open-text"></i>
                        </div>
                        <div class="content">
                            <span class="title">Email Address</span>
                            <span class="text">
                                <a href="mailto:contact@webuiz.com">contact@webuiz.com</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="contact-form br-10 bgc-lighter rmt-60 aos-init aos-animate" name="contact-form"
                    data-aos="fade-left" data-aos-duration="1500" data-aos-offset="50">
                    <form class="needs-validation" action="/contact" method="post" novalidate>

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @csrf
                        <img class="shape-one" src="assets/images/shapes/star-yellow-shape.png" alt="Star Shape">
                        <img class="shape-two" src="assets/images/shapes/star-black-shape.png" alt="Star Shape">
                        <h5>Send Us Message</h5>
                        <p>Questions or you would just like to say hello, contact us.</p>
                        <div class="row mt-30">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        placeholder="Somaia D. Silva" required>
                                    <div class="invalid-feedback">Please enter your Name.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email" class="form-control"
                                        placeholder="example@gmail.com" required>
                                    <div class="invalid-feedback">Please enter your email.</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">

                                    <label for="subject">Subject <span class="text-danger">*</span></label>
                                    <input type="text" id="subject" name="subject" class="form-control"
                                        placeholder="I like to discussed" required>
                                    <div class="invalid-feedback">Please enter your subject.</div>

                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="message">Message <span class="text-danger">*</span></label>
                                    <textarea name="message" id="message" class="form-control" rows="4" placeholder="Write Message" required></textarea>
                                    <div class="invalid-feedback">Please enter your Message.</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <button type="submit" class="theme-btn disabled"
                                        style="pointer-events: all; cursor: pointer;">Send Us Message <i
                                            class="far fa-arrow-right"></i></button>
                                    <div id="msgSubmit" class="hidden"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@include('footer')
