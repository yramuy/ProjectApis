<?php

header('content-type:text/html;charset=utf-8');
require_once '../include/DbHandler.php';
require_once '../include/EmailService.php';
require_once '../include/SmsService.php';
require '.././libs/Slim/Slim.php';

header("Content-Type: application/json");
header("Acess-Control-Allow-Origin: *");
header("Acess-Control-Allow-Methods: POST");
header("Acess-Control-Allow-Headers: Acess-Control-Allow-Headers,Content-Type,Acess-Control-Allow-Methods, Authorization");
header('Access-Control-Allow-Credentials', 'true');

// \Stripe\Stripe::setApiKey($stripe['secret_key']);

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// User id from db - Global Variable
$user_id = NULL;
$session_token= NULL;
/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'Authorization' header
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
    // Verifying Authorization Header
    if (isset($headers['Authorization'])) 
    {
        $db = new DbHandler();
        // get the api key
        $api_key = $headers['Authorization'];
        // validating api key
       
    if (!$db->isValidApiKey($api_key)) 
    {
            $response["status"] ="error";
            $response["message"] = "Access Denied";
            //$response["message"] = "Access Denied. Invalid Api key";
            echoRespnse(401, $response);
            $app->stop();
        } 
        else 
        {
            global $user_id;
            //get user primary key id
           $user_id = $db->getUserId($api_key);
        
        }
    } 
    else 
    {
        // api key is missing in header
        $response["status"] ="error";
        //$response["message"] = "Api key is misssing";
        $response["message"] = "Access Denied";
        echoRespnse(401, $response);
        $app->stop();
    }
}
// <----Webservice for mobile app pavan start-->
$app->post('/empLeavebyDate', 'authenticatedefault', function() use ($app) 
{         
      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);

      $userId  = $data['user_id'];
      // $plantId  = $data['plantId'];
      $date  = $data['date'];

  $response = array();
  $db = new DbHandler();

  $result=$db->getEmpLeaveDetails($userId,$date);
 if ($result['status']==1) 
 {
       $response["status"] =1;
       $response['message'] = "success";
       $response['empLeaveCount'] = $result['empLeaveCount'];
       $response['empLeaveList'] = $result['empLeaveList'];
  }
  else
  {
      $response['status'] =0;
      $response['message'] = 'unsuccessfull';
      $response['empLeaveList'] = array();
  }

  echoRespnse(200, $response);
 });

$app->post('/empPunchIn', 'authenticatedefault', function() use ($app) 
{         
      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);

      $userId  = $data['user_id'];
      // $departmentId  = $data['departmentId'];

  $response = array();
  $db = new DbHandler();

  $result=$db->getEmpPunchIn($userId);
 if ($result['status']==1) 
 {
       $response["status"] =1;
       $response['message'] = "success";
       $response['empPunchInCount'] = $result['empPunchInCount'];
       $response['empPunchInList'] = $result['empPunchInList'];
  }
  else
  {
      $response['status'] =0;
      $response['message'] = 'unsuccessfull';
      $response['empPunchInList'] = array();
  }

  echoRespnse(200, $response);
 });





$app->post('/employeesPunchInCount', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
     
      $response = array();
      $db = new DbHandler();

      $result=$db->employeesPunchInCount();
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response["employees_punchin_count"]=$result['employeesPunchinCount'];
           $response["employees_punchin_list"]=$result['employeesPunchinList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response["employees_punchin_count"]=array();
          $response["employees_punchin_list"]=array();
      }

      echoRespnse(200, $response);
 });

$app->post('/visitorInPlantCountAndList', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);

      $result = implode(',',$data);
      $user_id  = $data['user_id'];

      // echo $user_id;die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getVisitorInPlantCountAndList($user_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['visitorsCount'] = $result['visitorsCount'];
           $response['visitorsCountDepartment'] = $result['visitorsCountDepartment'];
           $response['visitor_personslist'] = $result['visitor_personslist'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'No Data Found';
          $response['visitorsCount'] = $result['visitorsCount'];
          $response['visitorsCountDepartment'] = $result['visitorsCountDepartment'];
          $response['visitor_personslist'] = $result['visitor_personslist'];
      }

      echoRespnse(200, $response);
 });

 $app->post('/membersInPlantCountAndList', 'authenticatedefault', function() use ($app) 
 {         

      $json = $app->request->getBody();
      $data = json_decode($json, true);

      $result = implode(',',$data);
      $user_id  = $data['user_id'];

      // echo $user_id;die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getMembersInPlantCountAndList($user_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['membersCount'] = $result['membersCount'];
           $response['departCount'] = $result['departCount'];
           $response['members_personslist'] = $result['members_personslist'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response['membersCount'] = $result['membersCount'];
          $response['departCount'] = $result['departCount'];
          $response['members_personslist'] = $result['members_personslist'];
      }

      echoRespnse(200, $response);
 });

 $app->post('/viewManpowerRequisitionList', 'authenticatedefault', function() use ($app) 
 {         

      $json = $app->request->getBody();
      $data = json_decode($json, true);

      $result = implode(',',$data);
      $user_id  = $data['user_id'];

      // echo $user_id;die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getViewManpowerRequisitionList($user_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['RequisitionList'] = $result['RequisitionList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'Data Not Found';
          $response['RequisitionList'] = $result['RequisitionList'];
      }

      echoRespnse(200, $response);
 });

 $app->post('/appMenuItemsList', 'authenticatedefault', function() use ($app) 
 {         

      $json = $app->request->getBody();
      $data = json_decode($json, true);

      $result = implode(',',$data);
      $user_id  = $data['user_id'];

      // echo $user_id;die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getAppMenuItemsList($user_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['menu_list'] = $result['menu_list'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = "Data Not Found";
          $response['menu_list'] = $result['menu_list'];
      }

      echoRespnse(200, $response);
 });

 $app->post('/inductionQuestionsList', 'authenticatedefault', function() use ($app) 
 {         

      $json = $app->request->getBody();
      $data = json_decode($json, true);

      $result = implode(',',$data);
      $user_id  = $data['user_id'];

      // echo $user_id;die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getInductionQuestionsList($user_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['induction_list'] = $result['induction_list'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = "Data Not Found";
          $response['induction_list'] = $result['induction_list'];
      }

      echoRespnse(200, $response);
 });

 $app->post('/inductionEmpStatusUpdate', 'authenticatedefault', function() use ($app) 
 {         

      $json = $app->request->getBody();
      $data = json_decode($json, true);

      $result = implode(',',$data);
      $user_id  = $data['user_id'];
      $induction_qns_id  = $data['induction_qns_id'];
      $induction_emp_status  = $data['induction_emp_status'];

      // echo $user_id;die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->inductionEmpStatusUpdate($user_id,$induction_qns_id,$induction_emp_status);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
      }
      else
      {
          $response['status'] =0;
          $response['message'] = "Data Not Found";
      }

      echoRespnse(200, $response);
 });

 $app->post('/employeePayrolList', 'authenticatedefault', function() use ($app) 
 {         

      $json = $app->request->getBody();
      $data = json_decode($json, true);

      $result = implode(',',$data);
      $user_id  = $data['user_id'];

      // echo $user_id;die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getEmployeePayrolList($user_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['payrol_list'] = $result['payrol_list'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = "Data Not Found";
          $response['payrol_list'] = $result['payrol_list'];
      }

      echoRespnse(200, $response);
 });


$app->post('/saveManpowerRequisition', 'authenticatedefault', function() use ($app) 
 {         

      $json = $app->request->getBody();
      $data = json_decode($json, true);

      $result = implode(',',$data);
      $user_id  = $data['user_id'];
      $req_id  = $data['req_id'];

      // echo $user_id;die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getSaveManpowerRequisition($user_id,$req_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['Requisition'] = $result['RequisitionList'];
           $response['Action_Perform'] = $result['Action_Perform'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'Data Not Found';
          $response['Requisition'] = $result['RequisitionList'];
          $response['Action_Perform'] = $result['Action_Perform'];
      }

      echoRespnse(200, $response);
 });

 $app->post('/requisitionActionPerform', 'authenticatedefault', function() use ($app) 
 {         

      $json = $app->request->getBody();
      $data = json_decode($json, true);

      $result = implode(',',$data);
      $user_id  = $data['user_id'];
      $req_id  = $data['req_id'];
      $action_perform  = $data['action_perform'];
      $comment  = $data['comment'];

      // echo $user_id;die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getRequisitionActionPerform($user_id,$req_id,$action_perform,$comment);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = $result['message'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = $result['message'];
      }

      echoRespnse(200, $response);
 });

$app->post('/startTaskPrograss', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);

      $result = implode(',',$data);
      $user_id  = $data['user_id'];
      $task_id  = $data['task_id'];
      $status  = $data['status_id'];

      // echo $user_id;die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getStartToTask($user_id,$task_id,$status);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = $result['message'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = $result['message'];
      }

      echoRespnse(200, $response);
 });

$app->post('/taskData', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);

      $result = implode(',',$data);
      $user_id  = $data['user_id'];
      $task_id  = $data['task_id'];

      // echo $user_id;die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getTaskData($user_id,$task_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['task_data'] = $result['task_data'];
      }
      else
      {
          $response['status'] =0;
          $response['task_data'] = $result['task_data'];
      }

      echoRespnse(200, $response);
 });


$app->post('/taskWorkHistoryData', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);

      $result = implode(',',$data);
      $user_id  = $data['user_id'];
      $task_id  = $data['task_id'];

      // echo $user_id;die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getTaskWorkHistoryData($user_id,$task_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['task_data'] = $result['task_data'];
      }
      else
      {
          $response['status'] =0;
          $response['task_data'] = $result['task_data'];
      }

      echoRespnse(200, $response);
 });

$app->post('/taskWorkHistoryLastRecord', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);

      $result = implode(',',$data);
      $user_id  = $data['user_id'];
      $task_id  = $data['task_id'];

      // echo $user_id;die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getTaskWorkHistoryLastRecord($user_id,$task_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['task_data'] = $result['task_data'];
      }
      else
      {
          $response['status'] =0;
          $response['task_data'] = $result['task_data'];
      }

      echoRespnse(200, $response);
 });

$app->post('/statusTaskPrograss', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);

      $result = implode(',',$data);
      $user_id  = $data['user_id'];
      $task_id  = $data['task_id'];
      $completion  = $data['completion'];
      $notes  = $data['notes'];
      $attachment  = $data['attachment'];
      $statuslevel  = $data['status_id'];

      // echo $user_id;die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getStatusToTask($user_id,$task_id,$completion,$notes,$attachment,$statuslevel);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = $result['message'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = $result['message'];
      }

      echoRespnse(200, $response);
 });

$app->post('/meetingRoomsList', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
     
      $response = array();
      $db = new DbHandler();

      $result=$db->meetingRoomsList();
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response["meeting_rooms_list"]=$result['meetingRoomsList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response["meetingRoomsList"]=array();
      }

      echoRespnse(200, $response);
 });

$app->post('/getStepCount', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
             $data = json_decode($json, true);

             // echo  $data['user_id'];die();
                // $result = implode(',',$data);
                $user_id  = $data['user_id'];
           
            $response = array();
            $db = new DbHandler();

            $result = $db->getStepCount($user_id);

            if ($result['status']==1) 
            {
            $response["status"] =1;
            $response['message'] = "success";
            $response["step_count"]=$result['step_count'];
            }
            else
            {
            $response['status'] =0;
            $response['message'] = 'unsuccessfull';
            $response["step_count"]=0;
            }

      echoRespnse(200, $response);
 });

$app->post('/stepCount', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
             $data = json_decode($json, true);
            // $result1 = implode(',',$data);
                $user_id  = $data['user_id'];
                $step_count  = $data['step_count'];
           
            $response = array();
            $db = new DbHandler();

            $result = $db->getCreateStepCount($user_id,$step_count);

            if ($result['status']==1) 
            {
            $response["status"] =1;
            $response['message'] = "success";
            $response["step_count"]=$result['step_count'];
            }
            else
            {
            $response['status'] =0;
            $response['message'] = 'unsuccessfull';
            $response["step_count"]=array();
            }

      echoRespnse(200, $response);
 });

$app->post('/taskList', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);

              $user_id  = $data['user_id'];

      $response = array();
      $db = new DbHandler();

      $result=$db->getTaskList($user_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['tasksList'] = $result['tasksList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response['tasksList'] = array();
      }

      echoRespnse(200, $response);
 });

$app->post('/createTask', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
             $data = json_decode($json, true);
            $result = implode(',',$data);
            $user_id  = $data['user_id'];

             $task_title  = $data['task_title'];
             $start_date  = date('Y-m-d',strtotime($data['start_date']));
             $due_date  = date('Y-m-d',strtotime($data['due_date']));
             $task_details  = $data['task_details'];
             $task_priority  = $data['task_priority'];
             $task_type  = $data['task_type'];
             $assigned_to  = $data['assigned_to'];
             $statusId  = 0;
           
            $response = array();
            $db = new DbHandler();

            $result = $db->getCreateTask($user_id,$task_title,$start_date,$due_date,$task_details,$task_priority,$task_type,$statusId,$assigned_to);

            if ($result['status']==1) 
            {
            $response["status"] =1;
            $response['message'] = "success";
            $response["task_data"]=$result['taskId'];
            }
            else
            {
            $response['status'] =0;
            $response['message'] = 'unsuccessfull';
            $response["task_data"]=array();
            }

      echoRespnse(200, $response);
 });

$app->post('/editTask', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
             $data = json_decode($json, true);
            $result = implode(',',$data);
            $user_id  = $data['user_id'];
            $task_id  = $data['task_id'];

             $task_title  = $data['task_title'];
             $start_date  = date('Y-m-d',strtotime($data['start_date']));
             $due_date  = date('Y-m-d',strtotime($data['due_date']));
             $task_details  = $data['task_details'];
             $task_priority  = $data['task_priority'];
             $task_type  = $data['task_type'];
             $assigned_to  = $data['assigned_to'];
             $statusId  = 0;
           
            $response = array();
            $db = new DbHandler();

            $result = $db->getEditTask($user_id,$task_id,$task_title,$start_date,$due_date,$task_details,$task_priority,$task_type,$statusId,$assigned_to);

            if ($result['status']==1) 
            {
            $response["status"] =1;
            $response['message'] = $result['message'];
            }
            else
            {
            $response['status'] =0;
            $response['message'] = $result['message'];
            }

      echoRespnse(200, $response);
 });


$app->post('/assignedToEmployeeEquipmentList', 'authenticatedefault', function() use ($app) 
{         

        $json = $app->request->getBody();
        $data = json_decode($json, true);
        $result = implode(',',$data);
        $user_id  = $data['user_id'];
        // $equipment_id  = $data['equipment_id'];
     
      $response = array();
      $db = new DbHandler();

      $result=$db->assignedToEmployeeEquipmentList($user_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response["assigned_emp_equipment_list"]=$result['assignedToEmployeeEquipmentList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response["assigned_emp_equipment_list"]=array();
      }

      echoRespnse(200, $response);
 });

$app->post('/bookMeetingRoom', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
             $data = json_decode($json, true);
             $result = implode(',',$data);
              $user_id  = $data['user_id'];
              $meetingRoomTitle  = $data['meeting_room_title'];
              $organiser  = $data['organiser'];
              $meetingRoomId  = $data['meeting_room_id'];
              $fromDate  = $data['from_date'];
              $toDate  = $data['to_date'];
              $fromTime  = $data['from_time'];
              $toTime  = $data['to_time'];
              $all_day  = $data['all_day'];
              $status_id  = $data['status_id'];
              // $attendees  = '0';
              $employee_ids  = $data['employee_ids'];
              $vendor_ids  = $data['vendor_ids'];
              $customer_ids  = $data['customer_ids'];
              $submitted_on  = getCurrentDateTime();
           
            

            $response = array();
            $db = new DbHandler();
            $result=$db->bookMeetingRoom($user_id,$meetingRoomTitle,$organiser,$meetingRoomId,$fromDate,$toDate,$fromTime,$toTime,$all_day,$status_id,$submitted_on,$employee_ids,$vendor_ids,$customer_ids);

            
           if ($result['status']==1) 
           {    

                $response["status"] =1;
                $response['message'] = "successful";
                $response["book_meeting_room_id"]=$result['bookMeetingRoom'];
               

            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Record insertion failed';
            }

            echoRespnse(200, $response);
 });

$app->post('/getBookMeetingRoom', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);
              $meeting_id  = $data['meeting_id'];
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getBookMeetingRoom($meeting_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response["get_meeting_room"]=$result['getBookMeetingRoom'];
           $response["meeting_room_employee_list"]=$result['meetingRoomEmployee'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response["getBookMeetingRoom"]=array();
          $response["meetingRoomEmployee"]=array();
      }

      echoRespnse(200, $response);
 });

