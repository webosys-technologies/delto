    <?php defined('BASEPATH') OR exit('No direct script access allowed');
class Index extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form','url','date'));
		$this->load->library(array('session', 'form_validation', 'email'));
		$this->load->database();
		$this->load->model('User_model');
                $this->load->model('Centers_model');
                $this->load->model('Sub_centers_model');
                $this->load->model('Batches_model');
                $this->load->model('Login_model');
                $this->load->model('Cities_model');
                $this->load->library('email');
                 
	}
       
	
	function index()
	{
	    	
//            $this->register();
	}

    function register()
    {
		$this->load->library('form_validation');

		$this->form_validation->set_rules('center_fname','Name','trim|required');
		$this->form_validation->set_rules('center_lname','Last_name','trim|required');
		$this->form_validation->set_rules('center_name','center_name','trim|required');
		$this->form_validation->set_rules('center_email','Email','trim|required|valid_email|callback_check_if_email_exist');
		$this->form_validation->set_rules('center_mobile','Mobile','trim|required|numeric');
		$this->form_validation->set_rules('center_gender','Gender','required');
		$this->form_validation->set_rules('center_password','Password','trim|required|min_length[8]');
		$this->form_validation->set_rules('center_cpassword','Confirm Password','trim|required|matches[center_password]');  
		$this->form_validation->set_rules('center_address','Address','trim|required');
		$this->form_validation->set_rules('center_city','City','trim|required');
		$this->form_validation->set_rules('center_pincode','Pincode','trim|required|numeric');
		$this->form_validation->set_rules('center_state','State','trim|required');   
                
		//validate form input
		if ($this->form_validation->run() == false)
        {
			
		$maha_cities['cities']=$this->Cities_model->getall_cities("Maharashtra");	
                $this->load->view('center/signup',$maha_cities);     
                
                    
        }
        else
		{
                   
            
			list($get_insert,$get_data)=$this->Centers_model->register();
			if($get_insert)
			{
                            $default_sub_center=array('center_id'=>$get_insert,
                                            'sub_center_fullname'=>'Owner Name',
                                            'sub_center_name'=>'Main Sub-Center',
                                            'sub_center_created_at'=>date('Y-m-d'),
                                            'sub_center_status'=>'1');                    
                              $this->Sub_centers_model->sub_center_add($default_sub_center); 
                
                                    $default_batch= array(
                                    'center_id' =>$get_insert,
                                    'batch_name' => 'Default Batch',
                                    'batch_time' => 'Not Set',
                                    'batch_created_at' => date('Y-m-d'),
                                    'batch_status'  =>'1' ,
                                    );               
                              
                              $this->Batches_model->batch_add($default_batch);
                            
				$msg=array(
                                    'title'=>'Delto Center Registration...!',
                                    'data'=>'Your Center Registration Successfully with delto',
                                    'email'=>$get_data['center_email']
                                );
				
                               $result=$this->signup_email($get_data,$msg);
                               $this->verification_email($get_data,$msg);
                               if($result==true)
                                {
                                  $this->session->set_flashdata('signup_success','Registration Successfull,please check email & verify your Account!');
                                //$this->load->view('center/signup');
                                  redirect('center/index/login');
                                }
                                else
                                {                                  
                                     $this->session->set_flashdata('signup_error','please Enter Valid Email...!');
                                     $cities['cities']=$this->Cities_model->getall_cities("Maharashtra");
                                $this->load->view('center/signup',$cities);
                                }

			}
			else
                            {
                           
                            $cities['cities']=$this->Cities_model->getall_cities("Maharashtra");
				$this->load->view('center/signup',$cities);
			}

		
                
             }    
        }
        
              
   function login()
    {
             $center_LoggedIn = $this->session->userdata('center_LoggedIn');
        
        if(isset($center_LoggedIn) || $center_LoggedIn == TRUE)
        {
           redirect('center/dashboard');
        }
        else
        {
           //$email_verify= $this->session->flashdata('email_verify');
//           $this->session->set_flashdata('email_verify','hello');
             $this->load->view('home/home_header');
             $this->load->view('center/login');
             $this->load->view('home/home_footer');
            
        }
    }
    function center_encrypt($email,$hash)
    {
        $data=array('center_verification'=>$hash);
        $center_email=array('center_email'=>$email);
        $this->Centers_model->center_update($center_email,$data);
    }
    function resend_email($center_email)
    {
        $get_data=$this->Centers_model->get_data_by_email($center_email);
        $msg=array(
                                    'title'=>'Delto Center Verification...!',
                                    'data'=>'Your Center Registration Successfully with delto',
                                    'email'=>$center_email
                                );
         
         $center_data=array('center_fname'=>$get_data->center_fname,
             'center_lname'=>$get_data->center_lname,
             'center_mobile'=>$get_data->center_mobile,
             'center_password'=>$get_data->center_password,
             'center_name'=>$get_data->center_name,
             'center_email'=>$center_email);
         
       
        $result=$this->verification_email($center_data,$msg);
        if($result==true)
        {
           $this->session->set_flashdata('signup_success','Verification code send successfully,please check & verify your email!');
                                
             redirect('center/index/login'); 
        }
        else
        {
            redirect('center/index/login');
        }
    }
    
    function verification_email($getdata,$msg)
    {
         $hash= md5( rand(0,1000) );
                 $this->center_encrypt($getdata['center_email'],$hash);
                
                    $headers = "From: no-reply@delto.in";
                    $headers .= ". DELTO-Team" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $to = $msg['email'];
                    $subject = "Delto.in - Account verification";

                    $txt = '<html>
                        <head>
                                            <style>
                                            .button {
                                                background-color: #4CAF50; 
                                                border: none;
                                                color: white;
                                                padding: 20px;
                                                text-align: center;
                                                text-decoration: none;
                                                display: inline-block;
                                                font-size: 16px;
                                                margin: 4px 2px;
                                                cursor: pointer;
                                            }
                                            .button3 {border-radius: 8px;}


                                             .div1 {
                                           
                                                   width: 100%;
                                                   border-radius: 5px;
                                                   background-color: #3c8dbc;
                                                   padding: 20px;
                                               }
                                                .div2 {

                                                   width: 100%;
                                                   border-radius: 5px;
                                                   background-color: #d2d6de;
                                                   padding: 20px;
                                               }
                                               #color{
                                               color:blue;
                                               }
                                            </style>
                                        </head>
                                             <body><div class="div1"><h2>Delto Center Verification...!<h2></div><div class="div2">Dear'." ".$getdata['center_fname']." ".$getdata['center_lname'].',<br><br> We are ready to activate your account.Simply Please Verify your email Address.<br><br><br>
                                            
                                              <center><a  href="'.base_url().'center/index/center_verification/'.$getdata['center_email'].'/'.$hash.'">Click here to verify your account </a></center>'
                            . '          <br>Best Regards,<br>Delto Team<br><a href="http://delto.in">http://delto.in</a><br> </div></body></html>';
                              
                                              
                                            
                 
                       $success=  mail($to,$subject,$txt,$headers); 
                       if($success)
                       {
                          return true;
                       }
    }
    
    function signup_email($getdata,$msg)
    {    
               
                 $hash= md5( rand(0,1000) );
                 $this->center_encrypt($getdata['center_email'],$hash);
                
                    $headers = "From: support@delto.in";
                    $headers .= ". DELTO-Team" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $to = $msg['email'];
                    $subject = "Welcome To Delto.in";

                    $txt = '<html>
                        <head>
                                            <style>
                                            

                                             .div1 {
                                           
                                                   width: 100%;
                                                   border-radius: 5px;
                                                   background-color: #3c8dbc;
                                                   padding: 20px;
                                               }
                                                .div2 {

                                                   width: 100%;
                                                   border-radius: 5px;
                                                   background-color: #d2d6de;
                                                   padding: 20px;
                                               }
                                               #color{
                                               color:blue;
                                               }
                                            </style>
                                        </head>
                                             <body><div class="div1"><h2>'.$msg['title'].'<h2></div><div class="div2">Dear'.$getdata['center_fname'].' '.$getdata['center_lname'].'('.$getdata['center_name'].'),<br>Thank You for sign in with delto.<br><br>You can now login with following login details<br><br>
                                            
                                            <b>center Owner Name :</b>'.$getdata['center_fname']." "
                                             .$getdata['center_lname'].
                                             "<br><b>Center Name :</b>".$getdata['center_name'].
                                             '<br><b>Center Login URL:</b> <a href="http://delto.in/center/index/login">http://delto.in/center/index/login</a>
                                             <br><b>Email Id:</b>'.$getdata['center_email'].
                                              "<br><b>Password :</b>".$getdata['center_password'].
                                              '<br>Best Regards,<br>Delto Team<br><a href="http://delto.in">http://delto.in</a><br></div></body></html>';
                              
                                              
                                            
                 
                       $success=  mail($to,$subject,$txt,$headers); 
                       if($success)
                       {
                          return true;
                       }
