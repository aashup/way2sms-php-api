<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Send Sms</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">		
        <meta name="generator" content="Bootply" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link href="asset/bootstrap.min.css" rel="stylesheet">
        <!--[if lt IE 9]>
                <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

    </head>
    <body>   
        <div class="container">
            <div class="container">
                <h2>Send SMS via way2sms<small>created by Aashutosh</small></h2>
                <form method="post" class="form form-horizontal" name="sendsms" id="sendsms">
                    <div class="row">
                        <div class="form-group">

                            <label for="usrname" class="col-md-3 col-sm-4 col-xs-12">User Name</label>
                            <div class=" col-sm-9 col-sm-8 col-xs-12 ">
                                <input class="form-control form-textbox" name="usrname" required="" type="text" value="" placeholder="way2sms username..">
                            </div>
                        </div>
                        <div class="form-group">

                            <label for="pwd" class="col-md-3 col-sm-4 col-xs-12">Password</label>
                            <div class=" col-sm-9 col-sm-8 col-xs-12 ">
                                <input class="form-control form-textbox" name="pwd" required="" type="password" value="" placeholder="way2sms password..">
                            </div>
                        </div>
                        <div class="form-group">

                            <label for="recipient" class="col-md-3 col-sm-4 col-xs-12">Recipient </label>
                            <div class=" col-sm-9 col-sm-8 col-xs-12 ">
                                <input class="form-control form-textbox" name="recipient" required="" value="" placeholder="recipient like 7398346600,7398346600,...">
                            </div>
                        </div>
                        <div class="form-group">

                            <label for="msg" class="col-md-3 col-sm-4 col-xs-12">message</label>
                            <div class=" col-sm-9 col-sm-8 col-xs-12 ">
                                <textarea type="text" class="form-control" name="msg" required="" placeholder="Enter msg here. like hello Anju how are u!!"></textarea>
                            </div>
                        </div>                
                        <br>
                        <div class="row text-center">
                        <button class="btn btn-primary btn-lg">Send message</button>
                        </div>
                    </div>

                    <div style="margin-top: 4%"></div>
                    <div class="jumbotron">
                        <ul id="result" class="text-success list-unstyled">

                        </ul>
                    </div>


                </form>
            </div>
        </div>
        <script src="asset/jquery.min.js"></script>
        <script src="asset/bootstrap.min.js"></script>
        <script>
            $("#sendsms").submit(function(){
            $("button").addClass("disabled");
            var last_response_len = false;
            $.ajax('proxy.php?'+$("#sendsms").serialize(), {
                xhrFields: {
                    onprogress: function (e)
                    {
                        var this_response, response = e.currentTarget.response;
                        if (last_response_len === false)
                        {
                            this_response = response;
                            last_response_len = response.length;
                        }
                        else
                        {
                            this_response = response.substring(last_response_len);
                            last_response_len = response.length;
                        }
                        $("#result").append('<li>' + this_response + '</li>');
                    }
                }
            })
                    .done(function (data)
                    {
                        $("#result").append("<li>completed</li>");
                        $("button").removeClass("disabled");
                    })
                    .fail(function (data)
                    {
                        $("#result").append("<li>Failed to get</li>");
                        $("button").removeClass("disabled");

                    });
            $("#result").append("<li>start sending..</li>");
            return false;
            });
        </script>
    </body>
</html>
