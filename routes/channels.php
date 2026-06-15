<?php

use App\Broadcasting\GlobalPresenceChannel;
use App\Broadcasting\PrivateChatChannel;
use App\Broadcasting\RoomChatChannel;
use App\Broadcasting\UserPresenceChannel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('private-chat.{conversationUuid}', PrivateChatChannel::class);
Broadcast::channel('presence-user', GlobalPresenceChannel::class);
Broadcast::channel('presence-room.{roomUuid}', RoomChatChannel::class);

// Legacy channel aliases
Broadcast::channel('chat.{conversationUuid}', PrivateChatChannel::class);
Broadcast::channel('room.{roomUuid}', RoomChatChannel::class);
Broadcast::channel('presence.user.{userUuid}', UserPresenceChannel::class);
