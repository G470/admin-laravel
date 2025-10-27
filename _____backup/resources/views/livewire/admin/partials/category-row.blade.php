{{-- Category Row Partial for 3-Level Hierarchy --}}
<tr data-id="{{ $category->id }}" class="category-row level-{{ $level }}">
    {{-- Expand/Collapse Toggle --}}
    <td class="text-center">
        @if($category->children->count() > 0)
            <button type="button" 
                class="btn btn-sm btn-link p-0 text-muted expand-toggle"
                wire:click="toggleExpand({{ $category->id }})"
                title="Unterkategorien {{ $this->isExpanded($category->id) ? 'einklappen' : 'ausklappen' }}">
                <i class="ti {{ $this->isExpanded($category->id) ? 'ti-minus' : 'ti-plus' }}"></i>
            </button>
        @else
            <span class="text-muted">—</span>
        @endif
    </td>

    {{-- ID --}}
    <td class="text-muted small">{{ $category->id }}</td>

    {{-- Category Name with Indentation --}}
    <td>
        <div class="d-flex align-items-center">
            @if($level > 0)
                <span class="text-muted me-2" style="margin-left: {{ $level * 20 }}px;">
                    @for($i = 1; $i < $level; $i++)
                        <span class="category-indent">│&nbsp;&nbsp;&nbsp;</span>
                    @endfor
                    <span class="category-branch">├─</span>
                </span>
            @endif
            
            <span class="category-name {{ $level == 0 ? 'fw-bold' : '' }}">
                @if($this->search && stripos($category->name, $this->search) !== false)
                    {!! str_ireplace($this->search, '<mark>' . $this->search . '</mark>', $category->name) !!}
                @else
                    {{ $category->name }}
                @endif
            </span>
            
            @if($category->children->count() > 0)
                <small class="text-muted ms-2">({{ $category->children->count() }} {{ $category->children->count() == 1 ? 'Unterkategorie' : 'Unterkategorien' }})</small>
            @endif
        </div>
    </td>

    {{-- Parent Category Name --}}
    <td>
        <span class="text-muted">{{ $parentName }}</span>
    </td>

    {{-- Status --}}
    <td>
        <button class="btn btn-sm {{ $category->status == 'online' ? 'btn-success' : 'btn-outline-secondary' }}"
            wire:click="toggleStatus({{ $category->id }})"
            title="Status ändern">
            {{ $category->status == 'online' ? 'Online' : 'Offline' }}
        </button>
    </td>

    {{-- Actions --}}
    <td>
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-primary" 
                wire:click="showEditModal({{ $category->id }})"
                title="Bearbeiten">
                <i class="ti ti-edit"></i>
            </button>
            <button type="button" class="btn btn-outline-info"
                title="Ansehen">
                <i class="ti ti-eye"></i>
            </button>
            <button type="button" class="btn btn-outline-danger" 
                wire:click="confirmDelete({{ $category->id }})"
                title="Löschen">
                <i class="ti ti-trash"></i>
            </button>
        </div>
    </td>
</tr>

{{-- Render Children (Level 2) --}}
@if($this->isExpanded($category->id) && $category->children->count() > 0)
    @foreach($category->children as $childCategory)
        @include('livewire.admin.partials.category-row', [
            'category' => $childCategory, 
            'level' => $level + 1,
            'parentName' => $category->name
        ])
        
        {{-- Render Grandchildren (Level 3) --}}
        @if($this->isExpanded($childCategory->id) && $childCategory->children->count() > 0)
            @foreach($childCategory->children as $grandchildCategory)
                @include('livewire.admin.partials.category-row', [
                    'category' => $grandchildCategory, 
                    'level' => $level + 2,
                    'parentName' => $childCategory->name
                ])
            @endforeach
        @endif
    @endforeach
@endif
