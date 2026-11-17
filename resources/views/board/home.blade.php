<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="manifest" href="/manifest.webmanifest">
<link rel="icon" href="/board-icon.svg" type="image/svg+xml">
<meta name="theme-color" content="#11171d">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="The Board">
<title>The Board | Member Home</title>
<style>
:root{color-scheme:dark}*{box-sizing:border-box}body{margin:0;background:#11171d;color:#f5f0e8;font:16px system-ui,sans-serif}header{padding:22px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #3c4650}main{max-width:1100px;margin:auto;padding:24px}h1{font-family:Georgia,serif;letter-spacing:.12em}h2{font-size:1.1rem}button,input,textarea,select{font:inherit;padding:10px;border-radius:6px}button{cursor:pointer;background:#b99a61;color:#141414;border:0}input,textarea,select{background:#242e36;color:white;border:1px solid #5a636c;width:100%}section{background:#1c252d;padding:18px;border:1px solid #36414b;border-radius:10px;min-width:0} .layout{display:grid;grid-template-columns:1fr 1fr;gap:18px}.muted{color:#c1c7ca}.message{border-bottom:1px solid #36414b;padding:10px 0;white-space:pre-wrap;overflow-wrap:anywhere}.item{display:block;width:100%;text-align:left;background:#2b3740;color:white;margin:8px 0}.item:focus{outline:2px solid #b99a61}.messages{height:270px;overflow:auto}label{display:block;margin:12px 0}small{color:#b9c3ca}#notice{min-height:24px;color:#e3c98c}@media(max-width:750px){.layout{grid-template-columns:1fr}main{padding:12px}header{padding:12px}}
</style>
</head>
<body>
<header><strong>THE BOARD</strong><form method="post" action="{{ route('board.logout') }}">@csrf<button type="submit">Sign out</button></form></header>
<main>
<h1>Welcome, {{ auth('board')->user()->name }}</h1>
<p class="muted">A private community of local business owners.</p>
@if(in_array(auth('board')->user()->role, ['admin', 'super_admin'], true))
<p><a href="{{ route('board.admin') }}" style="color:#e3c98c">Manage invitations and approvals</a></p>
@endif<p id="notice" role="status" aria-live="polite"></p>
<section style="margin-bottom:18px"><h2>Phone notifications</h2><p class="muted">Enable alerts on this device. On iPhone, add The Board to your Home Screen first, then open it there to enable notifications.</p>
<button type="button" id="enable-board-push">Enable notifications</button>
<button type="button" id="disable-board-push">Disable on this device</button>
<button type="button" id="test-board-push">Send test alert</button>
<form id="push-preferences"><label><input type="checkbox" name="messages" checked style="width:auto"> Chat messages</label>
<label><input type="checkbox" name="meetings" checked style="width:auto"> Meeting announcements and reminders</label>
<label><input type="checkbox" name="referrals" checked style="width:auto"> Business referrals</label>
<button type="submit">Save notification preferences</button></form>
<p id="push-status" class="muted" aria-live="polite"></p><p id="push-test-status" class="muted" aria-live="polite"></p></section>
<section style="margin-bottom:18px"><h2>Activity <span id="activity-count"></span></h2><div id="board-activity">Loading updates…</div></section>
<div class="layout">
<section><h2>Upcoming meetings</h2><div id="board-meetings">Loading meetings…</div>
@if(in_array(auth('board')->user()->role, ['admin', 'super_admin'], true))
<h2>Schedule a meeting</h2><form id="new-meeting">
<label>Title<input name="title" required maxlength="180"></label>
<label>Start<input type="datetime-local" name="starts_at" required></label>
<label>End<input type="datetime-local" name="ends_at" required></label>
<label>Location<input name="location" maxlength="255" placeholder="The Clubhouse"></label>
<label>Agenda<textarea name="description" maxlength="5000" rows="3"></textarea></label>
<button type="submit">Create meeting</button></form>
@endif</section>

<section><h2>Business referrals</h2><p class="muted">Share a business opportunity privately with another member. Avoid including confidential customer information without permission.</p>
<form id="new-referral"><label>Send to<select name="to_member_id" id="referral-recipient" required><option value="">Select a member</option></select></label>
<label>Opportunity<input name="subject" maxlength="180" required></label>
<label>Details<textarea name="details" maxlength="3000" rows="3"></textarea></label>
<button type="submit">Send referral</button></form><div id="referrals">Loading referrals…</div></section>
<section><h2>Member asks &amp; offers</h2><p class="muted">Ask fellow owners for advice, resources or introductions—or share something your business can offer.</p>
<form id="new-opportunity"><label>Post type<select name="type"><option value="ask">I need help with…</option><option value="offer">I can help with…</option></select></label>
<label>Headline<input name="title" maxlength="180" required></label><label>Details<textarea name="details" maxlength="3000" rows="3" required></textarea></label>
<button type="submit">Post to The Board</button></form>
<label>Show<select id="opportunity-filter"><option value="">All open posts</option><option value="ask">Requests</option><option value="offer">Offers</option></select></label>
<div id="opportunities">Loading opportunities…</div></section>
<section><h2>Member directory</h2><div id="directory">Loading members…</div></section>
<section><h2>My business profile</h2>
<form id="profile">
<label>Display name<input name="name" required value="{{ auth('board')->user()->name }}"></label>
<label>Company<input name="company" value="{{ auth('board')->user()->company }}"></label>
<label>Industry<input name="industry" value="{{ auth('board')->user()->industry }}"></label>
<label>Title<input name="title" value="{{ auth('board')->user()->title }}"></label>
<label>Website<input name="website" type="url" value="{{ auth('board')->user()->website }}"></label>
<label>About me<textarea name="bio" rows="3">{{ auth('board')->user()->bio }}</textarea></label>
<button type="submit">Save profile</button></form></section>
<section><h2>Conversations</h2><form id="new-conversation"><label>Conversation type<select name="type" id="conversation-type"><option value="direct">Direct message</option><option value="group">Group chat</option></select></label><label>Members<select name="member_ids" id="conversation-members" multiple size="5" required></select></label><label>Group name (for group chats)<input name="name" maxlength="120" placeholder="Business owners discussion"></label><button type="submit">Start conversation</button></form><div id="conversations">Loading conversations…</div></section>
<section><h2 id="conversation-title">Chat</h2><div class="messages" id="messages" aria-live="polite">Choose a conversation to view messages.</div>
<form id="chat"><label>Message<textarea name="body" rows="2" maxlength="5000" required></textarea></label><button type="submit" id="send" disabled>Send</button></form></section>
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
 if(!response.ok){throw new Error(response.status===403?'Access denied':response.status===419?'Session expired':'Request failed ('+response.status+')')}
 return response.json();
}
function el(tag,text,className){const node=document.createElement(tag);node.textContent=text;if(className)node.className=className;return node}
async function loadDirectory(){
 const data=await api('/api/members');const root=document.getElementById('directory');root.replaceChildren();document.getElementById('conversation-members').replaceChildren();const referralSelect=document.getElementById('referral-recipient');referralSelect.replaceChildren(new Option('Select a member',''));
 for(const member of data.data){const row=el('div',member.name+' — '+(member.company||'Business owner')+' · '+(member.industry||''),'message');root.append(row);if(member.id!=={{ auth('board')->id() }}){const option=document.createElement('option');option.value=member.id;option.textContent=member.name+' — '+(member.company||'');document.getElementById('conversation-members').append(option);referralSelect.append(new Option(member.name+' — '+(member.company||''),member.id))}}
 if(!data.data.length)root.textContent='No approved members yet.';
}
async function loadConversations(){
 const data=await api('/api/conversations');const root=document.getElementById('conversations');root.replaceChildren();
 for(const conversation of data.data){
 const button=el('button',(conversation.unread_count ? '● '+conversation.unread_count+' new · ' : '')+(conversation.name||('Conversation #'+conversation.id)),'item');
 button.onclick=()=>{selectedConversation=conversation.id;document.getElementById('conversation-title').textContent=conversation.name||'Direct message';document.getElementById('send').disabled=false;loadMessages().catch(showError)};
 root.append(button);
 }
 if(!data.data.length)root.textContent='No conversations yet.';
}
async function loadMessages(){
 if(!selectedConversation)return;
 const id=selectedConversation;const data=await api('/api/conversations/'+id+'/messages');if(selectedConversation!==id)return;
 const root=document.getElementById('messages');root.replaceChildren();
 for(const message of data.data.reverse()){root.append(el('div','Member #'+message.sender_id+': '+message.body,'message'))}
 if(!data.data.length)root.textContent='Start the conversation.';
 await loadConversations();
}
function showError(error){notice.textContent=error.message}
document.getElementById('new-conversation').onsubmit=async event=>{
 event.preventDefault();const form=event.currentTarget;const type=form.elements.type.value;
 const member_ids=Array.from(document.getElementById('conversation-members').selectedOptions).map(option=>Number(option.value));
 try{const created=await api('/api/conversations',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({type,name:form.elements.name.value,member_ids})});
 form.reset();await loadConversations();selectedConversation=created.id;document.getElementById('send').disabled=false;await loadMessages();notice.textContent='Conversation created.'}catch(error){showError(error)}
};
document.getElementById('profile').onsubmit=async event=>{
 event.preventDefault();const form=new FormData(event.currentTarget);
 try{await api('/api/profile',{method:'PATCH',headers:{'Content-Type':'application/json'},body:JSON.stringify(Object.fromEntries(form))});notice.textContent='Profile saved.';await loadDirectory()}catch(error){showError(error)}
};
document.getElementById('chat').onsubmit=async event=>{
 event.preventDefault();if(!selectedConversation)return;
 const body=new FormData(event.currentTarget).get('body');
 try{await api('/api/conversations/'+selectedConversation+'/messages',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({body})});event.currentTarget.reset();await loadMessages()}catch(error){showError(error)}
};
async function loadMeetings(){
 const data=await api('/api/meetings');const root=document.getElementById('board-meetings');root.replaceChildren();
 for(const meeting of data.data){
  const card=document.createElement('div');card.className='message';
  card.append(el('strong',meeting.title),el('div',new Date(meeting.starts_at).toLocaleString()+' – '+new Date(meeting.ends_at).toLocaleTimeString()),el('div',meeting.location||'Location to be announced'));
  if(meeting.description)card.append(el('p',meeting.description));
  card.append(el('small',meeting.attending_count+' attending'));
  const select=document.createElement('select');
  for(const [value,label] of [['','Your RSVP'],['going','Going'],['maybe','Maybe'],['declined','Cannot attend']]){
   const option=document.createElement('option');option.value=value;option.textContent=label;select.append(option);
  }
  select.value=meeting.my_rsvp||'';
  select.onchange=async()=>{if(!select.value)return;try{await api('/api/meetings/'+meeting.id+'/rsvp',{method:'PUT',headers:{'Content-Type':'application/json'},body:JSON.stringify({response:select.value})});notice.textContent='RSVP saved.';await loadMeetings()}catch(error){showError(error)}};
  card.append(select);root.append(card);
 }
 if(!data.data.length)root.textContent='No upcoming meetings yet.';
}
const meetingForm=document.getElementById('new-meeting');
if(meetingForm)meetingForm.onsubmit=async event=>{
 event.preventDefault();const form=event.currentTarget;const fields=Object.fromEntries(new FormData(form));
 try{await api('/api/meetings',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({...fields,starts_at:new Date(fields.starts_at).toISOString(),ends_at:new Date(fields.ends_at).toISOString()})});form.reset();notice.textContent='Meeting created.';await loadMeetings()}catch(error){showError(error)}
};

