<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="manifest" href="/manifest.webmanifest"><link rel="icon" href="/board-icon.svg" type="image/svg+xml"><meta name="theme-color" content="#11171d"><title>Join The Board</title></head>
<body style="background:#15191e;color:#f4eee3;font-family:system-ui;max-width:440px;margin:12vh auto;padding:24px">
<h1>Join The Board</h1><p>Registration requires a valid invitation and administrator approval.</p>
@if($errors->any())<p role="alert">{{ $errors->first() }}</p>@endif
<form method="post" action="{{ route('board.join.submit') }}">@csrf
<input type="hidden" name="token" value="{{ request('token') }}">
<label>Name <input name="name" required maxlength="255"></label><br><br>
<label>Password <input type="password" name="password" minlength="12" required></label><br><br>
<label>Confirm password <input type="password" name="password_confirmation" minlength="12" required></label><br><br>
<button type="submit">Request access</button></form></body></html>
