<?php
function getAdmin($chat){
$url = "https://api.telegram.org/bot".API_KEY."/getChatAdministrators?chat_id=@".$chat;
$result = file_get_contents($url);
$result = json_decode ($result);
return $result->ok;
}

function phone($cid) {
    if (user($cid)['phone'] !== null) {
        return true;
    } else {
        file_put_contents("step/$cid.step", 'request_contact');
        bot("sendMessage", [
            "chat_id" => $cid,
            'text' => "📲 <b>Botdan ro'yxatdan o'tish uchun quyidagi tugma orqali telefon raqamingizni yuboring</b>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'resize_keyboard' => true,
                'keyboard' => [
                    [['text' => "📱 Telefon raqamni yuborish", 'request_contact' => true]],
                ],
            ]),
        ]);
        return false;
    }
}




function enc($var,$exception) {
if($var=="encode"){
return base64_encode($exception);
}elseif($var=="decode"){
return base64_decode($exception);
}
}

function inline($a=[]){
$d=json_encode([
"inline_keyboard"=>$a
]);
return $d;
}

function keyboard($a=[]){
$d=json_encode([
'resize_keyboard'=>true,
'keyboard'=>$a
]);
return $d;
}





function number($a){
$form = number_format($a,00,' ',' ');
return $form;
}

function del(){
global $cid,$mid,$cid2,$mid2;
return bot('deleteMessage',[
'chat_id'=>$cid2.$cid,
'message_id'=>$mid2.$mid,
]);
}


function edit($id,$mid,$tx,$m=null){
return bot('editMessageText',[
'chat_id'=>$id,
'message_id'=>$mid,
'text'=>"<b>$tx</b>",
'parse_mode'=>"HTML",
'reply_markup'=>$m,
]);
}




function sms($id,$tx,$m=null){
return bot('sendMessage',[
'chat_id'=>$id,
'text'=>"<b>$tx</b>",
'parse_mode'=>"HTML",
'reply_markup'=>$m,
]);
}

function accl($d,$s,$j=false){
return bot('answerCallbackQuery',[
'callback_query_id'=>$d,
'text'=>$s,
'show_alert'=>$j
]);
}



function get($h){
return file_get_contents($h);
}

function put($h,$r){
file_put_contents($h,$r);
}#-----------



function user($cid){
global $connect;
$result = mysqli_query($connect, "SELECT * FROM users WHERE user_id = $cid");
$row = mysqli_fetch_assoc($result);
return $row;
}


function mysql($row,$put,$get){
global $connect;
$result = mysqli_query($connect, "SELECT * FROM $row WHERE $put = $get");
$row = mysqli_fetch_assoc($result);
return $row;
}


function name($id){
$nomi = bot('getChatMember',['chat_id' => $id,'user_id' => $id])->result->user->first_name;
$nomi = htmlspecialchars($nomi, ENT_QUOTES, 'UTF-8');
return $nomi;
}

function settings(){
global $connect;
$result = mysqli_query($connect, "SELECT * FROM settings WHERE id = '1'");
$row = mysqli_fetch_assoc($result);
return $row;
}