<?php

namespace App\Livewire;

use App\Events\SentMessage;
use App\Models\ChatMessage;
use App\Models\User;
use Livewire\Component;

class Chat extends Component
{
    public $users;
    public $selectedUser;
    public $newMessage = '';
    public $messages;
    public $loginId;

    public function mount()
    {
        $this->users = User::where('id', '!=', auth()->id())->get();
        $this->selectedUser = $this->users->first();
        $this->loadMessages();
        $this->loginId = auth()->id();
    }

    protected function loadMessages()
    {
        if (!$this->selectedUser) {
            $this->messages = collect();
            return;
        }

        $this->messages = ChatMessage::where(function ($query) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $this->selectedUser->id);
        })->orWhere(function ($query) {
            $query->where('sender_id', $this->selectedUser->id)
                  ->where('receiver_id', auth()->id());
        })
        ->orderBy('created_at', 'asc')
        ->get();
    }

    public function selectUser($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->loadMessages();
        $this->newMessage = '';
    }

    public function submit()
    {
        if (!$this->selectedUser) return;

        $message = ChatMessage::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->selectedUser->id,
            'message' => $this->newMessage,
        ]);

        $this->messages->push($message);
        $this->newMessage = '';

        broadcast(new SentMessage($message));
    }

    public function getListeners()
    {
        return [
            "echo-private:chat.{$this->loginId},sent-message" => 'handleNewMessage',
        ];
    }

    public function handleNewMessage($message)
    {
        if ($message['sender_id'] == $this->selectedUser->id) {
            $messageObject = ChatMessage::find($message['id']);
            $this->messages->push($messageObject);
        }
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
