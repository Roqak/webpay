<?php 
	session_start();
	$_SESSION["txn_ref"] = "JB"  . intval( "0" . rand(1,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) ); // random(ish) 7 digit int
	
?>
<!-- <form action="http://localhost/webpay/confirm.php" method="POST">

    First Name: <input name="FirstName" type="input" /></br>
    Last Name:  <input name="LastName" type="input" /></br>
    Amount:     <input name="amount" type="input" /></br></br>
    <input type="submit" />

</form>   	

<a href="http://localhost/webpay/">Page 1</a>
<a href="http://localhost/webpay/requery.php">Requery</a> -->
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

   <body data-gr-c-s-loaded="true"><div class="col-md-6 offset-md-3">
                    <span class="anchor" id="formPayment"></span>
                    <hr class="my-5">

                    <!-- form card cc payment -->
                    <div class="card card-outline-secondary">
                        <div class="card-body">
                            <h3 class="text-center">Donate</h3>
                            <hr>
                            
                            <form class="form" role="form" method="post" action="confirm.php" autocomplete="off">
                                <div class="form-group">
                                    <label for="cc_name">Fist Name</label>
                                    <input type="text" name="firstname" class="form-control" id="cc_name" title="First name" required="required">
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="lastname" class="form-control" autocomplete="off" maxlength="20" title="last name" required="">
                                </div>
                                
                                <div class="row">
                                    <label class="col-md-12">Amount</label>
                                </div>
                                <div class="form-inline">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">#</span></div>
                                        <input type="text" name="amount" class="form-control text-right" id="exampleInputAmount" placeholder="2000">
                                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group row">
                                    <!-- <div class="col-md-6">
                                        <button type="reset" class="btn btn-default btn-lg btn-block">Cancel</button>
                                    </div> -->
                                    <div class="col-md-6">
                                        <button name="submit" type="submit" class="btn btn-success btn-lg btn-block">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /form card cc payment -->
                
                </body>
