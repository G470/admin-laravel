<!-- Footer: Start -->
<footer class="landing-footer bg-body footer-text mt-6">
  <div class="footer-top position-relative overflow-hidden z-1">
    <img src="{{asset('assets/img/front-pages/backgrounds/footer-bg-'.$configData['style'].'.png')}}" alt="footer bg" class="footer-bg banner-bg-img z-n1" data-app-light-img="front-pages/backgrounds/footer-bg-light.png" data-app-dark-img="front-pages/backgrounds/footer-bg-dark.png" />
    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-5">
          <a href="{{ route('home') }}" class="app-brand-link mb-4">
            <span class="app-brand-logo demo">
              <img src="{{ asset('assets/img/branding/inlando-logo.svg') }}" alt="Inlando Logo" class="footer-brand-logo" style="height: 50px; width: auto;">
            </span>
          </a>
          <p class="footer-text footer-logo-description mb-4">
            Deine Plattform für Vermietungen aller Art. Einfach, schnell und sicher.
          </p>
          <form class="footer-form">
            <label for="footer-email" class="small">Newsletter abonnieren</label>
            <div class="d-flex mt-1">
              <input type="email" class="form-control rounded-0 rounded-start-bottom rounded-start-top" id="footer-email" placeholder="Deine E-Mail" />
              <button type="submit" class="btn btn-primary shadow-none rounded-0 rounded-end-bottom rounded-end-top waves-effect">
                Abonnieren
              </button>
            </div>
          </form>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <h6 class="footer-title mb-4">Links</h6>
          <ul class="list-unstyled">
            <li class="mb-3">
              <a href="#" class="footer-link">AGB</a>
            </li>
            <li class="mb-3">
              <a href="#" class="footer-link">Datenschutz</a>
            </li>
            <li class="mb-3">
              <a href="#" class="footer-link">Kontakt</a>
            </li>
            <li class="mb-3">
              <a href="#" class="footer-link">Impressum</a>
            </li>
          </ul>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <h6 class="footer-title mb-4">Kategorien</h6>
          <ul class="list-unstyled">
            <li class="mb-3">
              <a href="#" class="footer-link">Wohnmobile</a>
            </li>
            <li class="mb-3">
              <a href="#" class="footer-link">Baumaschinen</a>
            </li>
            <li class="mb-3">
              <a href="#" class="footer-link">Eventartikel</a>
            </li>
            <li class="mb-3">
              <a href="#" class="footer-link">Werkzeug</a>
            </li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <h6 class="footer-title mb-4">Sprache</h6>
          <div class="dropdown mb-4">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              Deutsch
            </button>
            <ul class="dropdown-menu" aria-labelledby="languageDropdown">
              <li><a class="dropdown-item" href="#">Deutsch</a></li>
              <li><a class="dropdown-item" href="#">English</a></li>
              <li><a class="dropdown-item" href="#">Français</a></li>
            </ul>
          </div>
          <h6 class="footer-title mb-3">Social Media</h6>
          <div class="d-flex gap-2">
            <a href="#" class="btn btn-icon btn-sm btn-text-primary btn-outline-primary rounded-pill waves-effect">
              <i class="tf-icons ti ti-brand-facebook"></i>
            </a>
            <a href="#" class="btn btn-icon btn-sm btn-text-primary btn-outline-primary rounded-pill waves-effect">
              <i class="tf-icons ti ti-brand-instagram"></i>
            </a>
            <a href="#" class="btn btn-icon btn-sm btn-text-primary btn-outline-primary rounded-pill waves-effect">
              <i class="tf-icons ti ti-brand-twitter"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom py-3 py-md-5">
    <div class="container d-flex flex-wrap justify-content-between flex-md-row flex-column text-center text-md-start">
      <div class="mb-2 mb-md-0">
        <span class="footer-bottom-text">© {{ date('Y') }} Inlando. Alle Rechte vorbehalten.</span>
      </div>
      <div>
        <a href="#" class="me-3 footer-link">AGB</a>
        <a href="#" class="me-3 footer-link">Datenschutz</a>
        <a href="#" class="footer-link">Impressum</a>
      </div>
    </div>
  </div>
</footer>
<!-- Footer: End -->