$app->post('/checkBookMeetingEmployee', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
             $data = json_decode($json, true);
             $result = implode(',',$data);
              $user_id  = $data['user_id'];
              $fromDate  = date('Y-m-d',strtotime($data['from_date']));
              $toDate  = date('Y-m-d',strtotime($data['to_date']));
              $fromTime  = $data['from_time'];
              $toTime  = $data['to_time'];
              $all_day  = 0;
             
            $response = array();
            $db = new DbHandler();
            $result=$db->checkBookMeetingEmployee($user_id,$fromDate,$toDate,$fromTime,$toTime,$all_day);

            
           if($result['status']==1) 
           {    

                $response["status"] =1;
                $response['message'] = "successful";
                $response["booked_employee"]=$result['booked_employee'];
               

            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'no records found';
                $response["booked_employee"]=$result['booked_employee'];
            }

            echoRespnse(200, $response);
 });

$app->post('/addVisitor', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);
              $user_id  = $data['user_id'];
              $contact_to  = $data['contact_to'];
              $vehicle_number  = $data['vehicle_number'];
              $members  = $data['members'];
              $names  = $data['names'];
              $pass_ids  = $data['pass_ids'];
              $phone  = $data['phone'];
              $address  = $data['address'];

             // print_r($data);die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getAddVisitorPass($user_id,$contact_to,$vehicle_number,$members,$names,$pass_ids,$phone,$address);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = $result['message'];
           $response['visitor_id'] = $result['visId'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = $result['message'];
      }

      echoRespnse(200, $response);
 });

$app->post('/checkOutVisitor', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);
              $user_id  = $data['user_id'];
              $visitor_id  = $data['visitor_id'];
             // print_r($data);die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getCheckOutVisitor($user_id,$visitor_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = $result['message'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = $result['message'];
      }

      echoRespnse(200, $response);
 });

$app->post('/visitorsList', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);
              $user_id  = $data['user_id'];

              // echo $user_id;die();
            
      $response = array();
      $db = new DbHandler();

      $result=$db->getVisitorsList($user_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = $result['message'];
           $response['visitors_list'] = $result['visitors_list'];
      }
      else
      {
          $response['status'] =0;
           $response['visitors_list'] = $result['visitors_list'];
          $response['message'] = $result['message'];
      }

      echoRespnse(200, $response);
 });
// Writen by pavan start Recruitment
// $app->post('/createManpowerRequisition', 'authenticatedefault', function() use ($app) 
// {         

//       $json = $app->request->getBody();
//       $data = json_decode($json, true);
//       $result = implode(',',$data);
//               $user_id  = $data['user_id'];
//               $requisition_type  = $data['requisition_type'];
//               $job_title  = $data['job_title'];
//               $location  = $data['location'];
//               $department  = $data['department'];
//               $report_to  = $data['report_to'];
//               $no_of_positions  = $data['no_of_positions'];
//               $job_description  = $data['job_description'];
//               $type_of_appointment  = $data['type_of_appointment'];
//               $duration  = $data['duration'];
//               $grade  = $data['grade'];
//               $qualifications  = $data['qualifications'];
//               $skill_experience  = $data['skill_experience'];
//               $min_eperience  = $data['min_eperience'];
//               $min_salary  = $data['min_salary'];
//               $max_salary  = $data['max_salary'];
//               $required_by  = $data['required_by'];
//               $requested_budgeted  = $data['requested_budgeted'];
//               $reason_for_requirement  = $data['reason_for_requirement'];
//               $replace_for  = $data['replace_for'];
//               $comments  = $data['comments'];
//               $status  = $data['status'];

//               // echo $user_id;die();
            
//       $response = array();
//       $db = new DbHandler();

//       $result=$db->createManpowerRequisition($user_id, $requisition_type, $job_title, $location, $department, $report_to, $no_of_positions, $job_description, $type_of_appointment, $duration, $grade,$qualifications, $skill_experience, $min_eperience, $min_salary, $max_salary, $required_by,$requested_budgeted, $reason_for_requirement, $replace_for, $comments, $status);
//      if ($result['status']==1) 
//      {
//            $response["status"] =1;
//            $response['message'] = $result['message'];
//       }
//       else
//       {
//           $response['status'] =0;
//           $response['message'] = $result['message'];
//       }

//       echoRespnse(200, $response);
//  });

// Writen by pavan end Recruitment
$app->post('/getBookMeetingRoomsList', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      // $result = implode(',',$data);
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getBookMeetingRoomsList();
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response["get_meeting_rooms_list"]=$result['getBookMeetingRoomsList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response["getBookMeetingRoomsList"]=array();
      }

      echoRespnse(200, $response);
 });



$app->post('/savePpeRequisition', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
             $data = json_decode($json, true);
             $result = implode(',',$data);

              $user_id  = $data['user_id'];
              $requisition_date  = $data['requisition_date'];
              $indent_type  = $data['indent_type'];
              $notes  = $data['notes'];
              $status  = 2;
              // $status  = $data['status'];
              $last_submitted_by  = $data['user_id'];
              $last_submitted_on  = date('Y-m-d');;

            $response = array();
            $db = new DbHandler();
            $result=$db->savePpeRequisition($user_id,$requisition_date,$indent_type,$notes,$status,$last_submitted_by,$last_submitted_on);

            
           if ($result['status']==1) 
           {    

                $response["status"] =1;
                $response['message'] = "successful";
                $response["reqId"]=$result['reqId'];
                $response["requisitionCode"]=$result['requisitionCode'];
               

            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Record insertion failed';
            }

            echoRespnse(200, $response);
 });



$app->post('/getPPEproductsList', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      // $result = implode(',',$data);
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getPPEproductsList();
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response["get_ppe_products_list"]=$result['getPPEproductsList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response["get_ppe_products_list"]=array();
      }

      echoRespnse(200, $response);
 });

$app->post('/savePpeProductsByRequisitionId', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
             $data = json_decode($json, true);
             // $result = implode(',',$data);
              // print_r($data['products']);die();

              $user_id  = $data['user_id'];
              $requisition_id  = $data['requisition_id'];
              $req_for  = $data['indent_type'];
              $products  = $data['products'];

              // for ($i=0; $i < sizeof($products); $i++) { 
              //  echo $products[$i]['product_id'];
              //  echo $products[$i]['product_qty'];die();
              // }
              $status  = 0;
             
            $response = array();
            $db = new DbHandler();
            $result=$db->savePpeProductsByRequisitionId($user_id,$requisition_id,$req_for,$products,$status);

            
           if ($result['status']==1) 
           {    

                $response["status"] =1;
                $response['message'] = "successful";
                $response["requisition_material"]=$result['requisition_material'];
                // $response["requisitionCode"]=$result['requisitionCode'];
               

            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Record insertion failed';
            }

            echoRespnse(200, $response);
 });

$app->post('/ppeSupervisiorStatusUpdate', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
             $data = json_decode($json, true);
             $result = implode(',',$data);

              $user_id  = $data['user_id'];
              $requisition_id  = $data['requisition_id'];
              $notes  = $data['notes'];
              $status  = $data['status'];

              $submitted_by  = $data['user_id'];
              $submitted_on  = date('Y-m-d');;

            $response = array();
            $db = new DbHandler();
            $result=$db->ppeSupervisiorStatusUpdate($user_id,$requisition_id,$notes,$status,$submitted_by,$submitted_on);

            
           if ($result['status']==1) 
           {    

                $response["status"] =1;
                $response['message'] = "successful";
               
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Record insertion failed';
            }

            echoRespnse(200, $response);
 });

$app->post('/getJobVacancyList', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      // $result = implode(',',$data);
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getJobVacancyList();
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response["vacancy_list"]=$result['vacancy_list'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response["vacancy_list"]=array();
      }

      echoRespnse(200, $response);
 });
$app->post('/saveEmployeeReferral', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
             $data = json_decode($json, true);
             // print_r($json);die();
             $result = implode(',',$data);


              $user_id  = $data['user_id'];
              $first_name  = $data['first_name'];
              $middle_name  = $data['middle_name'];
              $last_name  = $data['last_name'];
              $email  = $data['email'];
              $contact_number  = $data['contact_number'];
              $vacancy_id  = $data['vacancy_id'];
              $vacancy_name  = $data['vacancy_name'];
              // $resume  = $data['resume'];
              $keyWords  = $data['key_skills'];
              $comment  = $data['recommendation'];

              $submitted_by  = $data['user_id'];
              $submitted_on  = date('Y-m-d');;

            $response = array();
            $db = new DbHandler();
            $result=$db->saveEmployeeReferral($user_id,$first_name,$middle_name,$last_name,$email,$contact_number,$vacancy_id,$vacancy_name,$keyWords,$comment,$submitted_by,$submitted_on);

            
           if ($result['status']==1) 
           {    

                $response["status"] =1;
                $response['message'] = "successful";
                $response["referral_info"]=$result['candidate_details'];
               

            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Record insertion failed';
            }

            echoRespnse(200, $response);
 });

$app->post('/upload/resume', 'authenticatedefault', function() use ($app) 
{       

            $candidate_id = $app->request()->post('candidate_id');

            // echo $candidate_id;die();
          
            $response = array();
            $db = new DbHandler();
                       
            $file_content = file_get_contents($_FILES['resume']['tmp_name']);
            $file_name = $_FILES['resume']['name'];
            $file_type = $_FILES['resume']['type'];
            $file_size = $_FILES['resume']['size'];
            
            $result=$db->uploadResume($candidate_id,$file_name,$file_type,$file_size,$file_content);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["uploadResume"]=$result['uploadResume'];
            }else {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["uploadResume"]=array();
            }
        
            echoRespnse(200, $response);
 });


$app->post('/selectEmployeeOfTheMonth', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
             $data = json_decode($json, true);
             $result = implode(',',$data);

              $financial_year  = $data['financial_year'];
              $month  = $data['month'];
              $nominatEmpNumber  = $data['nominate_employee_id'];
              $department_id  = $data['department_id'];
              $comment  = $data['reason_for_select'];
              $status_id  = 2;
              $submitted_by  = $data['user_id'];
              $submitted_on  = date("Y-m-d H:i:s");
           
            

            $response = array();
            $db = new DbHandler();
            $result=$db->selectEmployeeOfTheMonth($financial_year,$month,$nominatEmpNumber,$department_id,$comment,$status_id,$submitted_by,$submitted_on);

            
           if ($result['status']==1) 
           {    

                $response["status"] =1;
                $response['message'] = "successful";
                $response["employee_of_month"]=$result['employee_of_month'];
               

            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Record insertion failed';
            }

            echoRespnse(200, $response);
 });

$app->post('/getEomListBySupervisor', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);

      $user_id  = $data['user_id'];
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getEomListBySupervisor($user_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response["eom_list"]=$result['eom_list'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response["eom_list"]=array();
      }

      echoRespnse(200, $response);
 });
 
$app->post('/getEomListByHod', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);

      $department_id  = $data['department_id'];
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getEomListByHod($department_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response["eom_list"]=$result['eom_list'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response["eom_list"]=array();
      }

      echoRespnse(200, $response);
 });

$app->post('/getRoleBasedEmployeeList', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);

      $user_id  = $data['user_id'];

      // echo $user_id;die();
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getRoleBasedEmployeeList($user_id);
      
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response["eom_list"]=$result['eom_list'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response["eom_list"]=array();
      }

      echoRespnse(200, $response);
 });




 $app->post('/hodApproveEomStatus', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
             $data = json_decode($json, true);
             $result = implode(',',$data);

              $eom_id  = $data['eom_id'];
              $emp_id  = $data['emp_id'];
              $status  = 3;

              $submitted_by  = $data['user_id'];
              $submitted_on  = date('Y-m-d');;

            $response = array();
            $db = new DbHandler();
            $result=$db->hodApproveEomStatus($eom_id,$emp_id,$status,$submitted_by,$submitted_on);

            
           if ($result['status']==1) 
           {    

                $response["status"] =1;
                $response['message'] = "successful";
                $response["emp_of_the_month_log"]=$result['emp_of_the_month_log'];
               
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Record insertion failed';
            }

            echoRespnse(200, $response);
 });


// <----Webservice for mobile app pavan end-->

///////////////// RAMU START ///////////////////////

$app->post('/leaveType', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
          //  $result = implode(',',$data);
           
            // $platform   = $data['platform'];
            $user_id  = $data['user_id'];

           
            $response = array();
      $db = new DbHandler();
      
      $result=$db->getLeaveTypes();
     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "success";
                 $response["leaveType"]=$result['leaveType'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'unsuccessfull';
                $response["leaveType"]=array();
            }
      
        echoRespnse(200, $response);
});

$app->post('/leaveDuration', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
          //  $result = implode(',',$data);
           
            // $platform   = $data['platform'];
            $user_id  = $data['user_id'];

           
            $response = array();
      $db = new DbHandler();
      
      $result=$db->getLeaveDuration();
     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "success";
                 $response["leaveDuration"]=$result['leaveDuration'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'unsuccessfull';
                $response["leaveDuration"]=array();
            }
      
        echoRespnse(200, $response);
});

$app->post('/applyLeave', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);
            // $result = implode(',',$data);
            
              $user_id  = $data['user_id'];
              // echo "string ".$user_id;die();
              $leave_type_id  = $data['leave_type_id'];
              $from_date  = $data['from_date'];
              $to_date  = $data['to_date'];              
              $duration  = $data['duration']; 
              $comments   = $data['comments'];
              $start_time   = $data['start_time'];
              $end_time   = $data['end_time'];
              $status_id   = $data['status_id'];

              // accessToken($user_id); 

            $response = array();
            $db = new DbHandler();
            $result = $db->applyLeave($user_id,$leave_type_id,$comments,$from_date,$to_date,$duration,$start_time,$end_time,$status_id);

            
           if ($result['status']==1) 
           {    

                $response["status"] = 1;
                $response['message'] = "successfully submitted";
         
            }
            else
            {
                $response['status'] = 0;
                $response['message'] = 'No Records Found';
                // $response["taskid"]=array();
            }

            echoRespnse(200, $response);
});

$app->post('/applyLeaveEmpBySupervisor', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);
            // $result = implode(',',$data);
            
              $user_id  = $data['user_id'];
              // echo "string ".$user_id;die();
              $emp_number  = $data['emp_number'];
              $leave_type_id  = $data['leave_type_id'];
              $from_date  = $data['from_date'];
              $to_date  = $data['to_date'];              
              $duration  = $data['duration']; 
              $comments   = $data['comments'];
              $start_time   = $data['start_time'];
              $end_time   = $data['end_time'];
              if(data('Y-m-d',strtotime($data['from_date'])) >= data('Y-m-d')){

                $status_id   = 2; // Status Taken
              }else{
                $status_id = 3;
              }

              // accessToken($user_id); 

            $response = array();
            $db = new DbHandler();
            $result = $db->applyLeaveEmpBySupervisor($user_id,$emp_number,$leave_type_id,$comments,$from_date,$to_date,$duration,$start_time,$end_time,$status_id);

            
           if ($result['status']==1) 
           {    

                $response["status"] = 1;
                $response['message'] = "Successfully Assigned";
         
            }
            else
            {
                $response['status'] = 0;
                $response['message'] = 'No Records Found';
                // $response["taskid"]=array();
            }

            echoRespnse(200, $response);
});

$app->post('/saveResumeBank', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);

            // $email  = $data['email'];              
            // $contact_number  = $data['contact_number']; 
            // $keywords   = $data['keywords'];
            // $comment   = $data['comment'];

            $email = $app->request()->post('email');
            $contact_number = $app->request()->post('contact_number');
            $keywords = $app->request()->post('keywords');
            $comment = $app->request()->post('comment');

            // $file_content = file_get_contents($_FILES['image']['tmp_name']);
            

            // $data = json_decode(file_get_contents("php://input"), true);
            $file_name = $_FILES['image']['name'];
            $file_type = $_FILES['image']['type'];
            $file_size = $_FILES['image']['size'];
            $tempPath  =  $_FILES['image']['tmp_name'];

            $response = array();
            $db = new DbHandler();
            $result = $db->saveResumeBank($email,$contact_number,$keywords,$comment,$file_name,$file_type,$file_size,$tempPath);

            
           if ($result['status']==1) 
           {
                  

                 $response["status"] =1;
                 $response['message'] = "success";
                 $response["ResumeBank"]=$result['ResumeBank'];
            }else {
                $response['status'] =0;
                $response['message'] = 'fail';
                // $response["saveResumeBank"]=array();
            }
        
            echoRespnse(200, $response);
});

$app->post('/leaveApprovals', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            // $result = implode(',',$data);


            $user_id  = $data['user_id'];            
            $status_id  = $data['status_id'];
            $leave_request_id  = $data['leave_request_id'];
            // echo $user_id;die();
            
            $response = array();
            $db = new DbHandler();
            $result=$db->leaveApprovals($user_id,$status_id,$leave_request_id);
        
           if ($result['status'] == 1) 
           {
                 $response["status"] =1;
                 $response['message'] = $result['log'];
                 // $response["leaveApprovals"] = $result['leaveApprovals'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                // $response["leaveApprovals"]=array();
            }
        
            echoRespnse(200, $response);
});

///////////// Overtime Start ////////////////////////////

