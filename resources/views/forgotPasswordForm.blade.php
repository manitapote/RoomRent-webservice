 <!DOCTYPE html>
 <html lang="en">

 <head>
  <title>Forgot Password</title>
</head>
<body>

  <h1>Room Rent</h1>

  <form action = "/api/password" method="post">
    {{csrf_field()}}
    @if($error)
    @foreach ($error->all() as $message)
    <p>{{$message}}</p>
    @endforeach

    @endif

    <input type = "hidden" name = "email" value = {{$email}}>
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


