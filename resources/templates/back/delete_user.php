<?php require_once("../../resources/config.php"); ?>

<?php 

if(isset($_GET['delete_user_id'])) {  // Poistaa ja palauttaa samalle sivulle.
    
    $query = query("DELETE FROM users WHERE user_id = " . escape_string($_GET['delete_user_id']) . " ");
    confirm($query);
    
    set_message("User has been DELETED.");
    
    redirect("index.php?users");
}
else {
    
    redirect("index.php?users");
}



?>




