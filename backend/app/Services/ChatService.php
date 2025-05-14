<?php

namespace App\Services;

use App\Models\Chat;

class ChatService
{
    public function create($data){
        return Chat::create([
            'user_id' => $data['user_id'],
            'role'    => $data['role'],
            'content' => $data['content'],
        ]);
    }

    public function getLastUserMessage()
    {
        return Chat::find(auth()->id())
            ->where('role', 'user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->reverse();
    }

    public function getLastAssistantMessage()
    {
        return Chat::find(auth()->id())
            ->where('role', 'assistant')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->reverse();
    }

    public function getLastMessage()
    {
        return Chat::where('user_id', auth()->id())
            ->where('created_at', '<=', now()->subMinutes(5))
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->reverse();
    }

}
