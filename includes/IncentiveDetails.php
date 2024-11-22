<?php
error_reporting(0);
class IncentiveDetails
{
    public function SocountCurMonth($sno)
    {

        
$cYear = date('Y');
$cdate = date('d');
$cmonth= date('m');
$tblname = 'SALES_'.$cYear.$cmonth;
$tblname2 = 'EMP_BASE';

$sql = "SELECT Y.DSERVICE_TYPE , NVL(Z,'0') Z
                FROM(
                SELECT  CASE WHEN SERVICE_TYPE LIKE 'BB%' THEN 'BROADBAND'
                                                WHEN SERVICE_TYPE LIKE '%IPTV%' THEN 'IPTV' ELSE SERVICE_TYPE END SERVICE_TYPE ,COUNT(DISTINCT SO_ID)  Z
                                                FROM $tblname2 A, $tblname B, CRM_EMP_LIST C 
                WHERE SERVICE_TYPE IN ( 'AB-FTTH' , 'AB-CAB' , 'AB-WIRELESS ACCESS' , 'E-IPTV COPPER' , 'E-IPTV FTTH' ,'BB-INTERNET COPPER','BB-INTERNET FTTH')
                AND ORDER_TYPE = 'CREATE'
                AND CUSROMER_TYPE NOT LIKE '%SLT%'
                AND SNO = :sno
                AND A.SNO = C.SERVICENO
                AND C.NAME = B.SALES_PERSON
                GROUP BY  CASE WHEN SERVICE_TYPE LIKE 'BB%' THEN 'BROADBAND'
                                                WHEN SERVICE_TYPE LIKE '%IPTV%' THEN 'IPTV' ELSE SERVICE_TYPE END
                UNION
                SELECT   'AB-FTTH :UPGRADE',NVL(COUNT(DISTINCT SO_ID),0)  Z
                FROM $tblname2 A, $tblname B, CRM_EMP_LIST C 
                WHERE SERVICE_TYPE = 'AB-FTTH'
                AND ORDER_TYPE LIKE 'CREATE-UPGRD%'
                AND CUSROMER_TYPE NOT LIKE '%SLT%'
                AND SNO =  :sno
                AND A.SNO = C.SERVICENO
                AND C.NAME = B.SALES_PERSON              
                ) X,
                SERVICE_TYPES   Y
                WHERE X.SERVICE_TYPE(+) = Y.DSERVICE_TYPE  
                ORDER BY DSERVICE_TYPE";


                $db = new DbConnect();
                $con = $db->connect();
                $stmt = $con->prepare($sql);
                $stmt->bindParam(':sno', $sno);

                if ($stmt->execute()) {

                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    $con = null;

                    foreach ($results as $value) {              

                        $svtype = $value['DSERVICE_TYPE'];
                        $value = $value['Z'];
                    }

                $data = [];
                $response_data['error'] = false;
                $response_data['message'] = 'SUCCESS';

                $data['svtype'] = $svtype;
                $data['value'] = $value;

                $response_data['data'] = $results;
                }
                else
                {
                    $response_data['error'] = true;
                    $response_data['message'] = 'Not Authorized';
                    $response_data['data'] ='';
                }

                return $response_data;



    }



    public function getTargetCUCo($sno)
    {


            $cYear = date('Y');
            $cdate = date('d');
            $cmonth= date('m');
            $tblname = 'SALES_'.$cYear.$cmonth;
            $tblname2 = 'EMP_BASE';
            
            $sql="SELECT TARGET, sum(NVL(Z,'0')) 
            as REC_COUNT FROM(
            SELECT TARGET ,COUNT(SO_ID) Z 
            FROM $tblname2 A, $tblname B, CRM_EMP_LIST C 
            WHERE SERVICE_TYPE IN ( 'AB-FTTH' , 'AB-CAB' , 'AB-WIRELESS ACCESS' , 'E-IPTV COPPER' , 'E-IPTV FTTH' ,'BB-INTERNET COPPER','BB-INTERNET FTTH') 
            AND ORDER_TYPE = 'CREATE' AND CUSROMER_TYPE NOT LIKE '%SLT%' AND SNO =  :sno
            AND A.SNO = C.SERVICENO AND C.NAME = B.SALES_PERSON 
            GROUP BY  TARGET  
            UNION 
            SELECT TARGET,NVL(COUNT(SO_ID),0) Z 
            FROM $tblname2 A, $tblname B, CRM_EMP_LIST C  
            WHERE SERVICE_TYPE = 'AB-FTTH' AND ORDER_TYPE LIKE 'CREATE-UPGRD%' 
            AND CUSROMER_TYPE NOT LIKE '%SLT%' AND SNO =  :sno
            AND A.SNO = C.SERVICENO AND C.NAME = B.SALES_PERSON
            GROUP BY  TARGET  ) X
            GROUP BY  TARGET";
          
            $db = new DbConnect();
            $con = $db->connect();
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':sno', $sno);