$app->post('/assignOvertime', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            // $result = implode(',',$data);

            $user_id  = $data['user_id'];            
            $emp_number  = $data['emp_number'];            
            $otdate  = $data['otdate'];            
            $start_time  = $data['start_time'];            
            $end_time  = $data['end_time'];            
            // $hours  = $data['hours'];            
            $reason  = $data['reason'];            
            $status  = $data['status'];   
            // $claim_type  = $data['claim_type'];   

            // print_r($data);die();         
            
            $response = array();
            $db = new DbHandler();
            $result=$db->assignOvertime($user_id,$emp_number,$otdate,$start_time,$end_time,$reason,$status);

            // echo $result['status'];die();
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = 'Successfully assigned';
                 // $response["leaveApprovals"] = $result['leaveApprovals'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                // $response["leaveApprovals"]=array();
            }
        
            echoRespnse(200, $response);
});

$app->post('/myOtList', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
          //  $result = implode(',',$data);
           
            // $platform   = $data['platform'];
            $user_id  = $data['user_id'];
            $role  = $data['role'];
            $status_id  = $data['status_id'];

           
            $response = array();
      $db = new DbHandler();
      
      $result=$db->getMyOTList($user_id,$role,$status_id);
     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "success";
                 $response["myOtList"]=$result['myOtList'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'unsuccessfull';
                $response["myOtList"]=array();
            }
      
        echoRespnse(200, $response);
});

$app->post('/checkOT', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
          //  $result = implode(',',$data);
           
            // $platform   = $data['platform'];
            $user_id  = $data['user_id'];
            $ot_id  = $data['ot_id'];

           
            $response = array();
      $db = new DbHandler();
      
      $result=$db->getCheckOT($user_id,$ot_id);
     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "success";
                 $response["myOtCheck"]=$result['myOtCheck'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'unsuccessfull';
                $response["myOtCheck"]='';
            }
      
        echoRespnse(200, $response);
});

$app->post('/allOtNewList', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
          //  $result = implode(',',$data);
           
            $user_id  = $data['user_id'];

           
            $response = array();
      $db = new DbHandler();
      
      $result=$db->getAllOtNewList($user_id);
     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "success";
                 $response["myOtList"]=$result['myOtList'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Data Found';
                $response["myOtList"]=array();
            }
      
        echoRespnse(200, $response);
});

// Over Time Verification

$app->post('/claimOT', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
          //  $result = implode(',',$data);
           
            // $platform   = $data['platform'];
            $user_id  = $data['user_id'];
            $status_id  = $data['status_id'];
            $ot_id  = $data['ot_id'];
            $claim_type  = $data['claim_type'];
            $notes  = $data['notes'];

           
            $response = array();
      $db = new DbHandler();
      
      $result=$db->claimOT($user_id,$status_id,$ot_id,$claim_type,$notes);
     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "success";
                 $response["claimOT"]=$result['log'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'unsuccessfull';
                $response["claimOT"]=array();
            }
      
        echoRespnse(200, $response);
});

$app->post('/bulkOtClaim', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
           // $result = implode(',',$data);
           
            // $platform   = $data['platform'];
            $user_id  = $data['user_id'];
            $status_id  = $data['status_id'];
            $ot_id  = $data['otlist'];

            $response = array();
      $db = new DbHandler();
      
      $result=$db->bulkOtClaim($user_id,$status_id,$ot_id);
     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "success";
                 $response["claimOT"]=$result['log'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'unsuccessfull';
                $response["claimOT"]=array();
            }
      
        echoRespnse(200, $response);
});

$app->post('/subOrdinateEmployees', 'authenticatedefault', function() use ($app) 
{     

        $json = $app->request->getBody();
            $data = json_decode($json, true);
            
            $user_id  = $data['user_id'];
              
            $response = array();
            $db = new DbHandler();
            $userDetails = $db->getUserRoleByUserId($user_id);
            $emp_number = $userDetails['empNumber'];
            $result = $db->subordinateByEmpList($emp_number);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["subOrdinateEmployees"] = $result['emplist'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                // $response["subOrdinateEmployees"]=array();
            }
        
            echoRespnse(200, $response);
});

///////////// Overtime End /////////////////////////////

    

///////////////// RAMU END  ///////////////////////


function accessToken($user_id) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
    // Verifying Authorization Header
    if (isset($headers['sessiontoken'])) 
    {
        $db = new DbHandler();
        // get the api key
        $api_key = $headers['sessiontoken'];
        // validating api key
        if (!$db->isValidSessionToken($api_key,$user_id)) 
        {
            $response["status"] ="error";
            $response["message"] = "Token Expired";
            //$response["message"] = "Access Denied. Invalid Api key";
            echoRespnse(401, $response);
            $app->stop();
        } 
    } 
    else 
    {
        // api key is missing in header
        $response["status"] ="error";
        //$response["message"] = "Api key is misssing";
        $response["message"] = "sessiontoken key is missing";
        echoRespnse(401, $response);
        $app->stop();
    }
}

/*** Indian Date Time Generation ***/
  function getCurrentDateTime(){
    $datetime = date('Y-m-d H:i:s');
    $given = new DateTime($datetime, new DateTimeZone("UTC"));
    $given->setTimezone(new DateTimeZone("asia/kolkata"));
    $output = $given->format("Y-m-d H:i:s"); 
    return $output;
  }

function authenticatedefault(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
    $APPKEY = "b8416f2680eb194d61b33f9909f94b9d";
    // Verifying Authorization Header
   //print_r($headers);exit;
    if (isset($headers['Authorization']) || isset($headers['authorization'])) 
    {
    if(isset($headers['authorization']))
    {
      $headers['Authorization']=$headers['authorization'];
    }
    
        $db = new DbHandler();

        // get the api key
        $api_key = $headers['Authorization'];
        // validating api key

        if($api_key != $APPKEY)
        {
      $response["status"] ="error";
            $response["message"] = "Access Denied";
            echoRespnse(401, $response);
            $app->stop();
    }
       else 
        {
            global $user_id;
            // get user primary key id
          //$user_id = $db->getUserId($api_key);

        }
    } 
    else 
    {
        // api key is missing in header
        $response["status"] ="error";
        //$response["message"] = "Api key is misssing";
        $response["message"] = "Access Denied";
        echoRespnse(401, $response);
        $app->stop();
    }
}

///////////////////////////////////////
/**
 * User Login
 * url - /login
 * method - POST
 * params - username, password,deviceId,pushId,latitude,longitude,platform , 'authenticatedefault'
 */

$app->post('/instanceurl', 'authenticatedefault', function() use ($app) 
{
     
            // reading post params
           
            $platform= $app->request()->post('platform');//1-Android ,2-IOS
            $url = $app->request()->post('url');
            
            // check for required params
            verifyRequiredParams(array('url','platform'));
            $req = $app->request;
            $base_url = $req->getUrl()."".$req->getRootUri()."/";
            $response = array();
            
           if ($base_url == $url) 
           {
                
                 $response["status"] =1;
                 $response['message'] = "Success";
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Entered instance url is invalid';
                
            }
           
            echoRespnse(200, $response);
 });


$app->post('/generate/sessiontoken', 'authenticatedefault', function() use ($app) 
{
             $json = $app->request->getBody();
            $data = json_decode($json, true);
            // reading post params
            $user_id = $data['user_id'];
            
            // check for required params
            // verifyRequiredParams(array('user_id','platform'));
            
            $response = array();
            $db = new DbHandler();
            $result=$db->generateSessionToken($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Session Token generated in successfully";
                 $response["session_token"]=$result['session_token'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Session Token generation failed';
                $response["session_token"]=array();
            }
           
            echoRespnse(200, $response);
 });


$app->post('/login', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            // $result = implode(',',$data);
                   
            $username  = $data['username'];
            $password = $data['password'];

            // echo $username;die();

           

                      
            $response = array();
      $db = new DbHandler();
      $result=$db->userLogin($username,$password);
    
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Logged in successfully";
                 $response["userDetails"]=$result['userDetails'];
               
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Incorrect Passcode';
                $response["userDetails"]=array();
            }
      
        echoRespnse(200, $response);
 });

$app->post('/otpverify', 'authenticatedefault', function() use ($app) 
{

            $json = $app->request->getBody();
            $data = json_decode($json, true);

            // reading post params
            // $platform= $data['platform'];//1-Android ,2-IOS
            $user_id = $data['user_id'];
            $otp = md5($data['otp']);


            // echo $otp;
            // exit();
              // accessToken($user_id); 
            // check for required params
            // verifyRequiredParams(array('user_id','otp','platform'));
            
             $req = $app->request;
            $base_url = $req->getUrl()."".$req->getRootUri()."/";
            $response = array();
            $db = new DbHandler();
            $result=$db->otpverify($user_id,$otp,$base_url);
            if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "OTP Verified";
                 $response["userDetails"]=$result['userDetails'];
            }
            else if ($result['status']==2) 
            {
                 $response["status"] =0;
                 $response['message'] = "Your entered Incorrect OTP";
                 $response["userDetails"]=array();
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Invalid OTP';
                $response["userDetails"]=array();
            }
       
            echoRespnse(200, $response);
 });


$app->post('/setpasscode', 'authenticatedefault', function() use ($app) 
{

             $json = $app->request->getBody();
            $data = json_decode($json, true);

            $user_id = $data['user_id'];
            $passcodeentrVal = md5($data['passcode']);
            $imeino = $data['imeino'];
            // $platform = $data['platform'];
            $datetime = getCurrentDateTime();
              // accessToken($user_id); 
            // check for required params
            //verifyRequiredParams(array('user_id', 'passcode','imeino','platform'));
            $req = $app->request;
            $base_url = $req->getUrl()."".$req->getRootUri()."/";
            $response = array();
            $db = new DbHandler();
            $result=$db->setpasscode($user_id,$passcodeentrVal,$base_url,$datetime,$imeino);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Passcode setted successfully";
                 $response["userDetails"]=$result['userDetails'];
            }else if ($result['status']==2) 
           {
                 $response["status"] =1;
                 $response['message'] = "Passcode insertion failed";
                 $response["userDetails"]=array();
            }
            else if ($result['status']==3) 
           {
                 $response["status"] =1;
                 $response['message'] = "Passcode updation failed";
                 $response["userDetails"]=array();
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Eorror setting passcode';
                $response["userDetails"]=array();
            }
        
            echoRespnse(200, $response);
 });

$app->post('/loginwtoutpasscode', 'authenticatedefault', function() use ($app) 
{         
     
         
            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
           
           
            $username  = $data['username'];
            $password = $data['password'];

           
 $req = $app->request;
            $base_url = $req->getUrl()."".$req->getRootUri()."/";
            $response = array();
            $db = new DbHandler();
            $result=$db->loginWtoutPasscode($username,$password,$base_url);
                      
           
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Logged in successfully";
                 $response["userDetails"]=$result['userDetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Incorrect Passcode';
                $response["userDetails"]=array();
            }
      
        echoRespnse(200, $response);
 });


$app->post('/passcodelogin', 'authenticatedefault', function() use ($app) 
{

            $json = $app->request->getBody();
            $data = json_decode($json, true);

            $user_id = $data['user_id'];
            $passcodeentrVal = md5($data['passcode']);
            // $platform = $data['platform'];
              // accessToken($user_id); 
            // check for required params
            //verifyRequiredParams(array('user_id', 'passcode','platform'));
            $req = $app->request;
            $base_url = $req->getUrl()."".$req->getRootUri()."/";
            $response = array();
            $db = new DbHandler();
            $result=$db->passcodelogin($user_id,$passcodeentrVal,$base_url);

           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Logged in successfully";
                 $response["userDetails"]=$result['userDetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Incorrect Passcode';
                $response["userDetails"]=array();
            }
        
            echoRespnse(200, $response);
 });

// login with mob number
$app->post('/loginMobNumber', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
           
            // $platform   = $data['platform'];
            $mobnumber  = $data['mobnumber'];

             $username  = $data['username'];
            $response = array();
      $db = new DbHandler();
      $result=$db->loginMobNum($mobnumber,$username);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Logged in successfully";
                 $response["userMobLogin"]=$result['userMobLogin'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Incorrect Mobile Number';
                $response["userMobLogin"]=array();
            }
      
        echoRespnse(200, $response);
 });


// password login using mobile or username
$app->post('/loginWithMobNumOrUsrname', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
           
            // $platform   = $data['platform'];
            $mobnumber  = $data['mobnumber'];

             $username  = $data['username'];

             $password  = $data['password'];

            $response = array();

            $req = $app->request;
            $base_url = $req->getUrl()."".$req->getRootUri()."/";
      $db = new DbHandler();
      $result=$db->loginWithMobNumOrUsrname($mobnumber,$username,$password,$base_url);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Logged in successfully";
                 $response["userDetails"]=$result['userDetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Mobile Number or Username or Password you have entered is incorrect';
                $response["userDetails"]=array();
            }
      
        echoRespnse(200, $response);
 });


// login with mob number
$app->post('/sendOtp', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
           
            // $platform   = $data['platform'];
            $user_id  = $data['user_id'];
            
            
            $response = array();
      $db = new DbHandler();
      $result=$db->sendOtp($user_id);
     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "otp sent successfully";
                 $response["sendOtpDetails"]=$result['sendOtpDetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'otp sending is unsuccessfull';
                $response["sendOtpDetails"]=array();
            }
      
        echoRespnse(200, $response);
 });


$app->post('/logout', 'authenticatedefault', function() use ($app) 
{
            // reading post params
            // $platform= $app->request()->post('platform');//1-Android ,2-IOS
            // $user_id = $app->request()->post('user_id');
           
            $json = $app->request->getBody();
            $data = json_decode($json, true);

            $user_id = $data['user_id'];
            // check for required params
            // verifyRequiredParams(array('user_id','platform'));
                // accessToken($user_id); 
            $response = array();
            $db = new DbHandler();
            $result=$db->logout($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Logged out successfully";
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Failed to logout';
            }
           
            echoRespnse(200, $response);
 });


// for upload/image we have to implement it here.
$app->post('/upload/image', 'authenticatedefault', function() use ($app) 
{           
            /*$json = $app->request->getBody();
            $data = json_decode($json, true);*/


             // reading post params
            $ticket_id = $app->request()->post('ticket_id');
            // $platform= $data['platform'];//1-Android ,2-IOS
            $user_id = $app->request()->post('user_id');
              // accessToken($user_id); 
             // check for required params
              // check for required params
            // verifyRequiredParams(array('ticket_id','user_id','platform'));
               $created_on  = getCurrentDateTime();
            $response = array();
            $db = new DbHandler();
                       
            $file_content = file_get_contents($_FILES['image']['tmp_name']);
            $file_name = $_FILES['image']['name'];
            $file_type = $_FILES['image']['type'];
            $file_size = $_FILES['image']['size'];
            //verifyRequiredParams(array('ticket_id','platform'));

           $result=$db->uploadImage($ticket_id,$file_name,$file_type,$file_size,$file_content,$created_on,$user_id);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["uploadImage"]=$result['uploadImage'];
            }else {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["uploadImage"]=array();
            }
        
            echoRespnse(200, $response);
 });


// for dateformat we have to implement it here.
$app->post('/dateFormat', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];

            // accessToken($userIdPass); 

            
            $response = array();
            $db = new DbHandler();
            $result=$db->dateFormat();
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["dateFormat"]=$result['dateFormat'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["dateFormat"]=array();
            }
        
            echoRespnse(200, $response);
 });




// for location we have to implement it here.
$app->post('/location', 'authenticatedefault', function() use ($app) 
{           

        $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

             
              $user_id  = $data['user_id'];

      /*$platform= $app->request()->post('platform');//1-Android ,2-IOS
        $user_id = $app->request()->post('user_id');*/
              // accessToken($user_id); 
             // check for required params
            //verifyRequiredParams(array('user_id','platform'));
          
            $response = array();
            $db = new DbHandler();
            $result=$db->location();
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["location"]=$result['location'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["location"]=array();
            }
        
            echoRespnse(200, $response);
 });

// for plantlst we have to implement it here.
$app->post('/plantlst', 'authenticatedefault', function() use ($app) 
{           

              $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

              $user_id  = $data['user_id'];
              $locationid  = $data['locationid'];
             // reading post params
           /* $locationid = $app->request()->post('locationid');
            $platform= $app->request()->post('platform');//1-Android ,2-IOS
            $user_id = $app->request()->post('user_id');*/
              // accessToken($user_id); 
             // check for required params
            //verifyRequiredParams(array('locationid','user_id','platform'));
            
            $response = array();
            $db = new DbHandler();
            $result=$db->plantlst($locationid);

           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["plantlst"]=$result['plantlst'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["plantlst"]=array();
            }
        
            echoRespnse(200, $response);
 });

$app->post('/departmentsList', 'authenticatedefault', function() use ($app) 
{

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            
             $user_id = $data['user_id'];
          
              //accessToken($user_id); 

            $response = array();
            $db = new DbHandler();
            $result=$db->departmentsList($user_id);

           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["departmntdetails"]=$result['departmntdetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["departmntdetails"]=array();
            }

            echoRespnse(200, $response);
 });

// for funcLocation we have to implement it here.
// $app->post('/funcLocation11', 'authenticatedefault', function() use ($app) 
// {           

//              $json = $app->request->getBody();
//             $data = json_decode($json, true);
//             // $result = implode(',',$data);

//             print_r($data);
//             die();
            
//             $user_id  = $data['user_id'];
//             $department_id   = $data['department_id'];
//             $parent_id  = 0;
//             $level = 0;
            
//             $response = array();
//             $db = new DbHandler();
//             $result=$db->funcLocation($department_id,$parent_id,$level);
        
