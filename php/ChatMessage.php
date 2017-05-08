<?php

/*
 * Functions that fetches data from scuchat_messages table
 * for different use cases
 */

// Fetches a message given a message ID
function fetchMessage($ch, $msg_id) {
    $query = "SELECT msg_id, message, scuchat_messages.date_created, 'chatstreamdata-from' div_class_name, scuchat_user.first_name sender_first_name
    FROM scuchat_messages, scuchat_user
    WHERE msg_id = $msg_id
    AND scuchat_messages.from_user_id = scuchat_user.user_id";
    
    $result = mysqli_query($ch, $query) or die("Query Error:" . mysqli_error($ch));
    
    $row = mysqli_fetch_assoc($result);
    return $row;
}

// Fetches all messages between two friends
function fetchMessages($ch, $user_id, $friend_user_id) {
    $query = "SELECT msg_id, message, scuchat_messages.date_created, 'chatstreamdata-from' div_class_name, scuchat_user.first_name sender_first_name
    FROM scuchat_messages, scuchat_user
    WHERE from_user_id = $user_id
    AND to_user_id = $friend_user_id
    AND scuchat_messages.from_user_id = scuchat_user.user_id
    UNION
    SELECT msg_id, message, scuchat_messages.date_created, 'chatstreamdata-to' div_class_name, scuchat_user.first_name sender_first_name
    FROM scuchat_messages, scuchat_user
    WHERE from_user_id = $friend_user_id
    AND to_user_id = $user_id
    AND scuchat_messages.from_user_id = scuchat_user.user_id
    ORDER BY msg_id";
    
    $result = mysqli_query($ch, $query) or die("Query Error:" . mysqli_error($ch));
    
    $messages = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $messages[] = $row;
    }
    return $messages;
}

// Fetches new messages between two friends since last ready message ID
function fetchRecentMessages($ch, $user_id, $friend_user_id, $last_msg_id) {
    $query = "SELECT msg_id, message, scuchat_messages.date_created, 'chatstreamdata-to' div_class_name, scuchat_user.first_name sender_first_name
    FROM scuchat_messages, scuchat_user
    WHERE from_user_id = $friend_user_id
    AND to_user_id = $user_id
    AND msg_id > $last_msg_id
    AND scuchat_messages.from_user_id = scuchat_user.user_id
    ORDER BY msg_id";
    
    $result = mysqli_query($ch, $query) or die("Query Error:" . mysqli_error($ch));
    
    $messages = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $messages[] = $row;
    }
    return $messages;
}

// Fetches new messages count for the badge icon for all friends
function fetchRecentMessagesCount($ch, $user_id, $last_msg_id) {
    $query = "SELECT count(*) count, from_user_id
                FROM scuchat_messages
                WHERE to_user_id = $user_id
                AND msg_id > $last_msg_id
                GROUP BY from_user_id";
    
    $result = mysqli_query($ch, $query) or die("Query Error:" . mysqli_error($ch));
    
    $messages_count = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $messages_count[] = $row;
    }
    return $messages_count;
}

?>
