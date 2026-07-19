@php
    $contact = config('marketing.contact');
    $brand = config('marketing.name');
    $isDemo = ($intent ?? null) === 'demo';
@endphp

<x-marketing-layout
    title="Contact"
    :description="$isDemo ? 'Book a demo of Algos CRM with our team.' : 'Contact the Algos team about demos, trials, and partnerships.'"
    body-class="starter-page-page"
>
    @include('marketing.partials.page-title', [
        'title' => $isDemo ? 'Book a demo' : 'Contact',
        'subtitle' => $isDemo ? 'Talk with our team about a guided walkthrough.' : 'Talk with our team',
    ])

    <section id="contact" class="contact section">
        <div class="container section-title" data-aos="fade-up">
            <h2>{{ $isDemo ? 'Book a demo' : 'Talk with our team' }}</h2>
            <p>
                {{ $isDemo
                    ? 'Tell us about your team and we’ll schedule a walkthrough of the Algos workspace.'
                    : 'Questions about Algos, onboarding, or Enterprise plans? Send a message—we usually reply within one business day.' }}
            </p>
        </div>

        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row">
                <div class="col-lg-6 mb-5" data-aos="fade-right" data-aos-delay="200">
                    <div class="contact-info-section">
                        <div class="info-header">
                            <h3>Connect With Us</h3>
                            <p>Reach the {{ $brand }} team for demos, trials, and partnership questions.</p>
                        </div>

                        <div class="contact-info-grid">
                            <div class="info-item" data-aos="zoom-in" data-aos-delay="250">
                                <div class="info-icon">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div class="info-content">
                                    <h5>Visit Our Office</h5>
                                    <p>{{ $contact['address'] }}</p>
                                </div>
                            </div>

                            <div class="info-item" data-aos="zoom-in" data-aos-delay="300">
                                <div class="info-icon">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                                <div class="info-content">
                                    <h5>Send Email</h5>
                                    <p>{{ $contact['email'] }}</p>
                                </div>
                            </div>

                            <div class="info-item" data-aos="zoom-in" data-aos-delay="350">
                                <div class="info-icon">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                                <div class="info-content">
                                    <h5>Call Direct</h5>
                                    <p>{{ $contact['phone'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-marketing.map-placeholder />
                        </div>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                    <div class="contact-form-wrapper">
                        <div class="form-header">
                            <h3>{{ $isDemo ? 'Request a demo' : 'Send Us Message' }}</h3>
                            <p>
                                {{ $isDemo
                                    ? 'Share a few details and we’ll book a demo of Algos.'
                                    : 'All fields marked required help us route your note to the right person.' }}
                            </p>
                        </div>

                        @include('marketing.partials.contact-form', ['intent' => $intent ?? null])
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-marketing-layout>