//            if ($result['status']==1) 
//            {
//                  $response["status"] =1;
//                  $response['message'] = "successful";
//                  $response["funcLocation"]=$result['funcLocation'];
//             }
//             else
//             {
//                 $response['status'] =0;
//                 $response['message'] = 'No Records Found';
//                 $response["funcLocation"]=array();
//             }
        
//             echoRespnse(200, $response);
//  });



// for unitmeasure we have to implement it here.
$app->post('/unitmeasure', 'authenticatedefault', function() use ($app) 
{                 
             $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            
              $user_id  = $data['user_id'];

            /*$platform= $app->request()->post('platform');//1-Android ,2-IOS
            $user_id = $app->request()->post('user_id');*/
              // accessToken($user_id); 
             // check for required params
            //verifyRequiredParams(array('user_id','platform'));   
            $db = new DbHandler();
            $result=$db->unitmeasure();
               
            // $user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["unitmeasure"]=$result['unitmeasure'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["unitmeasure"]=array();
            }
        
            echoRespnse(200, $response);
 });



// for parts we have to implement it here.
$app->post('/parts', 'authenticatedefault', function() use ($app) 
{       

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            
            $user_id  = $data['user_id'];
         
            // accessToken($user_id); 
            
            $db = new DbHandler();
            $result=$db->parts();
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["parts"]=$result['parts'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["parts"]=array();
            }
        
            echoRespnse(200, $response);
 });







// for deptList we have to implement it here.
$app->post('/deptLists', 'authenticatedefault', function() use ($app) 
{           

             $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            
              $user_id  = $data['user_id'];
               $location_id  = $data['location_id'];
                $plant_id  = $data['plant_id'];
             // reading post params
            /*$location_id = $app->request()->post('location_id');
            $plant_id = $app->request()->post('plant_id');
            $platform= $app->request()->post('platform');//1-Android ,2-IOS
            $user_id = $app->request()->post('user_id');*/
              // accessToken($user_id); 
             // check for required params
            //verifyRequiredParams(array('location_id','plant_id','user_id','platform'));
            
            $response = array();
            $db = new DbHandler();
            $result=$db->deptLists($location_id,$plant_id);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["deptLists"]=$result['deptlst'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["deptLists"]=array();
            }
        
            echoRespnse(200, $response);
 });






// for attendance we have to implement it here.
$app->post('/attendance', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

             
              $user_id  = $data['user_id'];  
              
              // accessToken($user_id); 
             
            
            $response = array();
            $db = new DbHandler();
            $result=$db->attendance($user_id);

           if ($result['status']==1) 
           {      
                 $response["attendancedetails"]=$result['attendancedetails'];
            }
            else
            {   
                $response["attendancedetails"]=$result['attendancedetails'];
            }
        
            echoRespnse(200, $response);
 });

// for punchInpunchOut we have to implement it here.
$app->post('/punchInpunchOut', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
        $data = json_decode($json, true);
        $result = implode(',',$data);

         
          $user_id  = $data['user_id'];  
          $id  = $data['id']; 
          $punch_note = $data['punch_note'];

           //$punch_out_utc_time1 = getCurrentDateTime();
        
        $punch_out_user_time1 = getCurrentDateTime();
       
        //$punch_in_utc_time1 = getCurrentDateTime();
       
      
        $punch_in_user_time1   = getCurrentDateTime();
       
           // accessToken($user_id);  

         
        $response = array();
        $db = new DbHandler();
        $result=$db->punchInOrOut($id,$user_id,$punch_note,$punch_in_user_time1,$punch_out_user_time1);

       if ($result['status']==1) 
       {      
          $response["status"] =1;
             $response["punchInpunchOutdetails"]=$result['punchInpunchOutdetails'];
        }
        else
        {   
            $response['status'] =0;
            $response["punchInpunchOutdetails"]=$result['punchInpunchOutdetails'];
        }
    
        echoRespnse(200, $response);

       
           
 });




// verify otp for password reset
$app->post('/pwdOtpVerify', 'authenticatedefault', function() use ($app) 
{

            $json = $app->request->getBody();
            $data = json_decode($json, true);

            // reading post params
            // $platform= $data['platform'];//1-Android ,2-IOS
            $user_id = $data['user_id'];
            $otp = md5($data['otp']);

            // echo $otp;
            // exit();
              // accessToken($user_id); 
            // check for required params
            // verifyRequiredParams(array('user_id','otp','platform'));
            
            $response = array();
            $db = new DbHandler();
            $result=$db->pwdOtpVerify($user_id,$otp);
            if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "OTP Verified";
                 $response["otpverified"]=$result['otpverified'];
            }
            else if ($result['status']==2) 
            {
                 $response["status"] =0;
                 $response['message'] = "Your entered Incorrect OTP";
                 $response["otpverified"]=array();
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Invalid OTP';
                $response["otpverified"]=array();
            }
       
            echoRespnse(200, $response);
 });


// password reset function
$app->post('/pwdChange', 'authenticatedefault', function() use ($app) 
{

            $json = $app->request->getBody();
            $data = json_decode($json, true);

            // reading post params
            // $platform= $data['platform'];//1-Android ,2-IOS
            $user_id = $data['user_id'];
            $oldPassword = $data['oldPwd'];
            $newPassword = $data['newPwd'];


  
              // accessToken($user_id); 
            // check for required params
            // verifyRequiredParams(array('user_id','otp','platform'));
            
            $response = array();
            $db = new DbHandler();
            $result=$db->passwordChange($user_id,$oldPassword,$newPassword);
            
            echoRespnse(200, $result);
 });


// Play Store
$app->post('/getPlayStoreUpdate', 'authenticatedefault', function() use ($app) 
{

            $json = $app->request->getBody();
            $data = json_decode($json, true);

           
            
            $response = array();
            $db = new DbHandler();
            $result=$db->getPlayStoreUpdate();

             if ($result['status']==1) 
           {
                 $response["status"] =1;
                 //$response['message'] = "Password changed successfully";
                 $response["playStoreDetails"]=$result['playStoreDetails'];
            }
            else
            {
                $response['status'] =0;
                //$response['message'] = 'Password change unsuccessfull';
                $response["playStoreDetails"]=$result['playStoreDetails'];
            }
            
            echoRespnse(200, $response);
 });


$app->post('/pdfDwnLoad', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
           
            $user_id  = $data['user_id'];
            // accessToken($user_id); 

            $req = $app->request;
            $index_url = $req->getUrl()."".$req->getRootUri();
            $base_url = implode('/',array_slice(explode('/',$index_url),0,-1));
            $pdf_url = $base_url."/include/pdfs/";
            $response = array();
          $db = new DbHandler();
          $result=$db->pdfDwnLoad($user_id);
   

         
            $response["pdf"]=$pdf_url."pdftest.php";
      
        echoRespnse(200, $response);
 });


$app->post('/xlsDwnLoad', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
          
            $user_id  = $data['user_id'];
            // accessToken($user_id); 

            $req = $app->request;
            $index_url = $req->getUrl()."".$req->getRootUri();
            $base_url = implode('/',array_slice(explode('/',$index_url),0,-1));
            $pdf_url = $base_url."/include/excel/";
            $response = array();
          $db = new DbHandler();
          $result=$db->xlsDwnLoad($user_id);
   

         
            $response["pdf"]=$pdf_url."exceltest.php";
      
        echoRespnse(200, $response);
 });

$app->post('/pdfStatusDwnLoad', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
           
         
            $user_id  = $data['user_id'];
            // accessToken($user_id); 

            $req = $app->request;
            $index_url = $req->getUrl()."".$req->getRootUri();
            $base_url = implode('/',array_slice(explode('/',$index_url),0,-1));
            $pdf_url = $base_url."/include/pdfs/";
            $response = array();
          $db = new DbHandler();
          $result=$db->pdfStatusDwnLoad($user_id);
   


            $response["pdf"]=$pdf_url."pdfstatus.php";
      
        echoRespnse(200, $response);
 });

$app->post('/excelStatusDwnLoad', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
           
           
            $user_id  = $data['user_id'];
            // accessToken($user_id); 

            $req = $app->request;
            $index_url = $req->getUrl()."".$req->getRootUri();
            $base_url = implode('/',array_slice(explode('/',$index_url),0,-1));
            $pdf_url = $base_url."/include/excel/";
            $response = array();
          $db = new DbHandler();
          $result=$db->excelStatusDwnLoad($user_id);

            $response["pdf"]=$pdf_url."excelstatus.php";
      
        echoRespnse(200, $response);
 });

$app->post('/pdfMainTypRepDwnLoad', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $user_id  = $data['user_id'];
            // accessToken($user_id); 

            $req = $app->request;
            $index_url = $req->getUrl()."".$req->getRootUri();
            $base_url = implode('/',array_slice(explode('/',$index_url),0,-1));
            $pdf_url = $base_url."/include/pdfs/";
            $response = array();
          $db = new DbHandler();
          $result=$db->pdfMainTypRepDwnLoad($user_id);

            $response["pdf"]=$pdf_url."pdfmainTypeRep.php";
      
        echoRespnse(200, $response);
 });


$app->post('/excelMainTypRepDwnLoad', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
           
           
            $user_id  = $data['user_id'];
            // accessToken($user_id); 

            $req = $app->request;
            $index_url = $req->getUrl()."".$req->getRootUri();
            $base_url = implode('/',array_slice(explode('/',$index_url),0,-1));
            $pdf_url = $base_url."/include/excel/";
            $response = array();
          $db = new DbHandler();
          $result=$db->excelMainTypRepDwnLoad($user_id);
   

         
            $response["pdf"]=$pdf_url."excelmainTypeRep.php";
      
        echoRespnse(200, $response);
 });


// for persnlDetails we have to implement it here.
$app->post('/persnlDetails', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            
            // accessToken($userIdPass); 
             
            $response = array();
            $db = new DbHandler();

            $req = $app->request;
            $hostUrl = $req->getUrl();
            $base_url = $req->getUrl()."".$req->getRootUri()."/";

            // echo $req->getUrl();

            $result1=$db->persnlDetails($userIdPass,$base_url,$hostUrl);




           if ($result1['status']==1) 
           {
                 
                 $response1["status"] =1;
                 $response1['message'] = "successful";
                 $response1["persnlDetails"] = $result1['persnlDetails'];
            }
            else
            {
                //echo "else";
                $response1['status'] =0;
                $response1['message'] = 'No Records Found';
                $response1["persnlDetails"]=array();
            }
        
            echoRespnse(200, $response1);
 });

$app->post('/contactDetails', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


            //accessToken($userIdPass); 
             
            
            $response = array();
            $db = new DbHandler();
            $result1=$db->contactDetails($userIdPass);


        
           if ($result1['status']==1) 
           {
                //echo "if";
                 
                 $response1["status"] =1;
                 $response1['message'] = "successful";
                 $response1["contactDetails"] = $result1['contactDetails'];

            

            }
            else
            {
                //echo "else";
                $response1['status'] =0;
                $response1['message'] = 'No Records Found';
                $response1["contactDetails"]=array();
            }
        
            echoRespnse(200, $response1);
 });


$app->post('/emergencyContacts', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


            //accessToken($userIdPass); 
             
            
            $response = array();
            $db = new DbHandler();
            $result1=$db->emergencyContacts($userIdPass);


        
           if ($result1['status']==1) 
           {
                //echo "if";
                 
                 $response1["status"] =1;
                 $response1['message'] = "successful";
                 $response1["emergencyContacts"] = $result1['emergencyContacts'];

            

            }
            else
            {
                //echo "else";
                $response1['status'] =0;
                $response1['message'] = 'No Records Found';
                $response1["emergencyContacts"]=array();
            }
        
            echoRespnse(200, $response1);
 });


$app->post('/asigndDepdents', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


            //accessToken($userIdPass); 
             
            
            $response = array();
            $db = new DbHandler();
            $result1=$db->asigndDepdents($userIdPass);


        
           if ($result1['status']==1) 
           {
                //echo "if";
                 
                 $response1["status"] =1;
                 $response1['message'] = "successful";
                 $response1["asigndDepdents"] = $result1['asigndDepdents'];

            

            }
            else
            {
                //echo "else";
                $response1['status'] =0;
                $response1['message'] = 'No Records Found';
                $response1["asigndDepdents"]=array();
            }
        
            echoRespnse(200, $response1);
 });



$app->post('/imgrtnRcrds', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


            //accessToken($userIdPass); 
             
            
            $response = array();
            $db = new DbHandler();
            $result1=$db->imgrtnRcrds($userIdPass);


        
           if ($result1['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["imgrtnRcrds"] = $result1['imgrtnRcrds'];

            

            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["imgrtnRcrds"]=array();
            }
        
            echoRespnse(200, $response);
 });


$app->post('/jobDetails', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


            //accessToken($userIdPass); 
             
            
            $response = array();
            $db = new DbHandler();
            $result=$db->jobDetails($userIdPass);


        
           if ($result['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["jobDetails"] = $result['jobDetails'];

            

            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["jobDetails"]=array();
            }
        
            echoRespnse(200, $response);
 });




$app->post('/salaryComponents', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


          //accessToken($userIdPass); 
             
            
            $response = array();
            $db = new DbHandler();
            $result=$db->salaryComponents($userIdPass);


        
           if ($result['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["salaryComponents"] = $result['salaryComponents'];
           

            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["salaryComponents"]=array();
            }
        
            echoRespnse(200, $response);
 });


$app->post('/reportTo', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


              // accessToken($userIdPass); 
             
            
            $response = array();
            $db = new DbHandler();
            $result=$db->reportTo($userIdPass);


        
           if ($result['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["reportTo"] = $result['reportTo'];
           

            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["reportTo"]=array();
            }
        
            echoRespnse(200, $response);
 });


$app->post('/empSubordinatesrepTo', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


              // accessToken($userIdPass); 
             
            
            $response = array();
            $db = new DbHandler();
            $result=$db->empSubordinatesrepTo($userIdPass);


        
           if ($result['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["empSubordinates"] = $result['empSubordinates'];
           

            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["empSubordinates"]=array();
            }
        
            echoRespnse(200, $response);
 });


$app->post('/workExp', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


              // accessToken($userIdPass); 
             
            
            $response = array();
            $db = new DbHandler();
            $result=$db->workExp($userIdPass);


        
           if ($result['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["workExp"] = $result['workExp'];
           

            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["workExp"]=array();
            }
        
            echoRespnse(200, $response);
 });


$app->post('/emplEductn', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


              // accessToken($userIdPass); 
             
            
            $response = array();
            $db = new DbHandler();
            $result=$db->emplEductn($userIdPass);


        
           if ($result['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["emplEductn"] = $result['emplEductn'];
           

            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["emplEductn"]=array();
            }
        
            echoRespnse(200, $response);
 });


$app->post('/empSkills', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


              // accessToken($userIdPass); 
             
            
            $response = array();
            $db = new DbHandler();
            $result=$db->empSkills($userIdPass);


        
           if ($result['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["empSkills"] = $result['empSkills'];
           

            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["empSkills"]=array();
            }
        
            echoRespnse(200, $response);
 });


$app->post('/empLang', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


              // accessToken($userIdPass); 
             
            
            $response = array();
            $db = new DbHandler();
            $result=$db->empLang($userIdPass);


        
           if ($result['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["empLang"] = $result['empLang'];
           

            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["empLang"]=array();
            }
        
            echoRespnse(200, $response);
 });


$app->post('/empLicense', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


            //  accessToken($userIdPass); 
             
            
            $response = array();
            $db = new DbHandler();
            $result=$db->empLicense($userIdPass);


        
           if ($result['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["empLicense"] = $result['empLicense'];
           

            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["empLicense"]=array();
            }
        
            echoRespnse(200, $response);
 });


$app->post('/empMbrshp', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


            //  accessToken($userIdPass); 
             
            
            $response = array();
            $db = new DbHandler();
            $result=$db->empMbrshp($userIdPass);


        
           if ($result['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["empMbrshp"] = $result['empMbrshp'];
           

            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["empMbrshp"]=array();
            }
        
            echoRespnse(200, $response);
 });





$app->post('/depDetlsNew', 'authenticatedefault', function() use ($app) 
{

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            
             $user_id = $data['user_id'];
          
              //accessToken($user_id); 

            $response = array();
            $db = new DbHandler();
            $result=$db->depDetlsNew($user_id);

           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["departmntdetails"]=$result['departmntdetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["departmntdetails"]=array();
            }

            echoRespnse(200, $response);
 });


$app->post('/sendWhatsapp_msg', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
           
            // $platform   = $data['platform'];
            $user_id  = $data['user_id'];

            $country_code = '91';

            $mobile = '9703762356';

            $message = 'whatsapp testing';

            
            
            $response = array();
      $db = new DbHandler();
      $result=$db->sendWhatsapp_msg($country_code, $mobile, $message, $type = 'text');
     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "otp sent successfully";
                 $response["sendOtpDetails"]=$result['sendOtpDetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'otp sending is unsuccessfull';
                $response["sendOtpDetails"]=array();
            }
      
        echoRespnse(200, $response);
 });


