<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>The Board | Sign in</title></head>
<body style="background:#15191e;color:#f4eee3;font-family:system-ui;max-width:440px;margin:12vh auto;padding:24px">
<h1>THE BOARD</h1><p>A private Clubhouse community</p>
@if(session('status'))<p role="status">{{ session('status') }}</p>@endif
@if($errors->any())<p role="alert">{{ $errors->first() }}</p>@endif
<form method="post" action="{{ route('board.login.submit') }}">@csrf
<label>Email <input type="email" name="email" required autocomplete="username"></label><br><br>
<label>Password <input type="password" name="password" required autocomplete="current-password"></label><br><br>
<button type="submit">Sign in</button></form></body></html>
