<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Rental;

class RentalCard extends Component
{
    /**
     * The rental instance.
     *
     * @var \App\Models\Rental
     */
    public $rental;

    /**
     * Create a new component instance.
     *
     * @param  \App\Models\Rental  $rental
     * @return void
     */
    public function __construct(Rental $rental)
    {
        $this->rental = $rental;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.rental-card');
    }
}
