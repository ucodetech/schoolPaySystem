<?php
require_once '../../core/init.php';
$general = new General();
$user = new User();
$show = new Show();
$db = Database::getInstance();
$validate = new Validate();
$user_id = $user->data()->stu_id;
$userlevel = $user->data()->level;
$user_unique = $user->data()->stud_unique_id;
if (isset($_POST['action']) && $_POST['action'] == 'generateCodeNow') {

    if(Input::exists()){
        $validation = $validate->check($_POST, array(
                'term' => array(
                    'required' => true,
                ),
                'level' => array(
                    'required' => true,
                ),
                'amount' => array(
                    'required' => true,
                )
        ));
        if ($validation->passed()){
            $dateyear = date('Y');
            $code = $dateyear.$user_id.rand(1000000000, 9999999999);
            $key = generateKey8();
            $term = Input::get('term');
            if ($general->checkGenerated($user_id, $term)){
                echo $show->showMessage('danger', 'You have generated for this term', 'warning');
                return false;
            }
            try {
                $general->create('generated_code', array(
                    'student_id' => $user_id,
                    'retrieval_code' => $code,
                    'amount' => Input::get('amount'),
                    'term' => Input::get('term'),
                    'level' => Input::get('level'),
                    's_key' => $key
                ));
                echo 'success';
            }catch (Exception $e){
                echo $show->showMessage('danger', $e->getMessage(), 'warning');
                return false;
            }
        }else{
            foreach ($validation->errors() as $error){
                echo $show->showMessage('danger', $error, 'warning');
                return false;
            }
        }
    }
}


if (isset($_POST['action']) && $_POST['action'] == 'fetchGeneratedCodes'){
    $getcodes = $general->getGeneratedCode($user_id);
    if ($getcodes){
        $output = '';
        $output .= '<table class="table table-hover">
                    <thead>
                    <th>#</th>
                    <th>Payment Code</th>
                    <th>Amount</th>
                    <th>Date Generated</th>
                    <th>Status</th>
                    <th>Pay Now</th>
                    <th>Print Acknowledgment</th>
                    </thead><tbody>';
        foreach ($getcodes as $code){
            if ($code->status == 'completed'){
                $paymentBtn = 'Payment Completed';
            }else{
                $paymentBtn = '<a href="payment/paynow/'.$code->s_key.'"  class="btn btn-info btn-sm" type="button">Pay</a>';
            }
            $output .='
                   <tr>
                <td>'.$code->id.'</td>
                 <td>'.$code->retrieval_code.'</td> 
                 <td>'.money($code->amount).'</td> 
                 <td>'.pretty_dates($code->date_generated).'</td> 
                 <td>'.$code->status.'</td>
                  <td>
                  '.$paymentBtn.'
                </td>
                <td>
                  <a href="printacknowlegment.php?payid='.$code->retrieval_code.'" class="btn btn-warning btn-sm printAck">Print Acknowlegment</a>
                </td>
                </tr>
            ';
        }
        $output .= '</tbody></table>';
        echo $output;
    }
}

//not needed for now
//if (isset($_POST['p_id']) && !empty($_POST['p_id'])){
//    $p_id = $_POST['p_id'];
//    $code = $_POST['code_id'];
//   $get =  $db->query("SELECT * FROM generated_code WHERE student_id = '$user_id' AND retrieval_code = '$code' ");
//   if ($get->count()){
//       $row =  $get->first();
//       $output .='
//            <form method="post" action="#" id="paynowForm">
//            <input type="hidden" name="student_id" id="student_id" value="'.$user_id.'">
//            <div class="form-group">
//                <label>Payment Code</label>
//                <input type="text" name="payment_code" id="payment_code" class="form-control" value="'.$row->retrieval_code.'" readonly>
//            </div>
//             <div class="form-group">
//                <label>Payment Amount</label>
//                <input type="text" name="payment_amount" id="payment_amount" class="form-control" value="'.$row->amount.'" readonly>
//            </div>
//             <div class="form-group">
//                <label>Payment Term</label>
//                <input type="text" name="payment_term" id="payment_term" class="form-control" value="'.mb_strtoupper($row->term).'" readonly>
//            </div>
//             <div class="form-group">
//                <label>Payment Level</label>
//                <input type="text" name="payment_level" id="payment_level" class="form-control" value="'.mb_strtoupper($row->level).'" readonly>
//            </div>
//             <div class="form-group">
//                <label>Payer Email</label>
//                <input type="text" name="email" id="email" class="form-control" value="'.$user->data()->stud_email.'" readonly>
//            </div>
//            <div class="form-group">
//                <label>Payer Phone No</label>
//                <input type="text" name="phoneNo" id="phoneNo" class="form-control" value="'.$user->data()->stud_tel.'" readonly>
//            </div>
//
//            <div class="form-group">
//            <a href="paynow.php?payment='.$row->retrieval_code.'&user='.$user_unique.'"  class="btn btn-info btn-block" type="button">Pay</a>
//            </div>
//            </form>
//       ';
//       echo $output;
//   }else{
//       return false;
//   }
//}