            if ($stmt->execute()) {

                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                $con = null;

            $response_data['error'] = false;
            $response_data['message'] = 'SUCCESS';  
            $response_data['data'] = $results;
            }else
            {
                $response_data['error'] = true;
                $response_data['message'] = 'Not Authorized';
                $response_data['data'] ='';
            }
            $stmt->closeCursor();
            $con = null;
            return $response_data;        
        }


            
        public function getSlab($sno,$salecount)
        {


            $cYear = date('Y');
            $cdate = date('d');
            $cmonth= date('m');
            $tblname = 'SALES_'.$cYear.$cmonth;
            $tblname2 = 'EMP_BASE';
            
            $sql="SELECT RATE_LEVEL,MIN_TARGET,MAX_TARGET FROM $tblname2 A,SALES_INCENTIVE_RATES B WHERE SNO = :sno AND A.CATAGORY = B.RTOM
                               AND MIN_TARGET <= :count AND MAX_TARGET >= :count";    
          
            $db = new DbConnect();
            $con = $db->connect();
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':sno', $sno);
            $stmt->bindParam(':count', $salecount);

            if ($stmt->execute()) {

                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                $con = null;

            $response_data['error'] = false;
            $response_data['message'] = 'SUCCESS';  
            $response_data['data'] = $results;
            }else
            {
                $response_data['error'] = true;
                $response_data['message'] = 'Not Authorized';
                $response_data['data'] ='';
            }
            $stmt->closeCursor();
            $con = null;
            return $response_data;        
        }
       


        public function getMonthSales($sno , $month)
    {

            $tblname = 'SALES_'.$month;
            $tablenameemp = 'EMP_'.$month;
            $tblname2 = 'EMP_BASE';
            
            $sql="SELECT Y.DSERVICE_TYPE, NVL(TOTAL,0) TOTAL, NVL(OK,0) OK, NVL(SU,0) SU, NVL(TX,0) TX, NVL(PN,0) PN 
                    FROM (SELECT * FROM (
                    SELECT CASE 
                        WHEN SERVICE_TYPE like 'BB%' THEN 'BROADBAND'
                        WHEN SERVICE_TYPE LIKE '%IPTV%' THEN 'IPTV' 
                        ELSE SERVICE_TYPE 
                        END 
                    DSERVICE_TYPE,NVL(BSS_STATUS,'XO') BSS_STATUS,SO_ID,COUNT( SO_ID ) OVER 
                    ( PARTITION BY CASE 
                        WHEN SERVICE_TYPE like 'BB%' THEN 'BROADBAND'
                        WHEN SERVICE_TYPE LIKE '%IPTV%' THEN 'IPTV' 
                        ELSE SERVICE_TYPE 
                        END ) AS TOTAL
                    FROM $tblname  A , CRM_EMP_LIST B, $tablenameemp C 
                    WHERE  SALES_PERSON = B.NAME
                    AND C.SNO = B.SERVICENO 
                    AND STATUS = 0
                    AND ORDER_TYPE = 'CREATE'
                    AND C.SNO = :sno
                    UNION		  
                    SELECT 'AB-FTTH :UPGRADE',NVL(BSS_STATUS,'XO') BSS_STATUS,SO_ID,COUNT( SO_ID ) OVER ( PARTITION BY 'AB-FTTH :UPGRADE' ) AS TOTAL
                    FROM $tblname A , CRM_EMP_LIST B, $tablenameemp C 
                    WHERE  SALES_PERSON = B.NAME
                    AND C.SNO = B.SERVICENO 
                    AND STATUS = 0
                    AND ORDER_TYPE LIKE 'CREATE-UPGRD%'
                    AND C.SNO = :sno
                    )
                 PIVOT 
                 ( 
                   COUNT(SO_ID) 
                   FOR BSS_STATUS 
                   IN ( 'OK'  \"OK\", 'SU'  \"SU\", 'TX'  \"TX\" , 'XO'  \"PN\" )
                 )
                 ORDER BY DSERVICE_TYPE) X,
                (SELECT  DISTINCT DSERVICE_TYPE FROM  SERVICE_TYPES ) Y
                 WHERE X.DSERVICE_TYPE(+) = Y.DSERVICE_TYPE
                 ORDER BY DSERVICE_TYPE";
          
            $db = new DbConnect();
            $con = $db->connect();
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':sno', $sno);

            if ($stmt->execute()) {

                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                $con = null;

            $response_data['error'] = false;
            $response_data['message'] = 'SUCCESS';  
            $response_data['data'] = $results;
            }else
            {
                $response_data['error'] = true;
                $response_data['message'] = 'Not Authorized';
                $response_data['data'] ='';
            }
            $stmt->closeCursor();
            $con = null;
            return $response_data;        
        }


	public function getLastUpdatedTime($sno)
        {
            $tblname = 'SETTING_TABLE';
            
            $sql="SELECT to_char(CREATE_DATE,'DD/MM/YYYY HH24:MI:SS') CREATE_DATE FROM $tblname WHERE DISCRIPTION = 'LAST_UPDATED_TIME'";
            
            $db = new DbConnect();
            $con = $db->connect();
            $stmt = $con->prepare($sql);
    
            if ($stmt->execute()) {
    
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                $con = null;  
    
                $data = [];
                $response_data['error'] = false;
                $response_data['message'] = 'SUCCESS';
        
                $response_data['data'] = $results;
            }

            $stmt->closeCursor();
            $con = null;
            return $response_data;
        }
            
    }

