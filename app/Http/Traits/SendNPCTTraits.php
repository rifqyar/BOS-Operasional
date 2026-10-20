<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\DB;

trait SendNPCTTraits
{
    public function message2a(object $arraydata)
    {
        $baseEndpoint = env('NPCT_API_URL');
        $url  = "$baseEndpoint/checkTar";
        $user = env('NPCT_API_USERNAME');
        $key  = env('NPCT_API_KEY');

        $e      = explode('-', $arraydata->ID_FLAT ?? '');
        $tid    = trim($e[1] ?? '');
        $e2     = explode('/', $arraydata->TAR ?? '');
        $tar    = $e2[0] ?? '';
        $nocont = $arraydata->NO_CONT ?? '';
        $weight = $arraydata->BERAT_TRUCK ?? '';

        $addXML = '<request>
                <tid>' . $tid . '</tid>
                <tar>' . $tar . '</tar>
                <cont_no>' . $nocont . '</cont_no>
                <weight>' . $weight . '</weight>
                <handling_type>IN</handling_type>
            </request>';

        $addXML = trim(preg_replace('/\s\s+/', '', str_replace("\n", " ", $addXML)));

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $addXML,
            CURLOPT_HTTPHEADER     => [
                'User-ID: ' . $user,
                'NPCT-API-Key: ' . $key,
                'Content-Type: application/xml'
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $json      = simplexml_load_string($response);
        $rawrespon = json_encode($response);

        // Logging menggunakan DB Builder Laravel
        DB::table('log_integrasi_behandle')->insert([
            'type'         => 'message2a',
            'no_cont'      => $nocont,
            'raw_request'  => $addXML,
            'raw_response' => $rawrespon,
        ]);

        return $json;
    }

    public function message2b(object $arraydata)
    {

        $baseEndpoint = env('NPCT_API_URL');
        $url  = "$baseEndpoint/gateIn";
        $user = env('NPCT_API_USERNAME');
        $key  = env('NPCT_API_KEY');

        $timenow       = date('Y-m-d') . 'T' . date('00:H:i+s:00');
        $ata           = $timenow;
        $plate         = trim($arraydata->NO_PLAT ?? '');
        $e2            = explode('/', $arraydata->TAR ?? '');
        $tar           = $e2[0] ?? '';
        $e             = explode('-', $arraydata->ID_FLAT ?? '');
        $tid           = trim($e[1] ?? '');
        $weight        = $arraydata->BERAT_TRUCK ?? '';
        $ContainerId   = $arraydata->NO_CONT ?? '';
        $ISOCode       = $arraydata->ISO_CODE ?? '';
        $LoadingStatus = $arraydata->KD_CONT_JENIS ?? '';
        $GrossWeight   = $arraydata->BERAT_TRUCK ?? '';
        $WeighingTime  = $timenow;
        $totalWeight   = $arraydata->BERAT_TRUCK ?? '';

        // Perbaikan Bug: Penggunaan operator perbandingan '==' (di CI lama tidak sengaja terpakai '=')
        $DamagePresent = (($arraydata->FL_DG ?? 'N') == 'N') ? 'N' : 'Y';
        $ContainerOOG  = (($arraydata->FL_OOG ?? 'N') == 'N') ? 'F' : 'T';

        $addXML = '<TIDRegistration_request>
                <TruckEvent>
                    <MessageHeader>
                        <Sender>IPC</Sender>
                        <Recipient>NPCT1</Recipient>
                        <MessageType>CIC</MessageType>
                        <MessageVersion>1.0.0</MessageVersion>
                        <MessageId>9990</MessageId>
                        <MessageName>CGPCHECK</MessageName>
                        <MessageFunction>CREATE</MessageFunction>
                        <Terminal>NPCT1</Terminal>
                        <TruckingCompany>CIC</TruckingCompany>
                    </MessageHeader>
                    <Status>
                        <StatusCode>ANC</StatusCode>
                    </Status>
                    <TruckCall>
                        <TruckingCompany>CIC</TruckingCompany>
                        <LicensePlate>' . $plate . '</LicensePlate>
                        <TAR>' . $tar . '</TAR>
                        <TID>' . $tid . '</TID>
                        <TruckingWeight>' . $weight . '</TruckingWeight>
                        <ATA>' . $ata . '</ATA>
                        <ExpectedLane>1</ExpectedLane>
                    </TruckCall>
                    <Prenotifications>
                        <Prenotification>
                            <Container>
                                <ContainerId>' . $ContainerId . '</ContainerId>
                                <PositionOnTruck></PositionOnTruck>
                                <ContainerReverse>FALSE</ContainerReverse>
                                <LoadingStatus>' . $LoadingStatus . '</LoadingStatus>
                                <GrossWeight>' . $GrossWeight . '</GrossWeight>
                                <WeighingTime>' . $WeighingTime . '</WeighingTime>
                                <TotalWeight>' . $totalWeight . '</TotalWeight>
                                <ISOCode>' . $ISOCode . '</ISOCode>
                                <SealPresent></SealPresent>
                                <DamagePresent>' . $DamagePresent . '</DamagePresent>
                                <IMDGPresent></IMDGPresent>
                                <ContainerOOG>' . $ContainerOOG . '</ContainerOOG>
                                <ContainerDam1></ContainerDam1>
                                <ContainerDam2></ContainerDam2>
                                <ContainerDam3></ContainerDam3>
                                <ContainerDam4></ContainerDam4>
                                <ContainerDam5></ContainerDam5>
                                <ContainerDam6></ContainerDam6>
                                <ContainerDam7></ContainerDam7>
                                <ContainerDam8></ContainerDam8>
                            </Container>
                        </Prenotification>
                    </Prenotifications>
                </TruckEvent>
            </TIDRegistration_request>';

        $addXML = trim(preg_replace('/\s\s+/', '', str_replace("\n", " ", $addXML)));

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $addXML,
            CURLOPT_HTTPHEADER     => [
                'User-ID: ' . $user,
                'NPCT-API-Key: ' . $key,
                'Content-Type: application/xml'
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $json      = simplexml_load_string($response);
        $rawrespon = json_encode($response);

        // Logging menggunakan DB Builder Laravel
        DB::table('log_integrasi_behandle')->insert([
            'type'         => 'message2b',
            'no_cont'      => $ContainerId,
            'raw_request'  => $addXML,
            'raw_response' => $rawrespon,
        ]);

        return $json;
    }
}
