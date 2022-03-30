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
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="container">
                    <h3 class="text-success">Succ<span>ess</span></h3>

                    <?php

                    $curl = curl_init();
                    $reference = isset($_GET['reference']) ? $_GET['reference'] : '';
                    $reference1 = $_GET['reference'];
                    if(!$reference){
                        Session::put('paymentError', 'An error Occurred!');
                        Redirect::to('../student-dashboard');
                    }

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.paystack.co/transaction/verify/".rawurlencode($reference),
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => [
                            "accept: application/json",
                            "authorization: Bearer sk_test_4ac56f5a703cf8d78642073a7d073bb9a529074a",
                            "cache-control: no-cache"
                        ],
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    if($err){
                        // there was an error contacting the Paystack API
                        die('Curl returned error: ' . $err);
                    }

                    $tranx = json_decode($response);

                    if(!$tranx->status){
                        // there was an error from the API
                        die('API returned error: ' . $tranx->message);
                    }

                    if('success' == $tranx->data->status) {
                        $check = $db->query("SELECT * FROM transactions WHERE transaction_ref = '$reference'");
                        if ($check->count()) {
                            $got = $check->first();
                            $transacid = $got->retrieval_code;
                            $checkTerm = $db->query("SELECT * FROM generated_code WHERE retrieval_code = '$transacid'");
                            if ($check->count()) {
                                $tem = $check->first();
                                $term = $tem->term;
                                $level = $tem->level;
                            }
                            $db->query("UPDATE transactions SET payment_status = 'completed' WHERE transaction_ref = '$reference1' ");
                            $db->query("UPDATE generated_code SET status = 'completed' WHERE retrieval_code = '$transacid' ");
                            $db->query("INSERT INTO payment_status (student_id, pay_status, term, level) VALUES ('$user_id', 'completed', '$term', '$level') ");
                        }


                        ?>
                        <h3 class="text-success">Your Payment was successful!</h3>
                        <hr> <h4 class="text-center"><a href="../student-dashboard" class="btn btn-info btn-rounded text-center">Go dashboard</a></h4>

                        <?php
                    }

                    ?>

                    <div class="clearfix"> </div>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
</div>
<?php
require APPROOT . '/includes/footer.php';
?>

