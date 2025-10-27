@extends('layouts/contentNavbarLayout')

@section('title', 'Payment Provider API Keys')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/admin-payment-providers.js'])
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-credit-card me-2"></i>
                        Payment Provider API Keys
                    </h5>
                    <div class="card-header-elements">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="test-all-connections">
                            <i class="ti ti-plug-connected me-1"></i>
                            Test All Connections
                        </button>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible m-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible m-3" role="alert">
                        <strong>Validation Error:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card-body">
                    <form action="{{ route('admin.payment-providers.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Navigation Tabs --}}
                        <ul class="nav nav-pills mb-4" id="payment-provider-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="paypal-tab" data-bs-toggle="pill"
                                    data-bs-target="#paypal" type="button" role="tab">
                                    <i class="ti ti-brand-paypal me-2"></i>PayPal Configuration
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="stripe-tab" data-bs-toggle="pill" data-bs-target="#stripe"
                                    type="button" role="tab">
                                    <i class="ti ti-credit-card me-2"></i>Stripe Configuration
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pricing-tab" data-bs-toggle="pill" data-bs-target="#pricing"
                                    type="button" role="tab">
                                    <i class="ti ti-currency-euro me-2"></i>Subscription Pricing
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="payment-provider-content">
                            {{-- PayPal Configuration --}}
                            <div class="tab-pane fade show active" id="paypal" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h6 class="text-muted mb-1">PayPal API Configuration</h6>
                                        <p class="small text-muted mb-0">Configure PayPal API keys for production and development environments.</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2 small text-muted">Active Environment:</span>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input environment-toggle" type="checkbox" 
                                                   id="paypal-environment-toggle" 
                                                   data-provider="paypal"
                                                   {{ isset($activeEnvironments['paypal']) && $activeEnvironments['paypal'] === 'development' ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="paypal-environment-toggle">
                                                <span class="environment-label-production">Production</span>
                                                <span class="environment-label-development d-none">Development</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    {{-- Production PayPal --}}
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">
                                                    <i class="ti ti-world me-2 text-success"></i>Production Environment
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="paypal_client_id_production" class="form-label">Client
                                                        ID</label>
                                                    <input type="text" class="form-control" id="paypal_client_id_production"
                                                        name="paypal_client_id_production"
                                                        value="{{ old('paypal_client_id_production', $paypalProduction['client_id']) }}"
                                                        placeholder="Enter PayPal Client ID">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="paypal_client_secret_production" class="form-label">Client
                                                        Secret</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control"
                                                            id="paypal_client_secret_production"
                                                            name="paypal_client_secret_production"
                                                            placeholder="Enter PayPal Client Secret">
                                                        <button class="btn btn-outline-secondary toggle-password"
                                                            type="button" data-target="paypal_client_secret_production">
                                                            <i class="ti ti-eye"></i>
                                                        </button>
                                                    </div>
                                                    @if($paypalProduction['client_secret'])
                                                        <small class="text-muted">Current:
                                                            {{ $paypalProduction['client_secret'] }}</small>
                                                    @endif
                                                </div>
                                                <div class="mb-3">
                                                    <label for="paypal_webhook_url_production" class="form-label">Webhook
                                                        URL</label>
                                                    <input type="url" class="form-control"
                                                        id="paypal_webhook_url_production"
                                                        name="paypal_webhook_url_production"
                                                        value="{{ old('paypal_webhook_url_production', $paypalProduction['webhook_url']) }}"
                                                        placeholder="https://yourdomain.com/webhooks/paypal">
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary test-connection"
                                                    data-provider="paypal" data-environment="production">
                                                    <i class="ti ti-plug-connected me-1"></i>Test Connection
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Development PayPal --}}
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">
                                                    <i class="ti ti-flask me-2 text-warning"></i>Development Environment
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="paypal_client_id_development" class="form-label">Client ID
                                                        (Sandbox)</label>
                                                    <input type="text" class="form-control"
                                                        id="paypal_client_id_development"
                                                        name="paypal_client_id_development"
                                                        value="{{ old('paypal_client_id_development', $paypalDevelopment['client_id']) }}"
                                                        placeholder="Enter PayPal Sandbox Client ID">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="paypal_client_secret_development" class="form-label">Client
                                                        Secret (Sandbox)</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control"
                                                            id="paypal_client_secret_development"
                                                            name="paypal_client_secret_development"
                                                            placeholder="Enter PayPal Sandbox Client Secret">
                                                        <button class="btn btn-outline-secondary toggle-password"
                                                            type="button" data-target="paypal_client_secret_development">
                                                            <i class="ti ti-eye"></i>
                                                        </button>
                                                    </div>
                                                    @if($paypalDevelopment['client_secret'])
                                                        <small class="text-muted">Current:
                                                            {{ $paypalDevelopment['client_secret'] }}</small>
                                                    @endif
                                                </div>
                                                <div class="mb-3">
                                                    <label for="paypal_webhook_url_development" class="form-label">Webhook
                                                        URL (Sandbox)</label>
                                                    <input type="url" class="form-control"
                                                        id="paypal_webhook_url_development"
                                                        name="paypal_webhook_url_development"
                                                        value="{{ old('paypal_webhook_url_development', $paypalDevelopment['webhook_url']) }}"
                                                        placeholder="https://yourdomain.test/webhooks/paypal">
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary test-connection"
                                                    data-provider="paypal" data-environment="development">
                                                    <i class="ti ti-plug-connected me-1"></i>Test Connection
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Stripe Configuration --}}
                            <div class="tab-pane fade" id="stripe" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h6 class="text-muted mb-1">Stripe API Configuration</h6>
                                        <p class="small text-muted mb-0">Configure Stripe API keys for production and development environments.</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2 small text-muted">Active Environment:</span>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input environment-toggle" type="checkbox" 
                                                   id="stripe-environment-toggle" 
                                                   data-provider="stripe"
                                                   {{ isset($activeEnvironments['stripe']) && $activeEnvironments['stripe'] === 'development' ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="stripe-environment-toggle">
                                                <span class="environment-label-production">Production</span>
                                                <span class="environment-label-development d-none">Development</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    {{-- Production Stripe --}}
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">
                                                    <i class="ti ti-world me-2 text-success"></i>Production Environment
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="stripe_publishable_key_production"
                                                        class="form-label">Publishable Key</label>
                                                    <input type="text" class="form-control"
                                                        id="stripe_publishable_key_production"
                                                        name="stripe_publishable_key_production"
                                                        value="{{ old('stripe_publishable_key_production', $stripeProduction['publishable_key']) }}"
                                                        placeholder="pk_live_...">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="stripe_secret_key_production" class="form-label">Secret
                                                        Key</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control"
                                                            id="stripe_secret_key_production"
                                                            name="stripe_secret_key_production" placeholder="sk_live_...">
                                                        <button class="btn btn-outline-secondary toggle-password"
                                                            type="button" data-target="stripe_secret_key_production">
                                                            <i class="ti ti-eye"></i>
                                                        </button>
                                                    </div>
                                                    @if($stripeProduction['secret_key'])
                                                        <small class="text-muted">Current:
                                                            {{ $stripeProduction['secret_key'] }}</small>
                                                    @endif
                                                </div>
                                                <div class="mb-3">
                                                    <label for="stripe_webhook_secret_production" class="form-label">Webhook
                                                        Secret</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control"
                                                            id="stripe_webhook_secret_production"
                                                            name="stripe_webhook_secret_production" placeholder="whsec_...">
                                                        <button class="btn btn-outline-secondary toggle-password"
                                                            type="button" data-target="stripe_webhook_secret_production">
                                                            <i class="ti ti-eye"></i>
                                                        </button>
                                                    </div>
                                                    @if($stripeProduction['webhook_secret'])
                                                        <small class="text-muted">Current:
                                                            {{ $stripeProduction['webhook_secret'] }}</small>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary test-connection"
                                                    data-provider="stripe" data-environment="production">
                                                    <i class="ti ti-plug-connected me-1"></i>Test Connection
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Development Stripe --}}
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">
                                                    <i class="ti ti-flask me-2 text-warning"></i>Development Environment
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="stripe_publishable_key_development"
                                                        class="form-label">Publishable Key (Test)</label>
                                                    <input type="text" class="form-control"
                                                        id="stripe_publishable_key_development"
                                                        name="stripe_publishable_key_development"
                                                        value="{{ old('stripe_publishable_key_development', $stripeDevelopment['publishable_key']) }}"
                                                        placeholder="pk_test_...">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="stripe_secret_key_development" class="form-label">Secret Key
                                                        (Test)</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control"
                                                            id="stripe_secret_key_development"
                                                            name="stripe_secret_key_development" placeholder="sk_test_...">
                                                        <button class="btn btn-outline-secondary toggle-password"
                                                            type="button" data-target="stripe_secret_key_development">
                                                            <i class="ti ti-eye"></i>
                                                        </button>
                                                    </div>
                                                    @if($stripeDevelopment['secret_key'])
                                                        <small class="text-muted">Current:
                                                            {{ $stripeDevelopment['secret_key'] }}</small>
                                                    @endif
                                                </div>
                                                <div class="mb-3">
                                                    <label for="stripe_webhook_secret_development"
                                                        class="form-label">Webhook Secret (Test)</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control"
                                                            id="stripe_webhook_secret_development"
                                                            name="stripe_webhook_secret_development"
                                                            placeholder="whsec_...">
                                                        <button class="btn btn-outline-secondary toggle-password"
                                                            type="button" data-target="stripe_webhook_secret_development">
                                                            <i class="ti ti-eye"></i>
                                                        </button>
                                                    </div>
                                                    @if($stripeDevelopment['webhook_secret'])
                                                        <small class="text-muted">Current:
                                                            {{ $stripeDevelopment['webhook_secret'] }}</small>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary test-connection"
                                                    data-provider="stripe" data-environment="development">
                                                    <i class="ti ti-plug-connected me-1"></i>Test Connection
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Subscription Pricing --}}
                            <div class="tab-pane fade" id="pricing" role="tabpanel">
                                <h6 class="text-muted">Subscription Pricing Configuration</h6>
                                <p class="small text-muted mb-4">Configure dynamic pricing based on vendor usage: rental
                                    submissions, categories, and locations.</p>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">
                                                    <i class="ti ti-currency-euro me-2"></i>Base Pricing
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="base_monthly_fee" class="form-label">Base Monthly Fee
                                                        (€)</label>
                                                    <input type="number" step="0.01" min="0" max="999.99"
                                                        class="form-control" id="base_monthly_fee" name="base_monthly_fee"
                                                        value="{{ old('base_monthly_fee', $pricingRules['base_monthly_fee']) }}"
                                                        required>
                                                    <small class="text-muted">Fixed monthly base fee charged to all
                                                        vendors</small>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="minimum_monthly_fee" class="form-label">Minimum Monthly Fee
                                                        (€)</label>
                                                    <input type="number" step="0.01" min="0" max="999.99"
                                                        class="form-control" id="minimum_monthly_fee"
                                                        name="minimum_monthly_fee"
                                                        value="{{ old('minimum_monthly_fee', $pricingRules['minimum_monthly_fee']) }}"
                                                        required>
                                                    <small class="text-muted">Minimum amount charged regardless of
                                                        usage</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">
                                                    <i class="ti ti-percentage me-2"></i>Variable Pricing
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="price_per_rental" class="form-label">Price per Rental
                                                        (€)</label>
                                                    <input type="number" step="0.01" min="0" max="99.99"
                                                        class="form-control" id="price_per_rental" name="price_per_rental"
                                                        value="{{ old('price_per_rental', $pricingRules['price_per_rental']) }}"
                                                        required>
                                                    <small class="text-muted">Fee per rental submission</small>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="price_per_category" class="form-label">Price per Category
                                                        (€)</label>
                                                    <input type="number" step="0.01" min="0" max="99.99"
                                                        class="form-control" id="price_per_category"
                                                        name="price_per_category"
                                                        value="{{ old('price_per_category', $pricingRules['price_per_category']) }}"
                                                        required>
                                                    <small class="text-muted">Fee per unique category used</small>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="price_per_location" class="form-label">Price per Location
                                                        (€)</label>
                                                    <input type="number" step="0.01" min="0" max="99.99"
                                                        class="form-control" id="price_per_location"
                                                        name="price_per_location"
                                                        value="{{ old('price_per_location', $pricingRules['price_per_location']) }}"
                                                        required>
                                                    <small class="text-muted">Fee per location managed</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">
                                                    <i class="ti ti-discount me-2"></i>Volume Discounts
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="volume_discount_threshold" class="form-label">Discount
                                                        Threshold (€)</label>
                                                    <input type="number" step="0.01" min="0" max="9999.99"
                                                        class="form-control" id="volume_discount_threshold"
                                                        name="volume_discount_threshold"
                                                        value="{{ old('volume_discount_threshold', $pricingRules['volume_discount_threshold']) }}"
                                                        required>
                                                    <small class="text-muted">Monthly amount required to qualify for volume
                                                        discount</small>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="volume_discount_percentage" class="form-label">Discount
                                                        Percentage (%)</label>
                                                    <input type="number" step="0.1" min="0" max="100" class="form-control"
                                                        id="volume_discount_percentage" name="volume_discount_percentage"
                                                        value="{{ old('volume_discount_percentage', $pricingRules['volume_discount_percentage']) }}"
                                                        required>
                                                    <small class="text-muted">Percentage discount applied to qualifying
                                                        vendors</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">
                                                    <i class="ti ti-calculator me-2"></i>Pricing Preview
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="pricing-preview">
                                                    <p class="small text-muted">Example for vendor with:</p>
                                                    <ul class="small text-muted">
                                                        <li>10 rental submissions</li>
                                                        <li>3 categories</li>
                                                        <li>2 locations</li>
                                                    </ul>
                                                    <hr>
                                                    <div id="pricing-calculation" class="small">
                                                        <div class="d-flex justify-content-between">
                                                            <span>Base fee:</span>
                                                            <span id="calc-base">€29.99</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span>Rentals (10 × €2.50):</span>
                                                            <span id="calc-rentals">€25.00</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span>Categories (3 × €5.00):</span>
                                                            <span id="calc-categories">€15.00</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span>Locations (2 × €3.00):</span>
                                                            <span id="calc-locations">€6.00</span>
                                                        </div>
                                                        <hr>
                                                        <div class="d-flex justify-content-between fw-bold">
                                                            <span>Total:</span>
                                                            <span id="calc-total">€75.99</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Save Button --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-2"></i>
                                        Save Configuration
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection