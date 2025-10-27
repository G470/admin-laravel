<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Booking;
use App\Models\BookingMessage;
use App\Models\User;

class BookingMessages extends Component
{
    public $booking;
    public $messages;
    public $newMessage = '';
    public $isVendor = false;

    protected $rules = [
        'newMessage' => 'required|string|max:1000'
    ];

    protected $listeners = [
        'messageAdded' => 'refreshMessages',
        'echo-private:booking.{booking.id},BookingMessageSent' => 'handleNewMessage'
    ];

    public function getListeners()
    {
        return [
            'messageAdded' => 'refreshMessages',
            "echo-private:booking.{$this->booking->id},BookingMessageSent" => 'handleNewMessage',
        ];
    }

    public function mount(Booking $booking)
    {
        $this->booking = $booking;
        $this->isVendor = Auth::check() && Auth::user()->is_vendor;
        $this->loadMessages();
    }

    public function loadMessages()
    {
        try {
            $messages = $this->booking->messages()
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Convert to array for Livewire compatibility
            $messagesArray = $messages->toArray();
            
            // Add initial booking message if it exists
            if ($this->booking->message) {
                // Check if initial message already exists in chat messages
                $hasInitialMessage = false;
                foreach ($messagesArray as $msg) {
                    if (isset($msg['id']) && $msg['id'] === 'initial') {
                        $hasInitialMessage = true;
                        break;
                    }
                }
                
                if (!$hasInitialMessage) {
                    // Create a virtual message for the initial booking message
                    $initialMessage = [
                        'id' => 'initial',
                        'booking_id' => $this->booking->id,
                        'user_id' => $this->booking->renter_id,
                        'message' => $this->booking->message,
                        'is_vendor_message' => false,
                        'read_at' => null,
                        'created_at' => $this->booking->created_at->toISOString(),
                        'updated_at' => $this->booking->created_at->toISOString(),
                        'user' => [
                            'id' => $this->booking->renter_id,
                            'name' => $this->booking->guest_name ?? ($this->booking->renter->name ?? 'Kunde'),
                            'email' => $this->booking->guest_email ?? ($this->booking->renter->email ?? ''),
                            'is_vendor' => false
                        ]
                    ];
                    
                    // Add initial message at the beginning
                    array_unshift($messagesArray, $initialMessage);
                }
            }
            
            $this->messages = $messagesArray;
            
            // Mark messages as read for current user (excluding initial message)
            if (Auth::check()) {
                $userId = Auth::id();
                $unreadMessages = $messages->filter(function ($message) use ($userId) {
                    return $message->user_id !== $userId && is_null($message->read_at);
                });
                
                foreach ($unreadMessages as $message) {
                    if (method_exists($message, 'markAsRead')) {
                        $message->markAsRead();
                    }
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Failed to load messages: ' . $e->getMessage());
            $this->messages = [];
        }
    }

    public function sendMessage()
    {
        // Validate with explicit rules to avoid array_merge issues
        $this->validateOnly('newMessage', [
            'newMessage' => 'required|string|max:1000'
        ]);

        // For token-based guest access, check if guest email matches booking
        $user = Auth::user();
        $isGuestAccess = false;
        
        if (!$user) {
            // Check if this is a valid guest access via booking email
            $guestEmail = session('guest_booking_email') ?? request()->get('guest_email');
            if ($guestEmail && $guestEmail === $this->booking->guest_email) {
                $isGuestAccess = true;
                
                // Find or create a temporary user for guest messaging
                $user = User::where('email', $guestEmail)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $this->booking->guest_name,
                        'email' => $guestEmail,
                        'password' => bcrypt(Str::random(16)),
                        'email_verified_at' => now(),
                    ]);
                }
            } else {
                $this->addError('newMessage', 'Sie müssen angemeldet sein, um Nachrichten zu senden.');
                return;
            }
        }

        // Check if user has permission to send messages for this booking
        $canSendMessage = false;

        if ($user->is_vendor) {
            // Vendor can send if they own the rental
            $canSendMessage = $this->booking->rental->vendor_id === $user->id;
        } else {
            // Customer can send if they are the renter or guest with matching email
            $canSendMessage = $this->booking->renter_id === $user->id || 
                             ($isGuestAccess && $this->booking->guest_email === $user->email);
        }

        if (!$canSendMessage) {
            $this->addError('newMessage', 'Sie sind nicht berechtigt, Nachrichten für diese Buchung zu senden.');
            return;
        }

        try {
            $message = BookingMessage::create([
                'booking_id' => $this->booking->id,
                'user_id' => $user->id,
                'message' => $this->newMessage,
                'is_vendor_message' => $user->is_vendor,
            ]);

            // Reset the message input
            $this->reset('newMessage');
            
            // Reload messages
            $this->loadMessages();
            
            // Broadcast message (wrapped in try-catch to prevent errors)
            try {
                if (class_exists('\App\Events\BookingMessageSent')) {
                    broadcast(new \App\Events\BookingMessageSent($message))->toOthers();
                }
            } catch (\Exception $broadcastException) {
                // Log broadcast error but don't fail the message sending
                \Log::warning('Failed to broadcast message: ' . $broadcastException->getMessage());
            }
            
            $this->dispatch('messageAdded');
            
            session()->flash('message', 'Nachricht gesendet!');

        } catch (\Exception $e) {
            \Log::error('Failed to send message: ' . $e->getMessage());
            $this->addError('newMessage', 'Fehler beim Senden der Nachricht. Bitte versuchen Sie es erneut.');
        }
    }

    public function refreshMessages()
    {
        $this->loadMessages();
    }

    public function handleNewMessage($event)
    {
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.booking-messages');
    }
}
