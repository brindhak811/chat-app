<?php
    
/*
 * Brings up the chat page for users to chat with their friends.
 * Contains JavaScript/jQuery/Ajax code to handle send/receive message,
 * new message alerts and regular checking of new messages for the
 * current chat window.
 */
    
include_once 'header.php';
session_write_close();
include_once 'UserProfile.php';
include_once 'ChatFriend.php';
?>

<?php
is_valid_session(); // if session is not valid, this will bring up login page
?>

<div class="chat-container">
    <div class="empty-header">
    </div>
    <div class="chat-title">
        <h2 class="chat-hdr-title">SCU Chat</h2>
    </div>
    <div class="logout">
        <h3 class="logout-text"><?php echo $_SESSION['user']['first_name']; ?> (<a href="logout.php">Logout</a>)</h3>
    </div>
    <div class="chat-box">
        <input id="MyUserID" name="MyUserID" type="hidden" value="<?php echo $_SESSION['user']['user_id']; ?>">
        <div class="chatdiv1" id="chatdiv1">
            <div class="contactsheader" id="contactsheader">
                <p class="contactstitle" id="contactstitle">Contacts</p>
            </div>
            <div class="chat-contacts">
                <div class="table-scroll" id="table-scroll">
                    <script type="text/javascript">getFriends(<?php echo $_SESSION['user']['user_id']; ?>);</script>
                </div>
                <input id="SelectFriendUserID" name="SelectFriendUserID" type="hidden">
                <input id="LastMsgIDForContacts" name="LastMsgIDForContacts" type="hidden">
            </div>
            <div class="chat-action">
                <form>
                <input class="AddBtn" id="AddBtn" name="AddBtn" type="button" value="+ Friend" onclick="addFriend();">
                <input class="DeleteBtn" id="DeleteBtn" name="DeleteBtn" type="button" value="- Friend" onclick="removeFriend();">
                </form>
            </div>
        </div>
        <div class="chatdiv2" id="chatdiv2">
            <div class="chatheader" id="chatheader">
                <p class="chatting-with-title" id="chatting-with-title"></p>
            </div>
            <div class="chattext" id="chattext">
                <div class="chattext2" id="chattext2">
                    <div class="chatstream" id="chatstream">

                    </div>
                </div>
            </div>
            <div class="msgdiv" id="msgdiv">
                <form method="post" id="msgform" action="">
                    <textarea name="sendtext" id="sendtext"></textarea>
                    <input id="UserID" name="user_id" type="hidden">
                    <input id="FriendUserID" name="friend_user_id" type="hidden">
                    <input id="LastMsgID" name="last_msg_id" type="hidden">
                    <input class="SendBtn" id="SendBtn" name="SendBtn" type="submit" value="Send">
                </form>
            </div>
        </div>
    </div>
</div>

<div id="add-friend-dialog">
<form id="addfriend" action="" method="post">
<input class="InputText" id="FriendEmail" name="FriendEmail" type="text" placeholder="Email Address">
</form>
</div>

<script>

// Hide chat section if not chatting with any friend
if ($("#FriendUserID").val() == "") {
    $("#chattext").css("display", "none");
    $("#msgdiv").css("display", "none");
}

// handle for "return" button press event
$(document).keyup(function(e){
    if(e.keyCode == 13){
        if($('#msgdiv textarea').val().trim() == ""){
            $('#msgdiv textarea').val('');
        }
        else{
            $('#msgdiv textarea').attr('readonly', 'readonly'); // Make the text area readonly while the transaction is still in progress
            $('#SendBtn').attr('disabled', 'disabled');	// Disable submit button
            sendMessage();
        }
    }
});

// handle for "submit" event (click of "Send" button)
$(document).ready(function() {
    $('#msgdiv').submit(function(e){
        $('#msgdiv textarea').attr('readonly', 'readonly'); // Make the text area readonly while the transaction is still in progress
        $('#SendBtn').attr('disabled', 'disabled');	// Disable submit button
        sendMessage();
        e.preventDefault();	
    });
});

// handle for window close event. Logout on window close.
$(window).on('beforeunload', function(){
    $.ajax({url: 'logout.php'});
});

// handle for click event on the contacts table
$('#table-scroll').on('click', '#tblcontacts tr', function(e) {
    var rows = $("#tblcontacts tr");
    var row = $(this);
    rows.removeClass('highlight');
    $("#SelectFriendUserID").val("");
    if (!e.metaKey) {
        var selected_friend_user_id = $(this).find(".hidden-cell:first").html();
        row.addClass('highlight');
        $("#SelectFriendUserID").val(selected_friend_user_id);
    }
});

// keep add friend modal window closed on page load
$("#add-friend-dialog").dialog({ autoOpen: false });

/*
 * called when "+ Friend" button is clicked
 * brings up modal window to add friend.
 * makes ajax call to add friend to the database.
 */