//                   
    }
    
    
    
    function otp_email($getdata,$msg)
    {
                 
                    $headers = "From: admin@webosys.com";
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $to = $msg['email'];
                    $subject = "Delto";
                    $txt='<html>
                        <head>
                                            <style>
                                            

                                             .div1 {
                                           
                                                   width: 100%;
                                                   border-radius: 5px;
                                                   background-color: #3c8dbc;
                                                   padding: 20px;
                                               }
                                                .div2 {

                                                   width: 100%;
                                                   border-radius: 5px;
                                                   background-color: #d2d6de;
                                                   padding: 20px;
                                               }
                                               #color{
                                               color:blue;
                                               }
                                            </style>
                                        </head>
                                             <body><div class="div1"><h2>'.$msg['title'].'</h2></div>
                                                                                       
                                             
                            <div class="div2">Dear Customer,<br><b>Center Name :</b>'.$getdata['center_name'].'<br><br>'.$msg['data'].'<b id="color"> '.$getdata['otp'].'</b><br><br>
                                              Best Regards,<br>Delto Team<br><a href="http://delto.in">http://delto.in</a><br>
                                               <a href="'.base_url().'center/index/login">Sign In</a> </div></body></html>';                               
                                      
                                                                                                     
                     $success=  mail($to,$subject,$txt,$headers); 
                       if($success)
                       {
                           
                           redirect('center/index/login');
                       }
                       else
                       {
                            redirect('center/index/login');
                       }
                  
             
    }
    
    
    
     function password_email($getdata,$msg)
    {
       
                
                    $headers = "From: admin@webosys.com";
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $to = $msg['email'];
                    $subject = "Delto";
                    $txt='<html>
                        <head>
                                            <style>
                                            

                                             .div1 {
                                           
                                                   width: 100%;
                                                   border-radius: 5px;
                                                   background-color: #3c8dbc;
                                                   padding: 20px;
                                               }
                                                .div2 {

                                                   width: 100%;
                                                   border-radius: 5px;
                                                   background-color: #d2d6de;
                                                   padding: 20px;
                                               }
                                               #color{
                                               color:blue;
                                               }
                                            </style>
                                        </head>
                                             <body><div class="div1"><h2>'.$msg['title'].'</h2></div>
                                                                                       
                                             
                            <div class="div2">Dear Customer,<br>'.$get_data['center_name'].'<br>'.$msg['data'].'<br>
                            <br><b>Username :</b>'.$msg['email'].
                                              "<br><b>New Password :</b>".$msg['password'].'
                                               <br>Thank You,<br>
                                               Webosys Team,<br>
                                               <a href=http:"'.base_url().'center/index/login">Sign In</a> </div></body></html>';
                                                
                                      
                                            
                                                         
                    $success=  mail($to,$subject,$txt,$headers); 
                      
                     if($success)
                   {
                          $this->session->set_flashdata('signup_success','Password changed successfully...!');
                       redirect('center/index/login');
                   }
             
    }
    
    
    
    
    
    
     public function loginMe()
    {
        
        $this->load->library('form_validation');
        
      //  $this->form_validation->set_rules('email', 'Username', 'callback_username_check');
        $this->form_validation->set_rules('center_email', 'Email', 'required|valid_email|max_length[128]|trim');
        $this->form_validation->set_rules('center_password', 'Password', 'required|max_length[32]');
        
        if($this->form_validation->run() == FALSE)
        {
            $this->login();
        }
        else  
        {
            $center_email = $this->input->post('center_email');
            $center_password = $this->input->post('center_password');
            list($result,$getdata,$valid_email) = $this->Centers_model->loginMe($center_email, $center_password);  
         foreach($getdata as $res)  
         {   
            $status=array('center_status'=>$res->center_status);
         }
        
       if($valid_email>0)
       {
           
         
                
            if($result > 0 && $status['center_status']==1)
            {
               foreach ($getdata as $res)
                {
                    $sessionArray = array(
                        
                         'center_id' => $res->center_id,
                    'center_fname' => $res->center_fname,
                    'center_lname' => $res->center_lname,
                    'center_email' => $res->center_email,
                     'center_name'=>$res->center_name,
                     'center_mobile' =>$res->center_mobile,
                    'center_LoggedIn' => true
                                    );
                                    
                    $this->session->set_userdata($sessionArray);  
                    
                    redirect('center/dashboard');
                  
                  
               }
              }
              
           
            else
            {   
                             
                if($result > 0 && $status['center_status']==0)
                {
                   
                 $this->session->set_flashdata('log_error', 'Account is not activeted yet.');
                 $this->session->set_flashdata('center_email', $center_email);
                }
                else
                {
                    $this->session->set_flashdata('error', 'Email or password mismatch');
                }
                
                redirect('center/index/login');  
            } 
            }
            else
            {
                 $this->session->set_flashdata('error', 'This email id is not registered with us.');
                 redirect('center/index/login'); 
            }
        
        }
    }
    
    
    public function forgotPassword()
    {
        $this->load->view('center/forgotPassword');
    }
    
    /**
     * This function used to generate reset password request link
     */
    function resetPasswordUser()
    {
        $status = '';
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('center_email','Email','trim|required|valid_email');
                
        if($this->form_validation->run() == FALSE)
        {
            //$this->forgotPassword();
             $this->session->set_flashdata('error', 'Invalid Email ID');
//            redirect('center/index/forgotPassword');
       
            echo json_encode(array('status'=>false));
        }
        else 
        {
             
            $center_email= $this->input->post('center_email');
            list($get_result,$get_data)=$this->Centers_model->checkEmailExist($center_email);
         
            if($get_result>0)
            {  
                $msg=array(
                    'title'=>"Delto Center Verification",
                    'data'=>'Your Center Verification OTP is: ',
                    'email'=>$get_data['center_email']
                );
//               
               $this->otp_email($get_data,$msg);
//                 
//                  $this->load->helper('string');
                 $data=array(
                              'id'=>$get_data['center_id'],

                              'email'=> $get_data['center_email'],
                              'activation_id' =>$get_data['otp'],
                              'createdDtm' => date('Y-m-d H:i:s'),
//                               'agent' =>$this->agent->browser(),
//                              'client_ip' => $this->input->ip_address()
                     );

                $save = $this->Centers_model->resetPasswordUser($data); 
                
           
                echo json_encode(array('status'=>true));
                
                
              
            }
            else
            {
                 $this->session->set_flashdata('error', 'This Email ID is not registered with us.');
                echo json_encode(array('status'=>false));

            }
             
        
        } 
    }
    function reset_password()
    {
        $this->form_validation->set_rules('center_password','Password','trim|required|min_length[8]');
        $this->form_validation->set_rules('center_cpassword','Confirm Password','trim|required|matches[center_password]');
        if ($this->form_validation->run() == false)
        {                  $status = 'invalid';
			 setFlashData($status, "Password and Confirm Password Does not match.");
			 redirect('center/index/forgotPassword');
                 
                    
        } 
        else
        {
        
       
        $form_otp=$this->input->post('otp');
        $center_email=$this->input->post('email');
         list($get_otp,$center_data)=$this->Centers_model->otp_verify($center_email);
        $password=$this->input->post('center_password');
        
        if($form_otp==$get_otp['otp'])
        {
//            echo"success";
            $data=array(
                        'center_email'=>$center_email,
                        'password'=>$password
                       
            );
             $result=$this->Centers_model->reset_password($data);
             if($result)
             {
                 $msg=array(
                     'title'=>'Delto center Updation...!',
                     'data'=>'Your delto center password has been changed successfully.',
                     'password'=>$password,
                     'email'=>$center_email,
                     'center_name'=>$center_data['center_name']
                     
                 );
               
                 
                $this->password_email($center_data,$msg);
                 
                redirect('center/index/login');
             }
        }
        else
        {

            $this->session->set_flashdata('error', 'OTP does not match');
            redirect('center/index/forgotPassword');
        }
        }
        
        
    }
    
    
       
     public function signout()
    {
    
        
//        $this->session->sess_destroy();
          $this->session->unset_userdata('center_LoggedIn'); 
        redirect('center/index/login');  
    }
    
        
    
        
        
  function check_if_email_exist($requested_email)
	{
		$this->load->model('Centers_model');
		$email_available=$this->Centers_model->check_if_email_exist($requested_email);

		if($email_available){
                    $this->form_validation->set_message('check_if_email_exist', 'You must select a business');
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

  public function ajax_edit($id)
  {

            $data = $this->Centers_model->get_id($id);
         
            echo json_encode($data);
  }

  public function update_profile()
        {
             $center_LoggedIn = $this->session->userdata('center_LoggedIn');
             
        
        if(isset($center_LoggedIn) || $center_LoggedIn == TRUE)
        {
                $id=$this->session->userdata('center_id');
                      
                      
             
                     
                      $data= array(
                'center_id'=>$this->input->post('center_id'),
                'center_fname' => strtoupper($this->input->post('center_fname')),
                'center_lname' => strtoupper($this->input->post('center_lname')),
                'center_email' => $this->input->post('center_email'),
                'center_mobile' => $this->input->post('center_mobile'),
                'center_address' => $this->input->post('center_address'),                
                
                
                );

                $res=$this->pic_upload($data);
                
                     
                  $this->Centers_model->center_update(array('center_id' => $this->input->post('center_id')), $data);
                   echo json_encode(array("status" => TRUE));
              
                     
                      
            

        }
        else
        {
            $this->load->view('student/login');
            

        }                     
        }
        
         function pic_upload($data)
    {  
       $id=$data['center_id'];
       
                                   $new_file=$data['center_fname'].mt_rand(100,999);
       
         $config = array(
                                  'upload_path' => './profile_pic',
                                  'allowed_types' => 'gif|jpg|png|jpeg',
                                  'max_size' => '7200',
                                  'max_width' => '1920',
                                  'max_height' => '1200',
                                  'overwrite' => false,
                                  'remove_spaces' =>true,
                                  'file_name' =>$new_file 
                              );           
                      
                    
                                  
                       $this->load->library('upload', $config);
                       $this->upload->initialize($config);
                       
                       if (!$this->upload->do_upload('img')) # form input field attribute
                       {
                           if(empty($this->input->post('img')))
                           {
                                $msg="Image size should less than 7MB,Dimension 1920*1200";
                           return $msg; 
                            
                           }
                           else
                           {
                                   return true;                    
                           }
                         
                       }
                       else
                       {
                        
                            $res=$this->Centers_model->get_id($this->input->post('center_id'));
                            if(file_exists($res->center_profile_pic))
                            {
                            unlink($res->center_profile_pic);
                            }
                                               
                           
                            $ext= explode(".",$this->upload->data('file_name'));  
                            $img_name =$new_file.".".end($ext); //video name as path in db
                             $img_path='profile_pic/'.str_replace(' ','_',$img_name);
                          $pic = array(
                              'center_profile_pic' => $img_path,
                            );
            
                                  
                                    
                   $insert =  $this->Centers_model->center_update(array('center_id' =>$id), $pic);
                          
                         return true; 
                                               
                       }

        

            
    }
        function center_verification($email,$hash)
        {
           $verify=$this->Centers_model->email_verification($email,$hash);
           if($verify)
           {
               $this->session->set_flashdata('email_verify','Account Verification Successfull,Please Login...!');
               redirect('center/index/login');
           }
           else
           {
                $this->session->set_flashdata('email_verify','Error...Please Resend link while login...!');
               redirect('center/index/login');
           }
      
                             
        }
        
        function show_cities($state)
        {
           
            $cities=$this->Cities_model->getall_cities(ltrim($state));
            echo json_encode($cities);
        }
        
        
        
        
        public function delete_photo()
        {
            $this->load->helper("file");
         
         if($s)
         {
             echo "success";
         }
         else
         {
             echo"false";
         }
        }	
    
}


  
    
    

