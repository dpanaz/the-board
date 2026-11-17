<?php
use Illuminate\Support\Facades\Schedule;
Schedule::command('board:meeting-reminders')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('board:send-push')->everyMinute()->withoutOverlapping();
