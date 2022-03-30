<?php
  require_once '../core/init.php';
  if (!isIsLoggedIn()){
      Session::flash('warning', 'You need to login to access that page!');
      Redirect::to('admin-login');
  }
    $admin = new Admin();
    $useremail = $admin->data()->admin_email;
    $uniqueid = $admin->data()->admin_uniqueid;
    if (otpCheck()) {
      Session::flash('emailVerify', 'Please verify your email!', 'warning');
      Redirect::to('admin-verify');
    }elseif(isOTPset($uniqueid)){
      Redirect::to('admin-otp');
    }
    $student = new Student();
  require APPROOT . '/includes/adminhead.php';
  require APPROOT . '/includes/adminnav.php';

 ?>
<style media="screen">
.activeImg,.profileSF{
  width: 70px;
  height: 70px;
  border-radius: 50%;
}
.card-title{
  color:#000;
}
.form-control{
  color: #000;
}
option{
  color: #fff;
  background: #000;
}
</style>
<div class="content">
  <div class="container-fluid">
    <!-- first role monitor users -->
    <div class="row">
      <!-- <div class="col-xl-4 col-lg-12">
        <div class="card card-chart">
          <div class="card-header card-header-success">
            <div class="ct-chart" id="dailySalesChart"></div>
          </div>
          <div class="card-body">
            <h4 class="card-title">Daily Sales</h4>
            <p class="card-category">
              <span class="text-success"><i class="fa fa-long-arrow-up"></i> 55% </span> increase in today sales.</p>
          </div>
          <div class="card-footer">
            <div class="stats">
              <i class="material-icons">access_time</i> updated 4 minutes ago
            </div>
          </div>
        </div>
      </div> -->
      <div class="col-xl-6 col-lg-12">
        <div class="card card-chart">
          <div class="card-header card-header-warning">
            <div class="ct-chart">
              <div class="row"  id="loggedInAdmin">  </div>
            </div>
          </div>
          <div class="card-body">
            <h4 class="card-title">Logged Admins</h4>
            <p class="card-category">Current Logged In Superuser
            </p>
          </div>
          <div class="card-footer">
            <div class="stats">
              <i class="material-icons">access_time</i>Update comes every 2 seconds
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-6 col-lg-12">
        <div class="card card-chart">
          <div class="card-header card-header-danger">
            <div class="ct-chart">
              <div class="row" id="showCurrentLoggedInS">

              </div>
            </div>
          </div>
          <div class="card-body">
            <h4 class="card-title">Logged Students</h4>
            <p class="card-category">Current Logged in student</p>
          </div>
          <div class="card-footer">
            <div class="stats">
              <i class="material-icons">access_time</i> Update comes every 2 seconds
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card card-stats">
          <div class="card-header card-header-warning card-header-icon">
            <div class="card-icon">
              <i class="material-icons fa fa-users"></i>
            </div>
            <p class="card-category">Total Students</p>
            <h3 class="card-title" id="totUsers">
            </h3>
          </div>
          <div class="card-footer">
            <div class="stats">
              <i class="material-icons text-warning">person</i>
              <a href="#pablo" class="warning-link">Total Users</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card card-stats">
          <div class="card-header card-header-success card-header-icon">
            <div class="card-icon">
              <i class="material-icons fa fa-user-circle-o"></i>
            </div>
            <p class="card-category">Total Payment Code</p>
            <h3 class="card-title" id="totCod"></h3>
          </div>
          <div class="card-footer">
            <div class="stats">
              <i class="material-icons">date_range</i> Total Payment code generated
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card card-stats">
          <div class="card-header card-header-danger card-header-icon">
            <div class="card-icon">
              <i class="material-icons fa fa-comment"></i>
            </div>
            <p class="card-category">Total Feedback</p>
            <h3 class="card-title" id="totFeedback"></h3>
          </div>
          <div class="card-footer">
            <div class="stats">
              <i class="material-icons">local_offer</i> Total feedback
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card card-stats">
          <div class="card-header card-header-info card-header-icon">
            <div class="card-icon">
              <i class="fa fa-bell"></i>
            </div>
            <p class="card-category">Total Notification</p>
            <h3 class="card-title" id="totNoti"></h3>
          </div>
          <div class="card-footer">
            <div class="stats">
              <i class="material-icons">update</i>  Total notifications
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- add and list supervisors -->
         <h3 class="text-center text-info text-bold text-underline">Generated Codes and Payments</h3><hr>
        <?php include 'superForm.php';?>

  </div>
