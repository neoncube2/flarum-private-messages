<?php

namespace Neoncube\FlarumPrivateMessages\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use Neoncube\FlarumPrivateMessages\Conversation;

class ConversationSerializer extends AbstractSerializer
{
    protected $type = 'conversations';

    protected function getDefaultAttributes($conversation)
    {
        if (!($conversation instanceof Conversation)) {
            throw new \InvalidArgumentException(
                get_class($this) . ' can only serialize instances of ' . Conversation::class
            );
        }

        return [
            //fix : json_decode() passed null problem
            'status' => $conversation->status ? json_decode($conversation->status) : [],
            'createdAt' => $this->formatDate($conversation->created_at),
            'updatedAt' => $this->formatDate($conversation->created_at),
            'totalMessages' => $conversation->total_messages,
            'notNew' => (bool) $conversation->notNew,
            'unReadCount' => $conversation->messages()
                ->get()
                ->filter(function ($message) {
                    if (!$message->is_seen) {
                        return $message;
                    }
                })
                ->count()
        ];
    }

    protected function messages($conversation)
    {
        return $this->hasMany($conversation, MessageSerializer::class);
    }

    protected function recipients($conversation)
    {
        return $this->hasMany($conversation, ConversationRecipientSerializer::class);
    }
}