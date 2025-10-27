<div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="price_type" class="form-label">Preistyp</label>
            <select class="form-select" wire:model="price_ranges_id" name="price_ranges_id">
                @foreach($priceTypes as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 mb-3">
            <label for="currency" class="form-label">Währung</label>
            <select class="form-select" wire:model="currency" name="currency">
                @foreach($currencies as $code => $label)
                    <option value="{{ $code }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 mb-3">
            <label for="service_fee" class="form-label">Servicegebühr</label>
            <div class="input-group">
                <input type="number" 
                       class="form-control" 
                       wire:model="service_fee" 
                       name="service_fee" 
                       step="0.01" 
                       min="0"
                       placeholder="0,00">
                <span class="input-group-text">{{ $currency }}</span>
            </div>
        </div>
    </div>

    <div class="row">
        @if($price_ranges_id == 1)
            <!-- Preis pro Stunde -->
            <div class="col-md-6 mb-3">
                <label for="price_range_hour" class="form-label">Preis pro Stunde</label>
                <div class="input-group">
                    <input type="number" 
                           class="form-control" 
                           wire:model="price_range_hour" 
                           name="price_range_hour" 
                           step="0.01" 
                           min="0"
                           placeholder="0,00">
                    <span class="input-group-text">{{ $currency }}</span>
                </div>
            </div>
        @elseif($price_ranges_id == 2)
            <!-- Preis pro Tag -->
            <div class="col-md-6 mb-3">
                <label for="price_range_day" class="form-label">Preis pro Tag</label>
                <div class="input-group">
                    <input type="number" 
                           class="form-control" 
                           wire:model="price_range_day" 
                           name="price_range_day" 
                           step="0.01" 
                           min="0"
                           placeholder="0,00">
                    <span class="input-group-text">{{ $currency }}</span>
                </div>
            </div>
        @elseif($price_ranges_id == 3)
            <!-- Preis pro Auftritt/Einmalig -->
            <div class="col-md-6 mb-3">
                <label for="price_range_once" class="form-label">Preis pro Auftritt</label>
                <div class="input-group">
                    <input type="number" 
                           class="form-control" 
                           wire:model="price_range_once" 
                           name="price_range_once" 
                           step="0.01" 
                           min="0"
                           placeholder="0,00">
                    <span class="input-group-text">{{ $currency }}</span>
                </div>
            </div>
        @endif

        <div class="col-md-6 mb-3">
            <label class="form-label">Preisvorschau</label>
            <div class="alert alert-info">
                @if($price_ranges_id == 1 && $price_range_hour > 0)
                    <strong>{{ number_format($price_range_hour, 2, ',', '.') }} {{ $currency }}</strong> pro Stunde
                    @if($service_fee > 0)
                        <br><small>zzgl. {{ number_format($service_fee, 2, ',', '.') }} {{ $currency }} Servicegebühr</small>
                    @endif
                @elseif($price_ranges_id == 2 && $price_range_day > 0)
                    <strong>{{ number_format($price_range_day, 2, ',', '.') }} {{ $currency }}</strong> pro Tag
                    @if($service_fee > 0)
                        <br><small>zzgl. {{ number_format($service_fee, 2, ',', '.') }} {{ $currency }} Servicegebühr</small>
                    @endif
                @elseif($price_ranges_id == 3 && $price_range_once > 0)
                    <strong>{{ number_format($price_range_once, 2, ',', '.') }} {{ $currency }}</strong> pro Auftritt
                    @if($service_fee > 0)
                        <br><small>zzgl. {{ number_format($service_fee, 2, ',', '.') }} {{ $currency }} Servicegebühr</small>
                    @endif
                @else
                    <em>Preis auf Anfrage</em>
                @endif
            </div>
        </div>
    </div>

    <!-- Hidden inputs to ensure form data is submitted -->
    <input type="hidden" name="price_ranges_id" value="{{ $price_ranges_id }}">
    <input type="hidden" name="price_range_hour" value="{{ $price_range_hour }}">
    <input type="hidden" name="price_range_day" value="{{ $price_range_day }}">
    <input type="hidden" name="price_range_once" value="{{ $price_range_once }}">
    <input type="hidden" name="service_fee" value="{{ $service_fee }}">
    <input type="hidden" name="currency" value="{{ $currency }}">
</div>