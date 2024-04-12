<?php require_once("../../resources/config.php"); ?>

<?php 

if(isset($_GET['delete_report_id'])) {  // Poistaa ja palauttaa samalle sivulle.
    
    $query = query("DELETE FROM reports WHERE report_id = " . escape_string($_GET['delete_report_id']) . " ");
    confirm($query);
    
    set_message("Report has been DELETED.");
    
    redirect("index.php?reports");
}
else {
    
    redirect("index.php?reports");
}



?>