$app->post('/whatsapp_test', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
           
            // $platform   = $data['platform'];
            $user_id  = $data['user_id'];

           
            $response = array();
      $db = new DbHandler();
      $result=$db->whatsapp_test();
     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "msg sent successfully";
                 $response["whtsMsgDetails"]=$result['whtsMsgDetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'msg sending is unsuccessfull';
                $response["whtsMsgDetails"]=array();
            }
      
        echoRespnse(200, $response);
 });


$app->post('/SupplierList', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
           
            // $platform   = $data['platform'];
            $user_id  = $data['user_id'];

           
            $response = array();
      $db = new DbHandler();
      $result=$db->SupplierList();
     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "success";
                 $response["SupplierListDetails"]=$result['SupplierListDetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'unsuccessfull';
                $response["SupplierListDetails"]=array();
            }
      
        echoRespnse(200, $response);
 });


$app->post('/CustomerList', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
           
            // $platform   = $data['platform'];
            $user_id  = $data['user_id'];

           
            $response = array();
      $db = new DbHandler();
      $result=$db->CustomerList();
     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "success";
                 $response["CustomerListDetails"]=$result['CustomerListDetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'unsuccessfull';
                $response["CustomerListDetails"]=array();
            }
      
        echoRespnse(200, $response);
 });


$app->post('/employeeDetailsAll', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $user_id  = $data['user_id'];

           
            $response = array();
      $db = new DbHandler();
      
      $result=$db->employeeDetailsAll($user_id);
     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "success";
                 $response["employeeDetailsAll"]=$result['employeeDetailsAll'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'unsuccessfull';
                $response["employeeDetailsAll"]=array();
            }
      
        echoRespnse(200, $response);
 });

    $app->post('/femaleEmployeeDetailsAll', 'authenticatedefault', function() use ($app) 
    {         
         

                $json = $app->request->getBody();
                $data = json_decode($json, true);
                $user_id  = $data['user_id'];

               
                $response = array();
          $db = new DbHandler();
          
          $result=$db->femaleEmployeeDetailsAll($user_id);
         //$user_details=$db->userDetails($user_id);
               if ($result['status']==1) 
               {
                     $response["status"] =1;
                     $response['message'] = "success";
                     $response["employeeDetailsAll"]=$result['employeeDetailsAll'];
                }
                else
                {
                    $response['status'] =0;
                    $response['message'] = 'unsuccessfull';
                    $response["employeeDetailsAll"]=array();
                }
          
            echoRespnse(200, $response);
     });


$app->post('/employeeDetailsByRoleAll', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $user_id  = $data['user_id'];

           
            $response = array();
      $db = new DbHandler();
      
      $result=$db->employeeDetailsByRoleAll($user_id);
     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "success";
                 $response["employeeDetailsAll"]=$result['emplist'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'unsuccessfull';
                $response["employeeDetailsAll"]=array();
            }
      
        echoRespnse(200, $response);
 });

// for ticket/add we have to implement it here.
// for ticket/add we have to implement it here.
$app->post('/ticketAdd', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
            $data = json_decode($json, true);
            // $result = implode(',',$data);
            
              $user_id  = $data['userid'];

              $locationId  = $data['locationid'];
              // print_r($data); $locationId;die();
              $plantId  = $data['plantid'];
              $usrdeptId  = $data['usrdeptid'];
              $notifytoId = 1;
              $statusId   = 1;
              $funclocId  = $data['funclocid'];
              $eqipmntId  = $data['eqipmntid'];
              $typofisId  = $data['typofisId'];
              $subject  = $data['subject'];
              $description  = $data['description'];
              $prtyId  = $data['prtyid'];
              $svrtyId  = $data['svrtyid'];
              $reportedBy  = $data['reportedby'];
              $submitted_by_emp_number  = $data['subbyempnum'];
              $submitted_by_name  = $data['submittedbyname'];
              $attachmentId  = $data['attachmentId'];
            
            $reportedOn = getCurrentDateTime();
            
            $submitted_on = getCurrentDateTime();
        
            $response = array();
            $db = new DbHandler();
            $result=$db->ticketAdd($locationId,$plantId,$usrdeptId,$notifytoId,$statusId,$funclocId,$eqipmntId,$typofisId,$subject,$description,$prtyId,$svrtyId,$reportedBy,$submitted_by_emp_number,$submitted_by_name,$reportedOn,$submitted_on,$user_id,$attachmentId);

            
           if ($result['status']==1) 
           {    

                $response["status"] =1;
                $response['message'] = "successful";
                $response["ticketid"]=$result['ticketid'];

                $data = $db->ticketIdDetails($result['ticketid']);

               /* $es = new EmailService();
                $result = $es->sendEmailWhenSubmitted($data['ticket_Details']);
                $ss = new SmsService();
                $result = $ss->sendSmsWhenSubmitted($data['ticket_Details']);*/

            }else  if ($result['status']==2) 
           {    

                $response["status"] =2;
                $response['message'] = "successful";
                $response["ticketid"]=$result['ticketid'];

            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["ticketid"]=array();
            }

            echoRespnse(200, $response);
 });

// for ticketpriority we have to implement it here.
$app->post('/ticketpriority', 'authenticatedefault', function() use ($app) 
{       

         $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            
              $user_id  = $data['userid'];
              $type_of_issue_id  = $data['type_of_issue'];
       /* $platform= $app->request()->post('platform');//1-Android ,2-IOS
        $user_id = $app->request()->post('user_id');*/
              //accessToken($user_id) 
        //verifyRequiredParams(array('user_id','platform'));

            $db = new DbHandler();
            $result=$db->ticketpriority($type_of_issue_id);

           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["ticketpriority"]=$result['ticketpriority'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["ticketpriority"]=array();
            }
        
            echoRespnse(200, $response);
 });

// for ticketseverity we have to implement it here.
$app->post('/ticketseverity', 'authenticatedefault', function() use ($app) 
{       

             $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            
              $user_id  = $data['userid'];
            /*$platform= $app->request()->post('platform');//1-Android ,2-IOS
            $user_id = $app->request()->post('user_id');*/
              //accessToken($user_id) 
            //verifyRequiredParams(array('user_id','platform'));

            $db = new DbHandler();
            $result=$db->ticketseverity();
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["ticketseverity"]=$result['ticketseverity'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["ticketseverity"]=array();
            }
        
            echoRespnse(200, $response);
 });

// for machinestatus we have to implement it here.
$app->post('/machinestatus', 'authenticatedefault', function() use ($app) 
{           
         $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            
              $user_id  = $data['userid'];
        /*$platform= $app->request()->post('platform');//1-Android ,2-IOS
        $user_id = $app->request()->post('user_id');*/
              //accessToken($user_id) 
        //verifyRequiredParams(array('user_id','platform'));
            
        $db = new DbHandler();
        $result=$db->machineStatus();
       
        if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["machineStatus"]=$result['machineStatus'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["machineStatus"]=array();
            }
        
            echoRespnse(200, $response);
 });

// for "EnMNew"Tasks we have to implement it here.
$app->post('/EnMNewTasks', 'authenticatedefault', function() use ($app) 
{
            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

             $location_id  = $data['locationid'];
            $user_id = $data['userid'];

            //$platform   = $result[0];
            //$location_id  = $result[2];
            //$user_id = $result[4];
           //   //accessToken($user_id) 


            $response = array();
            $db = new DbHandler();
            $result=$db->EnMNewTasks($user_id,$location_id);

           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["emTasks"]=$result['emTasks'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["emTasks"]=array();
            }

            echoRespnse(200, $response);
 });


// for EnMEngTechTasks we have to implement it here.
$app->post('/EnMEngTechTasks', 'authenticatedefault', function() use ($app) 
{

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
             // reading post params
             //$platform  = $data['platform'];
            $type_id = $data['type_id'];
             $user_id = $data['userid'];

           /* $platform   = $app->request()->post('platform');//1-Android ,2-IOS
            $type_id  = $app->request()->post('type_id');
            $user_id = $app->request()->post('user_id');*/
              //accessToken($user_id) 

             // check for required params
            //verifyRequiredParams(array('platform','user_id','type_id'));

            $response = array();
            $db = new DbHandler();
            $result=$db->EnMEngTechTasks($user_id,$type_id );

           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["taskslist"]=$result['taskslist'];
           }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["taskslist"]=array();
            }

            echoRespnse(200, $response);
 });

// for EngNewTskEmpnum we have to implement it here.
$app->post('/EngNewTskEmpnum', 'authenticatedefault', function() use ($app) 
{

             $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
             // reading post params
              $emp_number = $data['emp_number'];
             $user_id = $data['userid'];
            //$platform   = $app->request()->post('platform');//1-Android ,2-IOS
            /*$emp_number  = $app->request()->post('emp_number');
            $user_id = $app->request()->post('user_id');*/
              //accessToken($user_id) 

             // check for required params
            //verifyRequiredParams(array('platform','user_id','emp_number'));

            $response = array();
            $db = new DbHandler();
            $result=$db->EngNewTskEmpnum($emp_number);

           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["engNewTasks"]=$result['engNewTasks'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["engNewTasks"]=array();
            }

            echoRespnse(200, $response);
 });

// for InPrgTsksLstEmpnum we have to implement it here.
$app->post('/InPrgTsksLstEmpnum', 'authenticatedefault', function() use ($app) 
{

             $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
             // reading post params
            $emp_number = $data['emp_number'];
             $user_id = $data['userid'];
           /* $platform   = $app->request()->post('platform');//1-Android ,2-IOS
            $emp_number  = $app->request()->post('emp_number');
            $user_id = $app->request()->post('user_id');*/
              //accessToken($user_id) 

             // check for required params
            //verifyRequiredParams(array('platform','emp_number','user_id'));

            $response = array();
            $db = new DbHandler();
            $result=$db->InPrgTsksLstEmpnum($emp_number);

           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["inprogressTasks"]=$result['inprogressTasks'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["inprogressTasks"]=array();
            }

            echoRespnse(200, $response);
 });

// for TechTsksLstEmpnum we have to implement it here.
$app->post('/EngTechTsksLst', 'authenticatedefault', function() use ($app) 
{

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
             // reading post params
             $emp_number = $data['emp_number'];
             $user_id = $data['userid'];
           /* $platform   = $app->request()->post('platform');//1-Android ,2-IOS
            $emp_number  = $app->request()->post('emp_number');
            $user_id = $app->request()->post('user_id');*/
             // //accessToken($user_id) 

             // check for required params
            //verifyRequiredParams(array('platform','user_id','emp_number'));

            $response = array();
            $db = new DbHandler();
            $result=$db->EngTechTsksLst($emp_number);

           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["EngTechTasks"]=$result['EngTechTasks'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["EngTechTasks"]=array();
            }

            echoRespnse(200, $response);
 });



$app->post('/vehicle/book', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            
              $user_id  = $data['user_id'];
              $booked_by_id  = $data['booked_by_id'];
              $response_id  = $data['response_id'];
              $booked_for_id  = $data['booked_for_id'];
              $booked_for_value  = $data['booked_for_value'];
              $origin  = $data['origin'];
              $destination  = $data['destination'];
              $pick_up_point  = $data['pick_up_point'];
              $latitude  = $data['latitude'];
              $longitude  = $data['longitude'];
              $reason  = $data['reason'];
              $from_date  = $data['from_date'];
              $from_time  = $data['from_time'];
              $to_date  = $data['to_date'];
              $to_time  = $data['to_time'];
              $passengers_id  = $data['passengers_id'];
              $round_trip  = $data['round_trip'];
              $status_id  = $data['status_id'];
              $submitted_on  = getCurrentDateTime();
            
           
              // accessToken($user_id); 


            $response = array();
            $db = new DbHandler();
            $result=$db->bookVehicle($user_id,$booked_by_id,$response_id,$booked_for_id,$booked_for_value,$origin,$destination,$pick_up_point,$latitude,$longitude,$reason,$from_date,$from_time,$to_date,$to_time,$passengers_id,$round_trip,$status_id,$submitted_on);

            
           if ($result['status']==1) 
           {    

                $response["status"] =1;
                $response['message'] = "successful";
                /*$response["ticketid"]=$result['ticketid'];

                $data = $db->ticketIdDetails($result['ticketid']);

                $es = new EmailService();
                $result = $es->sendEmailWhenSubmitted($data['ticket_Details']);
                $ss = new SmsService();
                $result = $ss->sendSmsWhenSubmitted($data['ticket_Details']);*/

            }else  if ($result['status']==2) 
           {    

                $response["status"] =2;
                $response['message'] = "successful";
                //$response["ticketid"]=$result['ticketid'];

            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                //$response["ticketid"]=array();
            }

            echoRespnse(200, $response);
 });

// for actionlog/logadd we have to implement it here.
$app->post('/actionlog/vehicleLogAdd', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            
              $user_id  = $data['user_id'];
              $bookVehicle_id = $data['bookVehicle_id'];//1-Android ,2-IOS
              $notes = $data['notes'];
              $status_id = $data['status_id'];
              $performed_by_id = $data['performed_by_id'];
              $performed_by_name = $data['performed_by_name'];
              $created_by_user_id = $data['created_by_user_id'];
            $submitted_on       = getCurrentDateTime();
            //$user_id = $app->request()->post('user_id');
              // accessToken($user_id); 

            $response = array();
            $db = new DbHandler();
            $result=$db->bkVehlogAdd($user_id,$bookVehicle_id,$notes,$status_id,$performed_by_id,$performed_by_name,
                                      $created_by_user_id,$submitted_on);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 // $response["log"]=$result['log'];

                /* $data = $db->ticketIdDetails($ticket_id);

                 $data['ticket_Details']['Act_status'] = $status_id;
                 $data['ticket_Details']['Act_accepted_by'] = $accepted_by;
                 $data['ticket_Details']['Act_forward_from'] = $forward_from;
                 $data['ticket_Details']['Act_forward_to'] = $forward_to;
*/
                 echoRespnse(200, $response);

                 // print_r($data['ticket_Details']);
                 // exit();
                /* $status_ids=array(1,11);
                if (!in_array($status_id, $status_ids)){
                  $es = new EmailService();
                  $result = $es->sendEmailWhenAcknowledged($data['ticket_Details']);

                  $ss = new SmsService();
                  $result = $ss->sendSmsWhenAcknowledged($data['ticket_Details']);
                }*/

            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["log"]=array();

                echoRespnse(200, $response);
            }

 });


// for bookVehicledetails we have to implement it here.
$app->post('/bookVehicleNotifDetails', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


             // reading post params
          /*  $userIdPass = $app->request()->post('user_id');
            $platform= $app->request()->post('platform');*///1-Android ,2-IOS
              // accessToken($userIdPass); 
             // check for required params
            //verifyRequiredParams(array('user_id','platform'));l
            
            $response = array();
            $db = new DbHandler();
            $result=$db->bkVhclNotfctnDtls($userIdPass);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["bkVhclNotfctnDtls"]=$result['bkVhclNotfctnDtls'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["bkVhclNotfctnDtls"]=array();
            }
        
            echoRespnse(200, $response);
 });


$app->post('/bookVehicleDetails', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            


             // reading post params
          /*  $userIdPass = $app->request()->post('user_id');
            $platform= $app->request()->post('platform');*///1-Android ,2-IOS
              // accessToken($userIdPass); 
             // check for required params
            //verifyRequiredParams(array('user_id','platform'));
            
            $response = array();
            $db = new DbHandler();
            $result=$db->bookVehicleDetails($userIdPass);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["bookVehicleDetails"]=$result['bookVehicleDetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["bookVehicleDetails"]=array();
            }
        
            echoRespnse(200, $response);
 });


$app->post('/approveOrReject', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            
            $status_id  = $data['status_id'];

            $bookVehicle_id  = $data['bookVehicle_id'];

            $comment  = $data['comment'];

            $submitted_on = getCurrentDateTime();

             // reading post params
          /*  $userIdPass = $app->request()->post('user_id');
            $platform= $app->request()->post('platform');*///1-Android ,2-IOS
              // accessToken($userIdPass); 
             // check for required params
            //verifyRequiredParams(array('user_id','platform'));
            
            $response = array();
            $db = new DbHandler();
            $result=$db->approveOrReject($userIdPass,$bookVehicle_id,$status_id,$comment,$submitted_on);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Successful";
                 $response["approveOrReject"]=$result['approveOrReject'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["approveOrReject"]=array();
            }
        
            echoRespnse(200, $response);
 });



$app->post('/bkVhclDetlsById', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            
             $bookVehicle_id  = $data['bookVehicle_id'];

             // reading post params
          /*  $userIdPass = $app->request()->post('user_id');
            $platform= $app->request()->post('platform');*///1-Android ,2-IOS
              // accessToken($userIdPass); 
             // check for required params
            //verifyRequiredParams(array('user_id','platform'));
            
            $response = array();
            $db = new DbHandler();
            $result=$db->bkVhclDetlsById($userIdPass,$bookVehicle_id);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["bkVhclDetlsById"]=$result['bkVhclDetlsById'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["bkVhclDetlsById"]=array();
            }
        
            echoRespnse(200, $response);
 });

