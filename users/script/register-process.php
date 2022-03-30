<?php
require_once  '../../core/init.php';
$user = new User();
$validate = new Validate();
$show = new Show();

if (isset($_POST['action']) && $_POST['action'] == 'register'){
    if (Input::exists()){
        $validation = $validate->check($_POST, array(
            'stud_fname' => array(
                'required' => true,
                'min' => 5,
                'max' => 20
            ),
            'stud_lname' => array(
                'required' => true,
                'min' => 5,
                'max' => 20
            ),
            'level' => array(
                'required' => true,
            ),
            'stud_tel' => array(
                'required' => true,
                 'unique' => 'students'
            ),
            'password' => array(
                'required' => true,
                'min' => 10
            ),
            'cpassword' => array(
              'required' => true,
              'matches' => 'password'
            )
        ));
        if ($validation->passed()){
            $level = Input::get('level');
            $rn = rand(100000, 999999);
            $regNo = $level.'-'.$rn;

            $password = Input::get('password');
            $newPassword = password_hash($password, PASSWORD_DEFAULT);

            $stud_unique_id = 'stu-' . $rn;
            $rn2 = rand(100, 999);
            $webmail = strtolower(Input::get('stud_fname')).$rn2.'@agfps.edu.ng';
            try {
                $user->create(array(
                    'stud_fname' => Input::get('stud_fname'),
                    'stud_lname' => Input::get('stud_lname'),
                    'stud_oname' => Input::get('stud_oname'),
                    'level' => Input::get('level'),
                    'stud_tel' => Input::get('stud_tel'),
                    'stud_password' => $newPassword,
                    'stud_regNo' => $regNo,
                    'stud_unique_id' => $stud_unique_id,
                    'stud_email' => $webmail
                ));

                // ---------------------------------------------------------
                // Load Composer's autoloader

                Session::put('success','You have successfully registered! here is your registration number, it will also serve as your login username! '.$regNo );
                echo  'success';

            }catch( Exception $e){
            echo $show->showMessage('danger', $e->getMessage(), 'warning');
            echo $show->showMessage('danger', $e->getLine(), 'warning');
            echo $show->showMessage('danger', $e->getCode(), 'warning');
            return false;

        }

        }else{
            foreach($validation->errors() as $error){
                echo $show->showMessage('danger', $error, 'warning');
                return false;
            }
        }
    }
}
