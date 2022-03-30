<?php
  require_once '../core/init.php';
  if (!isLoggedInStudent()) {
      Session::flash('warning', 'You must login to access that page');
     Redirect::to('student-login');
    }

    $user = new User();
    $uniqueid = $user->data()->stud_unique_id;

//  if (verifyCheck()) {
//    Session::flash('emailVerify', 'Please verify your email address!', 'warning');
//    Redirect::to('student-verify');
//  }elseif(isOTPsetUser($uniqueid)){
//      Redirect::to('student-otp');
//    }


  require APPROOT . '/includes/sthead.php';
  require APPROOT . '/includes/stnav.php';


  $user = new User();
  $general = new General();
  $show = new Show();
  $db = Database::getInstance();
  $userlevel = $user->data()->level;
  $usersession = $user->data()->stu_id;
  $userunique = $user->data()->stud_unique_id;
    $terms = $general->getTerm();
    $fee_amount = $general->getAmount($userlevel);

?>
<style media="screen">
.activeImg{
  width: 70px;
  height: 70px;
  border-radius: 50%;
}
.card-title{
  color:#fff !important;
}
.form-control{
  color: #fff;
}
option{
  color: #fff;
  background: #000;
}
.form-control:read-only{
    background: none;
    border-bottom: 2px solid #0acffe;
}
</style>
<div class="content text-white">
  <div class="container-fluid">
    <!-- first role monitor users -->
    <!-- check student have filled placement form -->
      <h3 class="text-center">Welcome to your dashboard <u><?=$user->data()->stud_fname?></u></h3>
      <div class="row">
         <div class="col-md-4">
             Current Term: <span class="text-info"><?=current_term()?></span>
         </div>
          <div class="col-md-8">
              <?php if (Session::exists('paymentError')): ?>
                  <div class="alert alert-danger alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert">
                          &times;
                      </button>
                      <i class="fa fa-warning"></i>&nbsp;
                      <strong class="text-left">
                          <?=Session::flash('paymentError') ?>
                      </strong>
                  </div>
              <?php endif ?>
              <?php if (Session::exists('paymentSuccess')): ?>
                  <div class="alert alert-success alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert">
                          &times;
                      </button>
                      <i class="fa fa-check"></i>&nbsp;
                      <strong class="text-left">
                          <?=Session::flash('paymentSuccess') ?>
                      </strong>
                  </div>
              <?php endif ?>
              <?php if (Session::exists('paymentError')): ?>
                  <div class="alert alert-success alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert">
                          &times;
                      </button>
                      <i class="fa fa-check"></i>&nbsp;
                      <strong class="text-left">
                          <?=Session::flash('paymentError') ?>
                      </strong>
                  </div>
              <?php endif ?>
              <?php
                    if ($general->getFee($user->data()->stu_id)){
                        ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">
                                &times;
                            </button>
                            <i class="fa fa-check"></i>&nbsp;
                            <strong class="text-left">
                                You have paid your school fee for  <?=current_term()?>
                            </strong>
                        </div>
                        <?php
                    }else{
                        ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">
                                &times;
                            </button>
                            <i class="fa fa-warning"></i>&nbsp;
                            <strong class="text-left">
                                You have not paid your school fee for  <?=current_term()?>
                            </strong>
                        </div>
                        <?php
                    }
              ?>
          </div>

      </div>
      <hr class="invisible">
      <hr>
      <div class="row">
          <div class="col-md-4">
              <h3 class="text-left pb-4">Generate Code for school fee payment</h3>

              <form action="#" method="post" id="generateCodeForm">
                  <div class="form-group">
                      <label for="term">Term</label>
                      <select name="term" id="term" class="form-control">
                          <option value="">Select Term</option>
                            <?php
                                foreach($terms as $term){
                                    ?>
                                    <option value="<?=$term->term?>"><?=$term->term?></option>
                            <?php
                                }
                            ?>
                      </select>
                  </div>
                  <div class="form-group">
                      <label for="level">Level</label>
                      <select name="level" id="level" class="form-control">
                          <option value="">Select Term</option>
                          <option value="<?=$fee_amount->level?>"><?=$fee_amount->level?></option>
                      </select>
                  </div>
                  <div class="form-group">
                      <label for="amount">Amount</label>
                      <select name="amount" id="amount" class="form-control">
                          <option value="">Select Term</option>
                          <option value="<?=$fee_amount->amount?>"><?=$fee_amount->amount?></option>
                      </select>
                  </div>
                  <div class="form-group">
                      <button type="button" class="btn btn-info btn-block" id="generateNow">Generate Now</button>
                  </div>
                  <div class="form-group">
                      <span id="showErrors"></span>
                  </div>
              </form>
          </div>
          <div class="col-md-8">
              <h3 class="text-right">Generated Codes for payment</h3>
              <div class="table-responsive" id="codes"></div>
          </div>
      </div>
  <!-- end check here -->


  </div>
</div>

<?php
  require APPROOT . '/includes/stfooter.php';
 ?>


<script>
    $(document).ready(function(){
        // fetch my chat with supervisor

        $('#generateNow').click(function(e){
            e.preventDefault();
            $.ajax({
                url:'script/payment-process.php',
                method:'post',
                data:$('#generateCodeForm').serialize()+'&action=generateCodeNow',
                beforeSend(){
                  $('#generateNow').html('Generating...');
                },
                success:function (response){
                    // console.log(response);
                    if (response==='success'){
                        fetchCodes();
                        $('#generateCodeForm')[0].reset();
                        $('#showErrors').html(' <div id="" class="alert alert-success alert-dismissible"> <button type="button" class="close" data-dismiss="alert"> &times; </button> <i class="fa fa-check"></i>&nbsp; <span>Payment Code Generated Successfully! You can now pay</span> </div>');
                    }else{
                        $('#showErrors').html(response);
                    }
                },
                complete(){
                    $('#generateNow').html('Generate Now');
                }
            });
        })


        //fetch admin
        fetchCodes();
        function fetchCodes(){
            action = "fetchGeneratedCodes";
            $.ajax({
                url:'script/payment-process.php',
                method:'post',
                data:{action:action},
                success:function(response){
                    $('#codes').html(response);

                }
            })
        }


        //not need anymore else you wish to use ajax to process the payment
    //   $('body').on('click', '.payNow', function(e){
    //     e.preventDefault();
    //     p_id = $(this).attr('p-id');
    //     code_id = $(this).attr('c-id');
    //     $.ajax({
    //         url: 'script/payment-process.php',
    //         method: 'post',
    //         data: {p_id:p_id, code_id:code_id},
    //         success:function(data){
    //             $('#grabDetails').html(data);
    //         }
    //     })
    // });



    })


</script>
<!--  <script type="text/javascript" src="scripts.js"></script>
 <script type="text/javascript" src="activity.js"></script> -->
 <!-- <script type="text/javascript" src="notify.js"></script> -->
