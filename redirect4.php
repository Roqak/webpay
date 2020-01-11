<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<?php
session_start();
include('conn.php');
// print_r($_SESSION);
// GET GLOBALS FROM DB
$sql = "SELECT * FROM `GLOBALS` WHERE `name`='mac'";
    $mackey = mysqli_query($connect, $sql);
    if(!$mackey){
            echo "Error: ".mysqli_connect_error();
    }else{
        $mac = mysqli_fetch_assoc($mackey);
        $nhash=$mac['value'];
        // print_r($mac);
    }
    $sql = "SELECT * FROM `GLOBALS` WHERE `name`='product_id'";
    $result = mysqli_query($connect, $sql);
    if(!$result){
            echo "Error: ".mysqli_connect_error();
    }else{
        $product_id = mysqli_fetch_assoc($result);
        $subpdtid=$product_id['value'];
        // print_r($product_id);
    }
//     $sql = "SELECT * FROM `GLOBALS` WHERE `name`='pay_item_id'";
//     $result = mysqli_query($connect, $sql);
//     if(!$result){
//             echo "Error: ".mysqli_connect_error();
//     }else{
//         $pay_item_id = mysqli_fetch_assoc($result);
//         $pay_item_id=$pay_item_id['value'];
//         // print_r($pay_item_id);
//     }
//$subpdtid = 6204; //  product ID
// $subpdtid = 6205; // your product ID
$submittedamt = $_SESSION["amount"];
$submittedref = $_POST['txnref'];

        // $nhash = "D3D1D05AFE42AD50818167EAC73C109168A0F108F32645C8B59E897FA930DA44F9230910DAC9E20641823799A107A02068F7BC0F4CC41D2952E249552255710F" ; // the mac key sent to you
        //CP $nhash = "E187B1191265B18338B5DEBAF9F38FEC37B170FF582D4666DAB1F098304D5EE7F3BE15540461FE92F1D40332FDBBA34579034EE2AC78B1A1B8D9A321974025C4" ; // the mac key sent to you
        $hashv = $subpdtid.$submittedref.$nhash;  // concatenate the strings for hash again
$thash = hash('sha512',$hashv); 

$parami = array(
        "productid"=>$subpdtid,
        "transactionreference"=>$submittedref,
        "amount"=>$submittedamt
);
$payparams = http_build_query($parami);

$url = "https://sandbox.interswitchng.com/webpay/api/v1/gettransaction.json?" . $payparams; // json

$headers = array(
        "GET /HTTP/1.1",
        "Host: sandbox.interswitchng.com",
        "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1",
        "Accept-Language: en-us,en;q=0.5",
        "Keep-Alive: 300",
        "Connection: keep-alive",
        "Hash: " . $thash
    );        
// print_r2($headers);
// echo $url;
// echo $payparams;

$ch = curl_init();  //INITIALIZE CURL///////////////////////////////
//               
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 60); 
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
curl_setopt($ch, CURLOPT_POST, false );
//
$data = curl_exec($ch);  //EXECUTE CURL STATEMENT///////////////////////////////
$json = null;



if (curl_errno($ch)) 
{ 
        print "Error: " . curl_error($ch) . "</br></br>";

        $errno = curl_errno($ch);
        $error_message = curl_strerror($errno);
        print $error_message . "</br></br>";;

        // print_r($headers);

}
else 
{  
        // Show me the result
        $json = json_decode($data, TRUE);

        curl_close($ch);    //END CURL SESSION///////////////////////////////
        //Response from interswitch
        // print_r($json['ResponseDescription']);
        // print_r($json);
        $amount = $json['Amount']/100;
        if($json['ResponseCode'] == "00"){
                echo '<div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Payment Successful!</h4>
                <p>You have successfully Donated #'.$amount.' to the Alumni .</p>
                <hr>
                <p class="mb-0">Go back home <a href="http://localhost/webpay/">Home</a>.</p>
              </div>';
              $query = "SELECT * FROM successfull_transactions WHERE MerchantReference = '$json[MerchantReference]'";
              $result = mysqli_query($connect,$query);
              if ($result) {
                if (!mysqli_num_rows($result) > 0) {
                         $query = "INSERT INTO `successfull_transactions` (`Amount`,`CardNumber`,`MerchantReference`,`PaymentReference`,`RetrievalReferenceNumber`,`TransactionDate`)
                VALUES('".$amount."', '".$json['CardNumber']."', '".$json['MerchantReference']."',
              '".$json['PaymentReference']."','".$json['RetrievalReferenceNumber']."','".$json['TransactionDate']."')";
                if (!mysqli_query($connect, $query)) {
                echo "Error: ".mysqli_error();
                }
                }
              } else {
                echo 'Error: '.mysql_error();
              }
//        $query = "INSERT INTO `successfull_transactions` (`Amount`,`CardNumber`,`MerchantReference`,`PaymentReference`,`RetrievalReferenceNumber`,`TransactionDate`)
//        VALUES('".$json['Amount']."', '".$json['CardNumber']."', '".$json['MerchantReference']."',
//               '".$json['PaymentReference']."','".$json['RetrievalReferenceNumber']."','".$json['TransactionDate']."')";
// // echo $query;
//                 if (!mysqli_query($connect, $query)) {
//                 echo "Error: ".mysqli_error();
//                 }
        }else{
                echo '<div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Error!</h4>
                <p>Something went wrong. <em>'.$json['ResponseDescription'].'!!</em></p>
                <hr>
                <p class="mb-0">Go back home <a href="http://localhost/webpay/">Home</a>.</p>
              </div>';
        }
        $sql = "UPDATE Transactions SET status='".$json['ResponseDescription']."'
        ,paymentReference='".$json['PaymentReference']."'
        ,RetrievalReferenceNumber='".$json['RetrievalReferenceNumber']."'
        WHERE ref='$submittedref'";
// echo $sql;
                if (mysqli_query($connect, $sql)) {
                // echo "Record updated successfully";
                } else {
                echo "Error updating record: " . mysqli_error($connect);
                }


}

session_write_close();



?>
	<!-- </br></br></br><a href="http://localhost/webpay/">Home</a> -->
  </body>
</html>