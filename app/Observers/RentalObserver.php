<?php

namespace App\Observers;

use App\Models\Rental;
use App\Services\VendorMembershipService;

class RentalObserver
{
    public function created(Rental $rental)
    {
        $this->updateVendorSubscription($rental->vendor);
    }

    public function updated(Rental $rental)
    {
        $this->updateVendorSubscription($rental->vendor);
    }

    public function deleted(Rental $rental)
    {
        $this->updateVendorSubscription($rental->vendor);
    }

    private function updateVendorSubscription($vendor)
    {
        // Update subscription pricing when rentals change
        VendorMembershipService::createOrUpdateSubscription($vendor);
    }
}