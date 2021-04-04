<!-- Create a new ticket page. On this page a logged in client can create a new ticket -->
<?php 
    session_start();
    /* If user is not authorized, redirect to login page*/
    if(!isset($_SESSION['userId']) || $_SESSION['type'] != 'client'){
        header('location:login.php');
    }

    if(isset($_POST['ticketSubmit'])){
        if($_POST['messageContent'] == ""){
            $errMsg = '</br>Please enter a description of your issue' ;
        }
        else{
            /* Load the xml */

            /* Get the current highest ticket id and add 1 */

            /* Add new ticket element */

            /* Add ticketid to ticket */

            /* Add userId to ticket from session var */

            /* Add category to ticket from form*/

            /* Add status - set to be open */

            /* Add child 'messages' */

            /* Add child message with messageContent */

            /* Save XML*/

            /* Redirect to the listTickets page */
            
        }
        

    }
?>
<?php require_once 'views/header.php'?>
<main class = "homeContent">
    <form name='createTicketForm' action="" method="POST">
        <div>
            <label for = "category">Subject: </label>
            <select id = "category" name = "category">
                <option value = "techSupport" selected="selected" >Technical Support</option>
                <option value = "refunds">Refunds</option>
                <option value = "inqueries">Inqueries</option>
                <option value = "other">Other</option>
            </select>
        </div>
        <div>
            <label for = "messageContent">Your Message: </label>
            <input id = "messageContent" type = "text" name = "message"/>
        </div>
        <?= isset($errMsg)? $errMsg : ''?>
        <button type = "submit" name = "ticketSubmit" value = "submitted">Create Ticket</button>
    </form>
        
    </form>
</main>
<?php require_once 'views/footer.php' ?>