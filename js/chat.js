
// validate input fields in login form
function validateLoginForm()
{
    var validationStatus = true;
    var email = document.forms["login"]["username"].value;
    var password = document.forms["login"]["passwd"].value;
    
    $("span").remove(); // remove existing validation error messages
    
    if (email == null || email == "") {
        // dynamically create span element to show error message
        var inputNode = document.getElementById("Username");
        var spanTag = document.createElement("span");
        spanTag.id = "email_error";
        spanTag.innerHTML = "Email Address is required";
        inputNode.parentNode.insertBefore(spanTag, inputNode.nextSibling);
        validationStatus = false;
    }
    
    if (password == null || password == "") {
        // dynamically create span element to show error message
        var inputNode = document.getElementById("Passwd");
        var spanTag = document.createElement("span");
        spanTag.id = "password_error";
        spanTag.innerHTML = "Password is required";
        inputNode.parentNode.insertBefore(spanTag, inputNode.nextSibling);
        validationStatus = false;
    }
    
    if (validationStatus == true) {
        // copy user input to hidden fields
        document.forms["login"]["email"].value = email;
        document.forms["login"]["password"].value = password;
    }
    
    return validationStatus;
}


// validate input fields in register form
function validateRegisterForm()
{
    var validationStatus = true;
    var firstName = document.forms["register"]["fname"].value;
    var lastName = document.forms["register"]["lname"].value;
    var contactEmail = document.forms["register"]["emailaddress"].value;
    var password = document.forms["register"]["passwd"].value;
    
    $("span").remove(); // remove existing validation error messages
    
    if (firstName == null || firstName == "") {
        // dynamically create span element to show error message
        var inputNode = document.getElementById("FirstName");
        var spanTag = document.createElement("span");
        spanTag.id = "firstname_error";
        spanTag.innerHTML = "First name is required";
        inputNode.parentNode.insertBefore(spanTag, inputNode.nextSibling);
        validationStatus = false;
    }
    
    if (lastName == null || lastName == "") {
        // dynamically create span element to show error message
        var inputNode = document.getElementById("LastName");
        var spanTag = document.createElement("span");
        spanTag.id = "lastname_error";
        spanTag.innerHTML = "Last name is required";
        inputNode.parentNode.insertBefore(spanTag, inputNode.nextSibling);
        validationStatus = false;
    }
    
    if (contactEmail == null || contactEmail == "") {
        // dynamically create span element to show error message
        var inputNode = document.getElementById("Email");
        var spanTag = document.createElement("span");
        spanTag.id = "email_error";
        spanTag.innerHTML = "Email address is required";
        inputNode.parentNode.insertBefore(spanTag, inputNode.nextSibling);
    }
    else {
        var atPos = contactEmail.indexOf("@"); // email format validation
        var dotPos = contactEmail.lastIndexOf("."); // email format validation
        
        if (atPos < 1 || dotPos < atPos + 2 || dotPos + 2 >= contactEmail.length) {
            // dynamically create span element to show error message
            var inputNode = document.getElementById("Email");
            var spanTag = document.createElement("span");
            spanTag.id = "email_error";
            spanTag.innerHTML = "Not a valid email address";
            inputNode.parentNode.insertBefore(spanTag, inputNode.nextSibling);
            validationStatus = false;
        }
    }
    
    if (password == null || password == "") {
        // dynamically create span element to show error message
        var inputNode = document.getElementById("Password");
        var spanTag = document.createElement("span");
        spanTag.id = "password_error";
        spanTag.innerHTML = "Password is required";
        inputNode.parentNode.insertBefore(spanTag, inputNode.nextSibling);
        validationStatus = false;
    }
    
    if (validationStatus == true) {
        // copy user input to hidden fields
        document.forms["register"]["firstname"].value = firstName;
        document.forms["register"]["lastname"].value = lastName;
        document.forms["register"]["email"].value = contactEmail;
        document.forms["register"]["password"].value = password;
        document.forms["register"].submit();
    }
    return validationStatus;
}

function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}

/*
 userLogin function is called when login button is clicked
 from the login page. it validates the login page input
 fields and invokes ajax txn to perform login
*/
function userLogin() {
    if (validateLoginForm()) {
        var login = {
            email: $("#Username").val(),
            password: $("#Passwd").val(),
            action: "userLogin"
        }
        
        $.ajax({
            type: 'POST',
            url: 'chat.php',
            dataType: 'json',
            data: login,
            success: function(rsp){
                if (rsp.status == 1) {
                    window.location.href = "chat_main.php";
                } else {
                    var inputNode = document.getElementById("Passwd");
                    var spanTag = document.createElement("span");
                    spanTag.id = "password_error";
                    spanTag.innerHTML = rsp.message;
                    inputNode.parentNode.insertBefore(spanTag, inputNode.nextSibling);
                }
            },
            error: function (xmlHttpRequest, textStatus, errorThrown) {
               alert("Error in userLogin: " + errorThrown + " " + xmlHttpRequest.responseText);
            }
        });
    }
}


