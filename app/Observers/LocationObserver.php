<?php

namespace App\Observers;

use App\Models\Location;
use App\Services\VendorMembershipService;

class LocationObserver
{
    public function created(Location $location)
    {
        $this->updateVendorSubscription($location->vendor);
    }

    public function updated(Location $location)
    {
        $this->updateVendorSubscription($location->vendor);
    }

    public function deleted(Location $location)
    {
        $this->updateVendorSubscription($location->vendor);
    }

    private function updateVendorSubscription($vendor)
    {
        // Update subscription pricing when locations change
        VendorMembershipService::createOrUpdateSubscription($vendor);
    }
}