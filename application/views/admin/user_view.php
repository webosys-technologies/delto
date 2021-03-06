
<style type="text/css">
  .modal fade{
    display: block !important;
}
.modal-dialog{
      overflow-y: initial !important
}
.modal-body{
  height: 500px;
  overflow-y: auto;
}
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-users"></i><strong> User Management </strong>
        <small>Control Panel <?php  ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo base_url(); ?>admin/Dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Manage User</li>
      </ol>
    </section><br>
    <section class="content">
    <button class="btn btn-primary" onclick="add_user()" data-toggle="tooltip" data-placement="bottom" title="Add User">      <i class="glyphicon glyphicon-plus"></i> Add User</button>
<!--    <button class="btn btn-success" onclick="add_student()"><i class="glyphicon glyphicon-plus"></i> Payment</button>-->
  <br><br>
    <div class="form-group" style="width:350px" >
            <label for="name">SEARCH</label>
        <input id="myName" class="form-control" type="text" placeholder="Search..." >
        </div>
<div class="table-responsive">
    <table id="table_id" class="table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr bgcolor="#338cbf" style="color:#fff">
          <th>ID</th>
          <th>PICTURE</th>
          <th>NAME</th>
          <th>Email</th>
          <th>MOBILE</th>
          <th>CREATED AT</th>
          <th>STATUS</th>

          <th style="width:125px;">ACTION
          </p></th>
        </tr>
      </thead>
      <tbody id="myTable">
        <?php
          if (isset($user)) {
            
          
         foreach($user as $res){?>
             <tr>    
                                        <td><?php echo $res->user_id;?></td>
                                        <td><img src="<?php echo base_url(); ?><?php if (!empty($res->user_profile_pic)){echo $res->user_profile_pic;} else{echo "profile_pic/boss.png";}?>" class="avatar img-responsive"  width="40px" height="30px"></td>
                                        <td><?php echo $res->user_fname.' '. $res->user_lname; ?></td>
                                        <td><?php echo $res->user_email ;?></td>
                                       <td><?php echo $res->user_mobile;?></td>
                                       <td><?php echo $res->user_created_at;?></td>
                                       <td>
                                           <?php 
                                       if($res->user_status==1)
                                       {
                                           echo "Active";
                                       }
                                       else 
                                       {
                                           echo "Not Active";
                                       }
                                       ?></td>
                                       <td>
                  <button class="btn btn-success" onclick="edit_user(<?php echo $res->user_id; ?>)" data-toggle="tooltip" data-placement="bottom" title="Edit User"><i class="glyphicon glyphicon-pencil"></i></button>
                  <!--<button class="btn btn-info" onclick="view_user(<?php echo $res->user_id; ?>)" data-toggle="tooltip" data-placement="bottom" title="View User"><i class="glyphicon glyphicon-eye-open"></i></button>-->
                  <button class="btn btn-danger" onclick="delete_user(<?php echo $res->user_id;?>)" data-toggle="tooltip" data-placement="bottom" title="Delete User"><i class="glyphicon glyphicon-trash"></i></button>


                </td>
              </tr>
             <?php }}?>



      </tbody>

    </table>
    </div>
</section>
  </div>

  <script type="text/javascript">
  $(document).ready( function () {
      
      
      $('#table_id').DataTable();
  } );

    $("#myName").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
  
    var save_method; //for save method string
    var table;


    function add_user()
    {
      save_method = 'add';
      $('#form')[0].reset(); // reset form on modals
      $('#modal_form').modal('show'); // show bootstrap modal
      $('.modal-title').text('Add Centers'); // Set Title to Bootstrap modal title
    }

    function edit_user(id)
    {
      save_method = 'update';
     $('#form')[0].reset(); // reset form on modals

      //Ajax Load data from ajax
      $.ajax({
        url : "<?php echo site_url('index.php/admin/Users/ajax_edit/')?>/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
//            $("#append_city").remove();     
            $('[name="id"]').val(data.user_id);
            $('[name="fname"]').val(data.user_fname);
            $('[name="lname"]').val(data.user_lname);
            $('[name="email"]').val(data.user_email);
            $('[name="mobile"]').val(data.user_mobile);
            $('[name="gender"]').val(data.user_gender);
            $('[name="password"]').val(data.user_password);
             $('[name="status"]').val(data.user_status);
//        
            
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit Centers'); // Set title to Bootstrap modal title

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax 1');
        }
    });
    }



    function save()
    {
      var url;
      if(save_method == 'add')
      {
        url = "<?php echo site_url('index.php/admin/Users/user_add')?>";
      }
      else
      {
        url = "<?php echo site_url('index.php/admin/Users/user_update')?>";
      }

       // ajax adding data to database
          $.ajax({
            url : url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function(data)
            {
                if(data.status)
                {
                alert(data.msg);
               //if success close modal and reload ajax table
               $('#modal_form').modal('hide');
              location.reload();// for reload a page
                 }
                 else
                 {
                      alert(data.error);
                 }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data');
            }
        });
    }
    
    
    
    function view_user(id)
    {
    

      //Ajax Load data from ajax
      $.ajax({
        url : "<?php echo site_url('index.php/admin/Users/ajax_edit/')?>/" + id,        
        type: "GET",
               
        dataType: "JSON",
        success: function(data)
        {          
            $('#ufname').html(data.user_fname);
            $('#ulname').html(data.user_lname); 
            $('#uemail').html(data.user_email);
            $('#umobile').html(data.user_mobile);
            $('#ugender').html(data.user_gender);
            if(data.user_profile_pic)
            {
            $('#uprofile_pic').attr("src", "<?php  echo base_url();?>"+data.user_profile_pic);
             }
             else
             {
               $('#uprofile_pic').attr("src", "<?php echo base_url(); ?>profile_pic/boss.png");
             }
//            $('#remove_pic').attr("onclick","remove_profile_pic("+data.student_id+")");
            $('#udob').html(data.user_dob);
            $('#uaddress').html(data.user_address);  
            $('#ucity').html(data.user_city);
            $('#ustate').html(data.user_state);
            $('#upincode').html(data.user_pincode);
            
            $('#modal_form2').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('User Data'); // Set title to Bootstrap modal title

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax 1');
        }
    });
    }

    function delete_user(id)
    {
      if(confirm('Are you sure delete this data?'))
      {
        // ajax delete data from database
          $.ajax({
            url : "<?php echo site_url('index.php/admin/Users/user_delete')?>/"+id,
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
               
               location.reload();
               alert(data.msg);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error deleting data');
            }
        });

      }
    }

  </script>

