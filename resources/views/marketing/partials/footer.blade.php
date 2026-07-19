@php
    $brand = config('marketing.name');
    $contact = config('marketing.contact');
    $social = config('marketing.social');
    $navItems = config('marketing.nav', []);
@endphp

<footer id="footer" class="footer light-background">
    <div class="footer-top">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6 footer-about">
                    <a href="{{ route('marketing.home') }}" class="logo d-flex align-items-center">
                        <span class="sitename">{{ $brand }}</span>
                    </a>
                    <div class="footer-contact pt-3">
                        <p>{{ $contact['address'] }}</p>
                        <p class="mt-3"><strong>Phone:</strong> <span>{{ $contact['phone'] }}</span></p>
                        <p><strong>Email:</strong> <span>{{ $contact['email'] }}</span></p>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 footer-links">
                    <h4>Useful Links</h4>
                    <ul>
                        <li><a href="{{ route('marketing.home') }}">Home</a></li>
                        @foreach ($navItems as $item)
                            <li><a href="{{ route($item['route']) }}">{{ $item['label'] }}</a></li>
                        @endforeach
                        <li><a href="{{ route('login') }}">Log in</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-3 footer-links">
                    <h4>Product</h4>
                    <ul>
                        <li><a href="{{ route('marketing.features') }}">Features</a></li>
                        <li><a href="{{ route('marketing.pricing') }}">Pricing</a></li>
                        <li><a href="{{ route('marketing.contact', ['intent' => 'demo']) }}">Book a demo</a></li>
                        @if (Route::has('register'))
                            <li><a href="{{ route('register') }}">Start free trial</a></li>
                        @endif
                    </ul>
                </div>

                <div class="col-lg-3 col-md-3 footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="{{ route('marketing.about') }}">About</a></li>
                        <li><a href="{{ route('marketing.contact') }}">Contact</a></li>
                        <li><a href="{{ $social['linkedin'] ?? '#' }}">LinkedIn</a></li>
                        <li><a href="{{ $social['twitter'] ?? '#' }}">Twitter / X</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="copyright text-center">
        <div class="container d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center">
            <div class="d-flex flex-column align-items-center align-items-lg-start">
                <div>
                    © {{ date('Y') }} <strong><span>{{ $brand }}</span></strong>. All Rights Reserved
                </div>
                <div class="credits">
                    Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
                </div>
            </div>

            <div class="social-links order-first order-lg-last mb-3 mb-lg-0">
                <a href="{{ $social['twitter'] ?? '#' }}" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                <a href="{{ $social['linkedin'] ?? '#' }}" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                <a href="{{ $social['github'] ?? '#' }}" aria-label="GitHub"><i class="bi bi-github"></i></a>
            </div>
        </div>
    </div>
</footer>