</div>

<div class="modal fade" id="editResult" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Edit Result</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>

      </div>
      <div class="modal-body text-primary bg-gradient-dark" id="grabResult">

      </div>
      <div class="modal-footer">
           <hr class="invisible">
        <div id="eor"></div>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php
  require APPROOT . '/includes/adminfooter.php';
 ?>
 <script>
     $(document).ready(function(){

      $('#ToggleBtn2').click(function(){
        $('#advisorsToggle').toggle();
      })


     fetchLoggedInAdmins();

         function fetchLoggedInAdmins(){
             action = 'fetch_super';
             $.ajax({
                 url:'scripts/initate.php',
                 method:'post',
                 data:{action:action},
                 success:function(response){
                   console.log(response);

                     $('#loggedInAdmin').html(response);
                 }
             });
         }
         setTimeout(function () {
             fetchLoggedInAdmins();
         },1000);




         // add supervisor
         $('#saveBtn').click(function(e){
             e.preventDefault();
             $.ajax({
                 url:'scripts/super-process.php',
                 method:'post',
                 data:$('#uploadResultsForm').serialize()+'&action=uploadResult',
                 beforeSend:function(){
                     $('#saveBtn').html('Uploading...');
                 },
                 success:function(response){
                     if (response==='success') {
                         $('#uploadResultsForm')[0].reset();
                         $('#showError').html('<div id="" class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert"> &times;</button><i class="fa fa-check"></i>&nbsp; <span>Result Uploaded successfully!</span></div>');
                         fetchResults();
                     }else{
                         $('#showError').html(response);
                         // setTimeout(function(){
                         //     $('#showError').html('');
                         // },10000);

                     }
                 },
                 complete:function(){
                     $('#saveBtn').html('Upload');
                 }
             })
         });

         //fetch admin
         fetchPaymentCode();
         function fetchPaymentCode(){
           action = "fetchCodes";
           $.ajax({
             url:'scripts/super-process.php',
             method:'post',
             data:{action:action},
             success:function(response){
               $('#generatedCode').html(response);

             }
           })
         }

         fetchPayments();
         function fetchPayments(){
             action = "fetchPays";
             $.ajax({
                 url:'scripts/super-process.php',
                 method:'post',
                 data:{action:action},
                 success:function(response){
                     $('#showPayment').html(response);

                 }
             })
         }
    //assign students
  $(document).on('click', '.editResultIcon', function(e){
    e.preventDefault();
      result_id = $(this).attr('e-id');
    $.ajax({
      url: 'scripts/super-process.php',
      method: 'post',
      data: {result_id:result_id},
      success:function(data){
        $('#grabResult').html(data);
      }
    })
  });


 $('body').on('click', '.editResult', function(e){
     e.preventDefault();
     $.ajax({
         url: 'scripts/super-process.php',
         method: 'post',
         data: $('#editResultForm').serialize()+'&action=editResultreq',
         success:function(data){
             if (data==='success') {
                 $('#editResult').modal('hide');
                 fetchResults();

             }else{
                 $('#showErrorEdit').html(data);
             }

         }
     })
 })



 $("body").on("click", ".trashResultIcon", function(e){
             e.preventDefault();
             delete_id = $(this).attr('d-id');
             Swal.fire({
                 title: 'Are you sure?',
                 text: "You can not revert this!",
                 type: 'warning',
                 showCancelButton: true,
                 confirmButtonColor: '#3085d6',
                 cancelButtonColor: '#d33',
                 confirmButtonText: 'Yes, Delete it!'
             }).then((result) => {
                 if (result.value) {
                     $.ajax({
                         url: 'scripts/super-process.php',
                         method: 'POST',
                         data: {delete_id: delete_id},
                         success:function(response){
                             Swal.fire(
                                 'Code Trashed!',
                                 'Code have been deleted',
                                 'success'
                             );
                             fetchPaymentCode();
                         }
                     });

                 }
             });

         });












     });
 </script>
 <script type="text/javascript" src="scripts.js"></script>
 <script type="text/javascript" src="activity.js"></script>
 <!-- <script type="text/javascript" src="notify.js"></script> -->
