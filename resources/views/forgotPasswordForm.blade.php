 <!DOCTYPE html>
 <html lang="en">

 <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <title>Forgot Password</title>
</head>
<body>
  <div class="row">
   <div class="center-block" style="margin:0 auto;margin-top:50px;border-radius:5px;padding:50px 50px;width:600px;background:#707070;">
    <form class="form-horizontal" action = "/api/forgotpassword/change" method="post">
     {{csrf_field()}}

     <fieldset>
      <input type = "hidden" name = "token" value = {{$data['token']}}>
      <legend style="color:white;font-size:1.8em">RoomRent</legend>
      <div class="form-group">
        <label for="newPassword" class="col-lg-2 control-label" style='color:white;'>Password</label>
        <div class="col-lg-10">
          <input class="form-control" name="newPassword" placeholder="New Password" type="password">
        </div>
      </div>
      <div class="form-group">
        <label for="newPassword_confirmation" class="col-lg-2 control-label" style='color:white;'>Confirm Password</label>
        <div class="col-lg-10">
          <input class="form-control" name="newPassword_confirmation" placeholder="Confirm Password" type="password">
        </div>
      </div>
      <div style="color:#F0F0F0;">

       @if($data['error'])
       @foreach($data['error'] as $message)
       <p>{{$message}}</p>
       @endforeach
       @endif

     </div>
     <div class="form-group">
      <div class="col-lg-10 col-lg-offset-2">
        <button type="reset" class="btn btn-danger">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
     </div>
    </fieldset>
  </form>
</div>
</div>
</body>
</html> 


