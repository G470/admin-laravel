@extends('layouts/contentNavbarLayout')

@section('title', 'Nachrichten')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
    <style>
        .chat-container {
            height: calc(100vh - 300px);
            min-height: 500px;
        }
        .chat-sidebar {
            height: 100%;
            border-right: 1px solid #eee;
        }
        .chat-messages {
            height: calc(100% - 80px);
            overflow-y: auto;
            padding: 1rem;
        }
        .chat-input {
            border-top: 1px solid #eee;
            padding: 1rem;
        }
        .chat-contact {
            cursor: pointer;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s;
        }
        .chat-contact:hover, .chat-contact.active {
            background-color: rgba(105, 108, 255, 0.08);
        }
        .chat-message {
            max-width: 80%;
            margin-bottom: 1rem;
            position: relative;
        }
        .message-content {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            display: inline-block;
        }
        .message-sender .message-content {
            background-color: #f0f2f4;
            color: #636a78;
            border-top-left-radius: 0;
        }
        .message-receiver .message-content {
            background-color: rgba(105, 108, 255, 0.16);
            color: #696cff;
            border-top-right-radius: 0;
        }
        .message-time {
            font-size: 0.75rem;
            color: #a1acb8;
            margin-top: 0.25rem;
        }
        .chat-list {
            height: calc(100% - 60px);
            overflow-y: auto;
        }
        .contact-search {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
        .contact-status {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: block;
            position: absolute;
            right: 0;
            bottom: 0;
            border: 2px solid #fff;
        }
        .contact-status.online {
            background-color: #71dd37;
        }
        .contact-status.offline {
            background-color: #a1acb8;
        }
        .chat-header {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
    </style>
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            // Initialisierung von Select2
            $('.select2').select2();
            
            // Beispiel-Interaktion
            $('.chat-contact').on('click', function() {
                $('.chat-contact').removeClass('active');
                $(this).addClass('active');
                
                // Name des Kontakts in Header übernehmen
                var contactName = $(this).find('.fw-semibold').text();
                $('.chat-header .fw-semibold').text(contactName);
                
                // Zur Demo: Scrolle zum Ende der Nachrichten
                var chatMessages = $('.chat-messages');
                chatMessages.scrollTop(chatMessages[0].scrollHeight);
            });
            
            // Nachrichtensenden simulieren
            $('#sendMessage').on('click', function() {
                var messageText = $('#messageInput').val().trim();
                if (messageText) {
                    var now = new Date();
                    var timeString = now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();
                    
                    // Neue Nachricht erstellen
                    var newMessage = $('<div class="chat-message message-sender d-flex mb-3">' +
                        '<div class="flex-grow-1 ms-auto text-end">' +
                        '<div class="message-content">' + messageText + '</div>' +
                        '<div class="message-time">' + timeString + '</div>' +
                        '</div>' +
                        '<div class="avatar avatar-sm ms-3 mt-1">' +
                        '<img src="{{asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle">' +
                        '</div>' +
                        '</div>');
                    
                    // Nachricht zur Liste hinzufügen
                    $('.chat-messages').append(newMessage);
                    
                    // Input-Feld leeren
                    $('#messageInput').val('');
                    
                    // Zum Ende scrollen
                    var chatMessages = $('.chat-messages');
                    chatMessages.scrollTop(chatMessages[0].scrollHeight);
                    
                    // Demo: Automatische Antwort nach 1 Sekunde
                    setTimeout(function() {
                        var autoReply = $('<div class="chat-message message-receiver d-flex mb-3">' +
                            '<div class="avatar avatar-sm me-3 mt-1">' +
                            '<img src="{{asset('assets/img/avatars/5.png')}}" alt="Avatar" class="rounded-circle">' +
                            '</div>' +
                            '<div class="flex-grow-1">' +
                            '<div class="message-content">Danke für Ihre Nachricht! Ich melde mich in Kürze bei Ihnen.</div>' +
                            '<div class="message-time">' + timeString + '</div>' +
                            '</div>' +
                            '</div>');
                        
                        $('.chat-messages').append(autoReply);
                        chatMessages.scrollTop(chatMessages[0].scrollHeight);
                    }, 1000);
                }
            });
            
            // Beim Drücken von Enter im Input-Feld
            $('#messageInput').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#sendMessage').click();
                    e.preventDefault();
                }
            });
            
            // Initial zum Ende scrollen
            var chatMessages = $('.chat-messages');
            chatMessages.scrollTop(chatMessages[0].scrollHeight);
        });
    </script>
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Vendor /</span> Nachrichten
    </h4>

    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="chat-container">
                <div class="row g-0 h-100">
                    <!-- Kontaktliste -->
                    <div class="col-md-4 col-lg-3 chat-sidebar">
                        <div class="contact-search">
                            <input type="text" class="form-control" placeholder="Kontakte durchsuchen...">
                        </div>
                        <div class="chat-list">
                            <div class="chat-contact active">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3 position-relative">
                                        <div class="avatar avatar-sm">
                                            <img src="{{asset('assets/img/avatars/5.png')}}" alt="Avatar" class="rounded-circle">
                                            <span class="contact-status online"></span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-semibold mb-0">Marta Schmidt</h6>
                                        <small class="text-muted">Vielen Dank für die schnelle...</small>
                                    </div>
                                    <div class="text-end ms-2">
                                        <small class="text-muted">09:41</small>
                                        <span class="badge bg-primary rounded-pill ms-auto mt-1">3</span>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-contact">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3 position-relative">
                                        <div class="avatar avatar-sm">
                                            <img src="{{asset('assets/img/avatars/6.png')}}" alt="Avatar" class="rounded-circle">
                                            <span class="contact-status online"></span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-semibold mb-0">Thomas Müller</h6>
                                        <small class="text-muted">Wir freuen uns auf die Anreise!</small>
                                    </div>
                                    <div class="text-end ms-2">
                                        <small class="text-muted">Gestern</small>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-contact">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3 position-relative">
                                        <div class="avatar avatar-sm">
                                            <img src="{{asset('assets/img/avatars/8.png')}}" alt="Avatar" class="rounded-circle">
                                            <span class="contact-status offline"></span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-semibold mb-0">Sarah Becker</h6>
                                        <small class="text-muted">Gibt es WLAN im Ferienhaus?</small>
                                    </div>
                                    <div class="text-end ms-2">
                                        <small class="text-muted">Mo</small>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-contact">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3 position-relative">
                                        <div class="avatar avatar-sm">
                                            <img src="{{asset('assets/img/avatars/3.png')}}" alt="Avatar" class="rounded-circle">
                                            <span class="contact-status offline"></span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-semibold mb-0">Michael Klein</h6>
                                        <small class="text-muted">Danke für die Informationen!</small>
                                    </div>
                                    <div class="text-end ms-2">
                                        <small class="text-muted">29. Sep</small>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-contact">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3 position-relative">
                                        <div class="avatar avatar-sm">
                                            <img src="{{asset('assets/img/avatars/12.png')}}" alt="Avatar" class="rounded-circle">
                                            <span class="contact-status offline"></span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-semibold mb-0">Claudia Wagner</h6>
                                        <small class="text-muted">Der Aufenthalt war wunderbar!</small>
                                    </div>
                                    <div class="text-end ms-2">
                                        <small class="text-muted">15. Sep</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chat-Bereich -->
                    <div class="col-md-8 col-lg-9">
                        <div class="chat-header d-flex align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3 position-relative">
                                    <div class="avatar avatar-sm">
                                        <img src="{{asset('assets/img/avatars/5.png')}}" alt="Avatar" class="rounded-circle">
                                        <span class="contact-status online"></span>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-0">Marta Schmidt</h6>
                                    <small class="text-muted">Online</small>
                                </div>
                            </div>
                            <div class="ms-auto">
                                <div class="dropdown">
                                    <button class="btn btn-text-secondary dropdown-toggle hide-arrow" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="javascript:void(0);">Kontakt-Details anzeigen</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);">Buchung anzeigen</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);">Als gelesen markieren</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="javascript:void(0);">Konversation löschen</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="chat-messages">
                            <div class="text-center mb-4">
                                <span class="badge bg-label-secondary">Heute</span>
                            </div>

                            <div class="chat-message message-receiver d-flex mb-3">
                                <div class="avatar avatar-sm me-3 mt-1">
                                    <img src="{{asset('assets/img/avatars/5.png')}}" alt="Avatar" class="rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="message-content">Guten Morgen! Ich interessiere mich für Ihr Ferienhaus am See für den Zeitraum 15.07. - 30.07. Ist das Objekt noch verfügbar?</div>
                                    <div class="message-time">09:32</div>
                                </div>
                            </div>

                            <div class="chat-message message-sender d-flex mb-3">
                                <div class="flex-grow-1 ms-auto text-end">
                                    <div class="message-content">Guten Morgen Frau Schmidt! Vielen Dank für Ihre Anfrage. Ja, das Ferienhaus ist in diesem Zeitraum noch verfügbar.</div>
                                    <div class="message-time">09:35</div>
                                </div>
                                <div class="avatar avatar-sm ms-3 mt-1">
                                    <img src="{{asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle">
                                </div>
                            </div>

                            <div class="chat-message message-receiver d-flex mb-3">
                                <div class="avatar avatar-sm me-3 mt-1">
                                    <img src="{{asset('assets/img/avatars/5.png')}}" alt="Avatar" class="rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="message-content">Das freut mich zu hören! Wie hoch wären die Kosten für diesen Zeitraum? Und gibt es eine Endreinigung?</div>
                                    <div class="message-time">09:38</div>
                                </div>
                            </div>

                            <div class="chat-message message-sender d-flex mb-3">
                                <div class="flex-grow-1 ms-auto text-end">
                                    <div class="message-content">Der Preis für den Zeitraum beträgt insgesamt 1.600€. Die Endreinigung ist bereits im Preis inbegriffen. Zusätzlich fällt eine Kaution von 250€ an, die nach problemloser Abreise zurückerstattet wird.</div>
                                    <div class="message-time">09:40</div>
                                </div>
                                <div class="avatar avatar-sm ms-3 mt-1">
                                    <img src="{{asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle">
                                </div>
                            </div>

                            <div class="chat-message message-receiver d-flex mb-3">
                                <div class="avatar avatar-sm me-3 mt-1">
                                    <img src="{{asset('assets/img/avatars/5.png')}}" alt="Avatar" class="rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="message-content">Vielen Dank für die schnelle Auskunft. Das klingt sehr gut! Gibt es die Möglichkeit, weitere Bilder vom Haus zu sehen?</div>
                                    <div class="message-time">09:41</div>
                                </div>
                            </div>
                        </div>

                        <div class="chat-input">
                            <div class="d-flex align-items-center">
                                <input type="text" class="form-control rounded-pill me-2" id="messageInput" placeholder="Schreiben Sie eine Nachricht...">
                                <button class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" id="sendMessage" style="width: 40px; height: 40px;">
                                    <i class="ti ti-send"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection