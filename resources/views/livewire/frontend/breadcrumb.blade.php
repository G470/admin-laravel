<div>
    <nav aria-label="breadcrumb" class="breadcrumb-wrapper">
        <ol class="breadcrumb">
            @foreach($breadcrumbItems as $index => $item)
                <li class="breadcrumb-item {{ $item['active'] ? 'active' : '' }}" 
                    @if($item['active']) aria-current="page" @endif>
                    
                    @if($item['icon'])
                        <i class="{{ $item['icon'] }} me-1"></i>
                    @endif
                    
                    @if($item['url'] && !$item['active'])
                        <a href="{{ $item['url'] }}" 
                           class="text-decoration-none {{ $item['color'] ? 'text-' . $item['color'] : '' }}"
                           @if($index === 0) aria-label="Zur Startseite" @endif>
                            {{ $item['text'] }}
                        </a>
                    @else
                        <span class="{{ $item['active'] ? 'fw-semibold' : '' }} {{ $item['color'] ? 'text-' . $item['color'] : '' }}">
                            {{ $item['text'] }}
                        </span>
                    @endif
                </li>
                
                @if(!$loop->last)
                    <li class="breadcrumb-separator">
                        <i class="ti ti-chevron-right text-muted"></i>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
</div> 