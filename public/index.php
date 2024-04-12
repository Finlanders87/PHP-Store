<?php require_once("../resources/config.php"); ?>

<?php include(TEMPLATE_FRONT . DS . "header.php"); ?>



    <!-- Page Content -->
    <div class="container">

        <div class="row">
        
        <?php include(TEMPLATE_FRONT . DS . "side_nav.php") ?>


                           <!-- Categories -->            
            
            <div class="col-md-9">

                <div class="row carousel-holder">

                            <!-- Carouse -->

               <?php include(TEMPLATE_FRONT . DS . "slider.php"); ?>
               
               
                </div>

                <div class="row">

                  
                  <?php get_products_whit_pagination(); ?>           
                   

                </div> <!-- END OF ROW -->

            </div>

        </div>

    </div>
    <!-- /.container -->

<?php include(TEMPLATE_FRONT . DS . "footer.php"); ?>    