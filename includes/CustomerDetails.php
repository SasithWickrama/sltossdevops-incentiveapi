<?php
error_reporting(0);
class CustomerDetails
{
    public function getCustomerDetails($vno)
    {
        $cuslat = '0';
        $cuslon = '0';
        $email = '';
        $con = '';
        $nic = '';

        $users = array();
        $sql = "SELECT CIRT_CUSR_ABBREVIATION ,CIRT_ACCT_NUMBER ,  
            (SELECT CUSR_NAME||'*'||CUSR_CUTP_TYPE  FROM CUSTOMER WHERE CUSR_ABBREVIATION = CIRT_CUSR_ABBREVIATION ) CUS,
            (SELECT MAX(ADDE_STREETNUMBER||', '||ADDE_STRN_NAMEANDTYPE||', '||ADDE_SUBURB||', '||ADDE_CITY||', '||ADDE_COUNTRY)
                          FROM  SERVICES_ADDRESS, ADDRESSES
                          WHERE CIRT_SERV_ID = SADD_SERV_ID
                          AND SADD_TYPE = 'BEND'
                          AND ADDE_ID = SADD_ADDE_ID) ADDRESS ,
            NVL((SELECT FRAU_NAME||' / '||FRAA_POSITION||'*'||LOCN_X||'*'||LOCN_Y
                    FROM PORT_LINKS, PORT_LINK_PORTS, FRAME_APPEARANCES, FRAME_UNITS, FRAME_CONTAINERS , LOCATIONS
                    WHERE PORL_ID = POLP_PORL_ID
                    AND POLP_COMMONPORT = 'F'
                    AND POLP_FRAA_ID IS NOT NULL
                    AND FRAA_ID = POLP_FRAA_ID
                    AND FRAA_FRAU_ID = FRAU_ID
                    AND FRAU_FRAC_ID = FRAC_ID
                    AND LOCN_TTNAME = FRAC_LOCN_TTNAME
                    AND FRAC_FRAN_NAME IN ('FDP','DP')
                    AND PORL_CIRT_NAME IN
                    (SELECT CIRH_PARENT
                    FROM CIRCUIT_HIERARCHY
                    WHERE CIRH_CHILD =   CIRT_NAME)),'N/A')  DP,
            NVL((SELECT REPLACE(EQUP_LOCN_TTNAME,'-NODE','')||'_'||REPLACE(EQUP_EQUM_MODEL,'-ISL','')||'_'||SUBSTR(EQUP_INDEX,2)||' / '||PORT_CARD_SLOT||'-'||REPLACE(PORT_NAME,'POTS-IN-','')
            ||'*'||(select LOCN_X||'*'||LOCN_Y  from LOCATIONS  where LOCN_TTNAME = EQUP_LOCN_TTNAME)
                    FROM PORTS, EQUIPMENT
                    WHERE PORT_CIRT_NAME = CIRT_NAME
                    AND EQUP_EQUT_ABBREVIATION LIKE '%MSAN%'
                    AND PORT_EQUP_ID = EQUP_ID),'N/A') MSAN,
            (SELECT SERV_AREA_CODE  FROM SERVICES WHERE SERV_ID = CIRT_SERV_ID) LEA
            FROM CIRCUITS C 
            WHERE C.CIRT_DISPLAYNAME = :vno
            AND C.CIRT_STATUS IN ('INSERVICE','SUSPENDED')";

        $db = new DbConnect();
        $con = $db->connect();
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':vno', $vno);

        if ($stmt->execute()) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            $con = null;

            foreach ($results as $value) {


                $array = explode('*', $value['CUS']);
                $name = $array[0];
                $custype = $array[1];

                $array1 = explode('*', $value['DP'] . '0');
                $dp = $array1[0];
                $lat  = $array1[1];
                $lon = $array1[2];

                $array2 = explode('*', $value['MSAN'] . '0');
                $sw = $array2[0];
                $swlat  = $array2[1];
                $swlon = $array2[2];

                $cuslat = '';
                $cuslon = '';
                $email = '';
                $con = '';
                $nic = '';


                $sql = "SELECT count(*)  FROM CUSTOMER_RECORDS WHERE CR =  :cr  AND  VOICE_NO=:vno";
                $con = $db->connect();
                $stmt = $con->prepare($sql);
                $stmt->bindParam(':vno', $vno);
                $stmt->bindParam(':cr', $value['CIRT_CUSR_ABBREVIATION']);
                if ($stmt->execute()) {
                    $count1 = $stmt->rowCount();

                    if ($count1 == 0) {
                        $sql = "INSERT INTO LANECOVE.CUSTOMER_RECORDS (
                            CR, LEA, VOICE_NO, CUS_NAME, ENTERED_DATE, CUS_STATUS, CUS_ADDRESS,DP,CUS_SW) 
                            VALUES ( :cr,:lea,:vno,:cusname,sysdate,'ACTIVE',:cusaddress,:dp,:sw) ";
                        $stmt = $con->prepare($sql);
                        $stmt->bindParam(':vno', $vno);
                        $stmt->bindParam(':cr', $value['CIRT_CUSR_ABBREVIATION']);
                        $stmt->bindParam(':lea', $value['LEA']);
                        $stmt->bindParam(':cusname', $name);
                        $stmt->bindParam(':cusaddress', $value['ADDRESS']);
                        $stmt->bindParam(':dp', $dp);
                        $stmt->bindParam(':sw', $sw);
                        $stmt->execute();
                    } else {
                        $sql = "SELECT nvl(CUS_LAT,'0') LAT, nvl(CUS_LON,'0') LON,CUS_EMAIL, CUS_TP, CUS_NIC FROM CUSTOMER_RECORDS WHERE CR =  :cr AND  VOICE_NO=:vno";
                        $stmt = $con->prepare($sql);
                        $stmt->bindParam(':vno', $vno);
                        $stmt->bindParam(':cr', $value['CIRT_CUSR_ABBREVIATION']);

                        if ($stmt->execute()) {
                            $results1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $stmt->closeCursor();
                            $con = null;
                            foreach ($results1 as $value1) {
                                $cuslat = $value1['LAT'];
                                $cuslon = $value1['LON'];
                                $email = $value1['CUS_EMAIL'];
                                $con = $value1['CUS_TP'];
                                $nic = $value1['CUS_NIC'];
                            }
                        }
                    }
                }

                $data = [];
                $response_data['ERROR'] = false;
                $response_data['MESSAGE'] = 'SUCCESS';

                $data['NAME'] = $name;
                $data['CUS_TYPE'] = $custype;
                $data['CR'] = $value['CIRT_CUSR_ABBREVIATION'];
                $data['ACC'] = $value['CIRT_ACCT_NUMBER'];
                $data['ADDRESS'] = $value['ADDRESS'];
                $data['CUS_EMAIL'] = $email;
                $data['CUS_CAONTACT'] = $con;
                $data['CUS_NIC'] = $nic;
                $data['CUS_LON'] = $cuslon;
                $data['CUS_LAT'] = $cuslat;
                $data['FC_LOC'] = $dp;
                $data['FC_LON'] = $lon;
                $data['FC_LAT'] = $lat;
                $data['MSAN_LOC'] = $sw;
                $data['MSAN_LON'] = $swlon;
                $data['MSAN_LAT'] = $swlat;

                $response_data['DATA'] = $data;
            }
        } else {
            $response_data['ERROR'] = true;
            $response_data['MESSAGE'] = 'No Data';
            $response_data['DATA'] = '';
        }
        return $response_data;
    }
}
