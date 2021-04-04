<!-- User login. If client, redirects to list and shows tickets associated with that client
If Admin, redirects to list and shows all tickets -->
<?php 
    /* If logout is submitted, delete the session information */
    if(isset($_POST['logoutSubmit'])){
        session_start();
        session_destroy();
    }

    /* When user clicks 'log in' */
    if (isset($_POST['submit'])){
        /* If either username or pass have not been set have the client set them */
        if($_POST['username'] == "" || $_POST['password'] == "" ){
            $errMsg = 'Please enter username and password';
        }

        else {
            /* Create domdoc object and load users xml file into it */
            $doc = simplexml_load_file("xml/users.xml");
            /* $doc = new DOMDocument('1.0, "utf-8'); */
            /* load and save*/
            /* $doc->load("xml/users.xml");
            $doc->save('xml/users.xml'); */

            /* Define the username and hashed password */
            $username= $_POST['username'];
            $passwordHash = md5($_POST['password']);
            
            /*if username and password match, retrieve the user type and the user id*/
            
            foreach ($doc->children() as $user){
                /* If there is a match, store the matched user's type and userId as session variables */
                if($user->login->passWord == $passwordHash && $user->login->userName == $username){
                    /* Start session */
                    session_start();
                    /* Add type and userId to session variable */
                    foreach($user->attributes() as $key => $value){
                        $_SESSION["$key"] = (string)$value;
                    }
                }
            }            

            /* If no match let user know $errMsg = 'This username and password do not match any records. */
            if(!isset($_SESSION['type'])){
                $errMsg = 'This username and password do not match any records';
            }

            else{
                header('location: ticketList.php');
            }

            /* If match, store the usertype and userid in a cookie and redirect to ticketlist.php*/
        }
    }
    /* User needs to log in and their userId should be stored as a cookie. userType should be stored as separate cookie*/
?>
<?php require_once 'views/header.php'?>
<main class = "loginContent">
    <h2>Login</h2>
    <form action="" method='POST' name = 'LoginForm'>
        <div>
            <label for='username'>Username:</label>
            <input id='username' name='username' type='text'/>
        </div>
        <div>
            <label for='password'>Password:</label>
            <input id='password' name='password' type='text'/>
        </div>
        <?= isset($errMsg)? $errMsg : ""; ?>
        </br>
        </br>
        <button type='submit' name='submit' value='submitted'>Log in</button>
    </form>
</main>
<?php require_once 'views/footer.php' ?>