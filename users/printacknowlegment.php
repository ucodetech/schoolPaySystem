<?php
require_once '../core/init.php';
if (!isLoggedInStudent()) {
    Session::flash('warning', 'You must login to access that page');
    Redirect::to('student-login');
}
$db = Database::getInstance();
$user = new User();
$uniqueid = $user->data()->stud_unique_id;

//  if (verifyCheck()) {
//    Session::flash('emailVerify', 'Please verify your email address!', 'warning');
//    Redirect::to('student-verify');
//  }elseif(isOTPsetUser($uniqueid)){
//      Redirect::to('student-otp');
//    }


require APPROOT . '/includes/head.php';
?>
<?php
    if (isset($_GET['payid']) && !empty($_GET['payid'])){
        $payid = $_GET['payid'];
        $get = $db->query("SELECT * FROM generated_code WHERE retrieval_code = '$payid' AND status = 'completed' ");
        if ($get->count()){
            $row = $get->first();
        }else{
            Session::put('paymentError', 'You have not completed this payment yet');
            Redirect::to('student-dashboard');
        }
    }

?>
    <style>
        .imgs{
            max-width: 100px;
            max-height: 120px;
            border: 2px solid navy;
        }
    </style>
    <div class="content text-dark">
        <div class="container bg-white">
            <div class="row shadow-lg p-5">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="container pt-10">
                            <img src="profile/<?=$user->data()->passport?>" alt="<?=$user->data()->stud_fname?>"  class=" imgs">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h2 class="text-center text-info">SCHOOL FEE PAYMENT</h2>
                            <p class="text-center pt-0">Acknowledgment</p>
                            <p class="text-center">
                                This is to acknowledge that you have made school fees payment for this term. <?=$row->term?>
                            </p>
                            <hr>

                        </div>
                        <div class="col-md-2">
                            <span class="text-info">Payment Code: <?=$row->retrieval_code?></span>
                            <span class="text-info">Payment Date: <?=pretty_dates($row->payment_date)?></span>
                        </div>
                    </div>

                </div>
                <div class="col-md-2"></div>
            </div>
        </div>
    </div>





<?php require APPROOT . '/includes/footer.php'; ?>