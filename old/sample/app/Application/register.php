<?php
	$path = $_SERVER['DOCUMENT_ROOT'].'/perso/app/';
	require_once($path.'save.php');
?>
<html lang="en">
	<head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
        <meta charset="utf-8">
        <title>sample</title>
        <meta name="viewport" content="width
        =device-width, initial-scale=1, maximum-scale=1">
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
    	<style type="text/css">
            .modal-footer {   border-top: 0px; }
            .mt5 {margin-top: 5%;}
            .mt10 { margin-top: 10%; }
            .ml10{ margin-left: 10%; }
        </style>
    </head>
    <body cz-shortcut-listen="true">
	    <div class="container">
	    	<div class="row-fluid">
	    		<div class="form-group">
	    			<div class="col-sm-3 col-sm-offset-5 mt5">
	    				<p class="lead text-center ml10"><h3>Register form</h3></p>
	    			</div>
	    		</div>
	    		<div class="form-group">
	    			<div class="col-sm-6 col-sm-offset-3 mt5">
	    				<!--login modal-->
						<form class="form col-md-12 center-block" method="POST">
					  		<div class="form-group">
					  			<input type="text" name="lastName" class="form-control input-lg" placeholder="LastName" required="true">
							</div>
					  		<div class="form-group">
					  			<input type="text" name="firstName" class="form-control input-lg" placeholder="FirstName" required="true">
							</div>
					  		<div class="form-group">
					  			<input type="text" name="address" class="form-control input-lg" placeholder="Address" required="true">
							</div>
							<div class="form-group">
					  			<input type="text" name="furtherAddress" class="form-control input-lg" placeholder="Further Address">
							</div>
							<div class="form-group">
					  			<input type="text" name="zipCode" class="form-control input-lg" placeholder="zip" required="true">
							</div>
							<div class="form-group">
					  			<input type="text" name="town" class="form-control input-lg" placeholder="town" required="true">
							</div>
					  		<div class="form-group">
					  			<input type="text" name="email" class="form-control input-lg" placeholder="Email" required="true">
							</div>
					        <div class="form-group">
					        	<input type="password" name="password" class="form-control input-lg" placeholder="Password" required="true">
					        </div>
					         <div class="form-group">
					        	<input type="password" name="confirmPassword" class="form-control input-lg" placeholder="Confirm Password" required="true">
					        </div>
							<div class="form-group">
					  			<button class="btn btn-primary btn-lg btn-block" name="SignIn">Sign In</button>
					  		</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	   	<script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
	    <style>
	        .ad {
	          position: absolute;
	          bottom: 70px;
	          right: 48px;
	          z-index: 992;
	          background-color:#f3f3f3;
	          position: fixed;
	          width: 155px;
	          padding:1px;
	        }
	        
	        .ad-btn-hide {
	          position: absolute;
	          top: -10px;
	          left: -12px;
	          background: #fefefe;
	          background: rgba(240,240,240,0.9);
	          border: 0;
	          border-radius: 26px;
	          cursor: pointer;
	          padding: 2px;
	          height: 25px;
	          width: 25px;
	          font-size: 14px;
	          vertical-align:top;
	          outline: 0;
	        }
	        
	        .carbon-img {
	          float:left;
	          padding: 10px;
	        }
	        
	        .carbon-text {
	          color: #888;
	          display: inline-block;
	          font-family: Verdana;
	          font-size: 11px;
	          font-weight: 400;
	          height: 60px;
	          margin-left: 9px;
	          width: 142px;
	          padding-top: 10px;
	        }
	        
	        .carbon-text:hover {
	          color: #666;
	        }
	        
	        .carbon-poweredby {
	          color: #6A6A6A;
	          float: left;
	          font-family: Verdana;
	          font-size: 11px;
	          font-weight: 400;
	          margin-left: 10px;
	          margin-top: 13px;
	          text-align: center;
	        }
	    </style>
	</body>
</html>