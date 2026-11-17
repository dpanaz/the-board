<?php
return [
 'defaults'=>['guard'=>'board','passwords'=>'board_members'],
 'guards'=>['board'=>['driver'=>'session','provider'=>'board_members']],
 'providers'=>['board_members'=>['driver'=>'eloquent','model'=>App\Models\BoardMember::class]],
 'passwords'=>['board_members'=>['provider'=>'board_members','table'=>'password_reset_tokens','expire'=>60,'throttle'=>60]],
 'password_timeout'=>10800,
];
