<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="manifest" href="/manifest.webmanifest">
<link rel="icon" href="/board-icon.svg" type="image/svg+xml">
<meta name="theme-color" content="#11171d">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="NBTX Club">
<title>NBTX Club | Member Home</title>
<style>
:root{color-scheme:dark}*{box-sizing:border-box}body{margin:0;background:#11171d;color:#f5f0e8;font:16px system-ui,sans-serif}header{padding:22px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #3c4650}main{max-width:1100px;margin:auto;padding:24px}h1{font-family:Georgia,serif;letter-spacing:.12em}h2{font-size:1.1rem}button,input,textarea,select{font:inherit;padding:10px;border-radius:6px}button{cursor:pointer;background:#b99a61;color:#141414;border:0}input,textarea,select{background:#242e36;color:white;border:1px solid #5a636c;width:100%}section{background:#1c252d;padding:18px;border:1px solid #36414b;border-radius:10px;min-width:0} .layout{display:grid;grid-template-columns:1fr 1fr;gap:18px}.muted{color:#c1c7ca}.message{border-bottom:1px solid #36414b;padding:10px 0;white-space:pre-wrap;overflow-wrap:anywhere}.item{display:block;width:100%;text-align:left;background:#2b3740;color:white;margin:8px 0}.item:focus{outline:2px solid #b99a61}.messages{height:270px;overflow:auto}label{display:block;margin:12px 0}small{color:#b9c3ca}#notice{min-height:24px;color:#e3c98c}@media(max-width:750px){.layout{grid-template-columns:1fr}main{padding:12px}header{padding:12px}}

/* NBTX Club visual system */
:root{color-scheme:dark;--gold:#d8b477;--edge:#483a29}body{background:#0d0e0e;color:#f4ede2;font-family:system-ui,sans-serif}header{position:sticky;top:0;z-index:5;background:#141414;border-color:#443624;padding:18px 30px 18px 275px}header strong{font:24px Georgia,serif;color:var(--gold);letter-spacing:.1em}header button{background:transparent;color:var(--gold);border:1px solid var(--gold)}main{max-width:1500px;margin:0;padding:35px 35px 70px 285px}h1{font:clamp(36px,4vw,54px) Georgia,serif;letter-spacing:0;margin:14px 0}h2{font:23px Georgia,serif}section{background:linear-gradient(140deg,#1b1b1b,#151515);border:1px solid #45392c;border-radius:12px;padding:24px}.layout{gap:22px}button{background:linear-gradient(#e2c58c,#bc9457);color:#16120c;font-weight:600}input,textarea,select{background:#101010;border-color:#5c503e}.item{background:#2b241c}.muted,small{color:#bdb2a4}.message{border-color:#41372b}#notice{color:var(--gold)}.nbtx-sidebar{position:fixed;inset:0 auto 0 0;width:245px;background:linear-gradient(#181715,#101010);border-right:1px solid #453823;padding:24px 14px;overflow-y:auto;z-index:10}.nbtx-logo{display:block;margin:6px 8px 28px;border:1px solid #b99a63;color:#f6edda;text-decoration:none;text-align:center;padding:14px 8px;font:34px Georgia,serif;letter-spacing:.1em}.nbtx-logo span{display:block;font:11px system-ui;letter-spacing:.5em;color:#e2bd7d}.nbtx-sidebar nav{display:grid;gap:5px}.nbtx-sidebar nav a{display:block;color:#e8e0d4;text-decoration:none;padding:13px;border-radius:7px;font-size:14px}.nbtx-sidebar nav a:hover,.nbtx-sidebar nav a:first-child{background:#60482c}.nbtx-intro{padding-bottom:20px;border-bottom:1px solid #453823;margin-bottom:24px}.nbtx-eyebrow{color:var(--gold);font-size:11px;letter-spacing:.23em;text-transform:uppercase}.nbtx-links{display:flex;flex-wrap:wrap;gap:10px;margin:0 0 24px}.nbtx-links a{padding:12px 17px;border:1px solid #6a5233;border-radius:8px;color:#ead0a1;text-decoration:none;background:#241e16}@media(max-width:850px){.nbtx-sidebar{position:static;width:auto;padding:12px;border-right:0}.nbtx-logo{display:inline-block;font-size:23px;margin:0 0 8px}.nbtx-sidebar nav{display:flex;overflow-x:auto}.nbtx-sidebar nav a{white-space:nowrap;padding:9px}header{padding:12px 16px}main{padding:18px}.layout{grid-template-columns:1fr}}

/* Member dashboard refinement */
body{background:#10100f}.nbtx-sidebar{background:#151411}.nbtx-sidebar nav a:first-child{background:#3c3021;color:#f5d49b;border-left:2px solid #d8b477}
header{box-shadow:0 1px 0 #2d281f}main{padding-top:32px;max-width:1600px}
.dashboard-heading{display:flex;justify-content:space-between;align-items:center;gap:24px;margin-bottom:27px}
.dashboard-heading h1{font-size:clamp(30px,3vw,44px);margin:10px 0 8px;letter-spacing:-.035em}
.dashboard-heading p{margin:0;font-size:14px}.dashboard-date{color:#bcae98;font-size:13px;white-space:nowrap;border:1px solid #383027;border-radius:30px;padding:10px 15px}
.dashboard-shortcuts{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:38px}
.shortcut{min-width:0;display:flex;align-items:center;gap:12px;padding:21px 16px;background:#1c1b18;border:1px solid #393127;border-radius:12px;text-decoration:none;color:#f2ece1;transition:border-color .15s,transform .15s}
.shortcut:hover{border-color:#c39b5d;transform:translateY(-2px)}.shortcut-icon{font-size:23px;color:#d8b477;width:30px;flex-shrink:0;text-align:center}.shortcut strong{display:block;font-size:15px;font-weight:650}.shortcut small{display:block;font-size:11px;margin-top:4px;line-height:1.4}.shortcut-arrow{margin-left:auto;color:#b69a6d}
.dashboard-section-label{display:flex;align-items:end;justify-content:space-between;margin:0 0 17px;gap:12px}.dashboard-section-label h2{font:27px Georgia,serif;margin:6px 0 0}.dashboard-section-label>.muted{font-size:12px}
.activity-panel{margin-bottom:18px!important}.dashboard-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
.dashboard-grid>section,.activity-panel{background:#1a1917;border:1px solid #393228;padding:23px;border-radius:12px}
.dashboard-grid>section h2,.activity-panel h2{font:21px Georgia,serif;margin:0 0 18px;padding-bottom:15px;border-bottom:1px solid #342e26}
.dashboard-grid>section h2:not(:first-child){margin-top:25px}
.dashboard-grid .message{font-size:14px}.dashboard-grid label{font-size:13px;color:#d7cbbb}.dashboard-grid button{font-size:13px}
.settings-panel{margin-top:25px;border:1px solid #383027;border-radius:10px;background:#171613}.settings-panel summary{cursor:pointer;padding:16px 20px;color:#e8d5b2;font-size:14px}.settings-panel summary span{color:#998e7e;font-size:12px;margin-left:12px}.settings-panel section{border:0;background:transparent}
@media(max-width:1150px){.dashboard-shortcuts{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:700px){.dashboard-heading{align-items:start;flex-direction:column}.dashboard-date{display:none}.dashboard-shortcuts{gap:10px}.shortcut{padding:14px 11px}.shortcut small{display:none}.shortcut-icon{width:22px;font-size:19px}.shortcut-arrow{display:none}.dashboard-section-label>.muted{display:none}.dashboard-grid{grid-template-columns:1fr}.dashboard-grid>section,.activity-panel{padding:18px}.settings-panel summary span{display:block;margin:5px 0 0}}

.overview-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}.stat-card{display:flex;flex-direction:column;gap:9px;background:#1d1b18;border:1px solid #403629;border-radius:12px;padding:22px;text-decoration:none;color:#f5eee1}.stat-card:hover{border-color:#d8b477}.stat-card span{font-size:13px;color:#cfc2b0}.stat-card strong{font:36px Georgia,serif;color:#e6c38a}.stat-card small{font-size:11px;color:#b8a58b}.overview-top{display:grid;grid-template-columns:1.35fr 1fr;gap:18px;margin-bottom:30px}.overview-top section{background:#1b1a17;border:1px solid #403629;border-radius:12px;padding:23px}.overview-top h2{margin:10px 0 17px;font:23px Georgia,serif}.next-meeting-card #next-meeting-preview strong{display:block;font-size:17px;color:#f4e9d7}.next-meeting-card #next-meeting-preview p{margin:8px 0;font-size:13px}.overview-action{display:inline-block;margin-top:15px;color:#e2c18a;text-decoration:none;font-size:13px}.quick-actions-list{display:grid;grid-template-columns:1fr 1fr;gap:9px}.quick-actions-list a{border:1px solid #51412c;border-radius:8px;padding:12px;color:#e8d5b7;text-decoration:none;font-size:13px}.quick-actions-list a:hover{background:#33281b}.create-panel{margin:15px 0;border:1px solid #4d3c29;border-radius:8px;padding:0 12px}.create-panel summary{cursor:pointer;color:#e6c38a;font-size:13px;font-weight:600;padding:13px 0}.create-panel form{padding:5px 0 16px}.dashboard-grid{scroll-margin-top:90px}#board-meetings,#referrals,#opportunities,#directory,#conversations{scroll-margin-top:100px}
@media(max-width:1100px){.overview-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:700px){.overview-top{grid-template-columns:1fr}.overview-stats{gap:10px}.stat-card{padding:15px}.stat-card strong{font-size:29px}.quick-actions-list{grid-template-columns:1fr 1fr}}
.member-spotlight{display:flex;justify-content:space-between;align-items:center;gap:24px;padding:22px 26px;margin:0 0 24px;border:1px solid #53422c;border-radius:12px;background:linear-gradient(115deg,#242018,#191917)}.member-spotlight h2{font:24px Georgia,serif;margin:9px 0}.spotlight-details{display:flex;align-items:center;gap:20px;justify-content:space-between;min-width:40%}.spotlight-details strong{font-size:18px;color:#f3e4cd}.spotlight-details p{margin:6px 0}.spotlight-details small{color:#c6ac86}.spotlight-actions button{white-space:nowrap}.referral-tabs{display:flex;gap:7px;flex-wrap:wrap;margin:14px 0}.referral-tabs button{padding:8px 13px;font-size:12px;border-radius:7px}.referral-tabs button[aria-pressed="true"]{background:#c5a16a;color:#17130e;border-color:#c5a16a}@media(max-width:750px){.member-spotlight,.spotlight-details{display:block}.spotlight-actions{margin-top:12px}}
.directory-tools{display:grid;grid-template-columns:2fr 1fr;gap:12px;margin:15px 0}.directory-tools label{font-size:12px;color:#d2b98e}.directory-tools input,.directory-tools select{margin-top:6px}.directory-cards{display:grid;gap:10px}.directory-member{display:flex;align-items:center;gap:12px;border:1px solid #493b2a;background:#24211d;border-radius:10px;padding:13px}.directory-avatar{width:43px;height:43px;flex:0 0 43px;border-radius:50%;background:#55432a;color:#f4e6d2;display:grid;place-items:center;font-weight:700;font-size:13px}.directory-info{flex:1;min-width:0}.directory-info strong{display:block;color:#f5e9d5}.directory-info p{margin:4px 0;font-size:13px}.directory-info small{display:block;color:#baa88f;font-size:11px}.directory-connect{padding:8px 11px;font-size:12px;white-space:nowrap}@media(max-width:700px){.directory-tools{grid-template-columns:1fr}.directory-member{flex-wrap:wrap}.directory-connect{margin-left:55px}}
.referral-controls{display:grid;grid-template-columns:1fr 2fr;gap:12px;margin:14px 0}.referral-controls label{font-size:12px;color:#c9b393}.referral-summary{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin:12px 0}.referral-summary-item{padding:10px;border:1px solid #53432d;border-radius:8px;background:#252019;text-align:center}.referral-summary-item strong{display:block;font-size:21px;color:#e6c38a}.referral-summary-item small{font-size:11px}.referral-card{padding:14px 0;border-bottom:1px solid #473a2a}.referral-heading{display:flex;justify-content:space-between;gap:12px;align-items:center}.referral-heading strong{color:#f2e5d1}.referral-status{border:1px solid #8a704b;border-radius:20px;padding:4px 9px;font-size:11px;text-transform:capitalize;color:#e6c38a}.referral-card p{white-space:pre-wrap;overflow-wrap:anywhere}.referral-actions{display:flex;gap:9px;align-items:center;flex-wrap:wrap;margin-top:12px}.referral-actions select{width:auto;max-width:180px}.referral-followup{font-size:12px;padding:9px 12px}@media(max-width:700px){.referral-controls{grid-template-columns:1fr}.referral-summary{grid-template-columns:repeat(2,1fr)}}
.activity-heading{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}.activity-heading h2{margin:0}.activity-actions{display:flex;gap:7px;flex-wrap:wrap}.activity-actions button{padding:7px 11px;font-size:12px;background:#332b20;color:#e7d3b3;border:1px solid #58472e}.activity-actions button[aria-pressed="true"]{background:#c5a16a;color:#19140d}.activity-item{padding:13px 0;border-bottom:1px solid #493b2a}.activity-item-heading{display:flex;gap:9px;align-items:center}.activity-item-heading strong{color:#f0e3cf}.activity-new{border:1px solid #aa8650;border-radius:20px;padding:2px 7px;font-size:10px;color:#e5c18c}.activity-item p{margin:9px 0;white-space:pre-wrap;overflow-wrap:anywhere}.activity-item-actions{display:flex;gap:12px;align-items:center}.activity-item-actions a{color:#e3c98c;font-size:13px}.activity-item-actions button{padding:6px 10px;font-size:12px}
.directory-tools{grid-template-columns:2fr 1fr 1fr}.directory-bio{font-size:12px;line-height:1.5;color:#c5b9a7;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;white-space:normal}.directory-website{display:inline-block;color:#e6c38a;font-size:12px;margin-top:7px}.conversation-search-label{font-size:12px;color:#d0b68c}.conversation-search-label input{margin-top:6px}.item[aria-pressed="true"]{outline:2px solid #c5a16a}@media(max-width:700px){.directory-tools{grid-template-columns:1fr}}
.meeting-controls{display:grid;grid-template-columns:1fr 2fr;gap:12px;margin:10px 0}.meeting-controls label{font-size:12px;color:#d5c2a4}.meeting-controls input,.meeting-controls select{margin-top:5px}@media(max-width:700px){.meeting-controls{grid-template-columns:1fr}}
.opportunity-controls{display:grid;grid-template-columns:1fr 2fr;gap:12px;margin:10px 0}.opportunity-controls label{font-size:12px;color:#d5c2a4}.opportunity-controls input,.opportunity-controls select{margin-top:5px}@media(max-width:700px){.opportunity-controls{grid-template-columns:1fr}}
.profile-readiness{padding:14px;border:1px solid #57472f;border-radius:10px;margin:12px 0;background:rgba(197,161,106,.06)}.profile-readiness-heading{display:flex;justify-content:space-between;gap:12px;align-items:center}.profile-readiness progress{display:block;width:100%;height:12px;accent-color:#c5a16a;margin-top:10px}.profile-readiness .muted{margin:9px 0 0;font-size:12px;line-height:1.5}
.chat-message{margin:8px 0;overflow-wrap:anywhere}.chat-message small{display:block;color:#d1b783;font-size:11px;margin-bottom:5px}.chat-message p{margin:0;white-space:pre-wrap;line-height:1.5}.chat-message-own{border-left:3px solid #c5a16a;padding-left:12px;background:rgba(197,161,106,.06)}.chat-compose-footer{display:flex;justify-content:space-between;align-items:center;gap:12px}.chat-compose-footer small{color:#b7a88f;font-size:11px}
.meeting-calendar-button{font-size:12px;padding:7px 10px;margin:8px 10px 8px 0;background:#3a3023;color:#e7cf9e;border:1px solid #7d633f}
</style>
</head>
<body>
<aside class="nbtx-sidebar"><a class="nbtx-logo" href="{{ route('board.home') }}">NBTX<span>CLUB</span></a><nav aria-label="Member navigation"><a href="{{ route('board.home') }}">⌂ &nbsp; Home</a><a href="#directory">◉ &nbsp; Directory</a><a href="#conversations">▤ &nbsp; Messages</a><a href="#board-meetings">▦ &nbsp; Meetings</a><a href="#referrals">↗ &nbsp; Referrals</a><a href="#opportunities">✦ &nbsp; Asks &amp; offers</a><a href="#profile">⚙ &nbsp; Profile</a>@if(in_array(auth('board')->user()->role, ['admin','super_admin'], true))<a href="{{ route('board.admin') }}">⚙ &nbsp; Admin</a>@endif</nav></aside>
<header><strong>NBTX CLUB</strong><form method="post" action="{{ route('board.logout') }}">@csrf<button type="submit">Sign out</button></form></header>
<main>
<div class="dashboard-heading"><div><span class="nbtx-eyebrow">MEMBER OVERVIEW · NEW BRAUNFELS</span><h1>Good to see you, {{ explode(' ', trim(auth('board')->user()->name))[0] }}.</h1><p class="muted">Your connections, conversations, and opportunities—all in one place.</p></div><div class="dashboard-date">{{ now()->format('l, F j') }}</div></div>
<div class="overview-stats" aria-label="Network overview">
<a href="#conversations" class="stat-card"><span>Unread messages</span><strong id="stat-messages">—</strong><small>Open conversations ↗</small></a>
<a href="#board-meetings" class="stat-card"><span>Upcoming meetings</span><strong id="stat-meetings">—</strong><small>View your calendar ↗</small></a>
<a href="#referrals" class="stat-card"><span>New referrals</span><strong id="stat-referrals">—</strong><small>Review referrals ↗</small></a>
<a href="#opportunities" class="stat-card"><span>Open opportunities</span><strong id="stat-opportunities">—</strong><small>Explore member posts ↗</small></a>
</div>
<div class="overview-top">
<section class="next-meeting-card"><span class="nbtx-eyebrow">UP NEXT</span><h2>Next meeting</h2><div id="next-meeting-preview" class="muted">Loading upcoming meetings…</div><a href="#board-meetings" class="overview-action">All meetings ↗</a></section>
<section class="quick-actions-card"><span class="nbtx-eyebrow">QUICK ACTIONS</span><h2>Connect with your network</h2><div class="quick-actions-list"><a href="#directory">Find a member ↗</a><a href="#new-conversation">Start a conversation ↗</a><a href="#new-referral">Send a referral ↗</a><a href="#new-opportunity">Post an opportunity ↗</a></div></section>
</div>
<section class="member-spotlight" aria-label="Member spotlight"><div><span class="nbtx-eyebrow">DISCOVER YOUR NETWORK</span><h2>Member spotlight</h2><p class="muted">Get to know a fellow New Braunfels business owner.</p></div><div id="member-spotlight-content" class="spotlight-details">Loading a member…</div></section>
<div class="dashboard-section-label"><div><span class="nbtx-eyebrow">YOUR NETWORK</span><h2>What's happening</h2></div><span class="muted">Stay connected with your community</span></div>

@if(in_array(auth('board')->user()->role, ['admin', 'super_admin'], true))
<p><a href="{{ route('board.admin') }}" style="color:#e3c98c">Manage invitations and approvals</a></p>
@endif<p id="notice" role="status" aria-live="polite"></p>
<details class="settings-panel"><summary>Notification settings <span>Manage device alerts and preferences</span></summary><section><h2>Phone notifications</h2><p class="muted">Enable alerts on this device. On iPhone, add NBTX Club to your Home Screen first, then open it there to enable notifications.</p>
<button type="button" id="enable-board-push">Enable notifications</button>
<button type="button" id="disable-board-push">Disable on this device</button>
<button type="button" id="test-board-push">Send test alert</button>
<form id="push-preferences"><label><input type="checkbox" name="messages" checked style="width:auto"> Chat messages</label>
<label><input type="checkbox" name="meetings" checked style="width:auto"> Meeting announcements and reminders</label>
<label><input type="checkbox" name="referrals" checked style="width:auto"> Business referrals</label>
<button type="submit">Save notification preferences</button></form>
<p id="push-status" class="muted" aria-live="polite"></p><p id="push-test-status" class="muted" aria-live="polite"></p></section></details>
<section class="activity-panel"><div class="activity-heading"><h2>Recent activity <span id="activity-count"></span></h2><div class="activity-actions"><button type="button" id="activity-all" aria-pressed="true">All</button><button type="button" id="activity-unread" aria-pressed="false">Unread</button><button type="button" id="activity-refresh">Refresh ↻</button></div></div><div id="board-activity">Loading updates…</div></section>
<div class="layout dashboard-grid">
<section class="dashboard-panel"><h2>Meetings</h2><div class="meeting-controls"><label>Show<select id="meeting-view"><option value="upcoming">Upcoming</option><option value="past">Past meetings</option><option value="all">All meetings</option></select></label><label>Find a meeting<input id="meeting-search" type="search" placeholder="Search title, location or agenda"></label></div><p id="meeting-result-count" class="muted" role="status"></p><div id="board-meetings">Loading meetings…</div>
@if(in_array(auth('board')->user()->role, ['admin', 'super_admin'], true))
<details class="create-panel"><summary>+ Schedule a meeting</summary><form id="new-meeting">
<label>Title<input name="title" required maxlength="180"></label>
<label>Start<input type="datetime-local" name="starts_at" required></label>
<label>End<input type="datetime-local" name="ends_at" required></label>
<label>Location<input name="location" maxlength="255" placeholder="The Clubhouse"></label>
<label>Agenda<textarea name="description" maxlength="5000" rows="3"></textarea></label>
<button type="submit" id="create-meeting-button">Create meeting</button><p id="meeting-form-status" class="muted" role="status" aria-live="polite"></p></form></details>
@endif</section>

<section class="dashboard-panel"><h2>Business referrals</h2><p class="muted">Share a business opportunity privately with another member. Avoid including confidential customer information without permission.</p>
<details class="create-panel"><summary>+ Send a referral</summary><form id="new-referral"><label>Send to<select name="to_member_id" id="referral-recipient" required><option value="">Select a member</option></select></label>
<label>Opportunity<input name="subject" maxlength="180" required></label>
<label>Details<textarea name="details" maxlength="3000" rows="3"></textarea></label>
<button type="submit">Send referral</button></form></details><div class="referral-tabs" role="group" aria-label="Filter referrals"><button type="button" data-referral-view="all" aria-pressed="true">All</button><button type="button" data-referral-view="received" aria-pressed="false">Received</button><button type="button" data-referral-view="sent" aria-pressed="false">Sent</button></div><div class="referral-controls"><label>Status<select id="referral-status-filter"><option value="">All statuses</option><option value="new">New</option><option value="accepted">Accepted</option><option value="completed">Completed</option><option value="declined">Declined</option></select></label><label>Find a referral<input id="referral-search" type="search" placeholder="Search subject or member"></label><label>Sort<select id="referral-sort"><option value="recent">Newest first</option><option value="oldest">Oldest first</option><option value="subject">Subject A–Z</option></select></label></div><div id="referral-summary" class="referral-summary" aria-live="polite"></div><div id="referrals">Loading referrals…</div></section>
<section class="dashboard-panel"><h2>Member asks &amp; offers</h2><p class="muted">Ask fellow owners for advice, resources or introductions—or share something your business can offer.</p>
<details class="create-panel"><summary>+ Post an opportunity</summary><form id="new-opportunity"><label>Post type<select name="type"><option value="ask">I need help with…</option><option value="offer">I can help with…</option></select></label>
<label>Headline<input name="title" maxlength="180" required></label><label>Details<textarea name="details" maxlength="3000" rows="3" required></textarea></label>
<button type="submit">Post to NBTX Club</button></form></details>
<div class="opportunity-controls"><label>Show<select id="opportunity-filter"><option value="">All open posts</option><option value="ask">Requests</option><option value="offer">Offers</option></select></label><label>Search posts<input type="search" id="opportunity-search" placeholder="Search members, businesses or details"></label><label>Sort<select id="opportunity-sort"><option value="newest">Newest first</option><option value="oldest">Oldest first</option><option value="title">Title A–Z</option></select></label></div><p id="opportunity-result-count" class="muted" role="status"></p><div id="opportunities">Loading opportunities…</div></section>
<section class="dashboard-panel"><h2>Member directory</h2><div class="directory-tools"><label>Search members<input id="directory-search" type="search" placeholder="Search name, company, or industry" autocomplete="off"></label><label>Industry<select id="directory-industry"><option value="">All industries</option></select></label><label>Sort by<select id="directory-sort"><option value="name">Name A–Z</option><option value="company">Company A–Z</option></select></label></div><p id="directory-result-count" class="muted" role="status"></p><div id="directory" class="directory-cards">Loading members…</div></section>
<section class="dashboard-panel"><h2>My business profile</h2><div class="profile-readiness"><div class="profile-readiness-heading"><strong id="profile-readiness-score">Profile readiness</strong><span id="profile-readiness-count"></span></div><progress id="profile-readiness-progress" max="100" value="0"></progress><p class="muted" id="profile-readiness-hint">Complete your profile so other members can discover your business.</p></div>
<details class="create-panel" id="profile-editor"><summary>Edit business profile</summary><form id="profile">
<label>Display name<input name="name" required value="{{ auth('board')->user()->name }}"></label>
<label>Company<input name="company" value="{{ auth('board')->user()->company }}"></label>
<label>Industry<input name="industry" value="{{ auth('board')->user()->industry }}"></label>
<label>Title<input name="title" value="{{ auth('board')->user()->title }}"></label>
<label>Website<input name="website" type="url" value="{{ auth('board')->user()->website }}"></label>
<label>About me<textarea name="bio" rows="3">{{ auth('board')->user()->bio }}</textarea></label>
<button type="submit">Save profile</button></form></details></section>
<section class="dashboard-panel"><h2>Conversations</h2><label class="conversation-search-label">Find conversation<input type="search" id="conversation-search" placeholder="Search conversations" autocomplete="off"></label><details class="create-panel"><summary>+ Start a conversation</summary><form id="new-conversation"><label>Conversation type<select name="type" id="conversation-type"><option value="direct">Direct message</option><option value="group">Group chat</option></select></label><label>Members<select name="member_ids" id="conversation-members" multiple size="5" required></select></label><label>Group name (for group chats)<input name="name" maxlength="120" placeholder="Business owners discussion"></label><button type="submit" id="start-conversation-button">Start conversation</button><p id="conversation-form-status" class="muted" role="status" aria-live="polite">Select a member to begin messaging.</p></form></details><div id="conversations">Loading conversations…</div></section>
<section class="dashboard-panel"><h2 id="conversation-title">Chat</h2><div class="messages" id="messages" aria-live="polite">Choose a conversation to view messages.</div>
<form id="chat"><label>Message<textarea name="body" rows="2" maxlength="5000" required placeholder="Write a message…"></textarea></label><div class="chat-compose-footer"><small id="chat-character-count">0 / 5000 characters</small><button type="submit" id="send" disabled>Send</button></div></form></section>
</div>
</main>
<script>
const pushStatus=document.getElementById('push-status');
async function loadPushPreferences(){
 if(!('serviceWorker' in navigator)||!('PushManager' in window))return;
 const registration=await navigator.serviceWorker.getRegistration('/');
 const subscription=registration?await registration.pushManager.getSubscription():null;
 if(!subscription)return;
 const data=await api('/api/push/preferences');
 const hash=await crypto.subtle.digest('SHA-256',new TextEncoder().encode(subscription.endpoint));
 const device=Array.from(new Uint8Array(hash)).map(x=>x.toString(16).padStart(2,'0')).join('').slice(0,12);
 const entry=data.devices.find(item=>item.device===device);
 if(!entry)return;
 const form=document.getElementById('push-preferences');
 for(const name of ['messages','meetings','referrals'])form.elements[name].checked=entry.preferences[name]!==false;
}

function base64ToBytes(base64){
 const padding='='.repeat((4-base64.length%4)%4);
 const binary=atob((base64+padding).replace(/-/g,'+').replace(/_/g,'/'));
 return Uint8Array.from(binary,character=>character.charCodeAt(0));
}
async function pushRegistration(){
 if(!('serviceWorker' in navigator)||!('PushManager' in window)||!('Notification' in window))throw new Error('Push notifications are not supported in this browser.');
 return navigator.serviceWorker.register('/board-sw.js',{scope:'/'});
}
document.getElementById('enable-board-push').onclick=async()=>{
 try{
  const key=@json(config('services.webpush.public_key'));
  if(!key)throw new Error('Phone alerts are not configured yet. Contact the administrator.');
  const permission=await Notification.requestPermission();
  if(permission!=='granted')throw new Error('Notification permission was not granted.');
  const registration=await pushRegistration();
  let subscription=await registration.pushManager.getSubscription();
  if(!subscription)subscription=await registration.pushManager.subscribe({userVisibleOnly:true,applicationServerKey:base64ToBytes(key)});
  const payload=subscription.toJSON();
  await api('/api/push/subscribe',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({endpoint:payload.endpoint,keys:payload.keys})});
  pushStatus.textContent='This device is subscribed to Board notifications.';
  await loadPushPreferences();
 }catch(error){pushStatus.textContent=error.message}
};
document.getElementById('push-preferences').onsubmit=async event=>{
 event.preventDefault();
 try{
  const registration=await navigator.serviceWorker.getRegistration('/');
  const subscription=registration?await registration.pushManager.getSubscription():null;
  if(!subscription)throw new Error('Enable notifications on this device first.');
  const form=event.currentTarget;
  await api('/api/push/preferences',{method:'PATCH',headers:{'Content-Type':'application/json'},body:JSON.stringify({
   endpoint:subscription.endpoint,
   messages:form.elements.messages.checked,
   meetings:form.elements.meetings.checked,
   referrals:form.elements.referrals.checked
  })});
  pushStatus.textContent='Notification preferences saved for this device.';
 }catch(error){pushStatus.textContent=error.message}
};

document.getElementById('disable-board-push').onclick=async()=>{
 try{
  const registration=await navigator.serviceWorker.getRegistration('/');
  const subscription=registration?await registration.pushManager.getSubscription():null;
  if(subscription){
   await api('/api/push/unsubscribe',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({endpoint:subscription.endpoint})});
   await subscription.unsubscribe();
  }
  pushStatus.textContent='Notifications disabled on this device.';
 }catch(error){pushStatus.textContent=error.message}
};

const csrf=document.querySelector('meta[name="csrf-token"]').content;
const notice=document.getElementById('notice');
let selectedConversation=null;
async function api(path,options={}){
 const response=await fetch(path,{credentials:'same-origin',...options,headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf,...(options.headers||{})}});
 if(!response.ok){let message=response.status===401?'Please sign in again.':response.status===403?'Access denied':response.status===419?'Session expired — refresh the page and try again.':'Request failed ('+response.status+')';try{const details=await response.json();if(details.message)message=details.message;if(details.errors)message+=': '+Object.values(details.errors).flat().join('; ')}catch(_){}throw new Error(message)}
 return response.json();
}
function el(tag,text,className){const node=document.createElement(tag);node.textContent=text;if(className)node.className=className;return node}
function showMemberSpotlight(members){
 const root=document.getElementById('member-spotlight-content');root.replaceChildren();
 const eligible=members.filter(m=>m.id!=={{ auth('board')->id() }});
 if(!eligible.length){root.textContent='Member spotlights will appear as the network grows.';return;}
 const day=Math.floor(Date.now()/86400000);
 const member=eligible[day%eligible.length];
 const info=document.createElement('div');info.append(el('strong',member.name),el('p',member.company||'Local business owner'));
 if(member.industry)info.append(el('small',member.industry));
 const actions=document.createElement('div');actions.className='spotlight-actions';
 const connect=document.createElement('button');connect.type='button';connect.textContent='Start a conversation';
 connect.onclick=()=>{const select=document.getElementById('conversation-members');for(const option of select.options)option.selected=Number(option.value)===member.id;const form=document.getElementById('new-conversation');const panel=form.closest('details');if(panel)panel.open=true;form.scrollIntoView({behavior:'smooth',block:'center'});};
 actions.append(connect);root.append(info,actions);
}
let directoryMembers=[];
function startMemberConversation(member){
 const select=document.getElementById('conversation-members');
 for(const option of select.options)option.selected=Number(option.value)===member.id;
 const form=document.getElementById('new-conversation');const panel=form.closest('details');
 if(panel)panel.open=true;
 form.scrollIntoView({behavior:'smooth',block:'center'});
}
function renderDirectory(){
 const search=document.getElementById('directory-search').value.trim().toLowerCase();
 const industry=document.getElementById('directory-industry').value;
 const root=document.getElementById('directory');root.replaceChildren();
 const filtered=directoryMembers.filter(m=>(!industry||(m.industry||'')===industry)&&(!search||[m.name,m.company,m.industry,m.title,m.bio].some(v=>String(v||'').toLowerCase().includes(search))));const sort=document.getElementById('directory-sort').value;filtered.sort((a,b)=>String(a[sort]||a.name||'').localeCompare(String(b[sort]||b.name||'')));
 document.getElementById('directory-result-count').textContent=filtered.length+' member'+(filtered.length===1?'':'s')+' found';
 for(const member of filtered){
  const card=el('article','','directory-member');
  const initials=el('span',String(member.name||'?').trim().split(/\\s+/).slice(0,2).map(n=>n[0]||'').join('').toUpperCase(),'directory-avatar');
  const info=el('div','','directory-info');info.append(el('strong',member.name),el('p',member.company||'Business owner'));
  if(member.industry)info.append(el('small',member.industry));
  if(member.title)info.append(el('small',member.title));
  if(member.bio)info.append(el('p',member.bio,'directory-bio'));
  if(member.website){try{const url=new URL(member.website);if(['https:','http:'].includes(url.protocol)){const link=document.createElement('a');link.href=url.href;link.target='_blank';link.rel='noopener noreferrer';link.textContent='Visit website ↗';link.className='directory-website';info.append(link);}}catch(error){}}
  card.append(initials,info);
  if(member.id!=={{ auth('board')->id() }}){
   const button=el('button','Message','directory-connect');button.type='button';button.onclick=()=>startMemberConversation(member);card.append(button);
  }
  root.append(card);
 }
 if(!filtered.length)root.textContent='No members match your search.';
}
document.getElementById('directory-search').addEventListener('input',renderDirectory);
document.getElementById('directory-industry').addEventListener('change',renderDirectory);
document.getElementById('directory-sort').addEventListener('change',renderDirectory);
async function loadDirectory(){
 const data=await api('/api/members');directoryMembers=data.data;showMemberSpotlight(data.data);
 const industrySelect=document.getElementById('directory-industry');const selected=industrySelect.value;
 industrySelect.replaceChildren(new Option('All industries',''));
 for(const industry of [...new Set(data.data.map(m=>m.industry).filter(Boolean))].sort())industrySelect.append(new Option(industry,industry));
 industrySelect.value=selected;
 const membersSelect=document.getElementById('conversation-members');membersSelect.replaceChildren();
 const referralSelect=document.getElementById('referral-recipient');referralSelect.replaceChildren(new Option('Select a member',''));
 for(const member of data.data){if(member.id!=={{ auth('board')->id() }}){
  const option=document.createElement('option');option.value=member.id;option.textContent=member.name+' — '+(member.company||'');membersSelect.append(option);
  referralSelect.append(new Option(member.name+' — '+(member.company||''),member.id));
 }}
 renderDirectory();
}
let cachedConversations=[];
function renderConversations(){
 const root=document.getElementById('conversations');root.replaceChildren();
 const query=document.getElementById('conversation-search').value.trim().toLowerCase();
 const visible=cachedConversations.filter(c=>String(c.name||('Conversation #'+c.id)).toLowerCase().includes(query));
 for(const conversation of visible){
  const button=el('button',(conversation.unread_count ? '● '+conversation.unread_count+' new · ' : '')+(conversation.name||('Conversation #'+conversation.id)),'item');
  button.type='button';button.setAttribute('aria-pressed',String(selectedConversation===conversation.id));
  button.onclick=()=>{selectedConversation=conversation.id;document.getElementById('conversation-title').textContent=conversation.name||'Direct message';document.getElementById('send').disabled=false;renderConversations();loadMessages().catch(showError)};
  root.append(button);
 }
 if(!visible.length)root.textContent=query?'No conversations match your search.':'No conversations yet.';
}
document.getElementById('conversation-search').addEventListener('input',renderConversations);
async function loadConversations(){
 const data=await api('/api/conversations');cachedConversations=data.data;
 document.getElementById('stat-messages').textContent=data.data.reduce((sum,c)=>sum+(Number(c.unread_count)||0),0);
 renderConversations();
}
async function loadMessages(){
 if(!selectedConversation)return;
 const id=selectedConversation;const data=await api('/api/conversations/'+id+'/messages');if(selectedConversation!==id)return;
 const root=document.getElementById('messages');root.replaceChildren();
 for(const message of data.data.reverse()){
  const own=Number(message.sender_id)==={{ auth('board')->id() }};
  const card=el('div','','message chat-message'+(own?' chat-message-own':''));
  const sender=directoryMembers.find(m=>Number(m.id)===Number(message.sender_id));
  card.append(el('small',own?'You':sender?.name||'Member #'+message.sender_id));
  card.append(el('p',message.body));root.append(card);
 }
 root.scrollTop=root.scrollHeight;
 if(!data.data.length)root.textContent='Start the conversation.';
 await loadConversations();
}
function showError(error){notice.textContent=error.message;notice.scrollIntoView({behavior:'smooth',block:'nearest'})}
document.getElementById('new-conversation').onsubmit=async event=>{
 event.preventDefault();const form=event.currentTarget;const type=form.elements.type.value;
 const status=document.getElementById('conversation-form-status');const button=document.getElementById('start-conversation-button');
 const member_ids=Array.from(document.getElementById('conversation-members').selectedOptions).map(option=>Number(option.value));
 if(!member_ids.length){status.textContent='Select at least one member to message.';return;}
 if(type==='direct'&&member_ids.length!==1){status.textContent='Select exactly one member for a direct message.';return;}
 button.disabled=true;status.textContent='Starting conversation…';
 try{const created=await api('/api/conversations',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({type,name:form.elements.name.value,member_ids})});
 form.reset();await loadConversations();selectedConversation=created.id;document.getElementById('send').disabled=false;await loadMessages();status.textContent='Conversation ready. Type your message in the Chat panel.';notice.textContent='Conversation created.';document.getElementById('chat').scrollIntoView({behavior:'smooth',block:'center'});}
 catch(error){status.textContent=error.message;showError(error)}finally{button.disabled=false}
};
function updateProfileReadiness(){
 const form=document.getElementById('profile');
 const fields=[['name','display name'],['company','company'],['industry','industry'],['title','role or title'],['website','website'],['bio','business description']];
 const missing=fields.filter(([key])=>!String(form.elements.namedItem(key)?.value||'').trim());
 const complete=fields.length-missing.length;const score=Math.round(complete/fields.length*100);
 document.getElementById('profile-readiness-progress').value=score;
 document.getElementById('profile-readiness-count').textContent=score+'% complete';
 document.getElementById('profile-readiness-hint').textContent=missing.length?'Add '+missing.map(([,label])=>label).join(', ')+' to help members get to know your business.':'Your profile is complete and ready for other members to discover.';
}
document.getElementById('profile').addEventListener('input',updateProfileReadiness);
updateProfileReadiness();
document.getElementById('profile').onsubmit=async event=>{
 event.preventDefault();const form=new FormData(event.currentTarget);
 try{await api('/api/profile',{method:'PATCH',headers:{'Content-Type':'application/json'},body:JSON.stringify(Object.fromEntries(form))});notice.textContent='Profile saved.';updateProfileReadiness();await loadDirectory()}catch(error){showError(error)}
};
const chatTextarea=document.querySelector('#chat textarea[name="body"]');
const chatCount=document.getElementById('chat-character-count');
chatTextarea.addEventListener('input',()=>{chatCount.textContent=chatTextarea.value.length+' / 5000 characters'});
chatTextarea.addEventListener('keydown',event=>{if(event.key==='Enter'&&(event.ctrlKey||event.metaKey)){event.preventDefault();chatTextarea.form.requestSubmit()}});
document.getElementById('chat').onsubmit=async event=>{
 event.preventDefault();if(!selectedConversation)return;
 const body=new FormData(event.currentTarget).get('body');
 const send=document.getElementById('send');send.disabled=true;
 try{await api('/api/conversations/'+selectedConversation+'/messages',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({body})});event.currentTarget.reset();chatCount.textContent='0 / 5000 characters';await loadMessages()}catch(error){showError(error)}finally{send.disabled=!selectedConversation}
};
let cachedMeetings=[];
document.getElementById('meeting-view').addEventListener('change',renderMeetings);
document.getElementById('meeting-search').addEventListener('input',renderMeetings);
function meetingCalendarLink(meeting){
 const utc=value=>{const d=new Date(value);return Number.isNaN(d.getTime())?'':d.toISOString().replace(/[-:]/g,'').replace(/\\.\\d{3}Z$/,'Z')};
 const escapeText=value=>String(value||'').replace(/\\\\/g,'\\\\\\\\').replace(/\r?\n/g,'\\\\n').replace(/,/g,'\\\\,').replace(/;/g,'\\\\;');
 const start=utc(meeting.starts_at),end=utc(meeting.ends_at);
 if(!start||!end)return null;
 const lines=['BEGIN:VCALENDAR','VERSION:2.0','PRODID:-//NBTX Club//Meetings//EN','BEGIN:VEVENT','UID:nbtx-meeting-'+meeting.id+'@nbtx.club','DTSTAMP:'+utc(new Date()),'DTSTART:'+start,'DTEND:'+end,'SUMMARY:'+escapeText(meeting.title),'LOCATION:'+escapeText(meeting.location),'DESCRIPTION:'+escapeText(meeting.description),'END:VEVENT','END:VCALENDAR'];
 const blob=new Blob([lines.join('\\r\\n')+'\\r\\n'],{type:'text/calendar;charset=utf-8'});
 return URL.createObjectURL(blob);
}
function renderMeetings(){
 const root=document.getElementById('board-meetings');root.replaceChildren();
 const view=document.getElementById('meeting-view').value;
 const query=document.getElementById('meeting-search').value.trim().toLowerCase();
 const now=Date.now();
 const visible=cachedMeetings.filter(m=>(view==='all'||(view==='upcoming'?new Date(m.starts_at).getTime()>=now:new Date(m.starts_at).getTime()<now))&&(!query||[m.title,m.location,m.description].some(v=>String(v||'').toLowerCase().includes(query)))).sort((a,b)=>view==='past'?new Date(b.starts_at)-new Date(a.starts_at):new Date(a.starts_at)-new Date(b.starts_at));
 document.getElementById('meeting-result-count').textContent=visible.length+' meeting'+(visible.length===1?'':'s')+' found';
 const upcoming=cachedMeetings.filter(m=>new Date(m.starts_at).getTime()>=Date.now()).sort((a,b)=>new Date(a.starts_at)-new Date(b.starts_at));
 document.getElementById('stat-meetings').textContent=upcoming.length;
 const preview=document.getElementById('next-meeting-preview');preview.replaceChildren();
 if(upcoming.length){const next=upcoming[0];preview.append(el('strong',next.title),el('p',new Date(next.starts_at).toLocaleString()),el('p',next.location||'Location to be announced'));if(next.my_rsvp)preview.append(el('small','Your RSVP: '+next.my_rsvp));}
 else preview.textContent='No upcoming meetings scheduled.';
 for(const meeting of visible){
  const card=document.createElement('div');card.className='message';
  card.append(el('strong',meeting.title),el('div',new Date(meeting.starts_at).toLocaleString()+' – '+new Date(meeting.ends_at).toLocaleTimeString()),el('div',meeting.location||'Location to be announced'));
  if(meeting.description)card.append(el('p',meeting.description));
  const calendar=document.createElement('button');calendar.type='button';calendar.textContent='Add to calendar';calendar.className='meeting-calendar-button';
  calendar.onclick=()=>{const url=meetingCalendarLink(meeting);if(!url){notice.textContent='Meeting dates are unavailable.';return;}const link=document.createElement('a');link.href=url;link.download='nbtx-club-meeting-'+meeting.id+'.ics';document.body.append(link);link.click();link.remove();setTimeout(()=>URL.revokeObjectURL(url),1000)};
  card.append(calendar);
  card.append(el('small',meeting.attending_count+' attending'));
  const select=document.createElement('select');
  for(const [value,label] of [['','Your RSVP'],['going','Going'],['maybe','Maybe'],['declined','Cannot attend']]){
   const option=document.createElement('option');option.value=value;option.textContent=label;select.append(option);
  }
  select.value=meeting.my_rsvp||'';
  select.onchange=async()=>{if(!select.value)return;try{await api('/api/meetings/'+meeting.id+'/rsvp',{method:'PUT',headers:{'Content-Type':'application/json'},body:JSON.stringify({response:select.value})});notice.textContent='RSVP saved.';await loadMeetings()}catch(error){showError(error)}};
  card.append(select);root.append(card);
 }
 if(!visible.length)root.textContent=query?'No meetings match your search.':view==='past'?'No past meetings.':'No meetings to show.';
}
async function loadMeetings(){
 const root=document.getElementById('board-meetings');
 try{
  const data=await api('/api/meetings');
  if(!Array.isArray(data.data))throw new Error('Unexpected meetings response. Please contact support.');
  cachedMeetings=data.data;renderMeetings();
 }catch(error){
  root.replaceChildren();
  const message=document.createElement('p');message.setAttribute('role','alert');message.textContent='Could not load meetings: '+error.message;
  const retry=document.createElement('button');retry.type='button';retry.textContent='Retry loading meetings';retry.onclick=()=>{root.textContent='Loading meetings…';loadMeetings()};
  root.append(message,retry);
  throw error;
 }
}
const meetingForm=document.getElementById('new-meeting');
if(meetingForm)meetingForm.onsubmit=async event=>{
 event.preventDefault();const form=event.currentTarget;const fields=Object.fromEntries(new FormData(form));
 const status=document.getElementById('meeting-form-status');const button=document.getElementById('create-meeting-button');
 const start=new Date(fields.starts_at),end=new Date(fields.ends_at);
 if(!Number.isFinite(start.getTime())||!Number.isFinite(end.getTime())){status.textContent='Enter valid meeting start and end dates.';return;}
 if(end<=start){status.textContent='The meeting end must be after the start.';return;}
 button.disabled=true;status.textContent='Creating meeting…';
 try{await api('/api/meetings',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({...fields,starts_at:start.toISOString(),ends_at:end.toISOString()})});form.reset();status.textContent='Meeting created successfully.';notice.textContent='Meeting created.';await loadMeetings()}
 catch(error){status.textContent=error.message;showError(error)}finally{button.disabled=false}
};
let activityItems=[];
let activityFilter='all';
function renderActivity(){
 const root=document.getElementById('board-activity');root.replaceChildren();
 const visible=activityItems.filter(item=>activityFilter==='all'||!item.read_at);
 for(const item of visible){
  const card=el('article','','activity-item');
  const heading=el('div','','activity-item-heading');heading.append(el('strong',item.title));
  if(!item.read_at)heading.append(el('span','New','activity-new'));
  card.append(heading);
  if(item.body)card.append(el('p',item.body));
  const actions=el('div','','activity-item-actions');
  if(item.url){const link=document.createElement('a');link.href=item.url;link.textContent='View details ↗';actions.append(link);}
  if(!item.read_at){
   const button=document.createElement('button');button.type='button';button.textContent='Mark read';
   button.onclick=async()=>{button.disabled=true;try{await api('/api/notifications/'+item.id+'/read',{method:'PATCH'});await loadActivity()}catch(error){button.disabled=false;showError(error)}};
   actions.append(button);
  }
  card.append(actions);root.append(card);
 }
 if(!visible.length)root.textContent=activityFilter==='unread'?'You are all caught up — no unread updates.':'No activity yet.';
}
for(const [id,view] of [['activity-all','all'],['activity-unread','unread']]){
 document.getElementById(id).addEventListener('click',()=>{
  activityFilter=view;
  document.getElementById('activity-all').setAttribute('aria-pressed',String(view==='all'));
  document.getElementById('activity-unread').setAttribute('aria-pressed',String(view==='unread'));
  renderActivity();
 });
}
document.getElementById('activity-refresh').addEventListener('click',()=>loadActivity().catch(showError));
async function loadActivity(){
 const data=await api('/api/notifications');activityItems=data.items||[];
 const unread=activityItems.filter(item=>!item.read_at).length;
 document.getElementById('activity-count').textContent=(data.unread_count??unread)?'('+(data.unread_count??unread)+' new)':'';
 renderActivity();
}

async function loadPushTestStatus(){
 const data=await api('/api/push/test/status');
 const status=document.getElementById('push-test-status');
 if(!data.configured){status.textContent='Phone alerts are not yet configured on the server.';return;}
 if(!data.device_count){status.textContent='No devices registered. Enable notifications on this device first.';return;}
 if(!data.latest_test){status.textContent=data.device_count+' registered device(s). Send a test alert to check delivery.';return;}
 const t=data.latest_test;
 status.textContent=t.processed_at
  ? 'Latest test processed by server ('+t.attempts+' retries). Check your device for delivery.'
  : t.next_attempt_at
   ? 'Latest test is waiting for a retry. Attempts: '+t.attempts+'.'
   : 'Latest test queued for delivery.';
}
document.getElementById('test-board-push').addEventListener('click',async()=>{
 try{
  await api('/api/push/test',{method:'POST'});
  document.getElementById('push-status').textContent='Test alert queued. Check Activity now and your phone after the scheduler runs.';
  await loadActivity();
  await loadPushTestStatus();
 }catch(error){showError(error)}
});
let cachedOpportunities=[];
function renderOpportunities(){
 const type=document.getElementById('opportunity-filter').value;
 const query=document.getElementById('opportunity-search').value.trim().toLowerCase();
 const visible=cachedOpportunities.filter(item=>(!type||item.type===type)&&(!query||[item.title,item.details,item.member_name,item.company].some(v=>String(v||'').toLowerCase().includes(query))));
 const sort=document.getElementById('opportunity-sort').value;
 if(sort==='title')visible.sort((a,b)=>String(a.title||'').localeCompare(String(b.title||'')));
 else visible.sort((a,b)=>{const timestamp=item=>Date.parse(item.created_at||'')||0;return sort==='oldest'?timestamp(a)-timestamp(b):timestamp(b)-timestamp(a)});
 const root=document.getElementById('opportunities');root.replaceChildren();
 document.getElementById('opportunity-result-count').textContent=visible.length+' open post'+(visible.length===1?'':'s')+' found';
 for(const item of visible){
  const card=el('article','','message');
  card.append(el('strong',(item.type==='ask'?'REQUEST: ':'OFFER: ')+item.title));
  card.append(el('small',item.member_name+' · '+(item.company||'Local business owner')));
  card.append(el('p',item.details));
  if(item.member_id==={{ auth('board')->id() }}){
   const button=document.createElement('button');button.type='button';button.textContent='Close post';
   button.onclick=async()=>{button.disabled=true;try{await api('/api/opportunities/'+item.id+'/close',{method:'PATCH'});await loadOpportunities()}catch(error){button.disabled=false;showError(error)}};
   card.append(button);
  }else{
   const member=directoryMembers.find(m=>m.id===item.member_id);
   if(member){const button=document.createElement('button');button.type='button';button.textContent='Message member';button.onclick=()=>startMemberConversation(member);card.append(button);}
  }
  root.append(card);
 }
 if(!visible.length)root.textContent=query?'No posts match your search.':'No open posts in this category.';
}
document.getElementById('opportunity-filter').addEventListener('change',renderOpportunities);
document.getElementById('opportunity-search').addEventListener('input',renderOpportunities);
document.getElementById('opportunity-sort').addEventListener('change',renderOpportunities);
async function loadOpportunities(){
 const data=await api('/api/opportunities');cachedOpportunities=data.data;
 document.getElementById('stat-opportunities').textContent=data.data.length;
 renderOpportunities();
}
document.getElementById('new-opportunity').onsubmit=async event=>{
 event.preventDefault();const form=event.currentTarget;
 try{await api('/api/opportunities',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(Object.fromEntries(new FormData(form)))});form.reset();notice.textContent='Posted to NBTX Club.';await loadOpportunities()}catch(error){showError(error)}
};

let referralView='all';
document.querySelectorAll('[data-referral-view]').forEach(button=>button.addEventListener('click',()=>{
 referralView=button.dataset.referralView;
 document.querySelectorAll('[data-referral-view]').forEach(b=>b.setAttribute('aria-pressed',String(b===button)));
 renderReferrals();
}));
let cachedReferrals=[];
function renderReferrals(){
 const root=document.getElementById('referrals');root.replaceChildren();
 const status=document.getElementById('referral-status-filter').value;
 const search=document.getElementById('referral-search').value.trim().toLowerCase();
 const own={{ auth('board')->id() }};
 const visible=cachedReferrals.filter(r=>(referralView==='all'||(referralView==='received'?Number(r.to_member_id)===own:Number(r.from_member_id)===own))&&(!status||r.status===status)&&(!search||[r.subject,r.details,r.from_name,r.to_name].some(v=>String(v||'').toLowerCase().includes(search))));
 const sort=document.getElementById('referral-sort').value;
 if(sort==='subject')visible.sort((a,b)=>String(a.subject||'').localeCompare(String(b.subject||'')));
 else visible.sort((a,b)=>{const time=r=>Date.parse(r.created_at||'')||0;return sort==='oldest'?time(a)-time(b):time(b)-time(a)});
 const summary=document.getElementById('referral-summary');summary.replaceChildren();
 for(const [label,count] of [['Total',visible.length],['New',visible.filter(r=>r.status==='new').length],['Accepted',visible.filter(r=>r.status==='accepted').length],['Completed',visible.filter(r=>r.status==='completed').length],['Declined',visible.filter(r=>r.status==='declined').length]]){
  const stat=el('div','','referral-summary-item');stat.append(el('strong',String(count)),el('small',label));summary.append(stat);
 }
 for(const referral of visible){
  const card=el('article','','referral-card');
  const heading=el('div','','referral-heading');heading.append(el('strong',referral.subject),el('span',referral.status,'referral-status'));
  card.append(heading,el('small',referral.from_name+' → '+referral.to_name));
  if(referral.details)card.append(el('p',referral.details));
  const actions=el('div','','referral-actions');
  if(Number(referral.to_member_id)===own){
   const select=document.createElement('select');select.setAttribute('aria-label','Update referral status');
   for(const [value,label] of [['','Update status'],['accepted','Accepted'],['completed','Completed'],['declined','Declined']])select.append(new Option(label,value));
   select.value=referral.status==='new'?'':referral.status;
   select.onchange=async()=>{if(!select.value)return;try{await api('/api/referrals/'+referral.id,{method:'PATCH',headers:{'Content-Type':'application/json'},body:JSON.stringify({status:select.value})});await loadReferrals();notice.textContent='Referral status updated.'}catch(error){showError(error)}};
   actions.append(select);
  }
  const otherId=Number(referral.to_member_id)===own?Number(referral.from_member_id):Number(referral.to_member_id);
  const member=directoryMembers.find(m=>Number(m.id)===otherId);
  if(member){const followup=el('button','Message member','referral-followup');followup.type='button';followup.onclick=()=>startMemberConversation(member);actions.append(followup);}
  if(actions.childNodes.length)card.append(actions);
  root.append(card);
 }
 if(!visible.length)root.textContent='No referrals match these filters.';
}
document.getElementById('referral-status-filter').addEventListener('change',renderReferrals);
document.getElementById('referral-search').addEventListener('input',renderReferrals);
document.getElementById('referral-sort').addEventListener('change',renderReferrals);
async function loadReferrals(){
 const data=await api('/api/referrals');cachedReferrals=data.data;
 document.getElementById('stat-referrals').textContent=data.data.filter(r=>r.status==='new').length;
 renderReferrals();
}
document.getElementById('new-referral').onsubmit=async event=>{
 event.preventDefault();const form=event.currentTarget;const fields=Object.fromEntries(new FormData(form));
 try{await api('/api/referrals',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(fields)});form.reset();notice.textContent='Referral sent.';await loadReferrals()}catch(error){showError(error)}
};

async function openLinkedSection(){
 const params=new URLSearchParams(location.search);
 const conversation=Number(params.get('conversation'));
 if(Number.isSafeInteger(conversation)&&conversation>0){
  const data=await api('/api/conversations');
  const match=data.data.find(item=>item.id===conversation);
  if(match){
   selectedConversation=conversation;
   document.getElementById('conversation-title').textContent=match.name||'Direct message';
   document.getElementById('send').disabled=false;
   await loadMessages();
   document.getElementById('messages').scrollIntoView({behavior:'smooth',block:'center'});
  }
 }else if(params.get('section')==='referrals'){
  document.getElementById('referrals').scrollIntoView({behavior:'smooth',block:'start'});
 }else if(params.get('section')==='meetings'){
  document.getElementById('board-meetings').scrollIntoView({behavior:'smooth',block:'start'});
 }
}
document.querySelectorAll('.quick-actions-list a[href^="#"]').forEach(link=>link.addEventListener('click',()=>{const target=document.querySelector(link.getAttribute('href'));if(target){const panel=target.closest('details');if(panel)panel.open=true;}}));
Promise.all([loadDirectory(),loadConversations(),loadMeetings(),loadReferrals(),loadOpportunities(),loadPushPreferences(),loadPushTestStatus(),loadActivity()])
 .then(openLinkedSection).catch(showError);
document.addEventListener('visibilitychange',()=>{if(!document.hidden)Promise.all([loadMessages(),loadActivity(),loadConversations()]).catch(showError)});
setInterval(()=>{if(!document.hidden)Promise.all([loadActivity(),loadConversations(),...(selectedConversation?[loadMessages()]:[])]).catch(showError)},15000);
</script>
</body></html>
