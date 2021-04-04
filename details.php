<!-- Displays details of a ticket. Record of messages sent by both parties, as well as option to add message to the ticket -->
<?php 
    session_start();
    /* If user is not authorized, redirect to login page*/
    if(!isset($_SESSION['userId']) || !isset($_SESSION['type'])){
        header('location:login.php');
    }
     /* If ticket Id has not been sent via get request, redirect to ticketList.php */
    if(!isset($_GET['ticketId'])){
        header('location:ticketList.php');
    }

    /* Assign ticket Id to var */
    $ticketId = $_GET['ticketId'];

    /*Will require both user.xml AND tickets.xml for this page*/
    $ticketsXmlDoc = simplexml_load_file("xml/tickets.xml");
    $usersXmlDoc = simplexml_load_file("xml/users.xml");
    /* Find the ticket associated with the ticketId */
    $ticket = $ticketsXmlDoc->xpath('//ticket[@ticketId ='. $ticketId.']');
    /* Assign userId for this ticket to var for validation*/
    $thisTicket = $ticket[0];
    $ticketUser = $thisTicket->attributes()['userId'];
    
    /* If you are a client, and your userId does not match the userId on the ticket, redirect to ticketList.php */
    if($_SESSION['type']=='client'){
        if($_SESSION['userId'] != $ticketUser){
            header('location:ticketList.php');
        }
    }

    /* On new message submission, check for the new message and add it to the xml */
    if(isset($_POST['messageSubmit'])){
        /* Validate for empty string */
        if($_POST['newMessage'] == ""){
            $errMsg = '</br>Please type a message before sending';
        }

        /* Check for an accidental send on page refresh */
        elseif(isset($_SESSION['randomNumber']) && $_POST['randomNumber'] == $_SESSION['randomNumber']){
            $errMsg = '</br>duplicate message caught';
        }

        /* If the user has entered message to send, add the new message to the current ticket under the tickets.xml file */
        else{
            /* Set session var randomnumber */
            $postRandNumber = $_POST['randomNumber'];
            $_SESSION['randomNumber'] = $postRandNumber;

            /* Set required information */
            $senderType = $_SESSION['type'];
            $senderUserId = $_SESSION['userId'];
            $messageContent = $_POST['newMessage'];
            $currentTime = date("Y-m-d-H:i", time());

            /* Create new message element and attributes */
            $messageElement = $thisTicket->messages->addChild('message');
            $messageElement->addAttribute('sender',$senderType);
            $messageElement->addAttribute('userId',$senderUserId);
            /* Create new content element within new message element*/
            $messageElement->addChild('content',$messageContent);
            /* Create new timestamp element within new message element */
            $messageElement->addChild('timeStamp',$currentTime);


            /* something further to consider is if the ticket was closed before new message was sent, ticket status should change to open, and dateClosed should be removed from ticket. For another time. */

            /* SAVE CHANGES TO tickets.xml DOCUMENT */
            $ticketParentXML = dom_import_simplexml($ticketsXmlDoc)->ownerDocument;
            $ticketParentXML->preserveWhiteSpace = false;
            $ticketParentXML->formatOutput = true;
            $ticketParentXML->save("xml/tickets.xml");
        }
    }

    /* Reload the users and tickets xml files so current information can be used below */
    $ticketsXmlDoc = simplexml_load_file("xml/tickets.xml");
    $usersXmlDoc = simplexml_load_file("xml/users.xml");
    $thisTicket = $ticketsXmlDoc->xpath('//ticket[@ticketId ='. $ticketId.']')[0];

    /* Store the messages from $thisTicket along with sender and dates sent inside a new $messages variable */
    $messages="";
    foreach($thisTicket->messages->message as $message){
        /* If sender's userId matches the $_session['userId'] var, set senderName to 'You'. Otherwise, get the name of the sender from the users.xml file*/
        $senderName = "";
        if($message->attributes()['userId'] == $_SESSION['userId']){
            $senderName = "You";
        }

        else{
            $userId = $message->attributes()['userId'];
            $name = $usersXmlDoc->xpath('//user[@userId ='. $userId.']/name/first');
            $senderName = $name[0];
        }

        /* for each message within the ticket, add the following html to the $messages var that will populate the html page*/
        $messages .= "<div class=".$message->attributes()['sender']."><div class='sendDate'>".$message->timeStamp."</div><div class='msgContent'><p>".$message->content."</p><p> - ".$senderName."</p></div></div>";
    }

    /* Prevent form resubmits on page refresh -- random number stored as session var upon POST. Should not be the same twice in a row*/
    $randomNumber = rand(000000000,1000000000);

?>
<?php require_once 'views/header.php'?>
<main class = "detailsContent">
    <h2 class = "subTitle">Ticket <?=$ticketId?> Details</h2>
    <div class = "messages"><?=$messages?></div>
    <form class="standardForm" action = "" method = "POST" name = "newMessageForm">
        <input type="hidden" name="randomNumber" value=<?="$randomNumber";?>/>
        <input id="newMessage" name="newMessage" type="text" placeholder="New message">
        <button type="submit" name="messageSubmit" value="submitted">SEND</button>
        <?= isset($errMsg)? $errMsg : ''?>
    </form>
</main>
<?php require_once 'views/footer.php'?>