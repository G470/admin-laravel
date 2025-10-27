<div>
    <div class="table-responsive">
        <table class="table border-top">
            <thead>
                <tr>
                    <th>Objekt</th>
                    <th>Kunde</th>
                    <th>Bewertung</th>
                    <th>Kommentar</th>
                    <th>Datum</th>
                    <th>Status</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                    <tr>
                        <td>{{ $review->rental->name }}</td>
                        <td>{{ $review->user->name }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rating-stars me-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            <i class="ti ti-star-filled text-warning"></i>
                                        @else
                                            <i class="ti ti-star text-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span>{{ $review->rating }}.0</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-truncate d-inline-block" style="max-width: 250px;">
                                {{ $review->comment }}
                            </span>
                        </td>
                        <td>{{ $review->created_at->format('d.m.Y') }}</td>
                        <td>
                            <span class="badge bg-label-{{ $review->status === 'published' ? 'success' : ($review->status === 'pending' ? 'warning' : 'danger') }}">
                                {{ $review->status === 'published' ? 'Veröffentlicht' : ($review->status === 'pending' ? 'Ausstehend' : 'Abgelehnt') }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#replyReviewModal{{ $review->id }}">
                                            <i class="ti ti-message-circle me-1"></i> Antworten
                                        </a>
                                        @if ($review->status === 'pending')
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="updateStatus({{ $review->id }}, 'published')">
                                                <i class="ti ti-check me-1"></i> Genehmigen
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="updateStatus({{ $review->id }}, 'rejected')">
                                                <i class="ti ti-x me-1"></i> Ablehnen
                                            </a>
                                        @elseif ($review->status === 'published')
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="updateStatus({{ $review->id }}, 'pending')">
                                                <i class="ti ti-clock me-1"></i> Zurücksetzen
                                            </a>
                                        @endif
                                        <a class="dropdown-item text-danger" href="javascript:void(0);" wire:click="confirmDelete({{ $review->id }})">
                                            <i class="ti ti-trash me-1"></i> Löschen
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Reply Modal -->
                            <div class="modal fade" id="replyReviewModal{{ $review->id }}" tabindex="-1" aria-hidden="true" wire:ignore.self>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Auf Bewertung antworten</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Bewertung von {{ $review->user->name }}</label>
                                                <div class="d-flex mb-2">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $review->rating)
                                                            <i class="ti ti-star-filled text-warning me-1"></i>
                                                        @else
                                                            <i class="ti ti-star text-muted me-1"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <p>{{ $review->comment }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="replyText{{ $review->id }}">Ihre Antwort</label>
                                                <textarea class="form-control" id="replyText{{ $review->id }}" rows="4" wire:model="replyText"></textarea>
                                                @error('replyText') <span class="error text-danger">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Abbrechen</button>
                                            <button type="button" class="btn btn-primary" wire:click="saveReply({{ $review->id }})">Antwort senden</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="ti ti-message-off ti-lg text-muted mb-2"></i>
                            <p class="mb-0">Keine Bewertungen gefunden.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-3">
        {{ $reviews->links() }}
    </div>

    <!-- SweetAlert2 for delete confirmation -->
    @push('page-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('confirmDelete', event => {
                Swal.fire({
                    title: 'Sind Sie sicher?',
                    text: "Diese Bewertung wird dauerhaft gelöscht!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ja, löschen!',
                    cancelButtonText: 'Abbrechen'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('deleteReview', { reviewId: event.detail.reviewId });
                    }
                });
            });

            window.addEventListener('notify', event => {
                Swal.fire({
                    icon: event.detail.type,
                    title: event.detail.type === 'success' ? 'Erfolg!' : 'Fehler!',
                    text: event.detail.message,
                    timer: 3000,
                    timerProgressBar: true
                });
            });

            window.addEventListener('closeModal', event => {
                $('#' + event.detail.modalId).modal('hide');
            });
        });
    </script>
    @endpush
</div>
