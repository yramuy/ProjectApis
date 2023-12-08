<?php
/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author arun kumar,Pavan Kumar,Ramu,Sreekanth
 * @link URL Tutorial link
 */
ini_set("allow_url_fopen", 1);

// define(APPROVE, 2);

class DbHandler {
private $conn;

const COMPLAINT_DRAFT = 0; // POSH
const COMPLAINT_SUBMIT = 1; // POSH
const COMPLAINT_ACCEPTED            =   2;
const COMPLAINT_REJECTED            =   3;
const SHOW_CAUSE_NOTICE             =   4;
const PERSECUTOR_EXPLAINATION       =   5;
const MEETING_OUTPUT_SETTLEMENT     =   6;
const MANAGEMENT_ACTION             =   7;
const MEETING_OUTPUT_NO_SETTLEMENT  =   8;
const INVESTIGATION_DRAFT           =   9;
const INVESTIGATION_SUBMIT          =   10;
const SCHEDULE_MEETING          	=   11;

const COMP_TYPE_ORGANISATION = 1; // POSH
const COMP_TYPE_CONTRACT_OUTSOURCE = 2; // POSH
const COMP_TYPE_TRAINEE = 3; // POSH
const COMP_TYPE_OTHER = 4; // POSH

const HARASSMENT_TYPE_VISUAL = 1; // POSH
const HARASSMENT_TYPE_PHYSICAL = 2; // POSH
const HARASSMENT_TYPE_OTHER = 3; // POSH

const REPORTING_PERSON_YES = 0; // POSH
const REPORTING_PERSON_NO = 1; // POSH

const REPORTING_TYPE_ANONYMOUS = 0; // POSH
const REPORTING_TYPE_VOLUNTARY = 1; // POSH

const SICKLEAVE = 12;// Sick leave
const ANNUALLEAVE = 13; // Annual Leave
const FULLDAY = 0; // full day
const HALFDAY = 1; // Half day
const SPECIFIEDTIME = 1; // Half day
const SUBMITT = 1;
const APPROVE = 2;
const CANCEL = 0;
const REJECT = -1;
const TAKEN = 3;

// USER ROLE IDS

const ADMIN_USER_ROLE_ID             =   1;
const ESS_USER_ROLE_ID               =   2;
const SUPERVISOR                     =   3;
const PROJECTADMIN                   =   4;
const INTERVIEWER                   =   5;
const HIRING_MANAGER_ROLE_ID         =   6;
const REVIEWER_ROLE_ID               =   7;
const FINANCE_MANAGER_ROLE_ID        =   8;
const PROJECTMANAGER                 =   9;
const EMC_USER_ROLE_ID               =   10;
const ENG_USER_ROLE_ID               =   11;
const TECH_USER_ROLE_ID              =   12;
const SHIFT_INCHARGE_USER_ROLE_ID    =   13;
const OPERATOR                       =   14;
const SHIFT_TECHNICIAN_USER_ROLE_ID  =   15;
const HEADOFFICETEAM                 =   17;
const PLANT_MANAGER_USER_ROLE_ID     =   18;
const SHIFT_SUPERVISOR_USER_ROLE_ID  =   19;
const CENTRALSTOREMANAGER            =   20;
const DEPARTMENT_MANAGER_ID          =   22;
const DRIVER_ID                      =   24;
const PROJECTCONTROLLER              =   25;
const SECURITY                       =   30;

const CEO_USER_ROLE_ID               =   31;
const BID_UPLOAD_ROLE_ID             =   32;
const ASSIGNER_ROLE_ID               =   33;
const RESPONDER_ROLE_ID              =   34;
const ICCACTIONOWNER_ROLE_ID         =   35;
const RECRUITER_ROLE_ID              =   37;
const TRAINING_MANAGER_ID            =   34;
const PLANT_MANAGER                  =   39;
const CORPORATE_HEAD                 =   40;
   
    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        require_once dirname(__FILE__) . '/SmsService.php';
        require_once dirname(__FILE__) . '/PasswordHash.php';
        require_once dirname(__FILE__) . '/WhatsappService.php';
        // opening db connection
        date_default_timezone_set('UTC');
        $db = new DbConnect();
        $this->conn = $db->connect();

        // echo $this->conn;die();
        $this->apiUrl = 'https://www.whatsappapi.in/api';
    }
    
	/************function for check is valid api key*******************************/
    function isValidApiKey($token)
    {
		//echo 'SELECT userId FROM registerCustomers WHERE apiToken="'.$token.'"';exit;
		$query ='SELECT userId FROM registerCustomers WHERE apiToken="'.$token.'"';// AND password = $userPass";
		$result = mysqli_query($this->conn, $query);
		$num=mysqli_num_rows($result);
		return $num;
	}

	/************function for check is valid api key*******************************/
    function isValidSessionToken($token,$user_id)
    {
		//echo 'SELECT userId FROM registerCustomers WHERE apiToken="'.$token.'"';exit;
		$query ='SELECT * FROM erp_user_token WHERE userid = "'.$user_id.'" and session_token ="'.$token.'"';// AND password = $userPass";
		$result = mysqli_query($this->conn, $query);
		$num=mysqli_num_rows($result);
		return $num;
	}
		/**
     * Generating random Unique MD5 String for user Api key
     */
    function generateApiKey() {
        return md5(uniqid(rand(), true));
    }
	/** Password Encryption Algorithim*/
	function encrypt($str)
	{
		$key='grubvanapp1#20!8';
		$block = mcrypt_get_block_size('rijndael_128', 'ecb');
		$pad = $block - (strlen($str) % $block);
		$str .= str_repeat(chr($pad), $pad);
		$rst = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str,MCRYPT_MODE_ECB,str_repeat("\0", 16)));
		return str_ireplace('+', '-', $rst); 
	}

   /************function for check is valid api key*******************************/
  function getUserId($token)
    {
		$user_id='';
		// $query = "SELECT userId FROM  registerCustomers WHERE apiToken='$token'"; //table
		// $result=mysqli_query($this->conn, $query);
		// if(mysqli_num_rows($result)>0)
		// {
		//    $row = mysqli_fetch_array($result);
		//    $user_id=$row['userId'];
	 //    }
	   return 6;
	}

	function getUserRoleByUserId($id)
	{
		    $details = array();
			$query = "SELECT u.user_role_id AS id,ur.name AS name, u.emp_number AS empNumber FROM erp_user u LEFT JOIN erp_user_role ur ON u.user_role_id = ur.id WHERE u.id = $id"; //table
			$result=mysqli_query($this->conn, $query);
			if(mysqli_num_rows($result)>0)
			{
			   $row = mysqli_fetch_array($result);
			   $id=$row['id'];
			   $name=$row['name'];
			  
			   $empNumber=$row['empNumber'];
			   
		    $details['id'] = $id;
		    $details['name'] = $name;
		    $details['empNumber'] = $empNumber;
		    }
		   return $details;
	}

	function getUserRoleByEmpNumber($id)
		{
			//echo $id;
			$query = "SELECT u.user_role_id AS id,ur.name AS name, u.emp_number AS empNumber FROM erp_user u LEFT JOIN erp_user_role ur ON u.user_role_id = ur.id WHERE u.emp_number = $id"; //table

			$result=mysqli_query($this->conn, $query);
			if(mysqli_num_rows($result)>0)
			{
			   $row = mysqli_fetch_array($result);
			   $id=$row['id'];
			   $name=$row['name'];
			  
			   $empNumber=$row['empNumber'];
			   
		    }
		    $details = array();
		    $details['id'] = $id;
		    $details['name'] = $name;
		    $details['empNumber'] = $empNumber;
		   return $details;
		}

		function getDepEngineer($ticket_id){
			$query = "SELECT e.emp_number FROM hs_hr_employee e LEFT JOIN erp_user u ON u.emp_number = e.emp_number LEFT JOIN erp_ticket t ON t.user_department_id = e.work_station WHERE u.user_role_id = 11 AND t.id=$ticket_id
			UNION
			SELECT toi.engineer_id FROM erp_ticket t
			LEFT JOIN erp_type_of_issue toi ON t.type_of_issue_id = toi.id
			WHERE t.id = $ticket_id"; //table

			$result=mysqli_query($this->conn, $query);
			if(mysqli_num_rows($result)>0)
			{
			   $row = mysqli_fetch_array($result);
			   $empNumber=$row['emp_number'];
		    }

		    return $empNumber;
		}




		
		function getEmpnameByEmpNumber($emp_number)
		{
			$query = "SELECT concat(emp.emp_firstname,' ',emp.emp_middle_name,' ',emp.emp_lastname) as empname FROM hs_hr_employee emp WHERE emp.emp_number = $emp_number"; //table
			$result=mysqli_query($this->conn, $query);
			if(mysqli_num_rows($result)>0)
			{
			   $row = mysqli_fetch_array($result);
			   $empName=$row['empname'];
		    }

		   
		   return $empName;
		}
		function getEmpCompanyId($emp_number)
		{
			$query = "SELECT emp.business_area as companyId FROM hs_hr_employee emp WHERE emp.emp_number = $emp_number"; //table
			$result=mysqli_query($this->conn, $query);
			if(mysqli_num_rows($result)>0)
			{
			   $row = mysqli_fetch_array($result);
			   $companyId=$row['companyId'];
		    }

		   
		   return $companyId;
		}

		function getEmpBloodGroupName($bldId)
		{
			$bloodName ='';
			if(!empty($nalId)){
				$query = "SELECT * FROM `erp_blood_groups` WHERE id=$bldId"; //table
				$result=mysqli_query($this->conn, $query);
				if(mysqli_num_rows($result)>0)
				{
				   $row = mysqli_fetch_array($result);
				   $bloodName=$row['blood_name'];
			    }
			}

		   
		   return $bloodName;
		}

		function getEmpNationalityName($nalId)
		{
			$nanName ='';
			if(!empty($nalId)){
			$query = "SELECT * FROM `erp_nationality` WHERE id=$nalId"; //table
			$result=mysqli_query($this->conn, $query);
			if(mysqli_num_rows($result)>0)
			{
			   $row = mysqli_fetch_array($result);
			   $nanName=$row['name'];
		    }

			}

		   
		   return $nanName;
		}

		// function attachmentUpload($attachments,$attachmentsTmp_name,$attachmentsSize,$attachment_url)
		// {

		// 	$data =array();
		// 	// echo $_FILES['attachments']['tmp_name'];die();
		// 	$fileNameA =str_replace(" ", "_", $attachments);
		// 	$filename = time().'_'.$fileNameA;
		// 	$success =  move_uploaded_file($_FILES['attachments']['tmp_name'],$attachment_url.$filename,$_FILES['attachments']['tmp_name']);
		// 	if($success){
		// 		$data['status']=1;
		// 	}else{
		// 		$data['status']=0;
		// 	}
		   
		//   return $data;
		// }

	
	
	function generateSessionToken($user_id)
	{
		$data=array();
		$token=$this->generateApiKey();
		$query = "SELECT * FROM erp_user_token WHERE userid = $user_id";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			$token_userid = $row['userid'];
				if($token_userid == $user_id){
					$updatesql ="UPDATE erp_user_token SET session_token='$token' WHERE userid=$user_id";
					if($result2 = mysqli_query($this->conn, $updatesql)){
						$data['session_token'] = $token;
				        $data['status']=1;
					}else{
					    $data['status']=0;
					}
				}else{
					$data['status']=0;
				}
		}
		return $data;
    }


    function getStartToTask($user_id,$task_id,$status)
	{
		$userDetails = $this->getUserRoleByUserId($user_id);
		$empNumber = $userDetails['empNumber'];

		$data=array();

		if(!empty($task_id)){
			
					$updatesql ="UPDATE erp_assign_tasks SET status = ".$status." WHERE id = $task_id";
					if($result2 = mysqli_query($this->conn, $updatesql)){
						$data['message'] = "successfully started task";
				        $data['status']=1;
					}else{
						$data['message'] = "Task start Failed";
					    $data['status']=0;
					}
		}else{
			$data['message'] = "Task Id is invalid";
		    $data['status']=0;
		}
				
		return $data;
    }


    function getTaskData($user_id,$task_id)
	{
		$userDetails = $this->getUserRoleByUserId($user_id);
		$empNumber = $userDetails['empNumber'];

		$data=array();

		$query="SELECT * FROM `erp_assign_tasks` as t where t.id=$task_id order by t.id desc";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				
					do { 						
					
						$data['id'] = $row['id'];
						$data['title'] = $row['title'];
						$data['details'] = $row['details'];
						$data['details'] = $row['details'];
						$data['start_date'] = date('d-m-Y',strtotime($row['start_date']));
						$data['due_date'] = date('d-m-Y',strtotime($row['due_date']));
						$data['priority'] = $row['priority'];
						
						if($row['priority'] == 1){
							$data['priority_name'] = 'Low';
						}else if($row['priority'] == 2){
							$data['priority_name'] = 'Medium';
						}else if($row['priority'] == 3){
							$data['priority_name'] = 'High';
						}else if($row['priority'] == 4){
							$data['priority_name'] = 'Urgent';
						}else{
							$data['priority_name'] = 'General';
						}

						$data['assigned'] = $row['assigned'];
						if($row['assigned']==0){
						$data['assigned_status'] = "Self";
						}else if($row['assigned']==1){
						$data['assigned_status'] = "To Employee";
						}

						$data['assigned_to'] = $row['assigned_to'];
						if(!empty($row['assigned_to'])){
						$data['assigned_to_name'] = $this->getEmpnameByEmpNumber($row['assigned_to']);
						}else{
						$data['assigned_to_name'] = "";
						}

						// $data['assigned_to'] = $row['assigned_to'];
						$data['assigned_by'] = $row['assigned_by'];
						$data['assigned_by_name'] = $this->getEmpnameByEmpNumber($row['assigned_by']);
						$data['assigned_on'] = $row['assigned_on'];
						$data['status'] = $row['status'];

						if($row['status'] == 0){
							$data['status_name'] = 'New';
						}else if($row['status'] == 1){
							$data['status_name'] = 'Started';
						}else if($row['status'] == 2){
							$data['status_name'] = 'work in progress';
						}else if($row['status'] == 3){
							$data['status_name'] = 'completed';
						}else if($row['status'] == 4){
							$data['status_name'] = 'closed';
						}else{
							$data['status_name'] = 'QA Review';
						}

					}while($row = mysqli_fetch_assoc($count));
						$data['task_data']=$data;
						$data['status'] = 1;
					
		}else{
			$data['status'] = 0;
			$data['task_data']= array();
		}
				
		return $data;
    }

    function getTaskWorkHistoryData($user_id,$task_id)
	{
		$userDetails = $this->getUserRoleByUserId($user_id);
		$empNumber = $userDetails['empNumber'];

		$data=array();

		$query="SELECT * FROM `erp_task_progress_status` as t where t.task_id=$task_id order by t.id desc";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				
					do { 						
					
						$data['id'] = $row['id'];
						$data['task_id'] = $row['task_id'];
						$data['completion'] = $row['completion'];
						$data['notes'] = $row['notes'];
						$data['created_by'] = $row['created_by'];
						$data['created_on'] = $row['created_on'];
						
						if(!empty($row['created_by'])){
						$data['created_by_name'] = $this->getEmpnameByEmpNumber($row['created_by']);
						}else{
						$data['created_by_name'] = "";
						}
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['task_data']=$data1;
						$data['status'] = 1;
					
		}else{
			$data['status'] = 0;
			$data['task_data']= array();
		}
				
		return $data;
    }


    function getTaskWorkHistoryLastRecord($user_id,$task_id)
	{
		$userDetails = $this->getUserRoleByUserId($user_id);
		$empNumber = $userDetails['empNumber'];

		$data=array();

		$query="SELECT * FROM `erp_task_progress_status` as t where t.task_id=$task_id order by id desc limit 1";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				
					do { 						
					
						$data['id'] = $row['id'];
						$data['task_id'] = $row['task_id'];
						$data['completion'] = $row['completion'];
						$data['notes'] = $row['notes'];
						$data['created_by'] = $row['created_by'];
						
						if(!empty($row['created_by'])){
						$data['created_by_name'] = $this->getEmpnameByEmpNumber($row['created_by']);
						}else{
						$data['created_by_name'] = "";
						}
						// $data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['task_data']=$data;
						$data['status'] = 1;
					
		}else{
			$data['status'] = 0;
			$data['task_data']= array();
		}
				
		return $data;
    }

    function getStatusToTask($user_id,$task_id,$completion,$notes,$attachment,$statuslevel)
	{
		$userDetails = $this->getUserRoleByUserId($user_id);
		$empNumber = $userDetails['empNumber'];
		date_default_timezone_set("Asia/Kolkata");
		 $assigned_on = date("Y-m-d H:i:s");

		$data=array();

		if(!empty($task_id)){
		
				if(!empty($task_id)){

					$query1 = "INSERT INTO erp_task_progress_status (task_id, completion, notes, created_by, created_on) VALUES (?,?,?,?,?)";

					 if($stmt = mysqli_prepare($this->conn, $query1)){
						 mysqli_stmt_bind_param($stmt, "iisis",$task_id,$completion,$notes,$empNumber,$assigned_on);
						 mysqli_stmt_execute($stmt);

						 $tsId = $this->conn->insert_id;

						 if($tsId){

						 	if($completion == 100){
							$updatesql ="UPDATE erp_assign_tasks SET status =3 WHERE id = $task_id";
							$result2 = mysqli_query($this->conn, $updatesql);

						 	}else{
						 	$updatesql ="UPDATE erp_assign_tasks SET status =2 WHERE id = $task_id";
							$result2 = mysqli_query($this->conn, $updatesql);
						 	}
								
						$data['message'] = "Task work status inserted successfully";
						$data['status']=1;
						}else{
						$data['message'] = "Task work status insertion failed";
						$data['status']=0;
						}



					 }
						 	}
				// }

				if (!empty($task_id)) {
                    if(!empty($statuslevel) && $statuslevel == 1){
                
                		$status =2;
                		$statusquery = "UPDATE erp_assign_tasks SET status = ".$status." WHERE id = $task_id";              
                		$statusqueryResult = mysqli_query($this->conn, $statusquery);
						if($statusqueryResult){
						$data['message'] = "Task work status updated successfully";
						$data['status']=1;
						}else{
						$data['message'] = "Task work status updated failed";
						$data['status']=0;
						}

                    }else if(!empty($statuslevel) && $statuslevel == 3){
               
                		$status =3;
                		$statusquery = "UPDATE erp_assign_tasks SET status = ".$status." WHERE id = $task_id";    
                		$statusqueryResult = mysqli_query($this->conn, $statusquery);
                		if($statusqueryResult){
						$data['message'] = "Task work status completed successfully";
						$data['status']=1;
						}else{
						$data['message'] = "Task work status completed failed";
						$data['status']=0;
						}
                    }
                }


                // if(!empty($tskprgId)){

                //     $upload_dir = '../upload/Tasks/Attchments/';

                //     $countfiles = count($_FILES['frmSaveTask_feedbacktaskfiles']['name']);

                //     // echo $countfiles;
                //     // echo $task_action_log_id;die();
                //     if(!empty($countfiles)){
                //         for($i=0;$i<$countfiles;$i++){

                //         $fileNameA =str_replace(" ", "_", $_FILES['frmSaveTask_feedbacktaskfiles']['name'][$i]);

                //         // echo $fileNameA;die();

                //         $fileSize =$_FILES['frmSaveTask_feedbacktaskfiles']['size'][$i];

                //         $fileType =$_FILES['frmSaveTask_feedbacktaskfiles']['type'][$i];
                //         if(!empty($fileNameA)){
                //         $filename = time().'_'.$fileNameA;

                //         // echo $filename;die();
                //         move_uploaded_file($_FILES['frmSaveTask_feedbacktaskfiles']['tmp_name'][$i],'../upload/Tasks/Attchments/'.$filename);
                //         }

                //         // array_push($filenamesArr, $filename);

                //         $query1 = "INSERT INTO erp_task_action_log_attachment (task_action_log_id, file_name, file_type, file_size, created_by, created_on) VALUES ('".$tskprgId."','".$filename."','".$fileType."','".$fileSize."','".$loggedInEmpNumber."','".$assigned_on."')";     

                //             $statement1 = $conn->prepare($query1);
                //             $result1 = $statement1->execute();


                //         } 
                        

                //     }
                // }

		
			
					
		}else{
			$data['message'] = "Task status updation failed";
		    $data['status']=0;
		}
				
		return $data;
    }


    function getEmailByUsrname($user_name)
		{

			$data=array();

			$query = "SELECT emp.emp_work_email as email FROM hs_hr_employee emp 
						LEFT JOIN erp_user u ON emp.emp_number = u.emp_number WHERE u.user_name = '$user_name'";
			$result=mysqli_query($this->conn, $query);
			
			if(mysqli_num_rows($result)>0)
			{
				/*$row=mysqli_fetch_assoc($count);
				echo "if";
				exit();*/
			   $row = mysqli_fetch_array($result);
			   $email=$row['email'];
			   /*echo $email;
			   exit();*/
		    }

		    else
		    {

				$email = "";

		    }
		   return $email;
		}


		function getUserIdByUsrname($user_name)
		{

			$data=array();

			$query = "SELECT id as userId FROM erp_user WHERE user_name = '$user_name'";
			$result=mysqli_query($this->conn, $query);
			
			if(mysqli_num_rows($result)>0)
			{
				/*$row=mysqli_fetch_assoc($count);
				echo "if";
				exit();*/
			   $row = mysqli_fetch_array($result);
			   $userId=$row['userId'];
			   /*echo $userId;
			   exit();*/
		    }
		    else
		    {
		    		$userId = "";

		    }
		   return $userId;
		}



function getPlayStoreUpdate()
		{

			$data=array();
			$token=$this->generateApiKey();
			$query = "SELECT MAX(sno) AS serialNo,version_code as versionCode, version_name	as versionName FROM playstore_update";

			
			$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{			
			$row=mysqli_fetch_assoc($count);

						    	$data['serialNo'] = $row['serialNo'];
						    	$data['versionCode'] = $row['versionCode'];
						    	$data['versionName'] = $row['versionName'];
						        $data['playStoreDetails'] = $data;
						        $data['status']=1;
		}
			else{
				$data['status']=0;
			}
		   return $data;
		}


		function getEmpDepartmentByEmpNumber($emp_number)
		{
			$query = "SELECT work_station as workStation FROM hs_hr_employee WHERE emp_number = $emp_number"; //table
			$result=mysqli_query($this->conn, $query);
			if(mysqli_num_rows($result)>0)
			{
			   $row = mysqli_fetch_array($result);
			   $workStation=$row['workStation'];
		    }

		   
		   return $workStation;
		}

	// this is for login function
	/*function userLogin($username,$password)
	{
		$data=array();
		$token=$this->generateApiKey();
		$query = "SELECT u.id AS id,u.user_role_id AS user_roleid,u.user_name AS user_name,u.user_password AS user_password, emp.emp_number AS emp_number,emp.emp_mobile AS mobile_number,emp.emp_work_email AS email,
		CONCAT(emp.emp_firstname,' ',emp.emp_lastname) AS emp_name FROM erp_user u LEFT JOIN hs_hr_employee emp ON emp.emp_number = u.emp_number WHERE u.deleted=0 and u.user_name ='$username'";
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{			
			$row=mysqli_fetch_assoc($count);
			$user_name = $row['user_name'];
			$user_password = $row['user_password'];
			$user_id = $row['id'];
			$mobileno = $row['mobile_number'];
			$email = $row['email'];
			$emp_name = $row['emp_name'];

			$verify = password_verify($password, $user_password);
			if($verify){

	    		$result=$this->smsConfig();
	    		$emailresult=$this->emailConfig();
	    		
	    		$rndno=rand(1000, 9999);
	    		$ss = new SmsService();
	    		$ss->otpSms($mobileno,$emp_name,$rndno);

				$query = "SELECT * FROM erp_user_token WHERE userId = $user_id";
				$count=mysqli_query($this->conn, $query);
				$otpnumber = md5($rndno);

				if(mysqli_num_rows($count) > 0)
				{
					$row=mysqli_fetch_assoc($count);
					$token_userid = $row['userid'];
						if($token_userid == $user_id){
							$updatesql ="UPDATE erp_user_token SET userid=$user_id, otp='$otpnumber',session_token='$token' WHERE userid=$user_id";
							if($result2 = mysqli_query($this->conn, $updatesql)){
								$data['session_token'] = $token;
						    	$data['user_id'] = $user_id;
						        $data['userDetails'] = $data;
						        $data['status']=1;
							}else{
							    $data['status']=0;
							}
						}else{
							$data['status']=0;
						}
				}else{
					$sql = "INSERT INTO erp_user_token (userid,otp,session_token) VALUES (?,?,?)";
							
					if($stmt = mysqli_prepare($this->conn, $sql)){
					    // Bind variables to the prepared statement as parameters
					     mysqli_stmt_bind_param($stmt, "iss" , $user_id,$otpnumber,$token);
					    			   
					    // Attempt to execute the prepared statement
					    if(mysqli_stmt_execute($stmt)){
					    	$data['session_token'] = $token;
					    	$data['user_id'] = $user_id;
					        $data['userDetails'] = $data;
					        $data['status']=1;
					    } else{
					        $data['status']=0;
					    }
					} else{
					    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
					    $data['status']=0;
					}	
				}
			}else{
				$data['status']=0;
			}
		}else{
			$data['status']=0;
		}
		return $data;
    }
*/

    function isSupervisor($empnum){
		$data= array();
		$query="SELECT * FROM hs_hr_emp_reportto where erep_sup_emp_number IN ($empnum)";
		$count=mysqli_query($this->conn, $query);
		$row=mysqli_fetch_assoc($count);
		if(isset($row['erep_sup_emp_number'])){
			$supervisor = $row['erep_sup_emp_number'];
		}else{
			$supervisor = 0;
		}
		return $supervisor;
	}

    // this is for login function using curl
	function userLogin($username,$password)
	{
		$data=array();
		$token=$this->generateApiKey();
		$query = "SELECT u.id AS id,u.user_role_id AS user_roleid,u.user_name AS user_name,u.user_password AS user_password, emp.emp_number AS emp_number,emp.emp_mobile AS mobile_number,emp.emp_work_email AS email,emp.business_area AS companyId FROM erp_user u LEFT JOIN hs_hr_employee emp ON emp.emp_number = u.emp_number WHERE u.deleted=0 and u.user_name ='$username'";
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{			
			$row=mysqli_fetch_assoc($count);
			$user_name = $row['user_name'];
			$user_password = $row['user_password'];
			$user_id = $row['id'];
			$user_roleid = $row['user_roleid'];
			$mobileno = $row['mobile_number'];
			$email = $row['email'];
			$companyId = $row['companyId'];
			$emp_num = $row['emp_number'];
			$data['emp_number'] = $emp_num;
			$verify = password_verify($password, $user_password);
			if($verify){

	    		//$result=$this->smsConfig();
	    		//$emailresult=$this->emailConfig();
	    		
	    		$rndno=rand(1000, 9999);

	    		$mobile = $mobileno;

	    		//echo $rndno;
	    		//$ch = curl_init();

	    		 //echo "before url";
	    	//	curl_setopt($ch, CURLOPT_URL, "$result[url]"."authKey=$result[user_name]&senderId=$result[sender_id]&tempId=1470&Phone=$mobile&F1=teejayadmin&F2=$rndno&F3=Plant Maintenance Admin&response=Y");
			  	
	    		//echo "after url";
			    // curl_setopt($ch, CURLOPT_POST, 1);// set post data to true
			    // curl_setopt($ch, CURLOPT_POSTFIELDS,"username=$result[user_name]&pass=$result[password]");   // post data
			    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    // $json = curl_exec($ch);
			    // curl_close ($ch);
			    

				$query = "SELECT * FROM erp_user_token WHERE userId = $user_id";
				$count=mysqli_query($this->conn, $query);
				$otpnumber = md5($rndno);

				if(mysqli_num_rows($count) > 0)
				{
					$row=mysqli_fetch_assoc($count);
					$token_userid = $row['userid'];
						if($token_userid == $user_id){
							$updatesql ="UPDATE erp_user_token SET userid=$user_id, otp='$otpnumber',session_token='$token' WHERE userid=$user_id";
							if($result2 = mysqli_query($this->conn, $updatesql)){
								$data['session_token'] = $token;
						    	$data['user_id'] = $user_id;
						    	$data['user_roleid'] = $user_roleid;
						    	$data['company_id'] = $companyId;
						    	$supervisor = $this->isSupervisor($emp_num);
						    	$userRoleId = $this->getUserRoleByUserId($user_id);

						    	// print_r($userRoleId);die();
						    	// print_r($userRoleId);die();
						    	// echo "string ".$userRoleId['name'];
						    	// echo "string ".$userRoleId['empNumber'];die();
						    	// if($supervisor){
						    	// 	$data['supervisorId'] = $supervisor;
						    	// 	$data['supervisor'] = 'Supervisor';
						    	// }
						    	if($userRoleId['name'] == 'Department Manager'){
						    		$data['role'] = 'Department Manager';
						    	}else{

							    	if(!empty($supervisor)){
							    			$data['role'] = 'Supervisor';
							    	}else{
							    			$data['role'] = $userRoleId['name'];
							    	}
						    	}
						    	
						        $data['userDetails'] = $data;
						        // $data['role'] = $userRoleId['name'];
						        $data['status']=1;
							}else{
							    $data['status']=0;
							}
						}else{
							$data['status']=0;
						}
				}else{
					$sql = "INSERT INTO erp_user_token (userid,otp,session_token) VALUES (?,?,?)";
							
					if($stmt = mysqli_prepare($this->conn, $sql)){
					    // Bind variables to the prepared statement as parameters
					     mysqli_stmt_bind_param($stmt, "iss" , $user_id,$otpnumber,$token);
					    			   
					    // Attempt to execute the prepared statement
					    if(mysqli_stmt_execute($stmt)){
					    	$data['session_token'] = $token;
					    	$data['user_id'] = $user_id;
					    	$data['company_id'] = $companyId;
					    	$supervisor = $this->isSupervisor($emp_num);
						    if($supervisor){
						    	$data['supervisorId'] = $supervisor;
						    	$data['supervisor'] = 'Supervisor';
						    }
					        $data['userDetails'] = $data;
					        $data['status']=1;
					    } else{
					        $data['status']=0;
					    }
					} else{
					    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
					    $data['status']=0;
					}	
				}
			}else{
				$data['status']=0;
			}
		}else{
			$data['status']=0;
		}
		return $data;
    }

// this is for login function
	function loginWtoutPasscode($username,$password,$path)
	{


		$data=array();
		$token=$this->generateApiKey();
		$query = "SELECT u.id AS id,u.user_role_id AS user_roleid,u.user_name AS user_name,u.user_password AS user_password, emp.emp_number AS emp_number,emp.emp_mobile AS mobile_number,emp.emp_work_email AS email,
		CONCAT(emp.emp_firstname,' ',emp.emp_lastname) AS emp_name FROM erp_user u LEFT JOIN hs_hr_employee emp ON emp.emp_number = u.emp_number WHERE u.deleted=0 and u.user_name ='$username'";
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{			
			$row=mysqli_fetch_assoc($count);
			$user_name = $row['user_name'];
			$user_password = $row['user_password'];
			$user_id = $row['id'];
			$mobileno = $row['mobile_number'];
			$email = $row['email'];
			$emp_name = $row['emp_name'];

			$passwordhash = md5($password);

			$verify = password_verify($password, $passwordhash);
			
			/*echo $password.'  '.$user_password.'  '.$passwordhash.'  '.$verify;
							exit();*/
			if($verify){

				
				$query = "SELECT * FROM erp_user_token WHERE userId = $user_id";
				$count=mysqli_query($this->conn, $query);
				
				$otpnumber = "";
				if(mysqli_num_rows($count) > 0)
				{
					$row=mysqli_fetch_assoc($count);
					$token_userid = $row['userid'];
						if($token_userid == $user_id){
							$updatesql ="UPDATE erp_user_token SET userid=$user_id, otp='$otpnumber',session_token='$token' WHERE userid=$user_id";
							if($result2 = mysqli_query($this->conn, $updatesql)){
								$data['session_token'] = $token;
						    	$data['user_id'] = $user_id;
						       
							}else{
							    $data['status']=0;
							}
						}else{
							$data['status']=0;
						}
				}else{

					
					$sql = "INSERT INTO erp_user_token (userid,otp,session_token) VALUES (?,?,?)";
							
					if($stmt = mysqli_prepare($this->conn, $sql)){
					    // Bind variables to the prepared statement as parameters
					     mysqli_stmt_bind_param($stmt, "iss" , $user_id,$otpnumber,$token);
					    			   
					    // Attempt to execute the prepared statement
					    if(mysqli_stmt_execute($stmt)){
					    	$data['session_token'] = $token;
					    	$data['user_id'] = $user_id;
					        
					    } else{
					        $data['status']=0;
					    }
					} else{
					    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
					    $data['status']=0;
					}	
				}



					$query2 = "SELECT u.id AS user_id,u.user_name AS user_name,u.user_role_id AS user_role_id,e.emp_number AS emp_number,CONCAT(e.emp_firstname,' ',e.emp_lastname) AS emp_name,CONCAT(e.emp_firstname,' ',e.emp_lastname) AS reported_by_name,ur.display_name AS role_name ,el.location_id AS location_id,l.name AS location_name,e.plant_id AS plant_id,p.plant_name AS plant_name,e.work_station AS department_id,s.name AS department_name FROM erp_user u LEFT JOIN hs_hr_employee e ON e.emp_number = u.emp_number LEFT JOIN hs_hr_emp_locations el ON el.emp_number = u.emp_number	LEFT JOIN erp_location l ON l.id = el.location_id LEFT JOIN erp_plant p ON p.id = e.plant_id LEFT JOIN erp_subunit s ON s.id = e.work_station LEFT JOIN erp_user_role ur ON ur.id = u.user_role_id WHERE u.id = $user_id";
						$count=mysqli_query($this->conn, $query2);

						if(mysqli_num_rows($count) > 0)
						{
							$row=mysqli_fetch_assoc($count);
							//$data['user_id'] = $row['user_id'];
							$data['user_name'] = $row['user_name'];
							$data['user_role_id'] = $row['user_role_id'];
							$data['emp_number'] = $row['emp_number'];
							$data['emp_name'] = $row['emp_name'];
							$data['reported_by_name'] = $row['reported_by_name'];
							$data['role_name'] = $row['role_name'];
							$data['location_id'] = $row['location_id'];
							$data['location_name'] = $row['location_name'];
							$data['department_id'] = $row['department_id'];
							$data['department_name'] = $row['department_name'];
							$data['plant_id'] = $row['plant_id'];
							$data['plant_name'] = $row['plant_name'];
							$emp_number = $data['emp_number'];
						
								$query3 ="SELECT epic_picture FROM hs_hr_emp_picture WHERE emp_number = $emp_number";
								$count=mysqli_query($this->conn, $query3);
								if(mysqli_num_rows($count) > 0)
								{
									$row1=mysqli_fetch_assoc($count);
									$value = $path.'get_image.php?id='.$emp_number;
									$data['image'] = $value;
								}else{
									$value = $path.'default-photo.png';
									$data['image'] = $value;
								}
						    $data['userDetails']=$data;
							$data['status']=1;
						}
			}else{

				
				$data['status']=0;
			}
		}else{

			
			$data['status']=0;
		}
		return $data;
    }

    //this is for otp verfication function
    function otpverify($user_id,$otp,$path)
	{
		$data=array();
		$query = "SELECT otp FROM erp_user_token WHERE userid = $user_id";
		// echo $query;exit;
		$count=mysqli_query($this->conn, $query);

		$query1 = "SELECT user_name,user_password from erp_user WHERE id = $user_id";
		$count1=mysqli_query($this->conn, $query1);
		if(mysqli_num_rows($count1) > 0)
		{			
			$row=mysqli_fetch_assoc($count1);
			$user_name=$row['user_name'];
			$user_password=$row['user_password'];



			$result=$this->loginWtoutPasscode($user_name,$user_password,$path);
						// $data['otpverified']=$result['userDetails'];
						// $data['status']=1;

		}




		if(mysqli_num_rows($count) > 0)
		{			
			$row=mysqli_fetch_assoc($count);
			$data['otp']=$row['otp'];
			if($row['otp'] == $otp){
				//
				$query = "SELECT u.id AS user_id,u.user_name AS user_name,u.user_role_id AS user_role_id,e.emp_number AS emp_number,CONCAT(e.emp_firstname,' ',e.emp_lastname) AS emp_name,CONCAT(e.emp_firstname,' ',e.emp_lastname) AS reported_by_name,ur.display_name AS role_name ,el.location_id AS location_id,l.name AS location_name,e.plant_id AS plant_id,p.plant_name AS plant_name,e.work_station AS department_id,s.name AS department_name FROM erp_user u LEFT JOIN hs_hr_employee e ON e.emp_number = u.emp_number LEFT JOIN hs_hr_emp_locations el ON el.emp_number = u.emp_number	LEFT JOIN erp_location l ON l.id = el.location_id LEFT JOIN erp_plant p ON p.id = e.plant_id LEFT JOIN erp_subunit s ON s.id = e.work_station LEFT JOIN erp_user_role ur ON ur.id = u.user_role_id WHERE u.id = $user_id";
						$count=mysqli_query($this->conn, $query);

						if(mysqli_num_rows($count) > 0)
						{
							$row=mysqli_fetch_assoc($count);
							$data['user_id'] = $row['user_id'];
							$data['user_name'] = $row['user_name'];
							$data['user_role_id'] = $row['user_role_id'];
							$data['emp_number'] = $row['emp_number'];
							$data['emp_name'] = $row['emp_name'];
							$data['reported_by_name'] = $row['reported_by_name'];
							$data['role_name'] = $row['role_name'];
							$data['location_id'] = $row['location_id'];
							$data['location_name'] = $row['location_name'];
							$data['department_id'] = $row['department_id'];
							$data['department_name'] = $row['department_name'];
							$data['plant_id'] = $row['plant_id'];
							$data['plant_name'] = $row['plant_name'];
							$emp_number = $data['emp_number'];


								$query="SELECT epic_picture FROM hs_hr_emp_picture WHERE emp_number = $emp_number";
								
								$count=mysqli_query($this->conn, $query);
								if(mysqli_num_rows($count) > 0)
								{
									$row1=mysqli_fetch_assoc($count);
									$value = $path.'get_image.php?id='.$emp_number;
									$data['image'] = $value;
								}else{
									$value = $path.'default-photo.png';
									$data['image'] = $value;
								}
						    $data['userDetails']=$data;
							$data['status']=1;
						}
				//
			}else{
				$data['status']=2;
			}
		}
			else{
				$data['status']=0;
			}
		
		return $data;
    }

	//this is for set passcode for the logged in user function
	function setpasscode($user_id,$passcodeentrVal,$path,$datetime,$imeino)
	{
		$data=array();
		$query = "SELECT user_id from erp_passcode WHERE user_id = $user_id";
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{			
			$row=mysqli_fetch_assoc($count);
			$userid = $row['user_id'];

			if($userid == $user_id){
				$updatesql ="UPDATE erp_passcode SET passcode='$passcodeentrVal',imei_number='$imeino', date_time='$datetime' WHERE user_id=$user_id";
				if($result2 = mysqli_query($this->conn, $updatesql)){
						$result=$this->passcodelogin($user_id,$passcodeentrVal,$path);
						$data['userDetails']=$result['userDetails'];
						$data['status']=1;
				}else{
					//echo "ERROR: Could not prepare query: $updatesql. " . mysqli_error($this->conn);
						//echo "failure updated";
					        $data['status']=3;
				}
			}else{
				        $data['status']=0;
			}
		}else{
				$sql = "INSERT INTO erp_passcode (user_id,passcode,imei_number,date_time) VALUES (?,?,?,?)";
			 								
					if($stmt = mysqli_prepare($this->conn, $sql)){
					    // Bind variables to the prepared statement as parameters
					     mysqli_stmt_bind_param($stmt, "isss" , $user_id,$passcodeentrVal,$imeino,$datetime);
					    			   
					    // Attempt to execute the prepared statement
					    if(mysqli_stmt_execute($stmt)){
					    		$result=$this->passcodelogin($user_id,$passcodeentrVal,$path);
					    		$data['userDetails']=$result['userDetails'];
								$data['status']=1;
					    } else{
					    	 //echo "ERROR: Could not prepare query: $stmt. " . mysqli_error($this->conn);
					    	//echo "failure inserted";
					        $data['status']=2;
					    }
					} else{
					    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
					    $data['status']=2;
					}	
			}
		return $data;
    }

    //this is for login with passcode function
    function passcodelogin($user_id,$passcodeentrVal,$path)
	{
		$data=array();
		$query = "SELECT p.passcode AS passcode, u.id FROM erp_passcode p LEFT JOIN erp_user u ON u.id = p.user_id WHERE p.user_id = $user_id";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			$value = $row['passcode'];
				if($passcodeentrVal== $value){

					$query = "SELECT u.id AS user_id,u.user_name AS user_name,u.user_role_id AS user_role_id,e.emp_number AS emp_number,CONCAT(e.emp_firstname,' ',e.emp_lastname) AS emp_name,CONCAT(e.emp_firstname,' ',e.emp_lastname) AS reported_by_name,ur.display_name AS role_name ,el.location_id AS location_id,l.name AS location_name,e.plant_id AS plant_id,p.plant_name AS plant_name,e.work_station AS department_id,s.name AS department_name FROM erp_user u LEFT JOIN hs_hr_employee e ON e.emp_number = u.emp_number LEFT JOIN hs_hr_emp_locations el ON el.emp_number = u.emp_number	LEFT JOIN erp_location l ON l.id = el.location_id LEFT JOIN erp_plant p ON p.id = e.plant_id LEFT JOIN erp_subunit s ON s.id = e.work_station LEFT JOIN erp_user_role ur ON ur.id = u.user_role_id WHERE u.id = $user_id";
						$count=mysqli_query($this->conn, $query);

						if(mysqli_num_rows($count) > 0)
						{
							$row=mysqli_fetch_assoc($count);
							$data['user_id'] = $row['user_id'];
							$data['user_name'] = $row['user_name'];
							$data['user_role_id'] = $row['user_role_id'];
							$data['emp_number'] = $row['emp_number'];
							$data['emp_name'] = $row['emp_name'];
							$data['reported_by_name'] = $row['reported_by_name'];
							$data['role_name'] = $row['role_name'];
							$data['location_id'] = $row['location_id'];
							$data['location_name'] = $row['location_name'];
							$data['department_id'] = $row['department_id'];
							$data['department_name'] = $row['department_name'];
							$data['plant_id'] = $row['plant_id'];
							$data['plant_name'] = $row['plant_name'];
							$emp_number = $data['emp_number'];


								$query="SELECT epic_picture FROM hs_hr_emp_picture WHERE emp_number = $emp_number";
								
								$count=mysqli_query($this->conn, $query);
								if(mysqli_num_rows($count) > 0)
								{
									$row1=mysqli_fetch_assoc($count);
									$value = $path.'get_image.php?id='.$emp_number;
									$data['image'] = $value;
								}else{
									$value = $path.'default-photo.png';
									$data['image'] = $value;
								}
						    $data['userDetails']=$data;
							$data['status']=1;
						}
				}else{
					$data['status']=0;
				}
		}else
		{
			$data['status']=0;	
		}
		return $data;
    }

function loginMobNum($mobnumber,$username)
	{
		// print_r($this->conn);die();
		$data=array();
		$token=$this->generateApiKey();

		if($mobnumber!="")
		{
			
		$query = "SELECT emp.emp_number as empnumber FROM hs_hr_employee emp LEFT JOIN erp_emp_termination t ON emp.emp_number = t.emp_number WHERE emp.emp_mobile ='$mobnumber'";
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{			

			$row=mysqli_fetch_assoc($count);
			$empnumber = $row['empnumber'];

		$query1 = "SELECT usr.id as userid FROM erp_user usr WHERE usr.emp_number = '$empnumber'";
		$count1 =mysqli_query($this->conn, $query1);

			if(mysqli_num_rows($count1) > 0)
		{

			$row=mysqli_fetch_assoc($count1);
			$user_id = $row['userid'];

			$query2 = "SELECT * FROM erp_user_token WHERE userId = $user_id";
				$count2 =mysqli_query($this->conn, $query2);

				if(mysqli_num_rows($count2) > 0)
				{
					$row=mysqli_fetch_assoc($count2);
					$token_userid = $row['userid'];
						if($token_userid == $user_id){
							$updatesql ="UPDATE erp_user_token SET userid=$user_id,session_token='$token' WHERE userid=$user_id";
							if($result2 = mysqli_query($this->conn, $updatesql)){
								$data['session_token'] = $token;
						    	$data['user_id'] = $user_id;
						        $data['userMobLogin'] = $data;
						        $data['status']=1;
							}else{
							    $data['status']=0;
							}
						}else{
							$data['status']=0;
						}
				}else{

					/*echo "else";
					exit();*/
					$sql = "INSERT INTO erp_user_token (userid,session_token) VALUES (?,?)";
							
					if($stmt = mysqli_prepare($this->conn, $sql)){
					    // Bind variables to the prepared statement as parameters
					     mysqli_stmt_bind_param($stmt, "is" , $user_id,$token);
					    			   
					    // Attempt to execute the prepared statement
					    if(mysqli_stmt_execute($stmt)){
					    	$data['session_token'] = $token;
					    	$data['user_id'] = $user_id;
					        $data['userMobLogin'] = $data;
					        $data['status']=1;
					    } else{
					        $data['status']=0;
					    }
					} else{
					    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
					    $data['status']=0;
					}	
				}
			
		}
		else
		{
				
			$data['status']=0;
			
		}	

			
		}else{
			$data['status']=0;
		}

		}
		if($username != ""){

			

			$query3 = "SELECT u.id AS userid,u.user_role_id AS user_roleid,u.user_name AS user_name,u.user_password AS user_password, emp.emp_number AS emp_number,emp.emp_mobile AS mobile_number,emp.emp_work_email AS email FROM erp_user u LEFT JOIN hs_hr_employee emp ON emp.emp_number = u.emp_number WHERE u.deleted=0 and u.user_name ='$username'";
		$count3 = mysqli_query($this->conn, $query3);
		
		if(mysqli_num_rows($count3) > 0)
				{
					//echo "if";
						$row=mysqli_fetch_assoc($count3);
					$token_useridnew = $row['userid'];
					$user_name = $row['user_name'];
					
					if($user_name == $username){

								//echo "in if";

						$usertokenQuery = "SELECT * FROM `erp_user_token` WHERE userid = $token_useridnew";
						$usertokenQueryCount = mysqli_query($this->conn, $usertokenQuery);
						// echo $usertokenQuery;exit();
						if(mysqli_num_rows($usertokenQueryCount) > 0){


						//echo "update";
						$updatesql ="UPDATE erp_user_token SET userid=$token_useridnew,session_token='$token' WHERE userid=$token_useridnew";
						if($result2 = mysqli_query($this->conn, $updatesql)){
							$data['session_token'] = $token;
							$data['user_id'] = $token_useridnew;
							$data['userMobLogin'] = $data;
							$data['status']=1;
						}else{
							$data['status']=0;
						}
					}else{


						//echo $token_useridnew.''.$token;
						$otpVal = '';
																	
						$sql = "INSERT INTO erp_user_token (userid,otp,session_token) VALUES (?,?,?)";
																			
											$stmt = mysqli_prepare($this->conn, $sql);



											// print_r($stmt);die();								
					if($stmt = mysqli_prepare($this->conn, $sql)){
						// Bind variables to the prepared statement as parameters
						mysqli_stmt_bind_param($stmt, "iss" , $token_useridnew,$otpVal,$token);
																	    			   
							// Attempt to execute the prepared statement
							if(mysqli_stmt_execute($stmt)){
								$data['session_token'] = $token;
								$data['user_id'] = $token_useridnew;
								$data['userMobLogin'] = $data;
								$data['status']=1;

							} else{
								$data['status']=0;
							}
					} else{
							//echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
						$data['status']=0;
						}

					}

					}
					else{

						 $data['status']=0;
					}

		}else{
			$data['status']=0;
		}
	}
		return $data;
    }


// loginwithmobNumorusrname
function loginWithMobNumOrUsrname($mobnumber,$username,$password,$path)
	{
		$data=array();
		$token=$this->generateApiKey();

		if(($username!="")&&($password!=""))
		{


						$query = "SELECT u.id AS id,u.user_role_id AS user_roleid,u.user_name AS user_name,u.user_password AS user_password, emp.emp_number AS emp_number,emp.emp_mobile AS mobile_number,emp.emp_work_email AS email FROM erp_user u LEFT JOIN hs_hr_employee emp ON emp.emp_number = u.emp_number WHERE u.deleted=0 and u.user_name ='$username'";
				$count=mysqli_query($this->conn, $query);
				if(mysqli_num_rows($count) > 0)
				{			
					$row=mysqli_fetch_assoc($count);
					$user_name = $row['user_name'];
					$user_password = $row['user_password'];
					$user_id = $row['id'];
					$mobileno = $row['mobile_number'];
					$email = $row['email'];
					$emp_num = $row['emp_number'];

					$verify = password_verify($password, $user_password);
					if($verify){


			    		$query1 = "SELECT u.id AS user_id,u.user_name AS user_name,u.user_role_id AS user_role_id,e.emp_number AS emp_number,CONCAT(e.emp_firstname,' ',e.emp_lastname) AS emp_name,CONCAT(e.emp_firstname,' ',e.emp_lastname) AS reported_by_name,ur.display_name AS role_name ,el.location_id AS location_id,l.name AS location_name,e.plant_id AS plant_id,e.business_area,e.emp_gender,p.plant_name AS plant_name,e.work_station AS department_id,s.name AS department_name FROM erp_user u LEFT JOIN hs_hr_employee e ON e.emp_number = u.emp_number LEFT JOIN hs_hr_emp_locations el ON el.emp_number = u.emp_number	LEFT JOIN erp_location l ON l.id = el.location_id LEFT JOIN erp_plant p ON p.id = e.plant_id LEFT JOIN erp_subunit s ON s.id = e.work_station LEFT JOIN erp_user_role ur ON ur.id = u.user_role_id WHERE u.id = $user_id";

						$count1=mysqli_query($this->conn, $query1);

						if(mysqli_num_rows($count1) > 0)
						{
							$row=mysqli_fetch_assoc($count1);
							$data['user_id'] = $row['user_id'];
							$data['user_name'] = $row['user_name'];
							$data['user_role_id'] = $row['user_role_id'];
							$data['emp_number'] = $row['emp_number'];
							$data['emp_name'] = $row['emp_name'];
							$data['reported_by_name'] = $row['reported_by_name'];
							$data['role_name'] = $row['role_name'];
							$data['location_id'] = $row['location_id'];
							$data['location_name'] = $row['location_name'];
							$data['department_id'] = $row['department_id'];
							$data['department_name'] = $row['department_name'];
							$data['plant_id'] = $row['plant_id'];
							$data['plant_name'] = $row['plant_name'];
							$data['company_id'] = $row['business_area'];
							$data['gender'] = $row['emp_gender'];
							$emp_number = $data['emp_number'];


								$query2="SELECT epic_picture FROM hs_hr_emp_picture WHERE emp_number = $emp_number";
								
								$count2=mysqli_query($this->conn, $query2);
								if(mysqli_num_rows($count2) > 0)
								{
									$row1=mysqli_fetch_assoc($count2);
									$value = $path.'get_image.php?id='.$emp_number;
									$data['image'] = $value;
								}else{
									$value = $path.'default-photo.png';
									$data['image'] = $value;
								}



								$rndno=rand(1000, 9999);

						$query3 = "SELECT * FROM erp_user_token WHERE userId = $user_id";

						$count3=mysqli_query($this->conn, $query3);
						$otpnumber = md5($rndno);

						$supervisor = $this->isSupervisor($emp_num);

                        $userRoleId = $this->getUserRoleByUserId($user_id);

                        if($userRoleId['name'] == 'Department Manager'){
						    		$data['role'] = 'Department Manager';
						    	}else{

							    	if(!empty($supervisor)){
							    			$data['role'] = 'Supervisor';
							    	}else{
							    			$data['role'] = $userRoleId['name'];
							    	}
						    	}
                                
                            // if(!empty($supervisor)){
                            //         $data['role'] = 'Supervisor';
                            // }else{
                            //         $data['role'] = $userRoleId['name'];
                            // }
                            
                           
						    // if($supervisor){
						    // 	$data['supervisorId'] = $supervisor;
						    // 	$data['supervisor'] = 'Supervisor';
						    // }
						    // else
						    // {
						    // 	$data['supervisorId'] = '';
						    // 	$data['supervisor'] = '';

						    // }
						    $data['userDetails']=$data;
                            $data['role'] = $userRoleId['name'];
							$data['status']=1;
						
						}	//mysqli_num_rows($count1) > 0

									else{
								$data['status']=0;
									}
					    
					}  //verify

					else{
						$data['status']=0;
					}
				} //mysqli_num_rows($count) > 0

				else{
					$data['status']=0;
				}



		} //username and pwd != ''

		else if(($mobnumber != "")&&($password != "")){

			/*echo "mobnumber";
			exit();*/

			$query4 = "SELECT u.id AS id,u.user_role_id AS user_roleid,u.user_name AS user_name,u.user_password AS user_password, emp.emp_number AS emp_number,emp.emp_mobile AS mobile_number,emp.emp_work_email AS email FROM erp_user u LEFT JOIN hs_hr_employee emp ON emp.emp_number = u.emp_number WHERE u.deleted=0 and emp.emp_mobile ='$mobnumber'";
		$count4 =mysqli_query($this->conn, $query4);

		if(mysqli_num_rows($count4) > 0)
		{			
					$row=mysqli_fetch_assoc($count4);
					$user_name = $row['user_name'];
					$user_password = $row['user_password'];
					$user_id = $row['id'];
					$mobileno = $row['mobile_number'];
					$email = $row['email'];
					$emp_num = $row['emp_number'];

					$verify = password_verify($password, $user_password);
					if($verify){


									$query1 = "SELECT u.id AS user_id,u.user_name AS user_name,u.user_role_id AS user_role_id,e.emp_number AS emp_number,CONCAT(e.emp_firstname,' ',e.emp_lastname) AS emp_name,CONCAT(e.emp_firstname,' ',e.emp_lastname) AS reported_by_name,ur.display_name AS role_name ,el.location_id AS location_id,l.name AS location_name,e.plant_id AS plant_id,p.plant_name AS plant_name,e.work_station AS department_id,s.name AS department_name FROM erp_user u LEFT JOIN hs_hr_employee e ON e.emp_number = u.emp_number LEFT JOIN hs_hr_emp_locations el ON el.emp_number = u.emp_number	LEFT JOIN erp_location l ON l.id = el.location_id LEFT JOIN erp_plant p ON p.id = e.plant_id LEFT JOIN erp_subunit s ON s.id = e.work_station LEFT JOIN erp_user_role ur ON ur.id = u.user_role_id WHERE u.id = $user_id";

						$count1=mysqli_query($this->conn, $query1);

						if(mysqli_num_rows($count1) > 0)
						{
							$row=mysqli_fetch_assoc($count1);
							$data['user_id'] = $row['user_id'];
							$data['user_name'] = $row['user_name'];
							$data['user_role_id'] = $row['user_role_id'];
							$data['emp_number'] = $row['emp_number'];
							$data['emp_name'] = $row['emp_name'];
							$data['reported_by_name'] = $row['reported_by_name'];
							$data['role_name'] = $row['role_name'];
							$data['location_id'] = $row['location_id'];
							$data['location_name'] = $row['location_name'];
							$data['department_id'] = $row['department_id'];
							$data['department_name'] = $row['department_name'];
							$data['plant_id'] = $row['plant_id'];
							$data['plant_name'] = $row['plant_name'];
							$emp_number = $data['emp_number'];
							$supervisor = $this->isSupervisor($emp_num);
						    if($supervisor){
						    	$data['supervisorId'] = $supervisor;
						    	$data['supervisor'] = 'Supervisor';
						    }
						    else
						    {
						    	$data['supervisorId'] = '';
						    	$data['supervisor'] = '';

						    }

								$query2="SELECT epic_picture FROM hs_hr_emp_picture WHERE emp_number = $emp_number";
								
								$count2=mysqli_query($this->conn, $query2);
								if(mysqli_num_rows($count2) > 0)
								{
									$row1=mysqli_fetch_assoc($count2);
									$value = $path.'get_image.php?id='.$emp_number;
									$data['image'] = $value;
								}else{
									$value = $path.'default-photo.png';
									$data['image'] = $value;
								}

									
										$data['userDetails']=$data;
									$data['status']=1;

							}	//mysqli_num_rows($count1) > 0
											
								} //verify

								else{
								$data['status']=0;
							}

			}//mysqli_num_rows($count4) > 0

			else{
			$data['status']=0;
		}
			

		}

		else
		{
			

			$data['status']=0;


		}
		return $data;
    }

     function sendOtp($user_id)
	{
		$data=array();
		$token=$this->generateApiKey();

		$query1 = "SELECT e.emp_mobile as mobilenumber FROM hs_hr_employee e LEFT JOIN erp_user u ON e.emp_number = u.emp_number WHERE u.id = $user_id";

		/*echo "$query1";
		exit();*/
		$count1=mysqli_query($this->conn, $query1);

		if(mysqli_num_rows($count1) > 0)
				{
					$row=mysqli_fetch_assoc($count1);

					$mobnumber = $row['mobilenumber'];

					/*echo $mobnumber;
					exit();*/

		}
		$query = "SELECT CONCAT(emp.emp_firstname,' ',emp.emp_lastname) AS emp_name,emp.emp_mobile as mobnumber,u.id as userId FROM erp_user u LEFT JOIN hs_hr_employee emp ON emp.emp_number = u.emp_number WHERE emp.emp_mobile = '$mobnumber'";
		$count=mysqli_query($this->conn, $query);

			if(mysqli_num_rows($count) > 0)
				{
					$row=mysqli_fetch_assoc($count);

					$mobileno = $row['mobnumber'];
				$emp_name = $row['emp_name'];
					$result=$this->smsConfig();
	    		$emailresult=$this->emailConfig();
	    		
	    		$rndno=rand(1000, 9999);
	    		$ss = new SmsService();
	    		$ss->otpSms($mobileno,$emp_name,$rndno);
				$otpnumber = md5($rndno);

					$tuser_id = $row['userId'];
						if($tuser_id){
							
							$updatesql ="UPDATE erp_user_token SET otp='$otpnumber' WHERE userid=$tuser_id";
							if($result2 = mysqli_query($this->conn, $updatesql)){
								
						    	$data['userId'] = $tuser_id;
						        $data['sendOtpDetails'] = $data;
						        $data['status']=1;
							}else{
								
							    $data['status']=0;
							}
						}else{
							$data['status']=0;
						}
				}else{
					$sql = "INSERT INTO erp_user_token (otp) VALUES (?)";
							
					if($stmt = mysqli_prepare($this->conn, $sql)){
					    // Bind variables to the prepared statement as parameters
					     mysqli_stmt_bind_param($stmt, "s" , $otpnumber);
					    			   
					    // Attempt to execute the prepared statement
					    if(mysqli_stmt_execute($stmt)){
					    	
					    	$data['userId'] = $user_id;
					        $data['sendOtpDetails'] = $data;
					        $data['status']=1;
					    } else{
					        $data['status']=0;
					    }
					} else{
					    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
					    $data['status']=0;
					}	
				}

		return $data;
    }

    function logout($user_id)
	{
		$data=array();
		$token=$this->generateApiKey();
		$query = "SELECT * FROM erp_user_token WHERE userid = $user_id";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			$token_userid = $row['userid'];
				if($token_userid == $user_id){
					$updatesql ="UPDATE erp_user_token SET session_token=' ' WHERE userid=$user_id";
					if($result2 = mysqli_query($this->conn, $updatesql)){
				        $data['status']=1;
					}else{
					    $data['status']=0;
					}
				}else{
					$data['status']=0;
				}
		}
		return $data;
    }



    //routecause
    function routecause($type_of_issue_id)
	{
		$data= array();
		$query="SELECT * FROM erp_root_cause WHERE type_of_issue_id = $type_of_issue_id AND is_deleted = 0";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do { 						
						$data['id']=$row['id'];
						$data['name']=$row['name'];
						$data['type_of_issue_id']=$row['type_of_issue_id'];
						$data['is_deleted']=$row['is_deleted'];
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['routecause']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}

	//five Star Rating loop
    function fiveStarRating($starJsonAddObj)
    {
    	// echo sizeof($starJsonAddObj["startrating"]);
    	for ($i=0; $i < sizeof($starJsonAddObj["startrating"]); $i++) { 
	         $ticket_id = $starJsonAddObj["startrating"][$i]['ticket_id'];
	         $star_id = $starJsonAddObj["startrating"][$i]['star_id'];
	         $rating = $starJsonAddObj["startrating"][$i]['rating'];

	         $data = $this->fiveStarAdd($ticket_id,$star_id,$rating);
	     }
        
        return $data;
    }

    //star rating based on ticket Id
    function starRatingByTicktId($ticket_id)
	{
		$data= array();
		$query="SELECT fstr.id,fstr.star_id, fstr.ticket_id, fstr.rating,fs.name as star_name 
					FROM erp_five_star_rating fstr
					LEFT JOIN erp_five_s fs ON fs.id = fstr.star_id WHERE ticket_id = $ticket_id";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			do{ 
				$data['id']=$row['id'];
				$data['star_id']=$row['star_id'];
				$data['ticket_id']=$row['ticket_id'];
				$data['rating']=$row['rating'];	
				$data['star_name']=$row['star_name'];	
				$data1[]= $data;
			}while($row = mysqli_fetch_assoc($count));
			$data['RatingByTicketId']=$data1;
			$data['status']=1;
		}else{
			$data['status']=0;
		}
		return $data;  	
	}

	 function partDelete($id)
    {
        $data=array();
   			// Prepare an insert statement
			$query = "DELETE FROM erp_parts WHERE id = $id";
			 
			if($count=mysqli_query($this->conn, $query)){
			    // Bind variables to the prepared statement as parameters
			    
			    			   
			    // Attempt to execute the prepared statement
			    if($count){
			        $data['parts'] = "Part deleted successfully";
			        $data['status']=1;
			    } else{
			        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
			        $data['status']=0;
			    }
			} else{
			    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
			    $data['status']=0;
			}	

		
        return $data;
    }
 

	//dateformat configuration
    function dateFormat()
	{
		$data= array();
		$query="SELECT * FROM hs_hr_config WHERE `hs_hr_config`.`key` = 'admin.localization.default_date_format'";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			$configDate = $row['value'];

		}	

		return $configDate;   
	}



	function equipmentlist($location_id,$plant_id,$department_id,$functional_location_id)
	{
		$data= array();
		$query="SELECT id,name FROM `erp_equipment` WHERE functional_location_id = $functional_location_id and department_id = $department_id and location_id = $location_id and plant_id = $plant_id ORDER BY name ASC";
		// $query="SELECT id,name FROM `erp_equipment` WHERE functional_location_id = $functional_location_id and location_id = $location_id and plant_id = $plant_id ORDER BY name ASC";
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
						$row=mysqli_fetch_assoc($count);
					do{ 
						$data['id']=$row['id'];
						$data['name']=$row['name'];	
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 					
						$data['equipmentlist']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}

	//location
    function location()
	{
		$data= array();
		$query="SELECT id, name FROM erp_location";
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['id']=$row['id'];
						$data['name']=$row['name'];	
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['location']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}


//equipmentiddetails
    function equipmentiddetails($eqipmentid)
	{
		$data= array();
		$query="SELECT id,name FROM erp_equipment WHERE id = $eqipmentid";
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			$data['id']=$row['id'];
			$data['name']=$row['name'];					
			$data['equipmentiddetails']=$data;
			$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}


//typeofissue
    function typeofissue($eqipmentid)
	{
		$data= array();
		$query="SELECT i.id,i.name FROM erp_type_of_issue i LEFT JOIN erp_equipment e ON e.equipment_type_id = i.equipment_type_id  LEFT JOIN erp_category_type ct ON ct.id = e.category_type_id LEFT JOIN erp_category_sub_type cst ON cst.id = e.category_sub_type_id LEFT JOIN erp_equipment_type et ON et.id = i.equipment_type_id LEFT JOIN erp_equipment_sub_type est ON est.id = i.equipment_sub_type_id WHERE e.id = $eqipmentid AND i.category_type_id = e.category_type_id AND i.category_sub_type_id = e.category_sub_type_id AND i.equipment_type_id = e.equipment_type_id AND i.equipment_sub_type_id = e.equipment_sub_type_id ORDER by name ASC";
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			do{ 
				$data['id']=$row['id'];
				$data['name']=$row['name'];		
				$data1[]= $data;
			}while($row = mysqli_fetch_assoc($count));
								
			$data['typeofissue']=$data1;
			$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;       
	}
	
//plantlst
    function plantlst($locationid)
	{
		$data= array();
		$data1= array();
		$query="SELECT id, plant_name FROM erp_plant WHERE location_id = $locationid";
		$count=mysqli_query($this->conn, $query);
		
		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);

			do{ 
				$data['id']=$row['id'];
				$data['plant_name']=$row['plant_name'];	
				$data1[]= $data;
			}while($row = mysqli_fetch_assoc($count));	

			$data['plantlst']=$data1;
			$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;     
	}

	//ticketAdd
	function ticketAdd($locationId,$plantId,$usrdeptId,$notifytoId,$statusId,$funclocId,$eqipmntId,$typofisId,$subject,$description,$prtyId,$svrtyId,$reportedBy,$submitted_by_emp_number,$submitted_by_name,$reportedOn,$submitted_on,$user_id,$attachmentId)
    {
        $data=array();

     /*  $base64_string = "";
       $image = $this->base64_to_jpeg($base64_string,'tmp.jpg' );
       echo $image;
       exit();*/

		$query="SELECT ui.last_id FROM hs_hr_unique_id ui WHERE ui.table_name = 'erp_ticket' AND ui.field_name='job_id'";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);

			$data['last_id']=$row['last_id'];
			$jobinc = $row['last_id']+1;

			$sql = "UPDATE hs_hr_unique_id SET last_id = ".$jobinc." WHERE table_name = 'erp_ticket' AND field_name='job_id'";
			if(mysqli_query($this->conn, $sql)){

				$query="SELECT ui.last_id FROM hs_hr_unique_id ui WHERE ui.table_name = 'erp_ticket' AND ui.field_name='job_id'";
				$count=mysqli_query($this->conn, $query);
					if(mysqli_num_rows($count) > 0){
						$row=mysqli_fetch_assoc($count);
						$prefix = date('Ymd');
						$NewJobId = $row['last_id'];
						$jobIdNew = $prefix . str_pad($NewJobId, 3, "0", STR_PAD_LEFT);
					}

				$source = 1;
	   			// Prepare an insert statement
				$sql = "INSERT INTO erp_ticket (job_id,location_id,plant_id,user_department_id,notify_to,status_id,functional_location_id,equipment_id,type_of_issue_id,subject,description,priority_id,severity_id,reported_by,submitted_by_name,submitted_by_emp_number,reported_on,submitted_on,source) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				 
				/* echo $reportedOn." , ".$submitted_on;
				 exit();*/
				if($stmt = mysqli_prepare($this->conn, $sql)){
				    // Bind variables to the prepared statement as parameters
				     mysqli_stmt_bind_param($stmt, "siiisiiiissiiisissi" ,$jobIdNew,$locationId,$plantId,$usrdeptId,$notifytoId,$statusId,$funclocId,$eqipmntId,$typofisId,$subject,$description,$prtyId,$svrtyId,$reportedBy,$submitted_by_name,$submitted_by_emp_number,$reportedOn,$submitted_on,$source);
				    			   
					    // Attempt to execute the prepared statement
					    if(mysqli_stmt_execute($stmt)){

				    		$query="SELECT MAX(id) AS ticket_id  FROM erp_ticket";
							$count=mysqli_query($this->conn, $query);
								
								if(mysqli_num_rows($count) > 0)	{
											$row=mysqli_fetch_assoc($count);
											$data['ticket_id'] = $row['ticket_id'];
											$ticket_id = $data['ticket_id'];
											$result=$this->logAdd($user_id,$ticket_id,' ',' ',' ',' ',$user_id,$statusId,$prtyId,$svrtyId,$subject,' ',' ',' ',$submitted_by_name,$submitted_by_emp_number,' ',' ',$submitted_on);
											$sql = "UPDATE erp_ticket_attachment SET ticket_id = ".$ticket_id." WHERE ticket_id = ".$attachmentId;
											mysqli_query($this->conn, $sql);
								}

				        $data['ticketid'] = $data['ticket_id'];
				        $data['status']=1;
					    } else{
					        echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
					        $data['status']=0;
					    }
				} else{
				    echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
				    $data['status']=0;
					}	

			} else {
					    echo "ERROR: Could not able to execute $sql. " . mysqli_error($this->conn);
					     $data['status']=0;
			}
		} else{
				    echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
				    $data['status']=0;
		}
		
        return $data;
    }


    //ActionlogAdd
function logAdd($user_id,$ticket_id,$accepted_by,$rejected_by,$forward_from,$forward_to,$created_by_user_id,$status_id,$priority_id,$severity_id,$comment,$machine_status,$assigned_date,$due_date,$submitted_by_name,$submitted_by_emp_number,$root_cause_id,$response_id,$submitted_on)
    {

    	$empresult=$this->engLists();

		for ($i=0; $i < sizeof($empresult['englist']) ; $i++) { 
	    	$engList[] = $empresult['englist'][$i];
	    	//to convert Array into string the following implode method is used
	    	$engLists = implode(',', $engList);
	    }
		$minquery = "SELECT * FROM erp_ticket_acknowledgement_action_log WHERE id IN (SELECT MIN(id) FROM erp_ticket_acknowledgement_action_log WHERE forward_from IN ($engLists) AND ticket_id = $ticket_id)";

		$rowcount2 = mysqli_query($this->conn, $minquery);

		if(mysqli_num_rows($rowcount2) > 0)
			{
				$row2 = mysqli_fetch_assoc($rowcount2);
					$datacount2=$row2['forward_from'];
			}

        $data=array();

        $userDetails = $this->getUserRoleByUserId($user_id);
        $empNumber = $userDetails['empNumber'];
		$roleId = $userDetails['id'];

    	if($status_id == 14)
    	{
    		if($roleId == 11){
    			$forward_from = $empNumber;
	    		$forward_to = $datacount2;
	    		$accepted_by = 0;
    		}else{
    			$forward_from = $accepted_by;
	    		$forward_to = $this->getAcceptedEngId($ticket_id);
	    		$accepted_by = 0;
    		}
    	}

   			// Prepare an insert statement

    	$source = 1;     
     	$sql = "INSERT INTO erp_ticket_acknowledgement_action_log (ticket_id,accepted_by,rejected_by,forward_from,forward_to,created_by_user_id,status_id,priority_id,severity_id,comment,machine_status,submitted_by_name,submitted_by_emp_number,root_cause_id,response_id,submitted_on,source) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			 
			if($stmt = mysqli_prepare($this->conn, $sql)){
				
			    // Bind variables to the prepared statement as parameters
			     mysqli_stmt_bind_param($stmt, "iiiiiisiisisiiisi" ,$ticket_id,$accepted_by,$rejected_by,$forward_from,$forward_to,$created_by_user_id,$status_id,$priority_id,$severity_id,$comment,$machine_status,$submitted_by_name,$submitted_by_emp_number,$root_cause_id,$response_id,$submitted_on,$source);
			    			   
			    // Attempt to execute the prepared statement
			    if(mysqli_stmt_execute($stmt)){
			       
			        if($status_id == 2 || $status_id == 4)
    				 {

     				$updatesql = "UPDATE erp_ticket SET status_id = 3 WHERE id = $ticket_id";
     					if($result2 = mysqli_query($this->conn, $updatesql)){
						/*$data['session_token'] = $token;*/
						if($status_id == 2)
						{
							$data['log'] = "Job status changed to Assigned";
				        	$data['status']=1;
						}
						else
						{
							$data['log'] = "Job status changed to Accepted";
				        	$data['status']=1;
						}
						
						}
							else{
							//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
					    	$data['status']=0;
							}

     				}

     				else if($status_id == 14)
    				 {

    				 		/*echo $status_id;
    				 		echo $ticket_id;
    				 		exit;*/
     				$updatesql = "UPDATE erp_ticket SET status_id = 14 WHERE id = $ticket_id";
     					if($result2 = mysqli_query($this->conn, $updatesql)){
						/*$data['session_token'] = $token;*/
						$data['log'] = "Job Status Changed to Resolved";
				        $data['status']=1;
						}
							else{
							//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
					    	$data['status']=0;
							}

     				}

     				else if($status_id == 12)
    				 {

    				 		/*echo $status_id;
    				 		echo $ticket_id;
    				 		exit;*/
     				$updatesql = "UPDATE erp_ticket SET status_id = 12 WHERE id = $ticket_id";
     					if($result2 = mysqli_query($this->conn, $updatesql)){
						/*$data['session_token'] = $token;*/
						$data['log'] = "Job Status Changed to Paused";
				        $data['status']=1;
						}
							else{
							//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
					    	$data['status']=0;
							}

     				}

     				else if($status_id == 6)
    				 {

    				 		/*echo $status_id;
    				 		echo $ticket_id;
    				 		exit;*/
     				$updatesql = "UPDATE erp_ticket SET status_id = 6 WHERE id = $ticket_id";
     					if($result2 = mysqli_query($this->conn, $updatesql)){
						/*$data['session_token'] = $token;*/
						$data['log'] = "Job Status Changed to Reopened";
				        $data['status']=1;
						}
							else{
							//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
					    	$data['status']=0;
							}

     				}

     				else if($status_id == 13)
    				 {

    				 		/*echo $status_id;
    				 		echo $ticket_id;
    				 		exit;*/
     				$updatesql = "UPDATE erp_ticket SET status_id = 13 WHERE id = $ticket_id";
     					if($result2 = mysqli_query($this->conn, $updatesql)){
						/*$data['session_token'] = $token;*/
						$data['log'] = "Job Status Changed to Resumed";
						$data['forward_to']=1;
				        $data['status']=1;
						}
							else{
							//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
					    	$data['status']=0;
							}

     				}

     				else if($status_id == 7 || $status_id == 8 || $status_id == 9 || $status_id == 15 || $status_id == 10 || $status_id == 5)
    				 {

    				 		/*echo $status_id;
    				 		echo $ticket_id;
    				 		exit;*/
     				$updatesql = "UPDATE erp_ticket SET status_id = $status_id WHERE id = $ticket_id";
     					if($result2 = mysqli_query($this->conn, $updatesql)){
						/*$data['session_token'] = $token;*/
						$data['log'] = "Job Status Changed";
				        $data['status']=1;
						}
							else{
							//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
					    	$data['status']=0;
							}

     				}

     				else if($status_id == 16)
    				 {


    				 		$userDetails = $this->getUserRoleByUserId($user_id);
							$empNumber = $userDetails['empNumber'];

    				 		/*echo $empNumber;
    				 		exit();*/
							$rolid = $userDetails['id'];

							//echo $rolid;

							//exit();
							if($rolid == 12)
							{

											$updatesql = "UPDATE erp_ticket SET status_id = 2 WHERE id = $ticket_id";
			     					if($result2 = mysqli_query($this->conn, $updatesql)){
									/*$data['session_token'] = $token;*/
									$data['log'] = "Job Status Changed to Rejected";
							        $data['status']=1;
										}
										else{
										//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
								    	$data['status']=0;
										}
							}
							else
							{

								$updatesql = "UPDATE erp_ticket SET status_id = $status_id WHERE id = $ticket_id";
			     					if($result2 = mysqli_query($this->conn, $updatesql)){
									/*$data['session_token'] = $token;*/
									$data['log'] = "Job Status Changed to Rejected";
							        $data['status']=1;
										}
										else{
										//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
								    	$data['status']=0;
										}

							}
     				

     				}

     				else
     				{
     					$data['log'] = "Job Created Successfully";
			        	$data['status']=1;

     				}

			        
			    } else{
			        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
			        $data['status']=0;
			    }
			} else{
			    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
			    $data['status']=0;
			}
     
     
        return $data;
    }

     function engLists()
    {
        $data=array();
  			
		$query="SELECT e.emp_number as emp_number,concat(e.emp_firstname,e.emp_lastname) as name, u.user_role_id as role_id,l.location_id as location_id,e.plant_id as plant_id ,e.work_station as work_station FROM hs_hr_employee e LEFT JOIN hs_hr_emp_locations l ON l.emp_number = e.emp_number LEFT JOIN erp_user u ON u.emp_number = e.emp_number WHERE u.user_role_id = 11";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
					do{ 
						$data1[] = $row['emp_number'];
					}while($row = mysqli_fetch_assoc($count));
						$data['englist']=$data1;
						$data['status'] = 1;
		}
		return $data;
    }


    function techLists()
    {
        $data=array();
  			
		$query="SELECT e.emp_number as emp_number, u.user_role_id as role_id,l.location_id as location_id,e.plant_id as plant_id ,e.work_station as  work_station FROM hs_hr_employee e LEFT JOIN hs_hr_emp_locations l ON l.emp_number = e.emp_number LEFT JOIN erp_user u ON u.emp_number = e.emp_number WHERE u.user_role_id = 12";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
					do{ 
						$data1[] = $row['emp_number'];
					}while($row = mysqli_fetch_assoc($count));
						$data['techlist']=$data1;
						$data['status'] = 1;
		}
		return $data;
    }

//tktcnvrstnsAdd
    function tktcnvrstnsAdd($ticket_id,$emp_number,$date_time,$comments)
    {
        $data=array();
   			// Prepare an insert statement
			$sql = "INSERT INTO erp_ticket_conversations (ticket_id,emp_number,date_time,comments) VALUES (?,?,?,?)";
			 
			if($stmt = mysqli_prepare($this->conn, $sql)){
			    // Bind variables to the prepared statement as parameters
			     mysqli_stmt_bind_param($stmt, "iiss" , $ticket_id,$emp_number,$date_time,$comments);
			    			   
			    // Attempt to execute the prepared statement
			    if(mysqli_stmt_execute($stmt)){
			        $data['tktcnvrstns'] = "Ticket conversation added successfully";
			        $data['status']=1;
			    } else{
			        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
			        $data['status']=0;
			    }
			} else{
			    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
			    $data['status']=0;
			}	

		
        return $data;
    }

    //tktcnvrstns
    function tktconvrstns($ticket_id)
    {
        $data=array();
   			
		//getUser Details
		$query="SELECT cnvs.emp_number as emp_number,CONCAT(emp.emp_firstname,emp.emp_lastname) as empname, cnvs.ticket_id as ticket_id, cnvs.date_time as date_time, cnvs.comments as comments FROM erp_ticket_conversations cnvs JOIN hs_hr_employee emp ON emp.emp_number = cnvs.emp_number WHERE ticket_id = $ticket_id";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
						$row=mysqli_fetch_assoc($count);
					do{ 						
						$data['emp_number']=$row['emp_number'];
						$data['empname']=$row['empname'];
						$data['ticket_id']=$row['ticket_id'];
						$data['date_time']=$row['date_time'];
						$data['comments']=$row['comments'];
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['tktconvrstns']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
        return $data;
    }


	//alltktconvrstns
    function alltktconvrstns()
    {
        $data=array();
   			
		//getUser Details
		$query="SELECT cnvs.emp_number as emp_number,CONCAT(emp.emp_firstname,emp.emp_lastname) as empname, cnvs.ticket_id as ticket_id, cnvs.date_time as date_time, cnvs.comments as comments FROM erp_ticket_conversations cnvs JOIN hs_hr_employee emp ON emp.emp_number = cnvs.emp_number JOIN erp_ticket tkt ON tkt.id = cnvs.ticket_id";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
						$row=mysqli_fetch_assoc($count);
					do{ 						
						$data['emp_number']=$row['emp_number'];
						$data['empname']=$row['empname'];
						$data['ticket_id']=$row['ticket_id'];
						$data['date_time']=$row['date_time'];
						$data['comments']=$row['comments'];
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['alltktconvrstns']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
        return $data;
    }


    //EnMNewTasks($plant_id)
    function EnMNewTasks($user_id,$location_id)
    {
        $data=array();
		//getUser Details
       	$i=0;
		$empNumber = $this->getEmpnumberByUserId($user_id);
       	$empresult=$this->employeeDetails($empNumber);

       	$plantId = $empresult['plant_id'];
  //      	echo "<pre>";
  //      	print_r($empresult);
		// exit();
		$query="SELECT o.id AS id, o.job_id AS job_id, o.location_id AS location_id, o.plant_id AS plant_id, o.user_department_id AS user_department_id, o.functional_location_id AS functional_location_id, o.equipment_id AS equipment_id, o.type_of_issue_id AS type_of_issue_id, o.status_id AS status_id, o.sla AS sla, o.subject AS subject, o.description AS description, o.priority_id AS priority_id, o.severity_id AS severity_id, o.reported_by AS reported_by, o.reported_on AS reported_on, o.submitted_by_name AS submitted_by_name, o.submitted_by_emp_number AS submitted_by_emp_number, o.submitted_on AS submitted_on, o.modified_by_name AS modified_by_name, o.modified_by_emp_number AS modified_by_emp_number, o.modified_on AS modified_on, o.is_preventivemaintenance AS is_preventivemaintenance, o.is_deleted AS is_deleted FROM erp_ticket o LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id WHERE (o2.status_id IN (1, 6) AND o.location_id = $location_id AND o.plant_id = $plantId AND o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id) AND o.is_deleted = 0) ORDER BY o.id DESC";

		

		

		$count=mysqli_query($this->conn, $query);

		
		if(mysqli_num_rows($count) > 0)
		{
			
						$row=mysqli_fetch_assoc($count);
					do{ 	

						$i=$i+1;
						$data['sno']=$i;				
						$data['id']=$row['id'];
						$data['job_id']=$row['job_id'];
						$text = $row['subject'];
						$data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);
						
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['emTasks']=$data1;
						$data['status']=1;
							
		}else{
				/*$data1[] = $data;
				$data['emTasks']=$data1;*/
				$data['status']=0;
				$data['count']=0;	
			}
        return $data;
    }

//EnMENgTechTasks($plant_id)
    function EnMEngTechTasks($user_id,$type_id)
    {
        $data=array();
        	$response = array();
			$db = new DbHandler();

			$j = 0;

			$empNumber = $this->getEmpnumberByUserId($user_id);
			$locationid = $this->getLocationByUserId($user_id);
       		$empresult1=$this->employeeDetails($empNumber);
			$plantId = $empresult1['plant_id'];
			$usrRolId =$this->userIdbyUserRoleId($user_id);
			// echo $usrRolId;
			// exit();
			if($usrRolId == 11 &&  $type_id == 12){	
				$empresult=$this->subordinateByEmpList($empNumber);

				if($empresult = ' ')
				{
					
			        	//echo $empNumber;
			        	$empLists = $empNumber;

					}
					
				else
				{

					for ($i=0; $i < sizeof($empresult['emplist']) ; $i++) { 
			        	$empList[] = $empresult['emplist'][$i];
			        	//to convert Array into string the following implode method is used
			        	$empLists = implode(',', $empList);
			        }

				}
						
			}else{
					$empresult=$this->empList($type_id);
	        for ($i=0; $i < sizeof($empresult['emplist']) ; $i++) { 
	        	$empList[] = $empresult['emplist'][$i];
	        	//to convert Array into string the following implode method is used
	        	$empLists = implode(',', $empList);
	        }
			}
	        

			

			
			
	        // print_r($empLists1);
	        // exit();
	        
	        	
	        if($type_id == 11){
	        	$query = "SELECT o.id AS id, o.job_id AS job_id, o.location_id AS location_id, o.plant_id AS plant_id, o.user_department_id AS user_department_id, o.functional_location_id AS functional_location_id, o.equipment_id AS equipment_id, o.type_of_issue_id AS type_of_issue_id, o.status_id AS status_id, o.sla AS sla, o.subject AS subject, o.description AS description, o.priority_id AS priority_id, o.severity_id AS severity_id, o.reported_by AS reported_by, o.reported_on AS reported_on, o.submitted_by_name AS submitted_by_name, o.submitted_by_emp_number AS submitted_by_emp_number, o.submitted_on AS submitted_on, o.modified_by_name AS modified_by_name, o.modified_by_emp_number AS modified_by_emp_number, o.modified_on AS modified_on, o.is_preventivemaintenance AS is_preventivemaintenance FROM erp_ticket o LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id WHERE ((o2.accepted_by IN ($empLists) OR o2.forward_to IN ($empLists)) AND o.location_id = $locationid AND o.plant_id = $plantId AND o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id) AND o.is_deleted = 0) ORDER BY o.id DESC";

	        	/*echo $query;
	        	exit();*/
	        }
	        else if($type_id == 12){
	        	$query = "SELECT o.id AS id, o.job_id AS job_id, o.location_id AS location_id, o.plant_id AS plant_id, o.user_department_id AS user_department_id, o.functional_location_id AS functional_location_id, o.equipment_id AS equipment_id, o.type_of_issue_id AS type_of_issue_id, o.status_id AS status_id, o.sla AS sla, o.subject AS subject, o.description AS description, o.priority_id AS priority_id, o.severity_id AS severity_id, o.reported_by AS reported_by, o.reported_on AS reported_on, o.submitted_by_name AS submitted_by_name, o.submitted_by_emp_number AS submitted_by_emp_number, o.submitted_on AS submitted_on, o.modified_by_name AS modified_by_name, o.modified_by_emp_number AS modified_by_emp_number, o.modified_on AS modified_on FROM erp_ticket o LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id WHERE ((o.status_id = 3 OR o2.status_id = 7 OR o2.status_id = 9 OR o2.status_id = 8) AND (o2.accepted_by IN (4, 18, 19, 20, 64, 192, 209) OR o2.forward_to IN (4, 18, 19, 20, 64, 192, 209)) OR o2.forward_to IN (4, 18, 19, 20, 64, 192, 209) AND o.location_id = 3 AND o.plant_id = 1 AND o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id) AND o.is_deleted = 0) GROUP BY o.id ORDER BY o.id DESC";
	        	/*echo $query;
	        	exit();*/
	        }else{
	        	$query = "SELECT * from erp_ticket where id=null";
	        }
		
		//echo $query;
		//exit();
   		  
   		  				$configDate = $this->dateFormat();
							$count=mysqli_query($this->conn, $query);
						
							if(mysqli_num_rows($count) > 0)
							{
											$row=mysqli_fetch_assoc($count);
										do{ 		

											$j = $j+1;

											$data['sno']=$j;				
											$data['id']=$row['id'];
											$data['job_id']=$row['job_id'];
											$text = $row['subject'];
											$data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);
											
											
											$data1[] = $data;
										}while($row = mysqli_fetch_assoc($count));
											
											$data['taskslist']=$data1;
											$data['status']=1;
												
							}else{
									$data['status']=0;
							}               	
                	

		return $data;
    }
  
//EngNewTskEmpnum($emp_number)
   function EngNewTskEmpnum($emp_number)
    {
        $data=array();
    $j = 0;
    $result=$this->multipledeptList($emp_number);

    $empresult=$this->employeeDetails($emp_number);

    $issueresult=$this->typeofissuelist($emp_number);

    $multidept[] = $empresult['work_station'];
    $multi_dept = implode(',', $multidept);
    if($result['status']== 1){
        for ($i=0; $i < sizeof($result['deptmultlist']) ; $i++) { 
            $multidept[] = $result['deptmultlist'][$i];
            //to convert Array into string the following implode method is used
            $multi_dept = implode(',', $multidept);
        }
    }
    if($issueresult['status']== 1){
        for ($i=0; $i < sizeof($issueresult['typeid']) ; $i++) { 
            $issueList[] = $issueresult['typeid'][$i];
            //to convert Array into string the following implode method is used
            $multi_issue = implode(',', $issueList);
        }
    }else{
        $multi_issue = -1;
    }


		$empresult1=$this->empList(11);
		 
	        for ($i=0; $i < sizeof($empresult1['emplist']) ; $i++) { 
	        	$empList1[] = $empresult1['emplist'][$i];
	        	//to convert Array into string the following implode method is used
	        	$empLists1 = implode(',', $empList1);

	        }

	$query = "SELECT o.id AS id, o.job_id AS job_id, o.location_id AS o__location_id, o.plant_id AS o__plant_id, o.user_department_id AS o__user_department_id, o.notify_to AS o__notify_to, o.functional_location_id AS o__functional_location_id, o.equipment_id AS o__equipment_id, o.type_of_issue_id AS o__type_of_issue_id, o.status_id AS o__status_id, o.sla AS sla, o.subject AS subject, o.description AS o__description, o.priority_id AS o__priority_id, o.severity_id AS o__severity_id, o.reported_by AS reported_by, o.reported_on AS reported_on, o.submitted_by_name AS submitted_by, o.submitted_by_emp_number AS o__submitted_by_emp_number, o.submitted_on AS submitted_on, o.modified_by_name AS o__modified_by_name, o.modified_by_emp_number AS o__modified_by_emp_number, o.modified_on AS o__modified_on, o.is_preventivemaintenance AS o__is_preventivemaintenance, o.is_deleted AS o__is_deleted, o2.id AS o2__id, o2.ticket_id AS o2__ticket_id, o2.status_id AS o2__status_id, o2.priority_id AS o2__priority_id, o2.severity_id AS o2__severity_id, o2.comment AS o2__comment, o2.machine_status AS o2__machine_status, o2.assigned_date AS o2__assigned_date, o2.due_date AS o2__due_date, o2.accepted_by AS o2__accepted_by, o2.rejected_by AS o2__rejected_by, o2.submitted_on AS o2__submitted_on, o2.forward_from AS o2__forward_from, o2.forward_to AS o2__forward_to, o2.submitted_by_name AS o2__submitted_by_name, o2.submitted_by_emp_number AS o2__submitted_by_emp_number, o2.created_by_user_id AS o2__created_by_user_id, o2.root_cause_id AS o2__root_cause_id, o2.response_id AS o2__response_id,svrty.name as severity,prty.name as priority FROM erp_ticket o LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id
		LEFT JOIN erp_ticket_severity svrty ON svrty.id = o.severity_id
		LEFT JOIN erp_ticket_priority prty ON prty.id = o.priority_id
		WHERE((o.user_department_id IN ($multi_dept) OR o2.forward_to = $emp_number OR o.type_of_issue_id IN ($multi_issue) OR o2.forward_to = $emp_number) AND o2.status_id IN (1,2,6) AND o2.forward_from != $emp_number AND (o2.forward_to IN ($empLists1) OR o2.forward_to IS NULL OR o2.forward_to = 0) AND o.location_id = 3 AND o.plant_id = 1 AND o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id) AND o.is_deleted = 0) ORDER BY o.id DESC";

	/*echo $query;
	exit();*/
    
    $configDate = $this->dateFormat();
    $count=mysqli_query($this->conn, $query);

    
    $numofrows = mysqli_num_rows($count);
    if(mysqli_num_rows($count) > 0)
    {
        $row=mysqli_fetch_assoc($count);
        do{ 

            $j = $j+1;

            $data['sno'] = $j;    
            //$data['count'] = $numofrows;                    
            $data['id']=$row['id'];
            $data['job_id']=$row['job_id'];
            //$data['subject']=$row['subject'];
            $text = $row['subject'];
            $data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);
            $data['priority']=$row['priority'];
            $data['severity']=$row['severity'];
            $data['sla']=$row['sla'];
            $data['reported_by']=$row['reported_by'];
            $data['reported_on']=date($configDate, strtotime( $row['reported_on'] )).' '.date('H:i', strtotime( $row['reported_on'] ));
            $data['submitted_by']=$row['submitted_by'];
            $data['submitted_on']=date($configDate, strtotime( $row['submitted_on'] )).' '.date('H:i', strtotime( $row['submitted_on'] ));
            $data['status']='New';
            
    
            $data1[] = $data;
        }while($row = mysqli_fetch_assoc($count));

        $data['engNewTasks']=$data1;
        $data['status']=1;
                        
    }else{
        $data['status']=0;
    }
    return $data;
}

//multipledeptList($user_role_id)
	 function typeofissuelist($emp_number)
    {
        $data=array();
  			
		$query="SELECT * FROM erp_type_of_issue WHERE engineer_id = $emp_number";
		// exit();
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
					do{ 
						$data1[] = $row['id'];
					}while($row = mysqli_fetch_assoc($count));
					$data['typeid']=$data1;
					$data['status'] = 1;
		}else{
			$data['status'] = 0;
		}
		return $data;
    }



	//InPrgTsksLstEmpnum($emp_number)
    function InPrgTsksLstEmpnum($emp_number)
    {
        $data=array();
   		
   		$i = 0;	
		$query="SELECT ta.ticket_id AS ticket_id,t.job_id as job_id,t.sla AS sla,sta.name as status,t.subject as subject,tktprty.name as priority,tktsvrty.name as severity,CONCAT(emp.emp_firstname,emp.emp_lastname) AS raised_by,t.reported_on as raised_on,CONCAT(emp.emp_firstname,emp.emp_lastname) AS acknowledged_by,ta.submitted_on AS acknowledged_on FROM erp_ticket_acknowledgement_action_log ta LEFT JOIN erp_ticket t ON t.id = ta.ticket_id LEFT JOIN hs_hr_employee emp ON  emp.emp_number = ta.submitted_by_emp_number LEFT JOIN erp_location loc ON loc.id = t.location_id  LEFT JOIN erp_plant plant ON plant.id = t.plant_id LEFT JOIN erp_subunit sub ON sub.id = t.user_department_id  LEFT JOIN erp_functional_location func ON func.id = t.functional_location_id LEFT JOIN erp_equipment eqp ON eqp.id = t.equipment_id LEFT JOIN erp_type_of_issue iss ON iss.id = t.type_of_issue_id LEFT JOIN erp_ticket_status sta ON sta.id = t.status_id LEFT JOIN erp_ticket_priority tktprty ON tktprty.id = t.priority_id LEFT JOIN erp_ticket_severity tktsvrty ON tktsvrty.id = t.severity_id LEFT JOIN hs_hr_employee empsub ON empsub.emp_number  = t.submitted_by_name  WHERE ta.id IN (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log GROUP BY ticket_id) and ta.accepted_by = $emp_number AND ta.status_id IN (3,4) ORDER BY t.job_id DESC";
      
      	$configDate = $this->dateFormat();
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
						$row=mysqli_fetch_assoc($count);
					do{ 
						$i = $i+1;
						$data['sno'] = $i;						
						$data['ticket_id']=$row['ticket_id'];
						$data['job_id']=$row['job_id'];
						//$data['subject']=$row['subject'];
						$text = $row['subject'];
						$data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);
						$data['priority']=$row['priority'];
						$data['severity']=$row['severity'];
						$data['sla']=$row['sla'];
						$data['raised_by']=$row['raised_by'];
						$data['raised_on']=date($configDate, strtotime( $row['raised_on'] )).' '.date('H:i', strtotime( $row['raised_on'] ));
						$data['acknowledged_by']=$row['acknowledged_by'];
						$data['acknowledged_on']=date($configDate, strtotime( $row['acknowledged_on'] )).' '.date('H:i', strtotime( $row['acknowledged_on'] ));
						$data['status']=$row['status'];
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['inprogressTasks']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
        return $data;
    }

    //EngTechTsksLst webservice to get techincian tasks for engineer login
    function EngTechTsksLst($emp_number)
    {
        $data=array();

        $empresult5=$this->empEngTechList($emp_number);
       				/*echo "<pre>";
		 print_r($empresult5);
		 exit();*/
		if(!empty($empresult5)){

		
	        for ($i=0; $i < sizeof($empresult5['empTechlist']) ; $i++) { 
	        	$empList5[] = $empresult5['empTechlist'][$i];
	        	//to convert Array into string the following implode method is used
	        	$empLists5 = implode(',', $empList5);
	        	/*print_r($empLists5);
	        	exit();*/

	        }
        $i=0;
        $query = "SELECT o.id AS id, o.job_id AS job_id, o.location_id AS o__location_id, o.plant_id AS o__plant_id, o.user_department_id AS o__user_department_id, o.notify_to AS o__notify_to, o.functional_location_id AS o__functional_location_id, o.equipment_id AS o__equipment_id, o.type_of_issue_id AS o__type_of_issue_id, o.status_id AS o__status_id, o.sla AS o__sla, o.subject AS subject, o.description AS o__description, o.priority_id AS o__priority_id, o.severity_id AS o__severity_id, o.reported_by AS o__reported_by, o.reported_on AS o__reported_on, o.submitted_by_name AS o__submitted_by_name, o.submitted_by_emp_number AS o__submitted_by_emp_number, o.submitted_on AS o__submitted_on, o.modified_by_name AS o__modified_by_name, o.modified_by_emp_number AS o__modified_by_emp_number, o.modified_on AS o__modified_on, o.is_preventivemaintenance AS o__is_preventivemaintenance, o.is_deleted AS o__is_deleted, o2.id AS o2__id, o2.ticket_id AS o2__ticket_id, o2.status_id AS o2__status_id, o2.priority_id AS o2__priority_id, o2.severity_id AS o2__severity_id, o2.comment AS o2__comment, o2.machine_status AS o2__machine_status, o2.assigned_date AS o2__assigned_date, o2.due_date AS o2__due_date, o2.accepted_by AS o2__accepted_by, o2.rejected_by AS o2__rejected_by, o2.submitted_on AS o2__submitted_on, o2.forward_from AS o2__forward_from, o2.forward_to AS o2__forward_to, o2.submitted_by_name AS o2__submitted_by_name, o2.submitted_by_emp_number AS o2__submitted_by_emp_number, o2.created_by_user_id AS o2__created_by_user_id, o2.root_cause_id AS o2__root_cause_id, o2.response_id AS o2__response_id FROM erp_ticket o LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id WHERE ((o2.accepted_by IN ($empLists5) OR o2.forward_to IN ($empLists5)) AND o.location_id = 3 AND o.plant_id = 1 AND o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id) AND o.is_deleted = 0) ORDER BY o.id DESC";


    
        /*echo $query;
        exit();*/
        $configDate = $this->dateFormat();
		$count=mysqli_query($this->conn, $query);

		//$jobsCountNew = mysqli_num_rows($count);

			//echo $jobsCountNew;
			//exit();

			if(mysqli_num_rows($count) > 0)
			{	

							$row=mysqli_fetch_assoc($count);
						do{ 

							$i=$i+1;

							$data['sno']=$i;					
							$data['id']=$row['id'];
							$data['job_id']=$row['job_id'];
							$text = $row['subject'];
							$data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);
							
							//$data['status']="New";
							$data1[] = $data;
						}while($row = mysqli_fetch_assoc($count));
							$data['EngTechTasks']=$data1;
							$data['status']=1;
								
			}else{
					$data['status']=0;
			}

		}else{
				$data['status']=0;
		}
        return $data;
    }

     function empEngTechList($emp_number)
    {
        $data=array();
  			
		$query="SELECT h.erep_sub_emp_number as emp_number FROM hs_hr_emp_reportto h 
						LEFT JOIN erp_user u ON u.emp_number = h.erep_sub_emp_number
						WHERE u.user_role_id = 12 AND h.erep_sup_emp_number = $emp_number";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
					do{ 
						$data1[] = $row['emp_number'];
					}while($row = mysqli_fetch_assoc($count));
						$data['empTechlist']=$data1;
						$data['status'] = 1;
		}
		return $data;
    }

	//TechTsksLstEmpnum($emp_number)
    function TechTsksLstEmpnum($emp_number)
    {
        $data=array();

         $empresult=$this->empList(12);
		 // echo "<pre>";
		 /*print_r($empresult);
		 exit();*/
	        for ($i=0; $i < sizeof($empresult['emplist']) ; $i++) { 
	        	$empList[] = $empresult['emplist'][$i];
	        	//to convert Array into string the following implode method is used
	        	$empLists = implode(',', $empList);
	        }

        $i=0;
        $query = "SELECT o.id AS id, o.job_id AS job_id, o.location_id AS o__location_id, o.plant_id AS o__plant_id, o.user_department_id AS o__user_department_id, o.notify_to AS o__notify_to, o.functional_location_id AS o__functional_location_id, o.equipment_id AS o__equipment_id, o.type_of_issue_id AS o__type_of_issue_id, o.status_id AS o__status_id, o.sla AS o__sla, o.subject AS subject, o.description AS o__description, o.priority_id AS o__priority_id, o.severity_id AS o__severity_id, o.reported_by AS o__reported_by, o.reported_on AS o__reported_on, o.submitted_by_name AS o__submitted_by_name, o.submitted_by_emp_number AS o__submitted_by_emp_number, o.submitted_on AS o__submitted_on, o.modified_by_name AS o__modified_by_name, o.modified_by_emp_number AS o__modified_by_emp_number, o.modified_on AS o__modified_on, o.is_preventivemaintenance AS o__is_preventivemaintenance, o.is_deleted AS o__is_deleted, o2.id AS o2__id, o2.ticket_id AS o2__ticket_id, o2.status_id AS o2__status_id, o2.priority_id AS o2__priority_id, o2.severity_id AS o2__severity_id, o2.comment AS o2__comment, o2.machine_status AS o2__machine_status, o2.assigned_date AS o2__assigned_date, o2.due_date AS o2__due_date, o2.accepted_by AS o2__accepted_by, o2.rejected_by AS o2__rejected_by, o2.submitted_on AS o2__submitted_on, o2.forward_from AS o2__forward_from, o2.forward_to AS o2__forward_to, o2.submitted_by_name AS o2__submitted_by_name, o2.submitted_by_emp_number AS o2__submitted_by_emp_number, o2.created_by_user_id AS o2__created_by_user_id, o2.root_cause_id AS o2__root_cause_id, o2.response_id AS o2__response_id FROM erp_ticket o LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id WHERE ((o2.accepted_by IN ($empLists) OR o2.forward_to IN ($empLists)) AND o.location_id = 3 AND o.plant_id = 1 AND o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id) AND o.is_deleted = 0) ORDER BY o.id DESC";


       /* $query = "SELECT o.id AS id, o.job_id AS job_id, o.location_id AS o__location_id, o.plant_id AS o__plant_id, o.user_department_id AS o__user_department_id, o.notify_to AS o__notify_to, o.functional_location_id AS o__functional_location_id, o.equipment_id AS o__equipment_id, o.type_of_issue_id AS o__type_of_issue_id, o.status_id AS o__status_id, o.sla AS o__sla, o.subject AS subject, o.description AS o__description, o.priority_id AS o__priority_id, o.severity_id AS o__severity_id, o.reported_by AS o__reported_by, o.reported_on AS o__reported_on, o.submitted_by_name AS o__submitted_by_name, o.submitted_by_emp_number AS o__submitted_by_emp_number, o.submitted_on AS o__submitted_on, o.modified_by_name AS o__modified_by_name, o.modified_by_emp_number AS o__modified_by_emp_number, o.modified_on AS o__modified_on, o.is_preventivemaintenance AS o__is_preventivemaintenance, o.is_deleted AS o__is_deleted, o2.id AS o2__id, o2.ticket_id AS o2__ticket_id, o2.status_id AS o2__status_id, o2.priority_id AS o2__priority_id, o2.severity_id AS o2__severity_id, o2.comment AS o2__comment, o2.machine_status AS o2__machine_status, o2.assigned_date AS o2__assigned_date, o2.due_date AS o2__due_date, o2.accepted_by AS o2__accepted_by, o2.rejected_by AS o2__rejected_by, o2.submitted_on AS o2__submitted_on, o2.forward_from AS o2__forward_from, o2.forward_to AS o2__forward_to, o2.submitted_by_name AS o2__submitted_by_name, o2.submitted_by_emp_number AS o2__submitted_by_emp_number, o2.created_by_user_id AS o2__created_by_user_id, o2.root_cause_id AS o2__root_cause_id, o2.response_id AS o2__response_id FROM erp_ticket o LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id WHERE ((o.status_id = 3 OR o2.status_id = 7 OR o2.status_id = 9 OR o2.status_id = 8) AND (o2.accepted_by IN ($empLists) OR o2.forward_to IN ($empLists)) OR o2.forward_to IN ($empLists) AND o.location_id = 3 AND o.plant_id = 1 AND o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id) AND o.is_deleted = 0) ORDER BY o.id DESC";*/

        /*echo $query;
        exit();*/
        $configDate = $this->dateFormat();
		$count=mysqli_query($this->conn, $query);

		//$jobsCountNew = mysqli_num_rows($count);

			//echo $jobsCountNew;
			//exit();

		if(mysqli_num_rows($count) > 0)
		{	

						$row=mysqli_fetch_assoc($count);
					do{ 

						$i=$i+1;

						$data['sno']=$i;					
						$data['id']=$row['id'];
						$data['job_id']=$row['job_id'];
						$text = $row['subject'];
						$data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);
						
						$data['status']="New";
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['technicianTasks']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
        return $data;
    }

//TechNewTsksLstEmpnum($emp_number)
    function TechNewTsksLstEmpnum($emp_number)
    {
        $data=array();

         $empresult=$this->empList(12);
		 /*echo "<pre>";
		 print_r($empresult);
		 exit();*/
	        for ($i=0; $i < sizeof($empresult['emplist']) ; $i++) { 
	        	$empList[] = $empresult['emplist'][$i];
	        	//to convert Array into string the following implode method is used
	        	$empLists = implode(',', $empList);
	        }

        $i=0;
        $query = "SELECT o.id AS id, o.job_id AS job_id, o.location_id AS location_id, o.plant_id AS plant_id, o.user_department_id AS user_department_id, o.functional_location_id AS functional_location_id, o.equipment_id AS equipment_id, o.type_of_issue_id AS type_of_issue_id, o.status_id AS status_id, o.sla AS sla, o.subject AS subject, o.description AS description, o.priority_id AS priority_id, o.severity_id AS severity_id, o.reported_by AS reported_by, o.reported_on AS reported_on, o.submitted_by_name AS submitted_by_name, o.submitted_by_emp_number AS submitted_by_emp_number, o.submitted_on AS submitted_on, o.modified_by_name AS modified_by_name, o.modified_by_emp_number AS modified_by_emp_number, o.modified_on AS modified_on, o.is_preventivemaintenance AS is_preventivemaintenance, o.is_deleted AS is_deleted FROM erp_ticket o LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id
WHERE (o2.forward_to = $emp_number AND o2.status_id = 2 AND o2.forward_to IN ($empLists)
AND o.location_id = 3 
AND o.plant_id = 1 AND
o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id) AND o.is_deleted = 0) ORDER BY o.id DESC";


        $configDate = $this->dateFormat();
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{	

						$row=mysqli_fetch_assoc($count);
					do{ 

						$i=$i+1;

						$data['sno']=$i;					
						$data['id']=$row['id'];
						$data['job_id']=$row['job_id'];
						$text = $row['subject'];
						$data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);
						
						$data['status']="New";
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['technicianNewTasks']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
        return $data;
    }



//ReslvdTsksLst($emp_number)
    function ReslvdTsksLst($emp_number)
    {
        $data=array();

        $i = 0;
        $query = "SELECT t.id as id,t.job_id AS job_id,t.subject AS subject,tp.name AS priority,tsv.name AS severity,t.sla AS sla,
		CONCAT(e.emp_firstname,' ',e.emp_lastname) AS raised_by,t.reported_on AS raised_on,ta.submitted_by_name AS acknowledged_by,ta.submitted_on AS acknowledged_on,ts.name AS status
			FROM erp_ticket_acknowledgement_action_log ta
			LEFT JOIN erp_ticket t ON t.id = ta.ticket_id
			LEFT JOIN erp_ticket_priority tp ON tp.id = ta.priority_id
			LEFT JOIN erp_ticket_severity tsv ON tsv.id = ta.severity_id
			LEFT JOIN hs_hr_employee e ON e.emp_number = t.reported_by
			 LEFT JOIN erp_ticket_status ts ON ts.id = ta.status_id
			WHERE ta.id IN (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log GROUP BY ticket_id) and ta.forward_to = $emp_number AND ta.status_id = 14
			order by id desc";
			$configDate = $this->dateFormat();
		
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
						$row=mysqli_fetch_assoc($count);
					do{ 		
						$i = $i+1;

						$data['sno']=$i;				
						$data['id']=$row['id'];
						$data['job_id']=$row['job_id'];
						//$data['subject']=$row['subject'];
						$text = $row['subject'];
						$data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);
						$data['priority']=$row['priority'];
						$data['severity']=$row['severity'];
						$data['sla']=$row['sla'];
						$data['raised_by']=$row['raised_by'];
						$data['raised_on']=date($configDate, strtotime( $row['raised_on'] )).' '.date('H:i', strtotime( $row['raised_on'] ));
						$data['acknowledged_by']=$row['acknowledged_by'];
						$data['acknowledged_on']=date($configDate, strtotime( $row['acknowledged_on'] )).' '.date('H:i', strtotime( $row['acknowledged_on'] ));
						$data['status']=$row['status'];
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['resolvedTasks']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
        return $data;
    }

    function RejectTaskList($emp_number)
    {
        $data=array();

        $i = 0;
        $query = "SELECT t.id as id,t.job_id AS job_id,t.subject AS subject,tp.name AS priority,tsv.name AS severity,t.sla AS sla,
		CONCAT(e.emp_firstname,' ',e.emp_lastname) AS raised_by,t.reported_on AS raised_on,ta.submitted_by_name AS acknowledged_by,ta.submitted_on AS acknowledged_on,ts.name AS status, ta.comment as comment
			FROM erp_ticket_acknowledgement_action_log ta
			LEFT JOIN erp_ticket t ON t.id = ta.ticket_id
			LEFT JOIN erp_ticket_priority tp ON tp.id = ta.priority_id
			LEFT JOIN erp_ticket_severity tsv ON tsv.id = ta.severity_id
			LEFT JOIN hs_hr_employee e ON e.emp_number = t.reported_by
			 LEFT JOIN erp_ticket_status ts ON ts.id = ta.status_id
			WHERE ta.id IN (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log GROUP BY ticket_id) and ta.forward_to = $emp_number AND ta.status_id = 16
			order by id desc";
			//echo $query;
			$configDate = $this->dateFormat();
		
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
						$row=mysqli_fetch_assoc($count);
					do{ 		
						$i = $i+1;

						$data['sno']=$i;				
						$data['id']=$row['id'];
						$data['job_id']=$row['job_id'];
						//$data['subject']=$row['subject'];
						$text = $row['subject'];
						$data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);
						$data['priority']=$row['priority'];
						$data['severity']=$row['severity'];
						$data['sla']=$row['sla'];
						$data['comment']=$row['comment'];
						$data['raised_by']=$row['raised_by'];
						$data['raised_on']=date($configDate, strtotime( $row['raised_on'] )).' '.date('H:i', strtotime( $row['raised_on'] ));
						$data['acknowledged_by']=$row['acknowledged_by'];
						$data['acknowledged_on']=date($configDate, strtotime( $row['acknowledged_on'] )).' '.date('H:i', strtotime( $row['acknowledged_on'] ));
						$data['status']=$row['status'];
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['rejectedTasksList']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
        return $data;
    }

    //five Star Rating loop
    function TicketcheckListAddObj($checkListAddObj)
    {
    	// echo sizeof($starJsonAddObj["startrating"]);
    	for ($i=0; $i < sizeof($checkListAddObj["checklistAdd"]); $i++) { 
	         $ticket_id = $checkListAddObj["checklistAdd"][$i]['ticket_id'];
	         $check_list_item_id = $checkListAddObj["checklistAdd"][$i]['check_list_item_id'];
	         $value = $checkListAddObj["checklistAdd"][$i]['value'];
	         $comment = $checkListAddObj["checklistAdd"][$i]['comment'];

	         $data = $this->checkListAdd($ticket_id,$check_list_item_id,$comment,$value);
	     }
        
        return $data;
    }

    //ticketIdDetails
    function ticketIdDetails($ticket_id)
	{
		$data= array();
		//getUser Details

		$query ="SELECT is_PreventiveMaintenance from erp_ticket where id = $ticket_id";
		$query1="SELECT t.id,t.job_id as job_id,t.location_id,loc.name as location_name,t.plant_id,plnt.plant_name as plantname,t.subject, t.user_department_id as department_id,sub.name as department_name,t.functional_location_id as functionlocation_id,fnloc.name AS functionallocation_name, t.equipment_id as equipment_id, eqp.name as equipment_name, t.type_of_issue_id as typeofissue_id,otyiss.name as typeofissue,sta.id AS status_id, sta.name as status, t.priority_id as priority_id,tktprty.name as ticketpriority,t.severity_id as severityId,tktsvrty.name as ticketseverity,t.sla as sla, CONCAT(emp.emp_firstname,emp.emp_lastname) AS reportedby,t.reported_on as reporteddate, CONCAT(emp.emp_firstname,emp.emp_lastname) AS submittedby,t.submitted_on as submitteddate, t.submitted_by_emp_number AS submittedByEmp ,t.is_PreventiveMaintenance, log.accepted_by,log.forward_from,log.forward_to,log.created_by_user_id,
        log.comment,log.machine_status,log.root_cause_id,rc.name as root_cause,log.response_id FROM erp_ticket t 
		LEFT JOIN erp_ticket_acknowledgement_action_log log ON log.ticket_id = t.id 
  		LEFT join erp_location loc ON t.location_id = loc.id
        LEFT JOIN erp_plant plnt ON t.plant_id = plnt.id
        LEFT JOIN erp_subunit sub ON t.user_department_id = sub.id
        LEFT JOIN erp_functional_location fnloc ON t.functional_location_id = fnloc.id
        LEFT JOIN erp_equipment eqp ON t.equipment_id = eqp.id
        LEFT JOIN erp_type_of_issue otyiss ON t.type_of_issue_id = otyiss.id
        LEFT JOIN erp_ticket_status sta ON t.status_id = sta.id
        LEFT JOIN erp_ticket_priority tktprty ON t.priority_id = tktprty.id
        LEFT JOIN erp_ticket_severity tktsvrty ON t.severity_id = tktsvrty.id
        LEFT JOIN hs_hr_employee emp ON t.reported_by = emp.emp_number
        LEFT JOIN hs_hr_employee empsub ON t.submitted_by_name =  emp.emp_number
        LEFT JOIN erp_root_cause rc ON rc.id = log.root_cause_id
		 WHERE log.ticket_id = $ticket_id ORDER BY log.id DESC LIMIT 1";

		 $configDate = $this->dateFormat();

		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0){
			$row1=mysqli_fetch_assoc($count);
			$data1['is_PreventiveMaintenance']=$row1['is_PreventiveMaintenance'];

				if($data1['is_PreventiveMaintenance'] == "1"){
				
					$query2 ="SELECT p.ticket_id as ticketId,  c.name as checkList , g.name as groupName, n.name AS chklstname,p.value as value,p.comment as comment FROM erp_preventive_check_list p LEFT JOIN erp_ticket t ON t.id = p.ticket_id LEFT JOIN erp_check_list_name n ON n.id = p.check_list_item_id LEFT JOIN erp_group g ON g.id = n.group_id LEFT JOIN erp_check_list c ON c.id = g.check_list_id WHERE p.ticket_id = $ticket_id";
					$count=mysqli_query($this->conn, $query2);
					
					if(mysqli_num_rows($count) > 0)
						{
						while($row4 = mysqli_fetch_assoc($count)) { 						
						$data4['ticketId']=$row4['ticketId'];
						$data4['checkList']=$row4['checkList'];
						$data4['groupName']=$row4['groupName'];
						$data4['chklstname']=$row4['chklstname'];
						$data4['value']=$row4['value'];
						$data4['comment']=$row4['comment'];
						
						$data2[] = $data4;
						}
						$data3['checkLists']=$data2;
						
					}else{
						$data3['checkLists']=[];
					}
				
				}
							$count1=mysqli_query($this->conn, $query1);
							if(mysqli_num_rows($count1) > 0)
							{
										$row2=mysqli_fetch_assoc($count1);					
											$data['id']				=$row2['id'];
											$data['job_id']			=$row2['job_id'];
											$data['location_id']	=$row2['location_id'];
											$data['location_name']	=$row2['location_name'];
											$data['plant_id']		=$row2['plant_id'];
											$data['plant_name']		= $row2['plantname'];

											if($data1['is_PreventiveMaintenance'] == "1"){
													$data['subject']		='PM - '.$row2['equipment_name'];

											}else{
													//$data['subject']		=$row2['subject'];
													$text = $row2['subject'];
													$data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);

											}
											$data['department_id']	=$row2['department_id'];
											$data['department_name']=$row2['department_name'];

											$funLoc = $this->subfunctionalLocations($row2['functionlocation_id']);
											if($funLoc['status'] == 1){
												$data['functionlocation_id']=$funLoc['id'];
												$data['functionallocation_name']=$funLoc['name'];
												$data['subfunctionlocation_id']=$row2['functionlocation_id'];
												$data['subfunctionallocation_name']=$row2['functionallocation_name'];
											}else{
												$data['functionlocation_id']=$row2['functionlocation_id'];
												$data['functionallocation_name']=$row2['functionallocation_name'];
												$data['subfunctionlocation_id']=0;
												$data['subfunctionallocation_name']='';
											}
											$data['equipment_id']	=$row2['equipment_id'];
											$data['equipment_name']	= $row2['equipment_name'];
											$data['typeofissue_id']	=$row2['typeofissue_id'];
											$data['typeofissue']	=$row2['typeofissue'];
											$data['status_id']		= $row2['status_id'];
											$data['status_name']	= $row2['status'];
											$data['priority_id']	= $row2['priority_id'];
											$data['ticketpriority']	=$row2['ticketpriority'];
											$data['severityId']	= $row2['severityId'];
											$data['ticketseverity']	=$row2['ticketseverity'];
											$data['sla']			=$row2['sla'];
											$data['reportedby']		=$row2['reportedby'];
											$data['reporteddate']	=date($configDate, strtotime( $row2['reporteddate'] )).' '.date('H:i:s', strtotime( $row2['reporteddate'] ));
											$data['submittedby']	=$row2['submittedby'];
											$data['submittedByEmp']	=$row2['submittedByEmp'];
											$data['submitteddate']	= date($configDate, strtotime( $row2['submitteddate'] )).' '.date('H:i:s', strtotime( $row2['submitteddate'] ));
											$data['is_PreventiveMaintenance']= $row2['is_PreventiveMaintenance'];
											$data['accepted_by']	=$row2['accepted_by'];
											$data['forward_from']	= $row2['forward_from'];
											$data['forward_to']		= $row2['forward_to'];
											$data['created_by_user_id']= $row2['created_by_user_id'];
											$data['comment']		= $row2['comment'];
											$data['machine_status']	= $row2['machine_status'];
											$data['root_cause_id']	= $row2['root_cause_id'];
											$data['root_cause']	= $row2['root_cause'];
											$data['response_id']	= $row2['response_id'];
							}
										if($data1['is_PreventiveMaintenance'] == 1){
											$data['checkLists'] = $data3['checkLists'];
											$data['ticket_Details']=$data ;
										}else{
											$data['ticket_Details']=$data;
										}
												
										$data['status']=1;
		}else{
				$data['status']=0;
		}
		return $data;    
	}


	//funcLocation
    function funcLocation($department_id,$parent_id,$level)
	{
		$data= array();
		//getUser Detail
		// $query="SELECT name FROM ohrm_location WHERE id = $location_id";

		// $count=mysqli_query($this->conn, $query);

		// if(mysqli_num_rows($count) > 0)
		// {
			
		// 	$row=mysqli_fetch_assoc($count);
		// 		$data['name']=$row['name'];	
		// 		$locname = $data['name'];

		// 		$query1="SELECT lft,rgt FROM ohrm_subunit WHERE name = '".$locname."'";
		// 		$count1=mysqli_query($this->conn, $query1);



		// 		if(mysqli_num_rows($count1) > 0)
		// 		{
		// 			$row1=mysqli_fetch_assoc($count1);
		// 			$data['left']=$row1['lft'];
		// 			$data['right']=$row1['rgt'];	
		// 			$lft = $data['left'];
		// 			$rgt = $data['right']; 
									
		// 			$query2="select plant_name from ohrm_plant WHERE id = $plant_id";
		// 			$count2=mysqli_query($this->conn, $query2);

		// 				if(mysqli_num_rows($count2) > 0)
		// 				{
		// 					$row=mysqli_fetch_assoc($count2);
		// 					$data['plant_name']=$row['plant_name'];
		// 					$plantname = $data['plant_name'];
							
		// 						$query3="SELECT lft,rgt FROM ohrm_subunit WHERE name = '$plantname' and lft > ". $lft." and rgt < ".$rgt;
		// 								$count=mysqli_query($this->conn, $query3);

		// 									if(mysqli_num_rows($count) > 0)
		// 									{
		// 										$row=mysqli_fetch_assoc($count);
		// 										$data['lft']=$row['lft'];
		// 										$data['rgt']=$row['rgt'];	
		// 										$lft = $data['lft'];
		// 										$rgt = $data['rgt']; 

											
		// 											$query4="SELECT id FROM ohrm_subunit WHERE lft > ". $lft." and rgt < ".$rgt;
		// 											$count=mysqli_query($this->conn, $query4);

		// 											if(mysqli_num_rows($count) > 0)
		// 											{
		// 												$row=mysqli_fetch_assoc($count);
		// 												$data['id']=$row['id'];
		// 												$depId = $data['id'];
														$query5="SELECT id,name FROM erp_functional_location WHERE user_department_id = $department_id and is_deleted = 0";
														if($parent_id){
															$query5 .= " and parent_id = $parent_id";
														}

														if($level == 0){
															$query5 .= " and level = $level";
														}

														if($level == 1){
															$query5 .= " and level = $level";
														}

														$count=mysqli_query($this->conn, $query5);

														if(mysqli_num_rows($count) > 0)
														{
																$row=mysqli_fetch_assoc($count);
																do{ 
																	$data3['id']=$row['id'];
																	$data3['name']=$row['name'];					
																	$data4[] = $data3;
																}while($row = mysqli_fetch_assoc($count)); 
																$data['funcLocation']=$data4;
																$data['status']=1;
																											
														}else {
															//echo "ERROR: Could not able to execute $query5. " . mysqli_error($this->conn);
																     $data['status']=0;
														}
												
		// 											}else {
		// 													//echo "ERROR: Could not able to execute $query4. " . mysqli_error($this->conn);
		// 														     $data['status']=0;
		// 												}
		// 									}else {
		// 											    //echo "ERROR: Could not able to execute $query3. " . mysqli_error($this->conn);
		// 											     $data['status']=0;
		// 									}
					
		// 				}else {
		// 					    //echo "ERROR: Could not able to execute $query2. " . mysqli_error($this->conn);
		// 					     $data['status']=0;
		// 				}	

		// 		}else {
		// 		    //echo "ERROR: Could not able to execute $query1. " . mysqli_error($this->conn);
		// 		     $data['status']=0;
		// 			}
				
		// }else{
		// 		$data['status']=0;
		// 	}
			
		return $data;     
	}

	    //Get Employess based on location and Plant
    function empNumByLocPlnt($loc_id,$plant_id,$user_id,$user_role_id)
    {
        $data=array();

        if(strcmp($user_role_id,2)){
        	$query="SELECT u.id AS userId,e.emp_number as empNumber,CONCAT(e.emp_firstname,' ',e.emp_lastname) AS emp_name FROM hs_hr_employee e LEFT JOIN hs_hr_emp_locations l ON l.emp_number = e.emp_number LEFT JOIN erp_user u ON u.emp_number = e.emp_number WHERE l.location_id = $loc_id and e.plant_id = $plant_id ORDER BY FIELD(userId, $user_id) DESC";
        }else{
        	$query="SELECT u.id AS userId,e.emp_number as empNumber,CONCAT(e.emp_firstname,' ',e.emp_lastname) AS emp_name FROM hs_hr_employee e LEFT JOIN hs_hr_emp_locations l ON l.emp_number = e.emp_number LEFT JOIN erp_user u ON u.emp_number = e.emp_number WHERE u.id = $user_id";
        }
   
        $count=mysqli_query($this->conn, $query);
    if(mysqli_num_rows($count) > 0)
    {
            $row=mysqli_fetch_assoc($count);
            do {
	            $data['id'] = $row['empNumber'];
	            $data['name'] = $row['emp_name'];
	            $data1[] = $data;
			}while($row = mysqli_fetch_assoc($count));
			
			$data['emp_details']=$data1;
			$data['status']=1;
	     
    }else{
        $data['status'] = 0;
    }
    return $data;
}


	//unitmeasure
    function unitmeasure()
	{
		$data= array();
		$query="SELECT * from erp_product_unit WHERE is_deleted = 0";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			while($row = mysqli_fetch_assoc($count)) { 						
				$data['id']=$row['id'];
				$data['name']=$row['name'];	
				$data['name_plrl']=$row['name_plrl'];
				$data['isDeleted']=$row['is_deleted'];
				$data1[] = $data;
			}
			$data['unitmeasure']=$data1;
			$data['status']=1;
							
		}else{
			$data['status']=0;
		}
		return $data;  	
	}

	//parts
    function parts()
	{

		$data= array();
		$query="SELECT * from erp_product_unit WHERE is_deleted =0 ";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			do{ 
				$data['id']=$row['id'];
				$data['name']=$row['name'];
				$data['name_plrl']=$row['name_plrl'];
				$data['is_deleted']=$row['is_deleted'];
				$data1[]= $data;
			}while($row = mysqli_fetch_assoc($count)) ;
			$data['parts']=$data1;
			$data['status']=1;			
		}else{
				$data['status']=0;
			}
		return $data;  	
	}

	//partid($ticket_id)
    function partid($ticket_id)
	{
		$data= array();
		$query="SELECT p.*,u.name as unit_name,u.name_plrl as unit_name_plrl from erp_parts p LEFT JOIN erp_product_unit u ON u.id = p.unit_id WHERE ticket_id = $ticket_id";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			do{ 
				$data['id']=$row['id'];
				$data['ticket_id']=$row['ticket_id'];	
				$data['emp_number']=$row['emp_number'];
				$data['part_name']=$row['part_name'];
				$data['part_number']=$row['part_number'];
				$data['quantity']=$row['quantity'];
				$data['unit_id']=$row['unit_id'];
				$data['unit_name']=$row['unit_name'];
				$data['unit_name_plrl']=$row['unit_name_plrl'];
				$data1[]= $data;
			}while($row = mysqli_fetch_assoc($count)) ;
			$data['partdetail']=$data1;
			$data['status']=1;
		}else{
				$data['status']=0;
		}
		return $data;  	
	}

	function getPartById($id)
	{
		$data= array();

		$query = "SELECT pr.id AS id, pr.part_name as name, pr.part_number as part_number, pr.quantity AS quantity, pr.unit_id AS unit_id, pu.name AS unit_name, pu.name_plrl AS unit_name_plrl, pr.emp_number AS emp_number, pr.created_on AS created_on, pr.ticket_id AS ticket_id FROM erp_parts pr LEFT JOIN erp_product_unit pu ON pu.id = pr.unit_id WHERE pr.id=$id";

		$configDate = $this->dateFormat();
		/*$query="SELECT * FROM `erp_parts` WHERE is_deleted = 0 ORDER BY name ASC";*/
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			do{ 
				$data['id']=$row['id'];
				$data['name']=$row['name'];	
				$data['part_number']=$row['part_number'];
				$data['quantity']=$row['quantity'];
				$data['unit_id']=$row['unit_id'];
				$data['unit_name']=$row['unit_name'];
				$data['unit_name_plrl']=$row['unit_name_plrl'];
				$data['emp_number']=$row['emp_number'];
				$data['created_on']=date($configDate, strtotime( $row['created_on'] )).' '.date('H:i:s', strtotime( $row['created_on'] ));
				$data['ticket_id']=$row['ticket_id'];
				$data1[]= $data;
			}while($row = mysqli_fetch_assoc($count));
			$data['getPartById']=$data1;
			$data['status']=1;
		}else{
			$data['status']=0;
		}
		return $data;  	
	}

	// to get engineer or technician response based on ticked id
	 function getEngorTechRespByTktId($ticket_id,$user_role_id)
	{
		$data= array();

		$query = "SELECT ta.ticket_id,prty.name as priority,concat(emp1.emp_firstname,' ',emp1.emp_lastname) as forwardfrom,concat(emp2.emp_firstname,' ',emp2.emp_lastname) as forwardTo,concat(emp.emp_firstname,' ',emp.emp_lastname) as acceptedBy, ta.submitted_on as submittedOn, st.name as status, ta.comment as comment,rc.name as rootname FROM erp_ticket_acknowledgement_action_log ta
			LEFT JOIN erp_user usr ON ta.created_by_user_id = usr.id
			LEFT JOIN erp_root_cause rc ON ta.root_cause_id = rc.id
			LEFT JOIN erp_ticket_priority prty ON prty.id = ta.priority_id
			LEFT JOIN hs_hr_employee emp ON emp.emp_number = ta.accepted_by
			LEFT JOIN hs_hr_employee emp1 ON emp1.emp_number = ta.forward_from
			LEFT JOIN hs_hr_employee emp2 ON emp2.emp_number = ta.forward_to
			LEFT JOIN erp_ticket_status st ON st.id = ta.status_id
			WHERE ticket_id = $ticket_id and usr.user_role_id = $user_role_id";

			$configDate = $this->dateFormat();
		/*$query="SELECT * FROM `erp_parts` WHERE is_deleted = 0 ORDER BY name ASC";*/
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);

			if($user_role_id = 12)
			{
				do{ 
				$data['ticket_id']=$row['ticket_id'];
				$data['priority']=$row['priority'];	
				$data['forwardfrom']=$row['forwardfrom'];
				$data['forwardTo']=$row['forwardTo'];
				$data['acceptedBy']=$row['acceptedBy'];
				$data['submittedOn']=date($configDate, strtotime( $row['submittedOn'] )).' '.date('H:i', strtotime( $row['submittedOn'] ));
				$data['status']=$row['status'];
				$rootname = $row['rootname'];
				if($rootname)
				{
					$data['comment']= $row['comment'].' rootcause: '.$row['rootname'];
				}
				else
				{
					$data['comment']= $row['comment'];
				}
				
				//$data['comment'].=$row['comment'];
				//$rootname =$row['rootname'];
				//$data['comment']=$row['comment'].'('.$rootname.')';
				$data1[]= $data;
			}while($row = mysqli_fetch_assoc($count));
			$data['getEngorTechRespByTktId']=$data1;
			$data['status']=1;

			}
			else

			{


					do{ 
						$data['ticket_id']=$row['ticket_id'];
						$data['priority']=$row['priority'];	
						$data['forwardfrom']=$row['forwardfrom'];
						$data['forwardTo']=$row['forwardTo'];
						$data['acceptedBy']=$row['acceptedBy'];
						$data['submittedOn']=date($configDate, strtotime( $row['submittedOn'] )).' '.date('H:i', strtotime( $row['submittedOn'] ));
						$data['status']=$row['status'];
						$data['comment']= $row['comment'];
						
						//$data['comment'].=$row['comment'];
						//$rootname =$row['rootname'];
						//$data['comment']=$row['comment'].'('.$rootname.')';
						$data1[]= $data;
					}while($row = mysqli_fetch_assoc($count));
					$data['getEngorTechRespByTktId']=$data1;
					$data['status']=1;
				}
		}else{
			$data['status']=0;
		}
		return $data;  	
	}

	function getReqstrRespByTktId($ticket_id)
	{
		$data= array();

		$query = "SELECT ta.ticket_id,prty.name as priority,concat(emp1.emp_firstname,' ',emp1.emp_lastname) as forwardfrom,concat(emp2.emp_firstname,' ',emp2.emp_lastname) as forwardTo,ta.submitted_by_name AS submittedBy, ta.submitted_on as submittedOn, st.name as status, ta.comment as comment FROM erp_ticket_acknowledgement_action_log ta LEFT JOIN erp_ticket t ON t.id = ta.ticket_id LEFT JOIN erp_user usr ON ta.created_by_user_id = usr.id LEFT JOIN erp_ticket_priority prty ON prty.id = ta.priority_id LEFT JOIN hs_hr_employee emp ON emp.emp_number = ta.submitted_by_emp_number LEFT JOIN hs_hr_employee emp1 ON emp1.emp_number = ta.forward_from LEFT JOIN hs_hr_employee emp2 ON emp2.emp_number = ta.forward_to LEFT JOIN erp_ticket_status st ON st.id = ta.status_id WHERE ticket_id = $ticket_id AND ta.status_id IN (1,5,6)";
 
			$configDate = $this->dateFormat();
		/*$query="SELECT * FROM `erp_parts` WHERE is_deleted = 0 ORDER BY name ASC";*/
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			do{ 
				$data['ticket_id']=$row['ticket_id'];
				$data['priority']=$row['priority'];	
				$data['forwardfrom']=$row['forwardfrom'];
				$data['forwardTo']=$row['forwardTo'];
				$data['submittedBy']=$row['submittedBy'];
				$data['submittedOn']=date($configDate, strtotime( $row['submittedOn'] )).' '.date('H:i', strtotime( $row['submittedOn'] ));
				$data['status']=$row['status'];
				$data['comment']=$row['comment'];
				$data1[]= $data;
			}while($row = mysqli_fetch_assoc($count));
			$data['getReqstrRespByTktId']=$data1;
			$data['status']=1;
		}else{
			$data['status']=0;
		}
		return $data;  	
	}

	//ticketstatus
    function ticketstatus($user_id,$user_role_id,$ticket_id,$response_id)
	{
		$data= array();

		$userDetails = $this->getUserRoleByUserId($user_id);
		$empNumber = $userDetails['empNumber'];

		
	
		$assgn = array(2,3);

		
		if($user_role_id == 11 && $response_id == 1){
			$reslv = $this->getResolvedAction($ticket_id);
			$status = $reslv > 0 ? array(3,10) : array(3,10);
			$tktst = implode(',', $status);
			$query="SELECT * FROM `erp_ticket_status` WHERE id IN ($tktst)";

			
		}

		if($user_role_id == 11 && in_array($response_id, $assgn, TRUE) || $user_role_id == 12 && $response_id == 3){


			$empresult=$this->engLists();

										for ($i=0; $i < sizeof($empresult['englist']) ; $i++) { 
							        	$engList[] = $empresult['englist'][$i];
							        	//to convert Array into string the following implode method is used
							        	$engLists = implode(',', $engList);
							        }
		$countquery = "SELECT COUNT(*) as count FROM erp_ticket_acknowledgement_action_log WHERE forward_from IN ($engLists) AND ticket_id = $ticket_id";

		$rowcount = mysqli_query($this->conn, $countquery);

	if(mysqli_num_rows($rowcount) > 0)
		{
			$row = mysqli_fetch_assoc($rowcount);
				$datacount=$row['count'];		
		}

		//echo $datacount;
		//exit();

		if($datacount > 1 && $response_id == 2 )

		{ 
			$maxquery = "SELECT * FROM erp_ticket_acknowledgement_action_log WHERE id IN (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE forward_from IN ($engLists) AND ticket_id = $ticket_id)";

				$rowcount1 = mysqli_query($this->conn, $maxquery);

				if(mysqli_num_rows($rowcount1) > 0)
					{

						$row1 = mysqli_fetch_assoc($rowcount1);
							$datacount1 = $row1['forward_from'];	
							//echo '$datacount1'.' '.$datacount1;	
					}


						$minquery = "SELECT * FROM erp_ticket_acknowledgement_action_log WHERE id IN (SELECT MIN(id) FROM erp_ticket_acknowledgement_action_log WHERE forward_from IN ($engLists) AND ticket_id = $ticket_id)";

						$rowcount2 = mysqli_query($this->conn, $minquery);

							if(mysqli_num_rows($rowcount2) > 0)
								{
									$row2 = mysqli_fetch_assoc($rowcount2);
										$datacount2=$row2['forward_from'];
										//echo 'datacount2'. ' '.$datacount2;		
								}

									// echo $datacount1.' '.$empNumber;
									// exit();

								    if($datacount1 == $empNumber){

								    	$query="SELECT * FROM `erp_ticket_status` WHERE id IN (2,14)";

								    }else{
								    	$query="SELECT * FROM `erp_ticket_status` WHERE id IN (2)";
								    }

		}else{
			$query="SELECT * FROM `erp_ticket_status` WHERE id IN (2)";
		}
			
		}

		if($user_role_id == 12 && $response_id == 1){
			$query="SELECT * FROM `erp_ticket_status` WHERE id IN (3,14)";
		}

		if($user_role_id == 12 && $response_id == 2){
			$query="SELECT * FROM `erp_ticket_status` WHERE id IN (2,7,8,9)";
		}

		if($user_role_id == 10 && $response_id == 1 || $user_role_id == 2 && $response_id == 1){
			$query="SELECT * FROM `erp_ticket_status` WHERE id IN (5,6)";
		}



		
		
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			while($row = mysqli_fetch_assoc($count)) { 
				$data['id']=$row['id'];
				$data['name']=$row['name'];	
				$data1[]= $data;
			}
			$data['ticketstatus']=$data1;
			$data['status']=1;
		}else{
				$data['status']=0;
		}
		return $data;  	
	}



	//ticketpriority
    function ticketpriority($typeOfIssueId)
	{	
		$priorityId = $this->getPriorityByTypeOfIssueId($typeOfIssueId);
		$data= array();
		$query="SELECT * from erp_ticket_priority where id = $priorityId";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			while($row = mysqli_fetch_assoc($count)) { 
				$data['id']=$row['id'];
				$data['name']=$row['name'];	
				$data1[]= $data;
			}
			$data['ticketpriority']=$data1;
			$data['status']=1;
		}else{
			$data['status']=0;
		}
		return $data;  	
	}



	function getPriorityByTypeOfIssueId($typeOfIssueId)
		{
			$query = "SELECT * FROM `erp_type_of_issue` WHERE id = $typeOfIssueId"; //table
			$result=mysqli_query($this->conn, $query);
			if(mysqli_num_rows($result)>0)
			{
			   $row = mysqli_fetch_array($result);
			   $priority_id=$row['priority_id'];
		    }
		   return $priority_id;
		}

	//ticketseverity
    function ticketseverity()
	{
		$data= array();
		$query="SELECT id,name FROM `erp_ticket_severity` WHERE is_deleted = 0 ORDER BY name ASC";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			do{ 
				$data['id']=$row['id'];
				$data['name']=$row['name'];	
				$data1[]= $data;
			}while($row = mysqli_fetch_assoc($count));
			$data['ticketseverity']=$data1;
			$data['status']=1;
		}else{
			$data['status']=0;
		}
		return $data;  	
	}


	//machinestatus
    function machineStatus()
	{
		$data= array();
		$query="SELECT id,name FROM `erp_machine_status` WHERE is_deleted = 0 ORDER BY name ASC";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			do{ 
				$data['id']=$row['id'];
				$data['name']=$row['name'];	
				$data1[]= $data;
			}while($row = mysqli_fetch_assoc($count));
			$data['machineStatus']=$data1;
			$data['status']=1;
		}else{
			$data['status']=0;
		}
		return $data;  	
	}

	
	function getEmpnumberByUsrId($user_id)
		{
			/*echo $user_id;
			exit();*/
			$query = "SELECT emp_number FROM erp_user WHERE id = $user_id"; //table
			$result=mysqli_query($this->conn, $query);
			if(mysqli_num_rows($result)>0)
			{
			   $row = mysqli_fetch_array($result);
			   $emp_number=$row['emp_number'];
			   /*echo $emp_number;
			   exit();*/
		    }
		   return $emp_number;
		}

    //uploadImage($ticket_id,$image,$fileName,$fileType,$fileSize)
    function uploadImage($ticket_id,$file_name,$file_type,$file_size,$file_content,$created_on,$user_id)
    {
        $data=array();

        $created_by = $this->getEmpnumberByUsrId($user_id);
   			// Prepare an insert statement
			$sql = "INSERT INTO erp_ticket_attachment (ticket_id,file_name,file_type,file_size,file_content,created_on,created_by) VALUES (?,?,?,?,?,?,?)";
			 
			if($stmt = mysqli_prepare($this->conn, $sql)){
			    // Bind variables to the prepared statement as parameters
			     mysqli_stmt_bind_param($stmt, "ississs" , $ticket_id,$file_name,$file_type,$file_size,
			     	$file_content,$created_on,$created_by);
			    			   
			    // Attempt to execute the prepared statement
			    if(mysqli_stmt_execute($stmt)){
			        $data['uploadImage'] = "Image added successfully";
			        $data['status']=1;
			    } else{
			        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
			        $data['status']=0;
			    }
			} else{
			    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
			    $data['status']=0;
			}	

        return $data;
    }

    
//uploadImage($ticket_id,$image,$fileName,$fileType,$fileSize)
    function respupload($ticket_id,$file_name,$file_type,$file_size,$file_content,$created_on,$user_id)
    {
        $data=array();
   			// Prepare an insert statement
       
			$ticket_action_log_id = $this->getAcknowledgementId($ticket_id);

			$created_by = $this->getEmpnumberByUsrId($user_id);

			$sql = "INSERT INTO erp_ticket_action_log_attachment (ticket_action_log_id,file_name,file_type,file_size,file_content,created_on,created_by) VALUES (?,?,?,?,?,?,?)";
			 
			if($stmt = mysqli_prepare($this->conn, $sql)){
			    // Bind variables to the prepared statement as parameters
			     mysqli_stmt_bind_param($stmt, "ississs" , $ticket_action_log_id,$file_name,$file_type,$file_size,$file_content,$created_on,$created_by);
			    			   
			    // Attempt to execute the prepared statement
			    if(mysqli_stmt_execute($stmt)){
			        $data['respuploadImage'] = "upload is successfull";
			        $data['status']=1;
			    } else{
			        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
			        $data['status']=0;
			    }
			} else{
			    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
			    $data['status']=0;
			}	

        return $data;
    }


//getTicketAttachment
    function getTicketAttachment($ticket_id,$path)
    {
       $data= array();
		//getUser Details
		$query="SELECT id,file_name,file_type,file_content FROM erp_ticket_attachment WHERE ticket_id = $ticket_id";
		$count=mysqli_query($this->conn, $query);

			
			if(mysqli_num_rows($count) > 0)
					{
								$row=mysqli_fetch_assoc($count);
								do{ 
										$id = $data['id']=$row['id'];
										$data['file_name']=$row['file_name'];
										$data['file_type']=$row['file_type'];
										//$data['file_content']=$row['file_content'];	
										$value = $path.'get_ticket_attachment.php?id='.$id;
										$data['attachment'] = $value;				
										$data1[] = $data;
								}while($row = mysqli_fetch_assoc($count)); 
										$data['getTicketAttachment']=$data1;
										$data['status']=1;
																											
					}else {
								//echo "ERROR: Could not able to execute $query. " . mysqli_error($this->conn);
																     $data['status']=0;
						 }
	
			
		return $data;   
    }

    function base64_to_jpeg($base64_string, $output_file) {
    // open the output file for writing
    $ifp = fopen( $output_file, 'wb' ); 

    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode( ',', $base64_string );

    // we could add validation here with ensuring count( $data ) > 1
    fwrite( $ifp, base64_decode( $data[ 1 ] ) );

    // clean up the file resource
    fclose( $ifp ); 

    return $output_file; 
}










	function userIdbyUserRoleId($user_id)
	{
		$data= array();

		$query="SELECT  u.user_role_id,u.user_name FROM erp_user u WHERE u.id = $user_id";

		$count=mysqli_query($this->conn, $query);


			if(mysqli_num_rows($count) > 0)
					{
							$row=mysqli_fetch_assoc($count);
							$user_role_id = $row['user_role_id'];				
					}

		return $user_role_id; 
	}

	function getSubordinateByEmp($emp_number)
	{

		$data= array();

		$query="SELECT erep_sup_emp_number FROM `hs_hr_emp_reportto` WHERE erep_sub_emp_number = $emp_number AND erep_reporting_mode = 1";

		$count=mysqli_query($this->conn, $query);


			if(mysqli_num_rows($count) > 0)
					{
							$row=mysqli_fetch_assoc($count);
							$erep_sup_emp_number = $row['erep_sup_emp_number'];				
					}

		return $erep_sup_emp_number; 


	}

	function getDepartmentByTktId($ticket_id)
	{

		$data= array();

		$query="SELECT t.user_department_id as usrDeptId FROM erp_ticket t WHERE t.id = $ticket_id";

		$count=mysqli_query($this->conn, $query);


			if(mysqli_num_rows($count) > 0)
					{
							$row=mysqli_fetch_assoc($count);
							$usrDeptId = $row['usrDeptId'];				
					}

		return $usrDeptId; 


	}










	
    //employeelist($user_role_id)
	 function empList($user_role_id)
    {
        $data=array();
  			
		$query="SELECT e.emp_number as emp_number, u.user_role_id as role_id,l.location_id as location_id,e.plant_id as plant_id ,e.work_station as  work_station FROM hs_hr_employee e LEFT JOIN hs_hr_emp_locations l ON l.emp_number = e.emp_number LEFT JOIN erp_user u ON u.emp_number = e.emp_number WHERE u.user_role_id = $user_role_id";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
					do{ 
						$data1[] = $row['emp_number'];
					}while($row = mysqli_fetch_assoc($count));
						$data['emplist']=$data1;
						$data['status'] = 1;
		}
		return $data;
    }

 


 

    //employeelist($user_role_id)
	function subordinateByEmpList($empnumber)
    {
        $data=array();
  			
		$query="SELECT h.erep_sub_emp_number as emp_number FROM hs_hr_emp_reportto h WHERE (h.erep_sup_emp_number = $empnumber)";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
					do{ 
						$data1[] = $this->supervisorEmployeeDetails($row['emp_number']);
					}while($row = mysqli_fetch_assoc($count));
						$data['emplist']=$data1;
						$data['status'] = 1;
		}

		else{
			$data['emplist']=array();
			$data['status'] = 0;
		}
		return $data;
    }

     //smsConfig()
	 function smsConfig()
    {
        $data=array();
  			
		$query="SELECT * FROM erp_sms_configuration";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
					do{ 
						$data['id'] = $row['id'];
						$data['url'] = $row['url'];
						$data['sender_id'] = $row['sender_id'];
						$data['user_name'] = $row['user_name'];
						$data['password'] = $row['password'];
						$data['smtp_auth_type'] = $row['smtp_auth_type'];
						$data['smtp_security_type'] = $row['smtp_security_type'];
					}while($row = mysqli_fetch_assoc($count));
						$data['sms']=$data;
						$data['status'] = 1;
		}
		return $data;
    }

     //emailConfig()
	 function emailConfig()
    {
        $data=array();
  			
		$query="SELECT * FROM erp_email_configuration";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
					do{ 
						$data['id'] = $row['id'];
						$data['mail_type'] = $row['mail_type'];
						$data['sent_as'] = $row['sent_as'];
						$data['sendmail_path'] = $row['sendmail_path'];
						$data['smtp_host'] = $row['smtp_host'];
						$data['smtp_port'] = $row['smtp_port'];
						$data['smtp_username'] = $row['smtp_username'];
						$data['smtp_password'] = $row['smtp_password'];
						$data['smtp_auth_type'] = $row['smtp_auth_type'];
						$data['smtp_security_type'] = $row['smtp_security_type'];
					}while($row = mysqli_fetch_assoc($count));
						$data['email']=$data;
						$data['status'] = 1;
		}
		return $data;
    }

    // Employee Mails
    //multipledeptList($user_role_id)
	 function employeeEmails($emp_numbers)
    {	
    	$empLists = implode(',', $emp_numbers);
        $data=array();
  			
		$query="SELECT emp_work_email,emp_mobile,emp_oth_email,CONCAT(emp_firstname,' ',emp_lastname) AS emp_name FROM hs_hr_employee WHERE emp_number IN ($empLists) ";
		$count=mysqli_query($this->conn, $query);

		$data = array();
		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				do{ 
					if(!empty($row['emp_work_email'])){
						$data['mail'][] = $row['emp_work_email'];
						$data['mobile'][] = $row['emp_mobile'];
						$data['name'][] = $row['emp_name'];
						$data['status'][] = 1;
					}else{
						$data['mail'][] = $row['emp_oth_email'];
						$data['mobile'][] = $row['emp_mobile'];
						$data['name'][] = $row['emp_name'];
						$data['status'][] = 1;
					}
				}while($row = mysqli_fetch_assoc($count));
		}else{
			 $data['status'][] = 0;
		}
		return $data1['details'] = $data;
    }



    //multipledeptList($user_role_id)
	 function multipledeptList($user_role_id)
    {
        $data=array();
  			
		$query="SELECT user_department_id AS dept_id FROM erp_multiple_department WHERE emp_number = $user_role_id ";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
					do{ 
						$data1[] = $row['dept_id'];

					}while($row = mysqli_fetch_assoc($count));

					$data['deptmultlist']=$data1;
					$data['status'] = 1;
		}else{
			$data['status'] = 0;
		}
		return $data;
    }





     //multipledeptList($user_role_id)
	 function employeeDetails($emp_number)
    {
        $data=array();
  			
		$query="SELECT *,CONCAT(emp_firstname,' ',emp_lastname) AS emp_name FROM hs_hr_employee WHERE emp_number = $emp_number ";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				
					
						$data['emp_number'] = $row['emp_number'];
						$data['employee_id'] = $row['employee_id'];
						$data['emp_name'] = $row['emp_name'];
						$data['emp_nick_name'] = $row['emp_nick_name'];
						$data['emp_fathername'] = $row['emp_fathername'];
						$data['emp_mothername'] = $row['emp_mothername'];
						$data['emp_smoker'] = $row['emp_smoker'];
						$data['ethnic_race_code'] = $row['ethnic_race_code'];	
						$data['emp_birthday'] = $row['emp_birthday'];
						$data['nation_code'] = $row['nation_code'];
						$data['emp_gender'] = $row['emp_gender'];
						$data['emp_marital_status'] = $row['emp_marital_status'];
						$data['emp_ssn_num'] = $row['emp_ssn_num'];
						$data['emp_sin_num'] = $row['emp_sin_num'];
						$data['emp_other_id'] = $row['emp_other_id'];
						$data['emp_pancard_id'] = $row['emp_pancard_id'];
						$data['emp_uan_num'] = $row['emp_uan_num'];
						$data['emp_pf_num'] = $row['emp_pf_num'];
						$data['emp_dri_lice_num'] = $row['emp_dri_lice_num'];
						$data['emp_dri_lice_exp_date'] = $row['emp_dri_lice_exp_date'];
						$data['emp_military_service'] = $row['emp_military_service'];
						$data['blood_group'] = $row['blood_group'];
						$data['emp_hobbies'] = $row['emp_hobbies'];
						$data['emp_status'] = $row['emp_status'];
						$data['job_title_code'] = $row['job_title_code'];
						$data['eeo_cat_code'] = $row['eeo_cat_code'];
						$data['work_station'] = $row['work_station'];
						$data['department'] = $row['department'];
						$data['emp_street1'] = $row['emp_street1'];
						$data['emp_street2'] = $row['emp_street2'];
						$data['city_code'] = $row['city_code'];
						$data['coun_code'] = $row['coun_code'];
						$data['provin_code'] = $row['provin_code'];
						$data['emp_zipcode'] = $row['emp_zipcode'];
						$data['emp_hm_telephone'] = $row['emp_hm_telephone'];
						$data['emp_mobile'] = $row['emp_mobile'];
						$data['emp_work_telephone'] = $row['emp_work_telephone'];
						$data['emp_work_email'] = $row['emp_work_email'];
						$data['sal_grd_code'] = $row['sal_grd_code'];
						$data['joined_date'] = $row['joined_date'];
						$data['emp_oth_email'] = $row['emp_oth_email'];
						$data['termination_id'] = $row['termination_id'];
						$data['emp_ctc'] = $row['emp_ctc'];
						$data['emp_cost_of_company'] = $row['emp_cost_of_company'];
						$data['emp_gross_salary'] = $row['emp_gross_salary'];
						$data['custom1'] = $row['custom1'];
						$data['custom2'] = $row['custom2'];
						$data['custom3'] = $row['custom3'];
						$data['custom4'] = $row['custom4'];
						$data['custom5'] = $row['custom5'];
						$data['custom6'] = $row['custom6'];
						$data['custom7'] = $row['custom7'];
						$data['custom8'] = $row['custom8'];
						$data['custom9'] = $row['custom9'];
						$data['custom10'] = $row['custom10'];
						$data['plant_id'] = $row['plant_id'];




					$data['empdetails']=$data;
					$data['status'] = 1;
		}else{
			$data['status'] = 0;
		}
		return $data;
    }

    function supervisorEmployeeDetails($emp_number)
    {
        $data=array();
  			
		$query="SELECT *,CONCAT(emp_firstname,' ',emp_lastname) AS emp_name FROM hs_hr_employee WHERE emp_number = $emp_number ";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				
					
						$data['emp_number'] = $row['emp_number'];
						$data['employee_id'] = $row['employee_id'];
						$data['emp_name'] = $row['emp_name'];

					// $data['empdetails']=$data;
					// $data['status'] = 1;
		}else{
			$data['status'] = 0;
		}
		return $data;
    }

// dept lists
     function deptLists($location_id,$plant_id)
  {
    $data= array();
    $result = array();
    //getUser Details
    $query="SELECT name FROM erp_location WHERE id = $location_id";
    $count=mysqli_query($this->conn, $query);

    if(mysqli_num_rows($count) > 0)
    {
      
      $row=mysqli_fetch_assoc($count);
        $data['name']=$row['name']; 
        $locname = $data['name'];

        $query1="SELECT lft,rgt FROM erp_subunit WHERE name = '".$locname."'";
        $count1=mysqli_query($this->conn, $query1);

        if(mysqli_num_rows($count1) > 0)
        {
          $row1=mysqli_fetch_assoc($count1);
          $data['left']=$row1['lft'];
          $data['right']=$row1['rgt'];  
          $lft = $data['left'];
          $rgt = $data['right']; 
                  
          $query2="select plant_name from erp_plant WHERE id = $plant_id";
          $count2=mysqli_query($this->conn, $query2);

            if(mysqli_num_rows($count2) > 0)
            {
              $row=mysqli_fetch_assoc($count2);
              $data['plant_name']=$row['plant_name'];
              $plantname = $data['plant_name'];
              
                $query3="SELECT lft,rgt FROM erp_subunit WHERE name = '$plantname' and lft > ". $lft." and rgt < ".$rgt;
                    $count=mysqli_query($this->conn, $query3);

                      if(mysqli_num_rows($count) > 0)
                      {
                        $row=mysqli_fetch_assoc($count);
                        $data['lft']=$row['lft'];
                        $data['rgt']=$row['rgt']; 
                        $lft = $data['lft'];
                        $rgt = $data['rgt']; 

                      
                          $query4="SELECT * FROM erp_subunit WHERE lft > ". $lft." and rgt < ".$rgt. " ORDER BY name ASC";
                          
                          $count=mysqli_query($this->conn, $query4);

                          if(mysqli_num_rows($count) > 0)
                          {
                        
                            $row=mysqli_fetch_assoc($count);
                            do{ 
                              $result['id']=$row['id'];
                              $result['name']=$row['name'];
                              $data1[] = $result;
                            }while($row = mysqli_fetch_assoc($count));
                              $data['deptlst']=$data1;
                              $data['status'] = 1;
                            
                        
                          }else {
                              //echo "ERROR: Could not able to execute $query4. " . mysqli_error($this->conn);
                                     $data['status']=0;
                            }
                      }else {
                              //echo "ERROR: Could not able to execute $query3. " . mysqli_error($this->conn);
                               $data['status']=0;
                      }
          
            }else {
                  //echo "ERROR: Could not able to execute $query2. " . mysqli_error($this->conn);
                   $data['status']=0;
            } 

        }else {
            //echo "ERROR: Could not able to execute $query1. " . mysqli_error($this->conn);
             $data['status']=0;
          }
        
    }else{
        $data['status']=0;
      }
      
    return $data;     
  }


//funcLocation
    function funcLocationDrpDown($dept_id)
	{
		$data= array();
		//getUser Details
		$query="SELECT id,name FROM erp_functional_location WHERE user_department_id = $dept_id ORDER by name asc";
		$count=mysqli_query($this->conn, $query);

			
			if(mysqli_num_rows($count) > 0)
					{
								$row=mysqli_fetch_assoc($count);
								do{ 
										$data['id']=$row['id'];
										$data['name']=$row['name'];					
										$data1[] = $data;
								}while($row = mysqli_fetch_assoc($count)); 
										$data['funcLocationDrpDownLst']=$data1;
										$data['status']=1;
																											
					}else {
								//echo "ERROR: Could not able to execute $query. " . mysqli_error($this->conn);
																     $data['status']=0;
						 }
	
			
		return $data;     
	}


	function defEngTechResponse($ticket_id,$user_id)
	{


		$data= array();

		$userDetails = $this->getUserRoleByUserId($user_id);
		$empNumber = $userDetails['empNumber'];
		$roleId = $userDetails['id'];
		/*echo $roleId.'/'.$empNumber;
		exit();*/
		/*$empresult=$this->employeeDetails($empNumber);
		$department = $empresult['work_station'];*/

		$empNumber = $this->getEmpnumberByUsrId($user_id);
		$deptId = $this->getDepartmentByTktId($ticket_id);


		// echo $deptId.'/';
		// echo $empNumber;
		// exit();
		
		/*$empNumber = $empdet['empNumber'];
		echo $empNumber;
		exit();*/

		$query1 = "SELECT MAX(ta.forward_from) as empFrom FROM erp_ticket_acknowledgement_action_log ta WHERE ta.forward_to = $empNumber and ta.ticket_id = $ticket_id";
		$count1=mysqli_query($this->conn, $query1);

			
			if(mysqli_num_rows($count1) > 0)
					{
							//echo "if";
								$row1=mysqli_fetch_assoc($count1);
							
										$forwardFrom = $row1['empFrom'];
										if($forwardFrom){
											$userDetails = $this->getUserRoleByEmpNumber($forwardFrom);
											$userRolId = $userDetails['id'];
										}else{
											/*echo "else";
											exit();*/
											$userRolId = 11;
										}
										// echo $forwardFrom;
										// exit();					
										
								
																					
					}


					// if($userRolId  == 10){

									//echo "if 10";

								if($userRolId  == 10 || $userRolId  == 2 || $userRolId  == 19){


										$query = "SELECT h.emp_number AS emp_number, concat(h.emp_firstname,h.emp_middle_name,h.emp_lastname) AS emp_name  FROM hs_hr_employee h LEFT JOIN erp_user o ON h.emp_number = o.emp_number WHERE (o.user_role_id = 11 AND h.work_station = $deptId)";

								}else{

									$empresult=$this->engLists();

										for ($i=0; $i < sizeof($empresult['englist']) ; $i++) { 
							        	$engList[] = $empresult['englist'][$i];
							        	//to convert Array into string the following implode method is used
							        	$engLists = implode(',', $engList);
							        }
										/*$query = "SELECT e.emp_number AS emp_number, concat(e.emp_firstname,e.emp_middle_name,e.emp_lastname) AS emp_name FROM hs_hr_employee e LEFT JOIN erp_ticket_acknowledgement_action_log l ON e.emp_number = l.forward_from WHERE l.id IN (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE ticket_id = $ticket_id AND forward_from !=0)";*/
										$query = "SELECT e.emp_number AS emp_number, concat(e.emp_firstname,e.emp_middle_name,e.emp_lastname) AS emp_name FROM hs_hr_employee e LEFT JOIN erp_ticket_acknowledgement_action_log l ON e.emp_number = l.forward_from WHERE l.id IN (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE ticket_id = $ticket_id AND forward_from IN ($engLists))";

								}
									
									// echo $query;

									$count=mysqli_query($this->conn, $query);

						
										if(mysqli_num_rows($count) > 0)
											{

														$row=mysqli_fetch_assoc($count);
														do{ 
																$data['emp_number']=$row['emp_number'];
																$data['emp_name']=$row['emp_name'];					
																$data1[] = $data;
														}while($row = mysqli_fetch_assoc($count)); 
																$data['defEngforTechres']=$data1;
																$data['status']=1;
																														
												}else {
															//echo "ERROR: Could not able to execute $query. " . mysqli_error($this->conn);
																							     $data['status']=0;
													 }
								

		// 					}else{


		// 						$data= array();

		// $query = "SELECT e.emp_number AS emp_number, concat(e.emp_firstname,e.emp_middle_name,e.emp_lastname) AS emp_name FROM hs_hr_employee e LEFT JOIN erp_ticket_acknowledgement_action_log l ON e.emp_number = l.forward_from WHERE l.id IN (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE ticket_id = $ticket_id AND forward_from !=0)";
		// echo $query;
		 
		// $count=mysqli_query($this->conn, $query);

			
		// 	if(mysqli_num_rows($count) > 0)
		// 			{
		// 						$row=mysqli_fetch_assoc($count);
		// 						do{ 
		// 								$data['emp_number']=$row['emp_number'];
		// 								$data['emp_name']=$row['emp_name'];					
		// 								$data1[] = $data;
		// 						}while($row = mysqli_fetch_assoc($count)); 
		// 								$data['defEngforTechres']=$data1;
		// 								$data['status']=1;
																											
		// 			}else {
		// 						//echo "ERROR: Could not able to execute $query. " . mysqli_error($this->conn);
		// 														     $data['status']=0;
		// 				 }
	
			
		// return $data;   
		// 					}

					
		return $data;     

	}

	function getStarList()
    {
        $data=array();
  			
		$query="SELECT * FROM erp_five_s";

		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
			do{ 
				$data['id'] = $row['id'];
				$data['name'] = $row['name'];

				$data1[]= $data;
			}while($row = mysqli_fetch_assoc($count));
					$data['getStarList']=$data1;
						$data['status'] = 1;
		}
		else
		{
			 //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
			$data['status']=0;
		}		

		/*$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
					do{ 
						$data1['id'] = $row['id'];
						$data1['name'] = $row['name'];

					}while($row = mysqli_fetch_assoc($count));
						$data['getStarList']=$data1;
						$data['status'] = 1;
		}*/
		return $data;
    }

	//EngTechLists
    function EngTechLists($user_role_id,$user_id)
	{
		$data= array();
		//getUser Details
		//echo $user_id;
		$userDetails = $this->getUserRoleByUserId($user_id);
		$empNumber = $userDetails['empNumber'];
		$empresult=$this->employeeDetails($empNumber);
		 $department_Id = $empresult['work_station'];
		/*echo $department_Id.''.$empNumber;
		exit();*/
		$usrRolId =$this->userIdbyUserRoleId($user_id);

		// echo $usrRolId['user_role_id'];
		// exit();

		if($user_role_id == 11){

			/*echo "11";
			exit();*/
			$query="SELECT e.emp_number AS emp_number, concat(e.emp_firstname,' ',e.emp_lastname) as emp_name,e.emp_mobile as mobile FROM hs_hr_employee e LEFT JOIN erp_user u ON u.emp_number = e.emp_number LEFT JOIN erp_plant p ON p.id = e.plant_id WHERE u.user_role_id = $user_role_id AND u.id != $user_id AND p.id != 0 AND e.termination_id IS NULL";
		}
			else if($usrRolId == 10 || $usrRolId == 19){

					//echo "10";
			//exit();
				if($user_role_id == 11)
				{

					$query= "SELECT e.emp_number AS emp_number, concat(e.emp_firstname,' ',e.emp_lastname) as emp_name,e.emp_mobile as mobile FROM hs_hr_employee e LEFT JOIN erp_user u ON u.emp_number = e.emp_number LEFT JOIN erp_plant p ON p.id = e.plant_id WHERE u.user_role_id = 11 AND p.id != 0 AND e.termination_id IS NULL";
				}
				else
				{

					$query="SELECT e.emp_number AS emp_number, concat(e.emp_firstname,' ',e.emp_lastname) as emp_name,e.emp_mobile as mobile FROM hs_hr_employee e LEFT JOIN erp_user u ON u.emp_number = e.emp_number LEFT JOIN erp_plant p ON p.id = e.plant_id WHERE u.user_role_id = 12 AND p.id != 0 AND e.termination_id IS NULL";
				}
					
				
				}else if($user_role_id == 12){


				//echo "12";
			//exit();
					// $SubordinateByEmp = $this->getSubordinateByEmp($empNumber);
					
					if($usrRolId == 12){
						$query = "SELECT e.emp_number AS emp_number,concat(e.emp_firstname,' ',e.emp_lastname) as emp_name,e.emp_mobile as mobile FROM hs_hr_employee e LEFT JOIN erp_user u ON u.emp_number = e.emp_number WHERE u.user_role_id = 12 AND e.work_station = $department_Id AND e.emp_number != $empNumber";

					}else{

					$query="SELECT emp.emp_number AS emp_number, concat(emp.emp_firstname,' ',emp.emp_lastname) as emp_name,emp.emp_mobile as mobile FROM hs_hr_employee emp LEFT JOIN hs_hr_emp_reportto rep ON rep.erep_sub_emp_number = emp.emp_number WHERE rep.erep_sup_emp_number  = $empNumber AND rep.erep_reporting_mode = 1 AND emp.emp_number != $empNumber";
					}
					// $query = "SELECT e.emp_number AS emp_number,concat(e.emp_firstname,' ',e.emp_lastname) as emp_name,e.emp_mobile as mobile FROM hs_hr_employee e
					//  LEFT JOIN erp_user u ON u.emp_number = e.emp_number WHERE u.user_role_id = 12 AND e.work_station = $department_Id AND e.emp_number != $empNumber";

					// $query = " SELECT e.emp_number AS emp_number,concat(e.emp_firstname,' ',e.emp_lastname) as emp_name,e.emp_mobile as mobile FROM hs_hr_employee e
					//  LEFT JOIN erp_user u ON u.emp_number = e.emp_number WHERE u.user_role_id = 12 AND e.work_station = 7 AND e.emp_number != 10";
				}else{


					/*echo "13";
			exit();*/
					$query="SELECT emp.emp_number AS emp_number, concat(emp.emp_firstname,' ',emp.emp_lastname) as emp_name,emp.emp_mobile as mobile FROM hs_hr_employee emp LEFT JOIN hs_hr_emp_reportto rep ON rep.erep_sub_emp_number = emp.emp_number LEFT JOIN erp_user u ON u.emp_number = rep.erep_sup_emp_number WHERE u.id = $user_id AND rep.erep_reporting_mode = 1";
				}
			
		
		$count=mysqli_query($this->conn, $query);

		/*echo $count;

		exit();*/
			
			if(mysqli_num_rows($count) > 0)
					{
								$row=mysqli_fetch_assoc($count);
								do{ 
										$data['emp_number']=$row['emp_number'];
										if($row['mobile'])
											$data['emp_name']=$row['emp_name'].'('.$row['mobile'].')';
										else	
											$data['emp_name']=$row['emp_name'];			
										$data1[] = $data;
								}while($row = mysqli_fetch_assoc($count)); 
										$data['EngTechLists']=$data1;
										$data['status']=1;
																											
					}else {
								//echo "ERROR: Could not able to execute $query. " . mysqli_error($this->conn);
																     $data['status']=0;
						 }
	
			
		return $data; 
	}

	function JobsBasedonDept($userIdPass)
    {
        $data=array();
		//getUser Details

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];
		$empresult=$this->employeeDetails($empNumber);
		$departmentId = $empresult['work_station'];

		/*echo $department;
		exit();*/
       	
       $i=0;
		$query = "SELECT * FROM erp_ticket WHERE user_department_id = $departmentId";	

		
		$configDate = $this->dateFormat();

		$count=mysqli_query($this->conn, $query);

		
		if(mysqli_num_rows($count) > 0)
		{
			
						$row=mysqli_fetch_assoc($count);
					do{ 	

						$i=$i+1;
						$data['sno']=$i;
						//$data['count']=$count;					
						$data['id']=$row['id'];
						$data['job_id']=$row['job_id'];
						/*$data['location_id']=$row['location_id'];
						$data['user_department_id']=$row['user_department_id'];
						$data['notify_to']=$row['notify_to'];
						$data['functional_location_id']=$row['functional_location_id'];
						$data['equipment_id']=$row['equipment_id'];
						$data['plant_id']=$row['plant_id'];
						$data['type_of_issue_id']=$row['type_of_issue_id'];*/
						//$data['status_id']=$row['status_id'];
						/*$data['machine_status']=$row['machine_status'];*/
						//$data['subject']=$row['subject'];
						$text = $row['subject'];
						$data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);
						/*$data['priority_id']=$row['priority_id'];
						$data['severity_id']=$row['severity_id'];
						$data['sla']=$row['sla'];
						$data['reported_by']=$row['reported_by'];
						$data['reported_on']=$row['reported_on'];
						$data['submitted_by']=$row['submitted_by_name'];
						$data['submitted_on']=date($configDate, strtotime( $row['submitted_on'] )).' '.date('H:i', strtotime( $row['submitted_on'] ));
						$data['modified_by_name']=$row['modified_by_name'];
						$data['modified_by_emp_number']=$row['modified_by_emp_number'];
						$data['modified_on']=$row['modified_on'];
						$data['is_PreventiveMaintenance']=$row['is_PreventiveMaintenance'];*/
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['JobsByDept']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
        return $data;
    }

    //JobsHistory($emp_number)
    function JobsHistory($user_id)
    {
        $data=array();

        $usrRolId =$this->userIdbyUserRoleId($user_id);

        $userDetails = $this->getUserRoleByUserId($user_id);
		$empNumber = $userDetails['empNumber'];
		$empresult=$this->employeeDetails($empNumber);
		$departmentId = $empresult['work_station'];
		$plantId = $empresult['plant_id'];



        if($usrRolId == 11)
        {

        	$i = 0;
        	$query1 = "SELECT t.id AS id, st.id as statusId,st.name as statusName,count(*) AS status_count from erp_ticket t LEFT JOIN erp_ticket_status st on t.status_id = st.id where t.status_id NOT IN (1,11) and t.user_department_id = $departmentId GROUP BY t.status_id";

						$configDate = $this->dateFormat();
		
		$count1 = mysqli_query($this->conn, $query1);

		if(mysqli_num_rows($count1) > 0)
		{
						$row=mysqli_fetch_assoc($count1);
					do{ 		
						$i = $i+1;

						$data['sno']=$i;				
						$data['statusId']=$row['statusId'];
						$data['status_count']=$row['status_count'];
						$data['statusName']=$row['statusName'];

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count1));
						$data['JobsHistory']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}


        }
        else if($usrRolId == 10)
        {


        	$i = 0;
        	$query1 = "SELECT t.id AS id, st.id as statusId,st.name as statusName,count(*) AS status_count from erp_ticket t LEFT JOIN erp_ticket_status st on t.status_id = st.id where t.status_id NOT IN (1,11) and t.plant_id = $plantId GROUP BY t.status_id";

						$configDate = $this->dateFormat();
		
		$count1 = mysqli_query($this->conn, $query1);

		if(mysqli_num_rows($count1) > 0)
		{
						$row=mysqli_fetch_assoc($count1);
					do{ 		
						$i = $i+1;

						$data['sno']=$i;				
						$data['statusId']=$row['statusId'];
						$data['statusName']=$row['statusName'];
						$data['status_count']=$row['status_count'];
						

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count1));
						$data['JobsHistory']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}



        }

        else
        {

        	$data['status']=0;

        }
       
       
			
        return $data;
    }

    function JobsDeptHistory($user_id,$status_id)
    {
        $data=array();

        $usrRolId =$this->userIdbyUserRoleId($user_id);

        $userDetails = $this->getUserRoleByUserId($user_id);
		$empNumber = $userDetails['empNumber'];
		$empresult=$this->employeeDetails($empNumber);
		$departmentId = $empresult['work_station'];
		$plantId = $empresult['plant_id'];

		/*echo $departmentId. ''.$status_id;
		exit();*/

		 if($usrRolId == 11)
		 {


        	$i = 0;
        	$query1 = "SELECT t.user_department_id AS deptId, su.name as departmentName,COUNT(*) AS subJobscount FROM erp_ticket t LEFT JOIN erp_subunit su ON t.user_department_id = su.id WHERE t.status_id = $status_id AND t.user_department_id = $departmentId GROUP BY t.user_department_id";

        	/*echo $query1;
        	exit();*/

						$configDate = $this->dateFormat();
		
		$count1 = mysqli_query($this->conn, $query1);

		if(mysqli_num_rows($count1) > 0)
		{
						$row=mysqli_fetch_assoc($count1);
					do{ 		
						$i = $i+1;

						$data['sno']=$i;				
						$data['deptId']=$row['deptId'];
						$data['departmentName']=$row['departmentName'];
						$data['subJobsCount']=$row['subJobsCount'];

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count1));
						$data['JobsDeptHistory']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}

		}

		else if($usrRolId == 10)

		{


			$i = 0;
        	$query1 = "SELECT t.user_department_id AS deptId, su.name as departmentName,COUNT(*) AS subJobscount FROM erp_ticket t LEFT JOIN erp_subunit su ON t.user_department_id = su.id WHERE t.status_id = $status_id AND t.plant_id = $plantId GROUP BY t.user_department_id";

						$configDate = $this->dateFormat();
		
		$count1 = mysqli_query($this->conn, $query1);

		if(mysqli_num_rows($count1) > 0)
		{
						$row=mysqli_fetch_assoc($count1);
					do{ 		
						$i = $i+1;

						$data['sno']=$i;				
						$data['deptId']=$row['deptId'];
						$data['departmentName']=$row['departmentName'];
						$data['subJobscount']=$row['subJobscount'];

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count1));
						$data['JobsDeptHistory']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}


		}


        			
        return $data;
    }


    function JobsDeptTechHistory($user_id,$status_id,$deptId)
    {
        $data=array();

        $usrRolId =$this->userIdbyUserRoleId($user_id);

        $userDetails = $this->getUserRoleByUserId($user_id);
		$empNumber = $userDetails['empNumber'];
		$empresult=$this->employeeDetails($empNumber);
		$departmentId = $empresult['work_station'];
		$plantId = $empresult['plant_id'];


		 if($usrRolId == 11)
		 {


        	$i = 0;
        	$query1 = "SELECT emp.emp_number AS employeeNumber, concat(emp.emp_firstname,' ',emp.emp_middle_name,' ',emp.emp_lastname) AS name, usr.id as userId,COUNT(*) as engineer_count FROM erp_ticket t 
          LEFT JOIN erp_ticket_acknowledgement_action_log l ON l.ticket_id = t.id
          LEFT JOIN hs_hr_employee emp ON emp.emp_number IN (IF(l.accepted_by!=0,l.accepted_by,l.forward_from))
          LEFT JOIN erp_user usr ON usr.emp_number = emp.emp_number
          WHERE t.status_id = $status_id and t.user_department_id = $deptId AND l.id IN (
              SELECT MIN(log.id) FROM erp_ticket_acknowledgement_action_log log 
              LEFT JOIN hs_hr_employee e ON e.emp_number IN (IF(log.accepted_by!=0,log.accepted_by,log.forward_from))
            LEFT JOIN erp_user ou ON ou.emp_number = e.emp_number
              WHERE ou.user_role_id IN (12)
              GROUP BY log.ticket_id)
              GROUP BY emp.emp_number";

						$configDate = $this->dateFormat();
		
		$count1 = mysqli_query($this->conn, $query1);

		if(mysqli_num_rows($count1) > 0)
		{
						$row=mysqli_fetch_assoc($count1);
					do{ 		
						$i = $i+1;

						$data['sno']=$i;				
						$data['employeeNumber']=$row['employeeNumber'];
						$data['userId']=$row['userId'];
						$data['name']=$row['name'];
						$data['engineer_count']=$row['engineer_count'];

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count1));
						$data['JobsDeptTechHistory']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}

		}

		else

		{


			$i = 0;
        	$query2 = "SELECT emp.emp_number AS employeeNumber, concat(emp.emp_firstname,' ',emp.emp_middle_name,' ',emp.emp_lastname) AS name, usr.id as userId,COUNT(*) as engineer_count FROM erp_ticket t LEFT JOIN erp_ticket_acknowledgement_action_log l ON l.ticket_id = t.id LEFT JOIN hs_hr_employee emp ON emp.emp_number IN (IF(l.accepted_by!=0,l.accepted_by,l.forward_from)) LEFT JOIN erp_user usr ON usr.emp_number = emp.emp_number WHERE t.status_id = $status_id and t.user_department_id = $deptId AND l.id IN ( SELECT MIN(log.id) FROM erp_ticket_acknowledgement_action_log log LEFT JOIN hs_hr_employee e ON e.emp_number IN (IF(log.accepted_by!=0,log.accepted_by,log.forward_from)) LEFT JOIN erp_user ou ON ou.emp_number = e.emp_number WHERE ou.user_role_id IN (10,11,13) GROUP BY log.ticket_id) GROUP BY emp.emp_number";

						$configDate = $this->dateFormat();
		
		$count2 = mysqli_query($this->conn, $query2);

		if(mysqli_num_rows($count2) > 0)
		{
						$row=mysqli_fetch_assoc($count2);
					do{ 		
						$i = $i+1;

						$data['sno']=$i;				
						$data['employeeNumber']=$row['employeeNumber'];
						$data['userId']=$row['userId'];
						$data['name']=$row['name'];
						$data['engineer_count']=$row['engineer_count'];

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count2));
						$data['JobsDeptTechHistory']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}


		}


        			
        return $data;
    }




     function JobsTechncnHistory($user_id,$status_id,$employee_number)
    {
        $data=array();

        $usrRolId =$this->userIdbyUserRoleId($user_id);

        $userDetails = $this->getUserRoleByUserId($user_id);
		$empNumber = $userDetails['empNumber'];
		$empresult=$this->employeeDetails($empNumber);
		$departmentId = $empresult['work_station'];
		$plantId = $empresult['plant_id'];


		if($usrRolId == 11)

		{

			$i = 0;
        	$query1 = "SELECT t.id,t.job_id as job_id, t.subject as subject, t.description AS description, fl.name AS fun_loc,e.name AS equ_name FROM erp_ticket t LEFT JOIN erp_ticket_acknowledgement_action_log tal ON tal.ticket_id = t.id LEFT JOIN erp_functional_location fl ON fl.id = t.functional_location_id LEFT JOIN erp_equipment e ON e.id = t.equipment_id LEFT JOIN hs_hr_employee emp ON emp.emp_number IN (IF(tal.accepted_by!=0,tal.accepted_by,tal.forward_from)) WHERE t.status_id = $status_id AND t.user_department_id = $departmentId AND emp.emp_number = $employee_number GROUP BY t.id";



						$configDate = $this->dateFormat();
		
		$count1 = mysqli_query($this->conn, $query1);

		if(mysqli_num_rows($count1) > 0)
		{
						$row=mysqli_fetch_assoc($count1);
					do{ 		
						$i = $i+1;

						$data['sno']=$i;				
						$data['job_id']=$row['job_id'];
						$data['subject']=$row['subject'];
						$data['description']=$row['description'];
						$data['fun_loc']=$row['fun_loc'];
						$data['equ_name']=$row['equ_name'];

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count1));
						$data['JobsTechncnHistory']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}



		}

		else
		{


				$i = 0;
        	$query = "SELECT t.id,t.job_id as job_id, t.subject as subject, t.description AS description, fl.name AS fun_loc,e.name AS equ_name FROM erp_ticket t LEFT JOIN erp_ticket_acknowledgement_action_log tal ON tal.ticket_id = t.id LEFT JOIN erp_functional_location fl ON fl.id = t.functional_location_id LEFT JOIN erp_equipment e ON e.id = t.equipment_id LEFT JOIN hs_hr_employee emp ON emp.emp_number IN (IF(tal.accepted_by!=0,tal.accepted_by,tal.forward_from)) WHERE t.status_id = $status_id
        		AND t.plant_id = $plantId AND emp.emp_number = $employee_number GROUP BY t.id";



						$configDate = $this->dateFormat();
		
		$count = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
						$row=mysqli_fetch_assoc($count);
					do{ 		
						$i = $i+1;

						$data['sno']=$i;				
						//$data['sno']=$i;				
						$data['job_id']=$row['job_id'];
						//$data['subject']=$row['subject'];
						$text = $row['subject'];
						$data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);
						$data['description']=$row['description'];
						$data['fun_loc']=$row['fun_loc'];
						$data['equ_name']=$row['equ_name'];

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['JobsTechncnHistory']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}

		}

        
        			
        return $data;
    }


 function MachineWiseBrkDwnAndCount($user_id)
    {
        $data=array();

        $i=0;
        /*$query1 = "SELECT t.equipment_id as eqId,eqp.name as equipmentName,t.job_id as JobId FROM erp_ticket t LEFT JOIN erp_equipment eqp ON t.equipment_id = eqp.id WHERE t.status_id IN (10,5) GROUP BY t.equipment_id";*/

       /* $query1 = "SELECT t.equipment_id as eqId,eqp.name as equipmentName,t.submitted_on as createdOn, COUNT(*) as jobEqCount FROM erp_ticket t LEFT JOIN erp_equipment eqp ON t.equipment_id = eqp.id WHERE t.status_id IN (10,5) AND t.equipment_id IN ( SELECT t.equipment_id FROM erp_ticket t LEFT JOIN erp_equipment eqp ON t.equipment_id = eqp.id WHERE t.status_id IN (10,5) GROUP BY t.equipment_id ) GROUP BY t.equipment_id ORDER BY createdOn DESC";*/


        $query1 = "SELECT t.equipment_id as eqId,eqp.name as equipmentName,t.submitted_on as createdOn, COUNT(*) as jobEqCount,loc.name as 			functionallocation_name,loc.id as functionlocation_id FROM erp_ticket t
					LEFT JOIN erp_equipment eqp ON t.equipment_id = eqp.id
					LEFT JOIN erp_functional_location loc ON loc.id = t.functional_location_id 
					WHERE t.status_id IN (10,5)
					AND t.equipment_id IN ( SELECT t.equipment_id FROM erp_ticket t LEFT JOIN erp_equipment eqp ON t.equipment_id = eqp.id
					WHERE t.status_id IN (10,5) GROUP BY t.equipment_id ) GROUP BY t.equipment_id ORDER BY createdOn DESC";

						$configDate = $this->dateFormat();
		
		$count1 = mysqli_query($this->conn, $query1);

		$Count = mysqli_num_rows($count1);

		//echo $Count;

		if(mysqli_num_rows($count1) > 0)
		{
						$row=mysqli_fetch_assoc($count1);

					do{ 		
						//$i = $i+1;

						//$data['sno']=$i;	
						$data['eqId']= $row['eqId'];
						
						$data['equipmentName']=$row['equipmentName'];

						$data['jobEqCount']= $row['jobEqCount'];


						$funLoc = $this->subfunctionalLocations($row['functionlocation_id']);
				if($funLoc['status'] == 1){
					$data['functionlocation_id']=$funLoc['id'];
					$data['functionallocation_name']=$funLoc['name'];
					$data['subfunctionlocation_id']=$row['functionlocation_id'];
					$data['subfunctionallocation_name']=$row['functionallocation_name'];
				}else{
					$data['functionlocation_id']=$row['functionlocation_id'];
					$data['functionallocation_name']=$row['functionallocation_name'];
					$data['subfunctionlocation_id']=0;
					$data['subfunctionallocation_name']='';
				}			
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count1));
						$data['machineWiseBreakdown']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}

        			
        return $data;
    }

    function EqpmntJobCountEqId($user_id, $equipmntId)
    {
        $data=array();

        $i=0;
        $query1 = "SELECT t.equipment_id as eqId,eqp.name as equipmentName,t.job_id as JobId, t.submitted_on as createdDate FROM erp_ticket t LEFT JOIN erp_equipment eqp ON t.equipment_id = eqp.id WHERE t.status_id IN (10,5) AND t.equipment_id = $equipmntId";


						$configDate = $this->dateFormat();
		
		$count1 = mysqli_query($this->conn, $query1);

		$Count = mysqli_num_rows($count1);

		//echo $Count;

		if(mysqli_num_rows($count1) > 0)
		{
						$row=mysqli_fetch_assoc($count1);

					//do{ 		
						//$i = $i+1;

						//$data['sno']=$i;	
						$data['eqmntJobCount']= $Count;
						
						//$data['equipmentName']=$row['equipmentName'];	
						//$data['createdDate']= $row['createdDate'];		
						
						$data1[] = $data;
					//}while($row = mysqli_fetch_assoc($count1));
						$data['EqpmntJobCountEqId']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}

        			
        return $data;
    }


    function EqpmntDetailsBasedOnEqId($user_id, $equipmntId)
    {
        $data=array();

        $i=0;
        $query1 = "SELECT t.equipment_id as eqId,eqp.name as equipmentName,t.job_id as JobId, t.submitted_on as createdDate,t.id as ticket_id FROM erp_ticket t LEFT JOIN erp_equipment eqp ON t.equipment_id = eqp.id WHERE t.status_id IN (10,5) AND t.equipment_id = $equipmntId";

        $count1 = mysqli_query($this->conn, $query1);

        if(mysqli_num_rows($count1) > 0)

		{

			//echo "if";
						$row=mysqli_fetch_assoc($count1);

					
						

						//echo $ticket_id;

						do{ 

							//echo "do";

							$ticket_id= $row['ticket_id'];
							//echo $ticket_id."\n";
							$job_id = $row['JobId'];
							//echo $job_id."\n";
							$eq_id = $row['eqId'];
							//echo $eq_id."\n";
							$eq_name = $row['equipmentName'];
							//echo $eq_name."\n";
							$created_date = $row['createdDate'];
							//echo $created_date."\n";

								$i = $i+1;
							//$data['eqId']= $row['eqId'];
							$data['sno']=$i;	
								$data['eqId']= $row['eqId'];
								//echo $eqId;
								$data['JobId']= $row['JobId'];
								//echo $eqId;
								$data['ticket_id']= $row['ticket_id'];
								$data['equipmentName']=$row['equipmentName'];	
								$data['createdDate']= $row['createdDate'];

							
							

												$configDate = $this->dateFormat();

								$query2 = "SELECT submitted_on as suboncom FROM `erp_ticket_acknowledgement_action_log` WHERE ticket_id = $ticket_id AND status_id = 14";

									 $count2 = mysqli_query($this->conn, $query2);

									 $Count = mysqli_num_rows($count2);

									 //echo $Count;
									 //exit();
									  if(mysqli_num_rows($count2) > 0)
								{

									
												$row = mysqli_fetch_assoc($count2);

											
													$suboncom = $row['suboncom']; 
													//echo $suboncom;

												
								}else{

										$suboncom = '';
										$data['status']=0;
									}


									$query3 = "SELECT submitted_on as subonnew FROM `erp_ticket_acknowledgement_action_log` WHERE ticket_id = $ticket_id AND status_id = 1";

						       $count3 = mysqli_query($this->conn, $query3);

						       if(mysqli_num_rows($count3) > 0)
								{

										
												$row = mysqli_fetch_assoc($count3);

											
													$subonnew = $row['subonnew']; 
													/*echo $subonnew;
												exit();*/	
												
								}else{

										
										$data['status']=0;
									}


									
							//echo $ticket_id."\n";

									 $query4 = "SELECT submitted_on as subonEndcom FROM `erp_ticket_acknowledgement_action_log` WHERE ticket_id = $ticket_id AND status_id = 10";

									 $count4 = mysqli_query($this->conn, $query4);

									  if(mysqli_num_rows($count4) > 0)
								{

									//echo "count4 if";
												$row = mysqli_fetch_assoc($count4);

											
													$subonEndcom = $row['subonEndcom']; 
													//echo $suboncom;

												
								}else{

										//echo "count4 else";

											$subonEndcom = '';
										$data['status']=0;
									}


									/*echo 'com'.$suboncom.' ';
									echo 'new'.$subonnew.' ';
									echo 'endcom'.$subonEndcom.' ';
									exit();*/
									if(($suboncom != '') && ($subonEndcom != ''))
									{


										$dteStart = new DateTime($subonnew); 

											
								   			$dteEnd   = new DateTime($suboncom);

								   			

								   			$dteDiff  = $dteEnd->diff($dteStart); 


								   			
								   			$resolvedDuration = $dteDiff->format("%H:%I"); 
								   			
								   			$interval = $dteStart->diff($dteEnd);
											
											$dys = $interval->format('%a');
											
								                                    $hrs = $interval->format('%h');
								            
								                                    $mins = $interval->format('%i');

								                                    $secs = $interval->format('%s');

								                //echo 'mins'.$mins;

								                 $hrs += $dys * 24;

								                 $ResolvedTime = $hrs.':'.$mins.':'.$secs;

								                 $dteComEnd   = new DateTime($subonEndcom);
								                 $dteDiff1  = $dteComEnd->diff($dteEnd); 

												$resolvedDuration1 = $dteDiff1->format("%H:%I"); 
								   			
								   			$interval = $dteEnd->diff($dteComEnd);
											
											$dys = $interval->format('%a');
											
								                                    $hrs = $interval->format('%h');
								            
								             //echo 'hrs'.$hrs. "\n";
								                                    $mins = $interval->format('%i');

								                                     $secs = $interval->format('%s');
								                //echo 'mins'.$mins. "\n";

								                 $hrs += $dys * 24;

								                 //echo 'hrs'.$hrs. "\n";

								                 $CompletedTime = $hrs.':'.$mins.':'.$secs;

								                 //$dteComEnd   = new DateTime($subonEndcom);
								                 $dteDiff2  = $dteComEnd->diff($dteStart); 

												$resolvedDuration1 = $dteDiff2->format("%H:%I"); 
								   			
								   			$interval = $dteStart->diff($dteComEnd);
											
											$dys = $interval->format('%a');
											
								                                    $hrs = $interval->format('%h');
								            
								                                    $mins = $interval->format('%i');

								                                    $secs = $interval->format('%s');

								                //echo 'mins'.$mins;

								                 $hrs += $dys * 24;

								                 $TotalCompletedTime = $hrs.':'.$mins.':'.$secs;


								                 $data['ResolvedTime']= $ResolvedTime;
						$data['CompletedTime']= $CompletedTime;	
						$data['TotalCompletedTime']= $TotalCompletedTime;

									}

									else
									{

										$dteStart1 = new DateTime($subonnew); 

										//echo 'sub'.$subonnew;
										

										  $dteComEnd1   = new DateTime($subonEndcom);

										  //echo 'end'.$subonEndcom;
										//exit();


										 $dteDiff3  = $dteComEnd1->diff($dteStart1); 

												$resolvedDuration2 = $dteDiff3->format("%H:%I"); 
								   			
								   			$interval1 = $dteStart1->diff($dteComEnd1);
											
											$dys1 = $interval1->format('%a');

											 //echo 'dys1'.$dys1. "\n";
											
								                                    $hrs1 = $interval1->format('%h');
								            
								                                    $mins1 = $interval1->format('%i');

								                                     $secs1 = $interval->format('%s');

								                //echo 'mins1'.$mins1. "\n";

								                 $hrs1 += $dys1 * 24;

								                   //echo 'hrs1'.$hrs1. "\n";

								                 $ResolvedTime1 = '';

								                  $CompletedTime1 = $hrs1.':'.$mins1.':'.$secs1;

								                 $TotalCompletedTime1 = $hrs1.':'.$mins1.':'.$secs1;
										/*$ResolvedTime = '';
										 $CompletedTime = '';

										 $TotalCompletedTime = '';
*/

										 $data['ResolvedTime']= $ResolvedTime1;
						$data['CompletedTime']= $CompletedTime1;	
						$data['TotalCompletedTime']= $TotalCompletedTime1;
										//exit();

									}
						
							
						//$data['eqmntJobCount']= $Count;
						
						
						
								
								$data1[] = $data;
							}while($row = mysqli_fetch_assoc($count1));
								$data['EqpmntDetailsBasedOnEqId']=$data1;
								$data['status']=1;


						
						
						
							
		}else{
				//echo "last else";
				$data['status']=0;
			}

			

			
        			
        return $data;
    }


 function MaintenanceTypeReport($user_id)
    {
        $data=array();

        $query = "SELECT t.job_id AS jobId, t.id AS ticket_id, t.subject AS subject, t.submitted_on AS calFromDate, t.submitted_on AS calToDate, t.submitted_on AS createdOn, ta.machine_status AS machineStatus, fl.name as functionalLocation, fl.id as functionalLocationId,toi.name AS typeOfIssue, toi.id AS typeOfIssueId, toi.sla AS sla,loc.name AS location, loc.id AS locationId, plnt.plant_name AS plantName, plnt.id AS plantId, eq.name AS equipment, eq.id AS equipmentId, ts.name AS status, ts.id AS statusId, ta.ticket_id AS ticketId, t.submitted_by_name AS submittedByName, e.emp_number AS engineerId, e.emp_number AS technicianId,tp.name AS priority, tp.id AS priorityId, tsev.name AS severity, tsev.id AS severityId, u.id AS uaerId, msr.id AS scheduleId, msr.maintenance_type_id AS maintenanceType,mt.id AS maintenanceId, mt.name AS maintenanceName,cs.name AS subDivision, cs.id AS subDivisionId FROM erp_ticket t LEFT JOIN erp_functional_location fl ON fl.id = t.functional_location_id LEFT JOIN erp_type_of_issue toi ON toi.id = t.type_of_issue_id LEFT JOIN erp_location loc ON loc.id = t.location_id LEFT JOIN erp_plant plnt ON plnt.id = t.plant_id LEFT JOIN erp_equipment eq ON eq.id = t.equipment_id LEFT JOIN erp_ticket_status ts ON ts.id = t.status_id LEFT JOIN erp_ticket_acknowledgement_action_log ta ON ta.ticket_id = t.id LEFT JOIN hs_hr_employee e ON e.emp_number = ta.submitted_by_emp_number LEFT JOIN erp_user u ON u.id = ta.created_by_user_id LEFT JOIN erp_ticket_priority tp ON tp.id = t.priority_id LEFT JOIN erp_ticket_severity tsev ON tsev.id = t.severity_id LEFT JOIN erp_subunit cs ON cs.id = t.user_department_id LEFT JOIN erp_maintenance_schedule msr ON msr.ticket_id = t.id LEFT JOIN erp_maintenance_type mt ON mt.id = msr.maintenance_type_id WHERE t.status_id != 11 AND t.is_PreventiveMaintenance = 1 AND t.status_id IN (10, 5) GROUP BY t.id ORDER BY createdOn DESC";



        	 $count = mysqli_query($this->conn, $query);

       
			if(mysqli_num_rows($count) > 0)
		{
						$row=mysqli_fetch_assoc($count);

						$i = 0;

					do{ 	

						$i = $i+1;

						$data['sno']=$i;

						$ticket_id= $row['ticket_id'];
						//$ticket_id= 553;
						//echo $ticket_id;
						$data['jobId']=$row['jobId'];	
						$data['equipment']= $row['equipment'];
						$data['createDate']= $row['createdOn'];
						$data['maintenanceName']= $row['maintenanceName'];

						

       $query2 = "SELECT submitted_on as suboncom FROM `erp_ticket_acknowledgement_action_log` WHERE ticket_id = $ticket_id AND status_id = 14";

									 $count2 = mysqli_query($this->conn, $query2);

									  if(mysqli_num_rows($count2) > 0)
								{

									//echo "count2 if";
												$row = mysqli_fetch_assoc($count2);

											
													$suboncom = $row['suboncom']; 
													//echo 'suboncom'.$suboncom;

												
								}else{

										//echo "count2 else";
										$suboncom = ' ';
										//$data['status']=0;
									}

									//$ticket_id1 = $ticket_id;
									//echo 'ticket_id1'.$ticket_id1;
									//echo 'ticket_id1'.$ticket_id;
									$query3 = "SELECT submitted_on as subonnew FROM `erp_ticket_acknowledgement_action_log` WHERE ticket_id = $ticket_id AND status_id = 1";

						       $count3 = mysqli_query($this->conn, $query3);

						       if(mysqli_num_rows($count3) > 0)
								{

										//echo "count3 if";
												$row = mysqli_fetch_assoc($count3);

											
													$subonnew = $row['subonnew']; 
													//echo 'subonnew'.$subonnew;	
												
								}



									//$ticket_id2 = $ticket_id;
									//echo 'ticket_id2'.$ticket_id2;
									//echo 'ticket_id2'.$ticket_id;
									 $query4 = "SELECT submitted_on as subonEndcom FROM `erp_ticket_acknowledgement_action_log` WHERE ticket_id = $ticket_id AND status_id = 10";

									 $count4 = mysqli_query($this->conn, $query4);

									  if(mysqli_num_rows($count4) > 0)
								{

									//echo "count4 if";
												$row = mysqli_fetch_assoc($count4);

											
													$subonEndcom = $row['subonEndcom']; 
													//echo 'subonEndcom'.$subonEndcom;

												
								}else{

										//echo "count4 else";
										$subonEndcom =  ' ';
										//echo $subonEndcom;
									}


										//echo 'subonnew'.$subonnew;
							//$dteStart = new DateTime($subonnew); 

										//echo $suboncom;
										//exit();
									
									if($suboncom == ' ')
									{
										//echo "if";
										$ResolvedTime = '--:--';


								                //echo 'after';
								   		//echo 'subonEndcom1'.$subonEndcom;
								   		$dteComEnd   = new DateTime($subonEndcom);

								   		$dteEnd   = new DateTime($suboncom);
								   		$dteStart   = new DateTime($subonnew);
								   		$dteDiff  = $dteEnd->diff($dteStart);
								                 $dteDiff1  = $dteComEnd->diff($dteEnd); 

												$resolvedDuration1 = $dteDiff1->format("%H:%I"); 
								   			
								   			$interval = $dteEnd->diff($dteComEnd);
											
											$dys = $interval->format('%a');
											
								                                    $hrs = $interval->format('%h');
								            
								                                    $mins = $interval->format('%i');

								                                     $secs = $interval->format('%s');

								                //echo 'mins'.$mins;

								                 $hrs += $dys * 24;

								                 $CompletedTime = $hrs.':'.$mins.':'.$secs;

								                 //$dteComEnd   = new DateTime($subonEndcom);
								                 $dteDiff2  = $dteComEnd->diff($dteStart); 

												$resolvedDuration1 = $dteDiff2->format("%H:%I"); 
								   			
								   			$interval = $dteStart->diff($dteComEnd);
											
											$dys = $interval->format('%a');
											
								                                    $hrs = $interval->format('%h');
								            
								                                    $mins = $interval->format('%i');

								                                    $secs = $interval->format('%s');

								                //echo 'mins'.$mins;

								                 $hrs += $dys * 24;

								                 $TotalCompletedTime = $hrs.':'.$mins.':'.$secs;	 

									}	

									else 
									{

										//echo "else";
										$dteEnd   = new DateTime($suboncom);

								   			$dteStart   = new DateTime($subonnew);

								   			$dteDiff  = $dteEnd->diff($dteStart); 


								   			
								   			$resolvedDuration = $dteDiff->format("%H:%I"); 
								   			
								   			$interval = $dteStart->diff($dteEnd);
											
											$dys = $interval->format('%a');
											
								                                    $hrs = $interval->format('%h');
								            
								                                    $mins = $interval->format('%i');

								                                    $secs = $interval->format('%s');
								                //echo 'mins'.$mins;

								                 $hrs += $dys * 24;

								                 $ResolvedTime = $hrs.':'.$mins.':'.$secs;

								                //echo 'after';
								   		//echo $subonEndcom;
								   		$dteComEnd   = new DateTime($subonEndcom);
								                 $dteDiff1  = $dteComEnd->diff($dteEnd); 

												$resolvedDuration1 = $dteDiff1->format("%H:%I"); 
								   			
								   			$interval = $dteEnd->diff($dteComEnd);
											
											$dys = $interval->format('%a');
											
								                                    $hrs = $interval->format('%h');
								            
								                                    $mins = $interval->format('%i');

								                                    $secs = $interval->format('%s');
								                //echo 'mins'.$mins;

								                 $hrs += $dys * 24;

								                 $CompletedTime = $hrs.':'.$mins.':'.$secs;

								                 //$dteComEnd   = new DateTime($subonEndcom);
								                 $dteDiff2  = $dteComEnd->diff($dteStart); 

												$resolvedDuration1 = $dteDiff2->format("%H:%I"); 
								   			
								   			$interval = $dteStart->diff($dteComEnd);
											
											$dys = $interval->format('%a');
											
								                                    $hrs = $interval->format('%h');
								            
								                                    $mins = $interval->format('%i');

								                                     $secs = $interval->format('%s');

								                //echo 'mins'.$mins;

								                 $hrs += $dys * 24;

								                 $TotalCompletedTime = $hrs.':'.$mins.':'.$secs;	 
							

									}	
								   			
						//$data['eqmntJobCount']= $Count;
						
						
						$data['ResolvedTime']= $ResolvedTime;
						$data['CompletedTime']= $CompletedTime;	
						$data['TotalCompletedTime']= $TotalCompletedTime;	
								
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['MaintenanceTypeReport']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}

        return $data;

    }

     function ticketDetails($userIdPass)
	{
		$data= array();
		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];
		$empresult=$this->employeeDetails($empNumber);
		$department = $empresult['work_station'];
		$name = '';
		$emp_name = '';
		$i=0;

		
		  //$userDetails = $this->getUserRoleByUserId($user_id);
		$emp_number = $userDetails['empNumber'];
		//echo $emp_number;
		$userRoleId = $userDetails['id'];

		 if($userRoleId == 10){



		 	$query = "SELECT t.job_id AS job_id,t.id AS ticketId, t.functional_location_id AS funLocId, t.id AS id, t.subject AS subject, t.submitted_on AS calFromDate, t.submitted_on AS calToDate, t.submitted_on AS createdOn, ta.machine_status AS machineStatus, fl.name as functionallocation_name, fl.id as functionlocation_id,
		 	t.is_PreventiveMaintenance AS preventiveMaintenance,
                    toi.name AS typeOfIssue, toi.id AS typeOfIssueId, toi.sla AS sla,
                    loc.name AS location, loc.id AS locationId,
                    plnt.plant_name AS plantName, plnt.id AS plantId,
                    eq.name AS equipment, eq.id AS equipmentId,
                    ts.name AS status, ts.id AS statusId,
                    ta.ticket_id AS ticketId, t.submitted_by_name AS submittedByName, e.emp_number AS engineerId, e.emp_number AS technicianId,
                    tp.name AS priority, tp.id AS priorityId,
                    tsev.name AS severity, tsev.id AS severityId,
                    u.id AS uaerId,
                    msr.id AS scheduleId, msr.maintenance_type_id AS maintenanceType,
                    mt.id AS maintenanceId, mt.name AS maintenanceName,
                    cs.name AS subDivision, cs.id AS subDivisionId
				FROM erp_ticket t
                LEFT JOIN erp_functional_location fl ON fl.id = t.functional_location_id 
                LEFT JOIN erp_type_of_issue toi ON toi.id = t.type_of_issue_id 
                LEFT JOIN  erp_location loc ON loc.id = t.location_id 
                LEFT JOIN  erp_plant plnt ON plnt.id = t.plant_id 
                LEFT JOIN  erp_equipment eq ON eq.id = t.equipment_id 
                LEFT JOIN  erp_ticket_status ts ON ts.id = t.status_id
                LEFT JOIN  erp_ticket_acknowledgement_action_log ta ON ta.ticket_id = t.id
                LEFT JOIN  hs_hr_employee e ON e.emp_number = ta.submitted_by_emp_number
                LEFT JOIN  erp_user u ON u.id = ta.created_by_user_id 
                LEFT JOIN  erp_ticket_priority tp ON tp.id = t.priority_id
                LEFT JOIN  erp_ticket_severity tsev ON tsev.id = t.severity_id
                LEFT JOIN erp_subunit cs ON cs.id = t.user_department_id
                LEFT JOIN erp_maintenance_schedule msr ON msr.ticket_id = t.id
                LEFT JOIN erp_maintenance_type mt ON mt.id = msr.maintenance_type_id
                GROUP BY t.id
				ORDER BY `t`.`job_id`  DESC";


				$configDate = $this->dateFormat();

			$count=mysqli_query($this->conn, $query);

				if(mysqli_num_rows($count) > 0)
				{
					// $row=mysqli_fetch_all($count,MYSQLI_ASSOC);
					while($row = mysqli_fetch_assoc($count)) { 	

						$i=$i+1;
						
						if($row['preventiveMaintenance'] == 1){
							$data['sno']=$i;
							$data['ticketId']=$row['ticketId'];
						$data['job_id']=$row['job_id'];
						$data['subject']= 'PM - '.$row['equipment'];
						$data['issue']=$row['typeOfIssue'];
						$data['location']=$row['location'];
						$data['plant']= $row['plantName'];
						$data['department']=$row['subDivision'];
						$data['functionalLocation']=$row['functionallocation_name'];
						$funLoc = $this->subfunctionalLocations($row['functionlocation_id']);
											if($funLoc['status'] == 1){
												$data['functionlocation_id']=$funLoc['id'];
												$data['functionallocation_name']=$funLoc['name'];
												$data['subfunctionlocation_id']=$row['functionlocation_id'];
												$data['subfunctionallocation_name']=$row['functionallocation_name'];
											}else{
												$data['functionlocation_id']=$row['functionlocation_id'];
												$data['functionallocation_name']=$row['functionallocation_name'];
												$data['subfunctionlocation_id']=0;
												$data['subfunctionallocation_name']='';
											}

						$data['equipment']= $row['equipment'];
						$data['submittedby']=$row['submittedByName'];
						$data['submittedon']=date($configDate, strtotime( $row['createdOn'] )).' '.date('H:i:s', strtotime( $row['createdOn'] ));
						$data['status']= $row['status'];
	
						}else{
							$data['sno']=$i;
							$data['ticketId']=$row['ticketId'];
						$data['job_id']=$row['job_id'];
						$text = $row['subject'];
						$data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);
						//$data['subject']= iconv('UTF-8', 'ASCII//IGNORE', utf8_encode($text));
						$data['issue']=$row['typeOfIssue'];
						$data['location']=$row['location'];
						$data['plant']= $row['plantName'];
						$data['department']=$row['subDivision'];
						$data['functionallocation']=$row['functionallocation_name'];
						$funLoc = $this->subfunctionalLocations($row['functionlocation_id']);
											if($funLoc['status'] == 1){
												$data['functionlocation_id']=$funLoc['id'];
												$data['functionallocation_name']=$funLoc['name'];
												$data['subfunctionlocation_id']=$row['functionlocation_id'];
												$data['subfunctionallocation_name']=$row['functionallocation_name'];
											}else{
												$data['functionlocation_id']=$row['functionlocation_id'];
												$data['functionallocation_name']=$row['functionallocation_name'];
												$data['subfunctionlocation_id']=0;
												$data['subfunctionallocation_name']='';
											}
						$data['equipment']= $row['equipment'];
						$data['submittedby']=$row['submittedByName'];
						$data['submittedon']=date($configDate, strtotime( $row['createdOn'] )).' '.date('H:i:s', strtotime( $row['createdOn'] ));
						$data['status']= $row['status'];

						}				
						

						if ($row['statusId'] == 3) {
							$ticket_id = $row['ticketId'];

							$q1 = "SELECT s.id AS id,s.name AS name from erp_ticket_status s LEFT JOIN erp_ticket_acknowledgement_action_log l ON l.status_id = s.id WHERE l.id = (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE ticket_id = $ticket_id GROUP BY ticket_id)"; 
							$res1=mysqli_query($this->conn, $q1);
							if(mysqli_num_rows($res1)>0)
							{
							   $row1 = mysqli_fetch_array($res1);
							   $id=$row1['id'];
							   $name=$row1['name'];
						    }

						    if($id == 4){
						    	$q2 = "SELECT e.emp_number AS emp_number, concat(e.emp_firstname,' ',e.emp_middle_name,' ',e.emp_lastname) AS emp_name from hs_hr_employee e LEFT JOIN erp_ticket_acknowledgement_action_log l ON l.accepted_by = e.emp_number WHERE l.id = (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE ticket_id = $ticket_id GROUP BY ticket_id)"; 
							$res2=mysqli_query($this->conn, $q2);
							
							if(mysqli_num_rows($res2)>0)
							{
							   $row2 = mysqli_fetch_array($res2);
							   $emp_number=$row2['emp_number'];
							   $emp_name=$row2['emp_name'];
						    }

								$data['status']= $row['status'].'('.$name.' by '.$emp_name.')';
						    }else{

						    	$q2 = "SELECT e.emp_number AS emp_number, concat(e.emp_firstname,' ',e.emp_middle_name,' ',e.emp_lastname) AS emp_name from hs_hr_employee e LEFT JOIN erp_ticket_acknowledgement_action_log l ON l.forward_to = e.emp_number WHERE l.id = (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE ticket_id = $ticket_id GROUP BY ticket_id)"; 
							$res2=mysqli_query($this->conn, $q2);
							
							if(mysqli_num_rows($res2)>0)
							{
							   $row2 = mysqli_fetch_array($res2);
							   $emp_number=$row2['emp_number'];
							   $emp_name=$row2['emp_name'];
						    }

						    	$data['status']= $row['status'].'('.$name.' to '.$emp_name.')';
						    }
						}
						
						$data1[] = $data;
					}
						$data['ticketDetails']=$data1;
						$data['status']=1;
							
				}else{
				$data['status']=0;
			}


		 }
		 else if($userRoleId == 11)
		 {

		 		$query = "SELECT t.job_id AS job_id, t.id AS ticketId, t.subject AS subject, t.submitted_on AS submittedon,t.is_PreventiveMaintenance AS preventiveMaintenance,ta.machine_status AS machineStatus, fl.name as functionallocation, fl.id as functionalLocationId,toi.name AS issue, toi.id AS typeOfIssueId, toi.sla AS sla,loc.name AS location, loc.id AS locationId, plnt.plant_name AS plant, plnt.id AS plantId, eq.name AS equipment, eq.id AS equipmentId, ts.name AS status, ts.id AS statusId, ta.ticket_id AS ticketId, t.submitted_by_name AS submittedby, e.emp_number AS engineerId, e.emp_number AS technicianId,tp.name AS priority, tp.id AS priorityId, tsev.name AS severity, tsev.id AS severityId, u.id AS uaerId, cs.name AS department, cs.id AS subDivisionId FROM erp_ticket t LEFT JOIN erp_functional_location fl ON fl.id = t.functional_location_id LEFT JOIN erp_type_of_issue toi ON toi.id = t.type_of_issue_id LEFT JOIN erp_location loc ON loc.id = t.location_id LEFT JOIN erp_plant plnt ON plnt.id = t.plant_id LEFT JOIN erp_equipment eq ON eq.id = t.equipment_id LEFT JOIN erp_ticket_status ts ON ts.id = t.status_id LEFT JOIN erp_ticket_acknowledgement_action_log ta ON ta.ticket_id = t.id LEFT JOIN hs_hr_employee e ON e.emp_number = ta.submitted_by_emp_number LEFT JOIN erp_user u ON u.id = ta.created_by_user_id LEFT JOIN erp_ticket_priority tp ON tp.id = t.priority_id LEFT JOIN erp_ticket_severity tsev ON tsev.id = t.severity_id LEFT JOIN erp_subunit cs ON cs.id = t.user_department_id WHERE ta.submitted_by_emp_number = $empNumber AND t.id NOT IN (select id from erp_ticket where status_id = 11 and submitted_by_emp_number != $empNumber) GROUP BY t.id
			ORDER BY `t`.`job_id`  DESC";


			$configDate = $this->dateFormat();

			$count=mysqli_query($this->conn, $query);

				if(mysqli_num_rows($count) > 0)
				{
					// $row=mysqli_fetch_all($count,MYSQLI_ASSOC);
					while($row = mysqli_fetch_assoc($count)) { 	

						$i=$i+1;
						
						if($row['preventiveMaintenance'] == 1){
							$data['sno']=$i;
							$data['ticketId']=$row['ticketId'];
						$data['job_id']=$row['job_id'];
						$data['subject']= 'PM - '.$row['equipment'];
						$data['issue']=$row['issue'];
						$data['location']=$row['location'];
						$data['plant']= $row['plant'];
						$data['department']=$row['department'];
						$data['functionallocation']=$row['functionallocation'];
						$data['equipment']= $row['equipment'];
						$data['submittedby']=$row['submittedby'];
						$data['submittedon']=date($configDate, strtotime( $row['submittedon'] )).' '.date('H:i:s', strtotime( $row['submittedon'] ));
						$data['status']= $row['status'];
	
						}else{
							$data['sno']=$i;
							$data['ticketId']=$row['ticketId'];
						$data['job_id']=$row['job_id'];
						$text = $row['subject'];
						$data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);
						//$data['subject']= iconv('UTF-8', 'ASCII//IGNORE', utf8_encode($text));
						$data['issue']=$row['issue'];
						$data['location']=$row['location'];
						$data['plant']= $row['plant'];
						$data['department']=$row['department'];
						$data['functionallocation']=$row['functionallocation'];
						$data['equipment']= $row['equipment'];
						$data['submittedby']=$row['submittedby'];
						$data['submittedon']=date($configDate, strtotime( $row['submittedon'] )).' '.date('H:i:s', strtotime( $row['submittedon'] ));
						$data['status']= $row['status'];

						}				
						

						if ($row['statusId'] == 3) {
							$ticket_id = $row['ticketId'];

							$q1 = "SELECT s.id AS id,s.name AS name from erp_ticket_status s LEFT JOIN erp_ticket_acknowledgement_action_log l ON l.status_id = s.id WHERE l.id = (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE ticket_id = $ticket_id GROUP BY ticket_id)"; 
							$res1=mysqli_query($this->conn, $q1);
							if(mysqli_num_rows($res1)>0)
							{
							   $row1 = mysqli_fetch_array($res1);
							   $id=$row1['id'];
							   $name=$row1['name'];
						    }

						    if($id == 4){
						    	$q2 = "SELECT e.emp_number AS emp_number, concat(e.emp_firstname,' ',e.emp_middle_name,' ',e.emp_lastname) AS emp_name from hs_hr_employee e LEFT JOIN erp_ticket_acknowledgement_action_log l ON l.accepted_by = e.emp_number WHERE l.id = (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE ticket_id = $ticket_id GROUP BY ticket_id)"; 
							$res2=mysqli_query($this->conn, $q2);
							
							if(mysqli_num_rows($res2)>0)
							{
							   $row2 = mysqli_fetch_array($res2);
							   $emp_number=$row2['emp_number'];
							   $emp_name=$row2['emp_name'];
						    }

								$data['status']= $row['status'].'('.$name.' by '.$emp_name.')';
						    }else{

						    	$q2 = "SELECT e.emp_number AS emp_number, concat(e.emp_firstname,' ',e.emp_middle_name,' ',e.emp_lastname) AS emp_name from hs_hr_employee e LEFT JOIN erp_ticket_acknowledgement_action_log l ON l.forward_to = e.emp_number WHERE l.id = (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE ticket_id = $ticket_id GROUP BY ticket_id)"; 
							$res2=mysqli_query($this->conn, $q2);
							
							if(mysqli_num_rows($res2)>0)
							{
							   $row2 = mysqli_fetch_array($res2);
							   $emp_number=$row2['emp_number'];
							   $emp_name=$row2['emp_name'];
						    }

						    	$data['status']= $row['status'].'('.$name.' to '.$emp_name.')';
						    }
						}
						
						$data1[] = $data;
					}
						$data['ticketDetails']=$data1;
						$data['status']=1;
							
				}else{
				$data['status']=0;
			}

		 }

		 else 

		 {

		 		$query = "SELECT t.job_id AS job_id, t.id AS ticketId, t.subject AS subject, t.submitted_on AS submittedon,t.is_PreventiveMaintenance AS preventiveMaintenance,ta.machine_status AS machineStatus, fl.name as functionallocation, fl.id as functionalLocationId,toi.name AS issue, toi.id AS typeOfIssueId, toi.sla AS sla,loc.name AS location, loc.id AS locationId, plnt.plant_name AS plant, plnt.id AS plantId, eq.name AS equipment, eq.id AS equipmentId, ts.name AS status, ts.id AS statusId, ta.ticket_id AS ticketId, t.submitted_by_name AS submittedby, e.emp_number AS engineerId, e.emp_number AS technicianId,tp.name AS priority, tp.id AS priorityId, tsev.name AS severity, tsev.id AS severityId, u.id AS uaerId, cs.name AS department, cs.id AS subDivisionId FROM erp_ticket t LEFT JOIN erp_functional_location fl ON fl.id = t.functional_location_id LEFT JOIN erp_type_of_issue toi ON toi.id = t.type_of_issue_id LEFT JOIN erp_location loc ON loc.id = t.location_id LEFT JOIN erp_plant plnt ON plnt.id = t.plant_id LEFT JOIN erp_equipment eq ON eq.id = t.equipment_id LEFT JOIN erp_ticket_status ts ON ts.id = t.status_id LEFT JOIN erp_ticket_acknowledgement_action_log ta ON ta.ticket_id = t.id LEFT JOIN hs_hr_employee e ON e.emp_number = ta.submitted_by_emp_number LEFT JOIN erp_user u ON u.id = ta.created_by_user_id LEFT JOIN erp_ticket_priority tp ON tp.id = t.priority_id LEFT JOIN erp_ticket_severity tsev ON tsev.id = t.severity_id LEFT JOIN erp_subunit cs ON cs.id = t.user_department_id WHERE ta.submitted_by_emp_number = $empNumber AND t.id NOT IN (select id from erp_ticket where status_id = 11 and submitted_by_emp_number != $empNumber) GROUP BY t.id
			ORDER BY `t`.`job_id`  DESC";


			$configDate = $this->dateFormat();

			$count=mysqli_query($this->conn, $query);

				if(mysqli_num_rows($count) > 0)
				{
					// $row=mysqli_fetch_all($count,MYSQLI_ASSOC);
					while($row = mysqli_fetch_assoc($count)) { 	

						$i=$i+1;
						
						if($row['preventiveMaintenance'] == 1){
							$data['sno']=$i;
							$data['ticketId']=$row['ticketId'];
						$data['job_id']=$row['job_id'];
						$data['subject']= 'PM - '.$row['equipment'];
						$data['issue']=$row['issue'];
						$data['location']=$row['location'];
						$data['plant']= $row['plant'];
						$data['department']=$row['department'];
						$data['functionallocation']=$row['functionallocation'];
						$data['equipment']= $row['equipment'];
						$data['submittedby']=$row['submittedby'];
						$data['submittedon']=date($configDate, strtotime( $row['submittedon'] )).' '.date('H:i:s', strtotime( $row['submittedon'] ));
						$data['status']= $row['status'];
	
						}else{
							$data['sno']=$i;
							$data['ticketId']=$row['ticketId'];
						$data['job_id']=$row['job_id'];
						$text = $row['subject'];
						$data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);
						//$data['subject']= iconv('UTF-8', 'ASCII//IGNORE', utf8_encode($text));
						$data['issue']=$row['issue'];
						$data['location']=$row['location'];
						$data['plant']= $row['plant'];
						$data['department']=$row['department'];
						$data['functionallocation']=$row['functionallocation'];
						$data['equipment']= $row['equipment'];
						$data['submittedby']=$row['submittedby'];
						$data['submittedon']=date($configDate, strtotime( $row['submittedon'] )).' '.date('H:i:s', strtotime( $row['submittedon'] ));
						$data['status']= $row['status'];

						}				
						

						if ($row['statusId'] == 3) {
							$ticket_id = $row['ticketId'];

							$q1 = "SELECT s.id AS id,s.name AS name from erp_ticket_status s LEFT JOIN erp_ticket_acknowledgement_action_log l ON l.status_id = s.id WHERE l.id = (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE ticket_id = $ticket_id GROUP BY ticket_id)"; 
							$res1=mysqli_query($this->conn, $q1);
							if(mysqli_num_rows($res1)>0)
							{
							   $row1 = mysqli_fetch_array($res1);
							   $id=$row1['id'];
							   $name=$row1['name'];
						    }

						    if($id == 4){
						    	$q2 = "SELECT e.emp_number AS emp_number, concat(e.emp_firstname,' ',e.emp_middle_name,' ',e.emp_lastname) AS emp_name from hs_hr_employee e LEFT JOIN erp_ticket_acknowledgement_action_log l ON l.accepted_by = e.emp_number WHERE l.id = (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE ticket_id = $ticket_id GROUP BY ticket_id)"; 
							$res2=mysqli_query($this->conn, $q2);
							
							if(mysqli_num_rows($res2)>0)
							{
							   $row2 = mysqli_fetch_array($res2);
							   $emp_number=$row2['emp_number'];
							   $emp_name=$row2['emp_name'];
						    }

								$data['status']= $row['status'].'('.$name.' by '.$emp_name.')';
						    }else{

						    	$q2 = "SELECT e.emp_number AS emp_number, concat(e.emp_firstname,' ',e.emp_middle_name,' ',e.emp_lastname) AS emp_name from hs_hr_employee e LEFT JOIN erp_ticket_acknowledgement_action_log l ON l.forward_to = e.emp_number WHERE l.id = (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE ticket_id = $ticket_id GROUP BY ticket_id)"; 
							$res2=mysqli_query($this->conn, $q2);
							
							if(mysqli_num_rows($res2)>0)
							{
							   $row2 = mysqli_fetch_array($res2);
							   $emp_number=$row2['emp_number'];
							   $emp_name=$row2['emp_name'];
						    }

						    	$data['status']= $row['status'].'('.$name.' to '.$emp_name.')';
						    }
						}
						
						$data1[] = $data;
					}
						$data['ticketDetails']=$data1;
						$data['status']=1;
							
				}else{
				$data['status']=0;
			}

		 }


		
		return $data;    
	}



    ///ticketDetails
    function ticketDetByTypeOfIssue($userIdPass)
	{
		$data= array();
		$userDetails = $this->getUserRoleByUserId(6);
		$empNumber = $userDetails['empNumber'];
		$empresult=$this->employeeDetails($empNumber);
		$department = $empresult['work_station'];
		$name = '';
		$emp_name = '';
		$i=0;

		

			/*$query = "SELECT t.job_id AS job_id, t.id AS ticketId, t.subject AS subject, t.submitted_on AS submittedon,t.is_PreventiveMaintenance AS preventiveMaintenance,ta.machine_status AS machineStatus, fl.name as functionallocation, fl.id as functionalLocationId,toi.name AS issue, toi.id AS typeOfIssueId, toi.sla AS sla,loc.name AS location, loc.id AS locationId, plnt.plant_name AS plant, plnt.id AS plantId, eq.name AS equipment, eq.id AS equipmentId, ts.name AS status, ts.id AS statusId, ta.ticket_id AS ticketId, t.submitted_by_name AS submittedby, e.emp_number AS engineerId, e.emp_number AS technicianId,tp.name AS priority, tp.id AS priorityId, tsev.name AS severity, tsev.id AS severityId, u.id AS uaerId, cs.name AS department, cs.id AS subDivisionId FROM erp_ticket t LEFT JOIN erp_functional_location fl ON fl.id = t.functional_location_id LEFT JOIN erp_type_of_issue toi ON toi.id = t.type_of_issue_id LEFT JOIN erp_location loc ON loc.id = t.location_id LEFT JOIN erp_plant plnt ON plnt.id = t.plant_id LEFT JOIN erp_equipment eq ON eq.id = t.equipment_id LEFT JOIN erp_ticket_status ts ON ts.id = t.status_id LEFT JOIN erp_ticket_acknowledgement_action_log ta ON ta.ticket_id = t.id LEFT JOIN hs_hr_employee e ON e.emp_number = ta.submitted_by_emp_number LEFT JOIN erp_user u ON u.id = ta.created_by_user_id LEFT JOIN erp_ticket_priority tp ON tp.id = t.priority_id LEFT JOIN erp_ticket_severity tsev ON tsev.id = t.severity_id LEFT JOIN erp_subunit cs ON cs.id = t.user_department_id WHERE ta.submitted_by_emp_number = $empNumber AND t.id NOT IN (select id from erp_ticket where status_id = 11 and submitted_by_emp_number != $empNumber) GROUP BY t.id
			ORDER BY `t`.`job_id`  DESC";*/

			$query = "SELECT t.job_id AS job_id, t.functional_location_id AS funLocId, t.id AS ticketId, t.subject AS subject, t.submitted_on AS calFromDate, t.submitted_on AS calToDate, t.submitted_on AS createdOn, ta.machine_status AS machineStatus, fl.name as functionallocation_name, fl.id as functionlocation_id,toi.name AS issue, toi.id AS typeOfIssueId, toi.sla AS sla,loc.name AS location, loc.id AS locationId, plnt.plant_name AS plant, plnt.id AS plantId, eq.name AS equipment, eq.id AS equipmentId, ts.name AS status, ts.id AS statusId, ta.ticket_id AS ticketId, t.submitted_by_name AS submittedby, e.emp_number AS engineerId, e.emp_number AS technicianId,tp.name AS priority, tp.id AS priorityId, tsev.name AS severity, tsev.id AS severityId, u.id AS uaerId, msr.id AS scheduleId, msr.maintenance_type_id AS maintenanceType,mt.id AS maintenanceId, mt.name AS maintenanceName,cs.name AS department, cs.id AS subDivisionId,t.is_PreventiveMaintenance AS preventiveMaintenance,t.submitted_on AS submittedon FROM erp_ticket t LEFT JOIN erp_functional_location fl ON fl.id = t.functional_location_id LEFT JOIN erp_type_of_issue toi ON toi.id = t.type_of_issue_id LEFT JOIN erp_location loc ON loc.id = t.location_id LEFT JOIN erp_plant plnt ON plnt.id = t.plant_id LEFT JOIN erp_equipment eq ON eq.id = t.equipment_id LEFT JOIN erp_ticket_status ts ON ts.id = t.status_id LEFT JOIN erp_ticket_acknowledgement_action_log ta ON ta.ticket_id = t.id LEFT JOIN hs_hr_employee e ON e.emp_number = ta.submitted_by_emp_number LEFT JOIN erp_user u ON u.id = ta.created_by_user_id LEFT JOIN erp_ticket_priority tp ON tp.id = t.priority_id LEFT JOIN erp_ticket_severity tsev ON tsev.id = t.severity_id LEFT JOIN erp_subunit cs ON cs.id = t.user_department_id LEFT JOIN erp_maintenance_schedule msr ON msr.ticket_id = t.id LEFT JOIN erp_maintenance_type mt ON mt.id = msr.maintenance_type_id WHERE t.location_id = 3 AND t.status_id NOT IN (11)
				GROUP BY t.job_id ORDER BY `t`.`job_id` DESC";

		$configDate = $this->dateFormat();

			$count=mysqli_query($this->conn, $query);

			$jobsCountNew = mysqli_num_rows($count);

			/*echo $jobsCountNew;
			exit();*/

				if(mysqli_num_rows($count) > 0)
				{
					// $row=mysqli_fetch_all($count,MYSQLI_ASSOC);
					while($row = mysqli_fetch_assoc($count)) { 	

						$i=$i+1;
						
						if($row['preventiveMaintenance'] == 1){
							$data['sno']=$i;
							$data['ticketId']=$row['ticketId'];
						$data['job_id']=$row['job_id'];
						$data['subject']= 'PM - '.$row['equipment'];
						$data['issue']=$row['issue'];
						$data['location']=$row['location'];
						$data['plant']= $row['plant'];
						$data['department']=$row['department'];
						$data['functionallocation']=$row['functionallocation_name'];
						$funLoc = $this->subfunctionalLocations($row['functionlocation_id']);
											if($funLoc['status'] == 1){
												$data['functionlocation_id']=$funLoc['id'];
												$data['functionallocation_name']=$funLoc['name'];
												$data['subfunctionlocation_id']=$row['functionlocation_id'];
												$data['subfunctionallocation_name']=$row['functionallocation_name'];
											}else{
												$data['functionlocation_id']=$row['functionlocation_id'];
												$data['functionallocation_name']=$row['functionallocation_name'];
												$data['subfunctionlocation_id']=0;
												$data['subfunctionallocation_name']='';
											}
						$data['equipment']= $row['equipment'];
						$data['submittedby']=$row['submittedby'];
						$data['submittedon']=date($configDate, strtotime( $row['submittedon'] )).' '.date('H:i:s', strtotime( $row['submittedon'] ));
						$data['status']= $row['status'];
	
						}else{
							$data['sno']=$i;
							$data['ticketId']=$row['ticketId'];
						$data['job_id']=$row['job_id'];
						$text = $row['subject'];
						$data['subject']=iconv(mb_detect_encoding($text), "UTF-8//IGNORE", $text);
						//$data['subject']= iconv('UTF-8', 'ASCII//IGNORE', utf8_encode($text));
						$data['issue']=$row['issue'];
						$data['location']=$row['location'];
						$data['plant']= $row['plant'];
						$data['department']=$row['department'];
						$data['functionallocation']=$row['functionallocation_name'];
						$data['equipment']= $row['equipment'];
						$data['submittedby']=$row['submittedby'];
						$data['submittedon']=date($configDate, strtotime( $row['submittedon'] )).' '.date('H:i:s', strtotime( $row['submittedon'] ));
						$data['status']= $row['status'];

						}				
						

						if ($row['statusId'] == 3) {
							$ticket_id = $row['ticketId'];

							$q1 = "SELECT s.id AS id,s.name AS name from erp_ticket_status s LEFT JOIN erp_ticket_acknowledgement_action_log l ON l.status_id = s.id WHERE l.id = (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE ticket_id = $ticket_id GROUP BY ticket_id)"; 
							$res1=mysqli_query($this->conn, $q1);
							if(mysqli_num_rows($res1)>0)
							{
							   $row1 = mysqli_fetch_array($res1);
							   $id=$row1['id'];
							   $name=$row1['name'];
						    }

						    if($id == 4){
						    	$q2 = "SELECT e.emp_number AS emp_number, concat(e.emp_firstname,' ',e.emp_middle_name,' ',e.emp_lastname) AS emp_name from hs_hr_employee e LEFT JOIN erp_ticket_acknowledgement_action_log l ON l.accepted_by = e.emp_number WHERE l.id = (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE ticket_id = $ticket_id GROUP BY ticket_id)"; 
							$res2=mysqli_query($this->conn, $q2);
							
							if(mysqli_num_rows($res2)>0)
							{
							   $row2 = mysqli_fetch_array($res2);
							   $emp_number=$row2['emp_number'];
							   $emp_name=$row2['emp_name'];
						    }

								$data['status']= $row['status'].'('.$name.' by '.$emp_name.')';
						    }else{

						    	$q2 = "SELECT e.emp_number AS emp_number, concat(e.emp_firstname,' ',e.emp_middle_name,' ',e.emp_lastname) AS emp_name from hs_hr_employee e LEFT JOIN erp_ticket_acknowledgement_action_log l ON l.forward_to = e.emp_number WHERE l.id = (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log WHERE ticket_id = $ticket_id GROUP BY ticket_id)"; 
							$res2=mysqli_query($this->conn, $q2);
							
							if(mysqli_num_rows($res2)>0)
							{
							   $row2 = mysqli_fetch_array($res2);
							   $emp_number=$row2['emp_number'];
							   $emp_name=$row2['emp_name'];
						    }

						    	$data['status']= $row['status'].'('.$name.' to '.$emp_name.')';
						    }
						}
						
						$data1[] = $data;
					}
						$data['ticketDetByTypeOfIssue']=$data1;
						$data['status']=1;
							
				}else{
				$data['status']=0;
			}
		return $data;    
	}

	function ticketsDownTimeReport($userIdPass)
	{
		$data= array();
		
		
		$i = 0;
			$query1 = "SELECT t.job_id as jobId,t.id as ticket_id,t.subject,loc.name as location,p.plant_name as plant,sub.name as department,flc.name as functionallocation_name,flc.id as functionlocation_id,eqp.name as equipmment,t.submitted_on as createdDate FROM erp_ticket t
				LEFT JOIN erp_subunit sub ON sub.id = t.user_department_id
				LEFT JOIN erp_location loc ON loc.id = t.location_id
				LEFT JOIN erp_functional_location flc ON flc.id = t.functional_location_id
				LEFT JOIN erp_plant p ON p.id = t.plant_id
				LEFT JOIN erp_equipment eqp ON eqp.id = t.equipment_id 
				WHERE t.status_id = 5
				ORDER BY t.job_id DESC";

				/*echo $query1;
				exit();*/

		$configDate = $this->dateFormat();

			$count1=mysqli_query($this->conn, $query1);

			$jobsCountNew = mysqli_num_rows($count1);

			/*echo $jobsCountNew;
			exit();*/

			//$Count = mysqli_num_rows($count);

		//echo $Count;



		if(mysqli_num_rows($count1) > 0)
		{

			//echo "if";
						$row=mysqli_fetch_assoc($count1);

						
					do{ 		

							//echo "do";

							$i = $i+1;

							//echo $i;

							$ticket_id = $row['ticket_id'];

							//echo $ticket_id;
							//exit();

							$data['sno']=$i;	
							$data['jobId']=$row['jobId'];
							$jobId = $row['jobId'];
																
							$data['subject']= $row['subject'];	
							$data['location']= $row['location'];	
							$data['plant']= $row['plant'];	
							$data['department']= $row['department'];
							$data['functionalLocation']= $row['functionallocation_name'];
							$funLoc = $this->subfunctionalLocations($row['functionlocation_id']);

							if($funLoc['status'] == 1){

											$data['functionlocation_id']=$funLoc['id'];
																	
											$data['functionallocation_name']=$funLoc['name'];
											$data['subfunctionlocation_id']=$row['functionlocation_id'];
											$data['subfunctionallocation_name']=$row['functionallocation_name'];
									}else{
											$data['functionlocation_id']=$row['functionlocation_id'];
											$data['functionallocation_name']=$row['functionallocation_name'];
											$data['subfunctionlocation_id']=0;
											$data['subfunctionallocation_name']='';
										 }		 
										$data['equipmment']= $row['equipmment'];

							$query10 = "SELECT modified_on FROM erp_ticket WHERE id = $ticket_id";

							$count10 = mysqli_query($this->conn, $query10);

							if(mysqli_num_rows($count10) > 0)
							{

								//echo "count3 if";
								$row = mysqli_fetch_assoc($count10);

																		
								$modifiedOn = $row['modified_on']; 
								$subonnew = $modifiedOn;
													

								if($modifiedOn)
								{

														
									$query4 = "SELECT submitted_on as subonverclsd FROM `erp_ticket_acknowledgement_action_log` WHERE ticket_id = $ticket_id AND status_id = 5";

									$count4 = mysqli_query($this->conn, $query4);

									if(mysqli_num_rows($count4) > 0)
									{

										//echo "count4 if";
										$row = mysqli_fetch_assoc($count4);

																			
										$subonverclsd = $row['subonverclsd']; 
										//echo 'subonverclsd'.$subonverclsd;
										//exit();	
																				
									 }



								 }
								 else
								 {

										$configDate = $this->dateFormat();
	
										$query3 = "SELECT submitted_on as subonnew FROM `erp_ticket_acknowledgement_action_log`
									 			WHERE ticket_id = $ticket_id AND status_id = 1";

												 $count3 = mysqli_query($this->conn, $query3);

												  if(mysqli_num_rows($count3) > 0)
												{

													//echo "count3 if";
													$row = mysqli_fetch_assoc($count3);
																	
													$subonnew = $row['subonnew']; 
													//echo 'subonnew'.$subonnew;	
																		
												}


										$query4 = "SELECT submitted_on as subonverclsd FROM `erp_ticket_acknowledgement_action_log` WHERE ticket_id = $ticket_id AND status_id = 5";

												$count4 = mysqli_query($this->conn, $query4);

													if(mysqli_num_rows($count4) > 0)
													{

														//echo "count4 if";
														$row = mysqli_fetch_assoc($count4);

																			
														$subonverclsd = $row['subonverclsd']; 
														//echo 'subonverclsd'.$subonverclsd;
														//exit();	
																				
													}


									}						

															
							}


								$dteStart = new DateTime($subonnew); 

											
								 $dteEnd   = new DateTime($subonverclsd);

								   			

								  $dteDiff  = $dteEnd->diff($dteStart); 


								   			
								  $resolvedDuration = $dteDiff->format("%H:%I"); 
								   			
								  $interval = $dteStart->diff($dteEnd);
											
								$dys = $interval->format('%a');
											
								$hrs = $interval->format('%h');
								            
								$mins = $interval->format('%i');

								$secs = $interval->format('%s');

								                //echo 'mins'.$mins;

								$hrs += $dys * 24;

								$DownTime = $hrs.':'.$mins.':'.$secs;

								$data['createdDate']= $subonnew;

							    $data['ClosedDate']= $subonverclsd;

								$data['DownTime']= $DownTime;

	
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count1));
						$data['ticketsDownTimeReport']=$data1;
						$data['status']=1;
							
		}else{

			//echo "else";
				$data['status']=0;
			}
		
		//echo $data;		
		return $data;    
	}

	function machineDownTimeReport($user_id)
		{

				//echo "machinebreakdown";

				$data = array();

				$i = 0;

			$query = "SELECT t.job_id as jobId,t.id as ticket_id,t.subject as subject,loc.name as location,p.plant_name as plant,sub.name as department,flc.name as functionalLocation,eqp.name as equipmment,t.submitted_on as createdDate FROM erp_ticket t LEFT JOIN erp_subunit sub ON sub.id = t.user_department_id LEFT JOIN erp_location loc ON loc.id = t.location_id LEFT JOIN erp_functional_location flc ON flc.id = t.functional_location_id LEFT JOIN erp_plant p ON p.id = t.plant_id LEFT JOIN erp_equipment eqp ON eqp.id = t.equipment_id WHERE t.status_id = 5 ORDER BY t.job_id DESC LIMIT 278";


				$count = mysqli_query($this->conn, $query);

				//$jobsCount = mysqli_num_rows($count);
				//echo $jobsCount;

		if(mysqli_num_rows($count) > 0)
		{
			// echo 'if';
						$row1 = mysqli_fetch_assoc($count);

					do{ 		
						
							// echo 'do';
						/*$i = $i+1;

						$data['sno']=$i;*/


						$data['jobId']= $row1['jobId'];
						
						$data['subject']=$row1['subject'];

						$data['location']= $row1['location'];

						$data['ticket_id']= $row1['ticket_id'];

						$ticket_id = $row1['ticket_id'];


						$query3 = "SELECT submitted_on as subonnew FROM `erp_ticket_acknowledgement_action_log` WHERE ticket_id = $ticket_id AND status_id = 1";

						       $count3 = mysqli_query($this->conn, $query3);

						       if(mysqli_num_rows($count3) > 0)
								{

										//echo "count3 if";
												$row = mysqli_fetch_assoc($count3);

											
													$subonnew = $row['subonnew']; 
													//echo 'subonnew'.$subonnew;	
												
								}



									 $query4 = "SELECT submitted_on as subonEndcom FROM `erp_ticket_acknowledgement_action_log` WHERE ticket_id = $ticket_id AND status_id = 5";

									 $count4 = mysqli_query($this->conn, $query4);

									  if(mysqli_num_rows($count4) > 0)
								{

									//echo "count4 if";
												$row = mysqli_fetch_assoc($count4);

											
													$subonEndcom = $row['subonEndcom']; 
													//echo 'subonEndcom'.$subonEndcom;

												
								}else{

										//echo "count4 else";
										$subonEndcom =  ' ';
										//echo $subonEndcom;
									}

						
								   		
								   			$dteStart   = new DateTime($subonnew);

								   			$dteComEnd   = new DateTime($subonEndcom);

								                 $dteDiff2  = $dteComEnd->diff($dteStart); 

												$resolvedDuration1 = $dteDiff2->format("%H:%I"); 
								   			
								   			$interval = $dteStart->diff($dteComEnd);
											
											$dys = $interval->format('%a');
											
								                                    $hrs = $interval->format('%h');
								            
								                                    $mins = $interval->format('%i');

								                //echo 'mins'.$mins;

								                 $hrs += $dys * 24;

								                 $DownTime = $hrs.':'.$mins;	

								                 
							$data['CreatedDate'] = $subonnew;


						$data['ClosedDate'] = $subonEndcom;
						
						$data['DownTime']= $DownTime;	
								
						
						$data1[] = $data;

						

					}while($row1 = mysqli_fetch_assoc($count));

						$data['machineDownTimeReport']=$data1;
					// print_r($data['machineDownTimeReport']);
						$data['status']=1;
							
		}else{

			//echo 'else';
				$data['status']=0;
			}


					// print_r( $data);		
		return $data;   

        }


function jobsByStatus($user_id)
    {
        $data=array();

        $i=0;
        $query1 = "SELECT t.job_id as jobId, t.submitted_on as subOn, loc.name as location, t.subject as subject,p.plant_name as plant,s.name as department,fl.name as functionallocation_name,eqp.name as equipment,ts.name as status,fl.id AS functionlocation_id FROM erp_ticket t LEFT JOIN erp_location loc ON loc.id = t.location_id LEFT JOIN erp_plant p ON p.id = t.plant_id LEFT JOIN erp_subunit s ON s.id = t.user_department_id LEFT JOIN erp_functional_location fl ON fl.id = t.functional_location_id LEFT JOIN erp_equipment eqp ON eqp.id = t.equipment_id LEFT JOIN erp_ticket_status ts ON ts.id = t.status_id WHERE t.status_id NOT IN (11) ORDER BY t.submitted_on DESC LIMIT 278";


						$configDate = $this->dateFormat();
		
		$count1 = mysqli_query($this->conn, $query1);

		$Count = mysqli_num_rows($count1);

		//echo $Count;

		if(mysqli_num_rows($count1) > 0)
		{
						$row=mysqli_fetch_assoc($count1);

					do{ 		
						$i = $i+1;

						$data['sno']=$i;	
						$data['jobId']= $row['jobId'];
						$data['subject']= $row['subject'];
						
						$data['plant']=$row['plant'];	
						$data['department']= $row['department'];	
						$data['functionalLocation']= $row['functionallocation_name'];
						$funLoc = $this->subfunctionalLocations($row['functionlocation_id']);
											if($funLoc['status'] == 1){
												$data['functionlocation_id']=$funLoc['id'];
												$data['functionallocation_name']=$funLoc['name'];
												$data['subfunctionlocation_id']=$row['functionlocation_id'];
												$data['subfunctionallocation_name']=$row['functionallocation_name'];
											}else{
												$data['functionlocation_id']=$row['functionlocation_id'];
												$data['functionallocation_name']=$row['functionallocation_name'];
												$data['subfunctionlocation_id']=0;
												$data['subfunctionallocation_name']='';
											}
						$data['equipment']= $row['equipment'];	
						$data['status']= $row['status'];
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count1));
						$data['jobsByTicketStatus']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}

        			
        return $data;
    }



	//Sub Functional Location

	function subfunctionalLocations($id){
		$data= array();
		$query="SELECT * FROM erp_functional_location where id = $id";

		/*echo $query;
		exit();*/
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			$parent_id = $row['parent_id'];
		}	

		$query="SELECT * FROM erp_functional_location where id = $parent_id";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row1=mysqli_fetch_assoc($count);
			$row1['status']=1;
		}else{
			$row1['status']=0;
		}

		return $row1; 
	}

    function jobsHandledByEngineer($user_id)
    {
        $data=array();

        $j=0;


        $empresult=$this->engLists();

				for ($i=0; $i < sizeof($empresult['englist']) ; $i++) { 
	        	$engList[] = $empresult['englist'][$i];
	        	//to convert Array into string the following implode method is used
	        	$engLists = implode(',', $engList);
	        }

        $query1 = "SELECT count(*) AS equip_count, t.job_id AS jobId, t.id AS id, t.subject AS subject, t.submitted_on AS calFromDate, t.submitted_on AS calToDate, t.submitted_on AS createdOn, ta.machine_status AS machineStatus, fl.name as functionallocation_name, fl.id as functionlocation_id,toi.name AS typeOfIssue, toi.id AS typeOfIssueId, toi.sla AS sla,loc.name AS location, loc.id AS locationId, plnt.plant_name AS plantName, plnt.id AS plantId, eq.name AS equipment, eq.id AS equipmentId, ts.name AS status, ts.id AS statusId, ta.ticket_id AS ticketId, t.submitted_by_name AS submittedByName, e.emp_number AS engineerId, e.emp_number AS technicianId,tp.name AS priority, tp.id AS priorityId, tsev.name AS severity, tsev.id AS severityId, u.id AS uaerId, msr.id AS scheduleId, msr.maintenance_type_id AS maintenanceType,mt.id AS maintenanceId, mt.name AS maintenanceName,cs.name AS department, cs.id AS subDivisionId FROM erp_ticket t LEFT JOIN erp_functional_location fl ON fl.id = t.functional_location_id LEFT JOIN erp_type_of_issue toi ON toi.id = t.type_of_issue_id LEFT JOIN erp_location loc ON loc.id = t.location_id LEFT JOIN erp_plant plnt ON plnt.id = t.plant_id LEFT JOIN erp_equipment eq ON eq.id = t.equipment_id LEFT JOIN erp_ticket_status ts ON ts.id = t.status_id LEFT JOIN erp_ticket_acknowledgement_action_log ta ON ta.ticket_id = t.id LEFT JOIN hs_hr_employee e ON e.emp_number = ta.submitted_by_emp_number LEFT JOIN erp_user u ON u.id = ta.created_by_user_id LEFT JOIN erp_ticket_priority tp ON tp.id = t.priority_id LEFT JOIN erp_ticket_severity tsev ON tsev.id = t.severity_id LEFT JOIN erp_subunit cs ON cs.id = t.user_department_id LEFT JOIN erp_maintenance_schedule msr ON msr.ticket_id = t.id LEFT JOIN erp_maintenance_type mt ON mt.id = msr.maintenance_type_id WHERE t.location_id = 3 AND t.id IN (select ticket_id from erp_ticket_acknowledgement_action_log where submitted_by_emp_number IN ($engLists)) AND t.id IN (select ticket_id from erp_ticket_acknowledgement_action_log where status_id NOT IN (1,11)) AND t.status_id != 5 GROUP BY t.id ORDER BY id DESC";


						$configDate = $this->dateFormat();
		
		$count1 = mysqli_query($this->conn, $query1);

		$Count = mysqli_num_rows($count1);

		//echo $Count;

		if(mysqli_num_rows($count1) > 0)
		{
						$row=mysqli_fetch_assoc($count1);

					do{ 		
						$j = $j+1;

						$data['sno']=$j;
						//$data['equip_count']= $row['equip_count'];
						$data['jobId']= $row['jobId'];
						$data['subject']= $row['subject'];
						
						$data['location']=$row['location'];	
						$data['plantName']= $row['plantName'];	
						$data['department']= $row['department'];
						$data['functionalLocation']= $row['functionallocation_name'];
						$funLoc = $this->subfunctionalLocations($row['functionlocation_id']);
											if($funLoc['status'] == 1){
												$data['functionlocation_id']=$funLoc['id'];
												$data['functionallocation_name']=$funLoc['name'];
												$data['subfunctionlocation_id']=$row['functionlocation_id'];
												$data['subfunctionallocation_name']=$row['functionallocation_name'];
											}else{
												$data['functionlocation_id']=$row['functionlocation_id'];
												$data['functionallocation_name']=$row['functionallocation_name'];
												$data['subfunctionlocation_id']=0;
												$data['subfunctionallocation_name']='';
											}	
						$data['equipment']= $row['equipment'];
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count1));
						$data['jobsHandledByEngineer']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}

        			
        return $data;
    }	


    function jobsHandledByTechnician($user_id)
    {
        $data=array();

        $j=0;


        $empresult=$this->techLists();

				for ($i=0; $i < sizeof($empresult['techlist']) ; $i++) { 
	        	$techList[] = $empresult['techlist'][$i];
	        	//to convert Array into string the following implode method is used
	        	$techLists = implode(',', $techList);
	        }

        $query1 = "SELECT count(*) AS equip_count, t.job_id AS jobId, t.id AS id, t.subject AS subject, t.submitted_on AS calFromDate, t.submitted_on AS calToDate, t.submitted_on AS createdOn, ta.machine_status AS machineStatus, fl.name as functionallocation_name, fl.id as functionlocation_id,toi.name AS typeOfIssue, toi.id AS typeOfIssueId, toi.sla AS sla,loc.name AS location, loc.id AS locationId, plnt.plant_name AS plantName, plnt.id AS plantId, eq.name AS equipment, eq.id AS equipmentId, ts.name AS status, ts.id AS statusId, ta.ticket_id AS ticketId, t.submitted_by_name AS submittedByName, e.emp_number AS engineerId, e.emp_number AS technicianId,tp.name AS priority, tp.id AS priorityId, tsev.name AS severity, tsev.id AS severityId, u.id AS uaerId, msr.id AS scheduleId, msr.maintenance_type_id AS maintenanceType,mt.id AS maintenanceId, mt.name AS maintenanceName,cs.name AS department, cs.id AS subDivisionId FROM erp_ticket t LEFT JOIN erp_functional_location fl ON fl.id = t.functional_location_id LEFT JOIN erp_type_of_issue toi ON toi.id = t.type_of_issue_id LEFT JOIN erp_location loc ON loc.id = t.location_id LEFT JOIN erp_plant plnt ON plnt.id = t.plant_id LEFT JOIN erp_equipment eq ON eq.id = t.equipment_id LEFT JOIN erp_ticket_status ts ON ts.id = t.status_id LEFT JOIN erp_ticket_acknowledgement_action_log ta ON ta.ticket_id = t.id LEFT JOIN hs_hr_employee e ON e.emp_number = ta.submitted_by_emp_number LEFT JOIN erp_user u ON u.id = ta.created_by_user_id LEFT JOIN erp_ticket_priority tp ON tp.id = t.priority_id LEFT JOIN erp_ticket_severity tsev ON tsev.id = t.severity_id LEFT JOIN erp_subunit cs ON cs.id = t.user_department_id LEFT JOIN erp_maintenance_schedule msr ON msr.ticket_id = t.id LEFT JOIN erp_maintenance_type mt ON mt.id = msr.maintenance_type_id WHERE t.location_id = 3 AND t.id IN (select ticket_id from erp_ticket_acknowledgement_action_log where submitted_by_emp_number IN ($techLists)) AND t.id IN (select ticket_id from erp_ticket_acknowledgement_action_log where status_id NOT IN (1,11)) AND t.status_id != 5 GROUP BY t.id ORDER BY id DESC";


						$configDate = $this->dateFormat();
		
		$count1 = mysqli_query($this->conn, $query1);

		$Count = mysqli_num_rows($count1);

		//echo $Count;

		if(mysqli_num_rows($count1) > 0)
		{
						$row=mysqli_fetch_assoc($count1);

					do{ 		
						$j = $j+1;

						$data['sno']=$j;
						//$data['equip_count']= $row['equip_count'];
						$data['jobId']= $row['jobId'];
						$data['subject']= $row['subject'];
						
						$data['location']=$row['location'];	
						$data['plantName']= $row['plantName'];	
						$data['department']= $row['department'];
						$data['functionalLocation']= $row['functionallocation_name'];
						$funLoc = $this->subfunctionalLocations($row['functionlocation_id']);
											if($funLoc['status'] == 1){
												$data['functionlocation_id']=$funLoc['id'];
												$data['functionallocation_name']=$funLoc['name'];
												$data['subfunctionlocation_id']=$row['functionlocation_id'];
												$data['subfunctionallocation_name']=$row['functionallocation_name'];
											}else{
												$data['functionlocation_id']=$row['functionlocation_id'];
												$data['functionallocation_name']=$row['functionallocation_name'];
												$data['subfunctionlocation_id']=0;
												$data['subfunctionallocation_name']='';
											}	
						$data['equipment']= $row['equipment'];
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count1));
						$data['jobsHandledByTechnician']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}

        			
        return $data;
    }



   /*//punch in or punch out
    function punchInOrOut($user_id,$punch_in_utc_time,$punch_in_note,$punch_in_time_offset,$punch_in_user_time,$punch_out_utc_time,$punch_out_note,$punch_out_time_offset,$punch_out_user_time,$state)
    {
        $data=array();

        $EmpNumber = $this->getEmpnumberByUserId($user_id);

        $query="SELECT * FROM erp_attendance_record WHERE employee_id = $EmpNumber";
		
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$state = "PUNCHED OUT";
				$row=mysqli_fetch_assoc($count);
					$updatesql ="UPDATE erp_attendance_record 
					SET punch_out_utc_time = $punch_out_utc_time,
						punch_out_note = $punch_out_note,
						punch_out_time_offset = $punch_out_time_offset,
						punch_out_user_time = $punch_out_user_time,
						state = $state
					 WHERE employee_id = $EmpNumber";
					if($result2 = mysqli_query($this->conn, $updatesql)){
						$data['punch_out_utc_time'] = $row['punch_out_utc_time'];
						$data['punch_out_note'] = $row['punch_out_note'];
						$data['punch_out_time_offset'] = $row['punch_out_time_offset'];
						$data['punch_out_user_time'] = $row['punch_out_user_time'];
						$data['state'] = $row['state'];
				        $data['status']=1;
					}else{
					    $data['status']=0;
					}
		}

		else
		{
			$statein = "PUNCHED IN";
   			// Prepare an insert statement
			$sql = "INSERT INTO erp_attendance_record (employee_id,punch_in_utc_time,punch_in_note,punch_in_time_offset,punch_in_user_time,state) VALUES (?,?,?,?,?)";
			 
			  $configDate = $this->dateFormat();	
			if($stmt = mysqli_prepare($this->conn, $sql)){
			    // Bind variables to the prepared statement as parameters
			     mysqli_stmt_bind_param($stmt, "issss" , $employee_id,$punch_in_utc_time,$punch_in_note,$punch_in_time_offset,$punch_in_user_time,$statein);
			   		   
			    // Attempt to execute the prepared statement
			    if(mysqli_stmt_execute($stmt)){

			    	$data['punch_in_utc_time'] = $row['punch_in_utc_time'];
			    	date($configDate, strtotime( $row['punch_in_utc_time'] )).' '.date('H:i', strtotime( $row['punch_in_utc_time'] ));
						$data['punch_in_note'] = $row['punch_in_note'];
						$data['punch_in_time_offset'] = date($configDate, strtotime( $row['punch_in_time_offset'] )).' '.date('H:i', strtotime( $row['punch_in_time_offset'] ));
						$data['punch_in_user_time'] = date($configDate, strtotime( $row['punch_in_time_offset'] )).' '.date('H:i', strtotime( $row['punch_in_time_offset'] ));
						$data['state'] = $row['state'];
						$data['punchInOrOut'] = $data;
			     
			        $data['status']=1;
			    } else{
			        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
			        $data['status']=0;
			    }
			} else{
			    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
			    $data['status']=0;
			}

		}
        return $data;
    }

*/
  function getEmpnumberByUserId($user_id)
        {
            $query = "SELECT emp_number FROM erp_user WHERE id = $user_id";
            $result=mysqli_query($this->conn, $query);
            if(mysqli_num_rows($result)>0)
            {
               $row = mysqli_fetch_array($result);
               $emp_number=$row['emp_number'];
            }
       
       return $emp_number;
    }

     function getLocationByUserId($user_id)
        {
            $query = "SELECT l.location_id as location_id FROM erp_user u LEFT JOIN hs_hr_emp_locations l ON l.emp_number = u.emp_number WHERE u.id = $user_id";
            $result=mysqli_query($this->conn, $query);
            if(mysqli_num_rows($result)>0)
            {
               $row = mysqli_fetch_array($result);
               $emp_number=$row['location_id'];
            }
       
       return $emp_number;
    }

  //Attendance
    function attendance($userId)
	{	
		$empId = $this->getEmpnumberByUserId($userId);
		$data= array();
		$query="SELECT * FROM erp_attendance_record WHERE (employee_id = $empId AND state IN ('PUNCHED IN'))";

		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			$data['id']=$row['id'];
			$data['state']=$row['state'];	
			$data['attendancedetails']=$data;			
			$data['status']=1;
							
		}else{
				$data['id']='';
				$data['state']= 'PUNCHED OUT';	
				$data['status']=0;
				$data['attendancedetails']=$data;
			}
		return $data;    
	}

	function punchInOrOut($id,$user_id,$punch_note,$punch_in_user_time1,$punch_out_user_time1)
    {
      	$data=array();

		    //$EmpNumber = $this->getEmpnumberByUserId($user_id);
		    $punch_out_utc_time1 = date('Y-m-d H:i:s');
		    $punch_out_time_offset1 = 5.5;
		    //$punch_out_user_time1 = date('Y-m-d H:i:s');
		    $state = "PUNCHED OUT";
		    $punch_in_utc_time1 = date('Y-m-d H:i:s');
		    $statein = "PUNCHED IN";
		    $punch_in_time_offset1 = 5.5;
		    //$punch_in_user_time1   = date('Y-m-d H:i:s');
		    $punchId = '';

		    $empId = $this->getEmpnumberByUserId($user_id);
		    $data= array();


				    $query1 ="SELECT id,punch_in_note FROM erp_attendance_record WHERE id = $id";
				     if($result1 = mysqli_query($this->conn, $query1))
				     {

				     	$row=mysqli_fetch_assoc($result1);
				     	$punchId = $row['id'];
				     	$punch_in_note = $row['punch_in_note'];

				     }


				     if($punchId == '')
				     {
				     	$query="SELECT ui.last_id FROM hs_hr_unique_id ui WHERE ui.table_name = 'erp_attendance_record' AND ui.field_name='id'";
						$count=mysqli_query($this->conn, $query);

						if(mysqli_num_rows($count) > 0)
						{
							$row=mysqli_fetch_assoc($count);

							$data['last_id']=$row['last_id'];
							$id = $row['last_id']+1;

							$sql = "UPDATE hs_hr_unique_id SET last_id = ".$id." WHERE table_name = 'erp_attendance_record' AND field_name='id'";
							
							$result=mysqli_query($this->conn, $sql);
						}


						$sql = "INSERT INTO erp_attendance_record (id,employee_id,punch_in_utc_time,punch_in_note,
				     	punch_in_time_offset,punch_in_user_time,state) VALUES (?,?,?,?,?,?,?)";


							
					if($stmt1213 = mysqli_prepare($this->conn, $sql)){

						// echo "if";
						// exit();

					    // Bind variables to the prepared statement as parameters
					     mysqli_stmt_bind_param($stmt1213, "iisssss" , $id,$empId,$punch_in_utc_time1,$punch_note,$punch_in_time_offset1,$punch_in_user_time1,$statein);

					    // Attempt to execute the prepared statement
					    if(mysqli_stmt_execute($stmt1213)){
					    	
					    	$data['punchInpunchOutdetails']= "Punched in Successfully";          
				        	$data['status']=1;
					    } else{

					        $data['status']=0;
					        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
					    }
					} else{
						
					    $data['status']=0;
					    //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
					}	
			
				     }
				     else
				     {

				     		$query="UPDATE erp_attendance_record 
                SET punch_out_utc_time = '$punch_out_utc_time1',
                    punch_out_note = '$punch_note',
                    punch_out_time_offset = '$punch_out_time_offset1',
                    punch_out_user_time = '$punch_out_user_time1',
                    state = '$state'
                 WHERE id = $id";


                  $query23="SELECT * FROM erp_attendance_record WHERE (employee_id = $empId AND state IN ('PUNCHED IN'))";
				    $count=mysqli_query($this->conn, $query23);
				    if(mysqli_num_rows($count) > 0)
				    {
				        $row=mysqli_fetch_assoc($count);
				        $data2['id']=$row['id'];
				        $data2['state']=$row['state'];  

				        if($result2 = mysqli_query($this->conn, $query))
				    		{
				        		/*echo "punched out";
				    					exit();*/
				            // $row=mysqli_fetch_assoc($result2);
				                // echo $result2['punch_out_utc_time'];die();
				                
				                    // $data['punch_out_utc_time'] = $result2['punch_out_utc_time'];
				                    // $data['punch_out_note'] = $result2['punch_out_note'];
				                    // $data['punch_out_time_offset'] = $result2['punch_out_time_offset'];
				                    // $data['punch_out_user_time'] = $result2['punch_out_user_time'];
				                    // $data['state'] = $result2['state'];
				    					$data['status']=1;
				                   
							    }else{
							    	
							            $data['status']=0;
							                    

							    }

				        $data['punchInpunchOutdetails']="Punched Out Successfully";            
				        $data['status']=1;
				                        
				    }else{

				    	/*echo "already punched out";
				    	exit();*/
				            $data['punchInpunchOutdetails']="Already Punched Out";            
				            $data['status']=0;
			
				        }
    		

		     }

				   


    			return $data;

		}




	//Password Generator
    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

	function forgotPassword($username)
	{
		$data=array();
		$token=$this->generateApiKey();

		$userEmail = $this->getEmailByUsrname($username);

		$user_id = $this->getUserIdByUsrname($username);
		
			$rndno = $this->randomPassword();
			$data['status']=1;
			$data['user_id']=$user_id;
			$data['password'] = $rndno;
			$data['email'] = $userEmail;
			
		return $data;
	}

	function updatePassword($userId,$password){

		$hashPassword = $this->hashPassword($password);
		$query ="UPDATE erp_user SET user_password= '$hashPassword' WHERE id=$userId";
										
		$count=mysqli_query($this->conn, $query);
		return $count;
	}

	public function hashPassword($password) {
        return $this->getPasswordHasher()->hash($password);
    }

	public function getPasswordHasher() {
        if (empty($this->passwordHasher)) {
            $this->passwordHasher = new PasswordHash();
        }        
        return $this->passwordHasher;
    }

    public function setPasswordHasher($passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

	    
    //this is for otp verfication function
    function pwdOtpVerify($user_id,$otp)
	{
		$data=array();
		$query = "SELECT otp FROM erp_user_token WHERE userid = $user_id";
		// echo $query;exit;
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{			
			$row=mysqli_fetch_assoc($count);
			$data['otp']=$row['otp'];
			if($row['otp'] == $otp){
				$data['otpverified']="Successfully";
				$data['status']=1;
			}else{
				$data['status']=2;
			}
		}
			else{
				$data['status']=0;
			}
		
		return $data;
    }


    //this is for otp verfication function
    function passwordChange($user_id,$oldPassword,$newPassword)
	{

	
		$data=array();

		$query = "SELECT u.user_password AS user_password FROM erp_user u WHERE u.deleted=0 and u.id =$user_id";

		$count=mysqli_query($this->conn, $query);

		// echo $query;
		// exit();
		if(mysqli_num_rows($count) > 0)
		{
				
			$row=mysqli_fetch_assoc($count);
			$userPassword = $row['user_password'];

			$verify = password_verify($oldPassword,$userPassword);

			if($verify){
				$result = $this->updatePassword($user_id,$newPassword);
				if($result){
					$data['status']=1;
					$data['message']="Password Changed Successfully";
				}else{
					$data['status']=1;
					$data['message']="Password Changed unsuccessful";
				}
			}else{
				
				$data['status']=0;
				$data['message']="Password Incorrect";
			   
	    	}

		}else{
			$data['status']=0;
			$data['message']="User does not exist";
    	}

		return $data;
    }













	function pdfDwnLoad($userIdPass)
	{


		//echo $userIdPass;


				$userDetails = $this->ticketDetByTypeOfIssue($userIdPass);

		

		

		$data['tktdetails'] = $userDetails;
		

		return $data;
	}

	function pdfStatusDwnLoad($userIdPass)
	{


		//echo $userIdPass;


				$userDetails = $this->jobsByStatus($userIdPass);

		

		

		$data['tktdetails'] = $userDetails;
		

		return $data;
	}




	function xlsDwnLoad($userIdPass)
	{


		//echo $userIdPass;
		$userDetails = $this->ticketDetByTypeOfIssue($userIdPass);

	
		$data['tktdetails'] = $userDetails;
		

		return $data;
	}

function excelStatusDwnLoad($userIdPass)
	{


		//echo $userIdPass;
		$userDetails = $this->jobsByStatus($userIdPass);

		
		$data['tktdetails'] = $userDetails;
		
		return $data;
	}

	function pdfMainTypRepDwnLoad($userIdPass)
	{


		//echo $userIdPass;


				$userDetails = $this->MaintenanceTypeReport($userIdPass);

		

		

		$data['tktdetails'] = $userDetails;
		

		return $data;
	}

	function excelMainTypRepDwnLoad($userIdPass)
	{


		//echo $userIdPass;
		$userDetails = $this->MaintenanceTypeReport($userIdPass);

		
		$data['tktdetails'] = $userDetails;
		

		return $data;
	}


	function EqpmntDetailsBasedOnEqIdAll($userIdPass)
	{


		$data=array();

        $i=0;
        $query1 = "SELECT t.equipment_id as eqId,eqp.name as equipmentName,t.job_id as JobId, t.submitted_on as createdDate,t.id as ticket_id FROM erp_ticket t LEFT JOIN erp_equipment eqp ON t.equipment_id = eqp.id WHERE t.status_id IN (10,5)
GROUP BY t.equipment_id ORDER BY t.job_id ASC";

        $count1 = mysqli_query($this->conn, $query1);

        if(mysqli_num_rows($count1) > 0)

		{

			//echo "if";
						$row=mysqli_fetch_assoc($count1);


						do{ 

							//echo "do";

							$ticket_id= $row['ticket_id'];
							//echo $ticket_id."\n";
							$job_id = $row['JobId'];
							//echo $job_id."\n";
							$eq_id = $row['eqId'];
							//echo $eq_id."\n";
							$eq_name = $row['equipmentName'];
							//echo $eq_name."\n";
							$created_date = $row['createdDate'];
							//echo $created_date."\n";

								$i = $i+1;
							//$data['eqId']= $row['eqId'];
							$data['sno']=$i;	
								$data['eqId']= $row['eqId'];
								//echo $eqId;
								$data['JobId']= $row['JobId'];
								//echo $eqId;
								$data['ticket_id']= $row['ticket_id'];
								$data['equipmentName']=$row['equipmentName'];	
								$data['createdDate']= $row['createdDate'];

							
							

												$configDate = $this->dateFormat();

								$query2 = "SELECT submitted_on as suboncom FROM `erp_ticket_acknowledgement_action_log` WHERE ticket_id = $ticket_id AND status_id = 14";

									 $count2 = mysqli_query($this->conn, $query2);

									 $Count = mysqli_num_rows($count2);

									 //echo $Count;
									 //exit();
									  if(mysqli_num_rows($count2) > 0)
								{

									
												$row = mysqli_fetch_assoc($count2);

											
													$suboncom = $row['suboncom']; 
													//echo $suboncom;

												
								}else{

										$suboncom = '';
										$data['status']=0;
									}


									$query3 = "SELECT submitted_on as subonnew FROM `erp_ticket_acknowledgement_action_log` WHERE ticket_id = $ticket_id AND status_id = 1";

						       $count3 = mysqli_query($this->conn, $query3);

						       if(mysqli_num_rows($count3) > 0)
								{

										
												$row = mysqli_fetch_assoc($count3);

											
													$subonnew = $row['subonnew']; 
													/*echo $subonnew;
												exit();*/	
												
								}else{

										
										$data['status']=0;
									}


									
							//echo $ticket_id."\n";

									 $query4 = "SELECT submitted_on as subonEndcom FROM `erp_ticket_acknowledgement_action_log` WHERE ticket_id = $ticket_id AND status_id = 10";

									 $count4 = mysqli_query($this->conn, $query4);

									  if(mysqli_num_rows($count4) > 0)
								{

									//echo "count4 if";
												$row = mysqli_fetch_assoc($count4);

											
													$subonEndcom = $row['subonEndcom']; 
													//echo $suboncom;

												
								}else{

										//echo "count4 else";

											$subonEndcom = '';
										$data['status']=0;
									}


									/*echo 'com'.$suboncom.' ';
									echo 'new'.$subonnew.' ';
									echo 'endcom'.$subonEndcom.' ';
									exit();*/
									if(($suboncom != '') && ($subonEndcom != ''))
									{


										$dteStart = new DateTime($subonnew); 

											
								   			$dteEnd   = new DateTime($suboncom);

								   			

								   			$dteDiff  = $dteEnd->diff($dteStart); 


								   			
								   			$resolvedDuration = $dteDiff->format("%H:%I"); 
								   			
								   			$interval = $dteStart->diff($dteEnd);
											
											$dys = $interval->format('%a');
											
								                                    $hrs = $interval->format('%h');
								            
								                                    $mins = $interval->format('%i');

								                //echo 'mins'.$mins;

								                 $hrs += $dys * 24;

								                 $ResolvedTime = $hrs.':'.$mins;

								                 $dteComEnd   = new DateTime($subonEndcom);
								                 $dteDiff1  = $dteComEnd->diff($dteEnd); 

												$resolvedDuration1 = $dteDiff1->format("%H:%I"); 
								   			
								   			$interval = $dteEnd->diff($dteComEnd);
											
											$dys = $interval->format('%a');
											
								                                    $hrs = $interval->format('%h');
								            
								             //echo 'hrs'.$hrs. "\n";
								                                    $mins = $interval->format('%i');

								                //echo 'mins'.$mins. "\n";

								                 $hrs += $dys * 24;

								                 //echo 'hrs'.$hrs. "\n";

								                 $CompletedTime = $hrs.':'.$mins;

								                 //$dteComEnd   = new DateTime($subonEndcom);
								                 $dteDiff2  = $dteComEnd->diff($dteStart); 

												$resolvedDuration1 = $dteDiff2->format("%H:%I"); 
								   			
								   			$interval = $dteStart->diff($dteComEnd);
											
											$dys = $interval->format('%a');
											
								                                    $hrs = $interval->format('%h');
								            
								                                    $mins = $interval->format('%i');

								                //echo 'mins'.$mins;

								                 $hrs += $dys * 24;

								                 $TotalCompletedTime = $hrs.':'.$mins;


								                 $data['ResolvedTime']= $ResolvedTime;
						$data['CompletedTime']= $CompletedTime;	
						$data['TotalCompletedTime']= $TotalCompletedTime;

									}

									else
									{

										$dteStart1 = new DateTime($subonnew); 

										//echo 'sub'.$subonnew;
										

										  $dteComEnd1   = new DateTime($subonEndcom);

										  //echo 'end'.$subonEndcom;
										//exit();


										 $dteDiff3  = $dteComEnd1->diff($dteStart1); 

												$resolvedDuration2 = $dteDiff3->format("%H:%I"); 
								   			
								   			$interval1 = $dteStart1->diff($dteComEnd1);
											
											$dys1 = $interval1->format('%a');

											 //echo 'dys1'.$dys1. "\n";
											
								                                    $hrs1 = $interval1->format('%h');
								            
								                                    $mins1 = $interval1->format('%i');

								                //echo 'mins1'.$mins1. "\n";

								                 $hrs1 += $dys1 * 24;

								                   //echo 'hrs1'.$hrs1. "\n";

								                 $ResolvedTime1 = '';

								                  $CompletedTime1 = $hrs1.':'.$mins1;

								                 $TotalCompletedTime1 = $hrs1.':'.$mins1;

										 $data['ResolvedTime']= $ResolvedTime1;
						$data['CompletedTime']= $CompletedTime1;	
						$data['TotalCompletedTime']= $TotalCompletedTime1;
										//exit();

									}
						
				$data1[] = $data;
							}while($row = mysqli_fetch_assoc($count1));
								$data['EqpmntDetailsBasedOnEqIdAll']=$data1;
								$data['status']=1;


						
						
						
							
		}else{
				//echo "last else";
				$data['status']=0;
			}

			

		return $data;
	}

	function pdfMchnwiseBrkDwnLoad($userIdPass)
	{


		//echo $userIdPass;


				$userDetails = $this->EqpmntDetailsBasedOnEqIdAll($userIdPass);

		

		

		$data['tktdetails'] = $userDetails;
		

		return $data;
	}


	function excelMchnwiseBrkDwnLoad($userIdPass)
	{


		//echo $userIdPass;
		$userDetails = $this->EqpmntDetailsBasedOnEqIdAll($userIdPass);

		
		$data['tktdetails'] = $userDetails;
		

		return $data;
	}

	function pdfJobsHndlByTechDwnLoad($userIdPass)
	{


		//echo $userIdPass;


				$userDetails = $this->jobsHandledByTechnician($userIdPass);

		

		

		$data['tktdetails'] = $userDetails;
		

		return $data;
	}

	function excelJobsHndlByTechDwnLoad($userIdPass)
	{


		//echo $userIdPass;
		$userDetails = $this->jobsHandledByTechnician($userIdPass);

		
		$data['tktdetails'] = $userDetails;
		

		return $data;
	}

	function pdfJobsHndlByEngDwnLoad($userIdPass)
	{


		//echo $userIdPass;


				$userDetails = $this->jobsHandledByEngineer($userIdPass);

		

		

		$data['tktdetails'] = $userDetails;
		

		return $data;
	}

	function excelJobsHndlByEngDwnLoad($userIdPass)
	{


		//echo $userIdPass;
		$userDetails = $this->jobsHandledByEngineer($userIdPass);

		
		$data['tktdetails'] = $userDetails;
		

		return $data;
	}

	function pdfDwnTimeRptDwnLoad($userIdPass)
	{


		//echo $userIdPass;


				$userDetails = $this->ticketsDownTimeReport($userIdPass);

		

		

		$data['tktdetails'] = $userDetails;
		

		return $data;
	}

	function excelDowntimeReportDwnLoad($userIdPass)
	{


		//echo $userIdPass;
		$userDetails = $this->ticketsDownTimeReport($userIdPass);

		
		$data['tktdetails'] = $userDetails;
		

		return $data;
	}

	function ticketUpd($job_id,$locationId,$plantId,$usrdeptId,$notifytoId,$statusId,$funclocId,$eqipmntId,$typofisId,$subject,$description,$prtyId,$svrtyId,$reportedBy,$submitted_by_emp_number,$submitted_by_name,$reportedOn,$submitted_on,$user_id,$attachmentId)
	{
		$data= array();
		
		//echo $ticket_id;
		//exit();

			$updatesql1 ="UPDATE erp_ticket SET location_id = '$locationId',plant_id='$plantId', user_department_id='$usrdeptId',notify_to = '$notifytoId',status_id='$statusId',functional_location_id='$funclocId',equipment_id='$eqipmntId',type_of_issue_id='$typofisId',subject='$subject',description='$description',
				priority_id='$prtyId',severity_id='$svrtyId',reported_by='$reportedBy',submitted_by_name='$submitted_by_name',submitted_by_emp_number='$submitted_by_emp_number',reported_on='$reportedOn',submitted_on ='$submitted_on' WHERE job_id = $job_id";

				//echo $query1;
				//exit();

				if($result = mysqli_query($this->conn, $updatesql1)){

							//echo $job_id;
								$data['job_id'] = $job_id;
			
						        
						        $data1[] = $data;
						        $data['ticketupdid'] = $data1;
						        $data['status']=1;
							}else{
							    $data['status']=0;
							}


							//$count1 = mysqli_query($this->conn, $query1);

		//$Count = mysqli_num_rows($count1);

		//echo $Count;

	/*if(mysqli_num_rows($count1) > 0)
		{
						$row=mysqli_fetch_assoc($count1);

					
						$data['job_id'] = $job_id;
						
						
						
						$data1[] = $data;
					
						$data['ticketupdid']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}*/

			return $data;

		}





function persnlDetails($userIdPass,$path,$hostUrl)
	{
		$data= array();
		

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		$query="SELECT concat(emp.emp_firstname,' ',emp.emp_middle_name,' ',emp.emp_lastname) as empname, emp.emp_fathername as fathername,
			emp.emp_mothername as mothername,emp.emp_pancard_id,emp.emp_uan_num,emp.emp_pf_num,emp.emp_dri_lice_num,emp.emp_dri_lice_exp_date,emp.blood_group,emp.emp_hobbies,emp.nation_code,emp.emp_gender,emp.emp_marital_status,emp.emp_birthday,emp.plant_id,emp.business_area FROM hs_hr_employee emp WHERE emp.emp_number = $empNumber" ;
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);

					// print_r($row);die();
					do{ 

						$data['empName']=$row['empname'];
						$data['fatherName']=$row['fathername'];	
						$data['motherName']=$row['mothername'];
						$data['emp_pancard_id']=$row['emp_pancard_id'];	
						$data['emp_uan_num']=$row['emp_uan_num'];
						$data['emp_pf_num']=$row['emp_pf_num'];	
						$data['emp_dri_lice_num']=$row['emp_dri_lice_num'];
						$data['emp_dri_lice_exp_date']=$row['emp_dri_lice_exp_date'];	
						$data['blood_group']=$this->getEmpBloodGroupName($row['blood_group']);
						$data['emp_hobbies']=$row['emp_hobbies'];
						$data['nation_code']=$this->getEmpNationalityName($row['nation_code']);
						$data['emp_gender']=$row['emp_gender'];
						$data['plant_id']=$row['plant_id'];
						$data['company_id']=$row['business_area'];
						$data['emp_marital_status']=$row['emp_marital_status'];
						$data['emp_birthday']= date('d-m-Y',strtotime($row['emp_birthday']));
						$data['emp_picture']=$this->getEmployeePicture($empNumber,$path,$hostUrl);
						// $data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['persnlDetails']=$data;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}

	function getEmployeePicture($empNumber,$path,$hostUrl){
		
  		$value = $path.'get_image.php?id='.$empNumber;
		$dataimage = $value;
		if(empty($dataimage)){
		  return $hostUrl.'/entreplan3.1/symfony/web/webres_598bd8c4489f52.47381308/themes/default/images/noimage.png';
		}else{
		  // return $imageUser;
		  return $dataimage;
		}
	}



function contactDetails($userIdPass)
	{
		$data= array();

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		$query="SELECT * FROM hs_hr_employee WHERE emp_number = $empNumber" ;
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['add_strt1']=$row['emp_street1'];
						$data['add_strt2']=$row['emp_street2'];
						$data['city_code']=$row['city_code'];
						$data['coun_code']=$row['coun_code'];
						$data['emp_zipcode']= $row['emp_zipcode'];
						$data['emp_hm_telephone'] = $row['emp_hm_telephone'];
						$data['emp_mobile']= $row['emp_mobile'];
						$data['emp_work_telephone'] = $row['emp_work_telephone'];
						$data['emp_work_email']= $row['emp_work_email'];
						$data['emp_oth_email'] = $row['emp_oth_email'];
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['contactDetails']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}


function getCalenderEventsList($userIdPass,$filter_id)
	{
		$data= array();
		$information = array();


		$userDetails = $this->getUserRoleByUserId($userIdPass);

		// print_r($userDetails);die();
		$empNumber = $userDetails['empNumber'];

		 $year = date('Y');

		if($filter_id == 0){ 

						// All Events
						$query="SELECT *, IF( h.recurring=1 && YEAR(h.date) <={$year}, DATE_FORMAT(h.date, '{$year}-%m-%d'), h.date ) fdate FROM erp_holiday h WHERE h.recurring = 1 OR h.date >='{$year}-01-01' ORDER BY fdate ASC" ;
						// echo $query;die();
						$holydayscount=mysqli_query($this->conn, $query);
						if(mysqli_num_rows($holydayscount) > 0)
						{
							$hrow=mysqli_fetch_assoc($holydayscount);
							do{ 
								$data['start']=$hrow['date'];
								$data['end']=$hrow['date'];
								$data['allDay']=false;
								$data['title']= $hrow['description'];
								$data['type']= 'Holiday';
							$information[] = $data;
							}while($hrow = mysqli_fetch_assoc($holydayscount)); 


						}

						$bookquery="SELECT * FROM `erp_book_vehicle` as v WHERE v.is_deleted=0" ;

						// echo $query;die();
						$bookcount=mysqli_query($this->conn, $bookquery);
						if(mysqli_num_rows($bookcount) > 0)
						{
								$brow=mysqli_fetch_assoc($bookcount);
								do{ 

									$data['start']=$brow['from_date'];
									$data['end']=$brow['to_date'];
									$data['allDay']=false;
									$origin=$brow['origin'];
									$destination=$brow['destination'];
									$bookedby=$brow['booked_for_value'];
									$fromtime= date("h:i a",strtotime($brow['from_time']));
									$totime= date("h:i a",strtotime($brow['to_date']));

									if($brow['response_id'] == 1){
									$bookingFor = 'Employee : ';
									}
									if($brow['response_id'] == 2){
									$bookingFor = 'Supplier : ';
									}
									if($brow['response_id'] == 3){
									$bookingFor = 'Customer : ';
									}

									$data['title']= 'Booking Details : '.$origin.' to '.$destination.' '.$fromtime.'-'.$totime.' '.'Booked For '.$bookingFor.', Booked by '.$bookedby;
									$data['type']= 'Booking';

									$information[] = $data;
								}while($brow = mysqli_fetch_assoc($bookcount)); 

										
					}

					$leavequery="SELECT * FROM erp_leave WHERE emp_number = $empNumber AND status IN(1,2,3) order by id desc" ;

					// echo $query;die();
					$leavecount=mysqli_query($this->conn, $leavequery);
					if(mysqli_num_rows($leavecount) > 0)
					{
								$lrow=mysqli_fetch_assoc($leavecount);
								do{ 

									$data['start']=$lrow['date'];
									$data['end']=$lrow['date'];
									$status=$lrow['status'];
									$id=$lrow['id'];
									$data['allDay']=true;
									$leave_type_id=$lrow['leave_type_id'];
									// $data['type']=$lrow['leave_type_id'];
									$data['type']= 'Leave';

									// $leaveName = $this->getLeaveTypeName($leave_type_id);
									$leaveName = $this->getEmpnameByEmpNumber($empNumber);

									$currentDate = date('Y-m-d');
									
									$fromtime= date("h:i a",strtotime($lrow['start_time']));
									$totime= date("h:i a",strtotime($lrow['end_time']));

									// $leavedetails = $leaveName;
									$leavedetails = $lrow['comments'];

									$data['title']= 'Leave Details : '.$leavedetails.' '.'Leave '.$leaveName;
									// $data['title']= 'Leave Details : '.$leavedetails.' '.$fromtime.'-'.$totime.' '.'Leave '.$leaveName;

									$information[] = $data;
								}while($lrow = mysqli_fetch_assoc($leavecount)); 

										
					}

					$coursequery="SELECT * FROM `erp_course_schedule` as c WHERE c.is_deleted=0" ;

					// echo $query;die();
					$coursecount=mysqli_query($this->conn, $coursequery);
					if(mysqli_num_rows($coursecount) > 0)
					{
								$crow=mysqli_fetch_assoc($coursecount);
								do{ 

									$data['start']=$crow['schedule_start_date'];
									$data['end']=$crow['schedule_end_date'];
									$status=$crow['status'];
									$id=$crow['id'];
									$data['allDay']=true;
									$courseId=$crow['course_id'];
									$data['type']= 'Course';

									$courseName = $this->getCourseName($courseId);

									$currentDate = date('Y-m-d');
									
									$fromtime= date("h:i a",strtotime($crow['schedule_start_time']));
									$totime= date("h:i a",strtotime($crow['schedule_end_time']));

									$trainingdetails = $courseName;

									$data['title']= 'Traning Details : '.$trainingdetails.' '.$fromtime.'-'.$totime.' '.'Course '.$courseName;

									$information[] = $data;
								}while($crow = mysqli_fetch_assoc($coursecount)); 

										
					}

					$startDate = null; 
					$endDate = null;
					$startDateTimeStamp = (is_null($startDate)) ? strtotime(date("Y-m-d")) : strtotime($startDate);
			        $endDateTimeStamp = (is_null($endDate)) ? strtotime(date("Y-m-d")) : strtotime($endDate);

					$bookmeetingquery="SELECT b.*,m.name FROM `erp_book_meeting_room` as b INNER JOIN erp_meeting_room as m ON b.meeting_room_id=m.id ORDER BY b.from_date ASC" ;
					// $bookmeetingquery="SELECT b.*,m.name FROM `erp_book_meeting_room` as b LEFT JOIN erp_meeting_room as m ON b.meeting_room_id=m.id WHERE b.from_date BETWEEN '".date('Y-m-d', $startDateTimeStamp)."' AND '".date('Y-m-d', $endDateTimeStamp)."' ORDER BY b.from_date ASC;" ;

					// echo $bookmeetingquery;die();
					$meetingcount=mysqli_query($this->conn, $bookmeetingquery);
					if(mysqli_num_rows($meetingcount) > 0)
					{
								$mrow=mysqli_fetch_assoc($meetingcount);
								do{ 

									$date = $mrow['from_date'];
									$status = $mrow['status'];
									$meetingRoomId = $mrow['meeting_room_id'];
									$meetingRoom = 'Meeting Room Details : '.$mrow['name'];

									$start_time = $mrow['from_time'];
									$end_time = $mrow['to_time'];

									$id=$mrow['id'];

									$data['start']= $date;
									// $data['start']= $date.' '.$start_time;
									// $data['end']= $date.' '.$end_time;
									$data['end']= $date;
									$data['allDay']= false;


									$data['title']= $meetingRoom.'  '.date("h:i a",strtotime($start_time)).' - '.date("h:i a",strtotime($end_time)).' '.$mrow['meeting_room_title'];
									$data['type']= 'Meeting Room';

									$information[] = $data;
								}while($mrow = mysqli_fetch_assoc($meetingcount)); 

										
					}

					$taskquery="SELECT t.* FROM `erp_assign_tasks` as t WHERE (t.assigned_by=$empNumber OR t.assigned_to=$empNumber) AND t.is_deleted=0";

					// echo $query;die();
					$taskcount=mysqli_query($this->conn, $taskquery);
					if(mysqli_num_rows($taskcount) > 0)
					{
								$crow=mysqli_fetch_assoc($taskcount);
								do{ 

									$data['start']= date('Y-m-d',strtotime($crow['start_date']));
									$data['end']= date('Y-m-d',strtotime($crow['due_date']));
									$status=$crow['status'];
									$title=$crow['title'];
									$details=$crow['details'];
									$assigned_by=$crow['assigned_by'];
									$assigned_to=$crow['assigned_to'];
									$assigned=$crow['assigned'];
									$taskdetails ='';
									if($assigned == 0 && $assigned_by == $empNumber){
									$assigned_by_name=$this->getEmpnameByEmpNumber($crow['assigned_by']);
									$taskdetails = $title.' - '.$details.' assigned by '.$assigned_by_name;
									}else if($assigned == 1 && $assigned_by == $empNumber){
									$assigned_by_name=$this->getEmpnameByEmpNumber($crow['assigned_by']);
									$taskdetails = $title.' - '.$details.' assigned by '.$assigned_by_name;
									}

									if($assigned == 0){
									$assigned_type = 'Self';
									}else{
									$assigned_type = 'Assigned';

									}
									$id=$crow['id'];
									$data['allDay']=true;
									$data['type']= 'Tasks';
									
									$fromtime= date("h:i a",strtotime($crow['assigned_on']));


									$data['title']= $assigned_type.' Task Details : '.$taskdetails.' '.$fromtime.' Task due date '.$data['end'];

									$information[] = $data;
								}while($crow = mysqli_fetch_assoc($taskcount)); 

										
					}

		}else if($filter_id == 1){
			// Holiday

			$query="SELECT *, IF( h.recurring=1 && YEAR(h.date) <={$year}, DATE_FORMAT(h.date, '{$year}-%m-%d'), h.date ) fdate FROM erp_holiday h WHERE h.recurring = 1 OR h.date >='{$year}-01-01' ORDER BY fdate ASC" ;
			// echo $query;die();
			$holydayscount=mysqli_query($this->conn, $query);
			if(mysqli_num_rows($holydayscount) > 0)
			{
				$hrow=mysqli_fetch_assoc($holydayscount);
				do{ 
					$data['start']=$hrow['date'];
					$data['end']=$hrow['date'];
					$data['allDay']=false;
					$data['title']= $hrow['description'];
					$data['type']= 'Holiday';
				$information[] = $data;
				}while($hrow = mysqli_fetch_assoc($holydayscount)); 


			}
		}else if($filter_id == 2){
			// 2 Booking
				$bookquery="SELECT * FROM `erp_book_vehicle` as v WHERE v.is_deleted=0" ;

				// echo $query;die();
				$bookcount=mysqli_query($this->conn, $bookquery);
				if(mysqli_num_rows($bookcount) > 0)
				{
						$brow=mysqli_fetch_assoc($bookcount);
					do{ 

						$data['start']=$brow['from_date'];
						$data['end']=$brow['to_date'];
						$data['allDay']=false;
						$origin=$brow['origin'];
						$destination=$brow['destination'];
						$bookedby=$brow['booked_for_value'];
						$fromtime= date("h:i a",strtotime($brow['from_time']));
						$totime= date("h:i a",strtotime($brow['to_date']));

						if($brow['response_id'] == 1){
						$bookingFor = 'Employee : ';
						}
						if($brow['response_id'] == 2){
						$bookingFor = 'Supplier : ';
						}
						if($brow['response_id'] == 3){
						$bookingFor = 'Customer : ';
						}

						$data['title']= 'Booking Details : '.$origin.' to '.$destination.' '.$fromtime.'-'.$totime.' '.'Booked For '.$bookingFor.', Booked by '.$bookedby;
						$data['type']= 'Booking';

						$information[] = $data;
					}while($brow = mysqli_fetch_assoc($bookcount)); 

							
				}
		}else if($filter_id == 3){
			// 3 Leave
				$leavequery="SELECT * FROM erp_leave WHERE emp_number = $empNumber AND status IN(1,2,3) order by id desc" ;

				// echo $query;die();
				$leavecount=mysqli_query($this->conn, $leavequery);
				if(mysqli_num_rows($leavecount) > 0)
				{
					$lrow=mysqli_fetch_assoc($leavecount);
					do{ 

						$data['start']=$lrow['date'];
						$data['end']=$lrow['date'];
						$status=$lrow['status'];
						$id=$lrow['id'];
						$data['allDay']=true;
						$leave_type_id=$lrow['leave_type_id'];
						// $data['type']=$lrow['leave_type_id'];
						$data['type']= 'Leave';

						// $leaveName = $this->getLeaveTypeName($leave_type_id);
						$leaveName = $this->getEmpnameByEmpNumber($empNumber);

						$currentDate = date('Y-m-d');
						
						$fromtime= date("h:i a",strtotime($lrow['start_time']));
						$totime= date("h:i a",strtotime($lrow['end_time']));

						// $leavedetails = $leaveName;
						$leavedetails = $lrow['comments'];

						$data['title']= 'Leave Details : '.$leavedetails.' '.'Leave '.$leaveName;
						// $data['title']= 'Leave Details : '.$leavedetails.' '.$fromtime.'-'.$totime.' '.'Leave '.$leaveName;

						$information[] = $data;
					}while($lrow = mysqli_fetch_assoc($leavecount)); 

							
				}
		}else if($filter_id == 4){
			// course events
			$coursequery="SELECT * FROM `erp_course_schedule` as c WHERE c.is_deleted=0" ;
			// echo $query;die();
			$coursecount=mysqli_query($this->conn, $coursequery);
			if(mysqli_num_rows($coursecount) > 0)
			{
						$crow=mysqli_fetch_assoc($coursecount);
						do{ 

							$data['start']=$crow['schedule_start_date'];
							$data['end']=$crow['schedule_end_date'];
							$status=$crow['status'];
							$id=$crow['id'];
							$data['allDay']=true;
							$courseId=$crow['course_id'];
							$data['type']= 'Course';

							$courseName = $this->getCourseName($courseId);

							$currentDate = date('Y-m-d');
							
							$fromtime= date("h:i a",strtotime($crow['schedule_start_time']));
							$totime= date("h:i a",strtotime($crow['schedule_end_time']));

							$trainingdetails = $courseName;

							$data['title']= 'Traning Details : '.$trainingdetails.' '.$fromtime.'-'.$totime.' '.'Course '.$courseName;

							$information[] = $data;
						}while($crow = mysqli_fetch_assoc($coursecount)); 

								
			}
		}else if($filter_id == 5){
			// book meetings
			$startDate = null; 
			$endDate = null;
			$startDateTimeStamp = (is_null($startDate)) ? strtotime(date("Y-m-d")) : strtotime($startDate);
			$endDateTimeStamp = (is_null($endDate)) ? strtotime(date("Y-m-d")) : strtotime($endDate);

			$bookmeetingquery="SELECT b.*,m.name FROM `erp_book_meeting_room` as b INNER JOIN erp_meeting_room as m ON b.meeting_room_id=m.id ORDER BY b.from_date ASC" ;
			// $bookmeetingquery="SELECT b.*,m.name FROM `erp_book_meeting_room` as b LEFT JOIN erp_meeting_room as m ON b.meeting_room_id=m.id WHERE b.from_date BETWEEN '".date('Y-m-d', $startDateTimeStamp)."' AND '".date('Y-m-d', $endDateTimeStamp)."' ORDER BY b.from_date ASC;" ;

			// echo $bookmeetingquery;die();
			$meetingcount=mysqli_query($this->conn, $bookmeetingquery);
			if(mysqli_num_rows($meetingcount) > 0)
			{
					$mrow=mysqli_fetch_assoc($meetingcount);
					do{ 

						$date = $mrow['from_date'];
						$status = $mrow['status'];
						$meetingRoomId = $mrow['meeting_room_id'];
						$meetingRoom = 'Meeting Room Details : '.$mrow['name'];

						$start_time = $mrow['from_time'];
						$end_time = $mrow['to_time'];

						$id=$mrow['id'];

						$data['start']= $date;
						// $data['start']= $date.' '.$start_time;
						// $data['end']= $date.' '.$end_time;
						$data['end']= $date;
						$data['allDay']= false;


						$data['title']= $meetingRoom.'  '.date("h:i a",strtotime($start_time)).' - '.date("h:i a",strtotime($end_time)).' '.$mrow['meeting_room_title'];
						$data['type']= 'Meeting Room';

						$information[] = $data;
					}while($mrow = mysqli_fetch_assoc($meetingcount)); 

							
			}
		}else if($filter_id == 6){
			//  task management
				$taskquery="SELECT t.* FROM `erp_assign_tasks` as t WHERE (t.assigned_by=$empNumber OR t.assigned_to=$empNumber) AND t.is_deleted=0";

				// echo $query;die();
				$taskcount=mysqli_query($this->conn, $taskquery);
				if(mysqli_num_rows($taskcount) > 0)
				{
						$crow=mysqli_fetch_assoc($taskcount);
						do{ 

							$data['start']= date('Y-m-d',strtotime($crow['start_date']));
							$data['end']= date('Y-m-d',strtotime($crow['due_date']));
							$status=$crow['status'];
							$title=$crow['title'];
							$details=$crow['details'];
							$assigned_by=$crow['assigned_by'];
							$assigned_to=$crow['assigned_to'];
							$assigned=$crow['assigned'];
							$taskdetails ='';
							if($assigned == 0 && $assigned_by == $empNumber){
							$assigned_by_name=$this->getEmpnameByEmpNumber($crow['assigned_by']);
							$taskdetails = $title.' - '.$details.' assigned by '.$assigned_by_name;
							}else if($assigned == 1 && $assigned_by == $empNumber){
							$assigned_by_name=$this->getEmpnameByEmpNumber($crow['assigned_by']);
							$taskdetails = $title.' - '.$details.' assigned by '.$assigned_by_name;
							}

							if($assigned == 0){
							$assigned_type = 'Self';
							}else{
							$assigned_type = 'Assigned';

							}
							$id=$crow['id'];
							$data['allDay']=true;
							$data['type']= 'Tasks';
							
							$fromtime= date("h:i a",strtotime($crow['assigned_on']));


							$data['title']= $assigned_type.' Task Details : '.$taskdetails.' '.$fromtime.' Task due date '.$data['end'];

							$information[] = $data;
						}while($crow = mysqli_fetch_assoc($taskcount)); 

								
				}
		}
		$data['getCalenderEventsList']=$information;
		$data['status']=1;
		return $data;    
	}

	function getBookMeetingRoomList($userIdPass)
	{
		$data= array();
		$information = array();

		

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		 $year = date('Y');

		
		$startDate = null; 
		$endDate = null;
		$startDateTimeStamp = (is_null($startDate)) ? strtotime(date("Y-m-d")) : strtotime($startDate);
        $endDateTimeStamp = (is_null($endDate)) ? strtotime(date("Y-m-d")) : strtotime($endDate);

		$bookmeetingquery="SELECT b.*,m.name FROM `erp_book_meeting_room` as b INNER JOIN erp_meeting_room as m ON b.meeting_room_id=m.id ORDER BY b.from_date ASC" ;
		// $bookmeetingquery="SELECT b.*,m.name FROM `erp_book_meeting_room` as b LEFT JOIN erp_meeting_room as m ON b.meeting_room_id=m.id WHERE b.from_date BETWEEN '".date('Y-m-d', $startDateTimeStamp)."' AND '".date('Y-m-d', $endDateTimeStamp)."' ORDER BY b.from_date ASC;" ;

		// echo $bookmeetingquery;die();
		$meetingcount=mysqli_query($this->conn, $bookmeetingquery);
		if(mysqli_num_rows($meetingcount) > 0)
		{
					$mrow=mysqli_fetch_assoc($meetingcount);
					do{ 

						$date = $mrow['from_date'];
						$status = $mrow['status'];
						$meetingRoomId = $mrow['meeting_room_id'];
						$meetingRoom = 'Meeting Room Details : '.$mrow['name'];

						$start_time = $mrow['from_time'];
						$end_time = $mrow['to_time'];

						$id=$mrow['id'];

						$data['start']= $date;
						// $data['start']= $date.' '.$start_time;
						// $data['end']= $date.' '.$end_time;
						$data['end']= $date;
						$data['allDay']= false;


						$data['title']= $meetingRoom.'  '.date("h:i a",strtotime($start_time)).' - '.date("h:i a",strtotime($end_time)).' '.$mrow['meeting_room_title'];
						$data['type']= 'Meeting Room';

						$information[] = $data;
					}while($mrow = mysqli_fetch_assoc($meetingcount)); 

							
		}

		

		$data['bookMeetingRoom']=$information;
		$data['status']=1;
		return $data;    
	}


	// function getBirthdaysList($userIdPass)
	// {
	// 	$data= array();

	// 	$monthDay = date('m-d');

	// 	// echo $monthDay;die();

		

	// 	$userDetails = $this->getUserRoleByUserId($userIdPass);
	// 	$empNumber = $userDetails['empNumber'];


	// 	$query="SELECT * FROM `hs_hr_employee` as e WHERE e.emp_birthday LIKE '%".$monthDay."%'" ;

	// 	// echo $query;die();
	// 	$birthdayscount=mysqli_query($this->conn, $query);
	// 	if(mysqli_num_rows($birthdayscount) > 0)
	// 	{
	// 				$hrow=mysqli_fetch_assoc($birthdayscount);
	// 				do{ 

	// 					$data['id']=$hrow['emp_number'];
	// 					$data['emp_birthday']=$hrow['emp_birthday'];
	// 					$data['emp_name']=$hrow['emp_firstname'].' '.$hrow['emp_lastname'];

	// 					if($empNumber == $data['id']){
	// 					$data['wishes']='Happy Birth Day to You';
	// 					}else{
	// 					$data['wishes']='Today '.$data['emp_name'].' Birth Day';

	// 					}
						

	// 					$data1[] = $data;
	// 				}while($hrow = mysqli_fetch_assoc($birthdayscount)); 

	// 				$data['birthList']=$data1;
	// 				$data['status']=1;
							
	// 	}else{
	// 			$data['birthList']=array();
	// 			$data['status']=0;
	// 	}

	// 	return $data;    
	// }

	function getBirthdaysList($userIdPass)
	{
		$data= array();
		$information = array();

		$monthDay = date('m-d');
		$fulldate = date('Y-m-d');


		// echo $monthDay;die();


		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		if(!empty($userIdPass) || !empty($empNumber)){

		
		$query1="SELECT * FROM `hs_hr_employee` as e WHERE termination_id is NULL AND e.emp_birthday LIKE '%".$monthDay."%' " ;

		// echo $query;die();
		$birthdayscount=mysqli_query($this->conn, $query1);
		if(mysqli_num_rows($birthdayscount) > 0)
		{
					$hrow=mysqli_fetch_assoc($birthdayscount);
					do{ 

						$data['id']=$hrow['emp_number'];
						$data['emp_birthday']=$hrow['emp_birthday'];
						$data['emp_name']=$hrow['emp_firstname'].' '.$hrow['emp_lastname'];

						if($empNumber == $data['id']){
						$data['wishes']='Happy Birth Day to You';
						}else{
						$data['wishes']='Today '.$data['emp_name'].' Birth Day';

						}
						

						$information[] = $data;
					}while($hrow = mysqli_fetch_assoc($birthdayscount)); 
					// $information[] = $data1;		
		}
		// print_r($information);die();
		
		$query2="SELECT jch.*,jv.name FROM `erp_job_candidate_history` as jch LEFT JOIN erp_job_vacancy as jv ON jv.id=jch.vacancy_id WHERE jch.interview_id=$empNumber AND jch.interview_date='".$fulldate."'" ;
						// echo $query;die();
		$interviwscount=mysqli_query($this->conn, $query2);
		if(mysqli_num_rows($interviwscount) > 0)
		{
			$hrow=mysqli_fetch_assoc($interviwscount);
			do{ 
				$data['id']=$empNumber;
				$data['emp_birthday']=$hrow['interview_date'];
				$data['emp_name']= $this->getEmpnameByEmpNumber($empNumber);
				$candidateName = $this->getEmpnameByEmpNumber($hrow['candidate_id']);
				$data['wishes']='Interview '.$hrow['note'].' ,candidate '.$candidateName.' ,'.$hrow['interview_title'].' , Time - '.$hrow['interview_time'];
				$information[] = $data;
			}while($hrow = mysqli_fetch_assoc($interviwscount)); 
				// $information[] = $data1;
		}

		$query3="SELECT * FROM `erp_assign_tasks` as t WHERE t.assigned_to=$empNumber AND (t.start_date >= '".$fulldate."' OR t.due_date <= '".$fulldate."') AND t.status != 4";
						// echo $query;die();
		$taskcount=mysqli_query($this->conn, $query3);
		if(mysqli_num_rows($taskcount) > 0)
		{
			$hrow=mysqli_fetch_assoc($taskcount);
			do{ 
				$data['id']=$empNumber;
				$data['emp_birthday']=$hrow['due_date'];
				$data['emp_name']= $this->getEmpnameByEmpNumber($empNumber);
				$candidateName = $this->getEmpnameByEmpNumber($hrow['assigned_by']);
				$data['wishes']='Task '.$hrow['title'].' ,Assigned By  '.$candidateName.' ,';
				$information[] = $data;
			}while($hrow = mysqli_fetch_assoc($taskcount)); 
				// $information[] = $data1;
		}
		$data['birthList']=$information;
		$data['status']=1;

	}else{

		 // $information[] = $data1;

		// if(!empty($$information)){
			$data['birthList']=$information;
			$data['status']=0;
		// }else{
		// 	$data['birthList']=$information;
		// 	$data['status']=0;
		// }
	}

		return $data;    
	}

	function getCourseName($id){
		$course_name = '';

		$coursequery="SELECT * FROM `erp_course` as c WHERE c.id=$id";
		$coursecount=mysqli_query($this->conn, $coursequery);
		if(mysqli_num_rows($coursecount) > 0)
		{
			$crow=mysqli_fetch_assoc($coursecount);
			do{ 

				$course_name =$crow['course_name'];
						
			}while($crow = mysqli_fetch_assoc($coursecount)); 
		}

		return $course_name;
	}

	function emergencyContacts($userIdPass)
	{
		$data= array();

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		$query="SELECT * FROM hs_hr_emp_emergency_contacts WHERE emp_number = $empNumber" ;
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['name']=$row['eec_name'];
						$data['relationship']=$row['eec_relationship'];
						$data['hmtelphne']=$row['eec_home_no'];
						$data['mobile']=$row['eec_mobile_no'];
						$data['wrktelphn']= $row['eec_office_no'];
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['emergencyContacts']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
		}
		return $data;    
	}



function asigndDepdents($userIdPass)
	{
		$data= array();

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		$query="SELECT * FROM hs_hr_emp_dependents WHERE emp_number = $empNumber";
		
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['name']=$row['ed_name'];
						//$data['relationship']=$row['ed_relationship_type'];
						$data['relationship']=$row['ed_relationship'];
						$data['dteOfBrth']=$row['ed_date_of_birth'];
						
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['asigndDepdents']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}


	function imgrtnRcrds($userIdPass)
	{
		$data= array();

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		$query="SELECT * FROM hs_hr_emp_passport WHERE emp_number = $empNumber";
		
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['ep_passport_num']=$row['ep_passport_num'];
					$data['issdDate']=$row['ep_passportissueddate'];
					$data['expiryDate']=$row['ep_passportexpiredate'];
						//$data['dteOfBrth']=$row['ed_date_of_birth'];
						
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['imgrtnRcrds']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}



	function jobDetails($userIdPass)
	{
		$data= array();

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		$query="SELECT * FROM  hs_hr_employee WHERE emp_number = $empNumber";
		
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

					$data['job_title_code']= $this->getJobTitleCode($row['job_title_code']);
					$data['emplmntStatus']= $this->getEmployeementStatus($row['emp_status']);
					$data['joinedDate']=$row['joined_date'];
					$data['department']= $this->getDepartment($row['work_station']);
					$data['eeo_cat_code']=$row['eeo_cat_code'];
					$data['plantName']= $this->getPlantName($row['plant_id']);
					$data['plantId']= $row['plant_id'];
					$data['emp_ctc']=$row['emp_ctc'];	
					$data['emp_cost_of_company']=$row['emp_cost_of_company'];	
					$data['emp_gross_salary']=$row['emp_gross_salary'];		
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['jobDetails']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}

	function getJobTitleCode($id){

		$query = "SELECT job_title FROM erp_job_title WHERE id = $id";
            $result=mysqli_query($this->conn, $query);
            if(mysqli_num_rows($result)>0)
            {
               $row = mysqli_fetch_array($result);
               $job_title = $row['job_title'];
            }
       
       return $job_title;
	}
	function getEmployeementStatus($id){

		$query = "SELECT name FROM erp_employment_status WHERE id = $id";
            $result=mysqli_query($this->conn, $query);
            if(mysqli_num_rows($result)>0)
            {
               $row = mysqli_fetch_array($result);
               $name = $row['name'];
            }
       
       return $name;
	}
	function getDepartment($id){
		$name = "";
		$query = "SELECT name FROM erp_subunit WHERE id = $id";
            $result=mysqli_query($this->conn, $query);
            if(mysqli_num_rows($result)>0)
            {
               $row = mysqli_fetch_array($result);
               $name = $row['name'];
            }
       
       return $name;
	}
	function getPlantName($id){

		$query = "SELECT plant_name FROM erp_plant WHERE id = $id";
            $result=mysqli_query($this->conn, $query);
            if(mysqli_num_rows($result)>0)
            {
               $row = mysqli_fetch_array($result);
               $name = $row['plant_name'];
            }
       
       return $name;
	}

	function salaryComponents($userIdPass)
	{
		$data= array();

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		$query="SELECT * FROM  hs_hr_emp_basicsalary WHERE emp_number = $empNumber";
		
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['emp_number']=$row['emp_number'];
					$data['sal_grd_code']=$row['sal_grd_code'];
					$data['currency_id']=$row['currency_id'];
					$data['ebsal_basic_salary']=$row['ebsal_basic_salary'];
					$data['payperiod_code']=$row['payperiod_code'];
					$data['salary_component']=$row['salary_component'];
					$data['comments']=$row['comments'];	
						
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['salaryComponents']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}


	function reportTo($userIdPass)
	{
		$data= array();

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		$query="SELECT concat(emp.emp_firstname,emp.emp_middle_name,emp.emp_lastname) as name, rep.reporting_method_name as reptType FROM hs_hr_emp_reportto hrs LEFT JOIN hs_hr_employee emp ON emp.emp_number = hrs.erep_sup_emp_number
				   LEFT JOIN erp_emp_reporting_method rep ON rep.reporting_method_id = hrs.erep_reporting_mode WHERE hrs.erep_sub_emp_number = $empNumber";
		
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['name']=$row['name'];
					    $data['reptType']=$row['reptType'];
					   
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['reportTo']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}


	function empSubordinatesrepTo($userIdPass)
	{
		$data= array();

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

			$query="SELECT concat(emp.emp_firstname,' ',emp.emp_lastname) as name, rep.reporting_method_name as reptType FROM
						 	hs_hr_emp_reportto hrs
							LEFT JOIN hs_hr_employee emp ON emp.emp_number = hrs.erep_sub_emp_number
							LEFT JOIN erp_emp_reporting_method rep ON rep.reporting_method_id = hrs.erep_reporting_mode
							WHERE hrs.erep_sup_emp_number = $empNumber
							ORDER BY hrs.erep_sub_emp_number ASC";
		
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['name']=$row['name'];
					    $data['reptType']=$row['reptType'];
					   
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['empSubordinates']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}



	function workExp($userIdPass)
	{
		$data= array();

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		$query="SELECT exp.eexp_employer AS company, exp.eexp_jobtit AS jobTitle, exp.eexp_from_date AS fromDate, exp.eexp_to_date
		AS toDate,exp.eexp_comments as comment FROM hs_hr_emp_work_experience exp WHERE exp.emp_number = $empNumber";
		
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['company']=$row['company'];
					    $data['jobTitle']=$row['jobTitle'];
					     $data['fromDate']=$row['fromDate'];
					      $data['toDate']=$row['toDate'];
					       $data['comment']=$row['comment'];
					   
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['workExp']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}


	function emplEductn($userIdPass)
	{
		$data= array();

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		$query="SELECT e.name as level, ed.year as year,ed.score as score FROM erp_emp_education ed LEFT JOIN erp_education e ON e.id = ed.education_id WHERE ed.emp_number = $empNumber";
		
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['level']=$row['level'];
					    $data['year']=$row['year'];
					     $data['score']=$row['score'];
					     
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['emplEductn']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}



	function empSkills($userIdPass)
	{
		$data= array();

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		$query="SELECT sk.name as skill,epski.years_of_exp as exp FROM hs_hr_emp_skill epski LEFT JOIN erp_skill sk ON sk.id = epski.skill_id WHERE epski.emp_number = $empNumber";
		
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['skill']=$row['skill'];
					    $data['exp']=$row['exp'];
					   
					     
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['empSkills']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}

	function empLang($userIdPass)
	{
		$data= array();

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		$query="SELECT ln.name as language,lng.fluency as flncy,lng.competency as cmptncy,lng.comments as cmnts FROM hs_hr_emp_language lng LEFT JOIN erp_language ln ON ln.id = lng.lang_id WHERE lng.emp_number = $empNumber";
		
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['language']=$row['language'];
					    $data['flncy']=$row['flncy'];
					   $data['cmptncy']=$row['cmptncy'];
					     $data['cmnts']=$row['cmnts'];

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['empLang']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}


	function empLicense($userIdPass)
	{
		$data= array();

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		$query="SELECT hrli.name as type,li.license_issued_date as issdDate, li.license_expiry_date as expDate FROM erp_emp_license li LEFT JOIN erp_license hrli ON hrli.id = li.license_id WHERE li.emp_number = $empNumber";
		
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['type']=$row['type'];
					    $data['issdDate']=$row['issdDate'];
					   $data['expDate']=$row['expDate'];
					   
					     
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['empLicense']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}


	function empMbrshp($userIdPass)
	{
		$data= array();

		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

		$query="SELECT ms.name as membership,mem.ememb_subscript_ownership as subPaidBy, mem.ememb_subscript_amount as amount, mem.				ememb_subs_currency as cur,mem.ememb_commence_date cmnsDate,mem.ememb_renewal_date as renDate
						FROM hs_hr_emp_member_detail mem
						LEFT JOIN erp_membership ms ON ms.id = mem.membship_code
						WHERE mem.emp_number = $empNumber";
		
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['membership']=$row['membership'];
					    $data['subPaidBy']=$row['subPaidBy'];
					   $data['amount']=$row['amount'];
					    $data['cur']=$row['cur'];
					     $data['cmnsDate']=$row['cmnsDate'];
					      $data['renDate']=$row['renDate'];
					     
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['empMbrshp']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}






     function MyjbsandDeptJbsCount($user_id)
    {
        

        $data=array();

       
       $userDetails = $this->getUserRoleByUserId($user_id);
		$emp_number = $userDetails['empNumber'];
		//echo $emp_number;
		$userRoleId = $userDetails['id'];
		$empresult=$this->employeeDetails($emp_number);
		$departmentId = $empresult['work_station'];

       if($userRoleId == 10){
        //$query = "SELECT COUNT(t.id) as myJob FROM erp_ticket t";

         $query = "SELECT * FROM erp_ticket t";

		$count=mysqli_query($this->conn, $query);

		$myJobs = mysqli_num_rows($count);

			/*echo $myJobs;
			exit();*/

			$userDetails = $this->getUserRoleByUserId($user_id);
		$empNumber = $userDetails['empNumber'];
		

      
		$query1 = "SELECT * FROM erp_ticket WHERE user_department_id = $departmentId";	


		$count1 = mysqli_query($this->conn, $query1);

		$deptJobs = mysqli_num_rows($count1);

	

						if(mysqli_num_rows($count) > 0)
		{				
						$data['myJobs'] = $myJobs;

						$data['deptJobs'] = $deptJobs;


						
						$data1[] = $data;
				//	}while($row = mysqli_fetch_assoc($count));
						$data['MyjbsandDeptJbsCount']=$data1;
						$data['status']=1;
			}
			else
			{

				$data['status']=0;
			}
			

		}

		else if($userRoleId == 11)	

		{

			$userDetails = $this->getUserRoleByUserId($user_id);
		$empNumber = $userDetails['empNumber'];
		$empresult=$this->employeeDetails($empNumber);
		$departmentId = $empresult['work_station'];

		//echo $departmentId;
		//exit();

		$engMyJobQuery = "SELECT t.job_id AS job_id, t.id AS ticketId, t.subject AS subject, t.submitted_on AS submittedon,t.is_PreventiveMaintenance AS preventiveMaintenance,ta.machine_status AS machineStatus, fl.name as functionallocation, fl.id as functionalLocationId,toi.name AS issue, toi.id AS typeOfIssueId, toi.sla AS sla,loc.name AS location, loc.id AS locationId, plnt.plant_name AS plant, plnt.id AS plantId, eq.name AS equipment, eq.id AS equipmentId, ts.name AS status, ts.id AS statusId, ta.ticket_id AS ticketId, t.submitted_by_name AS submittedby, e.emp_number AS engineerId, e.emp_number AS technicianId,tp.name AS priority, tp.id AS priorityId, tsev.name AS severity, tsev.id AS severityId, u.id AS uaerId, cs.name AS department, cs.id AS subDivisionId FROM erp_ticket t LEFT JOIN erp_functional_location fl ON fl.id = t.functional_location_id LEFT JOIN erp_type_of_issue toi ON toi.id = t.type_of_issue_id LEFT JOIN erp_location loc ON loc.id = t.location_id LEFT JOIN erp_plant plnt ON plnt.id = t.plant_id LEFT JOIN erp_equipment eq ON eq.id = t.equipment_id LEFT JOIN erp_ticket_status ts ON ts.id = t.status_id LEFT JOIN erp_ticket_acknowledgement_action_log ta ON ta.ticket_id = t.id LEFT JOIN hs_hr_employee e ON e.emp_number = ta.submitted_by_emp_number LEFT JOIN erp_user u ON u.id = ta.created_by_user_id LEFT JOIN erp_ticket_priority tp ON tp.id = t.priority_id LEFT JOIN erp_ticket_severity tsev ON tsev.id = t.severity_id LEFT JOIN erp_subunit cs ON cs.id = t.user_department_id WHERE ta.submitted_by_emp_number = $empNumber AND t.id NOT IN (select id from erp_ticket where status_id = 11 and submitted_by_emp_number != $empNumber) GROUP BY t.id
			ORDER BY `t`.`job_id` DESC";

			$engMyJobcount = mysqli_query($this->conn, $engMyJobQuery);

			$myjbcnt = mysqli_num_rows($engMyJobcount);


						if(mysqli_num_rows($engMyJobcount) > 0)
							{				

									$data['myJobs'] = $myjbcnt;

							}

							else
							{

								$data['status']=0;
							}
			
			$engDeptQuery = "SELECT * FROM erp_ticket WHERE user_department_id = $departmentId";

		$engDeptcount=mysqli_query($this->conn, $engDeptQuery);

		
			$deptjbcnt = mysqli_num_rows($engDeptcount);

						//$row=mysqli_fetch_assoc($engDeptcount);

						if(mysqli_num_rows($engDeptcount) > 0)
		{				
			
						$data['deptJobs'] = $deptjbcnt;
						
		}else{
					$data['deptJobs'] = 0;
		}



			$empresult6=$this->empShiftSupvsrList($emp_number);
       				/*echo "<pre>";
		 print_r($empresult6);
		 exit();*/
		 
	       for ($i=0; $i < sizeof($empresult6['empShiftSupvsrList']) ; $i++) { 
	        	$empList6[] = $empresult6['empShiftSupvsrList'][$i];
	        	//to convert Array into string the following implode method is used
	        	$empLists6 = implode(',', $empList6);

	        	/*echo "<pre>";
		 print_r($empresult6);
		 exit();*/
	        	

	        }

		$shftJbsQuery = "SELECT COUNT(o.id) AS shftCont
						FROM erp_ticket o
						LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id
						WHERE (o.user_department_id = $departmentId AND o2.status_id = 2 AND o2.forward_from IN ($empLists6) AND o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id))";


						$shftJbscount=mysqli_query($this->conn, $shftJbsQuery);

		
			//$deptjbcnt = mysqli_num_rows($engDeptcount);

						

						if(mysqli_num_rows($shftJbscount) > 0)
							{				

								$row=mysqli_fetch_assoc($shftJbscount);
								
											$data['shftJobs'] = $row['shftCont'];
											
							}else{
										$data['shftJobs'] = 0;
							}



		 				$data1[] = $data;
		 				$data['MyjbsandDeptJbsCount']=$data1;
						$data['status']=1;

	}
	 else 

		 {

		 		$queryothrs = "SELECT t.job_id AS job_id FROM erp_ticket t LEFT JOIN erp_functional_location fl ON fl.id = t.functional_location_id LEFT JOIN erp_type_of_issue toi ON toi.id = t.type_of_issue_id LEFT JOIN erp_location loc ON loc.id = t.location_id LEFT JOIN erp_plant plnt ON plnt.id = t.plant_id LEFT JOIN erp_equipment eq ON eq.id = t.equipment_id LEFT JOIN erp_ticket_status ts ON ts.id = t.status_id LEFT JOIN erp_ticket_acknowledgement_action_log ta ON ta.ticket_id = t.id LEFT JOIN hs_hr_employee e ON e.emp_number = ta.submitted_by_emp_number LEFT JOIN erp_user u ON u.id = ta.created_by_user_id LEFT JOIN erp_ticket_priority tp ON tp.id = t.priority_id LEFT JOIN erp_ticket_severity tsev ON tsev.id = t.severity_id LEFT JOIN erp_subunit cs ON cs.id = t.user_department_id WHERE ta.submitted_by_emp_number = $emp_number AND t.id NOT IN (select id from erp_ticket where status_id = 11 and submitted_by_emp_number != $emp_number) GROUP BY t.id
			ORDER BY `t`.`job_id`  DESC";



			$countothrs=mysqli_query($this->conn, $queryothrs);
			$myjbcnt = mysqli_num_rows($countothrs);

				if(mysqli_num_rows($countothrs) > 0)
				{
						$data['myJobs']=$myjbcnt;
	
				}else{
				$data['myJobs']=0;
			

				 }
		 		$engDeptQuery = "SELECT * FROM erp_ticket WHERE user_department_id = $departmentId";

		$engDeptcount=mysqli_query($this->conn, $engDeptQuery);

		
			$deptjbcnt = mysqli_num_rows($engDeptcount);

						//$row=mysqli_fetch_assoc($engDeptcount);

						if(mysqli_num_rows($engDeptcount) > 0)
					{				
						
									$data['deptJobs'] = $deptjbcnt;
									
					}else{
							$data['deptJobs'] = 0;
						}

		 				$data1[] = $data;
		 				$data['MyjbsandDeptJbsCount']=$data1;
						$data['status']=1;
		}



		
        return $data;
    }


 






     function overAllPendingJobs($userIdPass)
    {
       
        $data=array();

        $i=0;

        $userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];
		$empresult=$this->employeeDetails($empNumber);
		$department = $empresult['work_station'];
		$plantId = $empresult['plant_id'];


		$stsresult=$this->statusLists();

										for ($i=0; $i < sizeof($stsresult['stslist']) ; $i++) { 
							        	$stsList[] = $stsresult['stslist'][$i];
							        	//to convert Array into string the following implode method is used
							        	$stsLists = implode(',', $stsList);
							        }


		$queryAll = "SELECT * from erp_ticket t where t.location_id = 3 and t.plant_id = $plantId and t.status_id IN ($stsLists)";

		/*echo $queryAll;
		exit();*/

		$countAll = mysqli_query($this->conn, $queryAll);

		$overPndngAll = mysqli_num_rows($countAll);


        $query = "SELECT * from erp_ticket t where t.location_id = 3 and t.plant_id = $plantId and t.status_id IN ($stsLists) AND t.submitted_on < NOW() - INTERVAL 30 DAY";


    
        //echo $query;
        //exit();
        $configDate = $this->dateFormat();
		$count=mysqli_query($this->conn, $query);

		$overPndgGtr30 = mysqli_num_rows($count);

			//echo $overNew;
			//exit();

		$query1 = "SELECT * from erp_ticket t where t.location_id = 3 and t.plant_id = $plantId AND t.status_id IN ($stsLists) AND t.submitted_on BETWEEN DATE_SUB( NOW() ,INTERVAL 30 DAY ) AND NOW()";

		/*echo $query1;
		exit();*/

		$count1 = mysqli_query($this->conn, $query1);

		$overPndngBtwn30 = mysqli_num_rows($count1);	



		$query5 = "SELECT * from erp_ticket t where t.location_id = 3 and t.plant_id = $plantId AND t.status_id IN ($stsLists) AND t.submitted_on BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW()";

		$count5 = mysqli_query($this->conn, $query5);

		$overPndngBtwn7 = mysqli_num_rows($count5);

		$query6 = "SELECT * from erp_ticket t where t.location_id = 3 and t.plant_id = $plantId AND t.status_id IN ($stsLists) AND t.submitted_on BETWEEN DATE_SUB( NOW() ,INTERVAL 15 DAY ) AND NOW()";

		//echo $query6;
		//exit();

		$count6 = mysqli_query($this->conn, $query6);

		$overPndngBtwn15 = mysqli_num_rows($count6);

		/*echo $overPndngBtwn15;
			exit();*/

		$query4 = "SELECT * from erp_ticket t where t.location_id = 3 and t.plant_id = $plantId AND t.status_id IN ($stsLists) AND t.submitted_on BETWEEN DATE_SUB(NOW(), INTERVAL 24 HOUR) AND NOW()";

		$count4 = mysqli_query($this->conn, $query4);

		$overPndngBtwn24Hrs = mysqli_num_rows($count4);

						$data['overPndngAll'] = $overPndngAll;	
						$data['overPndgGtr30'] = $overPndgGtr30;
						$data['overPndngBtwn30'] = $overPndngBtwn30;
						$data['overPndngBtwn15'] = $overPndngBtwn15;
						$data['overPndngBtwn7'] = $overPndngBtwn7;
						$data['overPndngBtwn24Hrs'] = $overPndngBtwn24Hrs;
						
						$data1[] = $data;
				
						$data['overAllPendingJobs']=$data1;
						$data['status']=1;
		
        return $data;
    }



    


   


    
    
     
    





 function depDetls()
    {
        $data=array();
  			
		$query="SELECT subu.id as id,subu.name as name FROM erp_subunit subu WHERE subu.level = 3 AND subu.id NOT IN(16)";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$rows=mysqli_fetch_assoc($count);
			for ($i=0; $i < 0; $i++) { 
				
			}
			// $data['Department']= $row['name'];
	  //       $data['Percentage']= $row['id'];
	  //       $data1[] = $data;
   //      }else{
   //      	$data['status']=0;
        }
	    $data['depDetls']=$data1;
		$data['status']=1;
		return $data;
	}

 function depDetlsNew($user_id)
    {
        $data1=array();
  			
		$query="SELECT subu.id as id,subu.name as name FROM erp_subunit subu WHERE subu.level = 3 AND subu.id NOT IN(16)";
		$res=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($res) > 0)
		{
				$row=mysqli_fetch_assoc($res);			
					
				do{ 
				$data1['Department'] = $row['name'];

				$noOfMachines = $this->getEquipmentsByDeptId($row['id']);

				// echo "string ".$noOfMachines;die();

				$noOfWorkingDays = cal_days_in_month(CAL_GREGORIAN,date("m",strtotime("-1 month")),date("Y"));
            	$noOfWorkingHrs = 24 * $noOfWorkingDays;

            	$lastMonth = date("m",strtotime("-1 month"));

            	$totalBreakDownHrs = $this->getBreakDownHours($lastMonth,$row['id']);

    //         	$from = date('Y-m-d 00:00:00');
				// $to = date('Y-m-d '.$totalBreakDownHrs.':00');
				// $diff = strtotime($to) - strtotime($from);
				// $minutes = $diff / 60;

				// $noOfWorkingMin = $noOfWorkingHrs*60;

				// 	$brk1 = $minutes/$noOfWorkingMin;

				// 	$brk2 = $brk1/$noOfMachines;

				// 	$brk3 = $brk2*100;
            	$brkhrs1 = $totalBreakDownHrs / $noOfWorkingHrs;

            	// echo $totalBreakDownHrs;die();

            	$brkhrs2 = $brkhrs1 / $noOfMachines;

            	$brkhrs3 = $brkhrs2 * 100;

            	$breakdown  = (((($totalBreakDownHrs / $noOfWorkingHrs) / $noOfMachines)) * 100);

            	$val = number_format(floatval($breakdown), 2);

				$data1['Percentage'] = $val;

				$data2[] = $data1;
			}while($row = mysqli_fetch_assoc($res));
				$data1['departmntdetails']=$data2;
				$data1['status'] = 1;
		}
		return $data1;
    }


     function departmentsList($user_id)
    {
        $data1=array();
  			
		$query="SELECT subu.id as id,subu.name as name FROM erp_subunit subu WHERE subu.level > 0 AND subu.id NOT IN(16,24,25)";
		// level 3 is department code
		$res=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($res) > 0)
		{
				$row=mysqli_fetch_assoc($res);			
					
				do{ 
				$data1['id'] = $row['id'];
				$data1['Department'] = $row['name'];

				$data2[] = $data1;
			}while($row = mysqli_fetch_assoc($res));
				$data1['departmntdetails']=$data2;
				$data1['status'] = 1;
		}
		return $data1;
    }

        function getEquipmentsByDeptId($id){
    	$query = "SELECT * FROM erp_equipment WHERE department_id = '$id'  AND category_type_id NOT IN (1,2) AND is_Deleted = 0";
    	$count=mysqli_query($this->conn, $query);
		$eqpCnts = mysqli_num_rows($count);
		return $eqpCnts;
    }

    function getBreakDownHours($lastMonth,$departmentId){
    	$data = array();
    	$query = 'SELECT DISTINCT(TIME_FORMAT(SEC_TO_TIME(b.total), "%H:%i")) AS duration1 FROM
        (SELECT t.id,(TIME_TO_SEC(timediff(IF(
            timediff(
                MIN(log.submitted_on),MAX(log.submitted_on)
            ) ,MAX(log.submitted_on),now()),MIN(log.submitted_on)))) AS duration1
        FROM erp_ticket t
        LEFT JOIN erp_ticket_acknowledgement_action_log log ON log.ticket_id = t.id
         LEFT JOIN erp_equipment e ON e.id = t.equipment_id
        WHERE t.user_department_id = '.$departmentId.' AND e.category_type_id NOT IN (1,2) AND MONTH(t.submitted_on) = '.$lastMonth.' AND YEAR(t.submitted_on) = YEAR(now()) GROUP BY log.ticket_id) AS act_preventive
        CROSS JOIN
        (
            SELECT SUM(duration1) total FROM 
            (SELECT t.id,(TIME_TO_SEC(timediff(IF(
            timediff(
                MIN(log.submitted_on),MAX(log.submitted_on)
            ) ,MAX(log.submitted_on),now()),MIN(log.submitted_on)))) AS duration1
        FROM erp_ticket t
        LEFT JOIN erp_ticket_acknowledgement_action_log log ON log.ticket_id = t.id
        LEFT JOIN erp_equipment e ON e.id = t.equipment_id
        WHERE t.user_department_id = '.$departmentId.' AND e.category_type_id NOT IN (1,2) AND MONTH(t.submitted_on) = '.$lastMonth.' AND YEAR(t.submitted_on) = YEAR(now()) GROUP BY log.ticket_id) AS act_preventive
        ) b';

        $qCount1=mysqli_query($this->conn, $query);
        $brkcnt = mysqli_num_rows($qCount1);

		$row1 = mysqli_fetch_assoc($qCount1);

		$duration1 = $row1['duration1'] ? $row1['duration1'] : 0;
		 return $duration1;
    }  



  	

	function sendWhatsapp_msg($country_code, $mobile, $message, $type = 'text')
	{

		$apiToken = '25846661210626682701454197775e16cd6521362'; // eg. 6846532456354354
		$fromNumber = '917989172686';
		$type = trim(strtolower($type));
		
		/* Check passed type is correct or not */
		if($type != 'text')
			return false;
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->apiUrl."?token=".$apiToken."&action=".$type."&from=".$fromNumber."&country=".$country_code."&to=".$mobile."&uid=".uniqid()."&".$type."=".urlencode($message),
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_SSL_VERIFYHOST => 0,
		  CURLOPT_SSL_VERIFYPEER => 0,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_POSTFIELDS => "",
		));
		$apiResponse = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
		  return false;
		} else {
		  return $apiResponse;
		}



	}


	function whatsapp_test()
	{

			$data=array();

				/*echo "whtappmsg";
				exit();*/
				$whatsapp_obj = new WhatsAppAPI();
				$apiResponse = $whatsapp_obj->sendText($country_code = '91', $to_mobile = '7989172686', $message = 'whatsapp msg testing');

				$data['whtsMsgDetails'] = $apiResponse;
		$data['status']=1;
		return $data;

	}


	 function SupplierList()
	{
		$data= array();
		$query="SELECT * FROM erp_vendor";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do { 						
						$data['id']=$row['id'];
						$data['vendor_name']=$row['vendor_name'];
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['SupplierListDetails']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}

	function getCustomer($client_id){
		$data= array();
		$query="SELECT * FROM erp_client where id = $client_id";
		$count=mysqli_query($this->conn, $query);
		$row=mysqli_fetch_assoc($count);
		$customer_name = $row['client_name'];
		return $customer_name;
	}


	function CustomerList()
	{
		$data= array();
		$query="SELECT * FROM erp_contacts where vendor_id = 0";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do { 						
						$data['contactId']=$row['id'];
						$cus = $this->getCustomer($row['client_id']);
						$data['customer']=$cus.' - '.$row['first_name'];
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['CustomerListDetails']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}



	 function employeeDetailsAll($userId)
    {
        $data=array();
        $perDet = $this->persnlDetails($userId,null,null);
        $companyId =  $perDet['persnlDetails']['company_id'];
  			
		$query="SELECT emp.employee_id, emp.emp_number,emp.work_station,CONCAT(emp.emp_firstname,' ',emp.emp_lastname) AS emp_name,usr.id as userId,emp.business_area FROM hs_hr_employee as emp LEFT JOIN erp_user as usr ON usr.emp_number = emp.emp_number AND (usr.deleted=0 AND usr.status=1) WHERE (emp.business_area=$companyId AND emp.termination_id IS NULL) AND emp.termination_id IS NULL";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				
					do { 						
						
					
						$data['emp_number'] = $row['emp_number'];
						$data['userId'] = $row['userId'];
						$data['emp_name'] = $row['emp_name'];
						$data['department'] = $row['work_station'];
						$data['companyId'] = $row['business_area'];
						if(!empty($row['work_station'])){
						$data['department_name'] = $this->getDepartment($row['work_station']);
						}else{
						$data['department_name'] = '';

						}
						/*$data['emp_nick_name'] = $row['emp_nick_name'];
						$data['emp_fathername'] = $row['emp_fathername'];
						$data['emp_mothername'] = $row['emp_mothername'];
						$data['emp_smoker'] = $row['emp_smoker'];
						$data['ethnic_race_code'] = $row['ethnic_race_code'];	
						$data['emp_birthday'] = $row['emp_birthday'];
						$data['nation_code'] = $row['nation_code'];
						$data['emp_gender'] = $row['emp_gender'];
						$data['emp_marital_status'] = $row['emp_marital_status'];
						$data['emp_ssn_num'] = $row['emp_ssn_num'];
						$data['emp_sin_num'] = $row['emp_sin_num'];
						$data['emp_other_id'] = $row['emp_other_id'];
						$data['emp_pancard_id'] = $row['emp_pancard_id'];
						$data['emp_uan_num'] = $row['emp_uan_num'];
						$data['emp_pf_num'] = $row['emp_pf_num'];
						$data['emp_dri_lice_num'] = $row['emp_dri_lice_num'];
						$data['emp_dri_lice_exp_date'] = $row['emp_dri_lice_exp_date'];
						$data['emp_military_service'] = $row['emp_military_service'];
						$data['blood_group'] = $row['blood_group'];
						$data['emp_hobbies'] = $row['emp_hobbies'];
						$data['emp_status'] = $row['emp_status'];
						$data['job_title_code'] = $row['job_title_code'];
						$data['eeo_cat_code'] = $row['eeo_cat_code'];
						$data['work_station'] = $row['work_station'];
						$data['department'] = $row['department'];
						$data['emp_street1'] = $row['emp_street1'];
						$data['emp_street2'] = $row['emp_street2'];
						$data['city_code'] = $row['city_code'];
						$data['coun_code'] = $row['coun_code'];
						$data['provin_code'] = $row['provin_code'];
						$data['emp_zipcode'] = $row['emp_zipcode'];
						$data['emp_hm_telephone'] = $row['emp_hm_telephone'];
						$data['emp_mobile'] = $row['emp_mobile'];
						$data['emp_work_telephone'] = $row['emp_work_telephone'];
						$data['emp_work_email'] = $row['emp_work_email'];
						$data['sal_grd_code'] = $row['sal_grd_code'];
						$data['joined_date'] = $row['joined_date'];
						$data['emp_oth_email'] = $row['emp_oth_email'];
						$data['termination_id'] = $row['termination_id'];
						$data['emp_ctc'] = $row['emp_ctc'];
						$data['emp_cost_of_company'] = $row['emp_cost_of_company'];
						$data['emp_gross_salary'] = $row['emp_gross_salary'];
						$data['custom1'] = $row['custom1'];
						$data['custom2'] = $row['custom2'];
						$data['custom3'] = $row['custom3'];
						$data['custom4'] = $row['custom4'];
						$data['custom5'] = $row['custom5'];
						$data['custom6'] = $row['custom6'];
						$data['custom7'] = $row['custom7'];
						$data['custom8'] = $row['custom8'];
						$data['custom9'] = $row['custom9'];
						$data['custom10'] = $row['custom10'];
						$data['plant_id'] = $row['plant_id'];*/

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['employeeDetailsAll']=$data1;
					$data['status'] = 1;
					
		}else{
			$data['status'] = 0;
		}
		return $data;
    }


    function femaleEmployeeDetailsAll($userId)
    {
        $data=array();

        $perDet = $this->persnlDetails($userId,null,null);
        $companyId =  $perDet['persnlDetails']['company_id'];
  			
		$query="SELECT emp.employee_id, emp.emp_number,emp.emp_gender,emp.work_station,CONCAT(emp.emp_firstname,' ',emp.emp_lastname) AS emp_name,usr.id as userId,emp.business_area FROM hs_hr_employee as emp LEFT JOIN erp_user as usr ON usr.emp_number = emp.emp_number AND (usr.deleted=0 AND usr.status=1) WHERE (emp.business_area=$companyId AND emp.termination_id IS NULL) AND emp.emp_gender=2";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);


				
					do { 						
						
					
						$data['emp_number'] = $row['emp_number'];
						$data['userId'] = $row['userId'];
						$data['emp_name'] = $row['emp_name'];
						$data['department'] = $row['work_station'];
						$data['companyId'] = $row['business_area'];
						if(!empty($row['work_station'])){
						$data['department_name'] = $this->getDepartment($row['work_station']);
						}else{
						$data['department_name'] = '';

						}
						/*$data['emp_nick_name'] = $row['emp_nick_name'];
						$data['emp_fathername'] = $row['emp_fathername'];
						$data['emp_mothername'] = $row['emp_mothername'];
						$data['emp_smoker'] = $row['emp_smoker'];
						$data['ethnic_race_code'] = $row['ethnic_race_code'];	
						$data['emp_birthday'] = $row['emp_birthday'];
						$data['nation_code'] = $row['nation_code'];
						$data['emp_gender'] = $row['emp_gender'];
						$data['emp_marital_status'] = $row['emp_marital_status'];
						$data['emp_ssn_num'] = $row['emp_ssn_num'];
						$data['emp_sin_num'] = $row['emp_sin_num'];
						$data['emp_other_id'] = $row['emp_other_id'];
						$data['emp_pancard_id'] = $row['emp_pancard_id'];
						$data['emp_uan_num'] = $row['emp_uan_num'];
						$data['emp_pf_num'] = $row['emp_pf_num'];
						$data['emp_dri_lice_num'] = $row['emp_dri_lice_num'];
						$data['emp_dri_lice_exp_date'] = $row['emp_dri_lice_exp_date'];
						$data['emp_military_service'] = $row['emp_military_service'];
						$data['blood_group'] = $row['blood_group'];
						$data['emp_hobbies'] = $row['emp_hobbies'];
						$data['emp_status'] = $row['emp_status'];
						$data['job_title_code'] = $row['job_title_code'];
						$data['eeo_cat_code'] = $row['eeo_cat_code'];
						$data['work_station'] = $row['work_station'];
						$data['department'] = $row['department'];
						$data['emp_street1'] = $row['emp_street1'];
						$data['emp_street2'] = $row['emp_street2'];
						$data['city_code'] = $row['city_code'];
						$data['coun_code'] = $row['coun_code'];
						$data['provin_code'] = $row['provin_code'];
						$data['emp_zipcode'] = $row['emp_zipcode'];
						$data['emp_hm_telephone'] = $row['emp_hm_telephone'];
						$data['emp_mobile'] = $row['emp_mobile'];
						$data['emp_work_telephone'] = $row['emp_work_telephone'];
						$data['emp_work_email'] = $row['emp_work_email'];
						$data['sal_grd_code'] = $row['sal_grd_code'];
						$data['joined_date'] = $row['joined_date'];
						$data['emp_oth_email'] = $row['emp_oth_email'];
						$data['termination_id'] = $row['termination_id'];
						$data['emp_ctc'] = $row['emp_ctc'];
						$data['emp_cost_of_company'] = $row['emp_cost_of_company'];
						$data['emp_gross_salary'] = $row['emp_gross_salary'];
						$data['custom1'] = $row['custom1'];
						$data['custom2'] = $row['custom2'];
						$data['custom3'] = $row['custom3'];
						$data['custom4'] = $row['custom4'];
						$data['custom5'] = $row['custom5'];
						$data['custom6'] = $row['custom6'];
						$data['custom7'] = $row['custom7'];
						$data['custom8'] = $row['custom8'];
						$data['custom9'] = $row['custom9'];
						$data['custom10'] = $row['custom10'];
						$data['plant_id'] = $row['plant_id'];*/

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['employeeDetailsAll']=$data1;
					$data['status'] = 1;
					
		}else{
			$data['status'] = 0;
		}
		return $data;
    } 


    function employeeDetailsByRoleAll($user_id)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);
		$empnumber = $userDetails['empNumber'];
		$roleId = $userDetails['id'];

		// echo $roleId;die();
        $data=array();
  			
		$query="SELECT h.erep_sub_emp_number as emp_number FROM hs_hr_emp_reportto h WHERE (h.erep_sup_emp_number = $empnumber)";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
					do{ 
						$data1[] = $this->supervisorEmployeeDetails($row['emp_number']);
					}while($row = mysqli_fetch_assoc($count));
						$data['emplist']=$data1;
						$data['status'] = 1;
		}else if($roleId == 17 || $roleId == 6){
				$query1="SELECT emp.employee_id, emp.emp_number,emp.work_station,CONCAT(emp.emp_firstname,' ',emp.emp_lastname) AS emp_name,usr.id as userId FROM hs_hr_employee emp LEFT JOIN erp_user usr ON usr.emp_number = emp.emp_number";
				$count1=mysqli_query($this->conn, $query1);

				if(mysqli_num_rows($count1) > 0)
				{
					$row1=mysqli_fetch_assoc($count1);
					do { 
						if($empnumber != $row1['emp_number']){
						$data['emp_number'] = $row1['emp_number'];
						$data['employee_id'] = $row1['employee_id'];
						$data['emp_name'] = $row1['emp_name'];

						$data1[] = $data;

						}						
					}while($row1 = mysqli_fetch_assoc($count1));
					$data['emplist']=$data1;
					$data['status'] = 1;

				}else{
					$data['emplist']=array();
					$data['status'] = 0;
				}
		}else{
					$data['emplist']=array();
					$data['status'] = 0;
		}

		return $data;
    }



    function bookVehicle($user_id,$booked_by_id,$response_id,$booked_for_id,$booked_for_value,$origin,$destination,$pick_up_point,$latitude,$longitude,
				$reason,$from_date,$from_time,$to_date,$to_time,$passengers_id,$round_trip,$status_id,$submitted_on)
    {
        $data=array();

    
		$query="SELECT ui.last_id FROM hs_hr_unique_id ui WHERE ui.table_name = 'erp_book_vehicle' AND ui.field_name='booked_by_id'";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);

			$data['last_id']=$row['last_id'];
			$jobinc = $row['last_id']+1;

			$sql = "UPDATE hs_hr_unique_id SET last_id = ".$jobinc." WHERE table_name = 'erp_book_vehicle' AND field_name='booked_by_id'";
			if(mysqli_query($this->conn, $sql)){

				$query="SELECT ui.last_id FROM hs_hr_unique_id ui WHERE ui.table_name = 'erp_book_vehicle' AND ui.field_name='booked_by_id'";
				$count=mysqli_query($this->conn, $query);
					if(mysqli_num_rows($count) > 0){
						$row=mysqli_fetch_assoc($count);
						$prefix = date('Ymd');
						$NewBookId = $row['last_id'];
						/*echo $NewBookId;
						exit();*/
						//$bkByIdNew = $prefix . str_pad($NewJobId, 3, "0", STR_PAD_LEFT);
					}

				$source = 1;
	   			// Prepare an insert statement
				$sql = "INSERT INTO erp_book_vehicle (booked_by_id,response_id,booked_for_id,booked_for_value,origin,destination,pick_up_point,latitude,longitude,
				reason,from_date,from_time,to_date,to_time,passengers_id,round_trip,status_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				 
				/* echo $reportedOn." , ".$submitted_on;
				 exit();*/
				if($stmt = mysqli_prepare($this->conn, $sql)){
				    // Bind variables to the prepared statement as parameters
				     mysqli_stmt_bind_param($stmt, "iiisssssssssssiii" ,$booked_by_id,$response_id,$booked_for_id,$booked_for_value,$origin,$destination,$pick_up_point,$latitude,$longitude,$reason,$from_date,$from_time,$to_date,$to_time,$passengers_id,$round_trip,$status_id);
				    			   
					    // Attempt to execute the prepared statement
					    if(mysqli_stmt_execute($stmt)){

				    		$query="SELECT MAX(id) AS bookVehicle_id  FROM erp_book_vehicle";
							$count=mysqli_query($this->conn, $query);
								
								if(mysqli_num_rows($count) > 0)	{
											$row=mysqli_fetch_assoc($count);
											$data['bookVehicle_id'] = $row['bookVehicle_id'];
											$bookVehicle_id = $data['bookVehicle_id'];
											$userDetails = $this->getUserRoleByUserId($user_id);
											$empNumber = $userDetails['empNumber'];
											$empresult=$this->employeeDetails($empNumber);
											$performed_by_name = $empresult['emp_name'];
						
											//echo $bookVehicle_id;
											$result=$this->bkVehlogAdd($user_id,$bookVehicle_id,' ',$status_id,$booked_by_id,$performed_by_name,$submitted_on);
											/*$sql = "UPDATE erp_ticket_attachment SET ticket_id = ".$ticket_id." WHERE ticket_id = ".$attachmentId;
											mysqli_query($this->conn, $sql);*/
								}

				        $data['bookVehicle_id'] = $data['bookVehicle_id'];
				        $data['status']=1;
					    } else{
					        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
					        $data['status']=0;
					    }
				} else{
				    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
				    $data['status']=0;
					}	

			} else {
					    //echo "ERROR: Could not able to execute $sql. " . mysqli_error($this->conn);
					     $data['status']=0;
			}
		} else{
				    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
				    $data['status']=0;
		}
		
        return $data;
    }


   function bkVehlogAdd($user_id,$bookVehicle_id,$notes,$status_id,$performed_by_id,$performed_by_name,$submitted_on)
    {

    		$minquery = "SELECT * FROM erp_book_vehicle_action_log WHERE id IN (SELECT MIN(id) FROM erp_book_vehicle_action_log WHERE book_vehicle_id = $bookVehicle_id)";

		$rowcount2 = mysqli_query($this->conn, $minquery);

		
        $data=array();

        $userDetails = $this->getUserRoleByUserId($user_id);
        $empNumber = $userDetails['empNumber'];
		$roleId = $userDetails['id'];


    	//$source = 1;     
     	$sql = "INSERT INTO erp_book_vehicle_action_log (book_vehicle_id,notes,status_id,performed_by_id,performed_by_name,submitted_on) VALUES (?,?,?,?,?,?)";
			 
			if($stmt = mysqli_prepare($this->conn, $sql)){
				
			    // Bind variables to the prepared statement as parameters
			     mysqli_stmt_bind_param($stmt, "isiiss",$bookVehicle_id,$notes,$status_id,$performed_by_id,$performed_by_name,$submitted_on);
			    			   
			    // Attempt to execute the prepared statement
						if(mysqli_stmt_execute($stmt)){
			       
												if($status_id == 1)
												 {

												$updatesql = "UPDATE erp_book_vehicle SET status_id = 1 WHERE id = $bookVehicle_id";
													if($result2 = mysqli_query($this->conn, $updatesql)){
													
														$data['log'] = "Vehicle Booking status changed to Pending for Approval";
														$data['status']=1;
													
													
													}
														else{
														//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
														$data['status']=0;
														}

												}

												else if($status_id == 2)
												 {

														/*echo $status_id;
														echo $ticket_id;
														exit;*/
												$updatesql = "UPDATE erp_book_vehicle SET status_id = 2 WHERE id = $bookVehicle_id";
													if($result2 = mysqli_query($this->conn, $updatesql)){
													/*$data['session_token'] = $token;*/
													$data['log'] = "Vehicle Booking Status Changed to approved";
													$data['status']=1;
													}
														else{
														//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
														$data['status']=0;
														}

												}

												else if($status_id == 3)
												 {

														/*echo $status_id;
														echo $ticket_id;
														exit;*/
												$updatesql = "UPDATE erp_book_vehicle SET status_id = 3 WHERE id = $bookVehicle_id";
													if($result2 = mysqli_query($this->conn, $updatesql)){
													/*$data['session_token'] = $token;*/
													$data['log'] = "Vehicle Booking Status Changed to Rejected";
													$data['status']=1;
													}
														else{
														//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
														$data['status']=0;
														}

												}

												else if($status_id == 4)
												 {

														/*echo $status_id;
														echo $ticket_id;
														exit;*/
												$updatesql = "UPDATE erp_book_vehicle SET status_id = 4 WHERE id = $bookVehicle_id";
													if($result2 = mysqli_query($this->conn, $updatesql)){
													/*$data['session_token'] = $token;*/
													$data['log'] = "Vehicle Booking Status Changed to Assigned";
													$data['status']=1;
													}
														else{
														//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
														$data['status']=0;
														}

												}

											else if($status_id == 5)
											 {

													/*echo $status_id;
													echo $ticket_id;
													exit;*/
											$updatesql = "UPDATE erp_book_vehicle SET status_id = 5 WHERE id = $bookVehicle_id";
												if($result2 = mysqli_query($this->conn, $updatesql)){
												/*$data['session_token'] = $token;*/
												$data['log'] = "Vehicle Booking Status Changed to Completed";
												//$data['forward_to']=1;
												$data['status']=1;
												}
													else{
													//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
													$data['status']=0;
													}

											}

											else if($status_id == 6)
											 {

													/*echo $status_id;
													echo $ticket_id;
													exit;*/
											$updatesql = "UPDATE erp_book_vehicle SET status_id = 6 WHERE id = $bookVehicle_id";
												if($result2 = mysqli_query($this->conn, $updatesql)){
												/*$data['session_token'] = $token;*/
												$data['log'] = "Vehicle Booking Status changed to Cancel";
												$data['status']=1;
												}
													else{
													//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
													$data['status']=0;
													}

											}

											else if($status_id == 7)
											 {


																	$updatesql = "UPDATE erp_book_vehicle SET status_id = 7 WHERE id = $bookVehicle_id";
															if($result2 = mysqli_query($this->conn, $updatesql)){
															/*$data['session_token'] = $token;*/
															$data['log'] = "Vehicle Booking Status Changed to BreakDown";
															$data['status']=1;
																}
																else{
																//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
																$data['status']=0;
																}
													

											}


										else if($status_id == 8)
										 {


																$updatesql = "UPDATE erp_book_vehicle SET status_id = 8 WHERE id = $bookVehicle_id";
														if($result2 = mysqli_query($this->conn, $updatesql)){
														/*$data['session_token'] = $token;*/
														$data['log'] = "Vehicle Booking Status Changed to Repaired";
														$data['status']=1;
															}
															else{
															//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
															$data['status']=0;
															}
												

										}


										else if($status_id == 9)
										 {


																$updatesql = "UPDATE erp_book_vehicle SET status_id = 9 WHERE id = $bookVehicle_id";
														if($result2 = mysqli_query($this->conn, $updatesql)){
														/*$data['session_token'] = $token;*/
														$data['log'] = "Vehicle Booking Status Changed to New";
														$data['status']=1;
															}
															else{
															//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
															$data['status']=0;
															}
												

										}
     				

     				}

     				else
     				{
     					$data['log'] = "Vehicle Booking Created Successfully";
			        	$data['status']=1;

     				}

			        
			    } else{
			        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
			        $data['status']=0;
			    }
			
     
     
        return $data;
    

    }	

  
    	function empSubListIds($userIdPass)
    {
        $data=array();
  			
		$userDetails = $this->getUserRoleByUserId($userIdPass);
		$empNumber = $userDetails['empNumber'];

			$query="SELECT erep_sub_emp_number as emp_number FROM `hs_hr_emp_reportto` WHERE erep_sup_emp_number = $empNumber";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
					do{ 
						$data1[] = $row['emp_number'];
					}while($row = mysqli_fetch_assoc($count));
						$data['emplist']=$data1;
						$data['status'] = 1;
		}
		return $data;
    }
		
    function bkVhclNotfctnDtls($userId)
	{
		$data= array();
		// SELECT * FROM erp_book_vehicle WHERE booked_by_id IN (23,24,39);

		 $empresult=$this->empSubListIds($userId);
		/*echo "<pre>";
		 print_r($empresult);
		 exit();*/

		 $userDetails = $this->getUserRoleByUserId($userId);
		$empNumber = $userDetails['empNumber'];
		$userRoleId = $userDetails['id'];

	        
	        //$emp_number = $userDetails['empNumber'];
		//echo $emp_number;
		

		 if($userRoleId == 23)
		 {


		 			$queryNotifyTrans = "SELECT veh.id as id,veh.booked_for_value,veh.origin,veh.destination, veh.reason,veh.from_date, veh.to_date, veh.from_time,veh.to_time,st.name as status,CONCAT(emp.emp_firstname,' ',emp.emp_lastname) as booked_by FROM erp_book_vehicle veh LEFT JOIN erp_book_vehicle_status st ON st.id = veh.status_id LEFT JOIN hs_hr_employee emp ON emp.emp_number = veh.booked_by_id WHERE veh.status_id = 2";

		 				$countqueryNotifyTrans =mysqli_query($this->conn, $queryNotifyTrans);
		if(mysqli_num_rows($countqueryNotifyTrans) > 0)
		{
						$row=mysqli_fetch_assoc($countqueryNotifyTrans);
					do{ 
						$data['id']=$row['id'];
						$data['booked_by']=$row['booked_by'];
						$data['booked_for']=$row['booked_for_value'];
						$data['origin']=$row['origin'];
						$data['destination']=$row['destination'];	
						
						$data['pick_up_date']=$row['from_date'];
						$data['pick_up_time']=$row['from_time'];
						$data['to_date']=$row['to_date'];
						$data['to_time']=$row['to_time'];
						$data['status']=$row['status'];

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($countqueryNotifyTrans)); 					
						$data['bkVhclNotfctnDtls']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}

		 }
		 if($userRoleId == 24)
		 {


		 			$queryNotifyTrans = "SELECT DISTINCT veh.id as id,veh.booked_by_id,CONCAT(emp.emp_firstname,' ',emp.emp_lastname) as booked_by, veh.booked_for_id,veh.booked_for_value, veh.origin,veh.destination, veh.reason,veh.from_date, veh.to_date, veh.from_time,veh.to_time,st.id as status_id,st.name as status,asv.driver_id,
					CONCAT(dremp.emp_firstname,' ',dremp.emp_lastname) as driver_name
					FROM erp_book_vehicle veh
					LEFT JOIN erp_book_vehicle_status st ON st.id = veh.status_id
					LEFT JOIN hs_hr_employee emp ON emp.emp_number = veh.booked_by_id
					LEFT JOIN erp_assign_vehicle asv ON asv.booking_id = veh.id
					LEFT JOIN erp_vehicle vh ON vh.id = asv.vehicle_id
					LEFT JOIN hs_hr_employee dremp ON dremp.emp_number = asv.driver_id
					LEFT JOIN hs_hr_employee bkemp ON bkemp.emp_number = veh.booked_by_id
					WHERE asv.driver_id = $empNumber AND veh.status_id != 5";

		 				$countqueryNotifyTrans =mysqli_query($this->conn, $queryNotifyTrans);
		if(mysqli_num_rows($countqueryNotifyTrans) > 0)
		{
						$row=mysqli_fetch_assoc($countqueryNotifyTrans);
					do{ 
						$data['id']=$row['id'];
						$data['booked_by']=$row['booked_by'];
						$data['booked_for']=$row['booked_for_value'];
						$data['origin']=$row['origin'];
						$data['destination']=$row['destination'];	
						
						$data['pick_up_date']=$row['from_date'];
						$data['pick_up_time']=$row['from_time'];
						$data['to_date']=$row['to_date'];
						$data['to_time']=$row['to_time'];
						$data['status']=$row['status'];

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($countqueryNotifyTrans)); 					
						$data['bkVhclNotfctnDtls']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}

		 }
		 else
		 {


		 		for ($i=0; $i < sizeof($empresult['emplist']) ; $i++) { 
	        	$empList[] = $empresult['emplist'][$i];
	        	//to convert Array into string the following implode method is used
	        	$empLists = implode(',', $empList);
	        }

		 		$query="SELECT veh.id as id,veh.booked_for_value,veh.origin,veh.destination, veh.reason,veh.from_date, veh.to_date, veh.from_time,veh.to_time,st.name as status,CONCAT(emp.emp_firstname,' ',emp.emp_lastname) as booked_by FROM erp_book_vehicle veh LEFT JOIN erp_book_vehicle_status st ON st.id = veh.status_id LEFT JOIN hs_hr_employee emp ON emp.emp_number = veh.booked_by_id WHERE veh.booked_by_id IN ($empLists) AND veh.status_id = 1";
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
						$row=mysqli_fetch_assoc($count);
					do{ 
						$data['id']=$row['id'];
						$data['booked_by']=$row['booked_by'];
						$data['booked_for']=$row['booked_for_value'];
						$data['origin']=$row['origin'];
						$data['destination']=$row['destination'];	
						
						$data['pick_up_date']=$row['from_date'];
						$data['pick_up_time']=$row['from_time'];
						$data['to_date']=$row['to_date'];
						$data['to_time']=$row['to_time'];
						$data['status']=$row['status'];

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 					
						$data['bkVhclNotfctnDtls']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}


		 }

				
		return $data;    
	}



	function bookVehicleDetails($userId)
	{
		$data= array();
		// SELECT * FROM erp_book_vehicle WHERE booked_by_id IN (23,24,39);

		
				$query="SELECT veh1.id,veh1.booked_for_value,veh1.origin,veh1.destination,veh1.from_date,veh1.from_time,veh1.to_date,veh1.to_time,st1.name as status FROM erp_book_vehicle veh1
							LEFT JOIN erp_book_vehicle_status st1 ON st1.id = veh1.status_id WHERE veh1.is_deleted = 0 ORDER BY id DESC";
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
						$row=mysqli_fetch_assoc($count);
					do{ 
						$data['id']=$row['id'];
						$data['booked_for']=$row['booked_for_value'];
						$data['origin']=$row['origin'];
						$data['destination']=$row['destination'];	
						//$data['reason']=$row['reason'];
						$data['pick_up_date']=$row['from_date'];
						$data['pick_up_time']=$row['from_time'];
						$data['to_date']=$row['to_date'];
						$data['to_time']=$row['to_time'];
						$data['status']=$row['status'];
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 					
						$data['bookVehicleDetails']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}




function approveOrReject($userId,$bookVehicle_id,$status_id,$notes,$submitted_on)
	{
		$data= array();
		// SELECT * FROM erp_book_vehicle WHERE booked_by_id IN (23,24,39);

			$performed_by_id = $userId;

			$userDetails = $this->getUserRoleByUserId($userId);
		$empNumber = $userDetails['empNumber'];
		$empresult=$this->employeeDetails($empNumber);
		$performed_by_name = $empresult['emp_name'];
		
				$updatesql = "UPDATE erp_book_vehicle SET status_id = $status_id WHERE id = $bookVehicle_id";

												
		//$count=mysqli_query($this->conn, $updatesql);
		if($count = mysqli_query($this->conn, $updatesql))
		{
						//$row=mysqli_fetch_assoc($count);

						

						$sql = "INSERT INTO erp_book_vehicle_action_log (book_vehicle_id,notes,action_status_id,performed_by_id,performed_by_name,submitted_on) VALUES (?,?,?,?,?,?)";
			 
			if($stmt = mysqli_prepare($this->conn, $sql)){
				
			    // Bind variables to the prepared statement as parameters
			     mysqli_stmt_bind_param($stmt, "isiiss",$bookVehicle_id,$notes,$status_id,$performed_by_id,$performed_by_name,$submitted_on);


			     if(mysqli_stmt_execute($stmt)){
					    	

			     				$data['status_id']= $status_id;
						$data['bookVehicle_id']= $bookVehicle_id;

									$data1[] = $data;

									$data['approveOrReject']=$data1;
									$data['status']=1;

					    } else{
					        $data['status']=0;
					    }


			 }
					else{
					    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
					    $data['status']=0;
					}	
						
								
											
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}



	function bkVhclDetlsById($userId,$bookVehicle_id)
	{
		$data= array();
		// SELECT * FROM erp_book_vehicle WHERE booked_by_id IN (23,24,39);

		
				$query="SELECT bk.id as book_vehicle_id,bk.booked_by_id as booked_by,CONCAT(emp.emp_firstname,' ',emp.emp_lastname) as emp_name,bk.booked_for_value as booked_for,bk.origin,bk.destination,bk.pick_up_point,bk.from_date,bk.to_date, bk.from_time,bk.to_time,vst.name as status,bk.reason,bk.passengers_id,bk.s_latitude,bk.s_longitude,bk.d_latitude,bk.d_longitude,asv.vehicle_id as vehicle_id
					FROM erp_book_vehicle bk
					LEFT JOIN hs_hr_employee emp ON bk.booked_by_id = emp.emp_number
					LEFT JOIN erp_book_vehicle_status vst ON vst.id = bk.status_id
                    LEFT JOIN erp_assign_vehicle asv ON bk.id = asv.booking_id
					WHERE bk.id = $bookVehicle_id";
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
						$row=mysqli_fetch_assoc($count);

						$getTripQuery = "SELECT * FROM `erp_vehicle_trip` as t WHERE t.book_vehicle_id=".$bookVehicle_id;
						$getTripCount=mysqli_query($this->conn, $getTripQuery);
						$tripRow=mysqli_fetch_assoc($getTripCount);
					do{ 
						$data['book_vehicle_id']=$row['book_vehicle_id'];
						$data['vehicle_id']=$row['vehicle_id'];
						$data['reason']=$row['reason'];
						$data['booked_by']=$row['booked_by'];
						$data['emp_name']=$row['emp_name'];
						$data['booked_for']=$row['booked_for'];
						$data['origin']=$row['origin'];
						$data['destination']=$row['destination'];
						$data['s_latitude']=$row['s_latitude'];
						$data['s_longitude']=$row['s_longitude'];
						$data['d_latitude']=$row['d_latitude'];
						$data['d_longitude']=$row['d_longitude'];
						$data['from_date']=$row['from_date'];
					
						$data['pick_up_point']=$row['pick_up_point'];
						$data['from_time']=$row['from_time'];
						$data['to_date']=$row['to_date'];
						$data['to_time']=$row['to_time'];
						$data['no_of_passengers']=$row['passengers_id'];
						$data['status']=$row['status'];
						if(!empty($tripRow['id'])){
						$data['trip_id']=$tripRow['id'];
						}else{
						$data['trip_id']='';
						}
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 					
						$data['bkVhclDetlsById']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}



	function vehicleList($userId,$bookVehicle_id)
	{
		$data= array();
		// SELECT * FROM erp_book_vehicle WHERE booked_by_id IN (23,24,39);

			$vehicleDetails = $this->bkVhclDetlsById($userId,$bookVehicle_id);

			$noOfPassengers = $vehicleDetails['no_of_passengers'];

				$query="SELECT veh.id,veh.name,veh.vehicle_number,vtyp.name as vehicle_type,trans.name as transmission,mxcap.name as capacity FROM erp_vehicle veh
					LEFT JOIN erp_vehicle_type vtyp ON vtyp.id = veh.vehicle_type_id
					LEFT JOIN erp_transmission trans ON trans.id = veh.transmission_id
					LEFT JOIN erp_maximum_capacity mxcap ON mxcap.id = veh.maximum_capacity
					WHERE veh.trip_status = 0";
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
						$row=mysqli_fetch_assoc($count);
					do{ 
						$data['id']=$row['id'];
						$data['name']=$row['name'];
						$data['vehicle_number']=$row['vehicle_number'];
						$data['vehicle_type']=$row['vehicle_type'];	
						$data['transmission']=$row['transmission'];
						$data['capacity']=$row['capacity'];
						$data['no_of_pasengers'] = $noOfPassengers;
						
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 					
						$data['vehicleList']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}


function vehicleTrack($user_id,$status_id,$book_vehicle_id,$vehicle_id,$s_latitude,$s_longitude,$d_latitude,$d_longitude,$c_latitude,$c_longitude)
{

		 $data=array();

		$query="SELECT ui.last_id FROM hs_hr_unique_id ui WHERE ui.table_name = 'erp_vehicle_trip' AND ui.field_name='id'";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{

			$row=mysqli_fetch_assoc($count);

			$last_id=$row['last_id'];
			// echo $last_id;
			$data['last_id']=$row['last_id'];

			$trpinc = $row['last_id']+1;

			$sql = "UPDATE hs_hr_unique_id SET last_id = ".$trpinc." WHERE table_name = 'erp_vehicle_trip' AND field_name='id'";

			if(mysqli_query($this->conn, $sql)){

				// $vehicleQuery = "SELECT * FROM `erp_vehicle_trip` WHERE book_vehicle_id=$book_vehicle_id";
				// $vehicletripcount=mysqli_query($this->conn, $vehicleQuery);


				// if(mysqli_num_rows($vehicletripcount) > 0){
				// 	$updateVehiclesql = "UPDATE erp_vehicle_trip SET status_id = $status_id  WHERE book_vehicle_id = $book_vehicle_id";
				// 	mysqli_query($this->conn, $updateVehiclesql);

				// 	$vehiclerow=mysqli_fetch_assoc($vehicletripcount);

				// 	// print_r($vehiclerow);die();
				// 	$data['trip_id'] =  $vehiclerow['id'];
				// 	$trip_id = $vehiclerow['id'];

				// 	$result=$this->vehicleTrackLog($user_id,$trip_id,$status_id,$c_latitude,$c_latitude,$book_vehicle_id);

				// 	$data['trip_id'] = $data['trip_id'];
				// 	$data['status_id']=$status_id;
				// 	$data['status']=1;

				// }else{

						//echo "if";
		   			// Prepare an insert statement


				$checkTripQuery="SELECT * FROM `erp_vehicle_trip` AS t WHERE t.book_vehicle_id=$book_vehicle_id AND t.vehicle_id=$vehicle_id";
				$tripCheck=mysqli_query($this->conn, $checkTripQuery);

				if(mysqli_num_rows($tripCheck) > 0)
				{
					$tripCheckUpdate = "UPDATE erp_vehicle_trip AS t SET t.status_id = ".$status_id." WHERE t.book_vehicle_id=$book_vehicle_id AND t.vehicle_id=$vehicle_id";

					$tripresult = mysqli_query($this->conn, $tripCheckUpdate);

					if($tripresult){

						    	//echo "if stmt";

					    		
									
									if(mysqli_num_rows($tripCheck) > 0)	{

										//echo "if count";
												$row=mysqli_fetch_assoc($tripCheck);
												$data['trip_id'] = $row['trip_id'];
												$trip_id = $data['trip_id'];
												$data['book_vehicle_id'] = $row['book_vehicle_id'];
												$book_vehicle_id = $data['book_vehicle_id'];
												
												$result=$this->vehicleTrackLog($user_id,$trip_id,$status_id,$c_latitude,$c_longitude,$book_vehicle_id);
									}

					        $data['trip_id'] = $data['trip_id'];
					        $data['status']=1;
					        $data['status_id']=$status_id;
						    } else{
						    	//echo "else";
						        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
						        $data['status']=0;
						        $data['status_id']=$status_id;
						    }

				}else{

					$sql = "INSERT INTO erp_vehicle_trip (status_id,book_vehicle_id,vehicle_id,s_latitude,s_longitude,d_latitude,d_longitude,c_latitude,c_longitude) VALUES (?,?,?,?,?,?,?,?,?)";
					if($stmt = mysqli_prepare($this->conn, $sql)){

						//echo "stmt";
					    // Bind variables to the prepared statement as parameters
					      mysqli_stmt_bind_param($stmt, "iiissssss" ,$status_id,$book_vehicle_id,$vehicle_id,$s_latitude,$s_longitude,$d_latitude,$d_longitude,$c_latitude,$c_longitude);
					    			   
						    // Attempt to execute the prepared statement
						    if(mysqli_stmt_execute($stmt)){

						    	//echo "if stmt";

					    		$query="SELECT MAX(id) AS trip_id,book_vehicle_id  FROM erp_vehicle_trip";
								$count=mysqli_query($this->conn, $query);
									
									if(mysqli_num_rows($count) > 0)	{

										//echo "if count";
												$row=mysqli_fetch_assoc($count);
												$data['trip_id'] = $row['trip_id'];
												$trip_id = $data['trip_id'];
												$data['book_vehicle_id'] = $row['book_vehicle_id'];
												$book_vehicle_id = $data['book_vehicle_id'];
												
												$result=$this->vehicleTrackLog($user_id,$trip_id,$status_id,$c_latitude,$c_longitude,$book_vehicle_id);
									}

					        $data['trip_id'] = $data['trip_id'];
					        $data['status']=1;
					        $data['status_id']=$status_id;
						    } else{
						    	//echo "else";
						        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
						        $data['status']=0;
						        $data['status_id']=$status_id;
						    }
					} else{
							//echo "2 else"; 
					    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
					    $data['status']=0;
					    $data['status_id']=$status_id;
					}	
				}
					 
					
				// }


			} else {
				//echo "3 else"; 
					    //echo "ERROR: Could not able to execute $sql. " . mysqli_error($this->conn);
					     $data['status']=0;
					     $data['status_id']=$status_id;
			}
		} else{		//echo "4 else"; 
				    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
				    $data['status']=0;
				    $data['status_id']=$status_id;
		}
		
	
		
        return $data;
	

}



function vehicleTrackLog($user_id,$trip_id,$status_id,$latitude,$longitude,$book_vehicle_id)
{

	 $data=array();


	 	if($status_id == 5)
			    	{
			    		/*echo $status_id;
			    		exit();*/

							$checkTripQuery="SELECT * FROM `erp_vehicle_trip` AS t WHERE t.id=$trip_id AND t.book_vehicle_id=$book_vehicle_id";
							$tripCheck=mysqli_query($this->conn, $checkTripQuery);

							if(mysqli_num_rows($tripCheck) > 0)
							{
									//echo "sql";

									$sql = "INSERT INTO erp_vehicle_trip_log (trip_id,status_id,latitude,longitude) VALUES (?,?,?,?)";




							if($stmt = mysqli_prepare($this->conn, $sql)){

								 mysqli_stmt_bind_param($stmt, "iiss" ,$trip_id,$status_id,$latitude,$longitude);

								 if(mysqli_stmt_execute($stmt)){


								 	$updatesql1 ="UPDATE erp_vehicle_trip SET status_id = $status_id,c_latitude = $latitude,c_longitude = $longitude  WHERE book_vehicle_id = $book_vehicle_id AND id=$trip_id";

								 	//echo $updatesql1;

							if($result3 = mysqli_query($this->conn, $updatesql1)){
								//echo "success";
								$updatesql2 ="UPDATE erp_book_vehicle SET status_id = $status_id WHERE id = $book_vehicle_id";

								if($result4 = mysqli_query($this->conn, $updatesql2)){

									//echo "result4";

										$data['log'] = "trip completed";
				        	$data['status']=1;

								}
								else{
								
							    $data['status']=0;
							}
								
						    	//$data['log'] = "trip completed";
				        	//$data['status']=1;
							}else{
								
							    $data['status']=0;
							}

								}
								else{
								
							    $data['status']=0;
							}

								
						}

								else{
								
							    $data['status']=0;
							}

							}
							else{
								
							    $data['status']=0;
			    		}
			    		
			    	}
			    	else if($status_id == 7 || $status_id == 8)
			    	{

			    		
			    		$checkTripQuery="SELECT * FROM `erp_vehicle_trip` AS t WHERE t.id=$trip_id AND t.book_vehicle_id=$book_vehicle_id";
							$tripCheck=mysqli_query($this->conn, $checkTripQuery);

							if(mysqli_num_rows($tripCheck) > 0)
							{

									$sql = "INSERT INTO erp_vehicle_trip_log (trip_id,status_id,latitude,longitude) VALUES (?,?,?,?)";




							if($stmt = mysqli_prepare($this->conn, $sql)){

								 mysqli_stmt_bind_param($stmt, "iiss" ,$trip_id,$status_id,$latitude,$longitude);

								 if(mysqli_stmt_execute($stmt)){


								 	$updatesql1 ="UPDATE erp_vehicle_trip SET status_id = $status_id,c_latitude = $latitude,c_longitude = $longitude  WHERE book_vehicle_id = $book_vehicle_id AND id=$trip_id";



							if($result3 = mysqli_query($this->conn, $updatesql1)){
								
						    	$data['log'] = "vehicle breakdown or completed";
				        	$data['status']=1;
							}else{
								
							    $data['status']=0;
							}

								}
								else{
								
							    $data['status']=0;
							}

								
						}

								else{
								
							    $data['status']=0;
							}

							}
							else{
								
							    $data['status']=0;
			    		}
			    		
			    	}
			    	else
			    	{



			    	
	$sql = "INSERT INTO erp_vehicle_trip_log (trip_id,status_id,latitude,longitude) VALUES (?,?,?,?)";
			 
			if($stmt = mysqli_prepare($this->conn, $sql)){
				
			    // Bind variables to the prepared statement as parameters
			     mysqli_stmt_bind_param($stmt, "iiss" ,$trip_id,$status_id,$latitude,$longitude);
			    			   
			    // Attempt to execute the prepared statement
			    if(mysqli_stmt_execute($stmt)){

			       
			      	
     					$data['log'] = "Trip log added Successfully";
			        	$data['status']=1;

			        
			    } else{
			        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
			        $data['status']=0;
			    }
			} else{
			    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
			    $data['status']=0;
			}
     
     }
        return $data;


}


function vehicleLatLongUpdate($user_id,$trip_id,$status_id,$latitude,$longitude,$book_vehicle_id)
{

	/*echo $user_id.' '.$trip_id.'  '.$status_id.'  '.$latitude.'  '.$longitude;
	exit();*/
	 $data=array();

	 	$query="SELECT MAX(id) FROM erp_vehicle_trip_log WHERE trip_id = $trip_id";

							$count=mysqli_query($this->conn, $query);

							if(mysqli_num_rows($count) > 0)
							{ 
								//echo "if";

									$sql = "INSERT INTO erp_vehicle_trip_log (trip_id,status_id,latitude,longitude) VALUES (?,?,?,?)";




							if($stmt = mysqli_prepare($this->conn, $sql)){

								//echo "sql";

								 mysqli_stmt_bind_param($stmt, "iiss" ,$trip_id,$status_id,$latitude,$longitude);

								 if(mysqli_stmt_execute($stmt)){

								 	//echo "stmt";

								 	$updatesql1 ="UPDATE erp_vehicle_trip SET status_id = $status_id,c_latitude = $latitude,c_longitude = $longitude WHERE book_vehicle_id = $book_vehicle_id";



							if($result3 = mysqli_query($this->conn, $updatesql1)){

								//echo "result3";

								$updatesql2 ="UPDATE erp_book_vehicle SET status_id = $status_id WHERE id = $book_vehicle_id";

								if($result4 = mysqli_query($this->conn, $updatesql2)){

									//echo "result4";

										$data['log'] = "success";
				        	$data['status']=1;

								}
								else{
								
							    $data['status']=0;
							}
								
						    	
							}else{
								
							    $data['status']=0;
							}

								}
								else{
								
							    $data['status']=0;
							}

								
						}

								else{
								
							    $data['status']=0;
							}

							}
							else{
								
							    $data['status']=0;
			    		}
	
	
     
        return $data;


}


/*function leaveApply($leave_type_id,$date_applied,$emp_number,$comments,$date,$length_hours,$length_days,$statusId,$leave_request_id,$leave_type_id,$emp_number,$start_time,$end_time,$duration_type)
    {
        $data=array();

    		$sql = "INSERT INTO erp_leave_request (leave_type_id,date_applied,emp_number,comments) VALUES (?,?,?,?)";
				
				if($stmt = mysqli_prepare($this->conn, $sql)){
				     mysqli_stmt_bind_param($stmt, "isis" ,$leave_type_id,$date_applied,$emp_number,$comments);
				    			   
					    if(mysqli_stmt_execute($stmt)){

				    		$query="INSERT INTO erp_leave (date,length_hours,length_days,status,comments,leave_request_id,leave_type_id,emp_number,start_time,end_time,duration_type) VALUES (?,?,?,?,?,?,?,?,?,?)";
							
				    		if($stmt = mysqli_prepare($this->conn, $sql)){
				     mysqli_stmt_bind_param($stmt, "sssisiiissi" ,$date,$length_hours,$length_days,$status,$comments,$leave_request_id,$leave_type_id,$emp_number,$start_time,$end_time,$duration_type);
				    			   
					    if(mysqli_stmt_execute($stmt)){
				        $data['leave_type_id'] = $data['leave_type_id'];
				        $data['status']=1;
					    } 
					}else{
					        $data['status']=0;
					    }
				} else{
				    $data['status']=0;
					}

				}

				else{
				    $data['status']=0;
					}
		
        return $data;
    }*/

	//ticketAdd
	// function ticketAdd($locationId,$plantId,$usrdeptId,$notifytoId,$statusId,$funclocId,$eqipmntId,$typofisId,$subject,$description,$prtyId,$svrtyId,$reportedBy,$submitted_by_emp_number,$submitted_by_name,$reportedOn,$submitted_on,$user_id,$attachmentId)
 //    {
 //        $data=array();

 //     /*  $base64_string = "";
 //       $image = $this->base64_to_jpeg($base64_string,'tmp.jpg' );
 //       echo $image;
 //       exit();*/

	// 	$query="SELECT ui.last_id FROM hs_hr_unique_id ui WHERE ui.table_name = 'erp_ticket' AND ui.field_name='job_id'";
	// 	$count=mysqli_query($this->conn, $query);

	// 	if(mysqli_num_rows($count) > 0)
	// 	{
	// 		$row=mysqli_fetch_assoc($count);

	// 		$data['last_id']=$row['last_id'];
	// 		$jobinc = $row['last_id']+1;

	// 		$sql = "UPDATE hs_hr_unique_id SET last_id = ".$jobinc." WHERE table_name = 'erp_ticket' AND field_name='job_id'";
	// 		if(mysqli_query($this->conn, $sql)){

	// 			$query="SELECT ui.last_id FROM hs_hr_unique_id ui WHERE ui.table_name = 'erp_ticket' AND ui.field_name='job_id'";
	// 			$count=mysqli_query($this->conn, $query);
	// 				if(mysqli_num_rows($count) > 0){
	// 					$row=mysqli_fetch_assoc($count);
	// 					$prefix = date('Ymd');
	// 					$NewJobId = $row['last_id'];
	// 					$jobIdNew = $prefix . str_pad($NewJobId, 3, "0", STR_PAD_LEFT);
	// 				}

	// 			$source = 1;
	//    			// Prepare an insert statement
	// 			$sql = "INSERT INTO erp_ticket (job_id,location_id,plant_id,user_department_id,notify_to,status_id,functional_location_id,equipment_id,type_of_issue_id,subject,description,priority_id,severity_id,reported_by,submitted_by_name,submitted_by_emp_number,reported_on,submitted_on,source) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				 
	// 			/* echo $reportedOn." , ".$submitted_on;
	// 			 exit();*/
	// 			if($stmt = mysqli_prepare($this->conn, $sql)){
	// 			    // Bind variables to the prepared statement as parameters
	// 			     mysqli_stmt_bind_param($stmt, "siiisiiiissiiisissi" ,$jobIdNew,$locationId,$plantId,$usrdeptId,$notifytoId,$statusId,$funclocId,$eqipmntId,$typofisId,$subject,$description,$prtyId,$svrtyId,$reportedBy,$submitted_by_name,$submitted_by_emp_number,$reportedOn,$submitted_on,$source);
				    			   
	// 				    // Attempt to execute the prepared statement
	// 				    if(mysqli_stmt_execute($stmt)){

	// 			    		$query="SELECT MAX(id) AS ticket_id  FROM erp_ticket";
	// 						$count=mysqli_query($this->conn, $query);
								
	// 							if(mysqli_num_rows($count) > 0)	{
	// 										$row=mysqli_fetch_assoc($count);
	// 										$data['ticket_id'] = $row['ticket_id'];
	// 										$ticket_id = $data['ticket_id'];
	// 										$result=$this->logAdd($user_id,$ticket_id,' ',' ',' ',' ',$user_id,$statusId,$prtyId,$svrtyId,$subject,' ',' ',' ',$submitted_by_name,$submitted_by_emp_number,' ',' ',$submitted_on);
	// 										$sql = "UPDATE erp_ticket_attachment SET ticket_id = ".$ticket_id." WHERE ticket_id = ".$attachmentId;
	// 										mysqli_query($this->conn, $sql);
	// 							}

	// 			        $data['ticketid'] = $data['ticket_id'];
	// 			        $data['status']=1;
	// 				    } else{
	// 				        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
	// 				        $data['status']=0;
	// 				    }
	// 			} else{
	// 			    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
	// 			    $data['status']=0;
	// 				}	

	// 		} else {
	// 				    //echo "ERROR: Could not able to execute $sql. " . mysqli_error($this->conn);
	// 				     $data['status']=0;
	// 		}
	// 	} else{
	// 			    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
	// 			    $data['status']=0;
	// 	}
		
 //        return $data;
 //    }

  
	function paymentAdd($user_id,$vendor_id,$customer_id,$invoice_id,$statusId,$submitted_by,$submitted_on)
    {
        $data=array();

     /*  $base64_string = "";
       $image = $this->base64_to_jpeg($base64_string,'tmp.jpg' );
       echo $image;
       exit();*/

		$query="SELECT ui.last_id FROM hs_hr_unique_id ui WHERE ui.table_name = 'erp_payments' AND ui.field_name='id'";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);

			$data['last_id']=$row['last_id'];
			$jobinc = $row['last_id']+1;

			$sql = "UPDATE hs_hr_unique_id SET last_id = ".$jobinc." WHERE table_name = 'erp_payments' AND field_name='id'";
			if(mysqli_query($this->conn, $sql)){

				
				$source = 1;
	   			// Prepare an insert statement
				$sql = "INSERT INTO erp_payments (vendor_id,customer_id,invoice_id,submitted_by,submitted_on,status) VALUES (?,?,?,?,?,?)";
				 
				/* echo $reportedOn." , ".$submitted_on;
				 exit();*/
				if($stmt = mysqli_prepare($this->conn, $sql)){
				    // Bind variables to the prepared statement as parameters
				     mysqli_stmt_bind_param($stmt, "iiiisi" ,$vendor_id,$customer_id,$invoice_id,$submitted_by,$submitted_on,$statusId);
				    			   
					    // Attempt to execute the prepared statement
					    if(mysqli_stmt_execute($stmt)){

				    		$query="SELECT MAX(id) AS pay_id  FROM erp_payments";
							$count=mysqli_query($this->conn, $query);
								
								if(mysqli_num_rows($count) > 0)	{
											$row=mysqli_fetch_assoc($count);
											$data['pay_id'] = $row['pay_id'];
											$pay_id = $data['pay_id'];
											$result=$this->payLogAdd($user_id,$pay_id,$submitted_by,$submitted_on,$statusId);
											
								}

				        $data['paymentid'] = $data['pay_id'];
				        $data['status']=1;
					    } else{
					        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
					        $data['status']=0;
					    }
				} else{
				    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
				    $data['status']=0;
					}	

			} else {
					    //echo "ERROR: Could not able to execute $sql. " . mysqli_error($this->conn);
					     $data['status']=0;
			}
		} else{
				    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
				    $data['status']=0;
		}
		
        return $data;
    }


    //ActionlogAdd
function payLogAdd($user_id,$pay_id,$submitted_by,$submitted_on,$status_id)
    {

    	//echo $pay_id;

        $userDetails = $this->getUserRoleByUserId($user_id);
        $empNumber = $userDetails['empNumber'];
		$roleId = $userDetails['id'];

    	
   			// Prepare an insert statement

    	$source = 1;     
     	$sql = "INSERT INTO erp_payment_action_log (payment_Id,submitted_by,submitted_on,status) VALUES (?,?,?,?)";

			 
			if($stmt = mysqli_prepare($this->conn, $sql)){
				
			    // Bind variables to the prepared statement as parameters
			     mysqli_stmt_bind_param($stmt, "iisi" ,$pay_id,$submitted_by,$submitted_on,$status_id);
			    			   
			    // Attempt to execute the prepared statement
			    if(mysqli_stmt_execute($stmt)){

			    	//echo "if";
			       
			        if($status_id == 2)
    				 {

     				$updatesql = "UPDATE erp_payments SET status = 2 WHERE id = $pay_id";
     					if($result2 = mysqli_query($this->conn, $updatesql)){
						/*$data['session_token'] = $token;*/
						
							$data['log'] = "Approved";
				        	$data['status']=1;
						
						
						}
							else{
							//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
					    	$data['status']=0;
							}

     				}

     				else if($status_id == 10)
    				 {

    				 		/*echo $status_id;
    				 		echo $ticket_id;
    				 		exit;*/
     				$updatesql = "UPDATE erp_payments SET status = 10 WHERE id = $pay_id";
     					if($result2 = mysqli_query($this->conn, $updatesql)){
						/*$data['session_token'] = $token;*/
						$data['log'] = "Reviewed";
				        $data['status']=1;
						}
							else{
							//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
					    	$data['status']=0;
							}

     				}

     				
     				
     				else{

     						$updatesql = "UPDATE erp_payments SET status = $status_id WHERE id = $pay_id";
     					if($result2 = mysqli_query($this->conn, $updatesql)){
						/*$data['session_token'] = $token;*/
						$data['log'] = "status changed";
				        $data['status']=1;
						}
							else{
							//echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
					    	$data['status']=0;
							}
     				}

     				
			        
			    } else{
			        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
			        $data['status']=0;
			    }
			} else{
			    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
			    $data['status']=0;
			}
     
     
        return $data;
    }


    //paymentDetails
    function paymentDetails($user_id)
	{
		$data= array();
		$query="SELECT p.id,p.customer_id,p.invoice_id,p.submitted_by,p.submitted_on FROM erp_payments p
LEFT JOIN erp_payment_action_log l ON p.id =  l.id";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do { 						
						$data['pay_id']=$row['id'];
						$data['customer_id']=$row['customer_id'];
						$data['invoice_id']=$row['invoice_id'];
						$data['submitted_by']=$row['submitted_by'];
						$data['submitted_on']=$row['submitted_on'];
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['paymentDetails']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}


		function paymentDetailsById($user_id,$pay_id)
	{
		$data= array();
		$query="SELECT p.id,p.customer_id,p.invoice_id,p.submitted_by,p.submitted_on FROM erp_payments p
		LEFT JOIN erp_payment_action_log l ON p.id =  l.id
		WHERE p.id = $pay_id";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do { 						
						$data['pay_id']=$row['id'];
						$data['customer_id']=$row['customer_id'];
						$data['invoice_id']=$row['invoice_id'];
						$data['submitted_by']=$row['submitted_by'];
						$data['submitted_on']=$row['submitted_on'];
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['paymentDetailsById']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}


//permissionAdd
	// function permissionAdd($user_id,$date,$from_time,$to_time,$reason,$statusId)
 //    {
       

 //       			 $data=array();
		
	// 			$sql = "INSERT INTO erp_permission (date,from_time,to_time,reason,status) VALUES (?,?,?,?,?)";
				 
	// 		/*	echo $sql;
	// 			 exit();*/
	// 			if($stmt = mysqli_prepare($this->conn, $sql)){

	// 				//echo "stmt";
	// 			    // Bind variables to the prepared statement as parameters
	// 			     mysqli_stmt_bind_param($stmt, "ssssi" ,$date,$from_time,$to_time,$reason,$statusId);
				    			   
	// 						if(mysqli_stmt_execute($stmt)){

	// 								//echo "if";
	// 								//permissionAdd
	// 								$data['status']=1;

	// 						}
	// 						else{
	// 							//echo "else";
	// 			    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
	// 			    $data['status']=0;
	// 	}

				        
	// 				}
	// 			    		else{
	// 			    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
	// 			    $data['status']=0;
	// 	}
		
 //        return $data;
 //    }


	function permissionReason()
	{

		$data= array();
		$query="SELECT id, reason FROM erp_reason";
		//echo $query;
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
			//echo "if";
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['id']=$row['id'];
						$data['reason']=$row['reason'];	
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['permissionReason']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}  

	function permissionReasonById($id)
	{

	
		$query="SELECT  reason FROM erp_reason WHERE id = $id ";
		
		$count=mysqli_query($this->conn, $query);
		
			$row=mysqli_fetch_assoc($count);
				
			foreach ($row as $key) {
							# code...
			}	
							
		
		return $key;    
	}   


	function permissionStatus()
	{
		$data= array();
		$query="SELECT id, status_name FROM erp_permission_status";
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

						$data['id']=$row['id'];
						$data['status_name']=$row['status_name'];	
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['permissionStatus']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	}    

	// Writen by pavan start Recruitment
	 // function createManpowerRequisition($user_id, $requisition_type, $job_title, $location, $department, $report_to, $no_of_positions, $job_description, $type_of_appointment, $duration, $grade,$qualifications, $skill_experience, $min_eperience, $min_salary, $max_salary, $required_by,$requested_budgeted, $reason_for_requirement, $replace_for, $comments, $status)
  //   {
  //   	$userDetails = $this->getUserRoleByUserId($user_id);
		// $userRoleId = $userDetails['id'];
		// $submittedbyEmp = $userDetails['empNumber'];
		// $action_status = 1;
  //      $data = array();

  //      $newDate = date('Y-m-d H:i:s');

  //      // Prepare an insert statement
		// $sql = "INSERT INTO erp_manpower_requisition (job_title,location,department,report_to,no_of_positions,job_description,type_of_appointment,duration,grade,qualifications,skill_experience,min_eperience,min_salary,max_salary,required_by,requested_budgeted,reason_for_requirement,replace_for,comments,status,requisition_type,submitted_by,submitted_on,action_status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

		// // echo $sql;die();

		// if ($stmt = mysqli_prepare($this->conn,$sql)) {

		// 	mysqli_stmt_bind_param($stmt,"iiiiisisssssiisisssiiis",$job_title, $location, $department, $report_to, $no_of_positions, $job_description, $type_of_appointment, $duration, $grade,$qualifications, $skill_experience, $min_eperience, $min_salary, $max_salary, $required_by,$requested_budgeted, $reason_for_requirement, $replace_for, $comments, $status, $requisition_type, $submittedbyEmp,$newDate,$action_status);
		// 	if($output = mysqli_execute($stmt)) {
		// 		$requiId = $this->conn->insert_id;

		// 		$logsql = "INSERT INTO erp_manpower_requisition_log (req_id,submitted_by,submitted_on,action_status,comment) VALUES (?,?,?,?,?)";
		// 		$logstmt = mysqli_prepare($this->conn,$logsql)
		// 		mysqli_stmt_bind_param($logstmt,"iisis",$requiId,$submittedbyEmp,$newDate,$action_status,$comment);
		// 		$logoutput = mysqli_execute($logstmt)
		// 		if($logoutput){
		// 			$data['status']= 1;
		// 			$data['message']= "Successfully created requisition";

		// 		}else{
		// 			$data['status']= 0;
		// 			$data['message']= "failed requisition";
		// 		}
		// 	}
		// }
		// return $data;
  //   }

	function getStepCount($user_id)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);
		$userRoleId = $userDetails['id'];

		
		$empNumber = $userDetails['empNumber'];
       	$data = array();

       	$count_date = date('Y-m-d');
	
       	$getQuery = "SELECT * FROM erp_app_stepcount WHERE emp_number = $empNumber AND count_date='".$count_date."'";
    	$result = mysqli_query($this->conn, $getQuery);


		if(mysqli_num_rows($result) > 0){
			$row = mysqli_fetch_assoc($result);
			do{
				$data['step_count']= $row['step_count'];
			}while($row = mysqli_fetch_assoc($result));
				$data['step_count']= $data;
				$data['status']= 1;
		}else{
			$data['step_count']= 0;
			$data['status']= 0;
		}
		return $data;
    }
	function getCreateStepCount($user_id,$step_count)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);
		$userRoleId = $userDetails['id'];
		
		$empNumber = $userDetails['empNumber'];
       $data = array();

       $count_date = date('Y-m-d');

       $updatedCount = 0;


       $getQuery = "SELECT * FROM erp_app_stepcount WHERE emp_number = $empNumber AND count_date='".$count_date."'";
    	$existRecord = mysqli_query($this->conn, $getQuery);
		if(mysqli_num_rows($existRecord) > 0){
			$row = mysqli_fetch_assoc($existRecord);

			do{
				// echo $step_count;die();
			$updatedCount = $row['step_count'] + $step_count;
				
			}while($row = mysqli_fetch_assoc($existRecord));

			$sql = "UPDATE erp_app_stepcount SET step_count = $updatedCount WHERE emp_number = $empNumber AND count_date = '".$count_date."'";

			$result = mysqli_query($this->conn, $sql);

			if($result){
				$data['step_count']= "Step count updated successfully";
				$data['status']= 1;
			}else{
				$data['step_count']= "Step count updated failed";
				$data['status']= 0;
			}


		}else{
		$sql = "INSERT INTO erp_app_stepcount (emp_number,count_date,step_count) VALUES (?,?,?)";

			if ($stmt = mysqli_prepare($this->conn,$sql)) {

				mysqli_stmt_bind_param($stmt,"isi",$empNumber,$count_date,$step_count);
				if($output = mysqli_execute($stmt)) {
					$countId = $this->conn->insert_id;
					// $permissionLog =	$this->permsnLogAdd($permissionId,$statusId,$submittedby,$submittedon,$date,$comment);
				$data['step_count']= "Inserted new record";
				$data['status']= 1;
				}else{
				$data['step_count']= "Failed";
				$data['status']= 0;
				}
			}
		}
     
		return $data;
    }
    // icc posh written by pavan
	function createComplaint($data)
    {
    	// print_r($_POST);die();
    	$user_id = $_POST['user_id'];

    	$userDetails = $this->getUserRoleByUserId($user_id);

    	// print_r($userDetails);die();
		$userRoleId = $userDetails['id'];

    	$tokenNo = date('Y').date('m').'/';

    	$complainantType = $_POST['complainantType'];

    	if(!empty($_POST['witness'])){
	 		$witness = $_POST['witness'];
	 		// $witness = implode(',', $_POST['witness']);
	 	}else{
	 		$witness='';
	 	}

	 	if(!empty($complainantType) && $complainantType == self::COMP_TYPE_OTHER){
	 		$complainantId = Null;
			$cmptOtherName = $_POST['cmptOtherName'];
    		$cmptOtherDetails = $_POST['cmptOtherDetails'];
    		$cmptOtherEmail = $_POST['cmptOtherEmail'];
    		$cmptOtherMobile = $_POST['cmptOtherMobile'];
            $complntCmpnyId = Null;
	 	}else{
	 		$complainantId = $_POST['complainantId'];
			$cmptOtherName = null;
			$cmptOtherDetails = null;
			$cmptOtherEmail = null;
			$cmptOtherMobile = null;
            $complntCmpnyId = $_POST['complntCmpnyId'];
	 	}

    	$persecutorType = $_POST['persecutorType'];

    	if(!empty($persecutorType) && $persecutorType == self::COMP_TYPE_OTHER){
			$persecutorId = Null;
			$persecutorOtherName = $_POST['persecutorOtherName'];
    		$persecutorOtherDetails = $_POST['persecutorOtherDetails'];
    		$persecutorOtherEmail = $_POST['persecutorOtherEmail'];
    		$persecutorOtherMobile = $_POST['persecutorOtherMobile'];
            $perstrCompId = Null;
		}else{
			$persecutorId = $_POST['persecutorId'];
			$persecutorOtherName = null;
			$persecutorOtherDetails = null;
			$persecutorOtherEmail = null;
			$persecutorOtherMobile = null;
            $perstrCompId = $_POST['perstrCompId'];
		}

		if(!empty($_POST['dateOfIncident'])){

    	$dateOfIncident = date('Y-m-d',strtotime($_POST['dateOfIncident']));
		}else{
			$dateOfIncident ='';
		}
    	$timeOfIncident = $_POST['timeOfIncident'];
    	$natureOfHarassment = $_POST['natureOfHarassment'];
    	$incidentShortNote = $_POST['incidentShortNote'];
    	$incidentDetails = $_POST['incidentDetails'];
    	$incidentLocation = $_POST['incidentLocation'];
    	$sequenceOfEvents = $_POST['sequenceOfEvents'];
    	$comments = $_POST['comments'];

    	if($_POST['submit_method']== self::COMPLAINT_DRAFT){
	 	$status = self::COMPLAINT_DRAFT;
	 	}else if($_POST['submit_method']== self::COMPLAINT_SUBMIT){
	 	$status = self::COMPLAINT_SUBMIT;
	 	}else{
	 	$status = self::COMPLAINT_DRAFT;
	 	}

    	

	 	$reportingPerson = $_POST['reportingPerson'];

	 	if($reportingPerson == self::REPORTING_PERSON_YES){
	 		$reportingType = self::REPORTING_PERSON_YES;
	 	}else{
	 	    $reportingType = $_POST['reportingType'];
	    }

	    if($reportingType == self::REPORTING_TYPE_VOLUNTARY){
			$reportedBy = Null;
			$reportedOn = Null;
		}else{
			$reportedBy = $userDetails['empNumber'];
			$reportedOn = date("Y-m-d");
		}

		//$upload_dir = './../../entreplanPosh/symfony/upload/icc/';
		
            $filenamesArr = array();

            // print_r($_FILES['attachments']['name']);die();
            $countfiles = sizeof($_FILES['attachments']['name']);

            // echo sizeof($_FILES['attachments']['name']);die();
        if(!empty($_FILES['attachments']['name'][0])){
            for($i=0;$i<$countfiles;$i++){
            $fileNameA =str_replace(" ", "_", $_FILES['attachments']['name'][$i]);
            $filename = time().'_'.$fileNameA;

            move_uploaded_file($_FILES['attachments']['tmp_name'][$i],'./../../entreplanPosh/symfony/upload/icc/'.$filename);
            array_push($filenamesArr, $filename);
            } 
             // print_r($filenamesArr);die();
             if(!empty($filenamesArr)){
                $attachments = implode(',', $filenamesArr);
             }else{
                $attachments='';
             }

        }else{
            $attachments= $_POST['attachmentshidden'];
        }


		
		$submittedby = $userDetails['empNumber'];
       	$data = array();

       $submittedon = date('Y-m-d H:i:s');

       // Prepare an insert statement
		$sql = "INSERT INTO erp_complaints (token_no,complainant_type,complainant_id,complt_comp_id,cmpt_other_name,cmpt_other_details,cmpt_other_email,cmpt_other_mobile,persecutor_type,persecutor_id,perstr_comp_id,persecutor_other_name,persecutor_other_details,persecutor_other_email,persecutor_other_mobile,date_of_incident,time_of_incident,nature_of_harassment,incident_short_note,incident_details,incident_location,witnesses,sequence_of_events,comments,reporting_person,reporting_type,reported_by,reported_on,submitted_by,submitted_on,status,attachments) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

		// echo $sql;die();

		if ($stmt = mysqli_prepare($this->conn,$sql)) {

    	// $sequenceOfEvents = $_POST['sequenceOfEvents'];

    	// $comments = $_POST['comments'];
			
			mysqli_stmt_bind_param($stmt,"siiissssiiissssssissssssiiisisis",$tokenNo,$complainantType,$complainantId,$complntCmpnyId,$cmptOtherName,$cmptOtherDetails,$cmptOtherEmail,$cmptOtherMobile,$persecutorType,$persecutorId,$perstrCompId,$persecutorOtherName,$persecutorOtherDetails,$persecutorOtherEmail,$persecutorOtherMobile,$dateOfIncident,$timeOfIncident,$natureOfHarassment,$incidentShortNote,$incidentDetails,$incidentLocation,$witness,$sequenceOfEvents,$comments,$reportingPerson,$reportingType,$reportedBy,$reportedOn,$submittedby,$submittedon,$status,$attachments);
			if($output = mysqli_execute($stmt)) {
				// echo "success";die();
				$cmtId = $this->conn->insert_id;
				$this->complaintLogAdd($cmtId, $status, $submittedby, $submittedon, $comments);
				$data['cmtId']= $cmtId;
				$data['status']= 1;
			}else{
				// echo "failed";die();
				$data['status']= 0;

			}
		}else{
			$data['status']= 0;
		}
		return $data;
    }


    function createVoiceComplaint($data)
    {
    	// print_r($_POST);die();
    	$user_id = $_POST['user_id'];

    	$userDetails = $this->getUserRoleByUserId($user_id);

    	// print_r($userDetails);die();
		$userRoleId = $userDetails['id'];

    	$tokenNo = date('Y').date('m').'/';

    	
	 		$complainantId = $_POST['complainantId'];
			
            $complntCmpnyId = $_POST['complntCmpnyId'];

            $complainantType = $_POST['complainantType'];

            $dateOfIncident = date('Y-m-d');

    	

    	if($_POST['submit_method']== self::COMPLAINT_DRAFT){
	 	$status = self::COMPLAINT_DRAFT;
	 	}else if($_POST['submit_method']== self::COMPLAINT_SUBMIT){
	 	$status = self::COMPLAINT_SUBMIT;
	 	}else{
	 	$status = self::COMPLAINT_DRAFT;
	 	}

		//$upload_dir = './../../entreplanPosh/symfony/upload/icc/';
		
            $filenamesArr = array();

            // print_r($_FILES['attachments']['name']);die();
            $countfiles = sizeof($_FILES['attachments']['name']);

            // echo sizeof($_FILES['attachments']['name']);die();
        if(!empty($_FILES['attachments']['name'][0])){
            for($i=0;$i<$countfiles;$i++){
            $fileNameA =str_replace(" ", "_", $_FILES['attachments']['name'][$i]);
            $filename = time().'_'.$fileNameA;

            move_uploaded_file($_FILES['attachments']['tmp_name'][$i],'./../../entreplanPosh/symfony/upload/icc/'.$filename);
            array_push($filenamesArr, $filename);
            } 
             // print_r($filenamesArr);die();
             if(!empty($filenamesArr)){
                $attachments = implode(',', $filenamesArr);
             }else{
                $attachments='';
             }

        }else{
            $attachments= $_POST['attachmentshidden'];
        }


		
		$submittedby = $userDetails['empNumber'];
       	$data = array();

       $submittedon = date('Y-m-d H:i:s');
       $time_of_incident = date('H:i');
       $date_of_incident = date('Y-m-d');
       $device_type = 'APP';

       // Prepare an insert statement
		$sql = "INSERT INTO erp_complaints (token_no,complainant_id,complt_comp_id,submitted_by,submitted_on,status,attachments,complainant_type,date_of_incident,device_type,time_of_incident) VALUES (?,?,?,?,?,?,?,?,?,?,?)";

		// echo $sql;die();

		if ($stmt = mysqli_prepare($this->conn,$sql)) {

    	// $sequenceOfEvents = $_POST['sequenceOfEvents'];

    	// $comments = $_POST['comments'];
			
			mysqli_stmt_bind_param($stmt,"siiisisisss",$tokenNo,$complainantId,$complntCmpnyId,$submittedby,$submittedon,$status,$attachments,$complainantType,$date_of_incident,$device_type,$time_of_incident);
			if($output = mysqli_execute($stmt)) {
				// echo "success";die();
				$cmtId = $this->conn->insert_id;
				// $this->complaintLogAdd($cmtId, $status, $submittedby, $submittedon);
				$data['cmtId']= $cmtId;
				$data['status']= 1;
			}else{
				$data['status']= 0;

			}
		}else{
				// echo "failed";die();
			$data['status']= 0;
		}
		return $data;
    }

    function acceptComplaintAcknowledge($values)
    {
    	// print_r($_POST);die();
    	$user_id = $_POST['user_id'];
    	$complaintId = $_POST['complaint_id'];
    	$status = $_POST['status'];
    	$comment = $_POST['comment'];
    	// $acknowledge = $_POST['acknowledge'];
    	$data =array();

    	$userDetails = $this->getUserRoleByUserId($user_id);
    	// print_r($userDetails);die();
		$userRoleId = $userDetails['id'];
		$submittedby = $userDetails['empNumber'];

		if($status== self::COMPLAINT_SUBMIT){


		if(!empty($_FILES['acknowledge']['name'])){
            $fileNameA =str_replace(" ", "_", $_FILES['acknowledge']['name']);
            $filename = time().'_'.$fileNameA;

            move_uploaded_file($_FILES['acknowledge']['tmp_name'],'./../../entreplanPosh/symfony/upload/icc/'.$filename);
             // print_r($filenamesArr);die();
             if(!empty($filename)){
                $acknowledge =$filename;
             }else{
                $acknowledge='';
             }

        }else{
            $acknowledge= '';
        }

        $submittedon = date('Y-m-d H:i:s');
       	$device_type = 'APP';
       	$statusId = self::COMPLAINT_ACCEPTED;
       	$sql = "UPDATE erp_complaints SET status = $statusId WHERE id = $complaintId";

		// echo $sql;die();

		if (!empty($complaintId)) {

	    		if($result = mysqli_query($this->conn, $sql)){
	    			$logsql = "INSERT INTO erp_complaints_action_log (complaint_id, status, submitted_by, submitted_on, comment,meetingattachments) VALUES (?,?,?,?,?,?)";
     				$logstmt = mysqli_prepare($this->conn, $logsql);
		 			mysqli_stmt_bind_param($logstmt, "iiisss" ,$complaintId, $statusId, $submittedby, $submittedon, $comment, $acknowledge);
		 			// mysqli_stmt_execute($stmt);
		 			mysqli_execute($logstmt);

					$data['message']= "Acknowledge status updated successfully";
					$data['status']= 1;

				}else{
					$data['message']= "Acknowledge status updated failed";
					$data['status']= 0;
				}
		}else{
			$data['message']= "Acknowledge status updated failed";
			$data['status']= 0;
		}
		}else{
			$data['message']= "Record not valid";
			$data['status']= 0;
		}
		return $data;
    }

    function persecutorExplanationComplaint($userId,$complaintId,$explanation,$status)
    {
    	
    	// $acknowledge = $_POST['acknowledge'];
    	$data =array();

    	$userDetails = $this->getUserRoleByUserId($userId);
    	// print_r($userDetails);die();
		$userRoleId = $userDetails['id'];
		$submittedby = $userDetails['empNumber'];

		if($status== self::SHOW_CAUSE_NOTICE){

        $submittedon = date('Y-m-d H:i:s');
       	$device_type = 'APP';
       	$statusId = self::PERSECUTOR_EXPLAINATION;
       	$sql = "UPDATE erp_complaints SET status = $statusId WHERE id = $complaintId";

		// echo $sql;die();

		if (!empty($complaintId)) {

	    		if($result = mysqli_query($this->conn, $sql)){
	    			$logsql = "INSERT INTO erp_complaints_action_log (complaint_id, status, submitted_by, submitted_on, comment) VALUES (?,?,?,?,?)";
     				$logstmt = mysqli_prepare($this->conn, $logsql);
		 			mysqli_stmt_bind_param($logstmt, "iiiss" ,$complaintId, $statusId, $submittedby, $submittedon, $explanation);
		 			// mysqli_stmt_execute($stmt);
		 			mysqli_execute($logstmt);

					$data['message']= "Persecutor status updated successfully";
					$data['status']= 1;

				}else{
					$data['message']= "Persecutor status updated failed";
					$data['status']= 0;
				}
		}else{
			$data['message']= "Persecutor status updated failed";
			$data['status']= 0;
		}
		}else{
			$data['message']= "Record not valid";
			$data['status']= 0;
		}
		return $data;
    }


    function sendShowCauseNotice($values)
    {
    	// print_r($values);die();
    	$user_id = $values['user_id'];
    	$complaintId = $values['complaint_id'];
    	$noticeTo = $values['noticeTo'];
    	$showCaseNotice = $values['showCase'];
    	if(!empty($values['dueDate'])){
    		$dueDate =  date('Y-m-d',strtotime($values['dueDate']));
    	}else{
    		$dueDate =  date('Y-m-d');
    	}
    	$status = $values['status'];
    	// $acknowledge = $_POST['acknowledge'];
    	$data =array();

    	$userDetails = $this->getUserRoleByUserId($user_id);
    	// print_r($userDetails);die();
		$userRoleId = $userDetails['id'];
		$submittedby = $userDetails['empNumber'];

		if($status== self::COMPLAINT_ACCEPTED){


        $submittedon = date('Y-m-d H:i:s');
       	$device_type = 'APP';
       	$statusId = self::SHOW_CAUSE_NOTICE;
       	$sql = "UPDATE erp_complaints SET status = $statusId WHERE id = $complaintId";

		// echo $sql;die();

		if (!empty($complaintId)) {

	    		if($result = mysqli_query($this->conn, $sql)){
	    			$logsql = "INSERT INTO erp_complaints_action_log (complaint_id, status, submitted_by, submitted_on, notice_to,show_cause,due_date) VALUES (?,?,?,?,?,?,?)";
     				$logstmt = mysqli_prepare($this->conn, $logsql);
		 			mysqli_stmt_bind_param($logstmt, "iiisiss" ,$complaintId, $statusId, $submittedby, $submittedon, $noticeTo, $showCaseNotice,$dueDate);
		 			// mysqli_stmt_execute($stmt);
		 			mysqli_execute($logstmt);

					$data['message']= "Show Case Notice Sent successfully";
					$data['status']= 1;

				}else{
					$data['message']= "Show Case Notice Sentfailed";
					$data['status']= 0;
				}
		}else{
			$data['message']= "Show Case Notice Sent failed";
			$data['status']= 0;
		}
		}else{
			$data['message']= "Invalid  Request";
			$data['status']= 0;
		}
		return $data;
    }


    function updateComplaint($data)
    {
    	// print_r($_POST);die();
    	$user_id = $_POST['user_id'];
    	$comltid = $_POST['comlt_id'];

    	$userDetails = $this->getUserRoleByUserId($user_id);

    	// print_r($userDetails);die();
		$userRoleId = $userDetails['id'];

    	$tokenNo = date('Y').date('m').'/';

    	$complainantType = $_POST['complainantType'];

    	if(!empty($_POST['witness'])){
	 		$witness = $_POST['witness'];
	 		// $witness = implode(',', $_POST['witness']);
	 	}else{
	 		$witness=Null;
	 	}

	 	if($complainantType == self::COMP_TYPE_OTHER){
	 		$complainantId = Null;
			$cmptOtherName = $_POST['cmptOtherName'];
    		$cmptOtherDetails = $_POST['cmptOtherDetails'];
    		$cmptOtherEmail = $_POST['cmptOtherEmail'];
    		$cmptOtherMobile = $_POST['cmptOtherMobile'];
            $complntCmpnyId = Null;
	 	}else{
	 		$complainantId = $_POST['complainantId'];
			$cmptOtherName = null;
			$cmptOtherDetails = null;
			$cmptOtherEmail = null;
			$cmptOtherMobile = null;
            $complntCmpnyId = $_POST['complntCmpnyId'];
	 	}

    	$persecutorType = $_POST['persecutorType'];

    	if(empty($_POST['persecutorId'])){
			$persecutorId = Null;
			$persecutorOtherName = $_POST['persecutorOtherName'];
    		$persecutorOtherDetails = $_POST['persecutorOtherDetails'];
    		$persecutorOtherEmail = $_POST['persecutorOtherEmail'];
    		$persecutorOtherMobile = $_POST['persecutorOtherMobile'];
            $perstrCompId = Null;
		}else{
			$persecutorId = $_POST['persecutorId'];
			$persecutorOtherName = null;
			$persecutorOtherDetails = null;
			$persecutorOtherEmail = null;
			$persecutorOtherMobile = null;
            $perstrCompId = $_POST['perstrCompId'];
		}


    	$dateOfIncident = date('Y-m-d',strtotime($_POST['dateOfIncident']));
    	$timeOfIncident = $_POST['timeOfIncident'];
    	$natureOfHarassment = $_POST['natureOfHarassment'];
    	$incidentShortNote = $_POST['incidentShortNote'];
    	$incidentDetails = $_POST['incidentDetails'];
    	$incidentLocation = $_POST['incidentLocation'];
    	$sequenceOfEvents = $_POST['sequenceOfEvents'];
    	$comments = $_POST['comments'];

    	if($_POST['submit_method']== self::COMPLAINT_DRAFT){
	 	$status = self::COMPLAINT_DRAFT;
	 	}else if($_POST['submit_method']== self::COMPLAINT_SUBMIT){
	 	$status = self::COMPLAINT_SUBMIT;
	 	}else{
	 	$status = self::COMPLAINT_DRAFT;
	 	}

    	

	 	$reportingPerson = $_POST['reportingPerson'];

	 	if($reportingPerson == self::REPORTING_PERSON_YES){
	 		$reportingType = self::REPORTING_PERSON_YES;
	 	}else{
	 	    $reportingType = $_POST['reportingType'];
	    }

	    if($reportingType == self::REPORTING_TYPE_VOLUNTARY){
			$reportedBy = Null;
			$reportedOn = Null;
		}else{
			$reportedBy = $userDetails['empNumber'];
			$reportedOn = date("Y-m-d");
		}

		$upload_dir = './../../entreplanPosh/symfony/upload/icc/';
		
            $filenamesArr = array();
            $countfiles = sizeof($_FILES['attachments']['name']);

            // $base64string = '';
		    // $uploadpath   = './../../entreplan3.1/symfony/upload/icc/';
		    // $parts        = explode(";base64,", $base64string);
		    // $imageparts   = explode("image/", @$parts[0]);
		    // $imagetype    = $imageparts[1];
		    // $imagebase64  = base64_decode($parts[1]);
		    // $file         = $uploadpath . uniqid() . '.png';
		    // file_put_contents($file, $imagebase64);

            // echo sizeof($_FILES['attachments']['name']);die();
        if(!empty($_FILES['attachments']['name'][0])){
            for($i=0;$i<$countfiles;$i++){
            $fileNameA =str_replace(" ", "_", $_FILES['attachments']['name'][$i]);
            $filename = time().'_'.$fileNameA;

            move_uploaded_file($_FILES['attachments']['tmp_name'][$i],'./../../entreplanPosh/symfony/upload/icc/'.$filename);
            array_push($filenamesArr, $filename);
            } 
             // print_r($filenamesArr);die();
             if(!empty($filenamesArr)){
                $attachments = implode(',', $filenamesArr);
             }else{
                $attachments='';
             }

        }else{
            $attachments= $_POST['attachmentshidden'];
        }


		
		$submittedby = $userDetails['empNumber'];
       	$data = array();

       $submittedon = date('Y-m-d H:i:s');

       // Prepare an insert statement
       	$sql = "UPDATE erp_complaints SET complainant_type = $complainantType, complainant_id = $complainantId, complt_comp_id = $complntCmpnyId, cmpt_other_name = '".$cmptOtherName."', cmpt_other_details = '".$cmptOtherDetails."', cmpt_other_email = '".$cmptOtherEmail."', cmpt_other_mobile = '".$cmptOtherMobile."', persecutor_type=$persecutorType,persecutor_id=$persecutorId,perstr_comp_id=$perstrCompId,persecutor_other_name='".$persecutorOtherName."',persecutor_other_details= '".$persecutorOtherDetails."',persecutor_other_email='". $persecutorOtherEmail."',persecutor_other_mobile= '".$persecutorOtherMobile."',date_of_incident='".$dateOfIncident."',time_of_incident='".$timeOfIncident."',nature_of_harassment=$natureOfHarassment,incident_short_note='".$incidentShortNote."',incident_details='".$incidentDetails."',incident_location='".$incidentLocation."',witnesses='".$witness."',sequence_of_events='".$sequenceOfEvents."',comments='".$comments."',reporting_person=$reportingPerson,reporting_type=$reportingType,reported_by=$reportedBy,reported_on='".$reportedOn."',status=$status, attachments='".$attachments."'  WHERE id = $comltid";

		// echo $sql;die();

		if (!empty($comltid)) {

	    		if($result = mysqli_query($this->conn, $sql)){
					$data['message']= "Complaint updated successfully";
					$data['status']= 1;

				}else{
					$data['message']= "Complaint updated failed";
					$data['status']= 0;
				}
		}else{
			$data['message']= "Complaint updated failed";
			$data['status']= 0;
		}
		return $data;
    }

    function complaintsList($userId)
    {
    	$userDetails = $this->getUserRoleByUserId($userId);
		$userRoleId = $userDetails['id'];
		
		$empNumber = $userDetails['empNumber'];
        $data=array();

        // echo $empNumber;die();
  			
		$query = "SELECT c.* FROM `erp_complaints` as c WHERE c.submitted_by=$empNumber ORDER BY c.id DESC";

		// echo $query;die();
		$count=mysqli_query($this->conn, $query);

		// echo mysqli_num_rows($count);die();

			if(mysqli_num_rows($count) > 0)
			{
				$row = mysqli_fetch_assoc($count);
					
				do { 						
				
					$data['id'] = $row['id'];
					$data['token_no'] = $row['token_no'];
					$data['complainant_type'] = $row['complainant_type'];

					if($row['complainant_type'] == self::COMP_TYPE_ORGANISATION){
						$data['complainant_type_name'] = 'ORGANISATION';
					}else if($row['complainant_type'] == self::COMP_TYPE_CONTRACT_OUTSOURCE){
						$data['complainant_type_name'] = 'CONTRACT/OUTSOURCE';
					}else if($row['complainant_type'] == self::COMP_TYPE_TRAINEE){
						$data['complainant_type_name'] = 'TRAINEE';
					}else if($row['complainant_type'] == self::COMP_TYPE_OTHER){
						$data['complainant_type_name'] = 'OTHER';
					}else{
						$data['complainant_type_name'] = '';
					}
					$data['complainant_id'] = $row['complainant_id'];

					if(!empty($row['device_type'])){
					$data['deviceType'] = $row['device_type'];
					}else{
					$data['deviceType'] = 'WEB';
					}

					if(!empty($row['complainant_id'])){
						$row['complainant_name'] = $this->getEmpnameByEmpNumber($row['complainant_id']);
					}else{
						$row['complainant_name'] = $row['cmpt_other_name'];
					}

					$data['persecutor_type'] = $row['persecutor_type'];

					if($row['persecutor_type'] == self::COMP_TYPE_ORGANISATION){
						$data['persecutor_type_name'] = 'ORGANISATION';
					}else if($row['persecutor_type'] == self::COMP_TYPE_CONTRACT_OUTSOURCE){
						$data['persecutor_type_name'] = 'CONTRACT/OUTSOURCE';
					}else if($row['persecutor_type'] == self::COMP_TYPE_TRAINEE){
						$data['persecutor_type_name'] = 'TRAINEE';
					}else if($row['persecutor_type'] == self::COMP_TYPE_OTHER){
						$data['persecutor_type_name'] = 'OTHER';
					}else{
						$data['persecutor_type_name'] = '';
					}
					$data['persecutor_id'] = $row['persecutor_id'];


					$data['date_of_incident'] = date('d-m-Y',strtotime($row['date_of_incident']));
					$data['nature_of_harassment'] = $row['nature_of_harassment'];
					if($row['nature_of_harassment'] == self::HARASSMENT_TYPE_VISUAL){
						$data['nature_of_harassment_name'] = 'VISUAL';
					}else if($row['nature_of_harassment'] == self::HARASSMENT_TYPE_PHYSICAL){
						$data['nature_of_harassment_name'] = 'PHYSICAL';
					}else if($row['nature_of_harassment'] == self::HARASSMENT_TYPE_OTHER){
						$data['nature_of_harassment_name'] = 'OTHER';
					}else{
						$data['nature_of_harassment_name'] = '';
					}	
					
					$data['status'] = $row['status'];
					if($row['status'] == self::COMPLAINT_DRAFT){
						$data['status_name'] = 'Draft';
					}elseif($row['status'] == self::COMPLAINT_SUBMIT){
						$data['status_name'] = 'Submitted';
					}elseif($row['status'] == 2){
						$data['status_name'] = 'Accepted';
					}elseif($row['status'] == 3){
						$data['status_name'] = 'Rejected';
					}elseif($row['status'] == 4){
						$data['status_name'] = 'Show Cause';
					}elseif($row['status'] == 5){
						$data['status_name'] = 'Respondent Explanation';
					}elseif($row['status'] == 6){
						$data['status_name'] = 'Settelment';
			        }elseif($row['status'] == 8){
			            $data['status_name'] = 'No Settelment';
			        }elseif($row['status'] == 7){
			            $data['status_name'] = 'Closed';
					}elseif($row['status'] == 9){
			            $data['status_name'] = 'Investigation Draft';
					}elseif($row['status'] == 10){
			            $data['status_name'] = 'Investigation Submitted';
					}
					$data1[] = $data;
				}while($row = mysqli_fetch_assoc($count));
					$data['status'] = 1;
					$data['complaint_list']=$data1;
						
			}else{
					$data['status'] = 0;
					$data['complaint_list']=array();
			}

		return $data;
    }

    function complaintDetails($userId,$complaintId)
    {
        $data=array();
  			
		$query = "SELECT c.* FROM `erp_complaints` as c WHERE c.id=$complaintId";

		// echo $query;die();
		$count=mysqli_query($this->conn, $query);

		// echo mysqli_num_rows($count);die();

			if(mysqli_num_rows($count) > 0)
			{
				$row = mysqli_fetch_assoc($count);
					
				do { 						
				
					$data['id'] = $row['id'];
					$data['token_no'] = $row['token_no'];
					$data['complainant_type'] = $row['complainant_type'];

					$data['complainant_id'] = $row['complainant_id'];

					if(!empty($row['complainant_id'])){
						$data['complainant_name'] = $this->getEmpnameByEmpNumber($row['complainant_id']);
					}else{
						$data['complainant_name'] = $row['cmpt_other_name'];
					}

					$data['complt_comp_id'] = $row['complt_comp_id'];
					$data['complt_comp_id'] = $row['complt_comp_id'];
					$data['cmpt_other_name'] = $row['cmpt_other_name'];
					$data['cmpt_other_details'] = $row['cmpt_other_details'];
					$data['cmpt_other_email'] = $row['cmpt_other_email'];
					$data['cmpt_other_mobile'] = $row['cmpt_other_mobile'];
					$data['persecutor_type'] = $row['persecutor_type'];
					$data['persecutor_id'] = $row['persecutor_id'];

					if(!empty($row['persecutor_id'])){
						$data['persecutor_name'] = $this->getEmpnameByEmpNumber($row['persecutor_id']);
					}else{
						$data['persecutor_name'] = $row['persecutor_other_name'];
					}

					if(!empty($row['device_type'])){
					$data['deviceType'] = $row['device_type'];
					}else{
					$data['deviceType'] = 'WEB';
					}

					$data['perstr_comp_id'] = $row['perstr_comp_id'];
					$data['persecutor_other_name'] = $row['persecutor_other_name'];
					$data['persecutor_other_details'] = $row['persecutor_other_details'];
					$data['persecutor_other_email'] = $row['persecutor_other_email'];
					$data['persecutor_other_mobile'] = $row['persecutor_other_mobile'];
					$data['date_of_incident'] = date('d-m-Y',strtotime($row['date_of_incident']));
					$data['time_of_incident'] = $row['time_of_incident'];
					$data['nature_of_harassment'] = $row['nature_of_harassment'];
					$data['incident_short_note'] = $row['incident_short_note'];
					$data['incident_details'] = $row['incident_details'];
					$data['incident_location'] = $row['incident_location'];
					$data['witnesses'] = array();
					$data['witnessNames'] =  array();
					if(!empty($row['witnesses'])){
						$expWitIds = explode(',', $row['witnesses']);
						for($i=0;$i<sizeof($expWitIds);$i++){
							$data['witnessNames'][]= $this->getEmpnameByEmpNumber($expWitIds[$i]);
							$data['witnesses'][]  = $expWitIds[$i];
						}
					}
					$data['sequence_of_events'] = $row['sequence_of_events'];
					if(!empty($row['attachments'])){
						$attachmentsArr = explode(',',$row['attachments']);
						for($i=0;$i<sizeof($attachmentsArr);$i++){
					$data['attachments'][] = $attachmentsArr[$i];

							// $data['attachments_urls'][] = $base_url.'/symfony/upload/icc/'.$attachmentsArr[$i];
							$data['attachments_urls'][] = '/symfony/upload/icc/'.$attachmentsArr[$i];
						}

					}else{
						$data['attachments_urls'] = array();
						$data['attachments'] = array();
					}
					$data['comments'] = $row['comments'];
					$data['status'] = $row['status'];
					$data['reporting_person'] = $row['reporting_person'];
					$data['reporting_type'] = $row['reporting_type'];
					$data['reported_by'] = $row['reported_by'];
					$data['reported_on'] = $row['reported_on'];
					$data['submitted_by'] = $row['submitted_by'];
					$data['submitted_on'] = $row['submitted_on'];
					

				}while($row = mysqli_fetch_assoc($count));
					$data['status'] = 1;
					$data['complaint_details']=$data;
						
			}else{
					$data['status'] = 0;
					$data['complaint_details']=array();
			}

		return $data;
    }

    function scheduleNotifyList($userId,$complaintId)
    {
        $data=array();
        $userDetails = $this->getUserRoleByUserId($userId);
		$userRoleId = $userDetails['id'];
		$empNumber = $userDetails['empNumber'];

		$companyId = $this->getEmpCompanyId($empNumber);

		// echo $companyId;die();
  			
		$query = "SELECT c.* FROM `erp_icc_committee` as i LEFT JOIN erp_committee as c ON i.id=c.committee_Id WHERE i.company_id=$companyId AND (i.committee_status=1 AND i.is_deleted=0)";

		// echo $query;die();
		$count=mysqli_query($this->conn, $query);

		// echo mysqli_num_rows($count);die();

			if(mysqli_num_rows($count) > 0)
			{
				$row = mysqli_fetch_assoc($count);
					
				do { 						
				


					if(!empty($row['emp_id'])){
						$data['emp_id'] = $row['emp_id'];
						$data['emp_name'] = $this->getEmpnameByEmpNumber($row['emp_id']);
					}else{
						$data['emp_id'] = $row['id'];
						$data['emp_name'] = $row['name'];
					}

					
					$data1[]=$data;

				}while($row = mysqli_fetch_assoc($count));
					$data['notify_list']=$data1;
						
			}else{
					$data['notify_list']=array();
			}

			$cmpltquery = "SELECT c.* FROM `erp_complaints` as c WHERE c.id=$complaintId";

			$cmpltcount=mysqli_query($this->conn, $cmpltquery);
			if(mysqli_num_rows($cmpltcount) > 0)
			{
				$cmpltrow = mysqli_fetch_assoc($cmpltcount);
					
				do { 

					if(!empty($cmpltrow['complainant_id'])){
						$data2['emp_id'] = $cmpltrow['complainant_id'];
						$data2['emp_name'] = $this->getEmpnameByEmpNumber($cmpltrow['complainant_id']);
					}else{
						$data2['emp_id'] = null;
						$data2['emp_name'] = $cmpltrow['cmpt_other_name'];
					}						
				
					$data1[]=$data2;
					
					if(!empty($cmpltrow['persecutor_id'])){
						$data2['emp_id'] = $cmpltrow['persecutor_id'];
						$data2['emp_name'] = $this->getEmpnameByEmpNumber($cmpltrow['persecutor_id']);
					}else{
						$data2['emp_id'] = null;
						$data2['emp_name'] = $cmpltrow['persecutor_other_name'];
					}

					$data1[]=$data2;
				}while($row = mysqli_fetch_assoc($count));
					$data['notify_list']=$data1;
			}else{
					$data['notify_list']=array();
			}

			if(!empty($data1)){
				$data['status'] = 1;
					$data['notify_list']=$data1;
			}else{
					$data['status'] = 0;
					$data['notify_list']=array();
			}

		return $data;
    }

    function scheduleMeeting($values)
    {
    	// print_r($values);die();
    	$user_id = $values['user_id'];
    	$complaintId = $values['complaint_id'];
    	$notify = $values['notify'];
    	$purpose = $values['purpose'];

    	if(!empty($values['schedule_date'])){
    		$schedule_date =  date('Y-m-d',strtotime($values['schedule_date']));
    	}else{
    		$schedule_date =  date('Y-m-d');
    	}
    	$status = $values['status'];
    	// $acknowledge = $_POST['acknowledge'];
    	$data =array();

    	$userDetails = $this->getUserRoleByUserId($user_id);
    	// print_r($userDetails);die();
		$userRoleId = $userDetails['id'];

		if($status== self::PERSECUTOR_EXPLAINATION){

			if(!empty($notify)){
				for($i=0;$i<sizeof($notify);$i++){
					if(is_int($notify[$i])){

						$attendees_id = $notify[$i];
						$attdsql = "INSERT INTO erp_icc_concilation_meeting_attedees (complaint_id, attendees_id) VALUES (?,?)";
						$attdsqlstmt = mysqli_prepare($this->conn, $attdsql);
			 			mysqli_stmt_bind_param($attdsqlstmt, "ii" ,$complaintId, $attendees_id);
			 			// mysqli_stmt_execute($stmt);
			 			mysqli_execute($attdsqlstmt);
					}else{
						$attendees_id = $notify[$i];
						$attdsql = "INSERT INTO erp_icc_concilation_meeting_attedees (complaint_id, attendees_name) VALUES (?,?)";
						$attdsqlstmt = mysqli_prepare($this->conn, $attdsql);
			 			mysqli_stmt_bind_param($attdsqlstmt, "is" ,$complaintId, $attendees_id);
			 			// mysqli_stmt_execute($stmt);
			 			mysqli_execute($attdsqlstmt);
					}
				}
			}


		$submittedby = $userDetails['empNumber'];
        $submittedon = date('Y-m-d H:i:s');
       	$device_type = 'APP';
       	$statusId = self::SCHEDULE_MEETING;
       	$sql = "UPDATE erp_complaints SET status = $statusId WHERE id = $complaintId";

		// echo $sql;die();

			if (!empty($complaintId)) {

		    		if($result = mysqli_query($this->conn, $sql)){
		    			$logsql = "INSERT INTO erp_complaints_action_log (complaint_id, status,comment, submitted_by, submitted_on,schedule_date) VALUES (?,?,?,?,?,?)";
	     				$logstmt = mysqli_prepare($this->conn, $logsql);
			 			mysqli_stmt_bind_param($logstmt, "iisiss" ,$complaintId, $statusId,$purpose, $submittedby, $submittedon ,$schedule_date);
			 			// mysqli_stmt_execute($stmt);
			 			mysqli_execute($logstmt);

						$data['message']= "Meeting Scheduled successfully";
						$data['status']= 1;

					}else{
						$data['message']= "Meeting Scheduled  failed";
						$data['status']= 0;
					}
			}else{
				$data['message']= "Meeting Scheduled failed";
				$data['status']= 0;
			}
		}else{
			$data['message']= "Invalid  Request";
			$data['status']= 0;
		}
		return $data;
    }

    function poshNotificationsList($userId,$compy_id)
    {
        $data=array();
        $userDetails = $this->getUserRoleByUserId($userId);
		$userRoleId = $userDetails['id'];
		
		$empNumber = $userDetails['empNumber'];
  			
		$cmtquery = "SELECT c.emp_id FROM `erp_icc_committee` as i LEFT JOIN erp_committee as c ON c.committee_Id=i.id AND i.company_id=c.emp_cmpy_id WHERE i.company_id=$compy_id AND i.is_deleted=0";

		// echo $query;die();
		$cmtcount=mysqli_query($this->conn, $cmtquery);
		$committeeList =[];
		if(mysqli_num_rows($cmtcount) > 0)
		{
				$cmtIds = mysqli_fetch_assoc($cmtcount);
			if(!empty($cmtIds)){
	            do { 						
					array_push($committeeList, $cmtIds['emp_id']);
	            }while($cmtIds = mysqli_fetch_assoc($cmtcount));
	        }
	    }
	    // print_r($committeeList);die();
	    $statusArr = implode(',',array(self::COMPLAINT_SUBMIT,self::COMPLAINT_ACCEPTED,self::COMPLAINT_REJECTED,self::PERSECUTOR_EXPLAINATION,self::MEETING_OUTPUT_SETTLEMENT,self::MANAGEMENT_ACTION,self::MEETING_OUTPUT_NO_SETTLEMENT,self::INVESTIGATION_DRAFT,self::INVESTIGATION_SUBMIT));
	    $showCause = self::SHOW_CAUSE_NOTICE;
		// echo mysqli_num_rows($count);die();
		if(in_array($empNumber, $committeeList)){
		$query = "SELECT * FROM erp_complaints WHERE ((is_deleted = 0 AND complt_comp_id = $compy_id) AND status IN ($statusArr)) OR (status = $showCause AND persecutor_id =$empNumber) ORDER BY id ASC";
		}else if(!empty($empNumber)){
		$query = "SELECT * FROM erp_complaints WHERE status = $showCause AND persecutor_id =$empNumber ORDER BY id ASC";
		}

		$count=mysqli_query($this->conn, $query);

			if(mysqli_num_rows($count) > 0)
			{
				$row = mysqli_fetch_assoc($count);
					
				do { 						
				
					$data['id'] = $row['id'];
					$data['token_no'] = $row['token_no'];
					$data['complainant_type'] = $row['complainant_type'];

					if($row['complainant_type'] == self::COMP_TYPE_ORGANISATION){
						$data['complainant_type_name'] = 'ORGANISATION';
					}else if($row['complainant_type'] == self::COMP_TYPE_CONTRACT_OUTSOURCE){
						$data['complainant_type_name'] = 'CONTRACT/OUTSOURCE';
					}else if($row['complainant_type'] == self::COMP_TYPE_TRAINEE){
						$data['complainant_type_name'] = 'TRAINEE';
					}else if($row['complainant_type'] == self::COMP_TYPE_OTHER){
						$data['complainant_type_name'] = 'OTHER';
					}else{
						$data['complainant_type_name'] = '';
					}
					$data['complainant_id'] = $row['complainant_id'];

					if(!empty($row['complainant_id'])){
						$data['complainant_name'] = $this->getEmpnameByEmpNumber($row['complainant_id']);
					}else{
						$data['complainant_name'] = $row['cmpt_other_name'];
					}

					$data['persecutor_type'] = $row['persecutor_type'];

					if($row['persecutor_type'] == self::COMP_TYPE_ORGANISATION){
						$data['persecutor_type_name'] = 'ORGANISATION';
					}else if($row['persecutor_type'] == self::COMP_TYPE_CONTRACT_OUTSOURCE){
						$data['persecutor_type_name'] = 'CONTRACT/OUTSOURCE';
					}else if($row['persecutor_type'] == self::COMP_TYPE_TRAINEE){
						$data['persecutor_type_name'] = 'TRAINEE';
					}else if($row['persecutor_type'] == self::COMP_TYPE_OTHER){
						$data['persecutor_type_name'] = 'OTHER';
					}else{
						$data['persecutor_type_name'] = '';
					}
					$data['persecutor_id'] = $row['persecutor_id'];
					if(!empty($row['persecutor_id'])){
						$data['persecutor_name'] = $this->getEmpnameByEmpNumber($row['persecutor_id']);
					}else{
						$data['persecutor_name'] = $row['cmpt_other_name'];
					}


					$data['date_of_incident'] = date('d-m-Y',strtotime($row['date_of_incident']));
					if(!empty($row['time_of_incident'])){

					$data['time_of_incident'] = date('H:i',strtotime($row['time_of_incident']));
					}else{
					$data['time_of_incident'] = '';

					}
					$data['incident_details'] = $row['incident_details'];
					$data['incident_location'] = $row['incident_location'];
					$data['incident_short_note'] = $row['incident_short_note'];
					$data['nature_of_harassment'] = $row['nature_of_harassment'];
					if($row['nature_of_harassment'] == self::HARASSMENT_TYPE_VISUAL){
						$data['nature_of_harassment_name'] = 'VISUAL';
					}else if($row['nature_of_harassment'] == self::HARASSMENT_TYPE_PHYSICAL){
						$data['nature_of_harassment_name'] = 'PHYSICAL';
					}else if($row['nature_of_harassment'] == self::HARASSMENT_TYPE_OTHER){
						$data['nature_of_harassment_name'] = 'OTHER';
					}else{
						$data['nature_of_harassment_name'] = '';
					}	
					
					$data['status'] = $row['status'];
					if($row['status'] == self::COMPLAINT_DRAFT){
						$data['status_name'] = 'Draft';
					}elseif($row['status'] == self::COMPLAINT_SUBMIT){
						$data['status_name'] = 'Submitted';
					}elseif($row['status'] == 2){
						$data['status_name'] = 'Accepted';
					}elseif($row['status'] == 3){
						$data['status_name'] = 'Rejected';
					}elseif($row['status'] == 4){
						$data['status_name'] = 'Show Cause';
					}elseif($row['status'] == 5){
						$data['status_name'] = 'Respondent Explanation';
					}elseif($row['status'] == 6){
						$data['status_name'] = 'Settelment';
			        }elseif($row['status'] == 8){
			            $data['status_name'] = 'No Settelment';
			        }elseif($row['status'] == 7){
			            $data['status_name'] = 'Closed';
					}elseif($row['status'] == 9){
			            $data['status_name'] = 'Investigation Draft';
					}elseif($row['status'] == 10){
			            $data['status_name'] = 'Investigation Submitted';
					}

					if(!empty($row['submitted_by'])){
						$data['submitted_by'] = $this->getEmpnameByEmpNumber($row['submitted_by']);
					}else{
						$data['submitted_by'] = '';
					}
					
					$data1[] = $data;
					

				}while($row = mysqli_fetch_assoc($count));
					$data['status'] = 1;
					$data['notification_complaints']=$data1;
						
			}else{
					$data['status'] = 0;
					$data['notification_complaints']=array();
			}

		return $data;
    }

    function complaintLogAdd($complainantId, $status, $submittedby, $submittedon, $comments)
    {

   		// Prepare an insert statement

     	$sql = "INSERT INTO erp_complaints_action_log (complaint_id, status, submitted_by, submitted_on, comment) VALUES (?,?,?,?,?)";
     	$stmt = mysqli_prepare($this->conn, $sql);
		 mysqli_stmt_bind_param($stmt, "iiiss" ,$complainantId, $status, $submittedby, $submittedon, $comments);
		 // mysqli_stmt_execute($stmt);
		 mysqli_execute($stmt);
			
    }


    function getCreateTask($user_id,$task_title,$start_date,$due_date,$task_details,$task_priority,$task_type,$statusId,$assigned_to)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);
		$userRoleId = $userDetails['id'];
		
		$submittedby = $userDetails['empNumber'];
       $data = array();

       $submittedon = date('Y-m-d H:i:s');

       if($assigned_to != 0){
       	$assigned_to =$assigned_to;
       }else{
       	$assigned_to ='';
       }

       // Prepare an insert statement
		$sql = "INSERT INTO erp_assign_tasks (title,details,start_date,due_date,priority,assigned,assigned_to,assigned_by,assigned_on,status) VALUES (?,?,?,?,?,?,?,?,?,?)";

		// echo $sql;die();

		if ($stmt = mysqli_prepare($this->conn,$sql)) {

			mysqli_stmt_bind_param($stmt,"ssssiisssi",$task_title,$task_details,$start_date,$due_date,$task_priority,$task_type,$assigned_to,$submittedby,$submittedon,$statusId);
			if($output = mysqli_execute($stmt)) {
				$taskId = $this->conn->insert_id;
				// $permissionLog =	$this->permsnLogAdd($permissionId,$statusId,$submittedby,$submittedon,$date,$comment);
			$data['taskId']= $taskId;
			$data['status']= 1;
			}
		}
		return $data;
    }


    function getEditTask($user_id,$task_id,$task_title,$start_date,$due_date,$task_details,$task_priority,$task_type,$statusId,$assigned_to)
    {

    	// echo $task_id;die();
    	$userDetails = $this->getUserRoleByUserId($user_id);
		$userRoleId = $userDetails['id'];
		
		$submittedby = $userDetails['empNumber'];
       $data = array();

       $submittedon = date('Y-m-d H:i:s');

       if(!empty($task_id)){

	       // Prepare an insert statement
			$sql = "UPDATE erp_assign_tasks SET title = '".$task_title."', start_date = '".$start_date."', due_date = '".$due_date."', details = '".$task_details."', priority = $task_priority, assigned = $task_type, assigned_to = $assigned_to, status=$statusId WHERE id = $task_id";

			// echo $sql;die();

			// echo $sql;die();

			if($result = mysqli_query($this->conn, $sql)){
				$data['message']= "Task updated successfully";
				$data['status']= 1;

			}else{
				$data['message']= "Task updated failed";
				$data['status']= 0;
			}
       	}else{
				$data['message']= "Task updated failed";
				$data['status']= 0;
       	}
		return $data;
    }

    function permsnAdd($date,$fromTime,$toTime,$reason,$statusId,$submittedby,$submittedon,$comment)
    {
    	$userDetails = $this->getUserRoleByUserId($submittedby);
		$userRoleId = $userDetails['id'];
		
		$submittedbyEmp = $userDetails['empNumber'];
       $data = array();

       $newDate = date('Y-m-d',strtotime($date));
       $frmhr =explode(':',$fromTime);
       $tohr =explode(':',$toTime);
       $duration = abs($frmhr[0] - $tohr[0]);
       // echo $duration;die();

       // Prepare an insert statement
		$sql = "INSERT INTO erp_permission (date,from_time,to_time,duration,reason_id,status_id,submitted_by,submitted_on) VALUES (?,?,?,?,?,?,?,?)";

		// echo $sql;die();

		if ($stmt = mysqli_prepare($this->conn,$sql)) {

			mysqli_stmt_bind_param($stmt,"ssssiiis",$newDate,$fromTime,$toTime,$duration,$reason,$statusId,$submittedbyEmp,$submittedon);
			if($output = mysqli_execute($stmt)) {
				$permissionId = $this->conn->insert_id;

				// echo $permissionId;die();
				$permissionLog = $this->permsnLogAdd($permissionId,$statusId,$submittedbyEmp,$submittedon,$date,$comment);
			$data['status']= $permissionLog;
			}
		}else{
			$data['status']= false;
		}
		return $data;
    }

    function permsnAssign($date,$fromTime,$toTime,$reason,$statusId,$submittedby,$submittedon,$comment)
    {
  //   	$userDetails = $this->getUserRoleByUserId($submittedby);
		// $userRoleId = $userDetails['id'];
		
		// $submittedbyEmp = $userDetails['empNumber'];
		$submittedbyEmp = $submittedby;
       $data = array();

       $newDate = date('Y-m-d',strtotime($date));

       $frmhr =explode(':',$fromTime);
       $tohr =explode(':',$toTime);
       $duration = abs($frmhr[0] - $tohr[0]);

       // Prepare an insert statement
		$sql = "INSERT INTO erp_permission (date,from_time,to_time,duration,reason_id,status_id,submitted_by,submitted_on) VALUES (?,?,?,?,?,?,?,?)";

		// echo $sql;die();

		if ($stmt = mysqli_prepare($this->conn,$sql)) {

			mysqli_stmt_bind_param($stmt,"ssssiiis",$newDate,$fromTime,$toTime,$duration,$reason,$statusId,$submittedbyEmp,$submittedon);
			if($output = mysqli_execute($stmt)) {
				$permissionId = $this->conn->insert_id;
				$permissionLog =	$this->permsnLogAdd($permissionId,$statusId,$submittedbyEmp,$submittedon,$date,$comment);
			$data['status']= $permissionLog;
			}
		}
		return $data;
    }
    function permsnUpdate($permissionId,$statusId,$user_id,$submittedon,$comment)
    {
    	// echo $permissionId;die;
    	$userDetails = $this->getUserRoleByUserId($user_id);
		$userRoleId = $userDetails['id'];
		$submittedby = $userDetails['empNumber'];

		$supervisior = $this->isSupervisor($submittedby); //2

       $data = array();

       $updatesql = "UPDATE erp_permission SET status_id=$statusId WHERE id=$permissionId";

       // echo $updatesql;die();
       if($result2 = mysqli_query($this->conn, $updatesql)){
				$this->permsnLogAdd($permissionId,$statusId,$submittedby,$submittedon,$date=null,$comment);		
			$data['log'] = "Updated";
			$data['status']=1;

		}else{
			$data['log'] = "Failed";
			$data['status']=0;
		}

		return $data;
    }

    function getPrmsnLstByEmp($user_id){
    	$userDetails = $this->getUserRoleByUserId($user_id);
        $employee = $userDetails['empNumber'];
    	$data = array();
    	$getQuery = "SELECT * FROM erp_permission WHERE submitted_by = $employee order by id desc";
    	$prmsnObj = mysqli_query($this->conn, $getQuery);
		if(mysqli_num_rows($prmsnObj)>0){
			$row = mysqli_fetch_assoc($prmsnObj);
			do{
				$data['id'] = $row['id'];
				$data['date'] = $row['date'];
				$data['from_time'] = $row['from_time'];
				$data['to_time'] = $row['to_time'];
				$reason = $this->permissionReasonById($row['reason_id']);
				$data['reason'] = $reason;

				if($row['status_id'] == 1){
					$status = "New";
				}elseif($row['status_id'] == 2){
					$status = "New";
				}elseif($row['status_id'] == 3){
					$status = "Hold";
				}elseif($row['status_id'] == 8){
					$status = "Approved by supervisor";
				}elseif($row['status_id'] == 9){
					$status = "Approved by HOD";
				}elseif($row['status_id'] == 10){
					$status = "Approved by HR";
				}elseif($row['status_id'] == 7){
					$status = "Reject";
				}else{
					$data['status'] = "--";
				}
				$data['status_id'] =$row['status_id'];
				$data['status'] =$status;
				
				$submittedBy = $this->getEmpnameByEmpNumber($row['submitted_by']);
				$data['submitted_by'] = $submittedBy;
				$data['submitted_on'] = $row['submitted_on'];

				$data1[] = $data;
			}while($row = mysqli_fetch_assoc($prmsnObj));
				$data['prmsnList'] = $data1;
				$data['status'] = 1;
			
		}else{
			$data['status'] = 0;
		}
    	return $data;
    }

    function subPrmsnList($user_id){
    	$userDetails = $this->getUserRoleByUserId($user_id);
        $supervisor = $userDetails['empNumber'];

    	$roleSupervisor = $this->isSupervisor($supervisor);
    	if($roleSupervisor !=0){
    	$subObj = $this->subordinateByEmpList($supervisor);
    	}else{
    	$subObj = array();

    	}
    	

		$query1="SELECT * FROM `hs_hr_employee` where emp_number=$supervisor";
		$count1=mysqli_query($this->conn, $query1);
		$row1=mysqli_fetch_assoc($count1);
		$work_station = $row1['work_station'];

    	// print_r($subObj);die();
    	$data = array();
    	if(!empty($supervisor)){
    	if(!empty($subObj['emplist']) && $roleSupervisor !=0){

			for ($i=0; $i < sizeof($subObj['emplist']) ; $i++) { 
			    $empList[] = $subObj['emplist'][$i]['emp_number'];
			        	//to convert Array into string the following implode method is used
			    $empLists = implode(',', $empList);
			        	
			}
			// echo $empLists;die();
			// $query = "SELECT ep.* FROM erp_permission AS ep LEFT JOIN erp_permission_action_log AS epLog ON epLog.permission_id = ep.id  WHERE epLog.submitted_by IN ($empLists) AND epLog.status_id =1 order by id desc";	
			$query = "SELECT p.*,r.reason FROM `erp_permission` as p LEFT JOIN erp_reason as r ON p.reason_id=r.id WHERE p.submitted_by IN($empLists) AND p.status_id IN(2) ORDER BY p.id desc";	
			  
		}else if($userDetails['id'] == 17 && !empty($work_station)){

			$query = "SELECT p.*,r.reason FROM `erp_permission` as p 
			LEFT JOIN erp_reason as r ON  p.reason_id=r.id
			LEFT JOIN hs_hr_employee as e ON p.submitted_by=e.emp_number AND e.work_station=$work_station  WHERE p.status_id IN(8) AND e.work_station=$work_station ORDER BY p.id desc";	

		}elseif($userDetails['id'] == 6){
			$query = "SELECT p.*,r.reason FROM `erp_permission` as p LEFT JOIN erp_reason as r ON p.reason_id=r.id WHERE p.status_id IN(9) ORDER BY p.id desc";	
		}elseif($userDetails['id'] == 30){

			$query = "SELECT p.*,r.reason FROM `erp_permission` as p LEFT JOIN erp_reason as r ON p.reason_id=r.id WHERE p.status_id=10 ORDER BY p.id desc";		
		}else{

			$query = "SELECT p.*,r.reason FROM `erp_permission` as p LEFT JOIN erp_reason as r ON p.reason_id=r.id WHERE p.status_id IN(2,3,7,8,9,10) AND p.submitted_by=$supervisor ORDER BY p.id desc";		
		}
			$count=mysqli_query($this->conn, $query);

			if(mysqli_num_rows($count) > 0){
				$row=mysqli_fetch_assoc($count);
										
					do{ 		

						$data['id'] = $row['id'];
						$data['date'] = $row['date'];
						$data['from_time'] = $row['from_time'];
						$data['to_time'] = $row['to_time'];
						$data['status_id'] = $row['status_id'];
						$reason = $this->permissionReasonById($row['reason_id']);
						$data['reason'] = $reason;

						if($row['status_id'] == 1){
							$data['status'] = "Pending for approval";
						}else if($row['status_id'] == 2){
							$data['status'] = "New";
						}else if($row['status_id'] == 3){
							$data['status'] = "Hold";
						}elseif($row['status_id'] == 7){
							$data['status'] = "Reject";
						}elseif($row['status_id'] == 8){
							$data['status'] = "Approved by supervisor";
						}elseif($row['status_id'] == 9){
							$data['status'] = "Approved by HOD";
						}elseif($row['status_id'] == 10){
							$data['status'] = "Approved by HR";
						}else{
							$data['status'] = "Pending for approval";
						}
				
						$submittedBy = $this->getEmpnameByEmpNumber($row['submitted_by']);
						$data['submitted_by'] = $submittedBy;
						$data['submitted_on'] = $row['submitted_on'];

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
											
						$data['permList']=$data1;
											
			}else{

						$data['permList']=array();
			}

		}else{
			$data['permList']=array();
		}  
    	return $data;
      
    }



 //ActionlogAdd
function permsnLogAdd($permissionId,$statusId,$submittedby,$submittedon,$date,$comment)
    {

   		// Prepare an insert statement

    	$source = 1;     
     	$sql = "INSERT INTO erp_permission_action_log (permission_id,status_id,submitted_by,submitted_on,comment) VALUES (?,?,?,?,?)";
     	$stmt = mysqli_prepare($this->conn, $sql);
		 mysqli_stmt_bind_param($stmt, "iiiss" ,$permissionId,$statusId,$submittedby,$submittedon,$comment);
		 // mysqli_stmt_execute($stmt);
		 mysqli_execute($stmt);
			
    }






/*	function leaveDetails($emp_number)
	{
		$data= array();
		$query="SELECT ent.no_of_days as leave_alloted,ent.days_used as leave_used,ent.leave_type_id,l.name FROM erp_leave_entitlement ent LEFT JOIN erp_leave_type l ON l.id = ent.leave_type_id WHERE ent.emp_number = $emp_number AND ent.from_date = '2020-01-01' AND ent.to_date = '2020-12-31'";
		
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
			$row=mysqli_fetch_assoc($count);
			do{
				//$data['leave_alloted'] = $row['leave_alloted'];
				//$data['leave_used']=$row['leave_used'];
				$data['leave_type']=$row['name'];
				$data['leave_balance'] = $row['leave_alloted'] - $row['leave_used'];

				$data1[]= $data;
			}while($row = mysqli_fetch_assoc($count));
			$data['leaveDetails']=$data1;
			$data['status']=1;
		}else{
			$data['status']=0;
		}
		return $data;  	
	}*/

	function leaveDetails($emp_number)
    {	
    	$fromDtae = Date("Y")."-01-01";
    	$toDtae = Date("Y")."-12-31";
        $data= array();
        $query="SELECT ent.no_of_days as leave_alloted,ent.days_used as leave_used,ent.leave_type_id,l.name,CONCAT(emp.emp_firstname,' ',emp.emp_lastname) as employee_name , emp.emp_birthday as emp_birthday, emp.joined_date as joined_date FROM erp_leave_entitlement ent LEFT JOIN erp_leave_type l ON l.id = ent.leave_type_id LEFT JOIN hs_hr_employee emp ON emp.emp_number = ent.emp_number WHERE ent.emp_number = $emp_number AND ent.from_date = '$fromDtae' AND ent.to_date = '$toDtae'";
        
        $count=mysqli_query($this->conn, $query);
    if(mysqli_num_rows($count) > 0)
    {
        $row=mysqli_fetch_assoc($count);
        do{
            /*$data['leave_alloted'] = $row['leave_alloted'];
            $data['leave_used']=$row['leave_used'];*/
            
            $empBirthday =  date("m-d",strtotime($row['emp_birthday']));
            $joinedDate =  date("m-d",strtotime($row['joined_date']));
            if($empBirthday == date('m-d')){
            	$data['is_birthday']= 1;
            	$data['is_anniversary']= 0;
            	$data['employee_name']=$row['employee_name'];
            }else if($joinedDate == date('m-d')){
            	$data['is_anniversary']= 1;
            	$data['is_birthday']= 0;
            	$data['employee_name']=$row['employee_name'];
            }
            else {
            	$data['is_anniversary']= 0;
            	$data['is_birthday']= 0;
            	$data['employee_name']=$row['employee_name'];
            }
            
            $data['leave_type']=$row['name'];
            $data['leave_balance'] = $row['leave_alloted'] - $row['leave_used'];

            $data1[]= $data;
        }while($row = mysqli_fetch_assoc($count));
        $data['leaveDetails']=$data1;
        $data['status']=1;
    }else{
        $data['status']=0;
    }
    return $data;      
}

// pavan start

     function employeesPunchInCount()
    {
        $data=array();
  			
		// $query="SELECT * FROM `erp_attendance_record` WHERE state='PUNCHED IN'   GROUP BY employee_id";
		$query="SELECT * FROM `erp_attendance_record` WHERE state='PUNCHED IN' AND  DATE(`punch_in_user_time`) = CURDATE()   GROUP BY employee_id";
		$count=mysqli_query($this->conn, $query);

		$recordsCount = mysqli_num_rows($count);

		if(mysqli_num_rows($count) > 0)
		{

				$row=mysqli_fetch_assoc($count);
				
					do { 						
						
						$data['employee_id'] = $row['employee_id'];
						$data['employee_Name'] = $this->getEmpnameByEmpNumber($row['employee_id']);
						$data['punch_in_utc_time'] = $row['punch_in_utc_time'];
						$data['punch_in_note'] = $row['punch_in_note'];
						$data['punch_in_time_offset'] = $row['punch_in_time_offset'];
						$data['punch_in_user_time'] = $row['punch_in_user_time'];
						$data['punch_out_note'] = $row['punch_out_note'];
						$data['punch_out_time_offset'] = $row['punch_out_time_offset'];
						$data['punch_out_user_time'] = $row['punch_out_user_time'];
						$data['state'] = $row['state'];

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['employeesPunchinCount']=$recordsCount;
						$data['employeesPunchinList']=$data1;
						$data['status'] = 1;
					
		}else{
			$data['status'] = 0;
		}
		return $data;
    }


    function meetingRoomsList()
    {
        $data=array();
  			
		$query="SELECT * FROM `erp_meeting_room` where is_deleted=0";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				
					do { 						
						
					
						$data['id'] = $row['id'];
						$data['name'] = $row['name'];
						$data['location_id'] = $row['location_id'];
						$data['plant_id'] = $row['plant_id'];
						$data['department_id'] = $row['department_id'];
						$data['capacity'] = $row['capacity'];
						$data['seating'] = $row['seating'];
						$data['projector'] = $row['projector'];
						$data['tv'] = $row['tv'];
						$data['conference_phone'] = $row['conference_phone'];
						$data['power_sockets'] = $row['power_sockets'];
						$data['white_board'] = $row['white_board'];
						$data['smart_board'] = $row['smart_board'];
						$data['is_executive_room'] = $row['is_executive_room'];
						$data['is_deleted'] = $row['is_deleted'];

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['meetingRoomsList']=$data1;
						$data['status'] = 1;
					
		}else{
			$data['status'] = 0;
		}
		return $data;
    }

    function getTaskList($user_id)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);
		$userRoleId = $userDetails['id'];
		
		$empId = $userDetails['empNumber'];

        $data=array();
  			
		$query="SELECT * FROM `erp_assign_tasks` as t where (t.assigned_to=$empId OR t.assigned_by=$empId) and t.is_deleted=0 order by t.id desc";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				
					do { 						
					
						$data['id'] = $row['id'];
						$data['title'] = $row['title'];
						$data['details'] = $row['details'];
						$data['details'] = $row['details'];
						$data['start_date'] =date('d-m-Y',strtotime($row['start_date']));
						$data['due_date'] =date('d-m-Y',strtotime($row['due_date']));
						$data['priority'] = $row['priority'];

						if($row['priority'] == 1){
							$data['priority_name'] = 'Low';
						}else if($row['priority'] == 2){
							$data['priority_name'] = 'Medium';
						}else if($row['priority'] == 3){
							$data['priority_name'] = 'High';
						}else if($row['priority'] == 4){
							$data['priority_name'] = 'Urgent';
						}else{
							$data['priority_name'] = 'Immediately';
						}

						if($row['assigned']==0){
						$data['assigned'] = "Self";
						}else if($row['assigned']==1){
						$data['assigned'] = "To Employee";
						}

						if(!empty($row['assigned_to'])){
						$data['assigned_to'] = $this->getEmpnameByEmpNumber($row['assigned_to']);
						}else{
						$data['assigned_to'] = "";
						}

						// $data['assigned_to'] = $row['assigned_to'];
						$data['assigned_by'] = $row['assigned_by'];
						$data['assigned_by_name'] = $this->getEmpnameByEmpNumber($row['assigned_by']);
						$data['assigned_on'] = $row['assigned_on'];
						$data['status'] = $row['status'];

						if($row['status'] == 0){
							$data['status_name'] = 'New';
						}else if($row['status'] == 1){
							$data['status_name'] = 'Started';
						}else if($row['status'] == 2){
							$data['status_name'] = 'work in progress';
						}else if($row['status'] == 3){
							$data['status_name'] = 'completed';
						}else if($row['status'] == 4){
							$data['status_name'] = 'closed';
						}else{
							$data['status_name'] = 'QA Review';
						}

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['tasksList']=$data1;
						$data['status'] = 1;
					
		}else{
			$data['status'] = 0;
			$data['tasksList']= array();
		}
		return $data;
    }



     function bookMeetingRoom($user_id,$meetingRoomTitle,$organiser,$meetingRoomId,$fromDate1,$toDate1,$fromTime,$toTime,$all_day,$status_id,$submitted_on,$employee_ids,$vendor_ids,$customer_ids)
    {
    	
    	if(!empty($employee_ids)){
    	$employeeIds = explode(',', $employee_ids);
    	}else{
    	$employeeIds = '';
    	}

    	if(!empty($vendor_ids)){
    	$vendorIds = explode(',', $vendor_ids);
    	}else{
    	$vendorIds = '';
    	}

    	if(!empty($customer_ids)){
    	$customerIds = explode(',', $customer_ids);
    	}else{
    	$customerIds = '';
    	}

    	 $userDetails = $this->getUserRoleByUserId($user_id);
         $empNumber = $userDetails['empNumber'];

    	// print_r($employeeIds);die();
         $fromDate = date('Y-m-d',strtotime($fromDate1));
         $toDate = date('Y-m-d',strtotime($toDate1));

        $data=array();

		$sql = "INSERT INTO erp_book_meeting_room (meeting_room_id,meeting_room_title,booked_by_id,meeting_organiser,from_date,to_date,from_time,to_time,all_day,submitted_on,status) VALUES (?,?,?,?,?,?,?,?,?,?,?)";

		if($stmt = mysqli_prepare($this->conn, $sql)){

			 mysqli_stmt_bind_param($stmt,"isiissssisi" ,$meetingRoomId, $meetingRoomTitle, $empNumber, $organiser, $fromDate, $toDate, $fromTime, $toTime, $all_day, $submitted_on, $status_id);

					    if(mysqli_stmt_execute($stmt)){

				    		$query="SELECT MAX(id) AS meetingId  FROM erp_book_meeting_room";
							$count=mysqli_query($this->conn, $query);


								if(mysqli_num_rows($count) > 0)	{

									$row=mysqli_fetch_assoc($count);
									$data['meetingId'] = $row['meetingId'];

									$bookingID = $row['meetingId'];
							// echo $bookingID;die();

									if(!empty($employeeIds)){
										for($e=0;$e<sizeof($employeeIds);$e++){
											$empId = $employeeIds[$e];
											$vendorId = 0;
											$clientId = 0;
											$status = 0;
									$empsql = "INSERT INTO erp_meeting_room_employee (meeting_room_id,booking_id,emp_number,vendor_id,client_id,submitted_on,status) VALUES (?,?,?,?,?,?,?)";
										if($empstmt = mysqli_prepare($this->conn, $empsql)){
											 mysqli_stmt_bind_param($empstmt,"iiiiisi",$meetingRoomId,$bookingID,$empId,$vendorId,$clientId,$submitted_on,$status);
											if(mysqli_stmt_execute($empstmt)){
												$data['status']=1;
											}else{
												// echo "Vendor inserted failed";
												$data['status']=0;
											}
										}else{
										$data['status']=0;
										}

										}
									}

									if(!empty($vendorIds)){
										for($v=0;$v<sizeof($vendorIds);$v++){
											$empId = 0;
											$vendorId = $vendorIds[$v];
											$clientId = 0;
											$status = 0;
									$vensql = "INSERT INTO erp_meeting_room_employee (meeting_room_id,booking_id,emp_number,vendor_id,client_id,submitted_on,status) VALUES (?,?,?,?,?,?,?)";
										if($venstmt = mysqli_prepare($this->conn, $vensql)){
											 mysqli_stmt_bind_param($venstmt,"iiiiisi" ,$meetingRoomId,$bookingID,$empId,$vendorId,$clientId,$submitted_on,$status);
											if(mysqli_stmt_execute($venstmt)){
												// echo "Vendor inserted success";
												$data['status']=1;
											}else{
												// echo "Vendor inserted failed";
												$data['status']=0;
											}
										}else{
										// echo "Vendor inserted failed";
											$data['status']=0;
										}

										}
									}

									if(!empty($customerIds)){
										for($c=0;$c<sizeof($customerIds);$c++){
											$empId = 0;
											$vendorId = 0;
											$clientId = $customerIds[$c];
											$status = 0;
									$cutsql = "INSERT INTO erp_meeting_room_employee (meeting_room_id,booking_id,emp_number,vendor_id,client_id,submitted_on,status) VALUES (?,?,?,?,?,?,?)";
										if($custstmt = mysqli_prepare($this->conn, $cutsql)){
											 mysqli_stmt_bind_param($custstmt,"iiiiisi" ,$meetingRoomId,$bookingID,$empId,$vendorId,$clientId,$submitted_on,$status);
											if(mysqli_stmt_execute($custstmt)){
												$data['status']=1;
											}else{
												// echo "Vendor inserted failed";
												$data['status']=0;
											}
										}else{
										$data['status']=0;
										}

										}
									}

									

											
											
								}

				        $data['bookMeetingRoom'] = $data['meetingId'];
				        $data['status']=1;
					    } else{
					        $data['status']=0;
					    }

		}else{
				    $data['status']=0;
					}

		
		return $data;
    }

    function getBookMeetingRoom($meeting_id)
    {
        $data=array();
  			
		$query="SELECT * FROM `erp_book_meeting_room` where id=$meeting_id";
		$count=mysqli_query($this->conn, $query);



		if(mysqli_num_rows($count) > 0)
		{

			$query1="SELECT * FROM `erp_meeting_room_employee` where booking_id=$meeting_id";
			$count1=mysqli_query($this->conn, $query1);

				$row=mysqli_fetch_assoc($count);
				
					do { 						
					
						$data['id'] = $row['id'];
						$data['meeting_room_id'] = $row['meeting_room_id'];
						$data['meeting_room_title'] = $row['meeting_room_title'];
						$data['booked_by_id'] = $row['booked_by_id'];
						$data['meeting_organiser'] = $row['meeting_organiser'];
						$data['from_date'] = $row['from_date'];
						$data['to_date'] = $row['to_date'];
						$data['from_time'] = $row['from_time'];
						$data['to_time'] = $row['to_time'];
						$data['all_day'] = $row['all_day'];
						$data['submitted_on'] = $row['submitted_on'];
						$data['status'] = $row['status'];

						// $data1[] = $data;

					}while($row = mysqli_fetch_assoc($count));
						$data['getBookMeetingRoom']=$data;


						$row1=mysqli_fetch_assoc($count1);
						do{

						$data3['id'] = $row1['id'];
						$data3['meetingId'] = $row1['meeting_room_id'];
						$data3['bookingId'] = $row1['booking_id'];
						$data3['empNumber'] = $row1['emp_number'];
						$data3['vendorId'] = $row1['vendor_id'];
						$data3['clientId'] = $row1['client_id'];
						$data3['submittedOn'] = $row1['submitted_on'];
						$data3['Empstatus'] = $row1['status'];

						$data2[] = $data3;
						}while($row1 = mysqli_fetch_assoc($count1));

						$data['meetingRoomEmployee']=$data2;
						$data['status'] = 1;
					
				

				
					
		}else{
			$data['status'] = 0;
		}
		return $data;
    }

    function checkBookMeetingEmployee($user_id,$fromDate,$toDate,$fromTime,$toTime,$all_day)
    {
        $data=array();
  			
		$query = "SELECT mre.* FROM `erp_meeting_room_employee` as mre LEFT JOIN erp_book_meeting_room as bm ON mre.booking_id=bm.id WHERE (bm.from_date >='".$fromDate."' AND bm.to_date <= '".$toDate."') AND ((bm.from_time >='".$fromTime."' OR bm.to_time >= '".$toTime."') AND (bm.from_time >='".$toTime."' OR bm.to_time >= '".$fromTime."'))";

		// echo $query;die();
		$count=mysqli_query($this->conn, $query);

		// echo mysqli_num_rows($count);die();

			if(mysqli_num_rows($count) > 0)
			{
				$row = mysqli_fetch_assoc($count);
					
				do { 						
				
					$data['id'] = $row['id'];
					$data['emp_number'] = $row['emp_number'];
					$data['client_id'] = $row['client_id'];
					$data['vendor_id'] = $row['vendor_id'];

					$data1[] = $data;

				}while($row = mysqli_fetch_assoc($count));
					$data['status'] = 1;
					$data['booked_employee']=$data1;
						
			}else{
				$data['status'] = 0;
				$data['booked_employee']=array();
			}

		return $data;
    }



    function getAddVisitorPass($user_id,$contact_to,$vehicle_number,$members,$names,$pass_ids,$phone,$address)
    {
		$userDetails = $this->getUserRoleByUserId($user_id);
		
		$employeedetails = $this->employeeDetails($contact_to);

		$work_station = $employeedetails['work_station'];
		$userRoleId = $userDetails['id'];

		// echo $emp_number;die();
		$visit_date = date('Y-m-d');
		$visit_time = date('H:i:s');
		$created_by = $userDetails['empNumber'];
		$created_on = date('Y-m-d H:i:s');
		$status_id = 10;

		$visitorNames = explode(',',$names);
		$visitorIds = explode(',',$pass_ids);
		$comment = "IN";

		// print_r($visitorIds);die();
        $data=array();

		if(sizeof($visitorNames) == sizeof($visitorIds)){
			// echo "both are equal";die();

				$sql = "INSERT INTO erp_visitor (vehicle_number,members,visit_date,visit_time,address,status_id,mobile,created_by,created_on,contact_to,deparment_id) VALUES (?,?,?,?,?,?,?,?,?,?,?)";

				if($stmt = mysqli_prepare($this->conn, $sql)){

					mysqli_stmt_bind_param($stmt,"sssssisisii" ,$vehicle_number,$members,$visit_date,$visit_time,$address,$status_id,$phone,$created_by,$created_on,$contact_to,$work_station);

					if(mysqli_stmt_execute($stmt)){

							$Vis_Id = $this->conn->insert_id;

							if(!empty($members)){
									for ($vn=0,$vp=0; $vn < sizeof($visitorNames),$vp < sizeof($visitorIds); $vn++,$vp++) { 
									$logsql = "INSERT INTO erp_visitor_contacts (visitor_id,name,phone,pass_id) VALUES (?,?,?,?)";
									$cntstmt = mysqli_prepare($this->conn, $logsql);
									mysqli_stmt_bind_param($cntstmt,"isss" ,$Vis_Id,$visitorNames[$vn],$phone,$visitorIds[$vp]);
									mysqli_stmt_execute($cntstmt);

									}
							}

							$logsql = "INSERT INTO erp_visitor_action_log (visitor_id,status_id,submitted_by,submitted_on,comment) VALUES (?,?,?,?,?)";
							$logstmt = mysqli_prepare($this->conn, $logsql);
							mysqli_stmt_bind_param($logstmt,"iisss" ,$Vis_Id,$status_id,$created_by,$created_on,$comment);
							mysqli_stmt_execute($logstmt);

							$data['visId'] = $Vis_Id;
							$data['message']="Visitor Insertion successfully completed";
							$data['status']=1;
					} else{
							$data['message']="Visitor Insertion binding failed";
							$data['status']=0;
					}

				}else{
					$data['message']="Visitor Insertion failed";
					$data['status']=0;
				}
		}else{
			// echo "both are not equal";die();
			$data['message']="Visitor names and ids not equal";
			$data['status']=0;
		}

       

		return $data;
    }

    function getCheckOutVisitor($user_id,$visitor_id)
    {
		$userDetails = $this->getUserRoleByUserId($user_id);
		$userRoleId = $userDetails['id'];
		
		$created_by = $userDetails['empNumber'];
		$created_on = date('Y-m-d H:i:s');
		$status_id = 11;
		$comment = 'OUT';

		// print_r($visitorIds);die();
        $data=array();

        $sql = "UPDATE erp_visitor SET status_id=$status_id WHERE id=$visitor_id";
		$result = mysqli_query($this->conn, $sql);
		
		if($result){

				$logsql = "INSERT INTO erp_visitor_action_log (visitor_id,status_id,submitted_by,submitted_on,comment) VALUES (?,?,?,?,?)";
				$logstmt = mysqli_prepare($this->conn, $logsql);
				mysqli_stmt_bind_param($logstmt,"iisss" ,$visitor_id,$status_id,$created_by,$created_on,$comment);
				mysqli_stmt_execute($logstmt);

				$data['visitor_action_log'] = $this->conn->insert_id;
				$data['message'] = "Visitor successfully checked out";
				// $query1="SELECT v.*,vl.id as visitiorContactId,vl.contact_ids,vl.name,vl.phone,vl.pass_id FROM erp_visitor as v LEFT JOIN erp_visitor_contacts as vl on v.id=vl.visitor_id WHERE v.status_id=11";
				// $count1=mysqli_query($this->conn, $query1);
				// $row=mysqli_fetch_assoc($count1);
				// do { 						
				// $data['id'] = $row['id'];
				// $data1[] = $data;
				// }while($row = mysqli_fetch_assoc($count));
				// $data['visitor_checkout_list']=$data1;

				$data['status']=1;

		}else{
		    $data['status']=0;
		    $data['message'] = "Visitor checked out failed";
		}

		

       

		return $data;
    }

    function getVisitorsList($user_id)
    {
		$userDetails = $this->getUserRoleByUserId($user_id);
		$userRoleId = $userDetails['id'];
		$created_by = $userDetails['empNumber'];
		$employeedetails = $this->employeeDetails($userDetails['empNumber']);
		$work_station = $employeedetails['work_station'];

		// echo $work_station;die();
        $data=array();
        // $query="SELECT v.*,vc.visitor_id as vid,vc.contact_ids as vcontact,vc.name,vc.phone,vc.pass_id FROM `erp_visitor` as v LEFT JOIN erp_visitor_contacts as vc on v.id=vc.visitor_id WHERE v.status_id IN(10,11)";
        if($userRoleId == 6 || $userRoleId == 30){
        $query="SELECT * FROM `erp_visitor` as v  WHERE v.status_id=10 ORDER BY id DESC";
        }else{
        $query="SELECT * FROM `erp_visitor` as v  WHERE v.status_id=10 AND v.deparment_id=$work_station ORDER BY id DESC";
        }

		$count=mysqli_query($this->conn, $query);

		// print_r($count);die();

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				
					do { 
					
						$data['id'] = $row['id'];
						$data['vehicle_number'] = $row['vehicle_number'];
						$data['members'] = $row['members'];
						$visitorId = $row['id'];
						// $membersDetails =array();
						$queryContacts="SELECT vc.*,v.contact_to FROM `erp_visitor_contacts` as vc LEFT JOIN erp_visitor as v ON v.id=vc.visitor_id  WHERE vc.visitor_id=$visitorId order by id DESC";

						$countCont=mysqli_query($this->conn, $queryContacts);
						$membersDetails =array();
						$passidDetails =array();
						if(mysqli_num_rows($countCont) > 0)
						{
							$rowCont=mysqli_fetch_assoc($countCont);
							do { 
								// print_r($rowCont);die();
								if(!empty($rowCont['contact_ids'])){
									$memberName = $this->getEmpnameByEmpNumber($rowCont['contact_ids']);
									$passID = $rowCont['pass_id'];
									array_push($membersDetails, $memberName);
									array_push($passidDetails, $passID);
								}else{
									$memberName = $rowCont['name'];
									$passID = $rowCont['pass_id'];
									array_push($membersDetails, $memberName);
									array_push($passidDetails, $passID);
								}
							}while($rowCont = mysqli_fetch_assoc($countCont));



							
						}
						$data['names'] = implode(',', $membersDetails);
						$data['pass_ids'] = implode(',', $passidDetails);

						$data['visit_date'] = $row['visit_date'];
						$data['visit_time'] = $row['visit_time'];
						$data['status_id'] = $row['status_id'];
						if($row['status_id'] == 10){
						$data['status_msg'] = 'IN';
						}else if($row['status_id'] == 11){
						$data['status_msg'] = 'OUT';
						}
						$data['created_by'] = $row['created_by'];
						$data['created_on'] = $row['created_on'];
						$data['address'] = $row['address'];
						$data['mobile'] = $row['mobile'];
						$data['contact_to'] = $this->getEmpnameByEmpNumber($row['contact_to']);
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['message']="All visitors list";
						$data['visitors_list']=$data1;
						$data['status'] = 1;
					
		}else{
			$data['status'] = 0;
			$data['message']="Request wrong";
			$data['visitors_list']=array();
		}


		return $data;
    }

    function getBookMeetingRoomsList()
    {
        $data=array();
  			
		$query="SELECT * FROM `erp_book_meeting_room` where status=1";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				
					do { 						
						
					
						$data['id'] = $row['id'];
						$data['meeting_room_id'] = $row['meeting_room_id'];
						$data['meeting_room_title'] = $row['meeting_room_title'];

						$emp_number= $row['booked_by_id'];
						$query1="SELECT * FROM `hs_hr_employee` where emp_number=$emp_number";
						$count1=mysqli_query($this->conn, $query1);
						$row1=mysqli_fetch_assoc($count1);
						do { 						
					
						$emp['emp_number'] = $row1['emp_number'];
						$emp['emp_lastname'] = $row1['emp_lastname'];
						$emp['emp_firstname'] = $row1['emp_firstname'];
						$emp['emp_middle_name'] = $row1['emp_middle_name'];
						$emp['emp_gender'] = $row1['emp_gender'];
						$emp['work_station'] = $row1['work_station'];
						$emp['department'] = $row1['department'];
						$emp['emp_mobile'] = $row1['emp_mobile'];
						$emp['emp_work_email'] = $row1['emp_work_email'];

						// $data1[] = $data;

						}while($row1 = mysqli_fetch_assoc($count1));
						// $data['empDetails']=$emp;

						$data['booked_by_id'] = $emp;
						$data['meeting_organiser'] = $row['meeting_organiser'];
						$data['from_date'] = $row['from_date'];
						$data['to_date'] = $row['to_date'];
						$data['from_time'] = $row['from_time'];
						$data['to_time'] = $row['to_time'];
						$data['all_day'] = $row['all_day'];
						$data['submitted_on'] = $row['submitted_on'];
						$data['status'] = $row['status'];

						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['getBookMeetingRoomsList']=$data1;
						$data['status'] = 1;
					
		}else{
			$data['status'] = 0;
		}
		return $data;
    }

    function savePpeRequisition($user_id,$requisition_date,$indent_type,$notes,$status,$last_submitted_by,$last_submitted_on)
    {
    	
        $data=array();


		$sql = "INSERT INTO erp_ppe_requisition (requisition_date,notes,indent_type,last_submitted_on,last_submitted_by,last_modified_by,status_id) VALUES (?,?,?,?,?,?,?)";
		
		if($stmt = mysqli_prepare($this->conn, $sql)){

			 $userDetails = $this->getUserRoleByUserId($last_submitted_by);
             $empNumber = $userDetails['empNumber'];
			 mysqli_stmt_bind_param($stmt,"ssisiii" ,$requisition_date,$notes,$indent_type,$last_submitted_on,$empNumber,$empNumber,$status);

					    if(mysqli_stmt_execute($stmt)){

					    	$data['REQ_Id'] = $this->conn->insert_id;
					    	$REQ_Id = $this->conn->insert_id;
								
           					$genReqid = "REQ" . str_pad($REQ_Id+1, 4, "0", STR_PAD_LEFT);
           					$requisitionCode =  date("Y")."/". $genReqid;

							$updatesql ="UPDATE erp_ppe_requisition SET requisition_code='$requisitionCode' WHERE id=$REQ_Id";
							$result2 = mysqli_query($this->conn, $updatesql);

							$logsql = "INSERT INTO erp_ppe_requistion_log (requisition_id,status,notes,submitted_on,submitted_by) VALUES (?,?,?,?,?)";
							$logstmt = mysqli_prepare($this->conn, $logsql);
							$last_submitted_onlog = date('Y-m-d H:i:s');
							mysqli_stmt_bind_param($logstmt,"iissi" ,$REQ_Id,$status,$notes,$last_submitted_onlog,$empNumber);
							mysqli_stmt_execute($logstmt);

           					$data['requisitionCode'] =  date("Y")."/". $genReqid;

				        $data['reqId'] = $data['REQ_Id'];
				        $data['requisitionCode'] = $data['requisitionCode'];
				        $data['status']=1;
					    } else{
					        $data['status']=0;
					    }

		}else{
				    $data['status']=0;
					}

		
		return $data;
    }

    function getPPEproductsList()
    {
        $data=array();
  			
		$query="SELECT p.*,(select pc.name from erp_product_category as pc WHERE pc.id=p.category) as product_category,( select name from erp_product_family as pf where pf.id=p.product_family) as productfamily_name,(select name from erp_product_type as pt where pt.id=p.type) as product_type FROM `erp_products` as p WHERE p.is_deleted=0";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				
					do { 						
						
					
						$data['id'] = $row['id'];
						$data['product_code'] = $row['material_code'];
						$data['product_family_name '] = $row['productfamily_name'];
						$data['category_name'] = $row['product_category'];
						$data['product_type'] = $row['product_type'];
						$data['product_name'] = $row['name'];
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['getPPEproductsList']=$data1;
						$data['status'] = 1;
					
		}else{
			$data['status'] = 0;
		}
		return $data;
    }

    function savePpeProductsByRequisitionId($user_id,$requisition_id,$req_for,$products,$status)
    {
			// print_r($products);die();
    	
        $data=array();

        $userDetails = $this->getUserRoleByUserId($user_id);
        $empNumber = $userDetails['empNumber'];

        // echo sizeof($products);die();
        $requisition_material = array();
		$sql = "INSERT INTO erp_ppe_requisition_material (requisition_id,emp_number,req_for,product_id,requested_quantity,status) VALUES (?,?,?,?,?,?)";
		
		if($stmt = mysqli_prepare($this->conn, $sql)){

			// echo $sql;die();

			 for ($i=0; $i < sizeof($products); $i++) { 
			 	$product_id = $products[$i]['product_id'];
			 	$product_qty = $products[$i]['product_qty'];
			 	mysqli_stmt_bind_param($stmt,"ssisii" ,$requisition_id,$empNumber,$req_for,$product_id,$product_qty,$status);
			 	mysqli_stmt_execute($stmt);

			 	$requisition_material[$i] = $this->conn->insert_id;
			 	
			 }

					    $data['requisition_material'] =$requisition_material;
								
				        $data['status']=1;
					   

		}else{
				    $data['status']=0;
					}

		
		return $data;
    }


    function ppeSupervisiorStatusUpdate($user_id,$requisition_id,$notes,$status,$submitted_by,$submitted_on)
    {
    	
        $data=array();

        $userDetails = $this->getUserRoleByUserId($user_id);
        $empNumber = $userDetails['empNumber'];

		$sql = "UPDATE erp_ppe_requisition SET status_id=$status WHERE id=$requisition_id";
		$result = mysqli_query($this->conn, $sql);
		
		if($result){

				$logsql = "INSERT INTO erp_ppe_requistion_log (requisition_id,status,notes,submitted_on,submitted_by) VALUES (?,?,?,?,?)";
				$logstmt = mysqli_prepare($this->conn, $logsql);
				$last_submitted_onlog = date('Y-m-d H:i:s');
				mysqli_stmt_bind_param($logstmt,"iissi" ,$requisition_id,$status,$notes,$last_submitted_onlog,$empNumber);
				mysqli_stmt_execute($logstmt);

				$data['requisition_action_log'] = $this->conn->insert_id;
				$data['status']=1;

		}else{
		    $data['status']=0;
			}

		
		return $data;
    }


    function saveEmployeeReferral($user_id,$first_name,$middle_name,$last_name,$email,$contact_number,$vacancy_id,$vacancy_name,$keyWords,$comment,$submitted_by,$submitted_on)
    {
		

        $data=array();

		$sql = "INSERT INTO erp_job_candidate (first_name,middle_name,last_name,email,contact_number,keywords,comment,mode_of_application,date_of_application,status,referred_by) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
		
		if($stmt = mysqli_prepare($this->conn, $sql)){

			 $userDetails = $this->getUserRoleByUserId($submitted_by);
             $empNumber = $userDetails['empNumber'];
             $mode_of_application=1;
             $status=1;
			 mysqli_stmt_bind_param($stmt,"sssssssisii" ,$first_name,$middle_name,$last_name,$email,$contact_number,$keyWords,$comment,$mode_of_application,$submitted_on,$status,$empNumber);

					    if(mysqli_stmt_execute($stmt)){

					    	$candidateId = $this->conn->insert_id;
					    	$action = 1;
							

							$logsql = "INSERT INTO erp_job_candidate_history (candidate_id,vacancy_id,candidate_vacancy_name,action,performed_date,performed_by) VALUES (?,?,?,?,?,?)";
							$logstmt = mysqli_prepare($this->conn, $logsql);
							$performed_date = date('Y-m-d H:i:s');
							mysqli_stmt_bind_param($logstmt,"iisisi" ,$candidateId,$vacancy_id,$vacancy_name,$action,$performed_date,$empNumber);
							mysqli_stmt_execute($logstmt);


							$vacancysql = "INSERT INTO erp_job_candidate_vacancy (candidate_id,vacancy_id,status,applied_date) VALUES (?,?,?,?)";
							$vacancytmt = mysqli_prepare($this->conn, $vacancysql);
							$applied_date = date('Y-m-d');
							$vacancyStatus = 'APPLICATION INITIATED';
							mysqli_stmt_bind_param($vacancytmt,"iiss" ,$candidateId,$vacancy_id,$vacancyStatus,$applied_date);
							mysqli_stmt_execute($vacancytmt);

							$candquery="SELECT * FROM `erp_job_candidate` where id=$candidateId";
							$count=mysqli_query($this->conn, $candquery);
							$row=mysqli_fetch_assoc($count);

							do { 						
							$data['id'] = $row['id'];
							$data['first_name'] = $row['first_name'];
							$data['middle_name '] = $row['middle_name'];
							$data['last_name'] = $row['last_name'];
							$data['email'] = $row['email'];
							$data['contact_number'] = $row['contact_number'];
							$data['comment'] = $row['comment'];
							$data['keywords'] = $row['keywords'];

							$data1[] = $data;
							}while($row = mysqli_fetch_assoc($count));

							$data['candidate_details']=$data1;
				        	$data['status']=1;
					    } else{
					        $data['status']=0;
					    }


						// if(!empty($resume)){
						// $target_dir = "../uploads/resumes/";
						// $resumefile = str_replace(" ", "_", $_FILES[$resume]["name"]);
						// $resumeSize = $_FILES[$resume]["size"];
						// $resumeTmpName = $_FILES[$resume]["tmp_name"];
						// $resumeFileType = pathinfo($resumefile,PATHINFO_EXTENSION);
						// $filename = time().$resumefile;
						// move_uploaded_file($_FILES[$resume]['tmp_name'],$target_dir.$filename);
						// }else{
						// $filename = '';
						// }

		}else{
				    $data['status']=0;
					}

		
		return $data;
    }

    function getJobVacancyList()
    {
        $data=array();
  			
		$query="SELECT * from erp_job_vacancy";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				
					do { 						
						
					
						$data['id'] = $row['id'];
						$data['vacancyes_name'] = $row['name'];
						
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['vacancy_list']=$data1;
						$data['status'] = 1;
					
		}else{
			$data['status'] = 0;
		}
		return $data;
    }

    function uploadResume($candidate_id,$file_name,$file_type,$file_size,$file_content)
    {
        $data=array();

       
			$sql = "INSERT INTO erp_job_candidate_attachment (candidate_id,file_name,file_type,file_size,file_content) VALUES (?,?,?,?,?)";
			 
			if($stmt = mysqli_prepare($this->conn, $sql)){
			     mysqli_stmt_bind_param($stmt, "issis" , $candidate_id,$file_name,$file_type,$file_size,$file_content);
			    			   
			    if(mysqli_stmt_execute($stmt)){
			        $data['uploadResume'] = "resume added successfully";
			        $data['status']=1;
			    } else{
			        $data['status']=0;
			    }
			} else{
			    $data['status']=0;
			}	

        return $data;
    }

    function selectEmployeeOfTheMonth($financial_year,$month,$nominatEmpNumber,$department_id,$comment,$status_id,$submitted_by,$submitted_on)
    {
		

        $data=array();

		$sql = "INSERT INTO erp_emp_of_the_month (financial_year,month,emp_number,department_id,comment,status_id,submitted_by,submitted_on) VALUES (?,?,?,?,?,?,?,?)";
		
		if($stmt = mysqli_prepare($this->conn, $sql)){

			 $userDetails = $this->getUserRoleByUserId($submitted_by);
             $empNumber = $userDetails['empNumber'];
             $mode_of_application=1;
             $status=1;
			 mysqli_stmt_bind_param($stmt,"ssiisiis" ,$financial_year,$month,$nominatEmpNumber,$department_id,$comment,$status_id,$empNumber,$submitted_on);

					    if(mysqli_stmt_execute($stmt)){

					    	$empmonthid = $this->conn->insert_id;
					    	$action = 1;
							

							$logsql = "INSERT INTO erp_emp_of_the_month_log (eom_id,emp_number,status_id,submitted_by,submitted_on) VALUES (?,?,?,?,?)";
							$logstmt = mysqli_prepare($this->conn, $logsql);
							$performed_date = date('Y-m-d H:i:s');
							mysqli_stmt_bind_param($logstmt,"iiiis" ,$empmonthid,$nominatEmpNumber,$status_id,$empNumber,$performed_date);
							mysqli_stmt_execute($logstmt);


							$candquery="SELECT * FROM `erp_emp_of_the_month` where id=$empmonthid";
							$count=mysqli_query($this->conn, $candquery);
							$row=mysqli_fetch_assoc($count);

							do { 						
							$data['id'] = $row['id'];
							$data['year'] = $row['financial_year'];
							$data['month '] = $row['month'];
							$data['emp_number'] = $row['emp_number'];
							$data['department_id'] = $row['department_id'];
							$data['comment'] = $row['comment'];
							$data['status_id'] = $row['status_id'];

							$data1[] = $data;
							}while($row = mysqli_fetch_assoc($count));

							$data['employee_of_month']=$data1;
				        	$data['status']=1;
					    } else{
					        $data['status']=0;
					    }


		}else{
				    $data['status']=0;
					}

		
		return $data;
    }

    function getEomListBySupervisor($user_id)
    {
        $data=array();

        $userDetails = $this->getUserRoleByUserId($user_id);
        $empNumber = $userDetails['empNumber'];
  			
		$query="SELECT * from erp_emp_of_the_month WHERE submitted_by=$empNumber AND is_deleted=0";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				
					do { 						
						
					
						$data['id'] = $row['id'];
						$data['financial_year'] = $row['financial_year'];
						$data['month'] = $row['month'];


						$eomnuber= $row['emp_number'];
						$query1="SELECT * FROM `hs_hr_employee` where emp_number=$eomnuber";
						$count1=mysqli_query($this->conn, $query1);
						$row1=mysqli_fetch_assoc($count1);
						do { 						
					
						$emp['emp_number'] = $row1['emp_number'];
						$emp['emp_lastname'] = $row1['emp_lastname'];
						$emp['emp_firstname'] = $row1['emp_firstname'];
						$emp['emp_middle_name'] = $row1['emp_middle_name'];
						$emp['emp_gender'] = $row1['emp_gender'];
						$emp['work_station'] = $row1['work_station'];
						$emp['department'] = $row1['department'];
						$emp['emp_mobile'] = $row1['emp_mobile'];
						$emp['emp_work_email'] = $row1['emp_work_email'];

						// $data1[] = $data;

						}while($row1 = mysqli_fetch_assoc($count1));
						// $data['empDetails']=$emp;

						$data['emp_number'] = $emp;
						$data['department_id'] = $row['department_id'];
						$data['comment'] = $row['comment'];
						$data['status_id'] = $row['status_id'];
						
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['eom_list']=$data1;
						$data['status'] = 1;
					
		}else{
			$data['status'] = 0;
		}
		return $data;
    }

    function getEomListByHod($department_id)
    {
        $data=array();

       
  			
		$query="SELECT * from erp_emp_of_the_month WHERE status_id=2 AND department_id=$department_id AND is_deleted=0";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				
					do { 						
						
					
						$data['id'] = $row['id'];
						$data['financial_year'] = $row['financial_year'];
						$data['month'] = $row['month'];


						$eomnuber= $row['emp_number'];
						$query1="SELECT * FROM `hs_hr_employee` where emp_number=$eomnuber";
						$count1=mysqli_query($this->conn, $query1);
						$row1=mysqli_fetch_assoc($count1);
						do { 						
					
						$emp['emp_number'] = $row1['emp_number'];
						$emp['emp_lastname'] = $row1['emp_lastname'];
						$emp['emp_firstname'] = $row1['emp_firstname'];
						$emp['emp_middle_name'] = $row1['emp_middle_name'];
						$emp['emp_gender'] = $row1['emp_gender'];
						$emp['work_station'] = $row1['work_station'];
						$emp['department'] = $row1['department'];
						$emp['emp_mobile'] = $row1['emp_mobile'];
						$emp['emp_work_email'] = $row1['emp_work_email'];

						// $data1[] = $data;

						}while($row1 = mysqli_fetch_assoc($count1));
						// $data['empDetails']=$emp;

						$data['emp_info'] = $emp;
						$data['department_id'] = $row['department_id'];
						$data['comment'] = $row['comment'];
						$data['status_id'] = $row['status_id'];
						
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count));
						$data['eom_list']=$data1;
						$data['status'] = 1;
					
		}else{
			$data['status'] = 0;
		}
		return $data;
    }

    function getRoleBasedEmployeeList($user_id)
    {
        $data=array();
        if(!empty($user_id) && $user_id >0){

	        
	        $userDetails = $this->getUserRoleByUserId($user_id);
	        $empNumber = $userDetails['empNumber'];
	        // print_r($userDetails);die();
	        
	        $subObj = $this->subordinateByEmpList($empNumber);

	        $departmentId = $this->getEmpDepartmentByEmpNumber($empNumber);

	        // echo($subObj['status']);die();

	        if($subObj['status'] == 1){

	        	for ($i=0; $i < sizeof($subObj['emplist']); $i++) { 
						    $empNum = $subObj['emplist'][$i]['emp_number'];

						    // $query1="SELECT * FROM `hs_hr_employee` where emp_number=$empNum";
						    $query1="SELECT emp.employee_id, emp.emp_number,emp.work_station,CONCAT(emp.emp_firstname,' ',emp.emp_lastname) AS emp_name FROM hs_hr_employee as emp WHERE emp.emp_number=$empNum AND emp.termination_id IS NULL";

							$count1=mysqli_query($this->conn, $query1);
							if(mysqli_num_rows($count1) > 0)
							{
								$row1=mysqli_fetch_assoc($count1);
								do { 						
							
								$emp['emp_number'] = $row1['emp_number'];
								$emp['emp_name'] = $row1['emp_name'];
								// $emp['emp_name'] = $row1['emp_firstname'].' '.$row1['emp_lastname'];
								$emp['department'] = $row1['work_station'];

								if(!empty($row1['work_station'])){
								$emp['department_name'] = $this->getDepartment($row1['work_station']);
								}else{
								$emp['department_name'] = '';
								}

								
								}while($row1 = mysqli_fetch_assoc($count1));

							}



				$data1[] = $emp;
				}
							// print_r($data1);die();
				$data['status'] = 1;
				$data['eom_list']=$data1;

	        }else if($userDetails['id'] == 17){
	        	$query="SELECT emp.employee_id, emp.emp_number,emp.work_station,CONCAT(emp.emp_firstname,' ',emp.emp_lastname) AS emp_name FROM hs_hr_employee as emp WHERE emp.work_station=$departmentId AND emp.termination_id IS NULL";
				$count=mysqli_query($this->conn, $query);

				if(mysqli_num_rows($count) > 0)
				{
						$row=mysqli_fetch_assoc($count);
						
							do { 						
								
							
								$data['emp_number'] = $row['emp_number'];
								$data['emp_name'] = $row['emp_name'];
								$data['department'] = $row['work_station'];
								if(!empty($row['work_station'])){
								$data['department_name'] = $this->getDepartment($row['work_station']);
								}else{
								$data['department_name'] = '';

								}
								$data1[] = $data;
							}while($row = mysqli_fetch_assoc($count));
							$data['status'] = 1;
							$data['eom_list']=$data1;
							
				}else{
					$data['status'] = 0;
					$data['eom_list']=array();
				}

	        }else if($userDetails['id'] == 6){
	        	$query="SELECT emp.employee_id, emp.emp_number,emp.work_station,CONCAT(emp.emp_firstname,' ',emp.emp_lastname) AS emp_name FROM hs_hr_employee as emp WHERE emp.termination_id IS NULL";
				$count=mysqli_query($this->conn, $query);

				if(mysqli_num_rows($count) > 0)
				{
						$row=mysqli_fetch_assoc($count);
						
							do { 						
								
							
								$data['emp_number'] = $row['emp_number'];
								$data['emp_name'] = $row['emp_name'];
								$data['department'] = $row['work_station'];
								if(!empty($row['work_station'])){
								$data['department_name'] = $this->getDepartment($row['work_station']);
								}else{
								$data['department_name'] = '';

								}
								$data1[] = $data;
							}while($row = mysqli_fetch_assoc($count));
							$data['status'] = 1;
							$data['eom_list']=$data1;
							
				}else{
					$data['status'] = 0;
					$data['eom_list']=array();
				}
	        }else{
	        	$data['status'] = 0;
	        	$data['eom_list']=$data;
	        }
	    }else{
	    	$data['status'] = 0;
	        $data['eom_list']=$data;
	    }
		
		return $data;
    }

    function hodApproveEomStatus($eom_id,$emp_id,$status,$submitted_by,$submitted_on)
    {
    	
        $data=array();

        $userDetails = $this->getUserRoleByUserId($submitted_by);
        $empNumber = $userDetails['empNumber'];

		$sql = "UPDATE erp_emp_of_the_month SET status_id=$status WHERE id=$eom_id";
		$result = mysqli_query($this->conn, $sql);
		
		if($result){

				$logsql = "INSERT INTO erp_emp_of_the_month_log (eom_id,emp_number,status_id,submitted_by,submitted_on) VALUES (?,?,?,?,?)";
				$logstmt = mysqli_prepare($this->conn, $logsql);
				$performed_date = date('Y-m-d H:i:s');
				mysqli_stmt_bind_param($logstmt,"iiiis" ,$eom_id,$emp_id,$status,$empNumber,$performed_date);
				mysqli_stmt_execute($logstmt);

				$data['emp_of_the_month_log'] = $this->conn->insert_id;
				$data['status']=1;

		}else{
		    $data['status']=0;
			}

		
		return $data;
    }

// pavan start
///////////// Ramu Start /////////////////////////////////////

function getLeaveTypes()
{
    $data=array();
      
  $query="SELECT * FROM erp_leave_type WHERE deleted = 0 ";
  $count=mysqli_query($this->conn, $query);

  if(mysqli_num_rows($count) > 0)
  {
      $row=mysqli_fetch_assoc($count);
      
        do {    
          
          $data['id'] = $row['id'];
          $data['name'] = $row['name'];

          $data1[] = $data;
        }while($row = mysqli_fetch_assoc($count));
          $data['leaveType']=$data1;
        $data['status'] = 1;
        
  }else{
    $data['status'] = 0;
  }
  return $data;
}

function getLeaveDuration()
{
    $data=array();
      
  $query="SELECT * FROM erp_leave_duration WHERE deleted = 0 ";
  $count=mysqli_query($this->conn, $query);

  if(mysqli_num_rows($count) > 0)
  {
      $row=mysqli_fetch_assoc($count);
      
        do {    
          
          $data['id'] = $row['duration_id'];
          $data['name'] = $row['name'];

          $data1[] = $data;
        }while($row = mysqli_fetch_assoc($count));
          $data['leaveDuration']=$data1;
        $data['status'] = 1;
        
  }else{
    $data['status'] = 0;
  }
  return $data;
}

function getHolidays(){
	$query = "SELECT * FROM erp_holiday WHERE YEAR(date) = ".date('Y').""; //table
	$result=mysqli_query($this->conn, $query);

	$holidayDates = array();
   	foreach ($result as $val) {
   		array_push($holidayDates,$val['date']);
   	}
	
  	return $holidayDates;
}

function getWeekends($from_date,$to_date){

	$from = strtotime($from_date);
	$to = strtotime($to_date);

	$start = $now = $from; // starting time
	$end = $to; // ending time
	$day = intval(date("N", $now));
	// echo $day;die();
	$weekends = array();
	if ($day < 6) {
	  $now += (6 - $day) * 86400;
	}
	while ($now <= $end) {
	  $day = intval(date("N", $now));
	  if ($day == 6) {
	    $weekends[] += $now;
	    $now += 86400;
	  }
	  elseif ($day == 7) {
	    $weekends[] += $now;
	    $now += 518400;
	  }
	}
	$weekdates = array();
	// echo "Weekends from " . date("r", $start) . " to " . date("r", $end) . ":\n";
	foreach ($weekends as $timestamp) {
	  $weekdates[] = date("Y-m-d", $timestamp);
	}

	return $weekdates;
}

function getAppliedLeaveDates(){
	$query = "SELECT * FROM erp_leave WHERE YEAR(date) = ".date('Y').""; //table
	$result=mysqli_query($this->conn, $query);

	$leaveDates = array();
   	foreach ($result as $val) {
   		array_push($leaveDates,$val['date']);
   	}
	
  	return $leaveDates;
}

function applyLeave($user_id,$leave_type_id,$comments,$from_date,$to_date,$duration,$start_time,$end_time,$status_id)
{
  	$userDetails = $this->getUserRoleByUserId($user_id);
    $emp_number = $userDetails['empNumber'];

    // echo "string ".$emp_number;die();

   $created_by_name = $this->getEmpnameByEmpNumber($emp_number);

   $holidayDates = $this->getHolidays();

   $weekdates = $this->getWeekends($from_date,$to_date);

   $appliedLeaveDates = $this->getAppliedLeaveDates();

  
  $date_applied  = date('Y-m-d');

  if($duration == self::FULLDAY){
    $length_hours = 8;
    $length_days = 1;
  }
  if($duration == self::HALFDAY){
    $length_hours = 4;
    $length_days = 0.5;
  }
  if($duration == self::SPECIFIEDTIME){
    $start_time = $start_time;
    $end_time = $end_time;
  }else{
    $start_time = '00:00:00';
    $end_time = '00:00:00';
  }
  $status = $status_id;
  $createddate = date('Y-m-d H:i:s'); 

  $data=array();

  $sql = "INSERT INTO erp_leave_request (leave_type_id,date_applied,emp_number,comments) VALUES (?,?,?,?)";
    
    if($stmt = mysqli_prepare($this->conn, $sql)){
        mysqli_stmt_bind_param($stmt, "isis" ,$leave_type_id,$date_applied,$emp_number,$comments);
                 
      if(mysqli_stmt_execute($stmt)){

            $leave_request_id = $this->conn->insert_id;
            	if($from_date == $to_date){
            		$cntdays = 0;
            	}else{

			    	$date1=date_create(date('Y-m-d',strtotime($from_date)));
					$date2=date_create(date('Y-m-d',strtotime($to_date)));

			    	$diff=date_diff($date1,$date2);

			    	// echo $date1-$date2;die();
					$cntdays = $diff->format("%a");
            	}

            	$from = strtotime($from_date);
        		$to = strtotime($to_date);

            	for ($timeStamp = $from; $timeStamp <= $to; $timeStamp = $this->incDate($timeStamp)) {	            

		            $leaveDate = date('Y-m-d', $timeStamp);

		            // if(!empty($weekdates)){

		            		// Check Weekends
			            if(!in_array($leaveDate, $weekdates)){

			            	// Check Holidays
			            	if(!in_array($leaveDate, $holidayDates)){

			            		if(!in_array($leaveDate, $appliedLeaveDates)){

					            	$query="INSERT INTO erp_leave (date,length_hours,length_days,status,comments,leave_request_id,leave_type_id,emp_number,
						    		start_time,end_time,duration_type) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
									
						    		if($stmt = mysqli_prepare($this->conn, $query)){
							     		mysqli_stmt_bind_param($stmt, "sssisiiissi" ,$leaveDate,$length_hours,$length_days,$status,$comments,$leave_request_id,$leave_type_id,$emp_number,$start_time,$end_time,$duration);
							    			   
											if(mysqli_stmt_execute($stmt)){
												$data['status']=1;
											} 
									}else{
										$data['status']=0;
									}
								}
			            	}
			            }
		            // }
		            
		        }

          $query="INSERT INTO erp_leave_request_comment (leave_request_id,created,created_by_name,created_by_id,
          created_by_emp_number,comments) VALUES (?,?,?,?,?,?)";
          
            if($stmt = mysqli_prepare($this->conn, $query)){
              mysqli_stmt_bind_param($stmt, "issiis" ,$leave_request_id,$createddate,$created_by_name,$created_by_id,$emp_number,$comments);
                   
              if(mysqli_stmt_execute($stmt)){
                $data['status']=1;
              } 
          }else{
            $data['status']=0;
          }
      }else{
          $data['status']=0;
      }

    }else{
        $data['status']=0;
    }
    
    return $data;
}

private function incDate($timestamp) {

    return strtotime("+1 day", $timestamp);
}

function applyLeaveEmpBySupervisor($user_id,$emp_number,$leave_type_id,$comments,$from_date,$to_date,$duration,$start_time,$end_time,$status_id)
{
  	$userDetails = $this->getUserRoleByUserId($user_id);
    $created_by = $userDetails['empNumber'];

    // echo "string ".$emp_number;die();

	$created_by_name = $this->getEmpnameByEmpNumber($created_by);

	$holidayDates = $this->getHolidays();

   $weekdates = $this->getWeekends($from_date,$to_date);

   $appliedLeaveDates = $this->getAppliedLeaveDates();
  
  $date_applied  = date('Y-m-d');

  if($duration == self::FULLDAY){
    $length_hours = 8;
    $length_days = 1;
  }
  if($duration == self::HALFDAY){
    $length_hours = 4;
    $length_days = 0.5;
  }
  if($duration == self::SPECIFIEDTIME){
    $start_time = $start_time;
    $end_time = $end_time;
  }else{
    $start_time = '00:00:00';
    $end_time = '00:00:00';
  }
  $status = $status_id;
  $createddate = date('Y-m-d H:i:s'); 

  $data=array();

  $sql = "INSERT INTO erp_leave_request (leave_type_id,date_applied,emp_number,comments) VALUES (?,?,?,?)";
    
    if($stmt = mysqli_prepare($this->conn, $sql)){
        mysqli_stmt_bind_param($stmt, "isis" ,$leave_type_id,$date_applied,$emp_number,$comments);
                 
      if(mysqli_stmt_execute($stmt)){

            $leave_request_id = $this->conn->insert_id;
            	if($from_date == $to_date){
            		$cntdays = 0;
            	}else{

			    	$date1=date_create(date('Y-m-d',strtotime($from_date)));
					$date2=date_create(date('Y-m-d',strtotime($to_date)));

			    	$diff=date_diff($date1,$date2);

			    	// echo $date1-$date2;die();
					$cntdays = $diff->format("%a");
            	}

            	$from = strtotime($from_date);
        		$to = strtotime($to_date);

            	for ($timeStamp = $from; $timeStamp <= $to; $timeStamp = $this->incDate($timeStamp)) {	            

		            $leaveDate = date('Y-m-d', $timeStamp);

		            if(!empty($weekdates)){

		            		// Check Weekends
			            if(!in_array($leaveDate, $weekdates)){

			            	// Check Holidays
			            	if(!in_array($leaveDate, $holidayDates)){

			            		if(!in_array($leaveDate, $appliedLeaveDates)){

				            		$query="INSERT INTO erp_leave (date,length_hours,length_days,status,comments,leave_request_id,leave_type_id,emp_number,
						    		start_time,end_time,duration_type) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
									
						    		if($stmt = mysqli_prepare($this->conn, $query)){
							     		mysqli_stmt_bind_param($stmt, "sssisiiissi" ,$leaveDate,$length_hours,$length_days,$status,$comments,$leave_request_id,$leave_type_id,$emp_number,$start_time,$end_time,$duration);
							    			   
											if(mysqli_stmt_execute($stmt)){
												$data['status']=1;
											} 
									}else{
										$data['status']=0;
									}
								}

			            	}
			            }
			        }
			    }

          $query="INSERT INTO erp_leave_request_comment (leave_request_id,created,created_by_name,created_by_id,
          created_by_emp_number,comments) VALUES (?,?,?,?,?,?)";
          
            if($stmt = mysqli_prepare($this->conn, $query)){
              mysqli_stmt_bind_param($stmt, "issiis" ,$leave_request_id,$createddate,$created_by_name,$user_id,$created_by,$comments);
                   
              if(mysqli_stmt_execute($stmt)){
                $data['status']=1;
              } 
          }else{
            $data['status']=0;
          }
      }else{
          $data['status']=0;
      }

    }else{
        $data['status']=0;
    }
    
    return $data;
}

function saveResumeBank($email,$contact_number,$keywords,$comment,$file_name,$file_type,$file_size,$tempPath){

	$data=array();
	$status = 1;
	$mode_of_application =1;
	$date_of_application= date('Y-m-d');

	$fe = explode('@', $email);
	$emailName = $fe[0];
	$emailLastName = $fe[0][0];



	$sql = "INSERT INTO erp_job_candidate (first_name,last_name,email,contact_number,keywords,comment,status,mode_of_application,date_of_application) VALUES (?,?,?,?,?,?,?,?,?)";
	 
	if($stmt = mysqli_prepare($this->conn, $sql)){		

	     mysqli_stmt_bind_param($stmt, "sssissiis" ,$emailName,$emailLastName,$email,$contact_number,$keywords,$comment,$status,$mode_of_application,$date_of_application);
	    			   
	    if(mysqli_stmt_execute($stmt)){

	    	$resume_id = $this->conn->insert_id;

	    	$file_content = file_get_contents($tempPath);

	    	$query="INSERT INTO erp_job_candidate_attachment (candidate_id,file_name,file_type,file_size) VALUES (?,?,?,?)";
				
    		if($stmt = mysqli_prepare($this->conn, $query)){
	     		mysqli_stmt_bind_param($stmt, "issi" ,$resume_id,$file_name,$file_type,$file_size);

	     		$upload_path = $_SERVER["DOCUMENT_ROOT"].'/entreplan3.1/symfony/upload/'.$file_name;

                move_uploaded_file($tempPath,$upload_path);
	    			   
					if(mysqli_stmt_execute($stmt)){
						$data['status']=1;
					}



				

			}else{
				$data['status']=0;
			}

	        $data['ResumeBank'] = "Resume added successfully";
	        $data['status']=1;
	    } else{
	        $data['status']=0;
	    }
	} else{
	    $data['status']=0;
	}	

	return $data;

}

function leaveApprovals($user_id,$status_id,$leave_request_id){

	// echo $leave_request_id;
	// die();
	// echo $leave_request_id;die();

  $data=array();

  	$query="SELECT * FROM erp_leave WHERE leave_request_id = $leave_request_id ";
  	// $query="SELECT * FROM erp_leave WHERE leave_request_id = $leave_request_id ";
	$result=mysqli_query($this->conn, $query);
	

	if(mysqli_num_rows($result) > 0)
	{
	while($row = mysqli_fetch_array($result))
	{
	  $leave_id =  $row["id"];

	  // echo $leave_id;die();

	  // echo $leave_id;die();

		  if($status_id == 2)
		  {

		    $updatesql = "UPDATE erp_leave SET status = $status_id WHERE id = $leave_id";
		    if($result2 = mysqli_query($this->conn, $updatesql)){

		      $data['log'] = "Successfully Approved";
		      $data['status']=1;

		    }
		    else{
		    //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
		    $data['status']=0;
		    }

		  }
		  if($status_id == self::CANCEL)
		  {

		    $updatesql = "UPDATE erp_leave SET status = $status_id WHERE id = $leave_id";
		    if($result2 = mysqli_query($this->conn, $updatesql)){

		      $data['log'] = "Successfully Cancelled";
		      $data['status']=1;

		    }
		    else{
		    //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
		    $data['status']=0;
		    }

		  }
		  if($status_id == self::REJECT)
		  {

		    $updatesql = "UPDATE erp_leave SET status = $status_id WHERE id = $leave_id";
		    if($result2 = mysqli_query($this->conn, $updatesql)){

		      $data['log'] = "Successfully Rejected";
		      $data['status']=1;

		    }
		    else{
		    //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
		    $data['status']=0;
		    }

		  }
		  if($status_id == self::TAKEN)
		  {

		    $updatesql = "UPDATE erp_leave SET status = $status_id WHERE id = $leave_id";
		    if($result2 = mysqli_query($this->conn, $updatesql)){

		      $data['log'] = "Successfully Taken";
		      $data['status']=1;

		    }
		    else{
		    //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
		    $data['status']=0;
		    }

		  }

		}
	}else{
		$data['status']=0;
	}

  	return $data;
}

///////////// Overtime Start ////////////////////////////

function assignOvertime($user_id,$emp_number,$otdate,$start_time,$end_time,$reason,$status)
{
  	$userDetails = $this->getUserRoleByUserId($user_id);
    $submittedby = $userDetails['empNumber'];
	$created_by_name = $this->getEmpnameByEmpNumber($emp_number);
	$submittedOn = date('Y-m-d H:i:s');

	$otdateNew = date('Y-m-d',strtotime($otdate));

	$h = strtotime($end_time) - strtotime($start_time);

	$hours = gmdate("H.i", $h); 
    // echo $hours;die();

	$data=array();

	  $sql = "INSERT INTO erp_ot_apply (emp_number,date,start_time,end_time,hours,reason,submitted_on,submitted_by,status) VALUES (?,?,?,?,?,?,?,?,?)";

	  // echo $sql;die();
	    
	    if($stmt = mysqli_prepare($this->conn, $sql)){
	        mysqli_stmt_bind_param($stmt, "issssssii" ,$emp_number,$otdateNew,$start_time,$end_time,$hours,$reason,$submittedOn,$submittedby,$status);
	                 
	      if(mysqli_stmt_execute($stmt)){

	            $ot_id = $this->conn->insert_id;

	            // echo $ot_id;die();

	            $claim_type = 0;


	    		$query="INSERT INTO erp_ot_log (ot_id,performed_by,performed_on,status,claim_type,notes) VALUES (?,?,?,?,?,?)";
				
	    		if($stmt = mysqli_prepare($this->conn, $query)){
		     		mysqli_stmt_bind_param($stmt, "iisiis" ,$ot_id,$submittedby,$submittedOn,$status,$claim_type,$reason);
		    			   
						if(mysqli_stmt_execute($stmt)){
							$data['status']=1;
						} 
				}else{
					$data['status']=0;
				}
				
	      	}else{
	          $data['status']=0;
		    }

		    }else{
		        $data['status']=0;
		    }
	    
	    return $data;
}

function getCheckOT($user_id,$ot_id)
{
	$userDetails = $this->getUserRoleByUserId($user_id);

	$empNumber = $userDetails['empNumber'];
	$roleId = $userDetails['id'];

	$query = "SELECT * FROM `erp_ot_apply` as o WHERE  id=$ot_id";

    $data=array();  
    $msg ='no record found';
    $nowdate = strtotime(date('Y-m-d H:i:s'));	

	$questionsList = mysqli_query($this->conn, $query);

	if(mysqli_num_rows($questionsList) > 0)
	{
	$row=mysqli_fetch_assoc($questionsList);
				
					do { 						
						
						$data['id'] = $row['id'];
						$data['date'] = $row['date'];
						$data['start_time'] = $row['start_time'];
						$data['end_time'] = $row['end_time'];
						// echo $nowdate.'-';
						$compotdate = strtotime($data['date'].' '.$data['end_time']);
						// echo $compotdate;
    						
						if( $nowdate >=$compotdate){
							$msg = true;
						}else{

							$msg = false;
						}

					
					}while($row = mysqli_fetch_assoc($questionsList));
						$data['myOtCheck']=$msg;
						$data['status'] = 1;
						
	}else{
			$data['myOtCheck']=$msg;
			$data['status']=0;
	}
	return $data; 
}

function getMyOTList($user_id,$role,$status_id)
{
  $userDetails = $this->getUserRoleByUserId($user_id);

  // print_r($userDetails);die();
  $userRoleId = $userDetails['id'];
  $emp_number = $userDetails['empNumber'];

	// print_r($rowSup);
	// foreach($rowSup as $s){
	// 	array_push($supList,$s['erep_sup_emp_number']);
	// 	echo $s->erep_sup_emp_number;
	// }


	// $supListData = implode(',',$supList);
	// echo $row1['work_station'];die();
	// echo $emp_number;die();
  $empName = $this->getEmpnameByEmpNumber($emp_number);

  $data=array();
      
  if($role == 'Supervisor'){

  $query="SELECT eoa.*,(SELECT l.claim_type FROM `erp_ot_log` as l WHERE l.ot_id=eoa.id order BY l.id desc limit 1) as claim_type FROM `erp_ot_apply` as eoa  WHERE eoa.status=$status_id AND eoa.submitted_by=$emp_number ORDER BY eoa.id DESC";
  // IN (1,2,3,4,5)

  }else if($role == 'Technician' || $role == 'ESS'){

  	$query="SELECT eoa.*,(SELECT l.claim_type FROM `erp_ot_log` as l WHERE l.ot_id=eoa.id order BY l.id desc limit 1) as claim_type FROM `erp_ot_apply` as eoa  WHERE eoa.status=$status_id AND eoa.emp_number=$emp_number ORDER BY eoa.id DESC";
  	// IN (1,2,3,4,5,6)

  }else if($role == 'HOD' || $role == 'Department Manager'){

  	$supList =[];
	$query1="SELECT * FROM `hs_hr_employee` where emp_number=$emp_number";
	$count1=mysqli_query($this->conn, $query1);
	$row1=mysqli_fetch_assoc($count1);
	$work_station = $row1['work_station'];

	// echo $work_station;die();
	// $querySup ="SELECT * FROM `hs_hr_emp_reportto` as rt LEFT JOIN hs_hr_employee as e ON e.emp_number=rt.erep_sup_emp_number WHERE e.work_station=$work_station GROUP BY rt.erep_sup_emp_number";
	$querySup ="SELECT * FROM  hs_hr_employee as e  WHERE e.work_station=$work_station AND e.termination_id is NULL";
	$countquerySup=mysqli_query($this->conn, $querySup);
	$rowSup=mysqli_fetch_assoc($countquerySup);
	// $supList = $rowSup['erep_sup_emp_number'];


			if(mysqli_num_rows($countquerySup) > 0)
			{
				$rowSup=mysqli_fetch_assoc($countquerySup);
					do { 						
					$supList[] = $rowSup['emp_number'];
					}while($rowSup=mysqli_fetch_assoc($countquerySup));
			}

			// print_r($supList);die();

			if(!empty($supList)){
			$supListArr = implode(',',$supList);
			}else{
				$supListArr =null;
			}

	// echo $supListArr;die();

  	$query="SELECT eoa.*,(SELECT l.claim_type FROM `erp_ot_log` as l WHERE l.ot_id=eoa.id order BY l.id desc limit 1) as claim_type FROM `erp_ot_apply` as eoa  WHERE eoa.status=$status_id  AND eoa.emp_number IN($supListArr) ORDER BY eoa.status ASC";
  	// IN (3,4,5)
  	// $query="SELECT (SELECT (e.emp_firstname) FROM hs_hr_employee e WHERE e.emp_number = (eoa.emp_number)) as fullName,eoa.date as applyDate, date_format(eoa.start_time,'%H:%i') as startTime, date_format(eoa.end_time,'%H:%i') as endTime,eoa.hours,oat.claim_type, (SELECT (e.emp_firstname) FROM hs_hr_employee e WHERE e.emp_number = (oat.performed_by)) as performedByName,oat.performed_on,eoa.status,eoa.id as otId,eoa.emp_number as empNumber FROM erp_ot_apply eoa LEFT JOIN erp_ot_log oat ON eoa.id=oat.ot_id LEFT JOIN hs_hr_employee e ON e.emp_number=eoa.emp_number WHERE oat.performed_by IN ($supListArr) AND oat.claim_type IN (1, 2) AND eoa.status = 3 GROUP BY eoa.id";

  	// echo $query;die();
  }else if($role == 'FinanceManager'){
  	$query="SELECT eoa.*,l.claim_type FROM `erp_ot_apply` as eoa LEFT JOIN erp_ot_log as l on l.ot_id=eoa.id  WHERE eoa.status=4 AND l.claim_type=1 GROUP BY eoa.id ORDER BY eoa.id DESC";
  }else if($role == 'HiringManager'){
  	$query="SELECT eoa.*,l.claim_type FROM `erp_ot_apply` as eoa LEFT JOIN erp_ot_log as l on l.ot_id=eoa.id AND l.claim_type IN (1,2) WHERE eoa.status=$status_id  GROUP BY eoa.id ORDER BY eoa.id DESC";
  	// 4
  } else{
  	$query="SELECT eoa.*,(SELECT l.claim_type FROM `erp_ot_log` as l WHERE l.ot_id=eoa.id order BY l.id desc limit 1) as claim_type FROM `erp_ot_apply` as eoa  WHERE eoa.status=$status_id  AND eoa.emp_number=$emp_number ORDER BY eoa.id DESC";
  	// IN (1,2,3,4,5,6)
  }

  // echo $query;die();   
  $count=mysqli_query($this->conn, $query);

  // print_r($count);die();

  if(mysqli_num_rows($count) > 0)
  {
    $row=mysqli_fetch_assoc($count);
      
    do {    
      
      $data['id'] = $row['id'];
      $data['name'] = $this->getEmpnameByEmpNumber($row['emp_number']);
      $data['date'] = $row['date'];
      $data['start_time'] = $row['start_time'];
      $data['end_time'] = $row['end_time'];
      $data['hours'] = $row['hours'];
      $data['statusId'] = $row['status'];
      $data['claim_type'] = $row['claim_type'];

      if($row['status'] == 0){
      	$data['status'] = 'New';
      }

      if($row['status'] == 1){
      	$data['status'] = 'New';
      }

      if($row['status'] == 2){
      	$data['status'] = 'Claim Request';
      }

      if($row['status'] == 3){
      	$data['status'] = 'Verified';
      }

      if($row['status'] == 4){
      	$data['status'] = 'Approved';
      }

      if($row['status'] == 5){
      	$data['status'] = 'Rejected';
      }

      if($row['status'] == 6){
      	$data['status'] = 'Claimed';
      }

      if($row['status'] == 7){
      	$data['status'] = 'WORK';
      }

      $data1[] = $data;
    }while($row = mysqli_fetch_assoc($count));
      $data['myOtList']=$data1;
    $data['status'] = 1;
        
  }else{
    $data['status'] = 0;
  }
  return $data;
  // $query="SELECT * FROM erp_ot_apply WHERE is_deleted = 0 AND emp_number = $emp_number";
}


function getAllOtNewList($user_id)
{
  $userDetails = $this->getUserRoleByUserId($user_id);

  $userRoleId = $userDetails['id'];
  $emp_number = $userDetails['empNumber'];

  $supervisior = $this->isSupervisor($emp_number);

  // echo $supervisior;die();

  $empName = $this->getEmpnameByEmpNumber($emp_number);

  $data=array();
      
  if($supervisior == 2){

  	$query="SELECT eoa.*,(SELECT l.claim_type FROM `erp_ot_log` as l WHERE l.ot_id=eoa.id order BY l.id desc limit 1) as claim_type FROM `erp_ot_apply` as eoa  WHERE eoa.status IN (0,1,2,3,4,5) AND eoa.submitted_by=$emp_number ORDER BY eoa.id DESC";

  	$count=mysqli_query($this->conn, $query);
	  // print_r($count);die();

	  if(mysqli_num_rows($count) > 0)
	  {
	    $row=mysqli_fetch_assoc($count);
	      
	    do {    
	      
	      $data['id'] = $row['id'];
	      $data['name'] = $this->getEmpnameByEmpNumber($row['emp_number']);
	      $data['date'] = $row['date'];
	      $data['start_time'] = $row['start_time'];
	      $data['end_time'] = $row['end_time'];
	      $data['hours'] = $row['hours'];
	      $data['statusId'] = $row['status'];
	      $data['claim_type'] = $row['claim_type'];
	      $data['comment'] = '';

	      if($row['status'] == 0){
	      	$data['status'] = 'New';
	      }else if($row['status'] == 1){
	      	$data['status'] = 'New';
	      }else{
	      	$data['status'] = 'New';
	      }

	      $data1[] = $data;
	    }while($row = mysqli_fetch_assoc($count));
	    $data['myOtList']=$data1;
	    $data['status'] = 1;
	        
	  }else{
	    $data['status'] = 0;
	  }
  }else if($userDetails['id'] == 17){
  	$supList =[];
	$query1="SELECT * FROM `hs_hr_employee` where emp_number=$emp_number";
	$count1=mysqli_query($this->conn, $query1);
	$row1=mysqli_fetch_assoc($count1);
	$work_station = $row1['work_station'];
	$querySup ="SELECT * FROM `hs_hr_emp_reportto` as rt LEFT JOIN hs_hr_employee as e ON e.emp_number=rt.erep_sup_emp_number WHERE e.work_station=$work_station GROUP BY rt.erep_sup_emp_number";
	$countquerySup=mysqli_query($this->conn, $querySup);
	$rowSup=mysqli_fetch_assoc($countquerySup);
	$supList = $rowSup['erep_sup_emp_number'];


  	$query="SELECT eoa.*,(SELECT l.claim_type FROM `erp_ot_log` as l WHERE l.ot_id=eoa.id order BY l.id desc limit 1) as claim_type FROM `erp_ot_apply` as eoa  WHERE eoa.status IN (3,4) AND eoa.submitted_by IN($supList) ORDER BY eoa.id DESC";

  	$count=mysqli_query($this->conn, $query);
	  // print_r($count);die();

	  if(mysqli_num_rows($count) > 0)
	  {
	    $row=mysqli_fetch_assoc($count);
	      
	    do {    
	      
	      $data['id'] = $row['id'];
	      $data['name'] = $this->getEmpnameByEmpNumber($row['emp_number']);
	      $data['date'] = $row['date'];
	      $data['start_time'] = $row['start_time'];
	      $data['end_time'] = $row['end_time'];
	      $data['hours'] = $row['hours'];
	      $data['statusId'] = $row['status'];
	      $data['claim_type'] = $row['claim_type'];
	      $data['comment'] = '';

	      if($row['status'] == 3){
	      	$data['status'] = 'Verified';
	      }

	      $data1[] = $data;
	    }while($row = mysqli_fetch_assoc($count));
	    $data['myOtList']=$data1;
	    $data['status'] = 1;
	        
	  }else{
	    $data['status'] = 0;
	  }
  }else if($userDetails['id'] == 6){
 //  	$supList =[];
	// $query1="SELECT * FROM `hs_hr_employee` where emp_number=$emp_number";
	// $count1=mysqli_query($this->conn, $query1);
	// $row1=mysqli_fetch_assoc($count1);
	// $work_station = $row1['work_station'];
	// $querySup ="SELECT * FROM `hs_hr_emp_reportto` as rt LEFT JOIN hs_hr_employee as e ON e.emp_number=rt.erep_sup_emp_number WHERE e.work_station=$work_station GROUP BY rt.erep_sup_emp_number";
	// $countquerySup=mysqli_query($this->conn, $querySup);
	// $rowSup=mysqli_fetch_assoc($countquerySup);
	// $supList = $rowSup['erep_sup_emp_number'];


  	$query="SELECT eoa.*,(SELECT l.claim_type FROM `erp_ot_log` as l WHERE l.ot_id=eoa.id order BY l.id desc limit 1) as claim_type FROM `erp_ot_apply` as eoa  WHERE eoa.status=4 ORDER BY eoa.id DESC";

  	$count=mysqli_query($this->conn, $query);
	  // print_r($count);die();

	  if(mysqli_num_rows($count) > 0)
	  {
	    $row=mysqli_fetch_assoc($count);
	      
	    do {    
	      
	      $data['id'] = $row['id'];
	      $data['name'] = $this->getEmpnameByEmpNumber($row['emp_number']);
	      $data['date'] = $row['date'];
	      $data['start_time'] = $row['start_time'];
	      $data['end_time'] = $row['end_time'];
	      $data['hours'] = $row['hours'];
	      $data['statusId'] = $row['status'];
	      $data['claim_type'] = $row['claim_type'];
	      $data['comment'] = '';

	      if($row['status'] == 3){
	      	$data['status'] = 'Verified';
	      }else if($row['status'] == 4){
	      	$data['status'] = 'Approved';
	      }

	      $data1[] = $data;
	    }while($row = mysqli_fetch_assoc($count));
	    $data['myOtList']=$data1;
	    $data['status'] = 1;
	        
	  }else{
	    $data['status'] = 0;
	  }
  }else{
  		$data['myOtList']=array();
	    $data['status'] = 0;
  }    

  return $data;
  // $query="SELECT * FROM erp_ot_apply WHERE is_deleted = 0 AND emp_number = $emp_number";
}

function claimOT($user_id,$status_id,$ot_id,$claim_type,$notes){

	// echo $claim_type;die();

	$userDetails = $this->getUserRoleByUserId($user_id);
	// print_r($userDetails);die();
    $submittedby = $userDetails['empNumber'];
    // echo $userDetails['id'];die();
	$created_by_name = $this->getEmpnameByEmpNumber($submittedby);
	$submittedOn = date('Y-m-d H:i:s');

  $data=array();

	if($status_id == 2)
	{

	    $updatesql = "UPDATE erp_ot_apply SET status = $status_id WHERE id = $ot_id";
	    if($result2 = mysqli_query($this->conn, $updatesql)){

	    	$query="INSERT INTO erp_ot_log (ot_id,performed_by,performed_on,status,claim_type,notes) VALUES (?,?,?,?,?,?)";
			
    		if($stmt = mysqli_prepare($this->conn, $query)){
	     		mysqli_stmt_bind_param($stmt, "iisiis" ,$ot_id,$submittedby,$submittedOn,$status_id,$claim_type,$notes);
	    			   
					if(mysqli_stmt_execute($stmt)){
						$data['status']=1;
					} 
			}else{
				$data['status']=0;
			}

	      $data['log'] = "Successfully Claimed";
	      $data['status']=1;

	    }
	    else{
	    //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
	    $data['status']=0;
	    }

	}

	if($status_id == 3)
	{

	    $updatesql = "UPDATE erp_ot_apply SET status = $status_id WHERE id = $ot_id";
	    if($result2 = mysqli_query($this->conn, $updatesql)){

	    	$query="INSERT INTO erp_ot_log (ot_id,performed_by,performed_on,status,claim_type,notes) VALUES (?,?,?,?,?,?)";
			
    		if($stmt = mysqli_prepare($this->conn, $query)){
	     		mysqli_stmt_bind_param($stmt, "iisiis" ,$ot_id,$submittedby,$submittedOn,$status_id,$claim_type,$notes);
	    			   
					if(mysqli_stmt_execute($stmt)){
						$data['status']=1;
					} 
			}else{
				$data['status']=0;
			}

	      $data['log'] = "Successfully Verified";
	      $data['status']=1;

	    }
	    else{
	    //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
	    $data['status']=0;
	    }

	}

	if($status_id == 4)
	{

	    $updatesql = "UPDATE erp_ot_apply SET status = $status_id WHERE id = $ot_id";
	    if($result2 = mysqli_query($this->conn, $updatesql)){

	    	$query="INSERT INTO erp_ot_log (ot_id,performed_by,performed_on,status,claim_type,notes) VALUES (?,?,?,?,?,?)";
			
    		if($stmt = mysqli_prepare($this->conn, $query)){
	     		mysqli_stmt_bind_param($stmt, "iisiis" ,$ot_id,$submittedby,$submittedOn,$status_id,$claim_type,$notes);
	    			   
					if(mysqli_stmt_execute($stmt)){
						$data['status']=1;
					} 
			}else{
				$data['status']=0;
			}

	      $data['log'] = "Successfully Approved";
	      $data['status']=1;

	    }
	    else{
	    //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
	    $data['status']=0;
	    }

	}

	if($status_id == 5)
	{

	    $updatesql = "UPDATE erp_ot_apply SET status = $status_id WHERE id = $ot_id";
	    if($result2 = mysqli_query($this->conn, $updatesql)){

	    	$query="INSERT INTO erp_ot_log (ot_id,performed_by,performed_on,status,claim_type,notes) VALUES (?,?,?,?,?,?)";
			
    		if($stmt = mysqli_prepare($this->conn, $query)){
	     		mysqli_stmt_bind_param($stmt, "iisiis" ,$ot_id,$submittedby,$submittedOn,$status_id,$claim_type,$notes);
	    			   
					if(mysqli_stmt_execute($stmt)){
						$data['status']=1;
					} 
			}else{
				$data['status']=0;
			}

	      $data['log'] = "Successfully Rejected";
	      $data['status']=1;

	    }
	    else{
	    //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
	    $data['status']=0;
	    }

	}

	if($status_id == 6 && $userDetails['id'] == 6){

	    $updatesql = "UPDATE erp_ot_apply SET status = $status_id WHERE id = $ot_id";
	    if($result2 = mysqli_query($this->conn, $updatesql)){

	    	$query="INSERT INTO erp_ot_log (ot_id,performed_by,performed_on,status,claim_type,notes) VALUES (?,?,?,?,?,?)";
			
    		if($stmt = mysqli_prepare($this->conn, $query)){
	     		mysqli_stmt_bind_param($stmt, "iisiis" ,$ot_id,$submittedby,$submittedOn,$status_id,$claim_type,$notes);
	    			   
					if(mysqli_stmt_execute($stmt)){
						$data['status']=1;
						$empQuery1="SELECT * FROM `erp_ot_apply` where id=$ot_id";
						$empCount1=mysqli_query($this->conn, $empQuery1);
						$empRow1=mysqli_fetch_assoc($empCount1);
						$otEmpNumber = $empRow1['emp_number'];
						$otHours = $empRow1['hours'];

						if($otEmpNumber){
							$leaveEntQuery1="SELECT * FROM `erp_leave_entitlement` as e WHERE e.emp_number=$otEmpNumber AND e.leave_type_id=5";
							$leaveEntCount1=mysqli_query($this->conn, $leaveEntQuery1);
							if(mysqli_num_rows($leaveEntCount1) > 0){
								$LeaveEntRow1=mysqli_fetch_assoc($leaveEntCount1);
									$cmHours = $LeaveEntRow1['no_of_hours']+$otHours;
									$cmDays = $LeaveEntRow1['no_of_days'];
									$cmLeavesId = $LeaveEntRow1['id'];

								$totHors  = $cmHours;
								$hourdays = $totHors/8 ;
								if($totHors >= 8){
								$days = explode('.',$totHors);
								$days1 = $days[0] / 8;
								$days2 = $cmDays + abs(floor($days1));
								$daysC2 = abs(floor($days1));
								$subHours = abs($totHors - ($daysC2*8));
								// echo 'days '.$days2;
								// echo 'hours '.$subHours;die();

								$updateCmsql = "UPDATE erp_leave_entitlement SET no_of_days = $days2,no_of_hours=$subHours WHERE id = $cmLeavesId";
								}else{
								// echo "mintues".$totHors;die();
								$updateCmsql = "UPDATE erp_leave_entitlement SET no_of_hours = $totHors WHERE id = $cmLeavesId";
								}
								$cmresult2 = mysqli_query($this->conn, $updateCmsql);

								// $updateClaimedQuery = "UPDATE erp_ot_apply SET status = $status_id WHERE id = $ot_id";
								// mysqli_query($this->conn, $updateClaimedQuery);

								// $logquery="INSERT INTO erp_ot_log (ot_id,performed_by,performed_on,status,claim_type,notes) VALUES (?,?,?,?,?,?)";
								// $stmtlog = mysqli_prepare($this->conn, $logquery);
								// mysqli_stmt_bind_param($stmtlog, "iisiis" ,$ot_id,$submittedby,$submittedOn,$status_id,$list_claim_type,$list_notes);
								// mysqli_stmt_execute($stmtlog);

								if($cmresult2){
									$data['status']=1;
									$data['log'] = "Successfully claimed";
								}else{
									$data['status']=0;
									$data['log'] = "failed claimed process";
								}
							}else{

								$totHors  = $otHours;
								$hourdays = $totHors/8 ;
								if($totHors >= 8){
									$days = explode('.',$totHors);
									$days1 = $days[0] / 8;
									$days2 = floor($days1);
									$subHours = $totHors - ($days2*8);
									$no_of_days =abs($days2);
									$no_of_hours =abs($subHours);
								}else{
									$no_of_days =0;
									$no_of_hours =abs($totHors);
								}
								

								$leave_type_id =5;
								$from_date = date('Y').'-'.'01-01 00:00:00';
								$to_date = date('Y').'-'.'12-31 00:00:00';
								$notes ='OT Compensatory off';
								$entitlement_type =1;
								$credited_date =date('Y-m-d H:i:s');
								$created_by_id = $userDetails['empNumber'];

								$addEntquery="INSERT INTO erp_leave_entitlement (emp_number,no_of_days,no_of_hours,leave_type_id,from_date,to_date,note,entitlement_type,credited_date,created_by_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
								$entAddstmt = mysqli_prepare($this->conn, $addEntquery);
								// echo $addEntquery;die();
								mysqli_stmt_bind_param($entAddstmt, "ississsisi" ,$otEmpNumber,$no_of_days,$no_of_hours,$leave_type_id,$from_date,$to_date,$notes,$entitlement_type,$credited_date,$created_by_id);

								$otClaimAdd = mysqli_stmt_execute($entAddstmt);

								$updateClaimedQuery = "UPDATE erp_ot_apply SET status = $status_id WHERE id = $ot_id";
								mysqli_query($this->conn, $updateClaimedQuery);

								$logquery="INSERT INTO erp_ot_log (ot_id,performed_by,performed_on,status,claim_type,notes) VALUES (?,?,?,?,?,?)";
								$stmtlog = mysqli_prepare($this->conn, $logquery);
								mysqli_stmt_bind_param($stmtlog, "iisiis" ,$ot_id,$submittedby,$submittedOn,$status_id,$list_claim_type,$list_notes);
								mysqli_stmt_execute($stmtlog);


								if($otClaimAdd){
									$data['status']=1;
									$data['log'] = "Claimed Successfully";
								}else{
									$data['status']=0;
									$data['log'] = "Claimed accept failed";
								}
							}
						}else{
							$data['status']=0;
						}



					} 
			}else{
				$data['status']=0;
			}

	      $data['log'] = "Successfully Approved";
	      $data['status']=1;

	    }
	    else{
	    //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
	    $data['status']=0;
	    }

	}

	if($status_id == 7)
	{

	    $updatesql = "UPDATE erp_ot_apply SET status = $status_id WHERE id = $ot_id";
	    if($result2 = mysqli_query($this->conn, $updatesql)){

	    	$query="INSERT INTO erp_ot_log (ot_id,performed_by,performed_on,status,claim_type,notes) VALUES (?,?,?,?,?,?)";
			
    		if($stmt = mysqli_prepare($this->conn, $query)){
	     		mysqli_stmt_bind_param($stmt, "iisiis" ,$ot_id,$submittedby,$submittedOn,$status_id,$claim_type,$notes);
	    			   
					if(mysqli_stmt_execute($stmt)){
						$data['status']=1;
					} 
			}else{
				$data['status']=0;
			}

	      $data['log'] = "Successfully Approved";
	      $data['status']=1;

	    }
	    else{
	    //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
	    $data['status']=0;
	    }

	}

  	return $data;
}


function bulkOtClaim($user_id,$status_id,$ot_id){
 
	$userDetails = $this->getUserRoleByUserId($user_id);
    $submittedby = $userDetails['empNumber'];
	$created_by_name = $this->getEmpnameByEmpNumber($submittedby);
	$submittedOn = date('Y-m-d H:i:s');

	$supervisior = $this->isSupervisor($userDetails['empNumber']);

  $data=array();


	if(($supervisior == 2 || $userDetails['id'] == 17) && !empty($ot_id))
	{
		for($s=0;$s<sizeof($ot_id);$s++){
			$list_ot_id = $ot_id[$s]['ot_id'];
			$list_claim_type = $ot_id[$s]['claim_type'];
			$list_notes = $ot_id[$s]['notes'];
	    	$updatesql = "UPDATE erp_ot_apply SET status = $status_id WHERE id = $list_ot_id";

		    if($result2 = mysqli_query($this->conn, $updatesql)){

		    	$query="INSERT INTO erp_ot_log (ot_id,performed_by,performed_on,status,claim_type,notes) VALUES (?,?,?,?,?,?)";
				
	    		if($stmt = mysqli_prepare($this->conn, $query)){
		     		mysqli_stmt_bind_param($stmt, "iisiis" ,$list_ot_id,$submittedby,$submittedOn,$status_id,$list_claim_type,$list_notes);
		    			   
						if(mysqli_stmt_execute($stmt)){
							$data['status']=1;
						} 
				}else{
					$data['status']=0;
				}
			  if($supervisior == 2){
		      $data['log'] = "Successfully Verified";
			  }else{
		      $data['log'] = "Successfully Approved";
			  }
		      $data['status']=1;

		    }
		}

	}else if($userDetails['id'] == 6 && !empty($ot_id) && $status_id ==6){

			for($s=0;$s<sizeof($ot_id);$s++){
				$list_ot_id = $ot_id[$s]['ot_id'];
				$list_claim_type = $ot_id[$s]['claim_type'];
				$list_notes = $ot_id[$s]['notes'];


				$empQuery1="SELECT * FROM `erp_ot_apply` where id=$list_ot_id";
				$empCount1=mysqli_query($this->conn, $empQuery1);
				$empRow1=mysqli_fetch_assoc($empCount1);
				$otEmpNumber = $empRow1['emp_number'];
				$otHours = $empRow1['hours'];



				if($otEmpNumber){
							$leaveEntQuery1="SELECT * FROM `erp_leave_entitlement` as e WHERE e.emp_number=$otEmpNumber AND e.leave_type_id=5";
						$leaveEntCount1=mysqli_query($this->conn, $leaveEntQuery1);
							if(mysqli_num_rows($leaveEntCount1) > 0){
								$LeaveEntRow1=mysqli_fetch_assoc($leaveEntCount1);
									$cmHours = $LeaveEntRow1['no_of_hours']+$otHours;
									$cmLeavesId = $LeaveEntRow1['id'];

								$totHors  = $cmHours;
								$hourdays = $totHors/8 ;
								if($totHors >= 8){
								$days = explode('.',$totHors);
								$days1 = $days[0] / 8;
								$days2 = floor($days1);
								$subHours = $totHors - ($days2*8);
								// echo 'days '.$days2;
								// echo 'hours '.$subHours;die();

								$updateCmsql = "UPDATE erp_leave_entitlement SET no_of_days = $days2,no_of_hours=$subHours WHERE id = $cmLeavesId";
								}else{
								// echo "mintues".$totHors;die();
								$updateCmsql = "UPDATE erp_leave_entitlement SET no_of_hours = $totHors WHERE id = $cmLeavesId";
								}
								$cmresult2 = mysqli_query($this->conn, $updateCmsql);

								$updateClaimedQuery = "UPDATE erp_ot_apply SET status = $status_id WHERE id = $list_ot_id";
								mysqli_query($this->conn, $updateClaimedQuery);

								$logquery="INSERT INTO erp_ot_log (ot_id,performed_by,performed_on,status,claim_type,notes) VALUES (?,?,?,?,?,?)";
								$stmtlog = mysqli_prepare($this->conn, $logquery);
								mysqli_stmt_bind_param($stmtlog, "iisiis" ,$list_ot_id,$submittedby,$submittedOn,$status_id,$list_claim_type,$list_notes);
								mysqli_stmt_execute($stmtlog);

								if($cmresult2){
									$data['status']=1;
									$data['log'] = "Successfully claimed";
								}else{
									$data['status']=0;
									$data['log'] = "failed claimed process";
								}
							}else{

								$totHors  = $otHours;
								$hourdays = $totHors/8 ;
								if($totHors >= 8){
									$days = explode('.',$totHors);
									$days1 = $days[0] / 8;
									$days2 = floor($days1);
									$subHours = $totHors - ($days2*8);
									$no_of_days =$days2;
									$no_of_hours =$subHours;
								}else{
									$no_of_days =0;
									$no_of_hours =$totHors;
								}
								

								$leave_type_id =5;
								$from_date = date('Y').'-'.'01-01 00:00:00';
								$to_date = date('Y').'-'.'12-31 00:00:00';
								$notes ='OT Compensatory off';
								$entitlement_type =1;
								$credited_date =date('Y-m-d H:i:s');
								$created_by_id = $userDetails['empNumber'];

								$addEntquery="INSERT INTO erp_leave_entitlement (emp_number,no_of_days,no_of_hours,leave_type_id,from_date,to_date,note,entitlement_type,credited_date,created_by_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
								$entAddstmt = mysqli_prepare($this->conn, $addEntquery);
								// echo $addEntquery;die();
								mysqli_stmt_bind_param($entAddstmt, "ississsisi" ,$otEmpNumber,$no_of_days,$no_of_hours,$leave_type_id,$from_date,$to_date,$notes,$entitlement_type,$credited_date,$created_by_id);

								$otClaimAdd = mysqli_stmt_execute($entAddstmt);

								$updateClaimedQuery = "UPDATE erp_ot_apply SET status = $status_id WHERE id = $list_ot_id";
								mysqli_query($this->conn, $updateClaimedQuery);

								$logquery="INSERT INTO erp_ot_log (ot_id,performed_by,performed_on,status,claim_type,notes) VALUES (?,?,?,?,?,?)";
								$stmtlog = mysqli_prepare($this->conn, $logquery);
								mysqli_stmt_bind_param($stmtlog, "iisiis" ,$list_ot_id,$submittedby,$submittedOn,$status_id,$list_claim_type,$list_notes);
								mysqli_stmt_execute($stmtlog);


								if($otClaimAdd){
									$data['status']=1;
									$data['log'] = "Claimed Successfully";
								}else{
									$data['status']=0;
									$data['log'] = "Claimed accept failed";
								}
							}
				}else{
				$data['status']=0;
				$data['log'] = "Claimed accept failed";
				}

			}
	}else{
		 $data['log'] = "Failed Verification";
		$data['status']=0;
	}

	
  	return $data;
}


///////////// Overtime Start ////////////////////////////


//////////// Ramu End    ////////////////////////////////////

function leaveCountNew($user_id,$emp_number,$leaveType)
	{

		$userDetails = $this->getUserRoleByUserId($user_id);
    	//$emp_number = $userDetails['empNumber'];

		$data= array();
		$queryEnt="SELECT e.no_of_days as sumEnt FROM `erp_leave_entitlement` as e WHERE (e.emp_number=$emp_number AND e.leave_type_id=$leaveType) AND (e.from_date='".date('Y')."-01-01' AND e.to_date='".date('Y')."-12-31') limit 1";
		$countEnt=mysqli_query($this->conn, $queryEnt);	
		$rowEnt=mysqli_fetch_array($countEnt);
		if(!empty($rowEnt['sumEnt'])){
			$sumEnt = $rowEnt['sumEnt'];
		}else{
			$sumEnt = 0;
		}

		// echo $sumEnt;die();

		$queryUsed="SELECT SUM(l.length_days) as usedEnt FROM `erp_leave` as l WHERE YEAR(date) = '".date('Y')."' AND l.emp_number=$emp_number AND l.leave_type_id=$leaveType AND l.status IN(1,2,3)";
		$countUsed=mysqli_query($this->conn, $queryUsed);	
		$rowUsed=mysqli_fetch_array($countUsed);
		if(!empty($rowUsed['usedEnt'])){
			$usedEnt = $rowUsed['usedEnt'];
		}else{
			$usedEnt = 0;
		}

		// echo 'total leves : '.$sumEnt.' used leves : '.$usedEnt;

		$balanceLeaves = $sumEnt - $usedEnt;



		// echo ' balance : '.round($balanceLeaves, 2);die();

		$query="SELECT lel.*,(SELECT SUM(le.no_of_days) FROM `erp_leave_entitlement` as le WHERE le.emp_number=$emp_number AND le.leave_type_id=$leaveType ) - (SELECT COUNT(l.length_days) FROM `erp_leave` as l WHERE l.emp_number=$emp_number AND (l.leave_type_id=$leaveType AND l.status NOT IN (-1,0,4,5))) AS Difference FROM erp_leave_entitlement as lel WHERE lel.emp_number=$emp_number AND lel.leave_type_id=$leaveType";
		$count=mysqli_query($this->conn, $query);	
			
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
				
					do{ 

						$data['id']=$row['id'];
						$data['emp_number']=$row['emp_number'];
						// $data['no_of_days']=$row['no_of_days'];
						if(!empty($row['Difference'])){
						$data['no_of_days']=$sumEnt;
						}else{
						$data['no_of_days']=$sumEnt;

						}
						$data['days_used']=$usedEnt;
						$data['balance_leaves']=round($balanceLeaves, 2);
						$data['leave_type_id']=$row['leave_type_id'];
						$data['from_date']=$row['from_date'];
						$data['to_date']=$row['to_date'];
						$data['credited_date']=$row['credited_date'];
						$data['note']=$row['note'];
						$data['entitlement_type']=$row['entitlement_type'];
						$data['deleted']=$row['deleted'];
						$data['created_by_id']=$row['created_by_id'];
						$data['created_by_name']=$row['created_by_name'];
							
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['leaveCount']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
		}
		return $data;    
	}
 
 
//chandu

function leaveCount($user_id,$emp_number,$leaveType)
	{

		$userDetails = $this->getUserRoleByUserId($user_id);
    	//$emp_number = $userDetails['empNumber'];

		$data= array();
		$query="SELECT lel.*,(SELECT SUM(le.no_of_days) FROM `erp_leave_entitlement` as le WHERE le.emp_number=$emp_number AND le.leave_type_id=$leaveType ) - (SELECT COUNT(l.length_days) FROM `erp_leave` as l WHERE l.emp_number=$emp_number AND (l.leave_type_id=$leaveType AND l.status NOT IN (-1,0,4,5))) AS Difference FROM erp_leave_entitlement as lel WHERE lel.emp_number=$emp_number AND lel.leave_type_id=$leaveType";
		$count=mysqli_query($this->conn, $query);		
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
				
					do{ 

						$data['id']=$row['id'];
						$data['emp_number']=$row['emp_number'];
						// $data['no_of_days']=$row['no_of_days'];
						if(!empty($row['Difference'])){
						$data['no_of_days']=$row['Difference'];
						}else{
						$data['no_of_days']=$row['no_of_days'];

						}
						$data['days_used']=$row['days_used'];
						$data['leave_type_id']=$row['leave_type_id'];
						$data['from_date']=$row['from_date'];
						$data['to_date']=$row['to_date'];
						$data['credited_date']=$row['credited_date'];
						$data['note']=$row['note'];
						$data['entitlement_type']=$row['entitlement_type'];
						$data['deleted']=$row['deleted'];
						$data['created_by_id']=$row['created_by_id'];
						$data['created_by_name']=$row['created_by_name'];
							
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['leaveCount']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	} 



	 function subordinateLeavelist($user_id,$status)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);
    	$supervisor = $userDetails['empNumber'];
        $data=array();  			

		$subObj = $this->subordinateByEmpList($supervisor);  

		if($subObj['status'] == 1){
					for ($i=0; $i < sizeof($subObj['emplist']); $i++) { 
					    $empList[] = $subObj['emplist'][$i]['emp_number'];
					        	//to convert Array into string the following implode method is used
					    $empLists = implode(',', $empList);
					}

				$query="SELECT l.*,s.name FROM erp_leave as l LEFT JOIN erp_leave_status as s ON s.status=l.status WHERE l.emp_number IN ($empLists) AND l.status=$status order by l.status ASC, l.date DESC";
				// $query="SELECT * FROM erp_leave WHERE emp_number IN ($empLists) and status=1";
				$count=mysqli_query($this->conn, $query);

				if(mysqli_num_rows($count) > 0)
				{
						$row=mysqli_fetch_assoc($count);
					do{ 

								$data['id']=$row['id'];
								$data['date']=$row['date'];
								$data['length_hours']=$row['length_hours'];
								$data['length_days']=$row['length_days'];
								$data['status']=$row['status'];
								$data['status_name']=$row['name'];
								$data['comments']=$row['comments'];
								$data['leave_request_id']=$row['leave_request_id'];
								$data['leave_type_id']=$row['leave_type_id'];
								$data['emp_number']=$row['emp_number'];
								$data['emp_name']=$this->getEmpnameByEmpNumber($row['emp_number']);
								$data['start_time']=$row['start_time'];
								$data['end_time']=$row['end_time'];
								$data['duration_type']=$row['duration_type'];
									
								$data1[] = $data;
						}while($row = mysqli_fetch_assoc($count)); 				
								$data['subOrdinateleaves']=$data1;
								$data['status']=1;
								$data['message']="Success";
									
				}else{
						$data['status']=0;
						$data['message']="Failed";
				}
		}else if($userDetails['id'] == 22){

			$query="SELECT * FROM erp_leave as l WHERE l.status=$status order by l.status ASC, l.date DESC";
				// $query="SELECT * FROM erp_leave WHERE emp_number IN ($empLists) and status=1";
				$count=mysqli_query($this->conn, $query);

				if(mysqli_num_rows($count) > 0)
				{
						$row=mysqli_fetch_assoc($count);

						do{ 

								$data['id']=$row['id'];
								$data['date']=$row['date'];
								$data['length_hours']=$row['length_hours'];
								$data['length_days']=$row['length_days'];
								$data['status']=$row['status'];
								$data['comments']=$row['comments'];
								$data['leave_request_id']=$row['leave_request_id'];
								$data['leave_type_id']=$row['leave_type_id'];
								$data['emp_number']=$row['emp_number'];
								$data['emp_name']=$this->getEmpnameByEmpNumber($row['emp_number']);
								$data['start_time']=$row['start_time'];
								$data['end_time']=$row['end_time'];
								$data['duration_type']=$row['duration_type'];
									
								$data1[] = $data;
						}while($row = mysqli_fetch_assoc($count)); 				
								$data['subOrdinateleaves']=$data1;
								$data['status']=1;
								$data['message']="Success";
									
				}else{
						$data['status']=0;
						$data['message']="Failed";
				}


		}else{
			$data['status']=0;
			$data['message']="Failed";
		}

		return $data; 
    }

	function MyLeavelist($user_id,$status)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);
    	$empNumber = $userDetails['empNumber'];

        $data=array();  			
		$query="SELECT * FROM erp_leave WHERE emp_number = $empNumber AND status=$status order by status asc, id desc";
		$count=mysqli_query($this->conn, $query);

		// print_r($count);die();

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
				do{ 

						$data['id']=$row['id'];
						$data['date']=$row['date'];
						$data['length_hours']=$row['length_hours'];
						$data['length_days']=$row['length_days'];
						$data['status']=$row['status'];
						$data['comments']=$row['comments'];
						$data['leave_request_id']=$row['leave_request_id'];
						$data['leave_type_id']=$row['leave_type_id'];
						$data['emp_number']=$row['emp_number'];
						$data['start_time']=$row['start_time'];
						$data['end_time']=$row['end_time'];
						$data['duration_type']=$row['duration_type'];
							
						$data1[] = $data;
				}while($row = mysqli_fetch_assoc($count)); 				
						$data['MyLeaves']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data; 
    }

    function appNewNotificationsCount($user_id)
    {
		$userDetails = $this->getUserRoleByUserId($user_id);
    	$supervisor = $userDetails['empNumber'];
    	$emp_number = $userDetails['empNumber'];

    	$empNumber = $userDetails['empNumber'];
    	$roleId = $userDetails['id'];

    	// echo $roleId;die();

    	$empquery="SELECT * FROM `hs_hr_employee` as e WHERE e.emp_number=$empNumber";
		$empcount=mysqli_query($this->conn, $empquery);
		$emprow=mysqli_fetch_assoc($empcount);
		$departmentId = $emprow['work_station'];

		// echo $departmentId;die();

        $data=array();  			

		$subObj = $this->subordinateByEmpList($supervisor);  

		// print_r($subObj);die();

		if($subObj['status'] == 1 && $roleId !=22){
					for ($i=0; $i < sizeof($subObj['emplist']); $i++) { 
					    $empList[] = $subObj['emplist'][$i]['emp_number'];
					    $empLists = implode(',', $empList);
					}

				// $query="SELECT * FROM erp_leave WHERE emp_number IN ($empLists) order by id desc";

				$leavequery="SELECT * FROM erp_leave WHERE emp_number IN ($empLists) and status=1";
				$leavecount=mysqli_query($this->conn, $leavequery);
				if(mysqli_num_rows($leavecount) > 0)
				{
			 		$MyLeaves_count = mysqli_num_rows($leavecount);
					$data['leaves_count']=$MyLeaves_count;
									
				}else{
					$data['leaves_count']=0;
				}

				$permsquery = "SELECT ep.* FROM erp_permission AS ep LEFT JOIN erp_permission_action_log AS epLog ON epLog.permission_id = ep.id  WHERE ep.submitted_by IN ($empLists) AND ep.status_id =2 order by id desc";

				$permscount=mysqli_query($this->conn, $permsquery);
				if(mysqli_num_rows($permscount) > 0)
				{
			 		$perms_count = mysqli_num_rows($permscount);
					$data['perms_count']=$perms_count;
									
				}else{
					$data['perms_count']=0;
				}

				$otquery="SELECT eoa.*,(SELECT l.claim_type FROM `erp_ot_log` as l WHERE l.ot_id=eoa.id order BY l.id desc limit 1) as claim_type FROM `erp_ot_apply` as eoa  WHERE eoa.status IN (1,2) AND eoa.submitted_by=$emp_number ORDER BY eoa.id DESC";

				$otcount=mysqli_query($this->conn, $otquery);
				if(mysqli_num_rows($otcount) > 0)
				{
			 		$ot_count = mysqli_num_rows($otcount);
					$data['ot_count']=$ot_count;
									
				}else{
					$data['ot_count']=0;
				}




				$requisitionquery = "SELECT r.*,jt.job_title as jobTitle,l.name as locationName,d.name as departmentName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.report_to=e.emp_number) as reportToName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.submitted_by=e.emp_number) as submittedBy FROM `erp_manpower_requisition` as r LEFT JOIN erp_job_title as jt ON r.job_title=jt.id LEFT JOIN erp_location as l on r.location=l.id LEFT JOIN erp_subunit as d ON r.department=d.id WHERE r.action_status IN(0,1,2) AND r.submitted_by=$emp_number";

				$requisitioncount=mysqli_query($this->conn, $requisitionquery);
				if(mysqli_num_rows($requisitioncount) > 0)
				{
			 		$req_count = mysqli_num_rows($requisitioncount);
					$data['requisition_count']=$req_count;
									
				}else{
					$data['requisition_count']=0;
				}

				



				if(!empty($data)){
					$data['status']=1;
					$data['message']="Success";
				}else{
					$data['status']=0;
					$data['message']="Failed";
				}

		}else if($roleId == 17){
			$requisitionquery = "SELECT r.*,jt.job_title as jobTitle,l.name as locationName,d.name as departmentName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.report_to=e.emp_number) as reportToName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.submitted_by=e.emp_number) as submittedBy FROM `erp_manpower_requisition` as r LEFT JOIN erp_job_title as jt ON r.job_title=jt.id LEFT JOIN erp_location as l on r.location=l.id LEFT JOIN erp_subunit as d ON r.department=d.id WHERE r.action_status=1 AND r.department=$departmentId";

				$requisitioncount=mysqli_query($this->conn, $requisitionquery);
				if(mysqli_num_rows($requisitioncount) > 0)
				{
			 		$req_count = mysqli_num_rows($requisitioncount);
					$data['requisition_count']=$req_count;
									
				}else{
					$data['requisition_count']=0;
				}

				$supList =[];
				$query1="SELECT * FROM `hs_hr_employee` where emp_number=$emp_number";
				$count1=mysqli_query($this->conn, $query1);
				$row1=mysqli_fetch_assoc($count1);
				$work_station = $row1['work_station'];
				$querySup ="SELECT * FROM `hs_hr_emp_reportto` as rt LEFT JOIN hs_hr_employee as e ON e.emp_number=rt.erep_sup_emp_number WHERE e.work_station=$work_station GROUP BY rt.erep_sup_emp_number";
				$countquerySup=mysqli_query($this->conn, $querySup);
				$rowSup=mysqli_fetch_assoc($countquerySup);
				$supList = $rowSup['erep_sup_emp_number'];


				$otquery="SELECT eoa.*,(SELECT l.claim_type FROM `erp_ot_log` as l WHERE l.ot_id=eoa.id order BY l.id desc limit 1) as claim_type FROM `erp_ot_apply` as eoa  WHERE eoa.status=3 AND eoa.submitted_by IN($supList) ORDER BY eoa.status ASC";

				$otcount=mysqli_query($this->conn, $otquery);
				if(mysqli_num_rows($otcount) > 0)
				{
			 		$ot_count = mysqli_num_rows($otcount);
					$data['ot_count']=$ot_count;
									
				}else{
					$data['ot_count']=0;
				}

				$permsquery = "SELECT p.* FROM erp_permission AS p LEFT JOIN hs_hr_employee as e ON p.submitted_by=e.emp_number WHERE p.status_id =8 AND e.work_station=$departmentId order by id desc";

				$permscount=mysqli_query($this->conn, $permsquery);
				if(mysqli_num_rows($permscount) > 0)
				{
			 		$perms_count = mysqli_num_rows($permscount);
					$data['perms_count']=$perms_count;
									
				}else{
					$data['perms_count']=0;
				}

				// $data['perms_count']=0;
				$data['leaves_count']=0;

				if(!empty($data)){
					$data['status']=1;
					$data['message']="Success";
				}else{
					$data['status']=0;
					$data['message']="Failed";
				}
		}else if($roleId == 22){
			$requisitionquery = "SELECT r.*,jt.job_title as jobTitle,l.name as locationName,d.name as departmentName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.report_to=e.emp_number) as reportToName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.submitted_by=e.emp_number) as submittedBy FROM `erp_manpower_requisition` as r LEFT JOIN erp_job_title as jt ON r.job_title=jt.id LEFT JOIN erp_location as l on r.location=l.id LEFT JOIN erp_subunit as d ON r.department=d.id WHERE r.submitted_by=$emp_number";

				$requisitioncount=mysqli_query($this->conn, $requisitionquery);
				if(mysqli_num_rows($requisitioncount) > 0)
				{
			 		$req_count = mysqli_num_rows($requisitioncount);
					$data['requisition_count']=$req_count;
									
				}else{
					$data['requisition_count']=0;
				}

				$supList =[];
				$query1="SELECT * FROM `hs_hr_employee` where emp_number=$emp_number";
				$count1=mysqli_query($this->conn, $query1);
				$row1=mysqli_fetch_assoc($count1);
				$work_station = $row1['work_station'];


				$querySup ="SELECT * FROM  hs_hr_employee as e  WHERE e.work_station=$work_station AND e.termination_id is NULL";
				$countquerySup=mysqli_query($this->conn, $querySup);
				$rowSup=mysqli_fetch_assoc($countquerySup);
				// $supList = $rowSup['erep_sup_emp_number'];


				if(mysqli_num_rows($countquerySup) > 0)
				{
				$rowSup=mysqli_fetch_assoc($countquerySup);
				do { 						
				$supList[] = $rowSup['emp_number'];
				}while($rowSup=mysqli_fetch_assoc($countquerySup));
				}

				// print_r($supList);die();

				if(!empty($supList)){
				$supListArr = implode(',',$supList);
				}else{
				$supListArr =null;
				}

				// $querySup ="SELECT * FROM `hs_hr_emp_reportto` as rt LEFT JOIN hs_hr_employee as e ON e.emp_number=rt.erep_sup_emp_number WHERE e.work_station=$work_station GROUP BY rt.erep_sup_emp_number";
				// $countquerySup=mysqli_query($this->conn, $querySup);
				// $rowSup=mysqli_fetch_assoc($countquerySup);
				// $supList = $rowSup['erep_sup_emp_number'];


				$otquery="SELECT eoa.*,(SELECT l.claim_type FROM `erp_ot_log` as l WHERE l.ot_id=eoa.id order BY l.id desc limit 1) as claim_type FROM `erp_ot_apply` as eoa  WHERE eoa.status=3 AND eoa.emp_number IN($supListArr) ORDER BY eoa.status ASC";

				$otcount=mysqli_query($this->conn, $otquery);
				if(mysqli_num_rows($otcount) > 0)
				{
			 		$ot_count = mysqli_num_rows($otcount);
					$data['ot_count']=$ot_count;
									
				}else{
					$data['ot_count']=0;
				}

				// $permsquery = "SELECT p.* FROM erp_permission AS p LEFT JOIN hs_hr_employee as e ON p.submitted_by=e.emp_number WHERE p.status_id =8 AND e.work_station=$departmentId order by id desc";

				// $permscount=mysqli_query($this->conn, $permsquery);
				// if(mysqli_num_rows($permscount) > 0)
				// {
			 // 		$perms_count = mysqli_num_rows($permscount);
				// 	$data['perms_count']=$perms_count;
									
				// }else{
				// 	$data['perms_count']=0;
				// }

				$data['perms_count']=0;
				$data['leaves_count']=0;

				if(!empty($data)){
					$data['status']=1;
					$data['message']="Success";
				}else{
					$data['status']=0;
					$data['message']="Failed";
				}
		}else if($roleId == 6){


				$requisitionquery = "SELECT r.*,jt.job_title as jobTitle,l.name as locationName,d.name as departmentName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.report_to=e.emp_number) as reportToName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.submitted_by=e.emp_number) as submittedBy FROM `erp_manpower_requisition` as r LEFT JOIN erp_job_title as jt ON r.job_title=jt.id LEFT JOIN erp_location as l on r.location=l.id LEFT JOIN erp_subunit as d ON r.department=d.id WHERE r.action_status=2";

				$requisitioncount=mysqli_query($this->conn, $requisitionquery);
				if(mysqli_num_rows($requisitioncount) > 0)
				{
			 		$req_count = mysqli_num_rows($requisitioncount);
					$data['requisition_count']=$req_count;
									
				}else{
					$data['requisition_count']=0;
				}


				$otquery="SELECT eoa.*,(SELECT l.claim_type FROM `erp_ot_log` as l WHERE l.ot_id=eoa.id order BY l.id desc limit 1) as claim_type FROM `erp_ot_apply` as eoa  WHERE eoa.status=4 ORDER BY eoa.status ASC";

				$otcount=mysqli_query($this->conn, $otquery);
				if(mysqli_num_rows($otcount) > 0)
				{
			 		$ot_count = mysqli_num_rows($otcount);
					$data['ot_count']=$ot_count;
									
				}else{
					$data['ot_count']=0;
				}

				$permsquery = "SELECT ep.* FROM erp_permission AS ep  WHERE ep.status_id =9 order by id desc";

				$permscount=mysqli_query($this->conn, $permsquery);
				if(mysqli_num_rows($permscount) > 0)
				{
			 		$perms_count = mysqli_num_rows($permscount);
					$data['perms_count']=$perms_count;
									
				}else{
					$data['perms_count']=0;
				}

				// $leavequery="SELECT * FROM erp_leave WHERE status=2";
				// $leavecount=mysqli_query($this->conn, $leavequery);
				// if(mysqli_num_rows($leavecount) > 0)
				// {
			 // 		$MyLeaves_count = mysqli_num_rows($leavecount);
				// 	$data['leaves_count']=$MyLeaves_count;
									
				// }else{
				// 	$data['leaves_count']=0;
				// }
				$data['leaves_count']=0;

				if(!empty($data)){
					$data['status']=1;
					$data['message']="Success";
				}else{
					$data['status']=0;
					$data['message']="Failed";
				}
		}else{
			$data['status']=0;
			$data['leaves_count']=0;
			$data['perms_count']=0;
			$data['ot_count']=0;
			$data['requisition_count']=0;
			$data['message']="Failed";
		}

		return $data;  
    }

    function getVisitorInPlantCountAndList($user_id)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);
    	$empNumber = $userDetails['empNumber'];
		$userRoleId = $userDetails['id'];
		$employeedetails = $this->employeeDetails($userDetails['empNumber']);
		$work_station = $employeedetails['work_station'];

		// echo $work_station;die();
        $data=array();
        // $query="SELECT v.*,vc.visitor_id as vid,vc.contact_ids as vcontact,vc.name,vc.phone,vc.pass_id FROM `erp_visitor` as v LEFT JOIN erp_visitor_contacts as vc on v.id=vc.visitor_id WHERE v.status_id IN(10,11)";
        // if($userRoleId == 6 || $userRoleId == 30){
		$query2="SELECT count(*)  as visitorsCount FROM `erp_visitor` as v LEFT JOIN erp_visitor_contacts as c ON v.id=c.visitor_id WHERE v.status_id=10";
        // }else{
		$query3="SELECT COUNT(*) as visitorsCountDepartment FROM `erp_visitor` as v LEFT JOIN erp_visitor_contacts as c ON v.id=c.visitor_id WHERE v.deparment_id=$work_station AND v.status_id=10;";
		// $query3="SELECT count(*) as visitorsCountDepartment FROM `erp_visitor` as v WHERE v.status_id=10 AND v.deparment_id=$work_station";
        // }

        // $data=array(); 

		$count2=mysqli_query($this->conn, $query2);
		$row2=mysqli_fetch_assoc($count2);
		$count3=mysqli_query($this->conn, $query3);
		$row3=mysqli_fetch_assoc($count3);
		// echo $row['visitorsCount'];die();

		if($userRoleId == 6 || $userRoleId == 30){
		$query1="SELECT v.id as vid,v.members,v.vehicle_number,v.address,vc.* FROM `erp_visitor` as v LEFT JOIN erp_visitor_contacts as vc on v.id=vc.visitor_id WHERE v.status_id=10 ORDER by id desc";
		}else{
		$query1="SELECT v.id as vid,v.members,v.vehicle_number,v.address,vc.* FROM `erp_visitor` as v LEFT JOIN erp_visitor_contacts as vc on v.id=vc.visitor_id WHERE v.status_id=10 AND v.deparment_id=$work_station ORDER by id desc";
		}


		$contactsList=mysqli_query($this->conn, $query1);
		if(mysqli_num_rows($contactsList) > 0)
		{
				$row1=mysqli_fetch_assoc($contactsList);
			do{ 

						$data['vid']=$row1['vid'];
						$data['members']=$row1['members'];
						$data['vehicle_number']=$row1['vehicle_number'];
						$data['address']=$row1['address'];
						$data['id']=$row1['id'];
						if(!empty($row1['contact_ids'])){
						$data['contact_details']=$this->getEmpnameByEmpNumber($row1['contact_ids']);
						}else{ 
						$data['contact_details']='';
						}
						$data['contact_ids']=$row1['contact_ids'];
						$data['others_details']=$row1['others_details'];
						$data['name']=$row1['name'];
						$data['phone']=$row1['phone'];
						$data['pass_id']=$row1['pass_id'];
					
						$data1[] = $data;
					}while($row1 = mysqli_fetch_assoc($contactsList)); 				
						$data['visitor_personslist']=$data1;
						$data['status']=1;
						$data['visitorsCount'] = $row2['visitorsCount'];
						$data['visitorsCountDepartment'] = $row3['visitorsCountDepartment'];
							
		}else{
				$data['status']=0;
				$data['visitor_personslist']=array();
				$data['visitorsCount'] = 0;
				$data['visitorsCountDepartment']=0;
			}
		return $data; 
    }

    function getEmpLeaveDetails($userId,$date)
	{
      $cdate = date('Y-m-d');
      $pdate = date('d/m/Y');
      $ndate = date('Y-m-d',strtotime($date));
      $userDetails = $this->getUserRoleByUserId($userId);
      $empNumber = $userDetails['empNumber'];
      $empDetails = $this->jobDetails($userId);

      // print_r($empDetails);
      // echo $empDetails['plantId'];die();
      if(!empty($empDetails['plantId'])){
      $plantId =$empDetails['plantId'];
      }else{
      	$plantId =1;
      }
      $query = "SELECT l.date as adt,concat(emp.emp_firstname,' ',emp.emp_lastname) as name,ltype.name as type,stats.name as status FROM erp_leave as l left join hs_hr_employee as emp on l.emp_number=emp.emp_number left join  erp_leave_type as ltype on ltype.id=l.leave_type_id left join erp_leave_status as stats on stats.status=l.status where l.date = '$ndate' AND emp.plant_id='$plantId'";
            

           

    $data=array();              
    $empLeaveResult = mysqli_query($this->conn, $query);
    if(mysqli_num_rows($empLeaveResult) > 0)
    {
            $empLeaveCnt=mysqli_fetch_assoc($empLeaveResult);
        do{ 

                    $data['name']=$empLeaveCnt['name'];
                    $data['type']=$empLeaveCnt['type'];
                    $data['status']=$empLeaveCnt['status'];
                
                    
                
                    $data1[] = $data;
                }while($empLeaveCnt = mysqli_fetch_assoc($empLeaveResult));                 
                    $data['status']=1;
                    $data['empLeaveCount']=mysqli_num_rows($empLeaveResult);
                    $data['empLeaveList']=$data1;
                        
    }else{
            $data['status']=0;
            $data['empLeaveList']=array();
        }
    return $data; 
	}
function getEmpPunchIn($userId)
{
      $cdate = date('Y-m-d');
      $pdate = date('d/m/Y');
      $userDetails = $this->getUserRoleByUserId($userId);
    	$empNumber = $userDetails['empNumber'];
      $departmentId = $this->getEmpDepartmentByEmpNumber($empNumber);
         $query = "SELECT CONCAT(e.emp_firstname,' ',e.emp_lastname)as emp_fullname,e.employee_id as empCode, s.name as department,
            atd.early_in as earlyIn,atd.late_in as lateIn,atd.early_out as earlyOut,atd.late_out as lateOut
            FROM erp_attendance_record as a 
            LEFT JOIN hs_hr_employee e ON a.employee_id = e.emp_number
            LEFT JOIN erp_subunit s ON s.id = e.work_station
            LEFT JOIN erp_attendance_total atd ON atd.emp_number = a.employee_id
            

            WHERE  date(a.punch_in_utc_time) =date('$cdate') and  atd.p_date = '$pdate' and a.state='PUNCHED IN' ";
             if($departmentId !=0){
             $query = $query."AND e.work_station = $departmentId";   
            }
        $query = $query." GROUP by a.employee_id"; 

    $data=array();              
    $empPunchInResult = mysqli_query($this->conn, $query);
    if(mysqli_num_rows($empPunchInResult) > 0)
    {
            $empPunchInCnt=mysqli_fetch_assoc($empPunchInResult);
        do{ 

                    $data['name']=$empPunchInCnt['emp_fullname'];
                    $data['empCode']=$empPunchInCnt['empCode'];
                    $data['department']=$empPunchInCnt['department'];
                    $data['earlyIn']=$empPunchInCnt['earlyIn'];
                    $data['lateIn']=$empPunchInCnt['lateIn'];
                    $data['earlyOut']=$empPunchInCnt['earlyOut'];
                    $data['lateOut']=$empPunchInCnt['lateOut'];
                    
                
                    $data1[] = $data;
                }while($empPunchInCnt = mysqli_fetch_assoc($empPunchInResult));                 
                    $data['status']=1;
                    $data['empPunchInCount']=mysqli_num_rows($empPunchInResult);
                    $data['empPunchInList']=$data1;
                        
    }else{
            $data['status']=0;
            $data['empPunchInList']=array();
        }
    return $data; 
}
    function getMembersInPlantCountAndList($user_id)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);

    	// print_r($userDetails);die();
    	$empNumber = $userDetails['empNumber'];
    	$roleId = $userDetails['id'];

        $data=array();  			
		$empquery="SELECT * FROM `hs_hr_employee` as e WHERE e.emp_number=$empNumber";
		$empcount=mysqli_query($this->conn, $empquery);
		$emprow=mysqli_fetch_assoc($empcount);
		
		$departmentId = $emprow['work_station'];

		// if($roleId ==6 ||  $roleId ==1){
		$queryOverall="SELECT COUNT(a.state) as memberCount FROM `erp_attendance_record` as a WHERE a.state='PUNCHED IN'";
		// }else{
		$queryDepart="SELECT COUNT(a.state) as departCount FROM `erp_attendance_record` as a LEFT JOIN hs_hr_employee as e ON a.employee_id =e.emp_number WHERE e.work_station=$departmentId AND a.state='PUNCHED IN'";
		// }

		$overalcount=mysqli_query($this->conn, $queryOverall);
		$overalrow=mysqli_fetch_assoc($overalcount);
		// echo $row['memberCount'];die();
		$deptcount=mysqli_query($this->conn, $queryDepart);
		$dprtrow=mysqli_fetch_assoc($deptcount);


		if($roleId == 6 ||  $roleId == 1){
		$query1="SELECT e.*,a.id,a.employee_id as employeeNum,a.punch_in_utc_time,a.punch_in_note,a.state FROM `erp_attendance_record` as a LEFT JOIN hs_hr_employee as e ON a.employee_id =e.emp_number WHERE a.state='PUNCHED IN'";
		}else{
		$query1="SELECT e.*,a.id,a.employee_id as employeeNum,a.punch_in_utc_time,a.punch_in_note,a.state FROM `erp_attendance_record` as a LEFT JOIN hs_hr_employee as e ON a.employee_id =e.emp_number WHERE e.work_station=$departmentId AND  a.state='PUNCHED IN'";
		}

		$contactsList=mysqli_query($this->conn, $query1);
		if(mysqli_num_rows($contactsList) > 0)
		{
				$row1=mysqli_fetch_assoc($contactsList);
			do{ 

						$data['record_id']=$row1['id'];
						$data['employee_number']=$row1['employeeNum'];
						$data['punch_in']=$row1['punch_in_utc_time'];
						$data['punch_in_note']=$row1['punch_in_note'];
						$data['state']=$row1['state'];
						$data['employee_name']=$this->getEmpnameByEmpNumber($row1['employeeNum']);
						$data['employee_code']=$row1['employee_id'];
						$data['departmentId']=$row1['work_station'];
						$data['departmentName']=$this->getDepartment($row1['work_station']);

					
						$data1[] = $data;
					}while($row1 = mysqli_fetch_assoc($contactsList)); 				
						$data['members_personslist']=$data1;
						$data['status']=1;
						$data['membersCount'] = $overalrow['memberCount'];
						$data['departCount'] = $dprtrow['departCount'];
							
		}else{
				$data['status']=0;
				$data['members_personslist']=array();
				$data['membersCount'] = 0;
				$data['departCount'] = 0;
			}
		return $data; 
    }

    function getViewManpowerRequisitionList($user_id)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);

    	$empNumber = $userDetails['empNumber'];
    	$roleId = $userDetails['id'];

    	$empquery="SELECT * FROM `hs_hr_employee` as e WHERE e.emp_number=$empNumber";
		$empcount=mysqli_query($this->conn, $empquery);
		$emprow=mysqli_fetch_assoc($empcount);
		$departmentId = $emprow['work_station'];

    	// echo $roleId;die();
    	$supervisior = $this->isSupervisor($empNumber);

    	// echo $supervisior;die();

    	if($supervisior != 0){

		$query1 = "SELECT r.*,jt.job_title as jobTitle,l.name as locationName,d.name as departmentName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.report_to=e.emp_number) as reportToName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.submitted_by=e.emp_number) as submittedBy FROM `erp_manpower_requisition` as r LEFT JOIN erp_job_title as jt ON r.job_title=jt.id LEFT JOIN erp_location as l on r.location=l.id LEFT JOIN erp_subunit as d ON r.department=d.id WHERE r.action_status IN(0,1,2,3,4) AND r.submitted_by=$empNumber ORDER BY r.id desc";
    	}else if( $roleId == 17){
		$query1 = "SELECT r.*,jt.job_title as jobTitle,l.name as locationName,d.name as departmentName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.report_to=e.emp_number) as reportToName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.submitted_by=e.emp_number) as submittedBy FROM `erp_manpower_requisition` as r LEFT JOIN erp_job_title as jt ON r.job_title=jt.id LEFT JOIN erp_location as l on r.location=l.id LEFT JOIN erp_subunit as d ON r.department=d.id WHERE r.action_status IN(1,2,4) AND r.department=$departmentId  ORDER BY r.id desc";
    	}else if($roleId == 6){
		$query1 = "SELECT r.*,jt.job_title as jobTitle,l.name as locationName,d.name as departmentName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.report_to=e.emp_number) as reportToName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.submitted_by=e.emp_number) as submittedBy FROM `erp_manpower_requisition` as r LEFT JOIN erp_job_title as jt ON r.job_title=jt.id LEFT JOIN erp_location as l on r.location=l.id LEFT JOIN erp_subunit as d ON r.department=d.id WHERE r.action_status IN(2,4)  ORDER BY r.id desc";

    	}else{
    		$query1 = "SELECT r.*,jt.job_title as jobTitle,l.name as locationName,d.name as departmentName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.report_to=e.emp_number) as reportToName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.submitted_by=e.emp_number) as submittedBy FROM `erp_manpower_requisition` as r LEFT JOIN erp_job_title as jt ON r.job_title=jt.id LEFT JOIN erp_location as l on r.location=l.id LEFT JOIN erp_subunit as d ON r.department=d.id WHERE r.action_status IN(0,1,3) AND r.submitted_by=$empNumber  ORDER BY r.id desc";
    	}

    	// echo $query1;die();

        $data=array();  			
		// $empquery="SELECT * FROM `hs_hr_employee` as e WHERE e.emp_number=$empNumber";
		// $empcount=mysqli_query($this->conn, $empquery);
		// $emprow=mysqli_fetch_assoc($empcount);
		// $departmentId = $emprow['work_station'];

		$contactsList = mysqli_query($this->conn, $query1);

		// echo $contactsList;die();
		if(mysqli_num_rows($contactsList) > 0)
		{
				$row1=mysqli_fetch_assoc($contactsList);
			do{ 

						$data['id']=$row1['id'];
						$data['jobTitle']=$row1['jobTitle'];
						$data['job_title_id']=$row1['job_title'];
						$data['locationName']=$row1['locationName'];
						$data['locname_id']=$row1['locationName'];
						$data['departmentName']=$row1['departmentName'];
						$data['department_id']=$row1['department'];
						$data['reportToName']=$row1['reportToName'];
						$data['report_to_id']=$row1['report_to'];
						$data['no_of_positions']=$row1['no_of_positions'];
						$data['job_description']=$row1['job_description'];
						$data['required_by']=$row1['required_by'];
						$data['qualifications'] = $this->getQualifictions($row1['qualifications']);
						$data['skills']=$row1['skill_experience'];
						$data['action_status_name']=$row1['action_status'];

						if($row1['action_status'] == 0){
						$data['action_status_name']='Save as Draft';
						}else if($row1['action_status'] == 1){
						$data['action_status_name']='Submitted';
						}else if($row1['action_status'] == 2){
						$data['action_status_name']='Approved';
						}else if($row1['action_status'] == 3){
						$data['action_status_name']='Rejected';
						}else if($row1['action_status'] == 4){
						$data['action_status_name']='Accepted';
						}else{
						$data['action_status_name']='No Data Found';
						}

						// $data['departmentName']=$this->getDepartment($row1['work_station']);

						// $data['employee_name']=$this->getEmpnameByEmpNumber($row1['employeeNum']);
					
						$data1[] = $data;
					}while($row1 = mysqli_fetch_assoc($contactsList)); 				
						$data['RequisitionList']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
				$data['RequisitionList']=array();
			}
		return $data; 
    }

    function getAppMenuItemsList($user_id)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);

    	$empNumber = $userDetails['empNumber'];
    	$roleId = $userDetails['id'];

    	// echo $roleId;die();
    	$supervisior = $this->isSupervisor($empNumber);

    	// echo $supervisior;die();

    	if($supervisior != 0 ){

		$query1 = "SELECT * FROM `erp_app_menu` as m WHERE m.role_id IN (2,3)  AND m.status=1 AND m.is_deleted=0   GROUP BY m.menu_item";

    	}else if( $roleId == 17){

		$query1 = "SELECT * FROM `erp_app_menu` as m WHERE m.role_id IN (2,".$roleId.")  AND m.status=1 AND m.is_deleted=0  GROUP BY m.menu_item";

    	}else if($roleId == 6){

		$query1 = "SELECT * FROM `erp_app_menu` as m WHERE m.role_id IN (2,".$roleId.")  AND m.status=1 AND m.is_deleted=0   GROUP BY m.menu_item";

    	}else{

    		$query1 = "SELECT * FROM `erp_app_menu` as m WHERE m.role_id IN (2)  AND m.status=1 AND m.is_deleted=0   GROUP BY m.menu_item";
    	}


        $data=array();  			
		

		$contactsList = mysqli_query($this->conn, $query1);

		// echo $contactsList;die();
		if(mysqli_num_rows($contactsList) > 0)
		{
				$row1=mysqli_fetch_assoc($contactsList);
			do{ 

						$data['id']=$row1['id'];
						$data['role_id']=$row1['role_id'];
						$data['menu_item']=$row1['menu_item'];
						$data['level']=$row1['level'];
						$data['menu_image']=$row1['menu_image'];
						$data['status']=$row1['status'];
						$data['menu_image_path']='http://portal.prospectatech.com/entreplan3.1/symfony/upload/app_menu_images/';
						$data1[] = $data;
					}while($row1 = mysqli_fetch_assoc($contactsList)); 				
						$data['status']=1;
						$data['menu_list']=$data1;
							
		}else{
				$data['status']=0;
				$data['menu_list']=array();
			}
		return $data; 
    }


    function getInductionQuestionsList($user_id)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);

    	$empNumber = $userDetails['empNumber'];
    	$roleId = $userDetails['id'];

		$query = "SELECT * FROM `erp_induction_question` WHERE is_deleted=0";

        $data=array();  			

		$questionsList = mysqli_query($this->conn, $query);

		// echo $questionsList;die();
		if(mysqli_num_rows($questionsList) > 0)
		{
				$row1=mysqli_fetch_assoc($questionsList);
			do{ 

						$data['id']=$row1['id'];
						$data['question']=$row1['name'];
						$data['terms_conditions']=$row1['terms_conditions'];
						$data['ind_status']=$this->getEmpInductionStatus($row1['id'],$empNumber);
						// $data['is_checked']=$row1['is_checked'];
						// $data['is_condition']=$row1['is_condition'];
						$data1[] = $data;
					}while($row1 = mysqli_fetch_assoc($questionsList)); 				
						$data['status']=1;
						$data['induction_list']=$data1;
							
		}else{
				$data['status']=0;
				$data['induction_list']=array();
			}
		return $data; 
    }

    function inductionEmpStatusUpdate($user_id,$induction_qns_id,$induction_emp_status)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);

    	$empNumber = $userDetails['empNumber'];
    	$roleId = $userDetails['id'];

		$query = "SELECT * FROM `erp_induction_emp_status` WHERE  induction_emp_number=$empNumber AND induction_question_id=$induction_qns_id";

        $data=array();  			

		$questionsList = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($questionsList) > 0)
		{
		// echo mysqli_num_rows($questionsList);die();
			$updatequery = "UPDATE `erp_induction_emp_status` SET induction_emp_status=$induction_emp_status WHERE  induction_emp_number=$empNumber AND induction_question_id=$induction_qns_id";
			$updateResultList = mysqli_query($this->conn, $updatequery);
			if($updateResultList){
				$data['status']=1;
			}else{
				$data['status']=0;

			}		
							
		}else{
			$insertquery = "INSERT INTO `erp_induction_emp_status`(induction_question_id,induction_emp_number,induction_emp_status) VALUES($induction_qns_id,$empNumber,$induction_emp_status)";	

			$insertResultList = mysqli_query($this->conn, $insertquery);
			if($insertResultList){
				$data['status']=1;
			}else{
				$data['status']=0;

			}
		}
		return $data; 
    }

     function getEmpInductionStatus($indId,$empId){

    	$query = "SELECT * FROM `erp_induction_emp_status` WHERE  induction_question_id=$indId AND induction_emp_number=$empId";

        $status =0;  			

		$questionsList = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($questionsList) > 0)
		{
		// echo mysqli_num_rows($questionsList);die();
			$status =1;
		}else{
			$status=0;
		}
		return $status; 
    }


    function getEmployeePayrolList($user_id)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);

    	$empNumber = $userDetails['empNumber'];
    	$roleId = $userDetails['id'];
    	
    	$query1 = "SELECT * FROM `erp_payroll` WHERE emp_number=$empNumber ORDER BY id desc";


        $data=array();  			
		

		$payslipsList = mysqli_query($this->conn, $query1);

		// echo $payslipsList;die();
		if(mysqli_num_rows($payslipsList) > 0)
		{
				$row1=mysqli_fetch_assoc($payslipsList);
			do{ 

						$data['id']=$row1['id'];
						$data['emp_number']=$row1['emp_number'];
						$data['pay_month']=$row1['pay_month'];
						if($row1['pay_month']==1){
						$data['pay_month_name']='January';
						}else if($row1['pay_month']==2){
						$data['pay_month_name']='February';
						}else if($row1['pay_month']==3){
						$data['pay_month_name']='March';
						}else if($row1['pay_month']==4){
						$data['pay_month_name']='April';
						}else if($row1['pay_month']==5){
						$data['pay_month_name']='May';
						}else if($row1['pay_month']==6){
						$data['pay_month_name']='June';
						}else if($row1['pay_month']==7){
						$data['pay_month_name']='July';
						}else if($row1['pay_month']==8){
						$data['pay_month_name']='August';
						}else if($row1['pay_month']==9){
						$data['pay_month_name']='September';
						}else if($row1['pay_month']==10){
						$data['pay_month_name']='October';
						}else if($row1['pay_month']==11){
						$data['pay_month_name']='November';
						}else if($row1['pay_month']==12){
						$data['pay_month_name']='December';
						}else{
						$data['pay_month_name']='';
						}
						$data['pay_year']=$row1['pay_year'];
						$data['pay_days']=$row1['pay_days'];
						$data['salary5']=$row1['salary5'];
						$data['salary8']=$row1['salary8'];
						$data['is_scheduled']=$row1['is_scheduled'];
						$data['payslip_path']='http://portal.prospectatech.com/entreplan3.1/symfony/web/index.php/pim/paySlipPDF/empNumber/'.$row1['emp_number'].'/id/'.$row1['id'];

						$data['payslip_path_iframe']='<iframe frameborder="0" scrolling="no" style="border:0px" src="http://portal.prospectatech.com/entreplan3.1/symfony/web/index.php/pim/paySlipPDF/empNumber/'.$row1['emp_number'].'/id/'.$row1['id'].'" width="100%" height="100%">';
						$data1[] = $data;
					}while($row1 = mysqli_fetch_assoc($payslipsList)); 				
						$data['status']=1;
						$data['payrol_list']=$data1;
							
		}else{
				$data['status']=0;
				$data['payrol_list']=array();
			}
		return $data; 
    }

    function getSaveManpowerRequisition($user_id,$req_id)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);

    	$empNumber = $userDetails['empNumber'];
    	$roleId = $userDetails['id'];




    	$query1 = "SELECT r.*,jt.job_title as jobTitle,l.name as locationName,d.name as departmentName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.report_to=e.emp_number) as reportToName,(SELECT e.emp_firstname from hs_hr_employee as e WHERE r.submitted_by=e.emp_number) as submittedBy FROM `erp_manpower_requisition` as r LEFT JOIN erp_job_title as jt ON r.job_title=jt.id LEFT JOIN erp_location as l on r.location=l.id LEFT JOIN erp_subunit as d ON r.department=d.id WHERE r.id=$req_id";

    	// echo $query1;die();

        $data=array();  			
    	$actiondata = array();
		// $empquery="SELECT * FROM `hs_hr_employee` as e WHERE e.emp_number=$empNumber";
		// $empcount=mysqli_query($this->conn, $empquery);
		// $emprow=mysqli_fetch_assoc($empcount);
		// $departmentId = $emprow['work_station'];

		$contactsList = mysqli_query($this->conn, $query1);

		// echo $contactsList;die();
		if(mysqli_num_rows($contactsList) > 0)
		{
				$row1=mysqli_fetch_assoc($contactsList);
			do{ 

						$data['id']=$row1['id'];
						$data['jobTitle']=$row1['jobTitle'];
						$data['job_title_id']=$row1['job_title'];
						$data['locationName']=$row1['locationName'];
						$data['locname_id']=$row1['location'];
						$data['departmentName']=$row1['departmentName'];
						$data['department_id']=$row1['department'];
						$data['reportToName']=$row1['reportToName'];
						$data['report_to_id']=$row1['report_to'];
						$data['no_of_positions']=$row1['no_of_positions'];
						$data['job_description']=$row1['job_description'];
						$data['required_by']=$row1['required_by'];
						$data['qualifications'] = $this->getQualifictions($row1['qualifications']);
						$data['skill_experience']=$row1['skill_experience'];
						$data['min_eperience']=$row1['min_eperience'];
						$data['min_salary']=$row1['min_salary'];
						$data['max_salary']=$row1['max_salary'];
						$data['requested_budgeted']=$row1['requested_budgeted'];
						$data['req_submitted_by']=$row1['submitted_by'];

						if($row1['requested_budgeted'] == 1){
							$data['requested_budgeted_name'] ='Yes';
						}else{
							$data['requested_budgeted_name'] ='No';
						}
						$data['reason_for_requirement']=$row1['reason_for_requirement'];

						if($row1['reason_for_requirement'] == 1){
							$data['reason_for_requirement_name']= 'New Opening';
						}else if($row1['reason_for_requirement'] == 2){
							$data['reason_for_requirement_name']= 'Replacement';
						}else if($row1['reason_for_requirement'] == 3){
							$data['reason_for_requirement_name']= 'Planned Addition';
						}else{
							$data['reason_for_requirement_name']= '';
						}
						$data['replace_for']=$this->getReplaceFor($row1['replace_for']);
						// $data['replace_for']=$this->getEmpnameByEmpNumber($row1['replace_for']);
						$data['comments']=$row1['comments'];
						$data['status']=$row1['status'];
						if($data['status'] == 1){
							$data['status_name'] = 'Active';
						}else if($data['status'] == 2){
							$data['status_name'] = 'Inactive';
						}else{
							$data['status_name'] = '';
						}
						$data['requisition_type']=$row1['requisition_type'];

						if($data['requisition_type'] == 1){
							$data['requisition_type_name'] = 'Trainee';
						}else if($data['requisition_type'] == 2){
							$data['requisition_type_name'] = 'Employee';
						}else{
							$data['requisition_type_name'] = '';
						}


						$data['action_status_id']=$row1['action_status'];

						if($row1['action_status'] == 0){
						$data['action_status_name']='Save as Draft';
						}else if($row1['action_status'] == 1){
						$data['action_status_name']='Submitted';
						}else if($row1['action_status'] == 2){
						$data['action_status_name']='Approved';
						}else if($row1['action_status'] == 3){
						$data['action_status_name']='Rejected';
						}else if($row1['action_status'] == 4){
						$data['action_status_name']='Accepted';
						}else{
						$data['action_status_name']='No Data Found';
						}


				if($row1['submitted_by'] == $userDetails['empNumber'] && $row1['action_status'] == 1){
					$actiondata = array(array('id'=>'0','name'=>'Cancel'));
				}else if($roleId == 17 && $row1['action_status'] == 1){
					$actiondata = array(array('id'=>'3','name'=>'Reject'),array('id'=>'2','name'=>'Approve'));
					// $actiondata = array('id'=>'2','name'=>'Approve');
				}else if($roleId == 6 && $row1['action_status'] == 2){
					$actiondata = array(array('id'=>'3','name'=>'Reject'),array('id'=>'4','name'=>'Accept'));
					
				}else{
					$actiondata = array(array('id'=>'0','name'=>'Cancel'));
				}

					
				}while($row1 = mysqli_fetch_assoc($contactsList)); 				
						$data['RequisitionList']=$data;
						$data['Action_Perform']=$actiondata;
						$data['status']=1;
							
		}else{
				$data['status']=0;
				$data['RequisitionList']=array();
			}
		return $data; 
    }

    function getQualifictions($qual){
    	if(!empty($qual)){

	    	$query ='SELECT e.* FROM `erp_education` as e WHERE e.id IN('.$qual.')';

	    	// echo $query;die();
	    	$qualifiationsList=mysqli_query($this->conn, $query);

	    	// print_r($qualifiationsList);die();
	    	if(mysqli_num_rows($qualifiationsList) > 0)
			{
				$qualifications = [];
				$row1=mysqli_fetch_assoc($qualifiationsList);
				do{ 
					array_push($qualifications,$row1['name']);
					// $data['name']=$row1['name'];
				}while($row1 = mysqli_fetch_assoc($qualifiationsList));

				// return 'implode';
				return implode(',',$qualifications);

			}else{
				return 'No Data Found';
			}
    	}else{

				return 'No Data Found';
    	}
    }

    function getRequisitionActionPerform($user_id,$req_id,$action_perform,$comment){

    	$userDetails = $this->getUserRoleByUserId($user_id);

    	$empNumber = $userDetails['empNumber'];
    	$roleId = $userDetails['id'];

    	$submittedOn = date('Y-m-d');

    	if(!empty($req_id) && !empty($action_perform)){

    		$updatesql = "UPDATE erp_manpower_requisition SET `action_status` = $action_perform WHERE id= $req_id";
	    	if($result2 = mysqli_query($this->conn, $updatesql)){

	    			$query1 = "INSERT INTO erp_manpower_requisition_log (req_id, submitted_by, submitted_on, action_status, comment) VALUES (?,?,?,?,?)";

					 if($stmt = mysqli_prepare($this->conn, $query1)){
						 mysqli_stmt_bind_param($stmt, "iisis",$req_id,$empNumber,$submittedOn,$action_perform,$comment);
						 mysqli_stmt_execute($stmt);

						 $tsId = $this->conn->insert_id;

						 $data['status']=1;
						 $data['message']='Successfully log inserted';
					 }else{
					 	$data['status']=0;
						 $data['message']='log failed';
					 }
	    	}else{
	    		$data['status']=0;
				$data['message']='update failed';
	    	}

    	}else{
    			$data['status']=0;
				$data['message']='action perform failed';
    	}
    	return $data;
    }

    function getReplaceFor($empIds){
    	if(!empty($empIds)){
    		$empNames = [];

	    	$empIdsArr = explode(',', $empIds);

	    	for($n=0;$n<sizeof($empIdsArr);$n++){
	    	 	$empName = $this->getEmpnameByEmpNumber($empIdsArr[$n]);
	    	 	array_push($empNames,$empName);
	    	}
	    	return implode(',',$empNames);
	    	
    	}else{

				return 'No Data Found';
    	}
    }


	function MyLeaveEntitlements($user_id,$leaveType)
    {

		$userDetails = $this->getUserRoleByUserId($user_id);
    	$empNumber = $userDetails['empNumber'];
        $data=array();  			
		$query="SELECT COUNT(l.id) as PendingApproval, (SELECT COUNT(*) FROM erp_leave l WHERE emp_number = $empNumber AND leave_type_id = $leaveType AND status = 2) as Scheduled, (SELECT COUNT(*) FROM erp_leave l WHERE emp_number = $empNumber AND leave_type_id = $leaveType AND status = 3) as Taken, (SELECT ROUND(ent.no_of_days) as Entitled FROM erp_leave_entitlement ent WHERE ent.emp_number = $empNumber AND ent.leave_type_id = $leaveType) as entitled  FROM erp_leave l WHERE emp_number = $empNumber AND leave_type_id = $leaveType AND status = 1";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
			do{ 

						$data['entitled']=$row['entitled'];
						$data['Taken']=$row['Taken'];
						$data['Scheduled']=$row['Scheduled'];
						$data['PendingApproval']=$row['PendingApproval'];
						$data['Balance']=$row['entitled'] - ($row['Taken'] + $row['Scheduled'] + $row['PendingApproval']);
						
							
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['MyLeavesEntitles']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data; 
    }

	function employeeByDept($departmentId)
	{

		$data= array();
		$query="SELECT * FROM `hs_hr_employee` WHERE department = $departmentId";
		$count=mysqli_query($this->conn, $query);		
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
				
					do{ 

						$data['emp_number']=$row['emp_number'];
						$data['employee_id']=$row['employee_id'];
						$data['emp_lastname']=$row['emp_lastname'];
						$data['emp_firstname']=$row['emp_firstname'];
						$data['emp_middle_name']=$row['emp_middle_name'];
						$data['emp_nick_name']=$row['emp_nick_name'];
						$data['emp_fathername']=$row['emp_fathername'];
						$data['emp_mothername']=$row['emp_mothername'];
						$data['emp_smoker']=$row['emp_smoker'];
						$data['ethnic_race_code']=$row['ethnic_race_code'];
						$data['emp_birthday']=$row['emp_birthday'];
						$data['nation_code']=$row['nation_code'];
						$data['emp_gender']=$row['emp_gender'];
						$data['emp_lastname']=$row['emp_lastname'];
						$data['emp_firstname']=$row['emp_firstname'];
						$data['emp_middle_name']=$row['emp_middle_name'];
						$data['emp_nick_name']=$row['emp_nick_name'];
						$data['emp_fathername']=$row['emp_fathername'];
						$data['emp_mothername']=$row['emp_mothername'];
						$data['emp_smoker']=$row['emp_smoker'];
						$data['ethnic_race_code']=$row['ethnic_race_code'];
						$data['emp_birthday']=$row['emp_birthday'];
						$data['nation_code']=$row['nation_code'];
						$data['emp_gender']=$row['emp_gender'];
							
						$data['emp_marital_status']=$row['emp_marital_status'];
						$data['emp_ssn_num']=$row['emp_ssn_num'];
						$data['emp_sin_num']=$row['emp_sin_num'];
						$data['emp_other_id']=$row['emp_other_id'];
						$data['emp_pancard_id']=$row['emp_pancard_id'];
						$data['emp_uan_num']=$row['emp_uan_num'];
						$data['emp_pf_num']=$row['emp_pf_num'];
						$data['emp_dri_lice_num']=$row['emp_dri_lice_num'];
						$data['emp_dri_lice_exp_date']=$row['emp_dri_lice_exp_date'];
						$data['emp_military_service']=$row['emp_military_service'];
						$data['blood_group']=$row['blood_group'];
							
						$data['emp_hobbies']=$row['emp_hobbies'];
						$data['emp_status']=$row['emp_status'];
						$data['job_title_code']=$row['job_title_code'];
						$data['eeo_cat_code']=$row['eeo_cat_code'];
						$data['work_station']=$row['work_station'];
						$data['department']=$row['department'];
						$data['user_level']=$row['user_level'];
						$data['functionality']=$row['functionality'];
						$data['referred_by']=$row['referred_by'];
						$data['billable']=$row['billable'];
						$data['emp_street1']=$row['emp_street1'];
							
						$data['emp_street2']=$row['emp_street2'];
						$data['city_code']=$row['city_code'];
						$data['coun_code']=$row['coun_code'];
						$data['provin_code']=$row['provin_code'];
						$data['emp_zipcode']=$row['emp_zipcode'];
						$data['emp_tstreet1']=$row['emp_tstreet1'];
						$data['emp_tstreet2']=$row['emp_tstreet2'];
						$data['tcity_code']=$row['tcity_code'];
						$data['tcoun_code']=$row['tcoun_code'];
						$data['tprovin_code']=$row['tprovin_code'];
						$data['emp_tzipcode']=$row['emp_tzipcode'];
							
						$data['emp_hm_telephone']=$row['emp_hm_telephone'];
						$data['emp_mobile']=$row['emp_mobile'];
						$data['emp_work_telephone']=$row['emp_work_telephone'];
						$data['emp_work_email']=$row['emp_work_email'];
						$data['sal_grd_code']=$row['sal_grd_code'];
						$data['joined_date']=$row['joined_date'];
						$data['emp_oth_email']=$row['emp_oth_email'];
						$data['termination_id']=$row['termination_id'];
						$data['emp_ctc']=$row['emp_ctc'];
						$data['emp_cost_of_company']=$row['emp_cost_of_company'];
						$data['emp_gross_salary']=$row['emp_gross_salary'];
						
						$data['custom1']=$row['custom1'];
						$data['custom2']=$row['custom2'];
						$data['custom3']=$row['custom3'];
						$data['custom4']=$row['custom4'];
						$data['custom5']=$row['custom5'];
						$data['custom6']=$row['custom6'];
						$data['custom7']=$row['custom7'];
						$data['custom8']=$row['custom8'];
						$data['custom9']=$row['custom9'];
						$data['custom10']=$row['custom10'];
						$data['plant_id']=$row['plant_id'];
						$data['vehicle_number_id']=$row['vehicle_number_id'];
						$data['is_executive']=$row['is_executive'];
						$data['is_login_image']=$row['is_login_image'];
						$data['flag']=$row['flag'];
						$data['esi_number']=$row['esi_number'];

						$data['reason_for_leaving']=$row['reason_for_leaving'];
						$data['emp_count_allowed']=$row['emp_count_allowed'];
						$data['attendance_allowed']=$row['attendance_allowed'];
						$data['authorized_logout']=$row['authorized_logout'];
						$data['offer_letter']=$row['offer_letter'];
					
						
						$data1[] = $data;
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['employeeByDetails']=$data1;
						$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data;    
	} 

	 function subordinateOTlist($user_id, $status)
    {
    	$userDetails = $this->getUserRoleByUserId($user_id);
    	$supervisor = $userDetails['empNumber'];
	

		$subObj = $this->subordinateByEmpList($supervisor);   	

			for ($i=0; $i < sizeof($subObj['emplist']) ; $i++) { 
			    $empList[] = $subObj['emplist'][$i];
			        	//to convert Array into string the following implode method is used
			    $empLists = implode(',', $empList);
			}

        $data=array();  			
		$query="SELECT oa.id as Id,CONCAT(emp.emp_firstname,' ',emp.emp_lastname) as employee_name,oa.date as Date,oa.start_time as startTime,oa.end_time as endTime,oa.hours as hours,CONCAT(e.emp_firstname,' ',e.emp_lastname) as approvedby,ol.performed_on as approvedOn,ol.claim_type as claimType, ol.status as status FROM erp_ot_apply oa LEFT JOIN erp_ot_log ol ON ol.ot_id = oa.id LEFT JOIN hs_hr_employee emp ON emp.emp_number = oa.emp_number LEFT JOIN hs_hr_employee e ON e.emp_number = ol.performed_by WHERE oa.emp_number IN ($empLists) AND ol.status = $status";
		$count=mysqli_query($this->conn, $query);

		if(mysqli_num_rows($count) > 0)
		{
				$row=mysqli_fetch_assoc($count);
			do{ 
				

				$data['Id']=$row['Id'];
				$data['employee_name']=$row['employee_name'];
				$data['Date']=$row['Date'];
				$data['startTime']=$row['startTime'];
				$data['endTime']=$row['endTime'];
				$data['hours']=$row['hours'];
				$data['approvedby']=$row['approvedby'];
				$data['approvedOn']=$row['approvedOn'];
				$data['claimType']=$row['claimType'];
				// $data['status']=$row['status'];
				if($row['claimType'] == 1){
					$data['claimType']= 'leave' ;
				}elseif($row['claimType'] == 2){
					$data['claimType'] = 'cash' ;
				}


				if($row['status'] == 2){
					$data['status'] = 'Claimed' ;
				}elseif($row['status'] == 3){
					$data['status'] = 'Approved' ;
				}
				
					
				$data1[] = $data;
			}while($row = mysqli_fetch_assoc($count)); 				
				$data['subOTlists']=$data1;
				$data['status']=1;
							
		}else{
				$data['status']=0;
			}
		return $data; 
    }

    function equipmentTracking($user_id,$status_id,$equipment_id,$track_id,$comments,$isWorking,$isSited){

	$userDetails = $this->getUserRoleByUserId($user_id);
    $submittedby = $userDetails['empNumber'];
	$created_by_name = $this->getEmpnameByEmpNumber($submittedby);
	$submittedOn = date('Y-m-d H:i:s');

  $data=array();

	if($status_id == 4)
	{

	    $updatesql = "UPDATE erp_assign_track_emp SET `is_sited` = $isSited, `is_working` = $isWorking, `status` = $status_id WHERE id = $track_id";
	    if($result2 = mysqli_query($this->conn, $updatesql)){

	    	$query="INSERT INTO erp_assign_track_emp_log (track_emp_id,submitted_by,submitted_on,status,comments) VALUES (?,?,?,?,?)";
			
    		if($stmt = mysqli_prepare($this->conn, $query)){
	     		mysqli_stmt_bind_param($stmt, "iisis" ,$track_id,$submittedby,$submittedOn,$status_id,$comments);
	    			   
					if(mysqli_stmt_execute($stmt)){
						$data['status']=1;
					} 
			}else{
				$data['status']=0;
			}

	      $data['log'] = "Success";
	      $data['status']=1;

	    }
	    else{
	    //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
	    $data['status']=0;
	    }

	    return $data;

	}

  	
}

function assignedEquipmentList($quipment_id)
    {
        $data=array();

        // $query="SELECT * FROM erp_assign_track_emp as te INNER JOIN erp_equipment as e WHERE te.assigned_to='".$user_id."' AND te.equipment_id='".$quipment_id."' AND te.equipment_id=e.id";
        $query="SELECT e.*,te.id as TID,te.group_id as TgroupId,te.equipment_id as TEquipmentId,te.is_sited as TisSighted,te.is_working as Tworking,g.id as GID, g.title as groupTitle,g.assigned_to as groupAssignTo,(SELECT ct.name FROM erp_category_type as ct WHERE ct.id=e.category_type_id) as category_type_name,
        (SELECT cst.name FROM erp_customer_sub_type as cst WHERE cst.id=e.category_sub_type_id) as category_sub_type_name,
        (SELECT et.name FROM erp_equipment_type as et WHERE et.id=e.equipment_type_id) as equipment_type_name,
        (SELECT est.name FROM erp_equipment_sub_type as est WHERE est.id=e.equipment_sub_type_id) as equipment_sub_type_name,
        (SELECT l.name FROM erp_location as l WHERE l.id=e.location_id) as location_name,
        (SELECT fl.name FROM erp_functional_location as fl WHERE fl.id=e.functional_location_id) as functional_location_name,
        (SELECT p.plant_name FROM erp_plant as p WHERE p.id=e.plant_id) as plant_name,
        (SELECT d.name FROM erp_subunit as d WHERE d.id=e.department_id) as department_name,e.asset_number,e.cost_center_id,e.acquistn_value,e.acquistion_date,e.manufacturer,e.model_number,e.manufacturer_country,e.manufacturer_part_number,e.manufacturer_serial_number,e.reference_equipment_id,e.level,e.is_assembly,e.reportable,e.current_value,e.scarp_value,e.useful_life,e.depreciation_method,e.depreciation_value,e.decommissioned_date,e.commissioned_date,e.is_Deleted FROM `erp_equipment`as e INNER JOIN erp_assign_track_emp as te INNER JOIN erp_group_title as g WHERE (e.id='".$quipment_id."' AND te.group_id=g.id AND te.equipment_id=e.id) OR (e.id='".$quipment_id."' AND te.group_id=g.id) OR (e.id='".$quipment_id."' AND te.equipment_id=e.id) OR e.id='".$quipment_id."' order by e.id desc limit 1";
    $count=mysqli_query($this->conn, $query);

    if(mysqli_num_rows($count) > 0)
    {
            $row=mysqli_fetch_assoc($count);
            
                do {                         
                
                    $data['id'] = $row['id'];
                    $data['TgroupId'] = $row['TgroupId'];
                    $data['isSighted'] = $row['TisSighted'];
                    $data['equipment_id'] = $row['TEquipmentId'];
                    $data['equipment_name'] = $row['name'];
                    $data['category_type_name'] = $row['category_type_name'];
                    $data['title'] = $row['groupTitle'];
                    $data['functional_location_name'] = $row['functional_location_name'];
                    // $data['equipment_id'] = $row['equipment_id'];
                    $data['assigned_to'] = $row['groupAssignTo'];
                    // $data['start_date'] = $row['start_date'];
                    // $data['start_time'] = $row['start_time'];
                    // $data['end_date'] = $row['end_date'];
                    // $data['end_time'] = $row['end_time'];
                    // $data['name'] = $row['name'];
                    $data['location_name'] = $row['location_name'];
                    $data['plant_name'] = $row['plant_name'];
                    $data['department_name'] = $row['department_name'];
                    $data['asset_number'] = $row['asset_number'];
                    $data['cost_center_id'] = $row['cost_center_id'];
                    $data['acquistn_value'] = $row['acquistn_value'];
                    $data['acquistion_date'] = $row['acquistion_date'];
                    $data['manufacturer'] = $row['manufacturer'];
                    $data['model_number'] = $row['model_number'];
                    $data['manufacturer_country'] = $row['manufacturer_country'];
                    $data['manufacturer_part_number'] = $row['manufacturer_part_number'];
                    $data['manufacturer_serial_number'] = $row['manufacturer_serial_number'];
                    $data['reference_equipment_id'] = $row['reference_equipment_id'];
                    $data['level'] = $row['level'];
                    $data['is_assembly'] = $row['is_assembly'];
                    $data['reportable'] = $row['reportable'];
                    // $data['current_value'] = $row['current_value'];
                    // $data['scarp_value'] = $row['scarp_value'];
                    // $data['useful_life'] = $row['useful_life'];
                    // $data['depreciation_method'] = $row['depreciation_method'];
                    // $data['depreciation_value'] = $row['depreciation_value'];
                    // // $data['other'] = $row['other'];
                    // $data['decommissioned_date'] = $row['decommissioned_date'];
                    // $data['commissioned_date'] = $row['commissioned_date'];
                    // $data['is_Deleted'] = $row['is_Deleted'];

                    // $data1[] = $data;
                }while($row = mysqli_fetch_assoc($count));
                    $data['assignedEquipmentList']=$data;
                    $data['status'] = 1;
                
    }else{
        $data['status'] = 0;
    }
    return $data;
}

function assignedToEmployeeEquipmentList($user_id)
	    {
	    	$userDetails = $this->getUserRoleByUserId($user_id);
    		$empNumber = $userDetails['empNumber'];
	        $data=array();
	  			
				$query="SELECT te.*,g.id as gid,g.title,g.assigned_to,e.name,
				(SELECT ct.name FROM erp_category_type as ct WHERE ct.id=e.category_type_id) as category_type_name,
				(SELECT cst.name FROM erp_customer_sub_type as cst WHERE cst.id=e.category_sub_type_id) as category_sub_type_name,
				(SELECT et.name FROM erp_equipment_type as et WHERE et.id=e.equipment_type_id) as equipment_type_name,
				(SELECT est.name FROM erp_equipment_sub_type as est WHERE est.id=e.equipment_sub_type_id) as equipment_sub_type_name,
				(SELECT l.name FROM erp_location as l WHERE l.id=e.location_id) as location_name,
				(SELECT fl.name FROM erp_functional_location as fl WHERE fl.id=e.functional_location_id) as functional_location_name,
				(SELECT p.plant_name FROM erp_plant as p WHERE p.id=e.plant_id) as plant_name,
				(SELECT d.name FROM erp_subunit as d WHERE d.id=e.department_id) as department_name,
				e.asset_number,e.cost_center_id,e.acquistn_value,e.acquistion_date,e.manufacturer,e.model_number,e.manufacturer_country,e.manufacturer_part_number,e.manufacturer_serial_number,e.reference_equipment_id,e.level,e.is_assembly,e.reportable,e.current_value,e.scarp_value,e.useful_life,e.depreciation_method,e.depreciation_value,e.decommissioned_date,e.commissioned_date,e.is_Deleted FROM `erp_group_title` as g LEFT JOIN erp_assign_track_emp as te ON g.assigned_to='".$empNumber."' AND te.status=1 AND g.id =te.group_id INNER JOIN erp_equipment as e WHERE te.equipment_id=e.id";
			$count=mysqli_query($this->conn, $query);

			if(mysqli_num_rows($count) > 0)
			{
					$row=mysqli_fetch_assoc($count);
					
						do { 						
						
							// $data['id'] = $row['id'];
							$data['equipment_id'] = $row['equipment_id'];
							$data['title'] = $row['title'];
							$data['assigned_to'] = $row['assigned_to'];
							// $data['start_date'] = $row['start_date'];
							// $data['start_time'] = $row['start_time'];
							// $data['end_date'] = $row['end_date'];
							// $data['end_time'] = $row['end_time'];
							$data['name'] = $row['name'];
							$data['status'] = $row['status'];
							$data['category_type_name'] = $row['category_type_name'];
							$data['location_name'] = $row['location_name'];
							$data['functional_location_name'] = $row['functional_location_name'];
							$data['plant_name'] = $row['plant_name'];
							$data['department_name'] = $row['department_name'];
							$data['asset_number'] = $row['asset_number'];
							$data['cost_center_id'] = $row['cost_center_id'];
							$data['acquistn_value'] = $row['acquistn_value'];
							$data['acquistion_date'] = $row['acquistion_date'];
							$data['manufacturer'] = $row['manufacturer'];
							$data['model_number'] = $row['model_number'];
							$data['manufacturer_country'] = $row['manufacturer_country'];
							$data['manufacturer_part_number'] = $row['manufacturer_part_number'];
							$data['manufacturer_serial_number'] = $row['manufacturer_serial_number'];
							$data['reference_equipment_id'] = $row['reference_equipment_id'];
							$data['level'] = $row['level'];
							$data['is_assembly'] = $row['is_assembly'];
							$data['reportable'] = $row['reportable'];
							$data['current_value'] = $row['current_value'];
							$data['scarp_value'] = $row['scarp_value'];
							$data['useful_life'] = $row['useful_life'];
							$data['depreciation_method'] = $row['depreciation_method'];
							$data['depreciation_value'] = $row['depreciation_value'];
							// $data['other'] = $row['other'];
							$data['decommissioned_date'] = $row['decommissioned_date'];
							$data['commissioned_date'] = $row['commissioned_date'];
							$data['is_Deleted'] = $row['is_Deleted'];

							$data1[] = $data;
						}while($row = mysqli_fetch_assoc($count));
							$data['assignedToEmployeeEquipmentList']=$data1;
							$data['status'] = 1;
						
			}else{
				$data['status'] = 0;
			}
			return $data;
	    }






		function getLocByEmpNumber($emp_number)
		{

			$data=array();

			
			$query = "SELECT loc.location_id as locationId FROM hs_hr_emp_locations loc LEFT JOIN erp_user usr ON loc.emp_number = usr.emp_number WHERE usr.emp_number = $emp_number";
			$result=mysqli_query($this->conn, $query);
			
			if(mysqli_num_rows($result)>0)
			{
				/*$row=mysqli_fetch_assoc($count);
				echo "if";
				exit();*/
			   $row = mysqli_fetch_array($result);
			   $locId=$row['locationId'];
			   /*echo $userId;
			   exit();*/
		    }
		    /*else
		    {
		    		$userId = "";

		    }*/
		   return $locId;
		}



		// echo($userId.$qrId);die();
		function getQrBusDetails($userId,$qrId)
	{
		$data= array();
		$userDetails = $this->getUserRoleByUserId($userId);
    		$empNumber = $userDetails['empNumber'];

		$query="SELECT v.*,CONCAT(e.emp_firstname,' ',e.emp_lastname) as empName,e.emp_mobile FROM `erp_vehicle` as v LEFT JOIN hs_hr_employee as e ON e.emp_number=v.driver_id WHERE v.id=$qrId";
		
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

					$data['vehicle_id']= $row['id'];
					$data['vehicle_name']= $row['name'];
					$data['vehicle_number']= $row['vehicle_number'];
					$data['driver_id']= $row['driver_id'];
					$data['driver_name']= $row['empName'];
					$data['driver_contact']= $row['emp_mobile'];

					$query1="SELECT m.status FROM `erp_emp_vehicle_management` as m WHERE m.created_at='".date('Y-m-d')."' AND m.emp_id=$userId";
					$count1=mysqli_query($this->conn, $query1);
					if(mysqli_num_rows($count1) > 0)
					{
								$row1=mysqli_fetch_assoc($count1);
								do{ 
								$data['status']= $row1['status'];
								}while($row1 = mysqli_fetch_assoc($count1)); 	
					}else{
						$data['status']=0;
					}
					
					}while($row = mysqli_fetch_assoc($count)); 				
						$data['vehicleDetails']=$data;
						$data['status']=1;
							
		}else{
				$data['status']=0;
				$data['vehicleDetails']=$data;
		}
		return $data;    
	}

	function getVehicleDetailsById($vid)
	{

		// echo($userId.$vid);die();
		$data= array();

		$query="SELECT v.*,CONCAT(e.emp_firstname,' ',e.emp_lastname) as empName,e.emp_mobile FROM `erp_vehicle` as v LEFT JOIN hs_hr_employee as e ON e.emp_number=v.driver_id WHERE v.id=$vid";
		
		$count=mysqli_query($this->conn, $query);
		if(mysqli_num_rows($count) > 0)
		{
					$row=mysqli_fetch_assoc($count);
					do{ 

					$data['vehicle_id']= $row['id'];
					$data['vehicle_name']= $row['name'];
					$data['vehicle_number']= $row['vehicle_number'];
					$data['driver_id']= $row['driver_id'];
					$data['driver_name']= $row['empName'];
					$data['driver_contact']= $row['emp_mobile'];
					
					}while($row = mysqli_fetch_assoc($count)); 				
							
		}
		return $data;    
	}

	function getEmpCheckInVehicle($userId,$vehicleId,$status)
	{

		// echo($userId.$qrId);die();
		$data= array();
		$data1= array();
		date_default_timezone_set('Asia/Kolkata');
		$created_at = date('Y-m-d');
		$submittedOn = date('Y-m-d H:i:s');

       // Prepare an insert statement
		$sql = "INSERT INTO erp_emp_vehicle_management (emp_id,vehicle_id,created_at,check_in,status) VALUES (?,?,?,?,?)";
		// echo $sql;die();
		if ($stmt = mysqli_prepare($this->conn,$sql)) {

			mysqli_stmt_bind_param($stmt,"iissi",$userId,$vehicleId,$created_at,$submittedOn,$status);
			if($output = mysqli_execute($stmt)) {
				$mangId = $this->conn->insert_id;

				$query="SELECT * FROM `erp_emp_vehicle_management` WHERE id=$mangId";
				$count=mysqli_query($this->conn, $query);
				if(mysqli_num_rows($count) > 0)
				{
							$row=mysqli_fetch_assoc($count);
							do{ 

							$data1['emp_trip_id']= $row['id'];
							$data1['emp_id']= $row['emp_id'];
							$data1['vehicle_id']= $row['vehicle_id'];
							$data1['created_at']= $row['created_at'];
							$data1['status']= $row['status'];

							if($row['status'] == 1){
							$data1['status_name']= 'VEHICLE IN';
							}else if($row['status'] == 2){
							$data1['status_name']= 'VEHICLE OUT';
							}else if($row['status'] == 3){
							$data1['status_name']= 'REACHED HOME';
							}								
							
							}while($row = mysqli_fetch_assoc($count)); 	
							$vhlmgtLog = $this->empVehicleManagementLogAdd($mangId,$userId,$submittedOn,$status);
							if($vhlmgtLog['status'] == 1){
							$data['empTripDetails']=$data1;
							$data['message']= "successfully";
							$data['status']= 1;

							}else{
							$data['empTripDetails']=$data1;
							$data['message']= "failed";
							$data['status']= 0;
							}			
									
				}else{
						$data['message']= "failed";
						$data['status']=0;
						$data['empTripDetails']=$data1;
				}
				
			}else{
				$data['message']= "failed";
				$data['status']= 0;
				$data['empTripDetails']=$data1;
			}
		}

		return $data;    
	}

	function getEmpCheckOutVehicle($userId,$empTripId,$status)
	{

		// echo($userId.$qrId);die();
		$data= array();
		$data1= array();
		date_default_timezone_set('Asia/Kolkata');
		$created_at = date('Y-m-d');
		$submittedOn = date('Y-m-d H:i:s');

       // Prepare an insert statement
		$sql = "UPDATE erp_emp_vehicle_management SET status = $status, check_out='".$submittedOn."' WHERE id = $empTripId";
		// echo $sql;die();
		if($result = mysqli_query($this->conn, $sql)){
				$query="SELECT * FROM `erp_emp_vehicle_management` WHERE id=$empTripId";
				$count=mysqli_query($this->conn, $query);
				if(mysqli_num_rows($count) > 0)
				{
							$row=mysqli_fetch_assoc($count);
							do{ 

							$data1['emp_trip_id']= $row['id'];
							$data1['emp_id']= $row['emp_id'];
							$data1['vehicle_id']= $row['vehicle_id'];
							$data1['created_at']= $row['created_at'];
							$data1['status']= $row['status'];

							if($row['status'] == 1){
							$data1['status_name']= 'VEHICLE IN';
							}else if($row['status'] == 2){
							$data1['status_name']= 'VEHICLE OUT';
							}else if($row['status'] == 3){
							$data1['status_name']= 'REACHED HOME';
							}							
							
							}while($row = mysqli_fetch_assoc($count)); 	
							$vhlmgtLog = $this->empVehicleManagementLogAdd($empTripId,$userId,$submittedOn,$status);
							$data['status']=1;
							$data['message']="Successfully";
							$data['empTripDetails']=$data1;
				}else{
					$data['status']=0;
					$data['message']="data retrieval failed";
					$data['empTripDetails']=$data1;
				}
		}else{
			$data['status']=0;
			$data['message']="status update failed";
			$data['empTripDetails']=$data1;
		}

		return $data;    
	}

	function getEmpCheckInHome($userId,$empTripId,$status)
	{

		// echo($userId.$qrId);die();
		$data= array();
		$data1= array();
		date_default_timezone_set('Asia/Kolkata');
		$created_at = date('Y-m-d');
		$submittedOn = date('Y-m-d H:i:s');

       // Prepare an insert statement
		$sql = "UPDATE erp_emp_vehicle_management SET status = $status,reched_home='".$submittedOn."' WHERE id = $empTripId";
		// echo $sql;die();
		if($result = mysqli_query($this->conn, $sql)){
				$query="SELECT * FROM `erp_emp_vehicle_management` WHERE id=$empTripId";
				$count=mysqli_query($this->conn, $query);
				if(mysqli_num_rows($count) > 0)
				{
							$row=mysqli_fetch_assoc($count);
							do{ 

							$data1['emp_trip_id']= $row['id'];
							$data1['emp_id']= $row['emp_id'];
							$data1['vehicle_id']= $row['vehicle_id'];
							$data1['created_at']= $row['created_at'];
							$data1['status']= $row['status'];

							if($row['status'] == 1){
							$data1['status_name']= 'VEHICLE IN';
							}else if($row['status'] == 2){
							$data1['status_name']= 'VEHICLE OUT';
							}else if($row['status'] == 3){
							$data1['status_name']= 'REACHED HOME';
							}							
							
							}while($row = mysqli_fetch_assoc($count)); 	
							$vhlmgtLog = $this->empVehicleManagementLogAdd($empTripId,$userId,$submittedOn,$status);
							$data['status']=1;
							$data['message']="Successfully";
							$data['empTripDetails']=$data1;
				}else{
					$data['status']=0;
					$data['message']="data retrieval failed";
					$data['empTripDetails']=$data1;
				}
		}else{
			$data['status']=0;
			$data['message']="status update failed";
			$data['empTripDetails']=$data1;
		}

		return $data;    
	}

	function getEmpDayVehicleHistory($userId)
	{

		// echo($userId.$qrId);die();
		$data= array();
		$data1= array();
		$data2= array();
		date_default_timezone_set('Asia/Kolkata');
		$cdate = date('Y-m-d');

				$query="SELECT m.*,ml.action_at,ml.action_status,ml.action_at,ml.id as mlId FROM `erp_emp_vehicle_management` as m LEFT JOIN erp_emp_vehicle_management_log as ml ON ml.management_id=m.id WHERE m.created_at='".$cdate."' AND m.emp_id=$userId";
				$count=mysqli_query($this->conn, $query);
				if(mysqli_num_rows($count) > 0)
				{
							$row=mysqli_fetch_assoc($count);
							do{ 

							$data1['id']= $row['mlId'];
							$data1['emp_trip_id']= $row['id'];
							$data1['emp_id']= $row['emp_id'];
							$data1['vehicle_details']= $this->getVehicleDetailsById($row['vehicle_id']);
							$data1['created_at']= $row['created_at'];
							$data1['action_at']= $row['action_at'];
							$data1['status']= $row['status'];

							if($row['status'] == 1){
							$data1['status_name']= 'VEHICLE IN';
							}else if($row['status'] == 2){
							$data1['status_name']= 'VEHICLE OUT';
							}else if($row['status'] == 3){
							$data1['status_name']= 'REACHED HOME';
							}							
							// $data2[] = $data1;
							}while($row = mysqli_fetch_assoc($count)); 	
							$data['status']=1;
							$data['message']="Successfully";
							$data['currentDateDetails']=$data1;
				}else{
					$data['status']=0;
					$data['message']="data retrieval failed";
					$data['currentDateDetails']=$data2;
				}
		

		return $data;    
	}

	function empVehicleManagementLogAdd($mangId,$userId,$created_at,$status){
		date_default_timezone_set('Asia/Kolkata');
		$data =array();
		$sql = "INSERT INTO erp_emp_vehicle_management_log (management_id,emp_id,action_at,action_status) VALUES (?,?,?,?)";
		// echo $sql;die();
		if ($stmt = mysqli_prepare($this->conn,$sql)) {

			mysqli_stmt_bind_param($stmt,"iisi",$mangId,$userId,$created_at,$status);
			if($output = mysqli_execute($stmt)) {
				$data['log'] = "Trip log added Successfully";
			    $data['status']=1;
			}else{
			        //echo "ERROR: Could not execute query: $sql. " . mysqli_error($this->conn);
			        $data['status']=0;
			}
		}
		else{
			    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($this->conn);
			    $data['status']=0;
		}

		return $data; 

	}


function jobCountAll($user_id)
	{

		$data=array();
		$i = 0;
		$userDetails = $this->getUserRoleByUserId($user_id);
		$emp_number = $userDetails['empNumber'];
		//echo $emp_number;
		$userRoleId = $userDetails['id'];
		//echo $userRoleId;

		$location_id = $this->getLocByEmpNumber($emp_number);
		//echo "loc".' ' .$location_id;
		
		//$i=0;
		$empNumber = $this->getEmpnumberByUserId($user_id);
       	$empresult=$this->employeeDetails($empNumber);

       	/*$EngSubTechDetails = $this->getSubTechListofEngineer($empNumber);

		$empNumber = $EngSubTechDetails['empNumber'];*/

       	$plantId = $empresult['plant_id'];
       	//echo "plantId".' ' .$plantId;


       	// echo $user_id.','.$userRoleId.','.$location_id.','.$emp_number;
       	// exit();

       	if($userRoleId == 10 || $userRoleId == 19){

       		//EnM New Tsks COunt
       			$querynewTsk="SELECT COUNT(o.id) as contnew FROM erp_ticket o LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id WHERE (o2.status_id IN (1, 6) AND o.location_id = 3 AND o.plant_id = 1 AND o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id) AND o.is_deleted = 0) ORDER BY o.id DESC";

       			//$configDate = $this->dateFormat();

		$EnMnewTsk=mysqli_query($this->conn, $querynewTsk);

								 if(mysqli_num_rows($EnMnewTsk) > 0)
								{

												$row = mysqli_fetch_assoc($EnMnewTsk);
												$EnMnewTskCount = $row['contnew']; 
												
								}else{
											$EnMnewTskCount = 0;
									}

       			//EnM Eng Tsks COunt
			$empresult=$this->empList(11);
	        for ($i=0; $i < sizeof($empresult['emplist']) ; $i++) { 
	        	$empList[] = $empresult['emplist'][$i];
	        	//to convert Array into string the following implode method is used
	        	$empLists = implode(',', $empList);
	        }
		
	        

       			$queryEngTsk="SELECT COUNT(o.id) as contEng FROM erp_ticket o LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id WHERE ((o2.accepted_by IN ($empLists) OR o2.forward_to IN ($empLists)) AND o.location_id = $location_id AND o.plant_id = $plantId AND o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id) AND o.is_deleted = 0) ORDER BY o.id DESC";


       			
				$EnMengTsk=mysqli_query($this->conn, $queryEngTsk);

		 						if(mysqli_num_rows($EnMengTsk) > 0)
								{
												$row1 = mysqli_fetch_assoc($EnMengTsk);
												$EnMengTskCount = $row1['contEng']; 
								}else{
												$EnMengTskCount = 0;
								}

       			//EnM Tech Tsks COunt
       		$empresult1=$this->empList(12);
		 
	        for ($i=0; $i < sizeof($empresult1['emplist']) ; $i++) { 
	        	$empList1[] = $empresult1['emplist'][$i];
	        	//to convert Array into string the following implode method is used
	        	$empLists1 = implode(',', $empList1);
	        }

       		$queryTechTsk = "SELECT COUNT(o.id) AS contTech FROM erp_ticket o LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id WHERE ((o2.accepted_by IN ($empLists1) OR o2.forward_to IN ($empLists1)) AND o.location_id = $location_id AND o.plant_id = $plantId AND o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id) AND o.is_deleted = 0) ORDER BY o.id DESC";

       		

				$EnMtechTsk=mysqli_query($this->conn, $queryTechTsk);

								if(mysqli_num_rows($EnMtechTsk) > 0)
								{
												$row2 = mysqli_fetch_assoc($EnMtechTsk);
												$EnMtechTskCount = $row2['contTech']; 
								}else{
											$EnMtechTskCount = 0;
								}



			$queryMyJbs="";
       		$queryDeptJbs="";

					$data['EnMNewTasks']=$EnMnewTskCount;
					$data['EnMEngTasks']=$EnMengTskCount;
					$data['EnMTechTasks']=$EnMtechTskCount;
					$data1[] = $data;
					$data['jobCountAll']=$data1;
					$data['status']=1;

       	}else if($userRoleId == 11){

       		/*Eng New Tasks Job Count*/

       		 $result=$this->multipledeptList($empNumber);

    $empresult=$this->employeeDetails($empNumber);

    $issueresult=$this->typeofissuelist($empNumber);

    $multidept[] = $empresult['work_station'];
    $multi_dept = implode(',', $multidept);
    if($result['status']== 1){
        for ($i=0; $i < sizeof($result['deptmultlist']) ; $i++) { 
            $multidept[] = $result['deptmultlist'][$i];
            //to convert Array into string the following implode method is used
            $multi_dept = implode(',', $multidept);
        }
    }
    if($issueresult['status']== 1){
        for ($i=0; $i < sizeof($issueresult['typeid']) ; $i++) { 
            $issueList[] = $issueresult['typeid'][$i];
            //to convert Array into string the following implode method is used
            $multi_issue = implode(',', $issueList);
        }
    }else{
        $multi_issue = -1;
    }
    
		$empresult1=$this->empList(11);
		 
	        for ($i=0; $i < sizeof($empresult1['emplist']) ; $i++) { 
	        	$empList1[] = $empresult1['emplist'][$i];
	        	//to convert Array into string the following implode method is used
	        	$empLists1 = implode(',', $empList1);

	        }

    	

    		$queryEngNewTsk = "SELECT COUNT(o.id) AS contEngNew FROM erp_ticket o LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id WHERE((o.user_department_id IN ($multi_dept) OR o2.forward_to = $empNumber OR o.type_of_issue_id IN ($multi_issue) OR o2.forward_to = $empNumber) AND o2.status_id IN (1,2,6) AND o2.forward_from != $empNumber AND (o2.forward_to IN ($empLists1) OR o2.forward_to IS NULL OR o2.forward_to = 0) AND o.location_id = $location_id AND o.plant_id = $plantId AND o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id) AND o.is_deleted = 0) ORDER BY o.id DESC";

    		/*echo $queryEngNewTsk1;
    		exit();*/


       		$EngNewTsk=mysqli_query($this->conn, $queryEngNewTsk);

		 						if(mysqli_num_rows($EngNewTsk) > 0)
								{
												$row1 = mysqli_fetch_assoc($EngNewTsk);
												$EngNewTskCount = $row1['contEngNew']; 
								}else{
												$EngNewTskCount = 0;
								}


       		/*Eng Inprogress Jobs Count */
       		$queryEngInprgTsk="SELECT COUNT(ta.id) AS contEngIng FROM erp_ticket_acknowledgement_action_log ta LEFT JOIN erp_ticket t ON t.id = ta.ticket_id LEFT JOIN hs_hr_employee emp ON  emp.emp_number = ta.submitted_by_emp_number LEFT JOIN erp_location loc ON loc.id = t.location_id  LEFT JOIN erp_plant plant ON plant.id = t.plant_id LEFT JOIN erp_subunit sub ON sub.id = t.user_department_id  LEFT JOIN erp_functional_location func ON func.id = t.functional_location_id LEFT JOIN erp_equipment eqp ON eqp.id = t.equipment_id LEFT JOIN erp_type_of_issue iss ON iss.id = t.type_of_issue_id LEFT JOIN erp_ticket_status sta ON sta.id = t.status_id LEFT JOIN erp_ticket_priority tktprty ON tktprty.id = t.priority_id LEFT JOIN erp_ticket_severity tktsvrty ON tktsvrty.id = t.severity_id LEFT JOIN hs_hr_employee empsub ON empsub.emp_number = t.submitted_by_name  WHERE ta.id IN (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log GROUP BY ticket_id) and ta.accepted_by = $empNumber AND ta.status_id IN (3,4) ORDER BY t.job_id DESC";


       		$EngInprgTsk=mysqli_query($this->conn, $queryEngInprgTsk);

		 						if(mysqli_num_rows($EngInprgTsk) > 0)
								{
												$row2 = mysqli_fetch_assoc($EngInprgTsk);
												$EngInprgTskCount = $row2['contEngIng']; 
								}else{
												$EngInprgTskCount = 0;
								}

       		/*Eng Resolved Tasks Count*/
				   $queryEngResolvedTsk="SELECT COUNT(t.id) as id,t.job_id AS job_id,t.subject AS subject,tp.name AS priority,tsv.name AS 						severity,t.sla AS sla,CONCAT(e.emp_firstname,' ',e.emp_lastname) AS raised_by,
				   							t.reported_on AS raised_on,
				   							ta.submitted_by_name AS acknowledged_by,ta.submitted_on AS acknowledged_on,ts.name AS status
											FROM erp_ticket_acknowledgement_action_log ta
											LEFT JOIN erp_ticket t ON t.id = ta.ticket_id
											LEFT JOIN erp_ticket_priority tp ON tp.id = ta.priority_id
											LEFT JOIN erp_ticket_severity tsv ON tsv.id = ta.severity_id
											LEFT JOIN hs_hr_employee e ON e.emp_number = t.reported_by
							 				LEFT JOIN erp_ticket_status ts ON ts.id = ta.status_id
											WHERE ta.id IN (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log GROUP BY ticket_id)
											and ta.forward_to = $empNumber AND ta.status_id = 14
											order by id desc";

				$EngReslvdTsk=mysqli_query($this->conn, $queryEngResolvedTsk);

		 						if(mysqli_num_rows($EngReslvdTsk) > 0)
								{
												$row2 = mysqli_fetch_assoc($EngReslvdTsk);
												$EngResvdTaskCount = $row2['id']; 
								}else{
												$EngResvdTaskCount = 0;
								}

       		/*Eng Reject Tasks Count*/
       		$queryEngRejectTsk="SELECT COUNT(t.id) as id,t.job_id AS job_id,t.subject AS subject,tp.name AS priority,tsv.name AS severity,t.sla AS sla,CONCAT(e.emp_firstname,' ',e.emp_lastname) AS raised_by,t.reported_on AS raised_on,ta.submitted_by_name AS acknowledged_by,ta.submitted_on AS acknowledged_on,ts.name AS status, ta.comment as comment
			FROM erp_ticket_acknowledgement_action_log ta
			LEFT JOIN erp_ticket t ON t.id = ta.ticket_id
			LEFT JOIN erp_ticket_priority tp ON tp.id = ta.priority_id
			LEFT JOIN erp_ticket_severity tsv ON tsv.id = ta.severity_id
			LEFT JOIN hs_hr_employee e ON e.emp_number = t.reported_by
			 LEFT JOIN erp_ticket_status ts ON ts.id = ta.status_id
			WHERE ta.id IN (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log GROUP BY ticket_id) and ta.forward_to = $empNumber
			AND ta.status_id = 16
			order by id desc";

				$EngRejctdTsk=mysqli_query($this->conn, $queryEngRejectTsk);

		 						if(mysqli_num_rows($EngRejctdTsk) > 0)
								{
												$row3 = mysqli_fetch_assoc($EngRejctdTsk);
												$EngRejctdTaskCount = $row3['id']; 
								}else{
												$EngRejctdTaskCount = 0;
								}

       		/*Eng Tech Tasks Count*/

       			//$EngSubTechDetails = $this->getSubTechListofEngineer($empNumber);

       				$empresult5=$this->empEngTechList($emp_number);
       				/*echo "<pre>";
		 print_r($empresult5);
		 exit();*/
		 
	        for ($i=0; $i < sizeof($empresult5['empTechlist']) ; $i++) { 
	        	$empList5[] = $empresult5['empTechlist'][$i];
	        	//to convert Array into string the following implode method is used
	        	$empLists5 = implode(',', $empList5);
	        	/*print_r($empLists5);
	        	exit();*/

	        }

       	
       		$queryEngTechTsk="SELECT COUNT(o.id) AS id, o.job_id AS job_id, o.location_id AS o__location_id, o.plant_id AS o__plant_id, o.user_department_id AS o__user_department_id, o.notify_to AS o__notify_to, o.functional_location_id AS o__functional_location_id, o.equipment_id AS o__equipment_id, o.type_of_issue_id AS o__type_of_issue_id, o.status_id AS o__status_id, o.sla AS o__sla, o.subject AS subject, o.description AS o__description, o.priority_id AS o__priority_id, o.severity_id AS o__severity_id, o.reported_by AS o__reported_by, o.reported_on AS o__reported_on, o.submitted_by_name AS o__submitted_by_name, o.submitted_by_emp_number AS o__submitted_by_emp_number, o.submitted_on AS o__submitted_on, o.modified_by_name AS o__modified_by_name, o.modified_by_emp_number AS o__modified_by_emp_number, o.modified_on AS o__modified_on, o.is_preventivemaintenance AS o__is_preventivemaintenance, o.is_deleted AS o__is_deleted, o2.id AS o2__id, o2.ticket_id AS o2__ticket_id, o2.status_id AS o2__status_id, o2.priority_id AS o2__priority_id, o2.severity_id AS o2__severity_id, o2.comment AS o2__comment, o2.machine_status AS o2__machine_status, o2.assigned_date AS o2__assigned_date, o2.due_date AS o2__due_date, o2.accepted_by AS o2__accepted_by, o2.rejected_by AS o2__rejected_by, o2.submitted_on AS o2__submitted_on, o2.forward_from AS o2__forward_from, o2.forward_to AS o2__forward_to, o2.submitted_by_name AS o2__submitted_by_name, o2.submitted_by_emp_number AS o2__submitted_by_emp_number, o2.created_by_user_id AS o2__created_by_user_id, o2.root_cause_id AS o2__root_cause_id, o2.response_id AS o2__response_id FROM erp_ticket o LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id WHERE ((o2.accepted_by IN ($empLists5) OR o2.forward_to IN ($empLists5)) AND o.location_id = 3 AND o.plant_id = 1 AND o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id) AND o.is_deleted = 0) ORDER BY o.id DESC";

       		/*$queryEngTechTsk="SELECT COUNT(o.id) AS id, o.job_id AS o__job_id, o.location_id AS o__location_id, o.plant_id AS o__plant_id, o.user_department_id AS o__user_department_id, o.notify_to AS o__notify_to, o.functional_location_id AS o__functional_location_id, o.equipment_id AS o__equipment_id, o.type_of_issue_id AS o__type_of_issue_id, o.status_id AS o__status_id, o.sla AS o__sla, o.subject AS o__subject, o.description AS o__description, o.priority_id AS o__priority_id, o.severity_id AS o__severity_id, o.reported_by AS o__reported_by, o.reported_on AS o__reported_on, o.submitted_by_name AS o__submitted_by_name, o.submitted_by_emp_number AS o__submitted_by_emp_number, o.submitted_on AS o__submitted_on, o.modified_by_name AS o__modified_by_name, o.modified_by_emp_number AS o__modified_by_emp_number, o.modified_on AS o__modified_on, o.is_preventivemaintenance AS o__is_preventivemaintenance, o.is_deleted AS o__is_deleted, o2.id AS o2__id, o2.ticket_id AS o2__ticket_id, o2.status_id AS o2__status_id, o2.priority_id AS o2__priority_id, o2.severity_id AS o2__severity_id, o2.comment AS o2__comment, o2.machine_status AS o2__machine_status, o2.assigned_date AS o2__assigned_date, o2.due_date AS o2__due_date, o2.accepted_by AS o2__accepted_by, o2.rejected_by AS o2__rejected_by, o2.submitted_on AS o2__submitted_on, o2.forward_from AS o2__forward_from, o2.forward_to AS o2__forward_to, o2.submitted_by_name AS o2__submitted_by_name, o2.submitted_by_emp_number AS o2__submitted_by_emp_number, o2.created_by_user_id AS o2__created_by_user_id, o2.root_cause_id AS o2__root_cause_id, o2.response_id AS o2__response_id FROM erp_ticket o LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id WHERE ((o.status_id = 3 OR o2.status_id = 7 OR o2.status_id = 9 OR o2.status_id = 8) AND (o2.accepted_by IN ($empLists5) OR o2.forward_to IN ($empLists5)) OR o2.forward_to IN ($empLists5) AND o.location_id = 3 AND o.plant_id = 1 AND o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id) AND o.is_deleted = 0) ORDER BY o.id DESC";*/


       		/*echo $queryEngTechTsk;
       		exit();*/
       		$EngTechTsks=mysqli_query($this->conn, $queryEngTechTsk);

		 						if(mysqli_num_rows($EngTechTsks) > 0)
								{
												$row4 = mysqli_fetch_assoc($EngTechTsks);
												$EngTechTasksCount = $row4['id']; 
								}else{
												$EngTechTasksCount = 0;
								}


       		$queryMyJbs="";
       		$queryDeptJbs="";

       		$data['EngNewTasks']=$EngNewTskCount;
       		$data['EngInprgTasks']=$EngInprgTskCount;
       		$data['EngResvdTasks']= $EngResvdTaskCount;
       		$data['EngRejctTasks']= $EngRejctdTaskCount;
       		$data['EngTechnTasks']= $EngTechTasksCount;
       		$data1[] = $data;
					$data['jobCountAll']=$data1;
					$data['status']=1;


       	}else if($userRoleId == 12){

       		
       		$empresult=$this->empList(12);
		 // echo "<pre>";
		 // print_r($empresult);
		 // exit();
	        for ($i=0; $i < sizeof($empresult['emplist']) ; $i++) { 
	        	$empList[] = $empresult['emplist'][$i];
	        	//to convert Array into string the following implode method is used
	        	$empLists = implode(',', $empList);
	        }

        $i=0;
        $queryTechNewTsk = "SELECT COUNT(o.id) AS id, o.job_id AS job_id, o.location_id AS location_id, o.plant_id AS plant_id, o.user_department_id AS user_department_id, o.functional_location_id AS functional_location_id, o.equipment_id AS equipment_id, o.type_of_issue_id AS type_of_issue_id, o.status_id AS status_id, o.sla AS sla, o.subject AS subject, o.description AS description, o.priority_id AS priority_id, o.severity_id AS severity_id, o.reported_by AS reported_by, o.reported_on AS reported_on, o.submitted_by_name AS submitted_by_name, o.submitted_by_emp_number AS submitted_by_emp_number, o.submitted_on AS submitted_on, o.modified_by_name AS modified_by_name, o.modified_by_emp_number AS modified_by_emp_number, o.modified_on AS modified_on, o.is_preventivemaintenance AS is_preventivemaintenance, o.is_deleted AS is_deleted FROM erp_ticket o LEFT JOIN erp_ticket_acknowledgement_action_log o2 ON o.id = o2.ticket_id
WHERE (o2.forward_to = $emp_number AND o2.status_id = 2 AND o2.forward_to IN ($empLists)
AND o.location_id = 3 
AND o.plant_id = 1 AND
o2.id IN (SELECT MAX(o3.id) AS o3__0 FROM erp_ticket_acknowledgement_action_log o3 GROUP BY o3.ticket_id) AND o.is_deleted = 0) ORDER BY o.id DESC";

	$TechNewTsks=mysqli_query($this->conn, $queryTechNewTsk);

		 						if(mysqli_num_rows($TechNewTsks) > 0)
								{
												$row4 = mysqli_fetch_assoc($TechNewTsks);
												$TechNewTasksCount = $row4['id']; 
								}else{
												$TechNewTasksCount = 0;
								}


       		$queryTechInprgTsk="SELECT COUNT(ta.ticket_id) AS id,t.job_id as job_id,t.sla AS sla,sta.name as status,t.subject as subject,tktprty.name as priority,tktsvrty.name as severity,CONCAT(emp.emp_firstname,emp.emp_lastname) AS raised_by,t.reported_on as raised_on,CONCAT(emp.emp_firstname,emp.emp_lastname) AS acknowledged_by,ta.submitted_on AS acknowledged_on FROM erp_ticket_acknowledgement_action_log ta LEFT JOIN erp_ticket t ON t.id = ta.ticket_id LEFT JOIN hs_hr_employee emp ON  emp.emp_number = ta.submitted_by_emp_number LEFT JOIN erp_location loc ON loc.id = t.location_id  LEFT JOIN erp_plant plant ON plant.id = t.plant_id LEFT JOIN erp_subunit sub ON sub.id = t.user_department_id  LEFT JOIN erp_functional_location func ON func.id = t.functional_location_id LEFT JOIN erp_equipment eqp ON eqp.id = t.equipment_id LEFT JOIN erp_type_of_issue iss ON iss.id = t.type_of_issue_id LEFT JOIN erp_ticket_status sta ON sta.id = t.status_id LEFT JOIN erp_ticket_priority tktprty ON tktprty.id = t.priority_id LEFT JOIN erp_ticket_severity tktsvrty ON tktsvrty.id = t.severity_id LEFT JOIN hs_hr_employee empsub ON empsub.emp_number  = t.submitted_by_name  WHERE ta.id IN (SELECT MAX(id) FROM erp_ticket_acknowledgement_action_log GROUP BY ticket_id) and ta.accepted_by = $emp_number AND ta.status_id IN (3,4) ORDER BY t.job_id DESC";

       		$TechInprgTsks=mysqli_query($this->conn, $queryTechInprgTsk);

		 						if(mysqli_num_rows($TechInprgTsks) > 0)
								{
												$row5 = mysqli_fetch_assoc($TechInprgTsks);
												$TechInprgTasksCount = $row5['id']; 
								}else{
												$TechInprgTasksCount = 0;
								}


       		$queryMyJbs="";
       		$queryDeptJbs="";


       		$data['TechNewTasks']=$TechNewTasksCount;
       		$data['TechInprgTasks']=$TechInprgTasksCount;
       		
       		$data1[] = $data;
					$data['jobCountAll']=$data1;
					$data['status']=1;
       	}else{

       		$queryMyJbs="";
       		$queryDeptJbs="";

       	}


     

	return $data;
	}


	// Gender List Dashboard 
    function getGenderList($plantId,$departmentId,$genderType)
    {
    	

		   if($plantId != 0 && $departmentId==0){
        $empGenderQuery = "SELECT s.name as department, count(case when emp.emp_gender=1 then 1 end) as male, COUNT(case when emp.emp_gender=2 then 1 end) as female, count( case when emp.emp_gender is null then 1 end) as other from erp_subunit as s LEFT JOIN hs_hr_employee as emp on emp.work_station=s.id  where emp.plant_id='$plantId' AND emp.termination_id IS NULL group by emp.work_station order by count(emp.emp_gender) DESC";
      }else if($departmentId){ 
        $empGenderQuery = "SELECT s.name as department, count(case when emp.emp_gender=1 then 1 end) as male, COUNT(case when emp.emp_gender=2 then 1 end) as female, count( case when emp.emp_gender is null then 1 end) as other from erp_subunit as s LEFT JOIN hs_hr_employee as emp on emp.work_station=s.id  where s.id='$departmentId' and emp.plant_id='$plantId' AND emp.termination_id IS NULL group by emp.work_station order by count(emp.emp_gender) DESC";
      }
      else{
         $empGenderQuery = "SELECT s.name as department, count(case when emp.emp_gender=1 then 1 end) as male, COUNT(case when emp.emp_gender=2 then 1 end) as female, count( case when emp.emp_gender is null then 1 end) as other from erp_subunit as s LEFT JOIN hs_hr_employee as emp on emp.work_station=s.id WHERE emp.termination_id IS NULL group by emp.work_station order by count(emp.emp_gender) DESC";
      }
      if($genderType != 0){
         $empGenderQuery = "SELECT s.name as department, count(case when emp.emp_gender=1 then 1 end) as male, COUNT(case when emp.emp_gender=2 then 1 end) as female, count( case when emp.emp_gender is null then 1 end) as other from erp_subunit as s LEFT JOIN hs_hr_employee as emp on emp.work_station=s.id WHERE emp.termination_id IS NULL AND emp.emp_gender= '$genderType' group by emp.work_station order by count(emp.emp_gender) DESC";
      }

        $data = array();  			

		$empGenderObj = mysqli_query($this->conn, $empGenderQuery);

		
		if(mysqli_num_rows($empGenderObj) > 0)
		{
				$empGender=mysqli_fetch_assoc($empGenderObj);
			do{ 

						$data['department']=$empGender['department'];
						$data['male']=$empGender['male'];
						$data['female']=$empGender['female'];
				
						$data1[] = $data;
					}while($empGender = mysqli_fetch_assoc($empGenderObj)); 				
						$data['status']=1;
						$data['gender_list']=$data1;
							
		}else{
				$data['status']=0;
				$data['gender_list']=array();
			}
		return $data; 
    }

 
    // Employees in plant Dashboard
    
     function getEmployeesinplant($plantId,$departmentId)
    {
    	 $data=array();  	
    	 $dep=array();  	
    	 $data2=array();  	

		   if($plantId != 0 && $departmentId==0){
        $empDptCntqry = "SELECT case when s.name is null then 'Not Assigned' else s.name end  as department,count(emp.emp_number) as empCount FROM `hs_hr_employee` as emp left join erp_subunit as s on s.id=emp.work_station WHERE plant_id ='$plantId' and termination_id is null group by s.name";
      }else if($departmentId!=0){ 
        $empDptCntqry = "SELECT s.name as department,count(emp.emp_number) as empCount FROM `hs_hr_employee` as emp left join erp_subunit as s on s.id=emp.work_station where emp.work_station='$departmentId' and emp.plant_id='$plantId' and termination_id is null ";
        $empDptCount = "SELECT count(emp.emp_number) as empCount FROM `hs_hr_employee` as emp left join erp_subunit as s on s.id=emp.work_station where emp.work_station !='$departmentId' and emp.plant_id='$plantId' and termination_id is null ";

         $empDptCntRslt = mysqli_query($this->conn,$empDptCount);

         if(mysqli_num_rows($empDptCntRslt) > 0)
		{
			 	foreach($empDptCntRslt as $empDpt){

          
           	$dep['department']="other";
			 $dep['empCount']=$empDpt['empCount'];
			 $data1[] = $dep;
			 //$data['emp_other']=$data2;
			// $data['emp_list']=$data1;
        }
							
		}
      }
    

       		

		$empDptCntListObj = mysqli_query($this->conn, $empDptCntqry);

		
		if(mysqli_num_rows($empDptCntListObj) > 0)
		{
				$empDptCount=mysqli_fetch_assoc($empDptCntListObj);
			do{ 

						$data['department']=$empDptCount['department'];
						$data['empCount']=$empDptCount['empCount'];
						$data1[] = $data;
						//$data1[] = $data2;
					}while($empDptCount = mysqli_fetch_assoc($empDptCntListObj)); 				
						$data['status']=1;
						$data['emp_list']=$data1;
							
		}else{
				$data['status']=0;
				$data['emp_list']=array();
			}

		return $data; 
    }

  //    function getEmployeesinplant($plantId,$departmentId)
  //   {
    	

		//    if($plantId != 0 && $departmentId==0){
  //       $empDptCntqry = "SELECT case when s.name is null then 'Not Assigned' else s.name end  as department,count(emp.emp_number) as empCount FROM `hs_hr_employee` as emp left join erp_subunit as s on s.id=emp.work_station WHERE plant_id ='$plantId' and termination_id is null group by s.name";
  //     }else if($departmentId!=0){ 
  //       $empDptCntqry = "SELECT s.name as department,count(emp.emp_number) as empCount FROM `hs_hr_employee` as emp left join erp_subunit as s on s.id=emp.work_station where emp.work_station='$departmentId' and emp.plant_id='$plantId' and termination_id is null ";
  //     }
    

  //       $data=array();  			

		// $empDptCntList = mysqli_query($this->conn, $empDptCntqry);

		
		// if(mysqli_num_rows($empDptCntList) > 0)
		// {
		// 		$empDptCnt=mysqli_fetch_assoc($empDptCntList);
		// 	do{ 

		// 				$data['department']=$empDptCnt['department'];
		// 				$data['empCount']=$empDptCnt['empCount'];
		// 				$data1[] = $data;
		// 			}while($empDptCnt = mysqli_fetch_assoc($empDptCntList)); 				
		// 				$data['status']=1;
		// 				$data['emp_list']=$data1;
							
		// }else{
		// 		$data['status']=0;
		// 		$data['emp_list']=array();
		// 	}
		// return $data; 
  //   }

      // Employees Attendance Dashboard

 //     function getEmpAttendance($plantId,$departmentId,$punchDate,$isAllDept)
 //    {
 //    		$currntDt = date('Y-m-d');

	// 	    if($isAllDept==1){
 //            if($punchDate == $currntDt){  
 //            $empAtdQry = "SELECT CASE WHEN dep.name IS NULL THEN 'Not Assigned' ELSE dep.name END as department,count(DISTINCT(attd.employee_id)) as count  from            hs_hr_employee as emp  left join erp_subunit as dep on dep.id=emp.work_station LEFT JOIN erp_attendance_record as attd
 //                      ON attd.employee_id=emp.emp_number  where attd.state= 'PUNCHED IN' AND Date(attd.punch_in_utc_time)=DATE('$punchDate') AND emp.termination_id IS NULL ";
 //            }else{
 //              $empAtdQry = "SELECT CASE WHEN dep.name IS NULL THEN 'Not Assigned' ELSE dep.name END as department,count(DISTINCT(attd.employee_id)) as count  from  hs_hr_employee as emp  left join erp_subunit as dep on dep.id=emp.work_station LEFT JOIN erp_attendance_record as attd
 //                      ON attd.employee_id=emp.emp_number  where attd.state= 'PUNCHED OUT' AND Date(attd.punch_in_utc_time)=DATE('$punchDate') AND emp.termination_id IS NULL ";
 //            }
 //            if($plantId != 0 && $departmentId==0){
 //              $empAtdQry .= "AND emp.plant_id= '$plantId'";
 //            }else if($departmentId){
 //              $empAtdQry .= " AND dep.id='$departmentId' and emp.plant_id='$plantId' ";
 //            }
 //            $empAtdQry .=   " GROUP  by dep.id";
            
 //        }else{
 //          if($punchDate == $currntDt){
 //            $empAtdQry = "SELECT COUNT(DISTINCT(emp.emp_number)) as count from hs_hr_employee as emp  LEFT JOIN erp_attendance_record as attd ON attd.employee_id=emp.emp_number  where attd.state= 'PUNCHED IN' AND Date(attd.punch_in_utc_time)=DATE('$punchDate') AND emp.termination_id IS NULL";
            
 //          }else{
 //            $empAtdQry = "SELECT COUNT(DISTINCT(attd.employee_id)) as count  from hs_hr_employee as emp  LEFT JOIN erp_attendance_record as attd ON attd.employee_id=emp.emp_number  where attd.state= 'PUNCHED OUT' AND Date(attd.punch_in_utc_time)=DATE('$punchDate') AND emp.termination_id IS NULL";
 //            $totlempAtdQry = "SELECT COUNT(emp_number) as count FROM hs_hr_employee where termination_id IS NULL" ;
 //          }
 //          if($plantId != 0 && $departmentId==0){
 //              $empAtdQry .= " AND emp.plant_id= '$plantId' ";
 //            }else if($departmentId){
 //              $empAtdQry .= " AND dep.id='$departmentId' and emp.plant_id='$plantId' GROUP  by dep.id ";
               
 //            }
           
 //          $totlempAtdQry = "SELECT COUNT(emp_number) as count FROM hs_hr_employee where termination_id IS NULL" ;
 //        }
    

 //        $data=array();  			

	// 	$empAtdList = mysqli_query($this->conn, $empAtdQry);
		

	// 	 if($isAllDept==1){
	// 	if(mysqli_num_rows($empAtdList) > 0)
	// 	{
	// 			$empAtdCnt=mysqli_fetch_assoc($empAtdList);
	// 		do{ 

	// 					$data['department']=$empAtdCnt['department'];
	// 					$data['empCount']=$empAtdCnt['count'];
	// 					$data1[] = $data;
	// 				}while($empAtdCnt = mysqli_fetch_assoc($empAtdList)); 				
	// 					$data['status']=1;
	// 					$data['empAttdList']=$data1;
							
	// 	}else{
	// 			$data['status']=0;
	// 			$data['emp_list']=array();
	// 		}
	// }else{
	// 	$totlempAtdResult = mysqli_query($this->conn,$totlempAtdQry);
	// 	if(mysqli_num_rows($empAtdList) > 0)
	// 	{
	// 			$empAtdCnt=mysqli_fetch_assoc($empAtdList);
	// 		do{ 

	// 					$data['department']="Present";
	// 					$data['empCount']=$empAtdCnt['count'];
	// 					$data1[] = $data;
	// 				}while($empAtdCnt = mysqli_fetch_assoc($empAtdList)); 				
	// 					$data['status']=1;
	// 					$data['empAttdList']=$data1;
	// 	} 
	// 		//			if(mysqli_num_rows($totlempAtdResult) > 0)
	// 	// {
	// 	// 		$totlempAtd=mysqli_fetch_assoc($totlempAtdResult);
	// 	// 	do{ 

	// 	// 				$data['department']="Total";
	// 	// 				$data['empCount']=$totlempAtd['count'];
	// 	// 				$data2[] = $data;
	// 	// 			}while($totlempAtd = mysqli_fetch_assoc($totlempAtdResult)); 				
	// 	// 				$data['status']=1;
	// 	// 				$data['empAttdList']=$data2;
	// 	// }
	// 	// else{
	// 	// 		$data['status']=0;
	// 	// 		$data['emp_list']=array();
	// 	// 	}
		
 //    }
 //    return $data; 
		
	// 	}

	function getEmpAttendance($plantId,$punchDate,$isAllDept)
    {
    		$currntDt = date('Y-m-d');
    		$departmentId=0;
    		$data=array();  			
        	$dep=array();  
		    if($isAllDept==1){
            if($punchDate == $currntDt){  
            $empAtdQry = "SELECT CASE WHEN dep.name IS NULL THEN 'Not Assigned' ELSE dep.name END as department,count(DISTINCT(attd.employee_id)) as count  from            hs_hr_employee as emp  left join erp_subunit as dep on dep.id=emp.work_station LEFT JOIN erp_attendance_record as attd
                      ON attd.employee_id=emp.emp_number  where attd.state= 'PUNCHED IN' AND Date(attd.punch_in_utc_time)=DATE('$punchDate') AND emp.termination_id IS NULL ";
            }else{
              $empAtdQry = "SELECT CASE WHEN dep.name IS NULL THEN 'Not Assigned' ELSE dep.name END as department,count(DISTINCT(attd.employee_id)) as count  from  hs_hr_employee as emp  left join erp_subunit as dep on dep.id=emp.work_station LEFT JOIN erp_attendance_record as attd
                      ON attd.employee_id=emp.emp_number  where attd.state= 'PUNCHED OUT' AND Date(attd.punch_in_utc_time)=DATE('$punchDate') AND emp.termination_id IS NULL ";
            }
            if($plantId != 0 && $departmentId==0){
              $empAtdQry .= "AND emp.plant_id= '$plantId'";
            }else if($departmentId){
              $empAtdQry .= " AND dep.id='$departmentId' and emp.plant_id='$plantId' ";
            }
            $empAtdQry .=   " GROUP  by dep.id";
            
        }else{
          if($punchDate == $currntDt){
            $empAtdQry = "SELECT COUNT(DISTINCT(emp.emp_number)) as count from hs_hr_employee as emp  LEFT JOIN erp_attendance_record as attd ON attd.employee_id=emp.emp_number  where attd.state= 'PUNCHED IN' AND Date(attd.punch_in_utc_time)=DATE('$punchDate') AND emp.termination_id IS NULL";
            
          }else{
            $empAtdQry = "SELECT COUNT(DISTINCT(attd.employee_id)) as count  from hs_hr_employee as emp  LEFT JOIN erp_attendance_record as attd ON attd.employee_id=emp.emp_number  where attd.state= 'PUNCHED OUT' AND Date(attd.punch_in_utc_time)=DATE('$punchDate') AND emp.termination_id IS NULL";
             $totlempAtdQry = "SELECT COUNT(emp_number) as count FROM hs_hr_employee where termination_id IS NULL" ;
          }
          if($plantId != 0 && $departmentId==0){
              $empAtdQry .= " AND emp.plant_id= '$plantId' ";
            }else if($departmentId){
              $empAtdQry .= " AND dep.id='$departmentId' and emp.plant_id='$plantId' GROUP  by dep.id ";
               
            }
           
          $totlempAtdQry = "SELECT COUNT(emp_number) as count FROM hs_hr_employee where termination_id IS NULL" ;
          $totlempAtdList = mysqli_query($this->conn, $totlempAtdQry);

          	 	 if(mysqli_num_rows($totlempAtdList) > 0)
		{
			 	foreach($totlempAtdList as $totlempAtd){

          
           	$dep['department']="Total";
			 $dep['empCount']=$totlempAtd['count'];
			 $data1[] = $dep;
			// $data['emp_total']=$data2;
			
        }
							
		}
          
        }
    

      			

		$empAtdList = mysqli_query($this->conn, $empAtdQry);
		

		 if($isAllDept==1){
	
		if(mysqli_num_rows($empAtdList) > 0)
		{
				$empAtdCnt=mysqli_fetch_assoc($empAtdList);
			do{ 

						$data['department']=$empAtdCnt['department'];
						$data['empCount']=$empAtdCnt['count'];
						$data1[] = $data;
					}while($empAtdCnt = mysqli_fetch_assoc($empAtdList)); 				
						$data['status']=1;
						$data['empAttdList']=$data1;
							
		}else{
				$data['status']=0;
				$data['emp_list']=array();
			}
	}else{
		$totlempAtdResult = mysqli_query($this->conn,$totlempAtdQry);
		if(mysqli_num_rows($empAtdList) > 0)
		{
				$empAtdCnt=mysqli_fetch_assoc($empAtdList);
			do{ 

						$data['department']="Present";
						$data['empCount']=$empAtdCnt['count'];
						$data1[] = $data;
					}while($empAtdCnt = mysqli_fetch_assoc($empAtdList)); 				
						$data['status']=1;
						$data['empAttdList']=$data1;
		} 
			
		
    }
    return $data; 
		
		}

//Leave Dashboard

	function getEmpLeave($plantId,$departmentId,$frm,$to)
    {
      if($plantId != 0 && $departmentId==0){
        $empLveQuery = "SELECT dep.name as department,count(case when l.status=1 then 1 end) as pending,count(case when l.status=2 then 1 end) as schedule,count(case when l.status=3 then 1 end) as taken  from erp_leave as l left join hs_hr_employee as emp on emp.emp_number= l.emp_number left join erp_subunit as dep on dep.id=emp.work_station where emp.plant_id='$plantId' and l.date between '$frm' and '$to' group by dep.id order by  count(l.status) DESC";
      }else if($departmentId){
        $empLveQuery = "SELECT dep.name as department,count(case when l.status=1 then 1 end) as pending,count(case when l.status=2 then 1 end) as schedule,count(case when l.status=3 then 1 end) as taken  from erp_leave as l left join hs_hr_employee as emp on emp.emp_number= l.emp_number left join erp_subunit as dep on dep.id=emp.work_station where dep.id='$departmentId' and emp.plant_id='$plantId' and  l.date between '$frm' and '$to' group by dep.id";
      }
      else{
        
        $empLveQuery = "SELECT dep.name as department,count(case when l.status=1 then 1 end) as pending,count(case when l.status=2 then 1 end) as schedule,count(case when l.status=3 then 1 end) as taken  from erp_leave as l left join hs_hr_employee as emp on emp.emp_number= l.emp_number left join erp_subunit as dep on dep.id=emp.work_station where l.date between '$frm' and '$to'  group by dep.id order by  count(l.status) DESC";

              }

        $data=array();  			
		$empLveResult = mysqli_query($this->conn, $empLveQuery);
		if(mysqli_num_rows($empLveResult) > 0)
		{
				$empDptCnt=mysqli_fetch_assoc($empLveResult);
			do{ 

						$data['department']=$empDptCnt['department'];
						$data['pending']=$empDptCnt['pending'];
						$data['schedule']=$empDptCnt['schedule'];
						$data['taken']=$empDptCnt['taken'];
						$data1[] = $data;
					}while($empDptCnt = mysqli_fetch_assoc($empLveResult)); 				
						$data['status']=1;
						$data['empLeaveList']=$data1;
							
		}else{
				$data['status']=0;
				$data['empLeaveList']=array();
			}
		return $data; 
    }

    //Employee Age Dashboard

	function getEmpAge($plantId,$departmentId)
    {
        $empAgeQry = "SELECT  dep.name as department,
count(case WHEN DATEDIFF(SYSDATE(), emp_birthday)/365 >20 AND DATEDIFF(SYSDATE(), emp_birthday)/365 <=30  THEN 1 END) as 20_30,count(case WHEN DATEDIFF(SYSDATE(), emp_birthday)/365 >30 AND DATEDIFF(SYSDATE(), emp_birthday)/365 <=40  THEN 1 END) as 30_40,count(case WHEN DATEDIFF(SYSDATE(), emp_birthday)/365 >40 AND DATEDIFF(SYSDATE(), emp_birthday)/365 <=50  THEN 1 END) as 40_50,count(case WHEN DATEDIFF(SYSDATE(), emp_birthday)/365 >50 AND DATEDIFF(SYSDATE(), emp_birthday)/365 <=60  THEN 1 END) as 50_60 from hs_hr_employee as emp
LEFT JOIN erp_subunit as dep on dep.id=emp.work_station  WHERE(DATEDIFF(SYSDATE(), emp_birthday)/365) AND termination_id is null";
            
          
          if($plantId != 0 && $departmentId==0){
              $empAgeQry .= " AND emp.plant_id= '$plantId' GROUP  by dep.id order by count(DATEDIFF(SYSDATE(), emp_birthday)/365) DESC";
            }else if($departmentId){
              $empAgeQry .= " AND dep.id='$departmentId' and emp.plant_id='$plantId' GROUP  by dep.id order by count(DATEDIFF(SYSDATE(), emp_birthday)/365) DESC";
               
            }

        $data=array();  			
		$empAgeResult = mysqli_query($this->conn, $empAgeQry);
		if(mysqli_num_rows($empAgeResult) > 0)
		{
				$empDptCnt=mysqli_fetch_assoc($empAgeResult);
			do{ 

						$data['department']=$empDptCnt['department'];
						$data['20_30']=$empDptCnt['20_30'];
						$data['30_40']=$empDptCnt['30_40'];
						$data['40_50']=$empDptCnt['40_50'];
						$data['50_60']=$empDptCnt['50_60'];
						$data1[] = $data;
					}while($empDptCnt = mysqli_fetch_assoc($empAgeResult)); 				
						$data['status']=1;
						$data['empAgeList']=$data1;
							
		}else{
				$data['status']=0;
				$data['empAgeList']=array();
			}
		return $data; 
    }
      //Employee Exp Dashboard

	function getEmpExp($plantId,$departmentId)
    {
           $empExpQry = "SELECT  dep.name as department,
count(case WHEN DATEDIFF(SYSDATE(), joined_date)/365 >1 AND DATEDIFF(SYSDATE(), joined_date)/365 <=3  THEN 1 END) as 1_3,count(case WHEN DATEDIFF(SYSDATE(), joined_date)/365 >3 AND DATEDIFF(SYSDATE(), joined_date)/365 <=6  THEN 1 END) as 3_6,count(case WHEN DATEDIFF(SYSDATE(), joined_date)/365 >6 AND DATEDIFF(SYSDATE(), joined_date)/365 <=8  THEN 1 END) as 6_8,count(case WHEN DATEDIFF(SYSDATE(), joined_date)/365 >8 AND DATEDIFF(SYSDATE(), joined_date)/365 <=12  THEN 1 END) as 8_12,count(case WHEN DATEDIFF(SYSDATE(), joined_date)/365 >12 AND DATEDIFF(SYSDATE(), joined_date)/365 <=18  THEN 1 END) as 12_18 from hs_hr_employee as emp
LEFT JOIN erp_subunit as dep on dep.id=emp.work_station  WHERE(DATEDIFF(SYSDATE(), joined_date)/365) AND termination_id is null";
            
          
          if($plantId != 0 && $departmentId==0){
              $empExpQry .= " AND emp.plant_id= '$plantId' GROUP  by dep.id order by count(DATEDIFF(SYSDATE(), joined_date)/365) DESC";
            }else if($departmentId){
              $empExpQry .= " AND dep.id='$departmentId' and emp.plant_id='$plantId' GROUP  by dep.id  order by count(DATEDIFF(SYSDATE(), joined_date)/365) DESC";
               
            }

        $data=array();  			
		$empExpResult = mysqli_query($this->conn, $empExpQry);
		if(mysqli_num_rows($empExpResult) > 0)
		{
				$empDptCnt=mysqli_fetch_assoc($empExpResult);
			do{ 

						$data['department']=$empDptCnt['department'];
						$data['1_3']=$empDptCnt['1_3'];
						$data['3_6']=$empDptCnt['3_6'];
						$data['6_8']=$empDptCnt['6_8'];
						$data['8_12']=$empDptCnt['8_12'];
						$data['12_18']=$empDptCnt['12_18'];
						$data1[] = $data;
					}while($empDptCnt = mysqli_fetch_assoc($empExpResult)); 				
						$data['status']=1;
						$data['empExpList']=$data1;
							
		}else{
				$data['status']=0;
				$data['empExpList']=array();
			}
		return $data; 
    }
  //Employee Late-In Dashboard

	function getEmpLateIn($plantId,$departmentId,$reqdate)
    {
          $date =  date('d/m/Y',strtotime($reqdate));
            $empLateInQry = "SELECT dep.name as department,  count(case when late_in BETWEEN '0:05' AND '0:15' THEN 1 END) as 15mns, count(case when late_in BETWEEN '0:16' AND '0:30' THEN 1 END) as 30mns,
                          count(case when late_in BETWEEN '0:31' AND '1:00' THEN 1 END) as 1hr,
                          count(case when late_in BETWEEN '1:01' AND '2:00' THEN 1 END) as 2hrs

                        FROM `erp_attendance_total` as att
                        LEFT join hs_hr_employee as emp on emp.emp_number=att.emp_number
                        LEFT join erp_subunit as dep on dep.id=emp.work_station
                         where att.p_date='$date'";
            
          
          if($plantId != 0 && $departmentId==0){
              $empLateInQry .= " AND emp.plant_id= '$plantId' GROUP  by dep.id order by count(late_in) DESC";
            }else if($departmentId){
              $empLateInQry .= " AND dep.id='$departmentId' and emp.plant_id='$plantId' GROUP  by dep.id order by count(late_in) DESC";
               
            }

        $data=array();  			
		$empLateInResult = mysqli_query($this->conn, $empLateInQry);
		if(mysqli_num_rows($empLateInResult) > 0)
		{
				$empLateInCnt=mysqli_fetch_assoc($empLateInResult);
			do{ 

						$data['department']=$empLateInCnt['department'];
						$data['15mns']=$empLateInCnt['15mns'];
						$data['30mns']=$empLateInCnt['30mns'];
						$data['1hr']=$empLateInCnt['1hr'];
						$data['2hrs']=$empLateInCnt['2hrs'];
					
						$data1[] = $data;
					}while($empLateInCnt = mysqli_fetch_assoc($empLateInResult)); 				
						$data['status']=1;
						$data['empLateInList']=$data1;
							
		}else{
				$data['status']=0;
				$data['empLateInList']=array();
			}
		return $data; 
    }

     //logged in empolyees leave details
       function getMyLeaveDetails($empNumber,$year)
    {
          $frm = $year."-01-01";
          $to = $year."-12-31";
          $myLvQuery = "SELECT date_format(l.date,'%M') as lvMonth,SUM(CASE WHEN l.leave_type_id=12 THEN l.length_hours/9 END) as sickLeave, SUM(CASE WHEN l.leave_type_id=13 THEN l.length_hours/9 END) as annualLeave FROM `erp_leave` as l where l.date BETWEEN '$frm' AND '$to' AND l.emp_number='$empNumber' GROUP BY month(l.date) order by l.date ASC";

        $data=array();  			
		$myLeaveResult = mysqli_query($this->conn, $myLvQuery);
		if(mysqli_num_rows($myLeaveResult) > 0)
		{
				$myLeaveCnt=mysqli_fetch_assoc($myLeaveResult);
			do{ 

						$data['lvMonth']=$myLeaveCnt['lvMonth'];
						$data['sickLeave']=$myLeaveCnt['sickLeave'];
						$data['annualLeave']=$myLeaveCnt['annualLeave'];
					
						
					
						$data1[] = $data;
					}while($myLeaveCnt = mysqli_fetch_assoc($myLeaveResult)); 				
						$data['status']=1;
						$data['empLeaveList']=$data1;
							
		}else{
				$data['status']=0;
				$data['empLeaveList']=array();
			}
		return $data; 
    }

           //logged in empolyees Late by Month
       function getMyLateByMnth($empNumber,$year,$month,$atdType)
    {	 
    	 // $frm = $year."-".$month."-01";
        // $to = $year."-".$month."-31";
           $frm = "2022-06-01";
           $to = "2022-06-31";
              if($atdType == 1){
            $myLateQuery = "SELECT date(p_date) as pdate,late_in as lateIn FROM `erp_emp_attendance` WHERE emp_number='$empNumber' AND p_date BETWEEN '$frm' AND '$to' AND late_in >'0:00' group by p_date order by  p_date ASC";
        }else if($atdType == 2){
            $myLateQuery = "SELECT date(p_date) as pdate,early_out as lateIn FROM `erp_emp_attendance` WHERE emp_number='$empNumber' AND p_date BETWEEN '$frm' AND '$to' AND early_out >'0:00' group by p_date order by  p_date ASC";
        }

        $data=array();  			
		$myLateResult = mysqli_query($this->conn, $myLateQuery);
		if(mysqli_num_rows($myLateResult) > 0)
		{
				$myLateCnt=mysqli_fetch_assoc($myLateResult);
			do{ 		$time=$myLateCnt['lateIn'];
       					//$timesplit=explode(':',$time);
        				//$min=($timesplit[0]*60)+($timesplit[1])+($timesplit[2]>30?1:0);
						$pDate = date('d/m',strtotime($myLateCnt['pdate']));
						$data['pdate']=$pDate;
						$data['lateIn']=$time;
						
					
						
					
						$data1[] = $data;
					}while($myLateCnt = mysqli_fetch_assoc($myLateResult)); 				
						$data['status']=1;
						$data['myLateList']=$data1;
							
		}else{
				$data['status']=0;
				$data['myLateList']=array();
			}
		return $data; 
    }

            //Empolyees Salary by Month

       function getEmpSalByMnth($plantId,$departmentId,$year,$month)
    {	 
    	   $empsalQuery = "SELECT CASE WHEN sub.name is NULL THEN 'Other' else sub.name END as department,SUM(salary5)as pf,sum(p.gross_salary) as gross,sum(net_pay) as netsalary FROM `erp_payroll` as p LEFT join hs_hr_employee as emp ON emp.emp_number= p.emp_number LEFT JOIN erp_subunit as sub ON sub.id=emp.work_station
            where p.pay_year='$year' AND pay_month='$month'";
 //Termination Report
      if($plantId != 0 && $departmentId==0){
        $empsalQuery .= "AND emp.plant_id = '$plantId' ";
      }else if($departmentId){
        $empsalQuery .= "AND emp.plant_id = '$plantId' AND emp.work_station='$departmentId'";
      }
      $empsalQuery .= "GROUP by emp.work_station ORDER BY sub.name ASC";

        $data=array();
        $data1=array();
        $totalSal= array();  			
		$empsalResult = mysqli_query($this->conn, $empsalQuery);
		$pf=0;
        $gross=0;
        $net=0;
		if(mysqli_num_rows($empsalResult) > 0)
		{
				$empsal=mysqli_fetch_assoc($empsalResult);
			do{ 		
						$pf = $pf+$empsal['pf'];
						$gross = $gross+$empsal['gross'];
       					$net = $net+$empsal['netsalary'];
       					$data['department'] = $empsal['department'];
       					$data['pf'] = $empsal['pf'];
       					$data['gross'] = $empsal['gross'];
       					$data['netsalary'] = $empsal['netsalary'];

						$data1[] = $data;
					}while($empsal = mysqli_fetch_assoc($empsalResult)); 				
						$data['status']=1;
						$data['empSalList']=$data1;
						$data2['PFTotal']= $pf;
						$data2['grossTotal']= $gross;
						$data2['netTotal']= $net;
						$totalSal[] = $data2;
						$data['totalSal']=$totalSal;
							
		}else{
				$data['status']=0;
				$data['empSalList']=array();
			}
		return $data; 
    }

    /* ------------------------------ Start API's-----------------------*/

    function loginApi($mobnumber,$username,$password,$base_url)
    {
        $data=array();
  			
		$query = "SELECT * FROM `tbl_users` WHERE user_name = '".$username."' AND password = '".$password."'";
		$count=mysqli_query($this->conn, $query);

			if(mysqli_num_rows($count) > 0)
			{
				$row = mysqli_fetch_assoc($count);
					
				do { 						
				
					$data['userId'] = $row['id'];
					$data['full_name'] = $row['full_name'];
					$data['email'] = $row['email'];
					$data['phone_number'] = $row['phone_number'];
					$data['roleName'] = $row['role_name'];
					$data['roleId'] = $row['role_id'];
					
					if($row['profile'] != ''){
						$data['image'] = $row['profile'];
					}else{
						$data['image'] = 'http://portal.prospectatech.com/POSH/v1/default-photo.png';
					}
					
					
					$data1[] = $data;
				}while($row = mysqli_fetch_assoc($count));
					$data['status'] = 1;
					$data['userDetails']=$data1;
						
			}else{
					$data['status'] = 0;
					$data['userDetails']=array();
			}

		return $data;
    }

    function getAllUsersApi($userId) {
    	$data = array();

    	$query = "SELECT * FROM tbl_users ORDER By id DESC";

    	$sql = mysqli_query($this->conn, $query);

    	if(mysqli_num_rows($sql) > 0){

    		$row = mysqli_fetch_assoc($sql);

    		do {

    			$data['userId'] = $row['id'];
    			$data['full_name'] = $row['full_name'];
    			$data['email'] = $row['email'];
    			$data['profile'] = $row['profile'];
    			$data['userPosts'] = $this->getUserPostsByIds($userId,$row['id']);
    			$data['singleUserPosts'] = $this->getSingleUserPosts($row['id'],$userId);
    			$data1[] = $data; 

    		}while($row = mysqli_fetch_assoc($sql));
    			$data['status'] = 1;
    			$data['AllUsers'] = $data1;


    	}else{
    		$data['status'] = 0;
			$data['AllUsers']=array();
    	}

    	return $data;

    }

    function getSingleUserPosts($senderId,$loginId){
    	$data = array();

    	$query = "SELECT * FROM `tbl_posts` WHERE sender_id = '".$senderId."' AND receiver_id = '".$loginId."' AND status = 0";

    	$sql = mysqli_query($this->conn, $query);


    	if(mysqli_num_rows($sql) > 0){
    		$data['postsCount'] = mysqli_num_rows($sql);

    	}else{
			$data['postsCount']=0;
    	}

    	return $data;
    }

    function SendMediaData($sender_id,$receiver_id,$message,$send_by,$image,$tempPath){

    	$data = array();

    	$send_date = date('Y-m-d');
    	$send_time = date('H:i');
    	$fileName = $_FILES['image']['name'];

    	if($fileName != ''){
    		$file_content = file_get_contents($tempPath);
    		// $image_src = $base_url.'/images/';
    		$upload_path = $_SERVER["DOCUMENT_ROOT"].'/POSH/v1/'.$fileName;
    		$file_content = file_get_contents($_FILES['image']['tmp_name']);
    		// $tempPath = $_FILES['image']['tmp_name'];

    		// echo $upload_path;

        	// $target_dir = $image_src;
        	// $target_file = $target_dir . $fileName;
        	move_uploaded_file($tempPath,$upload_path);
    	}

    	if($message != '' || $fileName != ''){
	    		$sql = "INSERT INTO tbl_posts(sender_id,receiver_id,message,send_date,send_time,send_by,attachment_name) VALUES(?,?,?,?,?,?,?)";
	    	if($stmt = mysqli_prepare($this->conn, $sql)){

	    		mysqli_stmt_bind_param($stmt,"iisssis" ,$sender_id,$receiver_id,$message,$send_date,$send_time,$send_by,$fileName);

	    		if (mysqli_stmt_execute($stmt)) {
	    			$data['message'] = "Successfully saved";
	    			$data['status'] = 1;
	    		}else{
	    			$data['message'] = "failed";
	    			$data['status'] = 0;
	    		}
	    	}else{
	    		$data['message'] = "fail";
	    		$data['status'] = 0;
	    	}
    	}else{
    		$data['message'] = "fail";
	    	$data['status'] = 0;
    	}

    	

    	return $data;

    }

    function SendPosts($sender_id,$receiver_id,$message,$send_by){

    	$data = array();

    	$send_date = date('Y-m-d');
    	$send_time = date('H:i');
    	$fileName = "";

    	if($message != ''){
	    		$sql = "INSERT INTO tbl_posts(sender_id,receiver_id,message,send_date,send_time,send_by,attachment_name) VALUES(?,?,?,?,?,?,?)";
	    	if($stmt = mysqli_prepare($this->conn, $sql)){

	    		mysqli_stmt_bind_param($stmt,"iisssis" ,$sender_id,$receiver_id,$message,$send_date,$send_time,$send_by,$fileName);

	    		if (mysqli_stmt_execute($stmt)) {
	    			$data['message'] = "Successfully saved";
	    			$data['status'] = 1;
	    		}else{
	    			$data['message'] = "failed";
	    			$data['status'] = 0;
	    		}
	    	}else{
	    		$data['message'] = "fail";
	    		$data['status'] = 0;
	    	}
    	}else{
    		$data['message'] = "fail";
	    	$data['status'] = 0;
    	}

    	

    	return $data;

    }

    function UpdatePostStatus($senderId,$loginUserId){

    	$data = array();

    	$updatesql = "UPDATE tbl_posts SET status = '1' WHERE sender_id = '".$senderId."' AND receiver_id = '".$loginUserId."' AND status = '0'";
					if($result2 = mysqli_query($this->conn, $updatesql)){
						$data['message'] = "Successfully updated";
	    				$data['status'] = 1;
					}else{
						$data['message'] = "failed";
					    $data['status']=0;
					}
    	

    	return $data;

    }

    function getUserPosts($sender_id, $receiver_id){
    	$data = array();

    	$query = "SELECT * FROM tbl_posts WHERE (sender_id = '".$sender_id."' AND receiver_id = '".$receiver_id."') OR (receiver_id = '".$sender_id."' AND sender_id = '".$receiver_id."')";

    	$sql = mysqli_query($this->conn, $query);


    	if(mysqli_num_rows($sql) > 0){

    		$row = mysqli_fetch_assoc($sql);

    		do {

    			$data['sender_id'] = $row['sender_id'];
    			$data['receiver_id'] = $row['receiver_id'];
    			$data['message'] = $row['message'];
    			$data['send_date'] = $row['send_date'];
    			$data['attachment_name'] = $row['attachment_name'];
    			$data['send_time'] = date('H:i',strtotime($row['send_time']));
    			$data['hours'] = date('H',strtotime($row['send_time']));
    			$data1[] = $data;

    		}while($row = mysqli_fetch_assoc($sql));
    			$data['status'] = 1;
    			$data['posts'] = $data1;


    	}else{
    		$data['status'] = 0;
			$data['posts']=array();
    	}
    			
   //  	}else{
   //  		$data['sender_id'] = "";
			// $data['receiver_id'] = "";
			// $data['message'] = "No Msg";
			// $data['send_date'] = date('Y-m-d');
			// $data['send_time'] = date('H:i');
			// $data['attachment_name'] = "";
			// $data['hours'] = date('H');
   //  	}

    	return $data;
    }

    function getUserPostsByIds($sender_id, $receiver_id){
    	$data = array();

    	$query = "SELECT * FROM tbl_posts WHERE (sender_id = '".$sender_id."' AND receiver_id = '".$receiver_id."') OR (receiver_id = '".$sender_id."' AND sender_id = '".$receiver_id."') ORDER BY id DESC LIMIT 1";

    	$sql = mysqli_query($this->conn, $query);


    	if(mysqli_num_rows($sql) > 0){

    		$row = mysqli_fetch_assoc($sql);

    		do {

    			$data['sender_id'] = $row['sender_id'];
    			$data['receiver_id'] = $row['receiver_id'];
    			$data['message'] = $row['message'];
    			$data['send_date'] = $row['send_date'];
    			$data['attachment_name'] = $row['attachment_name'];
    			$data['send_time'] = date('H:i',strtotime($row['send_time']));
    			$data['hours'] = date('H',strtotime($row['send_time']));

    		}while($row = mysqli_fetch_assoc($sql));
    			
    	}else{
    		$data['sender_id'] = "";
			$data['receiver_id'] = "";
			$data['message'] = "No Msg";
			$data['send_date'] = date('Y-m-d');
			$data['send_time'] = date('H:i');
			$data['attachment_name'] = "";
			$data['hours'] = date('H');
    	}

    	return $data;
    }

    function getUserReceivedPosts($loginUserId){
    	$data = array();

    	$query = "SELECT * FROM `tbl_posts` WHERE receiver_id = '".$loginUserId."' AND status = 0 GROUP BY sender_id";

    	$sql = mysqli_query($this->conn, $query);



    	if(mysqli_num_rows($sql) > 0){

    		$data['status'] = 1;
    		$data['AllUsersPosts'] = mysqli_num_rows($sql);


    	}else{
    		$data['status'] = 0;
			$data['AllUsersPosts'] = 0;
    	}

    	return $data;
    }

    function SaveUserData($user_id,$full_name,$email,$phone_number,$user_name,$password,$role_id,$role_name,$image,$tempPath){

    	$data = array();

    	$send_date = date('Y-m-d');
    	$send_time = date('H:i');
    	$fileName = $_FILES['profile']['name'];

    	if($fileName != ''){

    		$upload_path = $_SERVER["DOCUMENT_ROOT"].'/POSH/v1/'.$fileName;

        	move_uploaded_file($tempPath,$upload_path);
    	}

    	if($user_id == 0){
	    		$sql = "INSERT INTO tbl_users(full_name,email,phone_number,user_name,password,profile,role_name,role_id) VALUES(?,?,?,?,?,?,?,?)";
	    	if($stmt = mysqli_prepare($this->conn, $sql)){

	    		mysqli_stmt_bind_param($stmt,"ssissssi" ,$full_name,$email,$phone_number,$user_name,$password,$fileName,$role_name,$role_id);

	    		if (mysqli_stmt_execute($stmt)) {
	    			$data['message'] = "Successfully saved";
	    			$data['status'] = 1;
	    		}else{
	    			$data['message'] = "failed";
	    			$data['status'] = 0;
	    		}
	    	}else{
	    		$data['message'] = "fail";
	    		$data['status'] = 0;
	    	}
    	}else{
    		$updatesql = "UPDATE tbl_users SET full_name = '".$full_name."', email = '".$email."', phone_number = '".$phone_number."', user_name = '".$user_name."', password = '".$password."', profile = '".$fileName."', role_name = '".$role_name."', role_id = '".$role_id."'  WHERE id = '".$user_id."'";
					if($result2 = mysqli_query($this->conn, $updatesql)){
						$data['message'] = "Successfully updated";
	    				$data['status'] = 1;
					}else{
						$data['message'] = "failed";
					    $data['status']=0;
					}
    	}

    	

    	return $data;

    }

    function getUserDataByUserId($user_id) {

    	$data = array();

    	$query = "SELECT * FROM tbl_users WHERE id = '".$user_id."' ";

    	$sql = mysqli_query($this->conn, $query);


    	if(mysqli_num_rows($sql) > 0){

    		$row = mysqli_fetch_assoc($sql);

    		do {

    			$data['id'] = $row['id'];
    			$data['full_name'] = $row['full_name'];
    			$data['email'] = $row['email'];
    			$data['phone_number'] = $row['phone_number'];
    			$data['user_name'] = $row['user_name'];
    			$data['password'] = $row['password'];
    			$data['profile'] = $row['profile'];
    			$data['role_name'] = $row['role_name'];
    			$data['role_id'] = $row['role_id'];
    			
    			$data1[] = $data; 

    		}while($row = mysqli_fetch_assoc($sql));
    			$data['status'] = 1;
    			$data['userData'] = $data1;
    	}else{
    		$data['status'] = 0;
    		$data['userData'] = array();
    	}

    	return $data;

    }

    function getListItems($user_id) {

    	$data = array();

    	$query = "SELECT * FROM tbl_items";

    	$sql = mysqli_query($this->conn, $query);


    	if(mysqli_num_rows($sql) > 0){

    		$row = mysqli_fetch_assoc($sql);

    		do {

    			$data['id'] = $row['id'];
    			$data['item_name'] = $row['item_name'];
    			$data['image'] = $row['image'];
    			
    			$data1[] = $data; 

    		}while($row = mysqli_fetch_assoc($sql));
    			$data['status'] = 1;
    			$data['ListItems'] = $data1;
    	}else{
    		$data['status'] = 0;
    		$data['ListItems'] = array();
    	}

    	return $data;

    }

    function getSubListItems($user_id,$item_id) {

    	$data = array();

    	$query = "SELECT * FROM tbl_sub_items WHERE item_id = '".$item_id."'";

    	$sql = mysqli_query($this->conn, $query);


    	if(mysqli_num_rows($sql) > 0){

    		$row = mysqli_fetch_assoc($sql);

    		do {

    			$data['id'] = $row['id'];
    			$data['sub_item_name'] = $row['sub_item_name'];
    			$data['item_id'] = $row['item_id'];
    			$data['item_price'] = $row['item_price'];
    			$data['image'] = $row['image'];
    			
    			$data1[] = $data; 

    		}while($row = mysqli_fetch_assoc($sql));
    			$data['status'] = 1;
    			$data['SubListItems'] = $data1;
    	}else{
    		$data['status'] = 0;
    		$data['SubListItems'] = array();
    	}

    	return $data;

    }

    function getSubItemDetails($id) {

    	$data = array();

    	$query = "SELECT si.*,i.item_name FROM tbl_sub_items si LEFT JOIN tbl_items i ON si.item_id = i.id WHERE si.id = '".$id."'";

    	$sql = mysqli_query($this->conn, $query);


    	if(mysqli_num_rows($sql) > 0){

    		$row = mysqli_fetch_assoc($sql);

    		do {

    			$data['id'] = $row['id'];
    			$data['sub_item_name'] = $row['sub_item_name'];
    			$data['item_name'] = $row['item_name'];
    			$data['item_id'] = $row['item_id'];
    			$data['item_price'] = $row['item_price'];
    			$data['image'] = $row['image'];
    			
    			$data1[] = $data; 

    		}while($row = mysqli_fetch_assoc($sql));
    			$data['status'] = 1;
    			$data['SubItemDetails'] = $data1;
    	}else{
    		$data['status'] = 0;
    		$data['SubItemDetails'] = array();
    	}

    	return $data;

    }

    function GetProjectList() {
    	$data = array();
    	$query = "SELECT * FROM erp_project WHERE is_deleted = 0";
    	$sql = mysqli_query($this->conn, $query);
    	if(mysqli_num_rows($sql) > 0){

    		$row = mysqli_fetch_assoc($sql);

    		do {

    			$data['projectId'] = $row['project_id'];
    			$data['projectName'] = $row['name'];
    			$data1[] = $data;

    		}while($row = mysqli_fetch_assoc($sql));
    			$data['status'] = 1;
    			$data['ProjectDetails'] = $data1;


    	}else{
    		$data['status'] = 0;
    		$data['ProjectDetails'] = array();
    	}

    	return $data;
    }

    function GetProjectActivitiesByProjectId($projectId) {
    	$data = array();
    	$query = "SELECT * FROM erp_project_activity WHERE project_id = $projectId";
    	$sql = mysqli_query($this->conn,$query);

    	if(mysqli_num_rows($sql) > 0){

    		$row = mysqli_fetch_assoc($sql);
    		
    		do {
    			$data['projectId'] = $row['project_id'];
    			$data['activity'] = $row['activity_id'];
    			$data['activityName'] = $row['name'];
    			$date1[] = $data; 
    		} while($row = mysqli_fetch_assoc($sql));
    			$data['status'] = 1;
    			$data['ProjectActivities'] = $data1;
    	}else{
    		$data['status'] = 0;
    		$data['ProjectActivities'] = array();
    	}

    	return $data;
    }

    function getProjectIdByName($name) {
    	$data = array();
    	$query = "SELECT * FROM erp_project WHERE name = '".$name."'";
    	$sql = mysqli_query($this->conn,$query);

    	if(mysqli_num_rows($sql) > 0){

    		$row = mysqli_fetch_assoc($sql);
    		
    		do {
    			$data['projectId'] = $row['project_id'];    			
    			$data1[] = $data; 

    		} while($row = mysqli_fetch_assoc($sql));
    			$data['status'] = 1;
    			$data['ProjectNames'] = $data1;
    	}else{
    		$data['status'] = 0;
    		$data['ProjectNames'] = array();
    	}

    	return $data;
    }

    function GetProjectActivities($id) {
    	$data = array();
    	$query = "SELECT * FROM erp_project_activity WHERE project_id = '".$id."'";
    	$sql = mysqli_query($this->conn,$query);

    	if(mysqli_num_rows($sql) > 0){

    		$row = mysqli_fetch_assoc($sql);
    		
    		do {
    			$data['activity_id'] = $row['activity_id'];    			
    			$data['name'] = $row['name'];    			
    			$data1[] = $data; 

    		} while($row = mysqli_fetch_assoc($sql));
    			$data['status'] = 1;
    			$data['ProjectActivities'] = $data1;
    	}else{
    		$data['status'] = 0;
    		$data['ProjectActivities'] = array();
    	}

    	return $data;
    }

    function GetPostDetailsById($id) {
    	$data = array();

    	$sql = "SELECT * FROM tbl_posts WHERE id = '".$id."'"; 	
    }

    function SaveTaskData($data) {
    	$dataArr = array();

    	$projectId  = $data['projectId'];
        $activityId  = $data['activityId'];
        $taskName  = $data['taskName'];
        $assignedTo  = $data['assignedTo'];
        $assignedUserId  = $data['assignedUserId'];
        $fromDate  = $data['fromDate'];
        $durationTime  = $data['durationTime'];
        $endDate  = $data['endDate'];
        $submittedBy  = $data['submittedBy'];
        $taskStatus  = $data['taskStatus'];
        $submittedOn = date("Y-m-d H:i:s");

        $sql = "INSERT INTO tbl_tasks(project_id,activity_id,task_name,assignedTo,assignedUserId,fromDate,durationTime,endDate,submittedBy,taskStatus,submittedOn) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
        if($stmt = mysqli_prepare($this->conn,$sql)){

        	mysqli_stmt_bind_param($stmt, "iisiisisiis", $projectId,$activityId,$taskName,$assignedTo,$assignedUserId,$fromDate,$durationTime,$endDate,$submittedBy,$taskStatus,$submittedOn);

        	if(mysqli_execute($stmt)){
        		$dataArr['message'] = "Saved successfully";
        		$dataArr['status'] = 1;
        	}else{
        		$dataArr['message'] = "failed";
        		$dataArr['status'] = 0;
        	}

        }else{
        	$dataArr['message'] = "failed";
        	$dataArr['status'] = 0;
        }

        return $dataArr;
    }

    function GetTasks($user_id) {
    	$data = array();

    	$query = "SELECT t.*,p.name as project,pa.name as activity FROM tbl_tasks as t 
				LEFT JOIN erp_project as p ON t.project_id = p.project_id
				LEFT JOIN erp_project_activity pa ON t.activity_id = pa.activity_id
				WHERE t.assignedUserId = '".$user_id."'";
		$sql = mysqli_query($this->conn,$query);

		if(mysqli_num_rows($sql) > 0){

			$row = mysqli_fetch_assoc($sql);

			do {

				$data['project'] = $row['project'];
				$data['activity'] = $row['activity'];
				$data['task'] = $row['task_name'];
				$data['fromDate'] = $row['fromDate'];
				$data['duration'] = $row['durationTime'];
				$data['endDate'] = $row['endDate'];
				$data['statusId'] = $row['taskStatus'];
				if($row['taskStatus'] == 1){
					$data['status'] = "New";
				}else if($row['taskStatus'] == 2){
					$data['status'] = "In-progress";
				}else{
					$data['status'] = "Completed";
				}

				$data1[] = $data;				

			}while($row = mysqli_fetch_assoc($sql));
				$data['status'] = 1;
    			$data['Task'] = $data1;

		}else{
			$data['status'] = 0;
    		$data['Task'] = array();
		}

		return $data;
    }

    function GetUserPostsByUserId($user_id) {

    	$data = array();

    	$query = "SELECT up.*,u.user_name FROM tbl_user_posts up LEFT JOIN erp_user u ON up.user_id = u.id 
    			WHERE up.status = 0 ";

    	if(!empty($user_id)){
    		$query .= "AND up.user_id = '".$user_id."'";
    	}

    	$query .= " ORDER BY up.id DESC";

		$sql = mysqli_query($this->conn,$query);

		if(mysqli_num_rows($sql) > 0){

			$row = mysqli_fetch_assoc($sql);

			do {

				$data['id'] = $row['id'];
    			$data['user_id'] = $row['user_id'];
    			$data['title'] = $row['title'];
    			$data['body'] = $row['body'];
    			$data['user_name'] = $row['user_name'];

				$data1[] = $data;				

			}while($row = mysqli_fetch_assoc($sql));
				$data['status'] = 1;
    			$data['UserPosts'] = $data1;

		}else{
			$data['status'] = 0;
    		$data['UserPosts'] = array();
		}

		return $data;
    }

    function GetAutoCompleteUserPosts($user_id) {

    	$data = array();

    	$query = "SELECT up.*,u.user_name FROM tbl_user_posts up LEFT JOIN erp_user u ON up.user_id = u.id WHERE up.status = 0 group by up.user_id ORDER BY up.id DESC";

		$sql = mysqli_query($this->conn,$query);

		if(mysqli_num_rows($sql) > 0){

			$row = mysqli_fetch_assoc($sql);

			do {

				$data['id'] = $row['id'];
    			$data['user_id'] = $row['user_id'];
    			$data['title'] = $row['title'];
    			$data['body'] = $row['body'];
    			$data['user_name'] = $row['user_name'];

				$data1[] = $data;				

			}while($row = mysqli_fetch_assoc($sql));
				$data['status'] = 1;
    			$data['UserPosts'] = $data1;

		}else{
			$data['status'] = 0;
    		$data['UserPosts'] = array();
		}

		return $data;
    }

    function DeleteUserPostsById($id) {
    	$data = array();

    	$updateQuery = "UPDATE tbl_user_posts SET status = 1 WHERE id = '".$id."'";
    	$updateSql = mysqli_query($this->conn, $updateQuery);

    	
    	if($updateSql){

    		$query = "SELECT * FROM tbl_user_posts WHERE status = 0";

			$sql = mysqli_query($this->conn,$query);

			if(mysqli_num_rows($sql) > 0){

				$row = mysqli_fetch_assoc($sql);

				do {

					$data['id'] = $row['id'];
	    			$data['user_id'] = $row['user_id'];
	    			$data['title'] = $row['title'];
	    			$data['body'] = $row['body'];

					$data1[] = $data;				

				}while($row = mysqli_fetch_assoc($sql));
					$data['status'] = 1;
					$data['message'] = "Deleted Successfully";
	    			$data['UserPosts'] = $data1;
	    	} else{
	    		$data['status'] = 0;
				$data['message'] = "Failed";
	    		$data['UserPosts'] = array();
	    	}
    		
    	} else{
    		$data['status'] = 0;
			$data['message'] = "Failed";
	    	$data['UserPosts'] = array();
    	}

    	return $data;


    }

    function saveUserPosts($loginUserId,$title,$body) {

    	$data = array();

    	$query = "INSERT INTO tbl_user_posts(user_id,title,body) VALUES(?,?,?)";

    	if($stmt = mysqli_prepare($this->conn, $query)){

    		mysqli_stmt_bind_param($stmt, "sss", $loginUserId,$title,$body);
    		
    		if(mysqli_stmt_execute($stmt)){

    			$data['status'] = 1;
    			$data['message'] = "Saved Successfully";
    		}else{
    			$data['status'] = 0;
    			$data['message'] = "Failed";
    		}
    	}else{
    		$data['status'] = 0;
    		$data['message'] = "Failed";
    	}

    	return $data;

    }

    function getUserPostsById($id) {

    	$data = array();

    	$query = "SELECT * FROM tbl_user_posts WHERE id = '".$id."'";

		$sql = mysqli_query($this->conn,$query);

		if(mysqli_num_rows($sql) > 0){

			$row = mysqli_fetch_assoc($sql);

			do {

				$data['id'] = $row['id'];
    			$data['user_id'] = $row['user_id'];
    			$data['title'] = $row['title'];
    			$data['body'] = $row['body'];

				$data1[] = $data;				

			}while($row = mysqli_fetch_assoc($sql));
				$data['status'] = 1;
    			$data['posts'] = $data1;

		}else{
			$data['status'] = 0;
    		$data['posts'] = array();
		}

		return $data;
    }

    function updateUserPost($id, $title, $body) {

    	$data = array();

    	$updateQuery = "UPDATE tbl_user_posts SET title = '".$title."', body = '".$body."' WHERE id = '".$id."'";
    	$result = mysqli_query($this->conn, $updateQuery);

    	if($result){
    		$data['status'] = 1;
    		$data['message'] = "Updated Successfully";
    	}else{
    		$data['status'] = 0;
    		$data['message'] = "Failed";
    	}

    	return $data;
    }

    function getUserRoles() {
    	$data = array();

    	$query = "SELECT * FROM erp_user_role WHERE is_assignable = 1";
    	$sql = mysqli_query($this->conn, $query);

    	if(mysqli_num_rows($sql) > 0) {

    		$row = mysqli_fetch_assoc($sql);

    		do {

    			$data['id'] = $row['id'];
    			$data['name'] = $row['name'];
    			$data1[] = $data;
    		}while($row = mysqli_fetch_assoc($sql));
    			$data['status'] = 1;
    			$data['roles'] = $data1;

    	}else{
    			$data['status'] = 0;
    			$data['roles'] = "No data found";
    	}

    	return $data;
    }

    function saveUserSignUp($res) {

    	$data = array();

    	$query = "INSERT INTO hs_hr_employee(emp_lastname,emp_firstname,emp_middle_name,emp_birthday,emp_gender,emp_street1,emp_work_telephone,emp_work_email,skills) VALUES(?,?,?,?,?,?,?,?,?) ";
    	if($stmt = mysqli_prepare($this->conn, $query)){

    		mysqli_stmt_bind_param($stmt, "ssssisiss", $res['lname'],$res['fName'],$res['mname'],$res['dob'],$res['gender'],$res['address'],$res['mobileNo'],$res['email'],$res['skills']);

    		if(mysqli_stmt_execute($stmt)){

    			$hashPassword = password_hash($res['password'], PASSWORD_DEFAULT);

    			$last_emp_num = $this->conn->insert_id;

    			$userQuery = "INSERT INTO erp_user(user_role_id,emp_number,user_name,user_password) VALUES(?,?,?,?)";

    			if($stmt_user = mysqli_prepare($this->conn, $userQuery)) {

    				mysqli_stmt_bind_param($stmt_user, "iiss", $res['role_id'],$last_emp_num,$res['userName'],$hashPassword);
    				if(mysqli_stmt_execute($stmt_user)){
    					$data['status'] = 1;
    					$data['message'] = "Saved successfully";
    				}else{
    					$data['status'] = 0;
    					$data['message'] = "user failed";
    				}
    			}else{

    			}

    		}else{
    			$data['status'] = 0;
    			$data['message'] = "employee failed";
    		}
    	}else{
    		$data['status'] = 0;
    		$data['message'] = "overall failed";
    	}

    	return $data;
    }

    function getUsersList() {

    	$data = array();

    	$query = "SELECT u.id,u.user_role_id,u.emp_number,u.user_name,concat(e.emp_firstname,' ',e.emp_lastname) as full_name,e.emp_work_email as email,e.emp_work_telephone as mobile_no,ur.name as role FROM erp_user u 
				LEFT JOIN hs_hr_employee e ON u.emp_number = e.emp_number
				LEFT JOIN erp_user_role ur ON ur.id = u.user_role_id ORDER BY u.id DESC";

		$sql = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($sql) > 0) {

			$row = mysqli_fetch_assoc($sql);

			do {

				$data['id'] = $row['id'];
				$data['user_role_id'] = $row['user_role_id'];
				$data['emp_number'] = $row['emp_number'];
				$data['user_name'] = $row['user_name'];
				$data['full_name'] = $row['full_name'];
				$data['role'] = $row['role'];
				$data['email'] = $row['email'];
				$data['mobile_no'] = $row['mobile_no'];
				$data1[] = $data;

			}while($row = mysqli_fetch_assoc($sql));
				$data['status'] = 1;
				$data['users'] = $data1;

		}else{

			$data['status'] = 0;
			$data['users'] = array();

		}

		return $data;
    }

    function getChatMenus() {

    	$data = array();

    	$query = "SELECT * FROM tbl_chat_menus";

		$sql = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($sql) > 0) {

			$row = mysqli_fetch_assoc($sql);

			do {

				$data['id'] = $row['id'];
				$data['name'] = $row['name'];
				$data['sub_menus'] = $this->getSubMenusById($row['id']);
				$data1[] = $data;


			}while($row = mysqli_fetch_assoc($sql));
				$data['status'] = 1;
				$data['menus'] = $data1;						
				

		}else{

			$data['status'] = 0;
			$data['menus'] = array();

		}

		return $data;
    }

    function getSubMenusById($id) {

    	$data1 = array();

    	$query = "SELECT s.chat_type_id,s.name,s.id,u.id as userId,u.user_name FROM tbl_chat_sub_menus s LEFT JOIN erp_user u ON s.user_id = u.id WHERE chat_type_id = $id";

		$sql = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($sql) > 0) {

			while ($row = mysqli_fetch_assoc($sql)) {

				$data['menu_id'] = $row['chat_type_id'];
				$data['sub_menu_id'] = $row['id'];
				$data['user_id'] = $row['userId'];

				if($row['chat_type_id'] == 1){
					$data['sub_menu'] = $row['name'];
				}else{
					$data['sub_menu'] = $row['user_name'];
				}

				$data1[] = $data;
			}

		}

		return $data1;
    }

	/* ------------------------------ END API's-----------------------*/

}

?>