function addFriend() {
    $("#add-friend-dialog").dialog( "open" );
    $("#add-friend-dialog").dialog({
        modal: true,
        width: 300,
        height: "auto",
        resizable: false,
        buttons: [
            {
                text: "Add",
                class: "AddModelBtn",
                click: function() {
                    if ($("#FriendEmail").val() != "") {
                        var friend = {
                            FriendEmail: $("#FriendEmail").val(),
                            action: "addFriend"
                        }
                        $.ajax({
                            type: 'POST',
                            url: 'chat.php',
                            dataType: 'json',
                            data: friend,
                            success: function(rsp){
                                $(".tbl-contacts").remove();
                                getFriends(<?php echo $_SESSION['user']['user_id']; ?>);
                                $("#FriendEmail").val("");
                            },
                            error: function (xmlHttpRequest, textStatus, errorThrown) {
                                alert("Error in addFriend: " + errorThrown + " " + xmlHttpRequest.responseText);
                            }
                        });
                    }
                }
            },
            {
                text: "Cancel",
                class: "CancelModelBtn",
                click: function() {
                    $( this ).dialog( "close" );
                }
            }
        ]
    });
    $( "#add-friend-dialog" ).dialog( "option", "title", "Add Friend" );
}

/* 
 * called when "- Friend" button is clicked
 * makes ajax call to remove friend
 */

function removeFriend() {
    var session_user_id = <?php echo json_encode($_SESSION['user']['user_id']); ?>;
    var friend = {
    friend_user_id: $("#SelectFriendUserID").val(),
    action: "removeFriend"
    }
    $.ajax({
           type: 'POST',
           url: 'chat.php',
           dataType: 'json',
           data: friend,
           success: function(rsp){
           $(".tbl-contacts").remove();
           getFriends(session_user_id);
           $("#SelectFriendUserID").val("");
           },
           error: function (xmlHttpRequest, textStatus, errorThrown) {
           alert("Error in removeFriend: " + errorThrown + " " + xmlHttpRequest.responseText);
           }
           });
    
}

/*
 * function to check for new messages for the current chat window.
 * makes ajax call to get new messages.
 */

function checkForNewMessage(){
    var userID = $("#UserID").val();
    var friendUserID = $("#FriendUserID").val();
    var lastMsgID = $("#LastMsgID").val();
    
    if (friendUserID == "" || userID == "" || lastMsgID == "") {
        return;
    }
    
    $.ajax({
       type: 'GET',
       url: 'chat.php?action=getRecentMessages',
       data: {user_id: userID, friend_user_id: friendUserID, last_msg_id: lastMsgID},
       dataType: 'json',
       success: function(rsp){
           if (rsp.length > 0) {
               var msgID;
               $.each(rsp, function(i, item) {
                  var divText = rsp[i].sender_first_name + " " + rsp[i].date_created + ": ";
                  var divElement = $('<div></div>').addClass(rsp[i].div_class_name).html('<b>' + divText + '</b>').append('<br />').append(rsp[i].message);
                  $("#chatstream").append(divElement);
                  msgID = rsp[i].msg_id;
              });
              $('#LastMsgID').val(msgID); // resets last message ID seen by the session
              $('#chattext').scrollTop(1E10); // sets scroll bar position
           }
        },
        error: function (xmlHttpRequest, textStatus, errorThrown) {
        alert("Error in checkForNewMessage: " + errorThrown + " " + xmlHttpRequest.responseText);
        }
   });
}

// periodic check for new messages for the current chat window
setInterval(function(){checkForNewMessage();}, 2000);

/*
 * function to check for new messages for everyone in the contacts.
 * makes ajax call to check new messages.
 * dynamically displays a badge icon with the unread messages count
 */

function checkForAllNewMessages(){
    var userID = $("#MyUserID").val();
    var lastMsgID = $("#LastMsgIDForContacts").val();
    
    if (userID == "" || lastMsgID == "") {
        return;
    }
    
    $.ajax({
        type: 'GET',
        url: 'chat.php?action=getRecentMessagesCount',
        data: {user_id: userID, last_msg_id: lastMsgID},
        dataType: 'json',
        success: function(rsp) {
            if (rsp != "" && rsp.length > 0) {
                $.each(rsp, function(i, item) {
                    $("#tblcontacts tr").each(function() {
                        var friend_user_id = $(this).find('td').eq(2).text(); // third cell (hidden) that contains the user ID of the friend
                        if (friend_user_id == rsp[i].from_user_id) {
                        var currentBadgeCounter = $(this).find('.badge-icon').text(); // get the current count on the badge-icon
                        if (currentBadgeCounter != "") {
                            var newBadgeCounter = Number(currentBadgeCounter) + Number(rsp[i].count); // update the count
                            $(this).find('.badge-icon').remove(); // remove existing badge icon
                        } else {
                            var newBadgeCounter = rsp[i].count;
                        }
                        var divObj = $(this).find('td .image-container');
                        var divElement = $('<div></div>').addClass("badge-icon").html(newBadgeCounter); 
                        divObj.append(divElement); // appends badge icon div to the image container
                    }
                });
            });
        }
    },
    error: function (xmlHttpRequest, textStatus, errorThrown) {
        alert("not ok " + errorThrown + " " + xmlHttpRequest.responseText);
        }
    });
    
    getMaxMsgID(userID);
}

// periodic check for new messages from any friend
setInterval(function(){checkForAllNewMessages();}, 5000);

</script>

<?php include_once 'footer.php'; ?>