$app->post('/vehicleList', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            
            $bookVehicle_id = $data['bookVehicle_id'];

             // reading post params
          /*  $userIdPass = $app->request()->post('user_id');
            $platform= $app->request()->post('platform');*///1-Android ,2-IOS
              // accessToken($userIdPass); 
             // check for required params
            //verifyRequiredParams(array('user_id','platform'));
            
            $response = array();
            $db = new DbHandler();
            $result=$db->vehicleList($userIdPass,$bookVehicle_id);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["vehicleList"]=$result['vehicleList'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["vehicleList"]=array();
            }
        
            echoRespnse(200, $response);
 });


$app->post('/assignOrReject', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userIdPass  = $data['user_id'];
            
            $status_id  = $data['status_id'];

            $bookVehicle_id  = $data['bookVehicle_id'];

            $vehicle_id  = $data['vehicle_id'];

            $driver_id = $data['driver_id'];

            $status_id = $data['status_id'];

            $comment  = $data['comment'];

            $submitted_on = getCurrentDateTime();

              // accessToken($userIdPass); 
          
            
            $response = array();
            $db = new DbHandler();
            $result=$db->assignOrReject($userIdPass,$bookVehicle_id,$vehicle_id,$driver_id,$status_id,$comment,$submitted_on);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Successful";
                 $response["assignOrReject"]=$result['assignOrReject'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["assignOrReject"]=array();
            }
        
            echoRespnse(200, $response);
 });


$app->post('/vehicleTrack', 'authenticatedefault', function() use ($app) 
{           
           $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            
              $user_id  = $data['user_id'];
              $status_id  = $data['status_id'];
              $book_vehicle_id  = $data['book_vehicle_id'];
              $vehicle_id  = $data['vehicle_id'];
              $s_latitude  = $data['s_latitude'];
              $s_longitude  = $data['s_longitude'];
              $d_latitude  = $data['d_latitude'];
              $d_longitude  = $data['d_longitude'];
              $c_latitude = $data['c_latitude'];
              $c_longitude = $data['c_longitude'];
              
         
              // accessToken($user_id); 

              // echo $status_id;die();

            $response = array();
            $db = new DbHandler();
            $result=$db->vehicleTrack($user_id,$status_id,$book_vehicle_id,$vehicle_id,$s_latitude,$s_longitude,$d_latitude,$d_longitude,$c_latitude,$c_longitude);

            
           if ($result['status']==1) 
         
           {    

                $response["status"] =1;
                $response['message'] = "successful";
                $response["trip_id"]=$result['trip_id'];
                $response["status_id"]=$result['status_id'];

            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["trip_id"]=array();
                $response["status_id"]=$result['status_id'];
            }

            echoRespnse(200, $response);
           
 });


$app->post('/vehicleTrackLog', 'authenticatedefault', function() use ($app) 
{           


           $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            
              $user_id  = $data['user_id'];
              $trip_id = $data['trip_id'];//1-Android ,2-IOS
              $status_id = $data['status_id'];
              $latitude = $data['latitude'];
              $longitude = $data['longitude'];
              $book_vehicle_id = $data['book_vehicle_id'];
             
         
            //$user_id = $app->request()->post('user_id');
              // accessToken($user_id); 

            
            $response = array();
            $db = new DbHandler();
            $result=$db->vehicleTrackLog($user_id,$trip_id,$status_id,$latitude,$longitude,$book_vehicle_id);

          if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                
                 echoRespnse(200, $response);

            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["log"]=array();

                echoRespnse(200, $response);
            }
           
 });

$app->post('/vehicleLatLongUpdate', 'authenticatedefault', function() use ($app) 
{           


           $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            
              $user_id  = $data['user_id'];
              $trip_id = $data['trip_id'];//1-Android ,2-IOS
              $status_id = $data['status_id'];
              $latitude = $data['latitude'];
              $longitude = $data['longitude'];
              $book_vehicle_id = $data['book_vehicle_id'];
             
         
            //$user_id = $app->request()->post('user_id');
              // accessToken($user_id); 

            
            $response = array();
            $db = new DbHandler();
            $result=$db->vehicleLatLongUpdate($user_id,$trip_id,$status_id,$latitude,$longitude,$book_vehicle_id);

           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                
                 echoRespnse(200, $response);

            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["log"]=array();

                echoRespnse(200, $response);
            }
           
 });

//Sreekanth
// Permission Module

$app->post('/getPrmsnLstByEmp','authenticatedefault',function() use ($app){
  //$data = array();
  $response = array();
  $json = $app->request->getBody();
  $data = json_decode($json, true);
  
  $user_id  = $data['user_id'];
  $db = new DbHandler();
  $result = $db->getPrmsnLstByEmp($user_id);
  if(!empty($result['prmsnList'])){
  $response['prmsnList'] = $result['prmsnList'];

  }else{
  $response['prmsnList'] =array();

  }
 
  echoRespnse(200,$response);
});

$app->post('/subPrmsnList','authenticatedefault', function() use ($app)
{
  $json = $app->request->getBody();
  $data = json_decode($json, true);
  $supervisor = $data['user_id'];

  // echo $supervisor;die();
  $db = new DbHandler();
  $result = $db->subPrmsnList($supervisor);
  $response = array();
  $response['permList'] = $result['permList'];
  echoRespnse(200,$response);
});

$app->post('/permsnAdd', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
             $data = json_decode($json, true);

             $date  = $data['date'];
             $fromTime  = $data['from_time'];
             $toTime  = $data['to_time'];
              
             $reason  = $data['reason'];
             $statusId  = $data['statusId'];
             $submittedby = $data['submitted_by'];

             $submittedon = date('Y-m-d');
             $comment = $data['comment'];

            $response = array();
            $db = new DbHandler();
            $result = $db->permsnAdd($date,$fromTime,$toTime,$reason,$statusId,$submittedby,$submittedon,$comment);

            $response = $result;
            if ($result['status'] !=1) 
            {    

                $response["status"] =1;
                $response['message'] = "successful";
                
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                //$response["permissionAdd"]=array();
            }

            echoRespnse(200, $response);
 });

$app->post('/permsnAssign', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
             $data = json_decode($json, true);

             $date  = $data['date'];
             $fromTime  = $data['from_time'];
             $toTime  = $data['to_time'];
              
             $reason  = $data['reason'];
             $statusId  = $data['statusId'];
             $submittedby = $data['submitted_by'];

             $submittedon = date('Y-m-d');
             $comment = $data['comment'];

            $response = array();
            $db = new DbHandler();
            $result = $db->permsnAssign($date,$fromTime,$toTime,$reason,$statusId,$submittedby,$submittedon,$comment);

            $response = $result;
            if ($result['status'] !=1) 
            {    

                $response["status"] =1;
                $response['message'] = "successful";
                
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                //$response["permissionAdd"]=array();
            }

            echoRespnse(200, $response);
 });

$app->post('/permsnUpdate', 'authenticatedefault', function() use ($app) 
{           
             $json = $app->request->getBody();
             $data = json_decode($json, true);
             $result = implode(',',$data);

             $user_id = $data['user_id'];
             $permissionId  = $data['permissionId'];
             $statusId  = $data['statusId'];
             $comment = $data['comment'];

            // echo $user_id;die();
             // $submittedon = getCurrentDateTime();
             // $submittedon = '2021-10-16';
             $submittedon = date('Y-m-d H:i:s');

             // echo $statusId;die;

            $response = array();
            $db = new DbHandler();
            $result = $db->permsnUpdate($permissionId,$statusId,$user_id,$submittedon,$comment);

            // $response = $result;
            if ($result['status'] ==1) 
            {    

                $response["status"] =1;
                $response['message'] = "successful";
                
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                //$response["permissionAdd"]=array();
            }

            echoRespnse(200, $response);
 });



$app->post('/permsnLogAdd', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            
              $user_id  = $data['user_id'];
              $permission_id = $data['permission_id'];
              $date = $data['date'];//1-Android ,2-IOS
              $from_time = $data['from_time'];
              $to_time = $data['to_time'];
              $statusId = $data['statusId'];
              $submitted_by = $data['submitted_by'];
              
              
            $submitted_on       = getCurrentDateTime();
            //$user_id = $app->request()->post('user_id');
              // accessToken($user_id); 

             // check for required params
            //verifyRequiredParams(array('user_id','platform','ticket_id','accepted_by','forward_from','forward_to','created_by_user_id','status_id','priority_id','severity_id','comment','machine_status','submitted_by_name','submitted_by_emp_number','root_cause_id','response_id'));

            $response = array();
            $db = new DbHandler();
            $result=$db->permsnLogAdd($user_id,$permission_id,$statusId,$submitted_by,$submitted_on,$date,$comment);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["log"]=$result['log'];

                

            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["log"]=array();

                echoRespnse(200, $response);
            }

});

$app->post('/permissionReason', 'authenticatedefault', function() use ($app) 
{           

        $json = $app->request->getBody();
            $data = json_decode($json, true);
            // $result = implode(',',$data);

             
            // $user_id  = $data['user_id'];

      /*$platform= $app->request()->post('platform');//1-Android ,2-IOS
        $user_id = $app->request()->post('user_id');*/
              // accessToken($user_id); 
             // check for required params
            //verifyRequiredParams(array('user_id','platform'));
          
            $response = array();
            $db = new DbHandler();
            $result=$db->permissionReason();
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["permissionReason"]=$result['permissionReason'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["permissionReason"]=array();
            }
        
            echoRespnse(200, $response);
});


$app->post('/permissionStatus', 'authenticatedefault', function() use ($app) 
{           

        $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

             
            //  $user_id  = $data['user_id'];

      /*$platform= $app->request()->post('platform');//1-Android ,2-IOS
        $user_id = $app->request()->post('user_id');*/
             // accessToken($user_id); 
             // check for required params
            //verifyRequiredParams(array('user_id','platform'));
          
            $response = array();
            $db = new DbHandler();
            $result=$db->permissionStatus();
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["permissionStatus"]=$result['permissionStatus'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["permissionStatus"]=array();
            }
        
            echoRespnse(200, $response);
});
// Sreekanth
$app->post('/leaveCount', 'authenticatedefault', function() use ($app) 
{     

        $json = $app->request->getBody();
            $data = json_decode($json, true);
            //$result = implode(',',$data);
             
              $user_id  = $data['user_id'];
              $emp_number  = $data['emp_number'];
              $leaveType  = $data['leaveType'];

              // accessToken($user_id);           
          
            $response = array();
            $db = new DbHandler();
            $result=$db->leaveCountNew($user_id,$emp_number,$leaveType);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["leaveCount"]=$result['leaveCount'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["leaveCount"]=array();
            }
        
            echoRespnse(200, $response);
});
//Chandra sekhar //
$app->post('/leaveCountOld', 'authenticatedefault', function() use ($app) 
{     

        $json = $app->request->getBody();
            $data = json_decode($json, true);
            //$result = implode(',',$data);
             
              $user_id  = $data['user_id'];
              $emp_number  = $data['emp_number'];
              $leaveType  = $data['leaveType'];

              // accessToken($user_id);           
          
            $response = array();
            $db = new DbHandler();
            $result=$db->leaveCount($user_id,$emp_number,$leaveType);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["leaveCount"]=$result['leaveCount'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["leaveCount"]=array();
            }
        
            echoRespnse(200, $response);
});

$app->post('/approvedLeavelist', 'authenticatedefault', function() use ($app) 
{     

        $json = $app->request->getBody();
            $data = json_decode($json, true);
            
            $user_id  = $data['user_id'];
            $status  = $data['status'];
              
            $response = array();
            $db = new DbHandler();
            $result=$db->subordinateLeavelist($user_id,$status);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["subOrdinateleaves"]=$result['subOrdinateleaves'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["subOrdinateleaves"]=array();
            }
        
            echoRespnse(200, $response);
});

$app->post('/MyLeavelist', 'authenticatedefault', function() use ($app) 
{     

        $json = $app->request->getBody();
            $data = json_decode($json, true);
            
            $user_id  = $data['user_id'];
            $status  = $data['status'];
              
            $response = array();
            $db = new DbHandler();
            $result=$db->MyLeavelist($user_id,$status);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["MyLeaves"]=$result['MyLeaves'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["MyLeaves"]=array();
            }
        
            echoRespnse(200, $response);
});

$app->post('/appNewNotificationsCount', 'authenticatedefault', function() use ($app) 
{     

        $json = $app->request->getBody();
            $data = json_decode($json, true);
            
            $user_id  = $data['user_id'];;
              
            $response = array();
            $db = new DbHandler();
            $result=$db->appNewNotificationsCount($user_id);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["leaves_count"]=$result['leaves_count'];
                 $response["permissions_count"]=$result['perms_count'];
                 $response["ot_count"]=$result['ot_count'];
                 $response["requisition_count"]=$result['requisition_count'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["leaves_count"]=0;
                $response["permissions_count"]=0;
                $response["ot_count"]=0;
                $response["requisition_count"]=0;
            }
        
            echoRespnse(200, $response);
});

$app->post('/employeeByDept', 'authenticatedefault', function() use ($app) 
{     

        $json = $app->request->getBody();
            $data = json_decode($json, true);
            
              $departmentId  = $data['department_id'];
      
              
            $response = array();
            $db = new DbHandler();
            $result=$db->employeeByDept($departmentId);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["employeeByDetails"]=$result['employeeByDetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["employeeByDetails"]=array();
            }
        
            echoRespnse(200, $response);
});

$app->post('/subordinateOTlist', 'authenticatedefault', function() use ($app) 
{     

        $json = $app->request->getBody();
            $data = json_decode($json, true);
            
            $user_id  = $data['user_id'];
            $status  = $data['status'];
              
            $response = array();
            $db = new DbHandler();
            $result=$db->subordinateOTlist($user_id,$status);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["subOTlists"]=$result['subOTlists'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["subOTlists"]=array();
            }
        
            echoRespnse(200, $response);
});

// Chandra sekhar

$app->post('/getCalenderEventsList', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      // $result = implode(',',$data);
     
      $response = array();
      $db = new DbHandler();
      $user_id  = $data['user_id'];
      $filter_id  = $data['filter_id'];

      $result=$db->getCalenderEventsList($user_id,$filter_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response["getCalenderEventsList"]=$result['getCalenderEventsList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response["getCalenderEventsList"]=array();
      }

      echoRespnse(200, $response);
 });

$app->post('/getBookMeetingRoomList', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      // $result = implode(',',$data);
     
      $response = array();
      $db = new DbHandler();
      $user_id  = $data['user_id'];

      $result=$db->getBookMeetingRoomList($user_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response["bookMeetingRoom"]=$result['bookMeetingRoom'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response["bookMeetingRoom"]=array();
      }

      echoRespnse(200, $response);
 });

$app->post('/birthdaysList', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      // $result = implode(',',$data);
     
      $response = array();
      $db = new DbHandler();
      $user_id  = $data['user_id'];

      $result=$db->getBirthdaysList($user_id);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response["birthList"]=$result['birthList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response["birthList"]=array();
      }

      echoRespnse(200, $response);
 });

// equipmentTracking

$app->post('/equipmentTracking', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
          //  $result = implode(',',$data);
           
            // $platform   = $data['platform'];
            $user_id  = $data['user_id'];
            $status_id  = $data['status_id'];
            $equipment_id  = $data['equipment_id'];
            $track_id  = $data['track_id'];
            $comments  = $data['comments'];
            $isWorking  = $data['isWorking'];
            $isSited  = $data['isSited'];

           
            $response = array();
      $db = new DbHandler();
      
      $result=$db->equipmentTracking($user_id,$status_id,$equipment_id,$track_id,$comments,$isWorking,$isSited);
     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Success";
                 // $response["equipmentTracking"]=$result['log'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Failed';
                // $response["equipmentTracking"]=array();
            }
      
        echoRespnse(200, $response);
});

$app->post('/assignedEquipmentList', 'authenticatedefault', function() use ($app) 
{         
    $json = $app->request->getBody();
    $data = json_decode($json, true);
    $result = implode(',',$data);
    // $user_id  = $data['user_id'];
    $equipment_id  = $data['equipment_id'];
 
  $response = array();
  $db = new DbHandler();

  $result=$db->assignedEquipmentList($equipment_id);
 if ($result['status']==1) 
 {
       $response["status"] =1;
       $response['message'] = "success";
       $response["assigned_equipment_list"]=$result['assignedEquipmentList'];
  }
  else
  {
      $response['status'] =0;
      $response['message'] = 'unsuccessfull';
      $response["assigned_equipment_list"]=array();
  }

  echoRespnse(200, $response);
 });

$app->post('/funcLocation', 'authenticatedefault', function() use ($app) 
{         
    $json = $app->request->getBody();
    $data = json_decode($json, true);
    $result = implode(',',$data);

    // $user_id  = $data['user_id'];
    $user_id  = $data['user_id'];
    $department_id   = $data['department_id'];
    $parent_id  = 0;
    $level = 0;
    
    $response = array();
    $db = new DbHandler();
    $result=$db->funcLocation($department_id,$parent_id,$level);

   if ($result['status']==1) 
   {
         $response["status"] =1;
         $response['message'] = "successful";
         $response["funcLocation"]=$result['funcLocation'];
    }
    else
    {
        $response['status'] =0;
        $response['message'] = 'No Records Found';
        $response["funcLocation"]=array();
    }

    echoRespnse(200, $response);
});