async function loadActivity(){
 const data=await api('/api/notifications');
 const root=document.getElementById('board-activity');root.replaceChildren();
 document.getElementById('activity-count').textContent=data.unread_count?'('+data.unread_count+' new)':'';
 for(const item of data.items){
  const card=el('div','','message');
  card.append(el('strong',item.title+(item.read_at?'':' · New')));
  if(item.body)card.append(el('p',item.body));
  const link=document.createElement('a');link.href=item.url;link.textContent='View';link.style.color='#e3c98c';card.append(link);
  if(!item.read_at){
   const button=document.createElement('button');button.type='button';button.textContent='Mark read';button.style.marginLeft='12px';
   button.onclick=async()=>{try{await api('/api/notifications/'+item.id+'/read',{method:'PATCH'});await loadActivity()}catch(error){showError(error)}};
   card.append(button);
  }
  root.append(card);
 }
 if(!data.items.length)root.textContent='No activity yet.';
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
async function loadOpportunities(){
 const type=document.getElementById('opportunity-filter').value;
 const data=await api('/api/opportunities'+(type?'?type='+encodeURIComponent(type):''));
 const root=document.getElementById('opportunities');root.replaceChildren();
 for(const item of data.data){
  const card=el('div','','message');
  card.append(el('strong',(item.type==='ask'?'REQUEST: ':'OFFER: ')+item.title));
  card.append(el('small',item.member_name+' · '+(item.company||'Local business owner')));
  card.append(el('p',item.details));
  if(item.member_id==={{ auth('board')->id() }}){
   const button=document.createElement('button');button.type='button';button.textContent='Close post';
   button.onclick=async()=>{try{await api('/api/opportunities/'+item.id+'/close',{method:'PATCH'});await loadOpportunities()}catch(error){showError(error)}};
   card.append(button);
  }
  root.append(card);
 }
 if(!data.data.length)root.textContent='No open posts yet.';
}
document.getElementById('opportunity-filter').onchange=()=>loadOpportunities().catch(showError);
document.getElementById('new-opportunity').onsubmit=async event=>{
 event.preventDefault();const form=event.currentTarget;
 try{await api('/api/opportunities',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(Object.fromEntries(new FormData(form)))});form.reset();notice.textContent='Posted to The Board.';await loadOpportunities()}catch(error){showError(error)}
};

