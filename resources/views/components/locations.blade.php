<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Standorte auswählen</h4>
        </div>
        <div class="card-body">
            <div class="space-y-4">
                @foreach($countries as $countryIndex => $country)
                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h5 class="font-medium">{{ $country['name'] }}</h5>
                            <button type="button" wire:click="selectAllInCountry({{ $countryIndex }})"
                                class="text-sm text-primary hover:text-primary-dark">
                                Alle auswählen
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($country['locations'] as $location)
                                <button type="button" wire:click="toggleLocation({{ $countryIndex }}, {{ $location['id'] }})"
                                    class="flex items-center justify-between p-2 rounded-lg border hover:border-primary transition-colors {{ in_array('country-' . $countryIndex . '-location-' . $location['id'], $selectedLocations) ? 'border-primary bg-primary/5' : 'border-gray-200' }}">
                                    <span>{{ $location['name'] }}</span>
                                    @if(in_array('country-' . $countryIndex . '-location-' . $location['id'], $selectedLocations))
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>