$app->post('/funcLocation', 'authenticatedefault', function() use ($app) 
{         
    $json = $app->request->getBody();
    $data = json_decode($json, true);
    $result = implode(',',$data);

    // $user_id  = $data['user_id'];
    $user_id  = $data['user_id'];
    $department_id   = $data['department_id'];
    $parent_id  = $data['parent_id'];
    $level = 1;
    
    $response = array();
    $db = new DbHandler();
    $result=$db->funcLocation($department_id,$parent_id,$level);

   if ($result['status']==1) 
   {
         $response["status"] =1;
         $response['message'] = "successful";
         $response["funcLocation"]=$result['funcLocation'];
    }
    else
    {
        $response['status'] =0;
        $response['message'] = 'No Records Found';
        $response["funcLocation"]=array();
    }

    echoRespnse(200, $response);
 });

$app->post('/subfuncLocation', 'authenticatedefault', function() use ($app) 
{         
    $json = $app->request->getBody();
    $data = json_decode($json, true);
    $result = implode(',',$data);

    // $user_id  = $data['user_id'];
    $user_id  = $data['user_id'];
    $department_id   = $data['department_id'];
    $parent_id  = 0;
    $level = 0;
    
    $response = array();
    $db = new DbHandler();
    $result=$db->funcLocation($department_id,$parent_id,$level);

   if ($result['status']==1) 
   {
         $response["status"] =1;
         $response['message'] = "successful";
         $response["funcLocation"]=$result['funcLocation'];
    }
    else
    {
        $response['status'] =0;
        $response['message'] = 'No Records Found';
        $response["funcLocation"]=array();
    }

    echoRespnse(200, $response);
 });

// for equipmentlist we have to implement it here.
$app->post('/equipmentlist', 'authenticatedefault', function() use ($app) 
{               
            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

             
              $user_id  = $data['user_id'];
              $location_id   = $data['location_id'];
              $plant_id   = $data['plant_id'];
              $department_id   = $data['department_id'];
              $functional_location_id   = $data['functional_location_id'];
            /* $platform= $app->request()->post('platform');//1-Android ,2-IOS
            $user_id = $app->request()->post('user_id');*/
              //accessToken($user_id) 
             // check for required params
            //verifyRequiredParams(array('user_id','platform'));      
            $response = array();
            $db = new DbHandler();
            $result=$db->equipmentlist($location_id,$plant_id,$department_id,$functional_location_id);

           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["equipmentlist"]=$result['equipmentlist'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["equipmentlist"]=array();
            }
        
            echoRespnse(200, $response);
 });

// for equipmentlist we have to implement it here.
$app->post('/equipmentIdDetails', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

             
              $user_id  = $data['user_id'];  
              $equipment_id  = $data['equipment_id'];  
             // reading post params
          /*  $equipment_id = $app->request()->post('equipment_id');
            $platform= $app->request()->post('platform');//1-Android ,2-IOS
            $user_id = $app->request()->post('user_id');*/
              //accessToken($user_id) 
             // check for required params
            //verifyRequiredParams(array('equipment_id','user_id','platform'));
            
            $response = array();
            $db = new DbHandler();
            $result=$db->equipmentiddetails($equipment_id);

           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["equipmentiddetails"]=$result['equipmentiddetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["equipmentiddetails"]=array();
            }
        
            echoRespnse(200, $response);
 });


// for typeofissue we have to implement it here.
$app->post('/typeofissue', 'authenticatedefault', function() use ($app) 
{           

             $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

             
              $user_id  = $data['user_id'];
              $equipment_id  = $data['equipment_id'];  
             // reading post params
           /* $equipment_id = $app->request()->post('equipment_id');
            $platform= $app->request()->post('platform');//1-Android ,2-IOS
            $user_id = $app->request()->post('user_id');*/
              //accessToken($user_id) 
             // check for required params
            //verifyRequiredParams(array('equipment_id','user_id','platform'));
            
            $response = array();
            $db = new DbHandler();
            $result=$db->typeofissue($equipment_id);

           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["typeofissue"]=$result['typeofissue'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["typeofissue"]=array();
            }
        
            echoRespnse(200, $response);
 });


// for ticketDetails we have to implement it here.

$app->post('/ticketDetails', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            $userIdPass  = $data['user_id'];
                         
            $response = array();
            $db = new DbHandler();
            $result=$db->ticketDetails($userIdPass);
        
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["ticketDetails"]=$result['ticketDetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["ticketDetails"]=array();
            }
        
            echoRespnse(200, $response);
 });




// for ticketdetails we have to implement it here.
$app->post('/ticketIdDetails', 'authenticatedefault', function() use ($app) 
{        

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $ticket_id  = $data['ticket_id'];
            $user_id  = $data['user_id'];

             // reading post params
           /* $ticket_id = $app->request()->post('ticket_id');
            $platform= $app->request()->post('platform');//1-Android ,2-IOS
            $user_id = $app->request()->post('user_id');*/
              //accessToken($user_id) 
             // check for required params
            //verifyRequiredParams(array('ticket_id','user_id','platform'));
            
            $response = array();
            $db = new DbHandler();
            $result=$db->ticketIdDetails($ticket_id);

           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["ticket_Details"]=$result['ticket_Details'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["ticket_Details"]=array();
            }
        
            echoRespnse(200, $response);
 });

// All Job Count

$app->post('/jobCountAll', 'authenticatedefault', function() use ($app) 
{       

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userId  = $data['user_id'];
            
            $response = array();
            $db = new DbHandler();
            $result=$db->jobCountAll($userId);

            // echo $result['status'];
            // die();

           if ($result['status'] == 1) 
           {
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["jobCountAll"]=$result['jobCountAll'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["jobCountAll"]=array();
            }
        
            echoRespnse(200, $response);
 });

// Women security


$app->post('/qrBusDetails', 'authenticatedefault', function() use ($app) 
{           

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userId  = $data['user_id'];
            $qrId  = $data['qr_id'];

            $response = array();
            $db = new DbHandler();
            $result=$db->getQrBusDetails($userId,$qrId);

           if ($result['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["vehicleDetails"] = $result['vehicleDetails'];
            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["vehicleDetails"]=array();
            }
        
            echoRespnse(200, $response);
 });

$app->post('/empCheckInVehicle', 'authenticatedefault', function() use ($app) 
{           
    // checkin =1;
    // checkout =2;
    // reachedhome =3;

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userId  = $data['user_id'];
            $vehicleId  = $data['vehicle_id'];
            $status  = 1;

            $response = array();
            $db = new DbHandler();
            $result=$db->getEmpCheckInVehicle($userId,$vehicleId,$status);

           if ($result['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = "successful";
                 $response["empTripDetails"] = $result['empTripDetails'];
            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = 'No Records Found';
                $response["empTripDetails"]=array();
            }
        
            echoRespnse(200, $response);
 });

$app->post('/empCheckOutVehicle', 'authenticatedefault', function() use ($app) 
{           
    // checkin =1;
    // checkout =2;
    // reachedhome =3;

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userId  = $data['user_id'];
            $empTripId  = $data['emp_trip_id'];
            $status  = 2;

            $response = array();
            $db = new DbHandler();
            $result=$db->getEmpCheckOutVehicle($userId,$empTripId,$status);

           if ($result['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = $result['message'];
                 $response["empTripDetails"] = $result['empTripDetails'];
            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = $result['message'];
                $response["empTripDetails"]=array();
            }
        
            echoRespnse(200, $response);
 });

$app->post('/empCheckInHome', 'authenticatedefault', function() use ($app) 
{           
    // checkin =1;
    // checkout =2;
    // reachedhome =3;

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userId  = $data['user_id'];
            $empTripId  = $data['emp_trip_id'];
            $status  = 3;

            $response = array();
            $db = new DbHandler();
            $result=$db->getEmpCheckInHome($userId,$empTripId,$status);

           if ($result['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = $result['message'];
                 $response["empTripDetails"] = $result['empTripDetails'];
            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = $result['message'];
                $response["empTripDetails"]=array();
            }
        
            echoRespnse(200, $response);
 });

$app->post('/getEmpDayVehicleHistory', 'authenticatedefault', function() use ($app) 
{           
    // checkin =1;
    // checkout =2;
    // reachedhome =3;

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $userId  = $data['user_id'];
            // $empTripId  = $data['emp_trip_id'];
            $status  = 3;

            $response = array();
            $db = new DbHandler();
            $result=$db->getEmpDayVehicleHistory($userId);

           if ($result['status']==1) 
           {
                //echo "if";
                 
                 $response["status"] =1;
                 $response['message'] = $result['message'];
                 $response["currentDateDetails"] = $result['currentDateDetails'];
            }
            else
            {
                //echo "else";
                $response['status'] =0;
                $response['message'] = $result['message'];
                $response["currentDateDetails"]=array();
            }
        
            echoRespnse(200, $response);
 });

$app->post('/createComplaint', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);

              // accessToken($user_id); 
            $response = array();
            $db = new DbHandler();
            $result = $db->createComplaint($data);

            
           if ($result['status']==1) 
           {    

                $response["status"] = 1;
                $response['message'] = "successfully submitted";
         
            }
            else
            {
                $response['status'] = 0;
                $response['message'] = 'submition failed';
                // $response["taskid"]=array();
            }

            echoRespnse(200, $response);
});

$app->post('/createVoiceComplaint', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);

              // accessToken($user_id); 
            $response = array();
            $db = new DbHandler();
            $result = $db->createVoiceComplaint($data);

            
           if ($result['status']==1) 
           {    

                $response["status"] = 1;
                $response['message'] = "successfully submitted";
         
            }
            else
            {
                $response['status'] = 0;
                $response['message'] = 'submition failed';
                // $response["taskid"]=array();
            }

            echoRespnse(200, $response);
});

$app->post('/acceptComplaint', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);

              // accessToken($user_id); 
            $response = array();
            $db = new DbHandler();
            $result = $db->acceptComplaintAcknowledge($data);

            
           if ($result['status']==1) 
           {    

                $response["status"] = 1;
                $response['message'] = "successfully submitted";
         
            }
            else
            {
                $response['status'] = 0;
                $response['message'] = 'submition failed';
                // $response["taskid"]=array();
            }

            echoRespnse(200, $response);
});

$app->post('/persecutorExplanationComplaint', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);

              // accessToken($user_id); 
            $userId  = $data['user_id'];
            $complaintId  = $data['complaint_id'];
            $explanation  = $data['explanation'];
            $status  = $data['status'];
            $response = array();
            $db = new DbHandler();
            $result = $db->persecutorExplanationComplaint($userId,$complaintId,$explanation,$status);

            
           if ($result['status']==1) 
           {    

                $response["status"] = 1;
                $response['message'] = $result['message'];
         
            }
            else
            {
                $response['status'] = 0;
                $response['message'] = $result['message'];
                // $response["taskid"]=array();
            }

            echoRespnse(200, $response);
});

$app->post('/sendShowCauseNotice', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);

              // accessToken($user_id); 

            $response = array();
            $db = new DbHandler();
            $result = $db->sendShowCauseNotice($data);

            
           if ($result['status']==1) 
           {    

                $response["status"] = 1;
                $response['message'] = "successfully submitted";
         
            }
            else
            {
                $response['status'] = 0;
                $response['message'] = 'submition failed';
                // $response["taskid"]=array();
            }

            echoRespnse(200, $response);
});

$app->post('/updateComplaint', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);

              // accessToken($user_id); 
            $response = array();
            $db = new DbHandler();
            $result = $db->updateComplaint($data);

            
           if ($result['status']==1) 
           {    

                $response["status"] = 1;
                $response['message'] = "successfully submitted";
         
            }
            else
            {
                $response['status'] = 0;
                $response['message'] = 'No Records Found';
                // $response["taskid"]=array();
            }

            echoRespnse(200, $response);
});

$app->post('/complaintsList', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);

              // accessToken($user_id); 
            $userId  = $data['user_id'];
            $response = array();
            $db = new DbHandler();
            $result = $db->complaintsList($userId);

            
           if ($result['status']==1) 
           {    

                $response["status"] = 1;
                $response['message'] = "successfully submitted";
                $response['complaint_list'] = $result['complaint_list'];
         
            }
            else
            {
                $response['status'] = 0;
                $response['message'] = 'No Records Found';
                $response['complaint_list'] = $result['complaint_list'];
                // $response["taskid"]=array();
            }

            echoRespnse(200, $response);
});

$app->post('/complaintDetails', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);

              // accessToken($user_id); 
            $userId  = $data['user_id'];
            $complaintId  = $data['complaint_id'];
            // $base_url = $req->getUrl()."".$req->getRootUri()."/";
            $response = array();
            $db = new DbHandler();
            $result = $db->complaintDetails($userId,$complaintId);

            
           if ($result['status']==1) 
           {    

                $response["status"] = 1;
                $response['message'] = "successfully submitted";
                $response['complaint_details'] = $result['complaint_details'];
         
            }
            else
            {
                $response['status'] = 0;
                $response['message'] = 'No Records Found';
                $response['complaint_details'] = $result['complaint_details'];
                // $response["taskid"]=array();
            }

            echoRespnse(200, $response);
});

$app->post('/scheduleNotifyList', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);

              // accessToken($user_id); 
            $userId  = $data['user_id'];
            $complaintId  = $data['complaint_id'];
            // $base_url = $req->getUrl()."".$req->getRootUri()."/";
            $response = array();
            $db = new DbHandler();
            $result = $db->scheduleNotifyList($userId,$complaintId);

            
           if ($result['status']==1) 
           {    

                $response["status"] = 1;
                $response['message'] = "successfully submitted";
                $response['notify_list'] = $result['notify_list'];
         
            }
            else
            {
                $response['status'] = 0;
                $response['message'] = 'No Records Found';
                $response['notify_list'] = $result['notify_list'];
                // $response["taskid"]=array();
            }

            echoRespnse(200, $response);
});

$app->post('/scheduleMeeting', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);

              // accessToken($user_id); 
            // $userId  = $data['user_id'];
            // $complaintId  = $data['complaint_id'];
            // $base_url = $req->getUrl()."".$req->getRootUri()."/";
            $response = array();
            $db = new DbHandler();
            $result = $db->scheduleMeeting($data);

            
           if ($result['status']==1) 
           {    

                $response["status"] = 1;
                $response['message'] = "successfully submitted";
         
            }
            else
            {
                $response['status'] = 0;
                $response['message'] = 'No Records Found';
                // $response["taskid"]=array();
            }

            echoRespnse(200, $response);
});

$app->post('/poshNotificationsList', 'authenticatedefault', function() use ($app) 
{           
            $json = $app->request->getBody();
            $data = json_decode($json, true);

              // accessToken($user_id); 
            $userId  = $data['user_id'];
            $compy_id  = $data['compy_id'];
            $response = array();
            $db = new DbHandler();
            $result = $db->poshNotificationsList($userId,$compy_id);

            
           if ($result['status']==1) 
           {    

                $response["status"] = 1;
                $response['message'] = "successfully submitted";
                $response['notification_complaints'] = $result['notification_complaints'];
         
            }
            else
            {
                $response['status'] = 0;
                $response['message'] = 'No Records Found';
                $response['notification_complaints'] = $result['notification_complaints'];
                // $response["taskid"]=array();
            }

            echoRespnse(200, $response);
});


// Dashboards Start



//employees count by plant and department

      $app->post('/employeesinplant', 'authenticatedefault', function() use ($app) 
{         
      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);
   
       $plantId  = $data['plantid'];
       $departmentId  = $data['departmentId'];
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getEmployeesinplant($plantId,$departmentId);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['emp_list'] = $result['emp_list'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response['emp_list'] = array();
      }

      echoRespnse(200, $response);
 });


//employees count by plant and department
      $app->post('/empAttendance', 'authenticatedefault', function() use ($app) 
{         
      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);
   
       $plantId  = $data['plantid'];
       
       $punchDate  = $data['punchDate'];
       $isAllDept  = $data['isAllDept'];
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getEmpAttendance($plantId,$punchDate,$isAllDept);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['empAttdList'] = $result['empAttdList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response['empAttdList'] = array();
      }

      echoRespnse(200, $response);
 });

//employees Leave Count

      $app->post('/empLeave', 'authenticatedefault', function() use ($app) 
{         
      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);
   
       $plantId  = $data['plantid'];
       $departmentId  = $data['departmentId'];
       $yr  = $data['year'];
       $mnth  = $data['month'];
       $frm = $yr."-".$mnth."-01";
       $to = $yr."-".$mnth."-31";
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getEmpLeave($plantId,$departmentId,$frm,$to);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['empLeaveList'] = $result['empLeaveList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response['empLeaveList'] = array();
      }

      echoRespnse(200, $response);
 });

      //employees Age Count

      $app->post('/empAge', 'authenticatedefault', function() use ($app) 
{         
      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);
   
       $plantId  = $data['plantid'];
       $departmentId  = $data['departmentId'];

      $response = array();
      $db = new DbHandler();

      $result=$db->getEmpAge($plantId,$departmentId);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['empAgeList'] = $result['empAgeList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response['empAgeList'] = array();
      }

      echoRespnse(200, $response);
 });