<!-- Bootstrap modal -->
  <div class="modal fade" id="modal_form2" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="color:#fff; background-color:#338cbf" >
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <center><h3 class="modal-title"></h3></center>
      </div>
         <form action="#" name="" id="" class="form-horizontal">
      <div class="modal-body form">
       
          <input type="hidden" value="" name="student_id"/>
          <input type="hidden" value="" name="center_id"/>

          <div class="box-body">
                            
              
              
              <div class="row">
                
                  <div class="col-md-3" >
                      
                      <img id='uprofile_pic' src='' width="100px" hieght="100px" >
                  </div><br><br>
                 <!--<button id='remove_pic' value="" onclick="" class="btn btn-danger">Remove Profile Photo</button>-->
                   
              </div>
            
              <br>
               <div class="row">
                   <div class="col-md-5"> 
                <label for="pincode">User Name :</label><span id="ufname"></span>&nbsp;<span id="ulname"></span>
                   </div>
                                   

                   
                   <div class="col-md-5">                                   
                    <label for="pincode">Course :</label><span id="ucourse_name"></span>
                     </div>
               </div>
              <br>
              
               <div class="row">
                   <div class="col-md-5">                                    
                   <label for="pincode">Admission Month :</label><span id="saddmission_month"></span>                                 
                   </div>
                   
                   <div class="col-md-5">                                   
                    <label for="pincode">Course End Date :</label><span id="ucourse_end_date"></span>
                     </div>
               </div>
              <br>
              
               <div class="row">
                   <div class="col-md-5">                                    
                   <label for="pincode">Username :</label><span id="uusername"></span>                                 
                   </div>
                   
                   <div class="col-md-5">                                   
                    <label for="pincode">Password :</label><span id="upassword"></span>
                     </div>
               </div>
              
              <br>
               <div class="row">
                   <div class="col-md-5">                                    
                   <label for="pincode">Email :</label><span id="uemail"></span>                                 
                   </div>
                   
                   <div class="col-md-5">                                   
                    <label for="pincode">Mobile Number :</label><span id="umobile"></span>
                     </div>
               </div>
              
              <br>
               <div class="row">
                   <div class="col-md-5">                                    
                   <label for="pincode">Gender :</label><span id="ugender"></span>                                 
                   </div>
                   
                   <div class="col-md-5">                                   
                    <label for="pincode">DOB :</label><span id="udob"></span>
                     </div>
               </div>
              <br>
              <div class="row">
                   <div class="col-md-5">                                    
                   <label for="pincode">Last Education :</label><span id="ulast_education"></span>                                 
                   </div>
                   
                   <div class="col-md-5">                                   
                    <label for="pincode">Address :</label><span id="uaddress"></span>
                     </div>
               </div>
              <br>
              <div class="row">
                   <div class="col-md-5">                                    
                   <label for="pincode">City :</label><span id="ucity"></span>                                 
                   </div>
                   
                   <div class="col-md-5">                                   
                    <label for="pincode">State :</label><span id="ustate"></span>
                     </div>
               </div>
                         
               </div><!-- /.box-body -->
    
        
          </div>

          </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>  
  
  
  
   <!-- Bootstrap modal -->
  <div class="modal fade" id="modal_form" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="color:#fff; background-color:#338cbf" >
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <center><h3 class="modal-title">User Form</h3></center>
      </div>
      <div class="modal-body form">
        <form action="#" name="form_course" id="form" class="form-horizontal">
          <input type="hidden" value="" name="id"/>
          <div class="form-body">
            <div class="form-group">
              <label class="control-label col-md-3">First Name</label>
              <div class="col-md-9">
                <input name="fname" placeholder="First name" class="form-control" type="text" style="text-transform:uppercase">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Last Name</label>
              <div class="col-md-9">
                <input name="lname" placeholder="Last Name" class="form-control" type="text" style="text-transform:uppercase">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Gender</label>
              <div class="col-md-9">
                  <select name="gender" class="form-control">
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                  </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Mobile</label>
              <div class="col-md-9">
                <input name="mobile" placeholder="Mobile" class="form-control" type="text">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Email</label>
              <div class="col-md-9">
                <input name="email" placeholder="Email" class="form-control" type="email">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3">Password</label>
              <div class="col-md-9">
                <input name="password" placeholder="Password" class="form-control" type="password">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Profile Picture</label>
              <div class="col-md-9">
                <input name="img" placeholder="fees" class="form-control" type="file">
              </div>
            </div>
              <div class="form-group">
              <label class="control-label col-md-3">Status</label>
              <div class="col-md-9">
                  <select name="status" class="form-control">
                      <option value="1">Active</option>
                      <option value="0">Not Active</option>
                  </select>
              </div>
            </div>
            
      
        </form>
      </div>
          <div class="modal-footer">
            <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  <!-- End Bootstrap modal -->

</div>
