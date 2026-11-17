<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}"><title>The Board | Administration</title>
<style>
body{background:#121820;color:#f5efe5;font:16px system-ui,sans-serif;max-width:850px;margin:30px auto;padding:18px}a{color:#e1c18a}section{background:#202b35;padding:20px;border-radius:10px;margin:18px 0}input,button{font:inherit;padding:10px;border-radius:5px}input{background:#111820;border:1px solid #64717b;color:white;max-width:100%;width:330px}button{background:#b99a61;border:0;cursor:pointer}button+button{margin-left:8px}.member{border-top:1px solid #59636c;padding:14px 0}small{color:#bfc9ce}#status{min-height:24px}
</style></head><body>
<a href="{{ route('board.home') }}">Back to The Board</a><h1>Board administration</h1>
<p>Invitations are private. New members must be approved before accessing the community.</p>
<p id="status" role="status" aria-live="polite"></p>
<section><h2>Invite a business owner</h2><form id="invite"><label>Email address <input name="email" type="email" required></label><button type="submit">Send invitation</button></form>
<div id="invite-result"></div></section>
<section><h2>Pending approvals</h2><div id="pending">Loading…</div></section>
<script>
const csrf=document.querySelector('meta[name="csrf-token"]').content;
const status=document.getElementById('status');
async function api(url,method='GET',data){
 const response=await fetch(url,{method,headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf},body:data?JSON.stringify(data):undefined});
 const result=await response.json();
 if(!response.ok)throw new Error(result.message||'Request failed');
 return result;
}
async function loadPending(){
 const data=await api('/api/admin/pending');const root=document.getElementById('pending');root.replaceChildren();
 if(!data.data.length){root.textContent='No pending members.';return}
 for(const member of data.data){
 const row=document.createElement('div');row.className='member';
 const heading=document.createElement('p');heading.textContent=member.name+' ('+member.email+')';row.append(heading);
 for(const [label,value] of [['Approve','active'],['Reject','rejected']]){
 const button=document.createElement('button');button.textContent=label;
 button.onclick=async()=>{if(!confirm(label+' '+member.name+'?'))return;try{await api('/api/admin/members/'+member.id,'PATCH',{status:value});status.textContent='Membership updated.';await loadPending()}catch(error){status.textContent=error.message}};
 row.append(button)
 }root.append(row)
 }
}
document.getElementById('invite').onsubmit=async event=>{
 event.preventDefault();const email=new FormData(event.currentTarget).get('email');
 try{
 const result=await api('/api/admin/invitations','POST',{email});
 const root=document.getElementById('invite-result');root.replaceChildren();
 const note=document.createElement('p');note.textContent=result.email_sent?'Invitation email sent.':'Email could not be sent; securely share this invitation link:';root.append(note);
 const input=document.createElement('input');input.readOnly=true;input.value=result.invitation_url;input.setAttribute('aria-label','Invitation link');root.append(input);
 status.textContent='Invitation created.';
 }catch(error){status.textContent=error.message}
};
loadPending().catch(error=>status.textContent=error.message);
</script></body></html>