//employees Experince Count

      $app->post('/empExp', 'authenticatedefault', function() use ($app) 
{         
      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);
   
       $plantId  = $data['plantid'];
       $departmentId  = $data['departmentId'];

      $response = array();
      $db = new DbHandler();

      $result=$db->getEmpExp($plantId,$departmentId);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['empExpList'] = $result['empExpList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response['empExpList'] = array();
      }

      echoRespnse(200, $response);
 });

//employees Late-In Office Count

      $app->post('/empLateIn', 'authenticatedefault', function() use ($app) 
{         
      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);
   
       $plantId  = $data['plantid'];
       $departmentId  = $data['departmentId'];
       $reqdate  = $data['reqdate'];

      $response = array();
      $db = new DbHandler();

      $result=$db->getEmpLateIn($plantId,$departmentId,$reqdate);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['empLateInList'] = $result['empLateInList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response['empLateInList'] = array();
      }

      echoRespnse(200, $response);
 });



//Gender count  by plant and by gender type

    $app->post('/empGender', 'authenticatedefault', function() use ($app) 
{         
      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);
   
       $plantId  = $data['plantid'];
       $departmentId  = $data['departmentId'];
       $genderType  = $data['genderType'];
     
      $response = array();
      $db = new DbHandler();

      $result=$db->getGenderList($plantId,$departmentId,$genderType);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['gender_list'] = $result['gender_list'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response['gender_list'] = array();
      }

      echoRespnse(200, $response);
 });


//Login employee Leave Report

        $app->post('/lginEmpLvbyYr', 'authenticatedefault', function() use ($app) 
{         
      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);
   
      $empNumber  = $data['empNumber'];
      $year  = $data['year'];

      $response = array();
      $db = new DbHandler();

      $result=$db->getMyLeaveDetails($empNumber,$year);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['empLeaveList'] = $result['empLeaveList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response['empLeaveList'] = array();
      }

      echoRespnse(200, $response);
 });

//Login employee Late Report by month

        $app->post('/lginEmpLatebyMnth', 'authenticatedefault', function() use ($app) 
{         
      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);
   
      $empNumber  = $data['empNumber'];
      $year  = $data['year'];
      $month  = $data['month'];
      $atdType  = $data['atdType'];

      $response = array();
      $db = new DbHandler();

      $result=$db->getMyLateByMnth($empNumber,$year,$month,$atdType);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['myLateList'] = $result['myLateList'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response['myLateList'] = array();
      }

      echoRespnse(200, $response);
 });

// Employee Salaries by month

    $app->post('/empSalByMonth', 'authenticatedefault', function() use ($app) 
{         
      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);
   
      $plantId  = $data['plantId'];
      $departmentId  = $data['departmentId'];
      $year  = $data['year'];
      $month  = $data['month'];

      $response = array();
      $db = new DbHandler();

      $result=$db->getEmpSalByMnth($plantId,$departmentId,$year,$month);
     if ($result['status']==1) 
     {
           $response["status"] =1;
           $response['message'] = "success";
           $response['empSalList'] = $result['empSalList'];
           $response['totalSal'] = $result['totalSal'];
      }
      else
      {
          $response['status'] =0;
          $response['message'] = 'unsuccessfull';
          $response['empSalList'] = array();
      }
//$response["status"] =1;
      echoRespnse(200, $response);
 });
    

//Dashboards End

/*----------------------Start API's---------------------------------------*/
// password login using mobile or username
$app->post('/loginApi', 'authenticatedefault', function() use ($app) 
{         
     

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
           
            // $platform   = $data['platform'];
            $mobnumber  = $data['mobnumber'];

             $username  = $data['username'];

             $password  = $data['password'];

            $response = array();

            $req = $app->request;
            $base_url = $req->getUrl()."".$req->getRootUri()."/";
      $db = new DbHandler();
      $result=$db->loginApi($mobnumber,$username,$password,$base_url);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Logged in successfully";
                 $response["userDetails"]=$result['userDetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Mobile Number or Username or Password you have entered is incorrect';
                $response["userDetails"]=array();
            }
      
        echoRespnse(200, $response);
 });
// password login using mobile or username
$app->post('/getAllUsersApi', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);


            $userId  = $data['user_id'];

            $response = array();

          
      	$db = new DbHandler();
      	$result=$db->getAllUsersApi($userId);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "All Users";
                 $response["AllUsers"]=$result['AllUsers'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Invalid';
                $response["AllUsers"]=array();
            }
      
        echoRespnse(200, $response);
 });
// password login using mobile or username
$app->post('/getUserReceivedPosts', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);


            $userId  = $data['user_id'];

            $response = array();

          
      	$db = new DbHandler();
      	$result=$db->getUserReceivedPosts($userId);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "All Users Posts";
                 $response["AllUsersPosts"]=$result['AllUsersPosts'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'Invalid';
                $response["AllUsersPosts"]=0;
            }
      
        echoRespnse(200, $response);
 });

$app->post('/SendMediaData', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            // $result = implode(',',$data);
            $req = $app->request;


            $sender_id  = $_POST['sender_id'];
            $receiver_id  = $_POST['receiver_id'];
            $message  = $_POST['message'];
            $send_by  = $_POST['send_by'];
            $image = $_FILES['image']['name']; 
            $tempPath = $_FILES['image']['tmp_name'];
            $base_url = $req->getUrl()."".$req->getRootUri();
            $response = array();

          
        $db = new DbHandler();
        $result=$db->SendMediaData($sender_id,$receiver_id,$message,$send_by,$image,$tempPath);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Posts Send successfully";
                 // $response["posts"]=$result['posts'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'failed';
                // $response["posts"]=array();
            }
      
        echoRespnse(200, $response);
 });

$app->post('/SendPosts', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            $req = $app->request;


            $sender_id  = $data['sender_id'];
            $receiver_id  = $data['receiver_id'];
            $message  = $data['message'];
            $send_by  = $data['send_by'];
            $base_url = $req->getUrl()."".$req->getRootUri();
            $response = array();

          
        $db = new DbHandler();
        $result=$db->SendPosts($sender_id,$receiver_id,$message,$send_by);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Posts Send successfully";
                 // $response["posts"]=$result['posts'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'failed';
                // $response["posts"]=array();
            }
      
        echoRespnse(200, $response);
 });

$app->post('/UpdatePostStatus', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);
            $req = $app->request;


            $senderId  = $data['senderId'];
            $loginUserId  = $data['loginUserId'];

          
        $db = new DbHandler();
        $result=$db->UpdatePostStatus($senderId,$loginUserId);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Posts updated successfully";
                 // $response["posts"]=$result['posts'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'failed';
                // $response["posts"]=array();
            }
      
        echoRespnse(200, $response);
 });

$app->post('/getUserPosts', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);


            $sender_id  = $data['sender_id'];
            $receiver_id  = $data['receiver_id'];

            $response = array();

          
        $db = new DbHandler();
        $result=$db->getUserPosts($sender_id,$receiver_id);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "Posts";
                 $response["posts"]=$result['posts'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'failed';
                $response["posts"]=array();
            }
      
        echoRespnse(200, $response);
 });


$app->post('/SaveUserData', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            // $result = implode(',',$data);
            $req = $app->request;


            $user_id  = $_POST['user_id'];
            $full_name  = $_POST['full_name'];
            $email  = $_POST['email'];
            $phone_number  = $_POST['phone_number'];
            $user_name  = $_POST['user_name'];
            $password  = $_POST['password'];
            $role_id  = $_POST['role_id'];
            $role_name  = $_POST['role_name'];
            $image = $_FILES['profile']['name']; 
            $tempPath = $_FILES['profile']['tmp_name'];
            $base_url = $req->getUrl()."".$req->getRootUri();
            $response = array();

          
        $db = new DbHandler();
        $result=$db->SaveUserData($user_id,$full_name,$email,$phone_number,$user_name,$password,$role_id,$role_name,$image,$tempPath);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "User data saved successfully";
                 // $response["posts"]=$result['posts'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'failed';
                // $response["posts"]=array();
            }
      
        echoRespnse(200, $response);
 });

$app->post('/getUserDataByUserIdApi', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);


            $user_id  = $data['user_id'];

            $response = array();

          
        $db = new DbHandler();
        $result=$db->getUserDataByUserId($user_id);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "userData";
                 $response["userData"]=$result['userData'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'failed';
                $response["userData"]=array();
            }
      
        echoRespnse(200, $response);
 });

$app->post('/getListItemsApi', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);


            $user_id  = $data['user_id'];

            $response = array();

          
        $db = new DbHandler();
        $result=$db->getListItems($user_id);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "ListItems";
                 $response["ListItems"]=$result['ListItems'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'failed';
                $response["ListItems"]=array();
            }
      
        echoRespnse(200, $response);
 });

$app->post('/getSubListItemsApi', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);


            $user_id  = $data['user_id'];
            $item_id  = $data['item_id'];

            $response = array();

          
        $db = new DbHandler();
        $result=$db->getSubListItems($user_id,$item_id);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "SubListItems";
                 $response["SubListItems"]=$result['SubListItems'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'failed';
                $response["SubListItems"]=array();
            }
      
        echoRespnse(200, $response);
 });

$app->post('/GetSubItemDetails', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $id  = $data['id'];

            $response = array();

          
        $db = new DbHandler();
        $result=$db->getSubItemDetails($id);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "SubItemDetails";
                 $response["SubItemDetails"]=$result['SubItemDetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'failed';
                $response["SubItemDetails"]=array();
            }
      
        echoRespnse(200, $response);
 });

$app->post('/GetPostDetailsById', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $id  = $data['id'];

            $response = array();

          
        $db = new DbHandler();
        $result=$db->GetPostDetailsById($id);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "PostDetails";
                 $response["PostDetails"]=$result['PostDetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'failed';
                $response["PostDetails"]=array();
            }
      
        echoRespnse(200, $response);
 });

$app->post('/GetProjectList', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $user_id  = $data['user_id'];

            $response = array();

          
        $db = new DbHandler();
        $result=$db->GetProjectList($user_id);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "ProjectDetails";
                 $response["ProjectDetails"]=$result['ProjectDetails'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'failed';
                $response["ProjectDetails"]=array();
            }
      
        echoRespnse(200, $response);
 });

$app->post('/GetProjectActivitiesByProjectId', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $project_id  = $data['project_id'];

            $response = array();

          
        $db = new DbHandler();
        $result=$db->GetProjectActivitiesByProjectId($project_id);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "ProjectActivities";
                 $response["ProjectActivities"]=$result['ProjectActivities'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'failed';
                $response["ProjectActivities"]=array();
            }
      
        echoRespnse(200, $response);
 });

$app->post('/getProjectIdByName', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $name  = $data['name'];

            $response = array();

          
        $db = new DbHandler();
        $result=$db->getProjectIdByName($name);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "ProjectNames";
                 $response["ProjectNames"]=$result['ProjectNames'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'failed';
                $response["ProjectNames"]=array();
            }
      
        echoRespnse(200, $response);
 });

$app->post('/GetProjectActivities', 'authenticatedefault', function() use ($app) 
{         

            $json = $app->request->getBody();
            $data = json_decode($json, true);
            $result = implode(',',$data);

            $project_id  = $data['project_id'];

            $response = array();

          
        $db = new DbHandler();
        $result=$db->GetProjectActivities($project_id);


     //$user_details=$db->userDetails($user_id);
           if ($result['status']==1) 
           {
                 $response["status"] =1;
                 $response['message'] = "ProjectActivities";
                 $response["ProjectActivities"]=$result['ProjectActivities'];
            }
            else
            {
                $response['status'] =0;
                $response['message'] = 'failed';
                $response["ProjectActivities"]=array();
            }
      
        echoRespnse(200, $response);
 });

$app->post('/SaveTaskData', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);         
      $response = array();
      $db = new DbHandler();
      $result=$db->SaveTaskData($data);

      $response['status'] = $result['status'];
      $response['message'] = $result['message'];

      
      echoRespnse(200, $response);
 });

$app->post('/GetTasks', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);         
      $response = array();
      $db = new DbHandler();
      $user_id = $data['loginUserId'];
      $result=$db->GetTasks($user_id);

      $response['status'] = $result['status'];
      $response['Task'] = $result['Task'];

      
      echoRespnse(200, $response);
});

$app->post('/GetUserPosts', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);         
      $response = array();

      $db = new DbHandler();
      $user_id = $data['loginUserId'];
      $result = $db->GetUserPostsByUserId($user_id);
      
      $response['status'] = $result['status'];
      $response['UserPosts'] = $result['UserPosts'];

      
      echoRespnse(200, $response);
});

$app->post('/GetAutoCompleteUserPosts', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);         
      $response = array();

      $db = new DbHandler();
      $user_id = $data['loginUserId'];
      $result = $db->GetAutoCompleteUserPosts($user_id);
      
      $response['status'] = $result['status'];
      $response['UserPosts'] = $result['UserPosts'];

      
      echoRespnse(200, $response);
});

$app->post('/DeleteUserPosts', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);         
      $response = array();

      $db = new DbHandler();
      $post_id = $data['post_id'];
      $result = $db->DeleteUserPostsById($post_id);
      
      $response['status'] = $result['status'];
      $response['message'] = $result['message'];
      $response['UserPosts'] = $result['UserPosts'];

      
      echoRespnse(200, $response);
});

$app->post('/saveUserPosts', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);         
      $response = array();

      $db = new DbHandler();
      $loginUserId = $data['loginUserId'];
      $title = $data['title'];
      $body = $data['body'];


      $result = $db->saveUserPosts($loginUserId,$title,$body);
      
      $response['status'] = $result['status'];
      $response['message'] = $result['message'];

      
      echoRespnse(200, $response);
});

$app->post('/getUserPostsById', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);         
      $response = array();

      $db = new DbHandler();
      $post_id = $data['post_id'];
      
      $result = $db->getUserPostsById($post_id);
      
      $response['status'] = $result['status'];
      $response['posts'] = $result['posts'];

      
      echoRespnse(200, $response);
});

$app->post('/updateUserPost', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);         
      $response = array();

      $db = new DbHandler();
      $post_id = $data['post_id'];
      $title = $data['title'];
      $body = $data['body'];
      
      $result = $db->updateUserPost($post_id,$title,$body);
      
      $response['status'] = $result['status'];
      $response['message'] = $result['message'];

      
      echoRespnse(200, $response);
});

$app->get('/getUserRoles', 'authenticatedefault', function() use ($app) 
{         

      $response = array();
      $db = new DbHandler();      
      $result = $db->getUserRoles();
      
      $response['status'] = $result['status'];
      $response['roles'] = $result['roles'];
      
      echoRespnse(200, $response);
});

$app->post('/user_sign_up', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);       
      $response = array();

      $db = new DbHandler();
      
      $result = $db->saveUserSignUp($data);
      
      $response['status'] = $result['status'];
      $response['message'] = $result['message'];

      
      echoRespnse(200, $response);
});

$app->get('/usersList', 'authenticatedefault', function() use ($app){

   $response = array();
   $db = new DbHandler();
   $result = $db->getUsersList();

   $response['status'] = $result['status'];
   $response['users'] = $result['users'];

   echoRespnse(200, $response);
});

$app->post('/chatMenus', 'authenticatedefault', function() use ($app){

   	$json = $app->request->getBody();
    $data = json_decode($json, true);
    $result = implode(',',$data);         
    $response = array();

    $db = new DbHandler();
    $user_id = $data['user_id'];
   	$result = $db->getChatMenus($user_id);

   $response['status'] = $result['status'];
   $response['menus'] = $result['menus'];

   echoRespnse(200, $response);
});

$app->post('/chatDataByUserId', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);         
      $response = array();

      $db = new DbHandler();
      $user_id = $data['user_id'];
      
      $result = $db->getChatDataByUserId($user_id);
      
      $response['status'] = $result['status'];
      $response['user'] = $result['user'];

      
      echoRespnse(200, $response);
});

$app->post('/userDataByChatId', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);         
      $response = array();

      $db = new DbHandler();
      $chat_id = $data['chat_id'];
      
      $result = $db->getUserDataByChatId($chat_id);
      
      $response['status'] = $result['status'];
      $response['chat'] = $result['chat'];

      
      echoRespnse(200, $response);
});

$app->post('/saveUserChat', 'authenticatedefault', function() use ($app) 
{         

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);         
      $response = array();
      $db = new DbHandler();      
      $result = $db->saveUserChat($data);
      
      $response['status'] = $result['status'];
      $response['message'] = $result['message'];

      
      echoRespnse(200, $response);
});

$app->post('/userChatData', 'authenticatedefault', function() use ($app) 
{        

      $json = $app->request->getBody();
      $data = json_decode($json, true);
      $result = implode(',',$data);         
      $response = array();
      $db = new DbHandler();

      $req = $app->request;
      $base_url = $req->getUrl()."".$req->getRootUri()."/";

      $result = $db->getUserChatData($data,$base_url);
      
      $response['status'] = $result['status'];
      $response['userChat'] = $result['userChat'];

      
      echoRespnse(200, $response);
});

/*----------------------END API's---------------------------------------*/


///////////////////////////////////////////////////
/**
 * Verifying required params posted or not
 */
 
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
//print_r($error);
//exit;
    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        //$response["error"] = true;
        $response["status"] =0;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(200, $response);
        $app->stop();
    }
}

/**
 * Validating email address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(200, $response);
        $app->stop();
    }
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}
$app->run();


?>


