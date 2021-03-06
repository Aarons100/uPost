<?php
require_once 'config.php';
session_start();


	//checks to make sure that the user has been logged in and has an access token
	//if the user has no access token, they are redirected to index.php
	if(isset($_SESSION['fb_access_token'])) {session_regenerate_id();}

	else if(isset($_SESSION['tw_access_token'])) {session_regenerate_id();}

	else if(isset($_SESSION['lk_access_token'])) {session_regenerate_id();}

	else {header("Location: http://{$host}");}
	//if the user presses the logout button, they are logged out
	if(isset($_GET['action']) && $_GET['action'] == 'logout') {
		session_destroy();
		header("Location: http://{$host}/index.php");
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
  		<meta http-equiv="X-UA-Compatible" content="IE=edge">
  		<meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="shortcut icon" href="/favicon.ico" />

  		<!-- Jquery -->
  		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

  		<!-- Bootstrap -->
  		<link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
  		<link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap-theme.min.css">
  		<script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>


  		<!-- stylesheet specific to the site -->
  		<link rel="stylesheet" type="text/css" href="Styles/general.css">

  		<style>
	    #tweets, #feeds {
	    	height: 600px;
	    	position: relative;
	    	overflow: hidden;
	    }
	    #tweets ul, #feeds ul{
	    	margin: 0px;
	    }
	    #tweets ul li, #feeds ul li{
	    	list-style: none;
	    	background-color: #ffffff;
	    	border-top: solid rgb(170,170,255);
	    	position: absolute;
	    	left: -5px;
	    	width: 100%;
	    	height: 20%;
	    	overflow: hidden;
	    }
	    #tweets ul li div img{
	    	float: left;
	    	position: absolute;
	    	top: 0px;
	    	left: 5px;
	    	height:100%;
	    }
	    #tweets ul li div, #feeds ul li div{
	    	width: 75%;
	    	height: 100%;
	    	float: right;
	    }
	    #tweets ul li div p, #feeds ul li div p{
	    	margin: 0px;
	    	font-style: italic;
	    }
	    #tweets ul li div .text, #feeds ul li div .text{
	    	font-size: 120%;
	    	color: rgb(150,150,255);
	    	position: relative;
	    	left: 10px;
	    }
	    #tweets ul li div .created_at{
	    	font-size: 80%;
	    }
  		</style>


  		<script type = "text/javascript">
	  		$(document).ready(function() {
				//Character counter
				var text=$("#post_text").val();
				$("#char_count").html(text.length);
				$("#post_text").on("keyup", function(){
					var text=$(this).val();
					$("#char_count").html(text.length);
				});

				//when the user submits a post
	  	  		$("#submit").on('click', function(e){
		  	  		e.preventDefault();
					//Prepare the data to be sent
					var text=$("form [name='text']").val();
					var loc=$("#current_loc").html().split(" ");
					var lat=loc[1];
					var lon=loc[3];
					var location=false;
					var facebook="off";
					var twitter="off";
					var linkedin="off";

		  	  		if($("form [name='location']").prop('checked')==true)
		  	  		{
			  	  		location=true;
		  	  		}

		  	  		var all_off=true;
		  	  		if($("form [name='facebook']").prop('checked')==true)
		  	  		{
			  	  		facebook="on";
			  	  		all_off=false;
		  	  		}
		  	  		if($("form [name='twitter']").prop('checked')==true)
		  	  		{
			  	  		twitter="on";
			  	  		//Check for characters count
			  	  		if(parseInt($("#char_count").html())>140)
			  	  		{
				  	  		window.location="about.php?error=char-count-excceeds";
			  	  		}
			  	  		all_off=false;
			  	  	}
			  	  	if($("form [name='linkedin']").prop('checked')==true)
		  	  		{
			  	  		linkedin="on";
			  	  		all_off=false;
		  	  		}
		  	  		//If the user didn't select any sns, stop and inform the user
		  	  		if(all_off)
		  	  		{
		  	  			window.location="about.php?error=all-off";
		  	  		}

		  	  		var data={
				  	  	text: text,
				  	  	lat: lat,
				  	  	long: lon,
				  	  	location: location,
				  	  	facebook: facebook,
				  	  	twitter: twitter,
				  	  	linkedin: linkedin
				  	};

		  	  		$.ajax({
			  	  		url:"post.php",
			  	  		type:"POST",
			  	  		ajax: true,
			  	  		data:data,
			  	  		dataType: "json",
			  	  		success:function(data, textStatus, jqXHR){
				  	  		//console.dir(data);
				  	  		$("#sites_posted_to").html("");
				  	  		$("#ssn_selections input").each(function(i, e){
					  	  		//First, clear the list

				  	  			//Repopulate the list with sns's that the user posted to with success
					  	  		if($(e).prop("checked")==true)
					  	  		{
						  	  		var display_name;
						  	  		if($(e).attr("name")=="facebook")
						  	  		{
							  	  		display_name="Facebook";
						  	  		}
					  	  			else if($(e).attr("name")=="twitter")
					  	  			{
						  	  			display_name="Twitter";
					  	  			}
					  	  			else if($(e).attr("name")=="linkedin")
					  	  			{
						  	  			display_name="Linkedin";
					  	  			}
					  	  			$("#sites_posted_to").append("<li class='ssn_name'>"+display_name+"</li>");
					  	  		}
					  	  	});
					  	  	$("#success_posting").modal("show");

				  	  	},
				  	  	error:function(jqXHR, textStatus, errorThrown){
					  	  	//console.dir(jqXHR+textStatus+errorThrown);
					  	  	var error_type=JSON.parse(jqXHR.responseText)["error"];
							//If the user hasn't logged in to one of the selected sites, display a popup asking for log in
					  	  	if(error_type=="facebook")
					  	  	{
						  	  	$("#fb_user_login").modal("show");
					  	  	}
					  	  	else if(error_type=="twitter")
					  	  	{
					  	  		$("#tw_user_login").modal("show");
					  	  	}
					  	  	else if(error_type=="linkedin")
					  	  	{
					  	  		$("#lk_user_login").modal("show");
					  	  	}
					  	  	else if(error_type=="facebook-posting-failed")
					  	  	{
						  	  	window.location="about.php?error=facebook-posting-failed";
					  	  	}
					  	  	else if(error_type=="twitter-posting-failed")
					  	  	{
					  	  		window.location="about.php?error=twitter-posting-failed";
					  	  	}
					  	},
					  	complete:function(jqXHR, textStatus){
						}
			  	  	});
		  	  	});

				//Fetch feed updates from the sns
				$.ajax({
					url:"fetch.php",
					type:"GET",
					dataType:"json",
					data:{"ssns":["facebook", "twitter"]},
					ajax:true,
					success:function(data, textStatus, jqXHR){
						console.dir(data);
						if(typeof data["data"]["twitter"]!='undefined')
						{
							//Display twitter posts
							var tw_num=0;
				    	    tw_interval=window.setInterval(function(){
					      	  grab_next_tweet(tw_num, data["data"]["twitter"]);
					      	  tw_num+=1;
					      	  if(tw_num==50)
					          {
					      		  tw_num=0;
					          }
				            }, 3000);
						}

						if(typeof data["data"]["facebook"]!='undefined')
						{
							//Display facebook posts
							var fb_num=0;
				            fb_interval=window.setInterval(function(){
							  grab_next_fb_feed(fb_num, data["data"]["facebook"]);
							  fb_num+=1;
							  if(fb_num==25)
							  {
								  fb_num=0;
							  }

						    }, 3000);
						}



					},
					error:function(jqXHR, textStatus, errorThrown)
					{
						console.log(JSON.parse(jqXHR.responseText));
					},
					complete:function(jqXHR, textStatus){
					}
				});
	  	  	});

			function grab_next_fb_feed(current_index, feeds)
			{
				var feed=feeds[current_index];

				//Calculate the height of a single feed dynamically
	  	        var feed_h=0.2*document.getElementById("feeds").clientHeight;
	  			feed_h=feed_h.toString();

	  			//Get the fields that we need: created_at, text, user, place, entities, and compile them in a <li>
	  	        var output="<li><div>";

	  	        //:from:name
	  	        var username=feed["from"]["name"];

	  	        //get the link
	  	        var link="facebook.com";
	  	        if(typeof feed["actions"]!='undefined')
	  	        {
	  	        	link=feed["actions"][0]["link"];
		  	    }
	  	        else if(typeof feed["link"]!='undefined')
	  	        {
		  	        link=feed["link"];
	  	        }

				//get the text status(either the story field or the message field)
	  	        var text="";
	  	        if(typeof feed["story"]!='undefined')
	  	        {
		  	        text=feed["story"];
	  	        }
	  	        else if(typeof feed["message"]!='undefined')
	  	        {
		  	        text=feed["message"];
	  	        }
	  	        else
	  	        {
		  	        text="unfound";
	  	        }

	  	        //Get number of comments
	  	        var num_comments=0;
	  	        if(typeof feed["comments"]!='undefined')
	  	        {
	  	        	num_comments=feed["comments"]["data"].length;
	  	        }


				output+="<p>"+username+"</p>";
				output+="<p>"+text+"</p>";
				output+="<p>"+num_comments+" comments</p>";
				output+="<a href='"+link+"'>"+"Go to see it"+"</a>";
	  	        output+="</div></li>";

	  	        if($("#feeds ul li").length==0)
	  			{
	  				$("#feeds ul").prepend(output).hide().fadeIn(200);
	  			}
	  			else
	  			{
	  				//Animate each tweet down with the distance equal to the height of the each tweet
	  		        $("#feeds ul li").each(function(i){
	  			        $(this).animate({top: "+="+feed_h}, 400, "swing", function(){
	  						if(i==0)
	  						{
	  							$("#feeds ul").prepend(output);
	  				    		$("#feeds ul li:first-child").hide();
	  				        	$("#feeds ul li:first-child").fadeIn(200);
	  						}
	  						else if(i>5)
	  			            {
	  			        		$(this).remove();
	  			            }
	  		        	});
	  		        });
	  			}


			}
	  		//This function grabs a tweet from an array of tweets and pushes it to the current tweet stream
	  	    function grab_next_tweet(current_index, tweets)
	  	    {
	  			//Calculate the height of a single tweet dynamically
	  	        var tweet_h=0.2*document.getElementById("tweets").clientHeight;

	  			tweet_h=tweet_h.toString();

	  	        //Get the fields that we need: created_at, text, user, place, entities, and compile them in a <li>
	  	        var output="<li><div>";

	  			//arr is used for parsing datetime information
	  	       	var arr=new Array();
	  	       	arr=tweets[current_index]["created_at"].split(" ");

	  	        output+="<p class='created_at'> At "+arr[3]+" "+arr[1]+" "+arr[2]+"</p>";
	  	        output+="<p class='user_name'>"+tweets[current_index]["user"]["name"]+" tweets: </p>";
	  	        output+="<p class='text'>"+tweets[current_index]["text"]+"</p>";
	  	        if(tweets[current_index]["place"]!=null)
	  	        {
	  	        	output+="<p class='place'>In: "+tweets[current_index]["place"]["full_name"]+"</p>";
	  	        }
	  	        output+="<img src='"+tweets[current_index]["user"]["profile_image_url"]+"' alt='profile image' >";
	  			output+="</div></li>";

	  			if($("#tweets ul li").length==0)
	  			{
	  				$("#tweets ul").prepend(output).hide().fadeIn(200);
	  			}
	  			else
	  			{
	  				//Animate each tweet down with the distance equal to the height of the each tweet
	  		        $("#tweets ul li").each(function(i){
	  			        $(this).animate({top: "+="+tweet_h}, 400, "swing", function(){
	  						if(i==0)
	  						{
	  							$("#tweets ul").prepend(output);
	  				    		$("#tweets ul li:first-child").hide();
	  				        	$("#tweets ul li:first-child").fadeIn(200);
	  						}
	  						else if(i>5)
	  			            {
	  			        		$(this).remove();
	  			            }
	  		        	});
	  		        });
	  			}
	  	    }
  		</script>

		<title>uPost Social Network Update Manager</title>
	</head>

	<body>
		<!-- Login prompts when user has not logged in to one of the selected sns's -->
		<!-- Popup for facebook login -->
		<div class="modal fade" id="fb_user_login" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title" id="myModalLabel">You need to:</h4>
		      </div>
		      <div class="modal-body">
			      <form role="form" action="login.php" method="post" autocomplete="on">
			          <div class="form-group">
			            <input id="fb_login" name="sns" class="btn btn-primary btn-block btn-lg" type="submit" value="login with Facebook" >
			          </div>
			      </form>
			  </div>
		    </div>
		  </div>
		</div>



		<!-- Popup for twitter login -->
		<div class="modal fade" id="tw_user_login" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title" id="myModalLabel">You need to:</h4>
		      </div>
		      <div class="modal-body">
			      <form role="form" action="login.php" method="post" autocomplete="on">
			          <div class="form-group">
			            <input id="tw_login" name="sns" class="btn btn-primary btn-block btn-lg" type="submit" value="login with Twitter" >
			          </div>
			      </form>
			  </div>
		    </div>
		  </div>
		</div>

		<!-- Popup for linkedin login -->
		<div class="modal fade" id="lk_user_login" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title" id="myModalLabel">You need to:</h4>
		      </div>
		      <div class="modal-body">
			      <form role="form" action="login.php" method="post" autocomplete="on">
			          <div class="form-group">
			            <input id="lk_login" name="sns" class="btn btn-primary btn-block btn-lg" type="submit" value="login with LinkedIn" >
			          </div>
			      </form>
			  </div>
		    </div>
		  </div>
		</div>

		<!-- Prompt for successful posting -->
	    <div class="modal fade" id="success_posting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title" id="myModalLabel">You have successfully posted to the following sites:</h4>
		      </div>
		      <div class="modal-body">
			      <ul id="sites_posted_to">

			      </ul>
			  </div>
		    </div>
		  </div>
		</div>

		<!-- Navbar(header) -->
		<nav class="navbar navbar-default fixed-top" role="navigation">
		  <div class="container-fluid">
	        <div class="navbar-header">
	            <a class="navbar-brand" href="index.php">uPOST</a>
	        </div>

	      	<ul class="nav navbar-nav navbar-right">
          		<li class="dropdown">
		          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Account Settings <b class="caret"></b></a>
		          <ul class="dropdown-menu">
		            <li><a href="#" onclick="window.location='account.php'">Manage Social Accounts</a></li>
		            <li><a href="#" onclick="window.location='about.php?action=logout'">Log Out</a></li>
		            <li class="divider"></li>
		          </ul>
		        </li>
		    </ul>
		  </div>
		</nav>

		<!-- For client-side notifications -->
		<?php include("alert.php");?>

		<!-- Main body. Post area -->
		<div class="fluid-container">
			<div class="row">
			    <div class="col-md-2 hidden-sm hidden-xs"></div>
			    <div class="col-md-8 panel custom-panel">
			    	<!-- Get the user's current location -->
			    	<script type="text/javascript">
			    	function saveLocation(position)
			    	{
				    	$("#current_loc").html("latitude: "+position.coords.latitude.toFixed(2)+" longitude: "+position.coords.longitude.toFixed(2));
			    	}
			    	function showError(error)
			    	{
			    		switch(error.code)
					    {
					    case error.PERMISSION_DENIED:
					      $("#current_loc").html("User denied the request for Geolocation.");
					      break;
					    case error.POSITION_UNAVAILABLE:
						  $("#current_loc").html("Location information is unavailable.");
					      break;
					    case error.TIMEOUT:
						  $("#current_loc").html("The request to get user location timed out.");
					      break;
					    case error.UNKNOWN_ERROR:
						  $("#current_loc").html("An unknown error occurred.");
					      break;
					    }
			    	}
			    	if(navigator.geolocation)
			    	{
			    		navigator.geolocation.getCurrentPosition(saveLocation, showError);
			    	}
					</script>

					<!-- User's post -->
				    <form method="post" action="post.php" role="form">

				    	<!-- Text area -->
			    		<div class="form-group">
			    			<label for="post_text">Write something and publish everywhere!</label><span class="pull-right">Character count: <span id="char_count">0</span></span>
			    			<textarea id="post_text" name="text" class="form-control" placeholder="Write something in your mind..." rows="5"></textarea>

			    		</div>

			    		<!-- Current Location -->
			    		<div class="form-group">
			    		    <div class="row">
			    				<div class="col-sm-6">
			    					<p><span>Your current location: &nbsp;&nbsp;</span><span id="current_loc"></span></p>
			    				</div>
			    				<div class="col-sm-6">
			    				<label class="checkbox-inline">

			    					<input name="location" type="checkbox" checked>
			    					Enable location posting
			    				</label>
			    				</div>

			    			</div>
			    		</div>

			    		<!-- SNS options -->
			    		<div id="ssn_selections" class="form-group">
			    			<!-- Generated dynamically -->

			    			<img src="Images/Logos/facebook.jpg" height="20">
			    			<label class="checkbox-inline" >
			    				<input id="fb-checkbox" name="facebook" type="checkbox">
			    			</label>
			    			<span>&nbsp;&nbsp;</span>

			    			<img src="Images/Logos/twitter.jpg" height="20">
			    			<label class="checkbox-inline" >
			    			    <input id="tw-checkbox" name="twitter" type="checkbox">
			    			</label>
			    			<span>&nbsp;&nbsp;</span>

			    			<img src="Images/Logos/linkedin.jpg" height="20">
			    			<label class="checkbox-inline" >
			    			    <input id="lk-checkbox" name="linkedin" type="checkbox">
			    			</label>
			    			<span>&nbsp;&nbsp;</span>
			    		</div>

			    		<div class="form-group">
			    			<input id="submit" class="btn btn-default btn-block btn-lg" type="submit" value="uPOST!">
			    		</div>
			    	</form>
			    </div>
			    <div class="col-md-2 hidden-sm hidden-xs"></div>
			</div>

			<div class="row">
			    <div class="col-md-2 hidden-sm hidden-xs"></div>


				<div class="col-md-4">
				  <div class="panel panel-default">
					<div class="panel-heading">Latest Facebook Posts</div>
					<div class="panel-body">
					  <div id="feeds_container">
					  	<div id="feeds">
					  	  <ul>
					  	  </ul>
					  	</div>
					  </div>
					</div>
					<div class="panel-footer"></div>
				  </div>
				</div>

				<div class="col-md-4">
				  <div class="panel panel-default">
					<div class="panel-heading">Latest Twitter Posts</div>
					<div class="panel-body">
					  <div id="tweets_container">
		                  <div id="tweets">
					        <ul id="stream">

					        </ul>
					      </div>
					  </div>
					</div>
					<div class="panel-footer"></div>
				  </div>
				</div>
				<div class="col-md-2 hidden-sm hidden-xs"></div>
			</div>
		</div>
	</body>
</html>
