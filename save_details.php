<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "digimgames";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get email and name from POST data
$msisdn = $_POST['msisdn'] ?? '';
$stake = $_POST['stake'] ?? '';

// Insert data into users table
$sql = "INSERT INTO users (msisdn,time,amount) VALUES ('$msisdn',now(),'$stake')";

if ($conn->query($sql) === TRUE) {
    echo "Record inserted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
stk($msisdn,$stake);
$conn->close();



function stk($msisdn,$amount){
    // Initialize the variables
$consumer_key = 'AyfuU5McKIl82hqelKSBtjwXXrU1qmas';
$consumer_secret = 'L1aXGpLmgIT0lqX5';
$Business_Code = '4125017';
$Passkey = 'b9fe99ddbd61d3e7a726d78118591a197c3b06a6a03fc55958a95c05d29b4957';
$Type_of_Transaction = 'CustomerPayBillOnline';
$Token_URL = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$phone_number = $msisdn;
$OnlinePayment = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$total_amount = $amount;
$CallBackURL = 'https://144.126.201.163/digimpot/digimstk.php';
$Time_Stamp = date("Ymdhis");
$password = base64_encode($Business_Code . $Passkey . $Time_Stamp);

$curl_Tranfer = curl_init();
curl_setopt($curl_Tranfer, CURLOPT_URL, $Token_URL);
$credentials = base64_encode($consumer_key . ':' . $consumer_secret);
curl_setopt($curl_Tranfer, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
curl_setopt($curl_Tranfer, CURLOPT_HEADER, false);
curl_setopt($curl_Tranfer, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl_Tranfer, CURLOPT_SSL_VERIFYPEER, false);
$curl_Tranfer_response = curl_exec($curl_Tranfer);

$token = json_decode($curl_Tranfer_response)->access_token;
echo json_encode(json_decode($curl_Tranfer_response,JSON_PRETTY_PRINT));
echo "token ".$token;
$curl_Tranfer2 = curl_init();
curl_setopt($curl_Tranfer2, CURLOPT_URL, $OnlinePayment);
curl_setopt($curl_Tranfer2, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $token));

$curl_Tranfer2_post_data = [
    'BusinessShortCode' => $Business_Code,
    'Password' => $password,
    'Timestamp' =>$Time_Stamp,
    'TransactionType' =>$Type_of_Transaction,
    'Amount' => $total_amount,
    'PartyA' => $phone_number,
    'PartyB' => $Business_Code,
    'PhoneNumber' => $phone_number,
    'CallBackURL' => $CallBackURL,
    'AccountReference' => 'MagicPot_'.$Phone_number,
    'TransactionDesc' => 'Test',
];

$data2_string = json_encode($curl_Tranfer2_post_data);
echo "datastring ".$data2_string;
curl_setopt($curl_Tranfer2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl_Tranfer2, CURLOPT_POST, true);
curl_setopt($curl_Tranfer2, CURLOPT_POSTFIELDS, $data2_string);
curl_setopt($curl_Tranfer2, CURLOPT_HEADER, false);
curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYHOST, 0);
$curl_Tranfer2_response = json_decode(curl_exec($curl_Tranfer2));

echo json_encode($curl_Tranfer2_response, JSON_PRETTY_PRINT);
$response=json_encode($curl_Tranfer2_response, JSON_PRETTY_PRINT);
$responseData = json_decode($response, true);

    // Access the ResponseCode field
    if (isset($responseData['ResponseCode'])) {
        $responseCode = $responseData['ResponseCode'];
       // echo "Response Code: $responseCode";
        echo '<script>';
        echo 'var message = "An STK has been pushed to you, please complete the topup process to continue and enjoy more wins";';
        echo 'alert(message);'; // You can use other types of popups or custom modal dialogs instead of alert()
        echo 'window.location = "index.php";'; // Redirect to index.php after closing the popup
        echo '</script>';

    } else {
        echo '<script>';
        echo 'var message = "Top UP did not complete, Please try again later!";';
        echo 'alert(message);'; // You can use other types of popups or custom modal dialogs instead of alert()
        echo 'window.location = "index.php";'; // Redirect to index.php after closing the popup
        echo '</script>';
    }
}
?>
