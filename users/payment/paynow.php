<?php
require_once '../../core/init.php';
if (!isLoggedInStudent()) {
    Session::flash('warning', 'You must login to access that page');
    Redirect::to('../student-login');
}
$general = new General();
$user = new User();
$db = Database::getInstance();
$validate = new Validate();
$show = new Show();
$user_uniqueid = $user->data()->stud_unique_id;
$user_id = $user->data()->stu_id;

//  if (verifyCheck()) {
//    Session::flash('emailVerify', 'Please verify your email address!', 'warning');
//    Redirect::to('student-verify');
//  }elseif(isOTPsetUser($uniqueid)){
//      Redirect::to('student-otp');
//    }


require APPROOT . '/includes/head.php';



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
        <div class="row">
            <div class="col-md-3"></div>
<?php

if (isset($_POST['paynowBtn'])){

    $email = Input::get('email');
    $amount = Input::get('payment_amount');
    $amount1 = $amount * 100;
    //start payment initailize
    $curl = curl_init();
//            $emails = $email;
//            $amounts = $amount;  //the amount in kobo. This value is actually NGN 300
    // url to go to after payment
    $callback_url = URLROOT. 'users/payment/verified.php';
    $ref = 'AGFPS-'.md5(uniqid());
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([
            'amount'=>$amount1,
            'email'=> $email,
            "reference" => $ref,
            'callback_url' => $callback_url
        ]),
        CURLOPT_HTTPHEADER => [
            "authorization: Bearer sk_test_4ac56f5a703cf8d78642073a7d073bb9a529074a", //replace this with your own test key
            "content-type: application/json",
            "cache-control: no-cache"
        ],
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    if($err){
        // there was an error contacting the Paystack API
        echo $show->showMessage('danger','Curl returned error: ' . $err, 'warning');
    }

    $tranx = json_decode($response, true);

    if(!$tranx['status']){
        // there was an error from the API
        print_r('API returned error: ' . $tranx['message']);
    }

    // comment out this line if you want to redirect the user to the payment page
//             print_r($tranx);
    // redirect to page so User can payment
    // uncomment this line to allow the user redirect to the payment page
    // end payment
    header('Location: ' . $tranx['data']['authorization_url']);

    $student_id = Input::get('student_id');
    $retrieval_code = Input::get('payment_code');
    $phoneNo = Input::get('phoneNo');

    try {
        // end payment
        $general->create('transactions', array(
            'student_id' => $student_id,
            'retrieval_code' => $retrieval_code,
            'amount' => $amount,
            'transaction_ref' => $ref,
            'email' => $email,
            'phoneNo' => $phoneNo

        ));
    }catch (Exception $e){
        echo $show->showMessage('danger', $e->getMessage(), 'warning');
        echo $show->showMessage('danger', $e->getFile(), 'warning');
        echo $show->showMessage('danger', $e->getTrace(), 'warning');
        echo $show->showMessage('danger', $e->getLine(), 'warning');
        return false;
    }

}

if (isset($_GET['payment']) && !empty($_GET['payment'])){
    $payKey = $_GET['payment'];

    $getCode = $db->query("SELECT * FROM generated_code WHERE s_key = '$payKey' ");
    if ($getCode->count()){
        $row = $getCode->first();
    }else{
        $user->logout();
        Redirect::to('student-dashboard');
    }
}
    ?>
        <div class="col-md-6">
        <div class="card">
          <div class="card-header card-header-tabs card-header-warning">
            <div class="nav-tabs-navigation">
              <div class="nav-tabs-wrapper">
                <span class="nav-tabs-title">Form:</span>
                <ul class="nav nav-tabs" data-tabs="tabs">
                  <li class="nav-item">
                    <a class="nav-link active" href="#profile" data-toggle="tab">
                      <i class="material-icons fa fa-user-plus fa-lg"></i> Make payment
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active">
                <hr>
                 <form method="POST" action="">
            <input type="hidden" name="student_id" id="student_id" value="<?=$user_id?>">
            <div class="form-group">
                <label>Payment Code</label>
                <input type="text" name="payment_code" id="payment_code" class="form-control" value="<?=$row->retrieval_code?>" readonly>
            </div>
            <div class="form-group">
                <label>Payment Amount</label>
                <input type="text" name="payment_amount" id="payment_amount" class="form-control" value="<?=$row->amount?>" readonly>
            </div>
            <div class="form-group">
                <label>Payment Term</label>
                <input type="text" name="payment_term" id="payment_term" class="form-control" value="<?=mb_strtoupper($row->term)?>" readonly>
            </div>
            <div class="form-group">
                <label>Payment Level</label>
                <input type="text" name="payment_level" id="payment_level" class="form-control" value="<?=mb_strtoupper($row->level)?>" readonly>
            </div>
            <div class="form-group">
                <label>Payer Email</label>
                <input type="text" name="email" id="email" class="form-control" value="<?=$user->data()->stud_email?>" readonly>
            </div>
            <div class="form-group">
                <label>Payer Phone No</label>
                <input type="text" name="phoneNo" id="phoneNo" class="form-control" value="<?=$user->data()->stud_tel?>" readonly>
            </div>

            <div class="form-group">
               <button type="submit" class="btn btn-warning btn-block" name="paynowBtn">Pay now</button>
            </div>
        </form>
              </div>
            </div>
          </div>
        </div>
       </div>
        <div class="col-md-3">
                <h2 class="text-danger text-center">Date: <?=date('Y-m-d')?></h2>
            </div>
        </div>
    </div>
</div>
<?php
require APPROOT . '/includes/footer.php';
?>

