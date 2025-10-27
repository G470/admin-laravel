<?php

namespace App\Livewire\Admin;

use App\Models\Badword;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Badwords extends Component
{
    use WithPagination;

    public $word = '';
    public $replacement = '';
    public $status = true; // Use boolean for checkbox
    public $search = '';
    public $editingId = null;
    public $showModal = false;
    public $editMode = false;

    protected $listeners = ['deleteBadword'];

    protected $rules = [
        'word' => 'required|string|max:255',
        'replacement' => 'required|string|max:255', 
        'status' => 'required|boolean',
    ];

    protected $messages = [
        'word.required' => 'Das Wort ist erforderlich.',
        'word.max' => 'Das Wort darf maximal 255 Zeichen haben.',
        'replacement.required' => 'Die Ersetzung ist erforderlich.',
        'replacement.max' => 'Die Ersetzung darf maximal 255 Zeichen haben.',
        'status.required' => 'Der Status ist erforderlich.',
        'status.boolean' => 'Der Status muss wahr oder falsch sein.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function showCreateModal()
    {
        $this->reset(['word', 'replacement', 'editingId', 'editMode']);
        $this->status = true; // Default to active
        $this->showModal = true;
    }

    public function showEditModal($id)
    {
        $badword = Badword::findOrFail($id);
        $this->editingId = $id;
        $this->word = $badword->word;
        $this->replacement = $badword->replacement;
        $this->status = $badword->status === 'active'; // Convert to boolean
        $this->editMode = true;
        $this->showModal = true;
    }

    public function saveBadword()
    {
        $this->validate();

        // Convert boolean status to string for database
        $statusString = $this->status ? 'active' : 'inactive';

        // Check for uniqueness manually if editing
        if ($this->editingId) {
            $this->validate([
                'word' => 'required|string|max:255|unique:badwords,word,' . $this->editingId,
            ]);
        } else {
            $this->validate([
                'word' => 'required|string|max:255|unique:badwords,word',
            ]);
        }

        if ($this->editingId) {
            // Update existing badword
            $badword = Badword::find($this->editingId);
            $badword->update([
                'word' => $this->word,
                'replacement' => $this->replacement,
                'status' => $statusString,
            ]);

            session()->flash('success', 'Badword wurde erfolgreich aktualisiert.');
        } else {
            // Create new badword
            Badword::create([
                'word' => $this->word,
                'replacement' => $this->replacement,
                'status' => $statusString,
            ]);

            session()->flash('success', 'Badword wurde erfolgreich erstellt.');
        }

        $this->reset(['word', 'replacement', 'editingId', 'editMode', 'showModal']);
        $this->status = true; // Reset to default
    }

    public function toggleStatus($id)
    {
        $badword = Badword::findOrFail($id);
        $badword->update([
            'status' => $badword->status === 'active' ? 'inactive' : 'active'
        ]);

        session()->flash('success', 'Status wurde erfolgreich geändert.');
    }

    public function confirmDelete($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function deleteBadword($id)
    {
        Badword::findOrFail($id)->delete();
        session()->flash('success', 'Badword wurde erfolgreich gelöscht.');
    }

    public function render()
    {
        $badwords = Badword::when($this->search, function ($query) {
                return $query->where('word', 'like', '%' . $this->search . '%')
                           ->orWhere('replacement', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.admin.badwords', compact('badwords'));
    }
}
