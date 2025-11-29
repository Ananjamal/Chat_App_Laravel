<div class="row h-100">
    <div class="col-md-4 d-flex flex-column">
        <div class="card shadow-lg h-100 rounded-3">
            <div class="card-header bg-primary text-white d-flex align-items-center rounded-top-3">
                <i class="fas fa-users me-2"></i>
                <h5 class="mb-0">Contacts</h5>
            </div>
            <div class="card-body p-0 chat-sidebar" style="overflow-y: auto;">
                <ul class="list-group list-group-flush">
                    @foreach ($users as $user)
                    <li wire:click="selectUser({{ $user->id }})"
                        class="list-group-item list-group-item-action d-flex align-items-center py-2 px-3 user-contact {{ $selectedUser && $selectedUser->id === $user->id ? 'active-contact' : '' }}"
                        style="cursor:pointer;">
                        <img src="https://i.pravatar.cc/40?img={{ $loop->iteration }}"
                            class="rounded-circle me-3 border border-2" alt="User Avatar">
                        <div class="contact-info">
                            <h6 class="mb-0 user-name">{{ $user->name }}</h6>
                            <small class="text-muted">Online</small>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8 d-flex flex-column">
        <div class="card shadow-lg h-100 d-flex flex-column rounded-3">
            <div class="card-header bg-success text-white d-flex align-items-center rounded-top-3">
                <img src="https://i.pravatar.cc/40?img=1" class="rounded-circle me-3 border border-white border-2"
                    alt="Selected User Avatar">
                <h5 class="mb-0">Chat with {{ $selectedUser->name ?? 'Select a User' }}</h5>
            </div>

            <div class="card-body chat-box" id="chat-box" style="overflow-y:auto; flex-grow:1;">
                @foreach ($messages as $message)
                <div
                    class="message-row {{ $message->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }} d-flex">
                    <div class="message {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}">
                        {{ $message->message }}
                    </div>
                </div>
                @endforeach
            </div>

            <div class="card-footer p-3">
                <form wire:submit.prevent="submit">
                    <div class="input-group">
                        <input wire:model="newMessage" type="text" id="message"
                            class="form-control form-control-lg rounded-start-pill" placeholder="Type a message..."
                            required>
                        <button type="submit" class="btn btn-success rounded-end-pill px-4">
                            <i class="fas fa-paper-plane me-1"></i> Send
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const chatBox = document.getElementById('chat-box');

    function scrollToBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    document.addEventListener('DOMContentLoaded', () => {
        scrollToBottom();

        const userId = @json(auth() - > id());
        const channel = window.Echo.private("chat." + userId);
        channel.listen(".sent-message", (data) => {
            console.log("New Message:", data);

            @this.call('handleNewMessage', data);
        });

        channel.subscribed(() => {
            console.log("Subscribed successfully to chat." + userId);
        });

        channel.error((err) => {
            console.error("Echo error:", err);
        });
    });

    const observer = new MutationObserver(scrollToBottom);
    observer.observe(chatBox, {
        childList: true,
        subtree: true
    });
</script>