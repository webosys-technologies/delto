
<!Doctype html>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-upload"></i><strong> ORDER </strong>
        <small>Control panel</small>
        </h1>
      <ol class="breadcrumb" >
        <li><a href="<?php echo base_url(); ?>center/Dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Order</li>
      </ol>
    </section>
    <section class="content">
      <br>
    <div class="form-group" style="width:350px" >
            <label for="name">SEARCH</label>
        <input id="myName" class="form-control" type="text" placeholder="Search..." >
        </div>

    <form id="table" name="table" action="<?php echo base_url(); ?>center/Orders/payment" method="post">
    
    <div class="table-responsive">
    <table id="table_id" class="table table-striped table-bordered" cellspacing="0" width="100%">
      <thead class="thead-dark">
        <tr bgcolor="#338cbf" style="color:#fff">
          <th>ID</th>
          <th>DATE</th>          
          <th>CENTER NAME</th>
          <th>NO. OF STUDENT</th>
          <th>PAYMENT FOR</th>
          <th>AMOUNT</th>
          <th>STATUS</th>
          <th>ACTION</th>

        </tr>
      </thead>
      <tbody id="myTable" >
        <?php
          if (isset($orders)) {
            
          
         foreach($orders as $res){
          $status=$res->order_status; ?>
             <tr <?php if($status == 'success') { ?> style="background-color:#61F48B; "  <?php }elseif ($status == 'pending') { ?> class="bg-warning"
             <?php }else{?> style="background-color:#ec5858; "  <?php } ?> >
                            <td><?php echo $res->order_id;?></td>
                                        <td><?php echo $res->order_date ;?></td>
                                        <td><?php echo $res->center_name; ?></td>
                                       <td><?php echo $res->student_qty;?></td>
                                       <td><?php echo $res->order_name ;?></td>
                                        <td><?php echo $res->order_amount;?></td>                                       
                                       <td><?php echo $res->order_status;?></td>
                                       <td>      
             <button type="button" class="btn btn-info" onclick="order_details(<?php echo $res->order_id; ?>)" data-toggle="tooltip" data-placement="bottom" title="View Admission"><i class="glyphicon glyphicon-eye-open"></i></button>
                                        </td>
                                      
              </tr>
             <?php }}?>



      </tbody>

    </table>
    </div>
    
</form>
</section>
  </div>

  <script type="text/javascript">

    $("#myName").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });

    function order_details(id)
    {
      save_method = 'update';
     $('#form')[0].reset(); // reset form on modals
       //         $('#record').reset();


      //Ajax Load data from ajax
      $.ajax({
        url : "<?php echo site_url('index.php/admin/Orders/ajax_edit/')?>/" + id,        
        type: "GET",
        data: "{}",
        contentType: "application/json; charset=utf-8",               
        dataType: "JSON",
        success: function(data)
        {  

        $('#record').html(" ");
        var sum=0;

        $.each(data, function (i, row) {


            $('#record').append('<tr><td>'+row.order_detail_id+'</td><td>'+row.student_fname + row.student_lname+'</td><td>'+row.course_name+'</td><td>'+row.od_course_fees+'</td><td>'+row.od_book_price+'</td><td>'+row.od_total_amount+'</td><td>'+row.order_detail_status+'</td></tr>');
             sum=sum+parseInt(row.od_total_amount);

             $('[name="sum"]').val(sum);
                          

       });
        

        //     $('#modal_form').append();
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Orders Details'); // Set title to Bootstrap modal title

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax 1');
        }
    });
    }
     $(document).ready( function () {
      
      
      $('#table_id').DataTable();
  } );

  </script>


<div class="modal fade" id="modal_form" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="color:#fff; background-color:#338cbf">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <center><h3 class="modal-title">admission Details</h3></center>
      </div>
      <div class="modal-body form">
        <form action="#" name="form_student" id="form" class="form-horizontal">
           <div class="table-responsive">
    <table id="table_id" class="table table-bordered" cellspacing="0" width="100%">
      <thead class="thead-dark">
        <tr bgcolor="#338cbf" style="color:#fff">
          <th>Order Detail ID</th>
          <th>Student Name</th>
          <th>Course </th>
          <th>Course Amount</th>
          <th>Book Price</th>
          <th>Total</th>
          <th>Payment Status</th>

        </tr>
      </thead>
      <tbody id="record" >
        

      </tbody>

    </table>
    </div>
    <div id="ajax-content-container">
      
    </div>
        <div class="row">
          <label class="col-md-3">Total amount is </label>
          <div class="col-md-3">
            <input type="text" name="sum" value="" readonly>
          </div>
        </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
          </div>
          </form>
        </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

