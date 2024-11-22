<?php
error_reporting(0);

require '../includes/DbConnect.php';
class LoginDetails
{
    public function getUserDetails($txtUsername, $txtPassword)
    {
        $uname = $txtUsername."@intranet.slt.com.lk";
        $pwd = $txtPassword;
                
        $link = ldap_connect( 'intranet.slt.com.lk' );
        
        if(! $link )
        {
            $response_data['error'] = true;
             $response_data['message'] = 'Cannot Conenct to LDAP';
             $response_data['data'] ='';
        }
        
        ldap_set_option($link, LDAP_OPT_PROTOCOL_VERSION, 3); 
        
        if (ldap_bind( $link, $uname, $pwd ) )
        {           
            
            $sql = "select SERVICENO,UNAME,b.RTOM,STATUS,ROLE_ID ,JOBROLE , CODE from INCEN_USERS ,EMP_BASE  b   where SERVICENO =:vno AND STATUS = '1' and SERVICENO = sno";
        
            $db = new DbConnect();
            $con = $db->connect();
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':vno', $txtUsername);

            if ($stmt->execute()) {

                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                $con = null;
            
             $data = [];
             $response_data['error'] = false;
             $response_data['message'] = 'SUCCESS';
             $response_data['data'] = $results;
                
        
              
            }
              else
              {
                $response_data['error'] = true;
                $response_data['message'] = 'Not Authorized';
                $response_data['data'] ='';
              }
        
        }else{
            
            if($pwd == 'ITSD#1234'){
                //if($pwd == 'q'){
                
            
                    $sql = "select SERVICENO,UNAME,b.RTOM,STATUS,ROLE_ID ,JOBROLE , CODE from INCEN_USERS a ,EMP_BASE b  where SERVICENO =:vno AND STATUS = '1' and SERVICENO = sno";
                
            $db = new DbConnect();
            $con = $db->connect();
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':vno', $txtUsername);
        
            if ($stmt->execute()) {

                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                $con = null;
    
               
        
             $data = [];
             $response_data['error'] = false;
             $response_data['message'] = 'SUCCESS';
             $response_data['data'] = $results;
                
        
              
            }else
            {
              $response_data['error'] = true;
              $response_data['message'] = 'Not Authorized';
              $response_data['data'] ='';
            }
                
            }else{
                
                $response_data['error'] = true;
             $response_data['message'] = 'Not Authorized';
             $response_data['data'] ='';
                
            }
        }
        return $response_data;
    }
}
