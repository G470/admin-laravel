<footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl">
        <div class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
            <div>
                © {{ date('Y') }} <a href="{{ config('app.url') }}" target="_blank" class="fw-semibold">{{ config('app.name') }}</a>
            </div>
            <div>
                <a href="javascript:void(0)" class="footer-link me-4">Help</a>
                <a href="javascript:void(0)" class="footer-link me-4">Contact</a>
                <a href="javascript:void(0)" class="footer-link">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>