/*
 getFriends function is called when main chat window is loaded
 the first time, and when a friend is added or removed
*/
function getFriends(userID) {
    $.ajax({
       type: 'GET',
       url: 'chat.php?action=getFriends&user_id='+userID,
       dataType: 'json',
       success: function(rsp){
       var table = $('<table id="tblcontacts"></table>').addClass('tbl-contacts');
       
       $.each(rsp, function(i, item) {
              var row = $('<tr></tr>').addClass('contact-row');
              var rowData = $('<td></td>').addClass('contact-cell').text(rsp[i].first_name);
              row.append(rowData);
              var chatIconCell = $('<td></td>').addClass('chat-icon');
              var rowData = chatIconCell.html('<div class="image-container"><input type="image" class="chat-icon-button" src="ChatImage.png" onclick="chatWith(' + rsp[i].user_id + ', ' + rsp[i].friend_user_id + ', \'' + rsp[i].friend_name + '\')"/></div>');
              row.append(rowData);
              var rowData = $('<td id="cell_friend_user_id'+rsp[i].friend_user_id+'"></td>').addClass('hidden-cell').text(rsp[i].friend_user_id);
              row.append(rowData);
              var rowData = $('<td id="cell_user_id"></td>').addClass('hidden-cell').text(rsp[i].user_id);
              row.append(rowData);
              table.append(row);
          });
          $('#table-scroll').append(table);
       },
       error : function (xmlHttpRequest, textStatus, errorThrown) {
       alert("Error in getFriends: " + errorThrown + " " + xmlHttpRequest.responseText);
       }
    });

    /*
     Get the last message ID that was read by the session and
     use this value to check if any new messages received from friends
     (new messages alert)
    */
    getMaxMsgID(userID);

}


/*
 Get the last message ID that was read by the session and
 use this value to check if any new messages received from friends
 (new messages alert)
*/
function getMaxMsgID(userID) {
    $.ajax({
        type: 'GET',
        url: 'chat.php?action=getMaxMsgID',
        data: {user_id: userID},
        dataType: 'json',
        success: function(rsp){
            $("#LastMsgIDForContacts").val(rsp.max_msg_id);
        },
        error : function (xmlHttpRequest, textStatus, errorThrown) {
            alert("Error in getMaxMsgID: " + errorThrown + " " + xmlHttpRequest.responseText);
        }
    });
}

/*
 chatWith function is called when a chat with friend is initiated.
 this function loads all the previous messages sent/received 
 to/from friend and also sets "chatting with" title
*/
function chatWith(userID, friendUserID, friendName) {
    $("#chatting-with-title").text("Chatting with " + friendName); // chatting with title
    $("#UserID").val(userID); // session user ID
    $("#FriendUserID").val(friendUserID); // friend's user ID
    $("#LastMsgID").val(0); // Set last message ID from the friend to Zero
    $("#chattext").css("display", "block"); // make elements visible
    $("#msgdiv").css("display", "block"); // make elements visible
    $("#chatstream").empty(); // remove all chat entries as this will be reloaded again
    
    /* ajax txn to get all the previous messages sent/received
     to/from friend
    */
    $.ajax({
        type: 'GET',
        url: 'chat.php?action=getMessages',
        data: {user_id: userID, friend_user_id: friendUserID},
        dataType: 'json',
        success: function(rsp){
           var msgID;
           $.each(rsp, function(i, item) {
              var divText = rsp[i].sender_first_name + " " + rsp[i].date_created + ": ";
              var divElement = $('<div></div>').addClass(rsp[i].div_class_name).html('<b>' + divText + '</b>').append('<br />').append(rsp[i].message);
              $("#chatstream").append(divElement);
              msgID = rsp[i].msg_id;
          });
          $('#LastMsgID').val(msgID); // Reset the last message ID from this friend
          $('#chattext').scrollTop(1E10); // set scroll bar position
        },
        error: function (xmlHttpRequest, textStatus, errorThrown) {
            alert("Error in chatWith: " + errorThrown + " " + xmlHttpRequest.responseText);
        }
    });
    
    // remove existing new message alerts
    $("#tblcontacts tr").each(function() {
        var divObj = $(this).find('td .badge-icon');
        if (divObj)
            divObj.remove();
    });
}


/*
 sendMessage function is called when "Send" button on the main chat 
 window is clicked. This initiates ajax txn to post the message
 and also updates the chat stream to show the posted message
*/
function sendMessage() {
    var userID = $("#UserID").val(); // session user ID
    var friendUserID = $("#FriendUserID").val(); // friend's user ID
    var lastMsgID = $("#LastMsgID").val(); // Get last message ID from the friend

    // data to be submitted with ajax call
    var formData = {
        sendtext: $("#sendtext").val(),
        user_id: $("#UserID").val(),
        friend_user_id: $("#FriendUserID").val(),
        last_msg_id: $("#LastMsgID").val(),
        action: "sendMessage"
    }
    
    $.ajax({
        type: 'POST',
        url: 'chat.php',
        dataType: 'json',
        data: formData,
        success: function(rsp){
           // append the sent message to the chat stream
           var divText = rsp.sender_first_name + " " + rsp.date_created + ": ";
           var divElement = $('<div></div>').addClass('chatstreamdata-from').html('<b>' + divText + '</b>').append('<br />').append(rsp.message);
           $("#chatstream").append(divElement);
           $('#LastMsgID').val(rsp.msg_id);
           $('#chattext').scrollTop(1E10); // set scroll bar position
        },
        error: function (xmlHttpRequest, textStatus, errorThrown) {
            alert("Error in sendMessage: " + errorThrown + " " + xmlHttpRequest.responseText);
        }
    });
    $('#msgdiv textarea').val(''); // clear the text in chat textarea
    $('#msgdiv textarea').removeAttr('readonly'); // make the textarea editable again
    $('#SendBtn').removeAttr('disabled'); // enable Send button
    $('#msgdiv textarea').focus(); // set cursor focus to textarea

}
