<?php
require_once '../../core/init.php';
$admin = new Admin();
$show = new Show();
$validate = new Validate();
$adminEmail = $admin->data()->admin_email;
$fullname = $admin->data()->admin_fullname;
$mail = new MyMail();
if (isset($_POST['action']) && $_POST['action'] == 'verify') {
  $adminId = $admin->data()->id;

   if (Input::exists()) {
    $validation = $validate->check($_POST, array(
      'token' => array(
        'required' => true,
        'min' => 8,
        'max' => 8
      )
    ));
    if ($validation->passed()) {
      $status = "on";
      $token = Input::get('token');
      $sql = "SELECT * FROM verifyAdmin WHERE sudo_email = '$adminEmail' ";
      $query = Database::getInstance()->query($sql);
      if ($query->count()) {
          $row = $query->first();
          $date = date("Y-m-d");
          $cuDate = pretty_dated($row->dateSent);
          if ($cuDate != $date) {
            echo $show->showMessage('danger', 'Token Expired! request another one.', 'warning');
            return false;
          }elseif($token != $row->token){
            echo $show->showMessage('danger', 'Invalid Token!', 'warning');
            return false;

          }else{
            $admin->updateAdminVerified($adminId, array(
              'admin_email_verified' => 'yes'
            ));
            $update1 = "UPDATE verifyAdmin SET status = 1 WHERE sudo_email = '$adminEmail' ";
            Database::getInstance()->query($update1);
            echo 'success';

          }

          }

    }else{
      foreach ($validation->errors() as $error) {
        echo $show->showMessage('danger', $error, 'warning');
        return false;
      }
    }
  }
}

if (isset($_POST['action']) && $_POST['action'] == 'resendOTP') {

      $status = "on";

      $sql = "SELECT * FROM verifyAdmin WHERE sudo_email = '$adminEmail' ";
      $query = Database::getInstance()->query($sql);
      $rndno=rand(10000000, 99999999);//OTP generate
      if ($query->count()) {
            $update = "UPDATE verifyAdmin SET status = 0, token = '' WHERE sudo_email = '$adminEmail' ";
            Database::getInstance()->query($update);
          }else{
            Database::getInstance()->query("INSERT INTO verifyAdmin (sudo_email, token,status) VALUES ('$adminEmail', '$rndno', 0) ");
          }
            $token = "TOKEN: "."<h2>".$rndno."</h2>";

            // _____________________________________________________

    $messageBody = "
                <div style='width:80%; height:auto; padding:10px; margin:10px'>

            <p style='color: #fff; font-size: 20px; text-align: center; text-transform: uppercase;margin-top:0px'>One Time Password Verification<br></p>
            <p>Hey $fullname! <br><br>

          Here is your token please use the code to verify your email!

           <br><hr>
            $token

          </p>
           </div>
            ";

    $fromName = 'AG-FPS';
    $email = $adminEmail;
    $subject = 'Email Verification';
    if($mail->sendmail($fromName, $email, $subject, $messageBody))

            $update = "UPDATE verifyAdmin SET token = '$rndno', dateSent = NOW()  WHERE sudo_email = '$adminEmail' ";
            Database::getInstance()->query($update);
            echo 'success';



  }
//admin check
if (isset($_POST['action']) && $_POST['action'] == 'verifyAdmin') {
  $adminId = $admin->data()->id;
  $uniqueid = $admin->data()->admin_uniqueid;
   if (Input::exists()) {
    $validation = $validate->check($_POST, array(
      'secure_token' => array(
        'required' => true,
        'min' => 8,
        'max' => 8
      )
    ));
    if ($validation->passed()) {
      $token = Input::get('secure_token');
      $sql = "SELECT * FROM adminOtp WHERE admin_unique = '$uniqueid' ";
      $query = Database::getInstance()->query($sql);
      if ($query->count()) {
          $row = $query->first();
          $date = date("Y-m-d");
          $cuDate = pretty_dated($row->dateSent);
          if ($cuDate != $date) {
            echo $show->showMessage('danger', 'Token Expired! request another one.', 'warning');
            return false;
          }elseif($token != $row->secure_token){
            echo $show->showMessage('danger', 'Invalid Token!', 'warning');
            return false;

          }else{
            $sq = "UPDATE adminOtp SET status = 'used' WHERE admin_unique = '$uniqueid' ";
            if(Database::getInstance()->query($sq))
                echo 'success';

          }

          }

    }else{
      foreach ($validation->errors() as $error) {
        echo $show->showMessage('danger', $error, 'warning');
        return false;
      }
    }
  }
}



if (isset($_POST['action']) && $_POST['action'] == 'resendOTPadmin') {

      $status = "on";
      $uniqueid = $admin->data()->admin_uniqueid;

      $sql = "SELECT * FROM adminOtp WHERE admin_unique = '$uniqueid' ";
      $query = Database::getInstance()->query($sql);
      if ($query->count()) {
            $update = "UPDATE adminOtp SET status = 'unused', secure_token = Null WHERE admin_unique = '$uniqueid' ";
          }
            $rndno=rand(10000000, 99999999);//OTP generate
            $token = "TOKEN: "."<h2>".$rndno."</h2>";

            // _____________________________________________________
    $messageBody = "
                <div style='width:80%; height:auto; padding:10px; margin:10px'>

        <p style='color: #fff; font-size: 20px; text-align: center; text-transform: uppercase;margin-top:0px'>One Time Password Verification<br></p>
        <p>Hey $fullname! <br><br>

        A sign in attempt requires further verification because we did not recognize your device. To complete the sign in, enter the verification code on the unrecognized device.

       <br><hr>
        $token <br><hr>

        If you did not attempt to sign in to your account, your password may be compromised. Contact Administrator to create a new, strong password for your ELB account.</p>
                <hr>

       </div>
            ";

    $fromName = 'AG-FPS';
    $email = $adminEmail;
    $subject = 'Email Verification';
    if($mail->sendmail($fromName, $email, $subject, $messageBody))
            $update = "UPDATE adminOtp SET secure_token = '$rndno', dateSent = NOW()  WHERE admin_unique = '$uniqueid' ";
            Database::getInstance()->query($update);
            echo 'success';



  }
