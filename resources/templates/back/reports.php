
<h1 class="page-header">
   Reports
</h1>

<style>
  @media print{
      #delete-report-button, #print-button{
        display: none;
      }

  }

</style>

<button class="btn btn-primary" onclick="window.print()" id="print-button">Print</button>

<h3 class="bg-success"><?php display_message(); ?></h3>
<table class="table table-hover">


    <thead>

      <tr>
           <th>Report_id</th>
           <th>Customer Name</th>
           <th>Customer Email</th>
           <th>Price</th>
           <th>Product Title</th>
           <th>Product Quantity</th>
           <th>Total</th>
      </tr>
    </thead>


    <tbody>

      <?php get_reports(); ?>


  </tbody>
</table>
