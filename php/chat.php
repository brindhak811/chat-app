<?php

/*
 * handles all Ajax invocations
 */
    
ob_start();
session_start();
include_once 'lib.php';
$ch = dbConnect();
include_once 'UserProfile.php';
include_once 'ChatFriend.php';
include_once 'ChatMessage.php';


if (!empty($_GET)) {
    extract($_GET);
    switch ($action) {
        case "getFriends": // get friends list to be display in Contacts section
            $friends = array();
            $user_profile = new UserProfile();
            $friends = $user_profile->getFriends($ch, $user_id);
            echo json_encode($friends);
            break;
        case "getMaxMsgID": // get last messadge ID seen by the session for any friend. this will be used to display badge icon.
            $query = "SELECT max(msg_id) max_msg_id FROM scuchat_messages
            WHERE to_user_id = $user_id";
            $result = mysqli_query($ch, $query) or die("QUERY ERROR:" . mysqli_error($ch));
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $json = array('max_msg_id' => $row['max_msg_id']);
            } else {
                $json = array('max_msg_id' => 0);
            }
            echo json_encode($json);
            break;
        case "getMessages": // gets the message history for the current chat window
            $messages = array();
            $messages = fetchMessages($ch, $user_id, $friend_user_id);
            echo json_encode($messages);
            break;
        case "getRecentMessages": // fetches any new messages for the current chat window
            $messages = array();
            $messages = fetchRecentMessages($ch, $user_id, $friend_user_id, $last_msg_id);
            echo json_encode($messages);
            break;
        case "getRecentMessagesCount": // fetches new messages count for all friends in the contacts list
            $messages_count = array();
            $messages_count = fetchRecentMessagesCount($ch, $user_id, $last_msg_id);
            echo json_encode($messages_count);
            break;
        default:
            break;
    }
}


if (!empty($_POST)) {
    extract($_POST);
    switch ($action) {
        case "userLogin": // called when user signs in
            $user_profile = new UserProfile();
            $user_profile->setEmailAddress($email);
            $user_profile->setLoginPassword($password);
            try {
                $user_profile->login($ch);
                $json = array('status' => 1, 'message'=> 'Login Successful');
            }
            catch (Exception $e) {
                $json = array('status' => 0, 'message'=> $e->getMessage());
            }
            echo json_encode($json);
            break;
        case "addFriend": // called when a new friend is added
            $session_user_id = $_SESSION['user']['user_id'];
            $user_profile = new UserProfile();
            $tmp_email = trim($FriendEmail);
            $user_profile->fetchUserForEmail($ch, $tmp_email);
            $chat_friend = new ChatFriend();
            $chat_friend->setAttributes($session_user_id, $user_profile->getUserID(), 'Active');
            $chat_friend->insert($ch);
            $chat_friend2 = new ChatFriend();
            $chat_friend2->setAttributes($user_profile->getUserID(),$session_user_id, 'Active');
            $chat_friend2->insert($ch);
            $json = array('status' => 1, 'message'=> 'Friend Added');
            echo json_encode($json);
            break;
        case "removeFriend": // called when a friend is removed
            $session_user_id = $_SESSION['user']['user_id'];
            $chat_friend = new ChatFriend();
            $chat_friend->setUserID($session_user_id);
            $chat_friend->setFriendUserID($friend_user_id);
            $chat_friend->delete($ch);
            $json = array('status' => 1, 'message'=> 'Friend Removed');
            echo json_encode($json);
            break;
        case "sendMessage": // sends message to the friend in the current chat window
            $escaped_sendtext = mysqli_real_escape_string($ch, $sendtext); // escape special characters
            $query = "INSERT INTO scuchat_messages (from_user_id, to_user_id, message)
            VALUES ('$user_id', '$friend_user_id', '$escaped_sendtext')";
            if (!mysqli_query($ch, $query)) {
                $row = array('status' => 0, 'message'=> 'Unable to process request.');
            } else {
                $msg_id = mysqli_insert_id($ch);
                $row = fetchMessage($ch, $msg_id);
            }
            echo json_encode($row);
            break;
        default:
            break;
    }
}
session_write_close();
?>
