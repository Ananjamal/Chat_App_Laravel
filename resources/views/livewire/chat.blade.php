<div>
    {{-- Modern Dynamic Chat Interface --}}
    <div class="chat-wrapper">

        {{-- LEFT SIDEBAR - Contacts --}}
        <div class="chat-sidebar">

            {{-- Sidebar Header --}}
            <div class="sidebar-header">
                {{-- Logged-in User Profile --}}
                <div class="user-profile">
                    <div class="user-avatar-wrapper">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=764ba2&color=fff&size=50"
                            class="user-avatar"
                            alt="{{ auth()->user()->name }}">
                        <div class="user-status-dot"></div>
                    </div>
                    <div class="user-info">
                        <h6 class="user-name">{{ auth()->user()->name }}</h6>
                        <small class="user-status">Active now</small>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="logout-btn" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>

                {{-- Messages Header --}}
                <div class="messages-header">
                    <div>
                        <h4 class="messages-title">Messages</h4>
                        <small class="messages-count">{{ count($users) }} contacts</small>
                    </div>
                    <div class="search-btn">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>

            {{-- Contacts List --}}
            <div class="contacts-list">
                @foreach ($users as $user)
                <div wire:click="selectUser({{ $user->id }})"
                    class="contact-card {{ $selectedUser && $selectedUser->id === $user->id ? 'active' : '' }}">
                    <div class="contact-content">
                        <div class="contact-avatar-wrapper">
                            <img src="https://i.pravatar.cc/55?img={{ $loop->iteration }}"
                                class="contact-avatar"
                                alt="{{ $user->name }}">
                            <div class="contact-status-dot"></div>
                        </div>
                        <div class="contact-info">
                            <h6 class="contact-name">{{ $user->name }}</h6>
                            <p class="contact-preview">
                                <i class="fas fa-check-double" style="font-size: 0.75rem; margin-right: 4px;"></i>
                                Click to chat...
                            </p>
                        </div>
                        <div class="contact-meta">
                            <small class="contact-time">now</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- RIGHT SIDE - Chat Area --}}
        <div class="chat-main">

            {{-- Chat Header --}}
            <div class="chat-header">
                @if($selectedUser)
                <div class="chat-header-content">
                    <div class="chat-header-user">
                        <div class="chat-header-avatar-wrapper">
                            <img src="https://i.pravatar.cc/45?img=1"
                                class="chat-header-avatar"
                                alt="{{ $selectedUser->name }}">
                            <div class="chat-header-status-dot"></div>
                        </div>
                        <div class="chat-header-info">
                            <h5>{{ $selectedUser->name }}</h5>
                            <small class="chat-header-status">
                                <i class="fas fa-circle" style="font-size: 6px; margin-right: 4px;"></i>
                                Online
                            </small>
                        </div>
                    </div>
                    <!-- <div class="chat-header-actions">
                        <button type="button" class="action-btn">
                            <i class="fas fa-phone"></i>
                        </button>
                        <button type="button" class="action-btn">
                            <i class="fas fa-video"></i>
                        </button>
                        <button type="button" class="action-btn">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div> -->
                </div>
                @else
                <div style="text-align: center;">
                    <h5 style="margin: 0; font-weight: 600;">Select a contact to start chatting</h5>
                </div>
                @endif
            </div>

            {{-- Messages Area --}}
            <div id="chat-box" class="chat-messages">
                @if($selectedUser)
                @foreach ($messages as $message)
                <div class="message-wrapper {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}">
                    <div class="message-content">
                        <div class="message-bubble {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}">
                            <p class="message-text">{{ $message->message }}</p>
                            <div class="message-meta">
                                <small class="message-time">
                                    {{ $message->created_at->format('H:i') }}
                                </small>
                                @if($message->sender_id === auth()->id())
                                <i class="fas fa-check-double message-status"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="empty-state">
                    <div class="empty-state-content">
                        <div class="empty-state-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h4 class="empty-state-title">No conversation selected</h4>
                        <p class="empty-state-text">Choose a contact from the left to start messaging</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Message Input --}}
            @if($selectedUser)
            <div class="chat-input">
                {{-- Emoji Picker Container --}}
                <div id="emoji-picker-container" style="position: absolute; bottom: 80px; left: 24px; display: none; z-index: 1000;">
                    <emoji-picker></emoji-picker>
                </div>

                <form wire:submit.prevent="submit">
                    <div class="input-wrapper">
                        <button type="button" id="emoji-btn" class="input-btn" title="Add emoji">
                            <i class="fas fa-smile" style="font-size: 1.2rem;"></i>
                        </button>
                        <!-- <button type="button" class="input-btn" title="Attach file">
                            <i class="fas fa-paperclip" style="font-size: 1.2rem;"></i>
                        </button> -->
                        <input wire:model="newMessage"
                            type="text"
                            id="message-input"
                            class="message-input"
                            placeholder="Type your message..."
                            required>
                        <button type="submit" class="send-btn">
                            <i class="fas fa-paper-plane" style="font-size: 1.1rem;"></i>
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>

    <script>
        const chatBox = document.getElementById('chat-box');

        function scrollToBottom() {
            if (chatBox) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            scrollToBottom();

            const userId = @json(auth()->id());
            console.log('Setting up Echo for user:', userId);

            const channel = window.Echo.private("chat." + userId);

            channel.listen(".sent-message", (data) => {
                console.log("New Message Received:", data);

                // Call Livewire method and refresh
                @this.call('handleNewMessage', data).then(() => {
                    console.log('Message handled, scrolling to bottom');
                    setTimeout(scrollToBottom, 100);
                });
            });

            channel.subscribed(() => {
                console.log("✅ Subscribed successfully to chat." + userId);
            });

            channel.error((err) => {
                console.error("❌ Echo error:", err);
            });

            // ===== EMOJI PICKER FUNCTIONALITY =====
            const emojiBtn = document.getElementById('emoji-btn');
            const emojiPickerContainer = document.getElementById('emoji-picker-container');
            const messageInput = document.getElementById('message-input');
            const emojiPicker = document.querySelector('emoji-picker');

            // Toggle emoji picker
            if (emojiBtn && emojiPickerContainer) {
                emojiBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isVisible = emojiPickerContainer.style.display === 'block';
                    emojiPickerContainer.style.display = isVisible ? 'none' : 'block';
                });
            }

            // Handle emoji selection
            if (emojiPicker && messageInput) {
                emojiPicker.addEventListener('emoji-click', (event) => {
                    const emoji = event.detail.unicode;
                    const currentValue = messageInput.value;
                    const cursorPos = messageInput.selectionStart;

                    // Insert emoji at cursor position
                    const newValue = currentValue.slice(0, cursorPos) + emoji + currentValue.slice(cursorPos);
                    messageInput.value = newValue;

                    // Update Livewire model
                    @this.set('newMessage', newValue);

                    // Set cursor after emoji
                    const newCursorPos = cursorPos + emoji.length;
                    messageInput.setSelectionRange(newCursorPos, newCursorPos);
                    messageInput.focus();

                    // Hide picker after selection
                    emojiPickerContainer.style.display = 'none';
                });
            }

            // Close emoji picker when clicking outside
            document.addEventListener('click', (e) => {
                if (emojiPickerContainer &&
                    !emojiPickerContainer.contains(e.target) &&
                    e.target !== emojiBtn &&
                    !emojiBtn.contains(e.target)) {
                    emojiPickerContainer.style.display = 'none';
                }
            });
        });

        // Auto-scroll when new messages are added
        const observer = new MutationObserver(() => {
            scrollToBottom();
        });

        if (chatBox) {
            observer.observe(chatBox, {
                childList: true,
                subtree: true
            });
        }

        // Listen for Livewire updates
        document.addEventListener('livewire:update', () => {
            setTimeout(scrollToBottom, 100);
        });
    </script>
</div>
```