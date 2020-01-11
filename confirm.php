<?php
    session_start();

	include "conn.php";
    // echo "Post Details:";	print_r($_POST);	echo "</br>";
    // echo "Session Details:";	print_r($_SESSION);	
    echo "<p class='alert'>You're about to Donate ".$_POST['amount']." to Uniosun Alumni</p>";
    
    // Get Globals from db
    $sql = "SELECT * FROM `GLOBALS` WHERE `name`='mac'";
    $mackey = mysqli_query($connect, $sql);
    if(!$mackey){
            echo "Error: ".mysqli_connect_error();
    }else{
        $mac = mysqli_fetch_assoc($mackey);
        $mac=$mac['value'];
        // print_r($mac);
    }
    $sql = "SELECT * FROM `GLOBALS` WHERE `name`='product_id'";
    $result = mysqli_query($connect, $sql);
    if(!$result){
            echo "Error: ".mysqli_connect_error();
    }else{
        $product_id = mysqli_fetch_assoc($result);
        $product_id=$product_id['value'];
        // print_r($product_id);
    }
    $sql = "SELECT * FROM `GLOBALS` WHERE `name`='pay_item_id'";
    $result = mysqli_query($connect, $sql);
    if(!$result){
            echo "Error: ".mysqli_connect_error();
    }else{
        $pay_item_id = mysqli_fetch_assoc($result);
        $pay_item_id=$pay_item_id['value'];
        // print_r($pay_item_id);
    }
	$site_redirect_url = "http://localhost/webpay/redirect4.php";
    $txn_ref = "JB"  . intval( "0" . rand(1,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) ); // random(ish) 7 digit int. WEBPAY MAX - 50 characters
    // $mac    = "D3D1D05AFE42AD50818167EAC73C109168A0F108F32645C8B59E897FA930DA44F9230910DAC9E20641823799A107A02068F7BC0F4CC41D2952E249552255710F";
    // echo $mac;
    //$mac    = "DE29269C3523CC8446DF9AF42B4C443E6FFE6956AA66FFDAE37FD6D4D2A5255461C1DD3CA5B095A27B58975646385E6D802709E9A92AAD12BA6F45C61783D906";
    $amount = $_POST["amount"] * 100;
    $hashv  = $txn_ref . $product_id . "101" . $amount . $site_redirect_url . $mac;
    $donorName = $_POST["firstname"]." ".$_POST["lastname"];
    $hash  = hash('sha512',$hashv);       
    $_SESSION["amount"] = $amount;	//Store amount for use in GetTransaction
    if(isset($_POST['submit'])){
        $sql = "INSERT INTO Transactions (`donor_name`, `amount`,`ref`,`status`)
VALUES ('$donorName', '$amount', '$txn_ref','pending')";

if (!mysqli_query($connect, $sql)) {
    echo "Error: ".mysqli_connect_error();
}
    }
?>

<!-- LIVE URL => https://webpay.interswitchng.com/paydirect/pay          -->
<!-- TEST URL => https://stageserv.interswitchng.com/test_paydirect/pay  -->

<form method="post" action="https://sandbox.interswitchng.com/webpay/pay">
    <!-- REQUIRED HIDDEN FIELDS -->
    <input name="product_id" type="hidden" value="<?php echo $product_id; ?>" />
    <input name="pay_item_id" type="hidden" value="<?php echo $pay_item_id; ?>" />
    <input name="amount" type="hidden" value="<?php echo $amount; ?>" />
    <input name="currency" type="hidden" value="566" />
    <input name="site_redirect_url" type="hidden" value="<?php echo $site_redirect_url; ?>" />
    <input name="txn_ref" type="hidden" value="<?php echo $txn_ref; ?>" />
    <input name="cust_id" type="hidden" value="1759"/>
    <input name="cust_id" type="hidden" value="1759"/>
    <input name="site_name" type="hidden" value=""/>
    <input name="cust_name" type="hidden" value="<?php echo $donorName; ?>" />
    <input name="hash" type="hidden" id="hash" value="<?php echo $hash;  ?>" />
    </br></br>
    <a href="http://localhost/webpay/">Back</a>
    <input type="submit" value="Make Payment"></input>
</form> 