<?php

/*
 * ChatFriend class
 * Contains basic CRUD operations on scuchat_friends table
 */
    
    class ChatFriend
    {
        // property declarations
        private $user_id;
        private $friend_user_id;
        private $status;
        
        // method declarations
        public function setAttributes($user_id, $friend_user_id, $status) {
            $this->user_id = $user_id;
            $this->friend_user_id = $friend_user_id;
            $this->status = $status;
        }
        
        public function setUserID($user_id) {
            $this->user_id = $user_id;
        }
        
        public function setFriendUserID($friend_user_id) {
            $this->friend_user_id = $friend_user_id;
        }
        
        public function setStatus($status) {
            $this->status = $status;
        }
        
        public function getUserID() {
            return $this->user_id;
        }
        
        public function getFriendUserID() {
            return $this->friend_user_id;
        }
        
        public function getStatus() {
            return $this->status;
        }
        
        public function fetchFriendStatus($ch, $user_id, $friend_user_id) {
            $query = "SELECT status
            FROM scuchat_friends
            WHERE user_id = $user_id
            AND friend_user_id = $friend_user_id";
            
            $result = mysqli_query($ch, $query) or die("Query Error:" . mysqli_error($ch));
            
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
            }
            else {
                die("Error fetching from scuchat_friends: No records found");
            }
            extract($row);
            $this->setStatus($status);
        }
        
        public function insert($ch) {
            $query = "INSERT INTO scuchat_friends (user_id, friend_user_id, status)
            VALUES ('$this->user_id', '$this->friend_user_id', '$this->status')";
            if (!mysqli_query($ch, $query)) {
                if (mysqli_errno($ch) != 1062) {
                    die("Query Error:" . mysqli_error($ch));
                }
                
            }
        }
        
        public function update($ch) {
            $q = "UPDATE scuchat_friends
                    SET status = '$this->status'
                    WHERE user_id = $this->user_id
                    AND friend_user_id = $friend_user_id";
            if (!mysqli_query($ch, $q)) {
                die("Query Error:" . mysqli_error($ch));
            }
        }
        
        public function delete($ch) {
            $q = "DELETE FROM scuchat_friends
            WHERE user_id = $this->user_id
            AND friend_user_id = $this->friend_user_id";
            if (!mysqli_query($ch, $q)) {
                die("Query Error:" . mysqli_error($ch));
            }
        }
        
    }
    ?>
