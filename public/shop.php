<?php require_once("../resources/config.php"); ?>

<?php include(TEMPLATE_FRONT . DS . "header.php"); ?>

    <!-- Page Content -->
    <div class="container">

        <!-- Jumbotron Header -->
        <header class="jumbotron hero-spacer">
            <h1>Welcome to Shop</h1>
           
        </header>

        <hr>
        
   <!-- /.row -->

        <!-- Page Features -->
        <div class="row text-center">

            <?php get_products_whit_pagination("9"); ?>

        </div>
        <!-- /.row -->

        <hr>

    </div>
    <!-- /.container -->
    
    
<!-- Footer -->

<?php include(TEMPLATE_FRONT . DS . "footer.php"); ?>  