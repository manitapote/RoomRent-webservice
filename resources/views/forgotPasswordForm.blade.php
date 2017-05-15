 <!DOCTYPE html>
 <html lang="en">

 <head>
  <title>Forgot Password</title>
</head>
<body>

  <h1>Room Rent</h1>

  <form action = "/api/forgotpassword/change" method="post">
    {{csrf_field()}}
     @if($data['error'])
    @foreach($data['error'] as $message)
      <p>{{$message}}</p>
    @endforeach
    @endif
    <input type = "hidden" name = "token" value = {{$data['token']}}>
    <div>
      <label>Password</label>
      <input type="password" name = "newPassword" required/>
    </div>
    <div>
      <label>Confirm Password</label>
      <input type = "password" name = "newPassword_confirmation" required/>
    </div>
    <div>
      <input type = "submit" value = "Change password"/>
    </div>
  </form>
</body>
</html> 