async function loadReferrals(){
 const data=await api('/api/referrals');const root=document.getElementById('referrals');root.replaceChildren();
 for(const referral of data.data){
  const card=el('div',referral.subject+' · '+referral.status+' · '+referral.from_name+' → '+referral.to_name,'message');
  if(referral.details)card.append(el('p',referral.details));
  if(referral.to_member_id==={{ auth('board')->id() }}){
   const select=document.createElement('select');
   for(const [value,label] of [['','Update status'],['accepted','Accepted'],['completed','Completed'],['declined','Declined']])select.append(new Option(label,value));
   select.value=referral.status==='new'?'':referral.status;
   select.onchange=async()=>{if(!select.value)return;try{await api('/api/referrals/'+referral.id,{method:'PATCH',headers:{'Content-Type':'application/json'},body:JSON.stringify({status:select.value})});await loadReferrals()}catch(error){showError(error)}};
   card.append(select);
  }
  root.append(card);
 }
 if(!data.data.length)root.textContent='No referrals yet.';
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
Promise.all([loadDirectory(),loadConversations(),loadMeetings(),loadReferrals(),loadOpportunities(),loadPushPreferences(),loadPushTestStatus(),loadActivity()])
 .then(openLinkedSection).catch(showError);
document.addEventListener('visibilitychange',()=>{if(!document.hidden)Promise.all([loadMessages(),loadActivity(),loadConversations()]).catch(showError)});
setInterval(()=>{if(!document.hidden)Promise.all([loadActivity(),loadConversations(),...(selectedConversation?[loadMessages()]:[])]).catch(showError)},15000);
</script>
</body></html>
