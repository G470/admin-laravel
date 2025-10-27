<div class="chat-container">
    <!-- Inline Styles for this component -->
    <style>
        .chat-container {
            height: 70vh;
            min-height: 500px;
        }

        .messages-container {
            background: #f8f9fa;
        }

        .message-wrapper {
            margin-bottom: 0.5rem;
        }

        .message-bubble {
            position: relative;
        }

        .my-message .message-content {
            background: #007bff;
            color: white;
            border-radius: 18px 18px 5px 18px;
        }

        .other-message .message-content {
            background: white;
            color: #333;
            border: 1px solid #e9ecef;
            border-radius: 18px 18px 18px 5px;
        }

        .initial-message .message-content {
            background: #17a2b8;
            color: white;
            border-radius: 18px 18px 18px 5px;
            border-left: 4px solid #138496;
        }

        .initial-message-wrapper {
            background: rgba(23, 162, 184, 0.1);
        }

        .avatar-xs {
            width: 24px;
            height: 24px;
        }

        .avatar-xs .avatar-initial {
            font-size: 0.6rem;
            width: 24px;
            height: 24px;
            line-height: 24px;
        }
    </style>

    <div class="card h-100">
        <!-- Chat Header -->
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="ti ti-messages me-2"></i>
                <span class="fw-semibold">Nachrichten</span>
            </div>
            <span class="badge bg-primary rounded-pill">{{ count($messages) }}</span>
        </div>
        
        <!-- Messages Container -->
        <div class="card-body p-0">
            <div class="messages-container" style="height: 400px; overflow-y: auto;">
                @if(count($messages) > 0)
                    @foreach($messages as $message)
                        @php 
                            $messageData = (object) $message; 
                            $isInitialMessage = isset($message['id']) && $message['id'] === 'initial';
                            $isMyMessage = $messageData->user_id === Auth::id() || 
                                          (!Auth::check() && session('guest_booking_email') === $booking->guest_email && !$messageData->is_vendor_message);
                        @endphp
                        
                        <!-- Date Separator -->
                        @if($loop->first || \Carbon\Carbon::parse($messageData->created_at)->format('d.m.Y') !== \Carbon\Carbon::parse($messages[$loop->index - 1]['created_at'])->format('d.m.Y'))
                            <div class="text-center my-3">
                                <small class="bg-light px-3 py-1 rounded-pill text-muted">
                                    {{ \Carbon\Carbon::parse($messageData->created_at)->format('d. F Y') }}
                                </small>
                            </div>
                        @endif

                        <div class="message-wrapper p-3 {{ $isInitialMessage ? 'initial-message-wrapper' : '' }}">
                            <div class="d-flex {{ $isMyMessage ? 'justify-content-end' : 'justify-content-start' }}">
                                <div class="message-bubble {{ $isMyMessage ? 'my-message' : 'other-message' }} {{ $isInitialMessage ? 'initial-message' : '' }}" 
                                     style="max-width: 75%;">
                                    
                                    <!-- Message Header -->
                                    @if(!$isMyMessage || $isInitialMessage)
                                        <div class="message-header mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xs me-2">
                                                    <span class="avatar-initial rounded-circle bg-{{ $messageData->is_vendor_message ? 'success' : 'primary' }}">
                                                        {{ substr($messageData->user['name'] ?? 'U', 0, 1) }}
                                                    </span>
                                                </div>
                                                <small class="fw-semibold">
                                                    @if($isInitialMessage)
                                                        {{ $messageData->user['name'] ?? 'Unbekannt' }} (Initiale Buchungsanfrage)
                                                    @elseif($messageData->is_vendor_message)
                                                        Vermieter
                                                    @else
                                                        {{ $messageData->user['name'] ?? 'Unbekannt' }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Message Content -->
                                    <div class="message-content p-3 rounded">
                                        {{ $messageData->message }}
                                    </div>
                                    
                                    <!-- Message Footer -->
                                    <div class="message-footer mt-1 text-end">
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($messageData->created_at)->format('H:i') }}
                                            @if(!$isInitialMessage && $messageData->read_at && $isMyMessage)
                                                <i class="ti ti-check ms-1"></i>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="ti ti-message-off ti-xl text-muted mb-3"></i>
                        <p class="text-muted">Noch keine Nachrichten vorhanden</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Message Input -->
        @if(Auth::check() || (session('guest_booking_email') && session('guest_booking_email') === $booking->guest_email))
            <div class="card-footer border-top">
                @if($booking->status === 'cancelled')
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="ti ti-info-circle me-1"></i>
                            Der Vermieter hat die Mietanfrage bestätigt.
                        </small>
                    </div>
                @else
                    <form wire:submit.prevent="sendMessage">
                        <div class="d-flex align-items-end">
                            <div class="flex-grow-1 me-2">
                                <textarea 
                                    wire:model.defer="newMessage" 
                                    class="form-control @error('newMessage') is-invalid @enderror" 
                                    placeholder="Geben Sie Ihre Nachricht ein"
                                    rows="2"
                                    style="resize: none; border-radius: 20px;"
                                ></textarea>
                                @error('newMessage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button 
                                type="submit" 
                                class="btn btn-primary rounded-circle"
                                style="width: 40px; height: 40px;"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove>
                                    <i class="ti ti-send"></i>
                                </span>
                                <span wire:loading>
                                    <i class="ti ti-loader"></i>
                                </span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        @else
            <div class="card-footer text-center">
                <small class="text-muted">
                    <i class="ti ti-info-circle me-1"></i>
                    Melden Sie sich an, um Nachrichten zu senden
                </small>
            </div>
        @endif
    </div>
</div>
