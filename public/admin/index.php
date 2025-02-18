<?php require_once("../../resources/config.php"); ?>

<?php include(TEMPLATE_BACK . "/header.php"); ?>


<?php  

if(!isset($_SESSION['username'])) {
    
    redirect("../../public");
}

?>

        <div id="page-wrapper">

            <div class="container-fluid">

                <!-- Page Heading -->

                <!-- /.row -->
                 
                <?php
                
                if($_SERVER['REQUEST_URI'] == "/ecom/public/admin/" || $_SERVER['REQUEST_URI'] == "/ecom/public/admin/index.php") {
                    
                    include(TEMPLATE_BACK . "/admin_content.php");
                }
                
                if(isset($_GET['orders'])) {
                    
                    include(TEMPLATE_BACK . "/orders.php");
                }
                
                if(isset($_GET['categories'])) {
                    
                    include(TEMPLATE_BACK . "/categories.php");
                }
                
                if(isset($_GET['products'])) {
                    
                    include(TEMPLATE_BACK . "/products.php");
                }
                    
                if(isset($_GET['add_product'])) {
                    
                    include(TEMPLATE_BACK . "/add_product.php");
                }
                
                if(isset($_GET['edit_product'])) {
                    
                    include(TEMPLATE_BACK . "/edit_product.php");
                }
                
                if(isset($_GET['users'])) {
                    
                    include(TEMPLATE_BACK . "/users.php");
                }
                
                if(isset($_GET['add_user'])) {
                    
                    include(TEMPLATE_BACK . "/add_user.php");
                }
                
                if(isset($_GET['edit_user'])) {
                    
                    include(TEMPLATE_BACK . "/edit_user.php");
                }
                
                if(isset($_GET['reports'])) {
                    
                    include(TEMPLATE_BACK . "/reports.php");
                }
                
                
                if(isset($_GET['reports'])) {
                    
                    include(TEMPLATE_BACK . "/reports.php");
                }
                
                if(isset($_GET['slides'])) {
                    
                    include(TEMPLATE_BACK . "/slides.php");
                }
                
                
                /****************  DELETE  *******************/  
                
                
                
                if(isset($_GET['delete_order_id'])) {
                    
                    include(TEMPLATE_BACK . "/delete_order.php");
                }
                
                if(isset($_GET['delete_product_id'])) {
                    
                    include(TEMPLATE_BACK . "/delete_product.php");
                }
                
                if(isset($_GET['delete_user_id'])) {
                    
                    include(TEMPLATE_BACK . "/delete_user.php");
                }
                
                if(isset($_GET['delete_category_id'])) {
                    
                    include(TEMPLATE_BACK . "/delete_category.php");
                }
                
                if(isset($_GET['delete_report_id'])) {
                    
                    include(TEMPLATE_BACK . "/delete_report.php");
                }
                
                if(isset($_GET['delete_slide_id'])) {
                    
                    include(TEMPLATE_BACK . "/delete_slide.php");
                }
                
                ?>

                 <!-- FIRST ROW WITH PANELS -->


            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->


<?php include(TEMPLATE_BACK . "/footer.php"); ?>
