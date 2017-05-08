<?php

/*
 * UserProfile class
 * Contains basic CRUD operations on scuchat_user table 
 * and login validations
 */
    
    class UserProfile
    {
        // property declarations
        private $user_id; // unique identifier
        private $first_name; // student first name
        private $last_name; // student last name
        private $email_address; // student's contact email
        private $login_pwd; // login password
        private $role; // regular user vs admin
        
        // method declarations
        public function setAttributes($first_name, $last_name, $email_address,$login_pwd) {
            $this->first_name = $first_name;
            $this->last_name = $last_name;
            $this->login_pwd = $login_pwd;
            $this->email_address = $email_address;
        }
        
        public function setUserID($user_id) {
            $this->user_id = $user_id;
        }
        
        public function setFirstName($first_name) {
            $this->first_name = $first_name;
        }
        
        public function setLastName($last_name) {
            $this->last_name = $last_name;
        }
        
        public function setLoginPassword($login_pwd) {
            $this->login_pwd = $login_pwd;
        }
        
        public function setEmailAddress($email_address) {
            $this->email_address = $email_address;
        }
        
        public function setRole($role) {
            $this->role = $role;
        }
        
        public function getUserID() {
            return $this->user_id;
        }
        
        public function getFirstName() {
            return $this->first_name;
        }
        
        public function getLastName() {
            return $this->last_name;
        }
        
        public function getEmailAddress() {
            return $this->email_address;
        }
        
        public function getLoginPassword() {
            return $this->login_pwd;
        }
        
        public function getRole() {
            return $this->role;
        }
        
        public function fetchAsArray($ch, $user_id) {
            $query = "SELECT user_id, first_name, last_name, email_address, login_pwd, role
            FROM scuchat_user WHERE user_id = $user_id";
            $result = mysqli_query($ch, $query) or die("Query Error:" . mysqli_error($ch));
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
            }
            else {
                die("Error in fetching user profile: User does not exist");
            }
            return $row;
        }
        
        public function fetchUserForEmail($ch, $email_address) {
            $query = "SELECT user_id, first_name, last_name, email_address, login_pwd, role
            FROM scuchat_user WHERE email_address = '$email_address'";
            $result = mysqli_query($ch, $query) or die("Query Error:" . mysqli_error($ch));
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
            }
            else {
                die("Error in fetching user profile: User does not exist");
            }
            extract($row);
            $this->setUserID($user_id);
            $this->setFirstName($first_name);
            $this->setLastName($last_name);
            $this->setEmailAddress($email_address);
            $this->setLoginPassword($login_pwd);
        }
        
        public function fetch($ch, $user_id) {
            $row = $this->fetchAsArray($ch, $user_id);
            extract($row);
            $this->setUserID($user_id);
            $this->setFirstName($first_name);
            $this->setLastName($last_name);
            $this->setEmailAddress($email_address);
            $this->setLoginPassword($login_pwd);
        }
        
        public function insert($ch) {
            $query = "INSERT INTO scuchat_user (first_name, last_name, email_address, login_pwd)
            VALUES ('$this->first_name', '$this->last_name', '$this->email_address', '$this->login_pwd')";
            if (!mysqli_query($ch, $query)) {
                die("Query Error:" . mysqli_error($ch));
                
            }
        }
        
        public function update($ch) {
            $query = "UPDATE scuchat_user SET
            first_name = '$this->first_name',
            last_name = '$this->last_name',
            email_address = '$this->email_address',
            login_pwd = '$this->login_pwd'
            WHERE user_id = $this->user_id";
            
            if (!mysqli_query($ch, $query)) {
                die("Query Error:" . mysqli_error($ch));
            }
        }
        
        public function delete($ch) {
            $q = "DELETE FROM scuchat_user WHERE user_id = $this->user_id";
            if (!mysqli_query($ch, $q)) {
                die("Query Error:" . mysqli_error($ch));
            }
        }
        
        public function login($ch) {
            
            $query = "SELECT user_id, first_name, last_name, email_address, login_pwd, role, (SELECT count(*) FROM scuchat_active_users WHERE scuchat_active_users.user_id = scuchat_user.user_id) active
            FROM scuchat_user WHERE email_address = '$this->email_address' AND login_pwd = '$this->login_pwd'";
            $result = mysqli_query($ch, $query) or die("QUERY ERROR:" . mysqli_error($ch));
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                if ($row['active'] != 0) {
                    throw new Exception('Login Error: Already logged in from another session');
                }
                $_SESSION['user'] = $row;
                $this->setUserID($row['user_id']);
                $this->setFirstName($row['first_name']);
                $this->setLastName($row['last_name']);
                $this->setEmailAddress($row['email_address']);
            }
            else {
                throw new Exception('Login Error: Incorrect username or password');
            }
            
            $query = "INSERT INTO scuchat_active_users (user_id) VALUES ($this->user_id)";
            $result = mysqli_query($ch, $query) or die("QUERY ERROR:" . mysqli_error($ch));
        }
        
        public function getFriends($ch, $user_id) {
            $query = "SELECT scuchat_friends.id, scuchat_friends.user_id, scuchat_friends.friend_user_id, scuchat_user.first_name, concat(scuchat_user.first_name, ' ', scuchat_user.last_name) friend_name, scuchat_friends.status
            FROM scuchat_friends, scuchat_user
            WHERE scuchat_friends.user_id = $user_id
            AND scuchat_friends.friend_user_id = scuchat_user.user_id
            ORDER BY first_name";
            /*
            $query = "SELECT scuchat_friends.id, scuchat_friends.user_id, scuchat_friends.friend_user_id, scuchat_user.first_name, concat(scuchat_user.first_name, ' ', scuchat_user.last_name) friend_name, scuchat_friends.status
            FROM scuchat_friends, scuchat_user
            WHERE scuchat_friends.user_id = $user_id
            AND scuchat_friends.friend_user_id = scuchat_user.user_id
            AND EXISTS (SELECT 1 FROM scuchat_active_users WHERE
                        scuchat_active_users.user_id = scuchat_friends.friend_user_id)
            ORDER BY first_name";
            */
            $result = mysqli_query($ch, $query) or die("Query Error:" . mysqli_error($ch));
            
            $friends = array();
            while ($row=mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $friends[] = $row;
            }
            return $friends;
        }
        
    }
    ?>
