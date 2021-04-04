<!-- List of tickets. If logged in as client, lists that client's tickets, if logged in as staff, listsa all-->
<!-- If not logged in, this page will redirect to the login page -->
<?php
    session_start();
    /* If userId is not set, redirect to login page (header('location:login.php'))*/
    if(!isset($_SESSION['userId']) || !isset($_SESSION['type'])){
        header('location:login.php');
    }

    /* var to hold info to be displayed */
    $tableData = "";

    $userType = $_SESSION['type'];
    $userId = $_SESSION['userId'];

    /* load the xml file */
    $doc = simplexml_load_file("xml/tickets.xml");

    /* If the user type is client, take userId from cookie and display tickets where userId = cookie*/
    if($_SESSION['type'] == 'client'){
        foreach ($doc->children() as $ticket){
            /* if dateClosed is set, proved date, if not, keep as 'n/a' */
            $dateClosed = 'n/a';
            if (isset($ticket->dateClosed)){
                $dateClosed = $ticket->dateClosed;
            }
            /* if the attribute value is the userId, add the ticket data to the code to $tableData var */
            if($ticket->attributes()['userId'] == $userId){
                $tableData .= "<tr><td>".$ticket->attributes()['ticketId']."</td><td>".$ticket->attributes()['category']."</td><td>".$ticket->attributes()['status']."</td><td>".$ticket->dateCreated."</td><td>".$dateClosed."</td><td><a href='details.php?ticketId=".$ticket->attributes()['ticketId']."'>details</a></td></tr>";    
            }
        }
    }   
    
    /* If the user type is staff, just display all of the tickets*/
    elseif($_SESSION['type'] == 'staff'){
        foreach ($doc->children() as $ticket){
            
            $dateClosed = 'n/a';
            if (isset($ticket->dateClosed)){
                $dateClosed = $ticket->dateClosed;
            }

            /* if the attribute value is the userId, add the ticket data to the code to $tableData var */
            $tableData .= "<tr><td>".$ticket->attributes()['ticketId']."</td><td>".$ticket->attributes()['category']."</td><td>".$ticket->attributes()['status']."</td><td>".$ticket->dateCreated."</td><td>".$dateClosed."</td><td><a href='details.php?ticketId=".$ticket->attributes()['ticketId']."'>details</a></td></tr>";    
        }
    }

    else {
        $tableData .= 'error loading data';
    }
?>

<?php require_once 'views/header.php'?>
<main class = "ticketListContent">
    <h2>Your Tickets</h2>
    <table class = 'ticketList'>
        <?php 
            if($_SESSION['type'] == 'client'){
                echo '<a href="createTicket.php">New Ticket</a>';
            }
        ?>
        <thead>
            <tr>
                <th>Ticket Id</th>
                <th>Category</th>
                <th>Status</th>
                <th>Created</th>
                <th>Closed</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?= $tableData?>
        </tbody>
    </table>
</main>
<?php require_once 'views/footer.php' ?>