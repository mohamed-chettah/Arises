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

}
