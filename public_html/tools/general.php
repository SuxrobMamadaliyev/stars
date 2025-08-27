<?php
include 'tools/config.php';
include 'tools/function.php';



function mysqli_query_new($connect, $sql){
$tmp_Data = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
return mysqli_query($connect, "/*L: ".$tmp_Data[0]['line']."*/ ".$sql);
}


function joinchat($id){
    global $connect;
    $result = $connect->query("SELECT * FROM `channels`");
    if ($result->num_rows > 0 and $id != "6155982488") {
        $no_subs = 0;
        $button = [];
        while ($row = $result->fetch_assoc()) {
            $type = $row['type'];
            $link = $row['link'];
            $channelID = $row['channelID'];
            $title = $row['title'];
            $gettitle = bot('getchat', ['chat_id' => $channelID])->result->title;
            if ($type == "lock" or $type == "request") {
                if ($type == "request") {
                    $check = $connect->query("SELECT * FROM `requests` WHERE id = '$id' AND chat_id = '$channelID'");
                    if ($check->num_rows > 0) {
                        $button[] = ['text' => "✅ $gettitle", 'url' => $link];
                    } else {
                        $button[] = ['text' => "❌ $gettitle", 'url' => $link];
                        $no_subs++;
                    }
                } elseif ($type == "lock") {
                    $check = bot('getChatMember', ['chat_id' => $channelID, 'user_id' => $id])->result->status;
                    if ($check == "left") {
                        $button[] = ['text' => "❌ $gettitle", 'url' => $link];
                        $no_subs++;
                    } else {
                        $button[] = ['text' => "✅ $gettitle", 'url' => $link];
                    }
                }
            } elseif ($type == "social") {
                $button[] = ['text' => base64_decode($title), 'url' => $link];
            }
        }
        if ($no_subs > 0) {
            $button[] = ['text' => "✅ Tekshirish", 'callback_data' => "result"];
            $keyboard2 = array_chunk($button, 1);
            $keyboard = json_encode([
                'inline_keyboard' => $keyboard2,
            ]);
            bot('sendMessage', [
                'chat_id' => $id,
                'text' => "<b>❌ Kechirasiz botimizdan foydalanishdan oldin ushbu kanallarga a'zo bo'lishingiz kerak.</b>",
                'parse_mode' => 'html',
                'reply_markup' => $keyboard
            ]);
            exit();
        } else return true;
    } else return true;
}

$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$tx = $message->text;
$cid = $message->chat->id;
$mid = $message->message_id;
$text = $message->text;
$chat_id = $message->chat->id;
$message_id = $message->message_id;
$from_id = $message->from->id;
$name = $message->from->first_name;
$last = $message->from->last_name;
$username = $message->from->username;
$bio = $message->from->about;
$contact = $message->contact;
$contact_id = $contact->user_id;
$contact_user = $contact->username;
$contact_name = $contact->first_name;
$phone = $contact->phone_number;
$data = $update->callback_query->data;
$qid = $update->callback_query->id;
$cid2 = $update->callback_query->message->chat->id;
$mid2 = $update->callback_query->message->message_id;
$callback = $update->callback_query;
$callfrid = $update->callback_query->from->id;
$callname = $update->callback_query->from->first_name;
$calluser = $update->callback_query->from->username;
$surname = $update->callback_query->from->last_name;
$about = $update->callback_query->from->about;
$step = file_get_contents("step/$cid.step");
$kanal = file_get_contents("admin/kanal.txt");
mkdir("step");
mkdir("admin");
$botdel = $update->my_chat_member->new_chat_member;
$botdel_id = $update->my_chat_member->from->id;
$userstatus = $botdel->status;
$phone = $message->contact->phone_number;
$uid= $update->callback_query->from->id;
$chat_join_request = $update->chat_join_request;
$join_chat_id = $chat_join_request->chat->id;
$join_user_id = $chat_join_request->from->id;

if (isset($chat_join_request)) {
    $connect->query("INSERT INTO requests (id, chat_id) VALUES ('$join_user_id', '$join_chat_id')");
}

if ($botdel) {
if ($userstatus == "kicked") {
mysqli_query_new($connect, "UPDATE users SET action = 'kicked' WHERE user_id = '$botdel_id'");
}elseif($userstatus == "member"){
mysqli_query_new($connect, "UPDATE users SET action = 'member' WHERE user_id = '$botdel_id'");
}
}

$panel = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"⚙️ Sozlama"],['text'=>"📢 Kanallar"]],
[['text'=>"📊 Hisobot"],['text'=>"🔍 User ko'rish"]],
[['text'=>"🤖 Bot holati"]],
[['text'=>"📤 Yechish usullari"],['text'=>"📧 Xabar yuborish"]],
[['text'=>"🔙 Orqaga"]],
]]);

if($cid == $admin || $cid2 == $admin){
$menu = json_encode([
'resize_keyboard' => true,
'keyboard' => [
[['text' => "🎰 Slot aylantirish"]],
[['text' => "🔢 Auksion"],['text' => "💼 Stars ishlash"]],
[['text' => "📥 Stars kiritish"],['text' => "📤 Stars yechish"]],
[['text' => "💰 Hisobim"],['text' => "🎁 Kunlik bonus"]],  
[['text' => "⚙️ Sozlamalar paneli"]]
]
]);
} else {
$menu = json_encode([
'resize_keyboard' => true,
'keyboard' => [
[['text' => "🎰 Slot aylantirish"]],
[['text' => "🔢 Auksion"],['text' => "💼 Stars ishlash"]],
[['text' => "📥 Stars kiritish"],['text' => "📤 Stars yechish"]],
[['text' => "💰 Hisobim"],['text' => "🎁 Kunlik bonus"]],  
]
]);
}

$back = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 Orqaga"]],
]]);

$boshqarish = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🗄 Boshqarish"]],
]]);

if($text){
    $ban = mysqli_fetch_assoc(mysqli_query_new($connect,"SELECT * FROM users WHERE user_id=$cid"))['status'];
if(!$ban == "active"){
if($cid == $admin){
}else{
}}else{}}

if($text){
$ban = mysqli_fetch_assoc(mysqli_query_new($connect,"SELECT * FROM users WHERE user_id=$cid2"))['status'];
if(!$ban == "active"){
if($cid2 == $admin){
}else{
}}else{}}


if(settings()['bot_status']=="off"){
if($data){
if($cid2==$admin){
}else{
accl($qid,"⛔️ Bot vaqtinchalik o'chirilgan!",true);
exit();
}
}elseif($text){
if($cid==$admin){
}else{
sms($cid,"⛔️ Bot vaqtinchalik o'chirilgan!",null);
exit();
}
}
}

if ($step == "request_contact") {
    $phone = trim($phone);
    $phone = str_replace(" ", "", $phone);
    $phone = str_replace("+", "", $phone);
    if (mb_stripos($phone, "998") === 0) {
        mysqli_query_new($connect, "UPDATE users SET phone='$phone' WHERE user_id = $cid");
        sms($cid, "<b>✅ Qabul qilindi! Botdan foydalanishingiz mumkin.</b>", $menu);
        unlink("step/$cid.step");
if(joinchat($cid)==true){
}
    } else {
        sms($cid, "⛔️ Botdan faqat O'zbekiston raqamlari foydalana oladi!");
        unlink("step/$cid.step");
        exit();
    }
}


if ($data == "result") {
$ref1 = file_get_contents("step/$cid2.txt");
$ref2 = file_get_contents("step/$cid2.id");
    del();
    if (joinchat($cid2)) {
        if ($ref1) {
$setting = mysqli_fetch_assoc(mysqli_query_new($connect, "SELECT * FROM settings WHERE id = '1'"));
$referal_price = $setting['referal_price'];
            mysqli_query_new($connect, "UPDATE users SET main_balance = main_balance + $referal_price WHERE user_id = '$ref1'");
 mysqli_query_new($connect,"INSERT INTO referals(`ref_id`,`user_id`) VALUES ('$ref1','$cid2');");
            sms($ref2, "✅ Hisobingizga $referal_price ⭐️ qo'shildi.");
        }
        sms($cid2, "✅ <b>Obunangiz tasdiqlandi. Bosh menyudasiz.</b>", $menu);
        unlink("step/$cid2.txt");
        unlink("step/$cid2.id");
        exit(); 
    }
}



if($text == "🎰 Slot aylantirish" and joinchat($cid)==true){
if (user($cid)['main_balance'] < 2) {
sms($cid, "📛 Asosiy balansingizda stars yetarli emas!

🎰 1 ta aylantirish 2 ⭐️
🎉 Yutuq 60 ⭐️", $menu);
unlink("step/$cid.step");
exit();
}
mysqli_query($connect, "UPDATE users SET main_balance = main_balance - 2 WHERE user_id = '$cid'");
$dice = bot('sendDice', [
'chat_id' => $cid,
'emoji' => '🎰'
]);
$result = $dice->result->dice->value;
if ($result == 64) {
$win = 60;
mysqli_query($connect, "UPDATE users SET balance = balance + $win WHERE user_id = '$cid'");
sms($cid, "🎉 Tabriklaymiz! Sizga $win ⭐️ yulduz berildi.");
} else {
sms($cid, "😢 Afsus, yutuq chiqmadi. Omadingizni yana sinab ko‘ring!");
}}



if (mb_stripos($text, "/start R") !== false) {
    $code = explode("/start R", $text)[1];
    $refid = mysqli_fetch_assoc(mysqli_query_new($connect, "SELECT * FROM users WHERE id = '$code'"))['user_id'];
    if ($refid == $cid) {
        bot('SendMessage', [
            'chat_id' => $cid,
            'text' => "<b>🖐 Salom ".name($cid)."</b>",
            'parse_mode' => 'html',
            'reply_markup' => $menu,
        ]);
        exit();
    } else {
$result = mysqli_query_new($connect,"SELECT * FROM users WHERE user_id = $cid");
$row = mysqli_fetch_assoc($result);
        if ($row) {
            bot('SendMessage', [
                'chat_id' => $cid,
                'text' => "<b>🖐 Salom ".name($cid)."</b>",
                'parse_mode' => 'html',
                'reply_markup' => $menu
            ]);
            exit();
        } else {
file_put_contents("step/$cid.id", $refid);
file_put_contents("step/$cid.txt", $refid);
mysqli_query_new($connect, "UPDATE users SET referal = referal + 1 WHERE user_id = '$refid'"); 
$text = "🎉 Sizda Yangi Referal Mavjud!";
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
            bot('SendMessage', [
                'chat_id' => $refid,
                'text' => $text,
                'parse_mode' => 'html',
            ]);
            $result = mysqli_query_new($connect,"SELECT * FROM users WHERE user_id = $cid");
$row = mysqli_fetch_assoc($result);
if(!$row){
mysqli_query_new($connect,"INSERT INTO users(`user_id`,`date`) VALUES ('$cid','$sana')");
}
exit();
}}}

if(isset($message)){
$result = mysqli_query_new($connect,"SELECT * FROM users WHERE user_id = $cid");
$row = mysqli_fetch_assoc($result);
if(!$row){
mysqli_query_new($connect,"INSERT INTO users(`user_id`,`date`) VALUES ('$cid','$sana')");
}
}


if (($text == "/start" || $text == "🔙 Orqaga") && phone($cid) == true && joinchat($cid) == true) {
sms($cid,"🖐 Assalomu Aleykum ".name($cid),$menu);
unlink("step/$cid.step");
}


if(($text=="⚙️ Sozlamalar paneli" or $text == "🗄 Boshqarish") and $cid == $admin){
sms($cid,"🖐 Assalomu Aleykum ".name($cid),$panel);
unlink("step/$cid.step");
}






if($text == "🔢 Auksion" && joinchat($cid) == true && phone($cid) == true){
sms($cid,"👨‍⚖️ Auktsion qoidalari:
⚜️ Auksionni 1⭐️dan boshlashingiz mumkin.
⚜️ Auktsion 2 ta garovga yetganda tugashi mumkin.
⚜️ Har qanday ishtirokchi oldingi garovni oshirishi va Liderga aylanishi mumkin.
⚜️ Maksimal o'sish bosqichi-10 ⭐️.
⚜️ Garov ko'tarilgandan so'ng, auksion 10 daqiqaga uzaytiriladi.
⚜️ Taymer nolga yetgandan so'ng, pul oxirgi pul tikgan kishiga o'tkaziladi.
⚜️ Foydalanuvchi ketma-ket pul tika olmaydi.
⚜️ Auksion tugaganda g'olib bankni yechish balansiga oladi.

👨‍⚖ Eng kuchlilar g'alaba qozonadi!",inline([
[['text'=>"🧑‍⚖️ Auksionni boshlash",'callback_data'=>"start_auksion"]],   
[['text'=>"👀 Auksionni kuzatish",'url'=>"https://t.me/" . ltrim(settings()['proof'], '@')]],
]));
}



if($data == "start_auksion"){
if(settings()['auksion_status'] == "on"){
accl($qid,"⚠️ Auksion boshlangan!
👀 Kanalda auksionni kuzating...",true);
exit();
}else{
del();
sms($cid2,"👉 Auksionni boshlash uchun boshlang'ich garovni kiriting:",$back);
put("step/$cid2.step","start_auksion");
}
}


if($step == "start_auksion" and is_numeric($text)){
if ($text < 1) {
sms($cid, "❗️ Minimal kirish miqdori 1 ⭐️!", $menu);
unlink("step/$cid.step");
exit();
}
if (user($cid)['main_balance'] < $text and is_numeric($text)) {
sms($cid, "⛔️ Hisobingizda yetarli mablag' mavjud emas!", $menu);
unlink("step/$cid.step");
exit();
}
mysqli_query_new($connect,"UPDATE users SET main_balance = main_balance - $text WHERE user_id = '$cid'");
$tex=$text+1;
$txt=$text+10;
for($o=$tex;$o<=$txt;$o++){
$keyboards[]=["text"=>"$o ⭐️","callback_data"=>"high_auksion|$o"];
$key=array_chunk($keyboards, 5);
$key[]=[['callback_data'=>"my_balance",'text'=>"💳 Mening balansim"]];
$key[]=[['url'=>"https://t.me/$bot",'text'=>"◀️ Botga Kirish"]];
$stavki=json_encode(['inline_keyboard'=>$key]);
}
sms(settings()['proof'],"✅ <a href='tg://user?id=$cid'>".name($cid)."</a> auksionni $text ⭐️ bilan boshladi!",null);
$msg=sms(settings()['proof'],"👨🏻‍⚖️ Auksion

⚜️ Holati: Boshlangan!
⏱ Qolgan vaqt: 10:00
💰 Auksion banki: $text ⭐️
🔨 Garovlar soni: 1 ta 

👑 Lider: <a href='tg://user?id=$cid'>".name($cid)."</a> Tikdi $text ⭐️!

👇 Garovni oshirish uchun miqdorini tanlang:",$stavki)->result->message_id;
sms($cid,"✅ Siz Auksionni $text ⭐️ bilan boshlab berdingiz!",$menu);
$time = date("H:i", strtotime("+1 minutes"));
$over_time = date("H:i", strtotime("+10 minutes"));
mysqli_query_new($connect,"INSERT INTO auction(`post_id`,`last_id`,`stake`,`bank`,`last_stake`,`auction_time`,`time_1`,`time_2`,`over_time`) VALUES ('$msg','$cid','1','$text','$text','$time','12','10','$over_time')");
mysqli_query_new($connect,"UPDATE settings SET auksion_status = 'on'");
unlink("step/$cid.step");
}



if($_GET['update']=="auction"){
$rew = mysqli_fetch_assoc(mysqli_query_new($connect,"SELECT * FROM auction"));
$stake=$rew['stake'];
$atime=$rew['auction_time'];
$hoo=$rew['time_2'];
if($soat==$atime and $hoo!="0"){
if(!$hoo){
$hoo=10;
}
$t=$hoo-1;
mysqli_query_new($connect,"UPDATE auction SET time_2 = '$t'");
$last_stake=$rew['last_stake'];
$last=$rew['last_id'];
$bank=$rew['bank'];
$tex=$last_stake+1;
$txt=$last_stake+10;
for($o=$tex;$o<=$txt;$o++){
$keyboards[]=["text"=>"$o ⭐️","callback_data"=>"high_auksion|$o"];
$key=array_chunk($keyboards, 5);
$key[]=[['callback_data'=>"my_balance",'text'=>"💳 Mening balansim"]];
$key[]=[['url'=>"https://t.me/$bot",'text'=>"◀️ Botga Kirish"]];
$stavki=json_encode(['inline_keyboard'=>$key]);
}
bot('editMessageText',[
'chat_id'=>settings()['proof'],
'message_id'=>$rew['post_id'],
'text'=>"👨🏻‍⚖️ Auksion

⚜️ Holati: Boshlangan
⏱ Qolgan vaqt: $t:00
💰 Auksion banki: $bank ⭐️
🔨 Garovlar soni: $stake ta

👑 Lider: <a href='tg://user?id=$last'>".name($last)."</a> Tikdi $last_stake ⭐️!

👇 Garovni oshirish uchun miqdorini tanlang:",
'parse_mode'=>'HTML',
'reply_markup'=>$stavki
]);
$time=date("H:i",strtotime("+1 minutes"));
mysqli_query_new($connect,"UPDATE auction SET auction_time = '$time'");
}}






if (mb_stripos($data, "high_auksion|") !== false) {
    $stavka = explode("|", $data)[1];
    $row = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM auction"));
    $last = $row['last_id'];

    if ($last == $uid) {
        accl($qid, "❗Siz ketma-ket 2 ta garov tikolmaysiz!", true);
        exit();
    }

    $balance = user($uid)['main_balance'];
    if ($balance >= $stavka) {
        $bank = $row['bank'] + $stavka;
        $stake = $row['stake'] + 1;
        $time = date("H:i", strtotime("+1 minutes"));
        $over_time = date("H:i", strtotime("+10 minutes"));

        mysqli_query_new($connect, "UPDATE auction SET auction_time='$time', over_time='$over_time', last_id='$uid', time_2='10', stake=stake+1, last_stake='$stavka', bank='$bank'");
        mysqli_query($connect, "UPDATE users SET main_balance = main_balance - $stavka WHERE user_id = '$uid'");

        $buttons = [];
        for ($i = $stavka + 1; $i <= $stavka + 10; $i++) {
            $buttons[] = ["text" => "$i ⭐️", "callback_data" => "high_auksion|$i"];
        }
        $keyboard = array_chunk($buttons, 5);
        $keyboard[] = [["text" => "💳 Mening balansim", "callback_data" => "my_balance"]];
        $keyboard[] = [["text" => "◀️ Botga Kirish", "url" => "https://t.me/$bot"]];
        $markup = json_encode(["inline_keyboard" => $keyboard]);

bot('editMessageText',[
'chat_id'=>settings()['proof'],
'message_id'=>$row['post_id'],
'text'=>"👨🏻‍⚖️ Auksion

⚜️ Holati: Boshlangan
⏱ Qolgan vaqt: 10:00
💰 Auksion banki: $bank ⭐️
🔨 Garovlar soni: $stake

👑 Lider: <a href='tg://user?id=$uid'>" . name($uid) . "</a> Tikdi $stavka ⭐️!

👇 Garovni oshirish uchun miqdorini tanlang:",
'parse_mode'=>'HTML',
'reply_markup'=>$markup
]);
sms(settings()['proof'], "➕ <a href='tg://user?id=$uid'>" . name($uid) . "</a> Garovni $stavka ⭐️ga oshirdi!");
} else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "❗O'yin balansida mablag' yetarli emas!",
            'show_alert' => false,
        ]);
    }
}



$rew = mysqli_fetch_assoc(mysqli_query_new($connect,"SELECT * FROM auction"));
$end = $rew['over_time'];
$ho = $rew['time_2'];

// Vaqt tugaganini tekshiramiz
if($soat == $end || $ho == "0"){

    // Auksion statusini tekshiramiz
    $settings = settings();
    if($settings['auksion_status'] == "on"){

        // Darhol "processing" deb qo'yamiz, boshqa so'rovlar kutib turadi
        mysqli_query_new($connect, "UPDATE settings SET auksion_status = 'processing'");

        // Keyin qolgan ishlarni bajarish xavfsiz
        $last = $rew['last_id'];
        $bankk = $rew['bank'];
        $laststavka = $rew['last_stake'];
        $winn = $bankk / 100 * 85;

        mysqli_query($connect,"UPDATE users SET balance = balance + $winn WHERE user_id='$last'");

        sms(settings()['proof'],"🧑‍⚖️Auksion Tugadi!

👑Lider: <a href='tg://user?id=$last'>".name($last)."</a> tikdi <b>$laststavka</b> ⭐️!
💰Auksion Banki: <b>$bankk</b> ⭐️!
💳G'olib auksion bankining 85%ni oldi - <b>$winn</b> ⭐️",null);

        sms($last,"📢Hurmatli <a href='tg://user?id=$last'>".name($last)."</a> siz <b>🧑‍⚖️Auksionda</b> g'olib bo'ldingiz!
💰Auksion Banki: <b>$bankk</b> ⭐️!
💳Siz auksion bankining 85%ni oldingiz - <b>$winn</b>⭐️",$menu);

        bot('deleteMessage', [
            'chat_id' => settings()['proof'],
            'message_id' => $rew['post_id']
        ]);

        mysqli_query($connect,"DELETE FROM auction");

        // Endi auksionni "off" ga o'zgartiramiz
        mysqli_query_new($connect, "UPDATE settings SET auksion_status = 'off'");

        exit();
    }
}



if($data=="my_balance"){
bot('answerCallbackQuery',[
'callback_query_id'=>$qid,
'text'=>"💳 Sizning balansingiz: ".user($uid)['main_balance']." ⭐️",
'show_alert'=>true,
]);
}


if($text == "🎁 Kunlik bonus" && joinchat($cid) == true && phone($cid) == true){
if(user($cid)['bonus']==$sana){
sms($cid,"⛔️ Siz bugun bonusingizni oldingiz!",null);
}else{
sms($cid,"🎁 1 ⭐️ kunlik bonus oldingiz!",null);
mysqli_query($connect,"UPDATE users SET main_balance = main_balance + 1 , bonus = '$sana' WHERE user_id = '$cid'");
}
}


if($text == "💰 Hisobim" && joinchat($cid) == true && phone($cid) == true){
$rew = mysqli_fetch_assoc(mysqli_query_new($connect,"SELECT * FROM users WHERE user_id = $cid"));
$main_balance = $rew['main_balance'] ?? 0;
$balance = $rew['balance'] ?? 0;
$referal = $rew['referal'] ?? 0;
sms($cid,"┌🏛 Sizning hisobingiz haqida:
│
├👤 User: ".name($cid)."
├📑 ID raqam: <code>$cid</code>
├🪪 Hisob raqam: <code>{$rew['id']}</code>
├🔗 Do'stlaringiz: $referal ta
├💰 Asosiy Balans: ".round($main_balance, 2)." ⭐️
└📥 Yechish Balans: ".round($balance, 2)." ⭐️

⏰ $sana | $soat",json_encode([
'inline_keyboard'=>[
[['text'=>"📥 Yechish Balans ➡️ 💰 Asosiy Balans",'callback_data'=>"exchange"]],
[['text'=>"📥 Stars kiritish",'callback_data'=>"addfunds"],['text'=>"📤 Stars yechish",'callback_data'=>"solving"]],
[['text'=>"📜 Ma'lumotlar",'callback_data'=>"user_info"]],
]]));
}


if($data == "my_balance22"){
$rew = mysqli_fetch_assoc(mysqli_query_new($connect,"SELECT * FROM users WHERE user_id = $cid2"));
$main_balance = $rew['main_balance'] ?? 0;
$balance = $rew['balance'] ?? 0;
$referal = $rew['referal'] ?? 0;
edit($cid2,$mid2,"┌🏛 Sizning hisobingiz haqida:
│
├👤 User: ".name($cid2)."
├📑 ID raqam: <code>$cid2</code>
├🪪 Hisob raqam: <code>{$rew['id']}</code>
├🔗 Do'stlaringiz: $referal ta
├💰 Asosiy Balans: ".round($main_balance, 2)." ⭐️
└📥 Yechish Balans: ".round($balance, 2)." ⭐️

⏰ $sana | $soat",json_encode([
'inline_keyboard'=>[
[['text'=>"📥 Yechish Balans ➡️ 💰 Asosiy Balans",'callback_data'=>"exchange"]],
[['text'=>"📥 Stars kiritish",'callback_data'=>"addfunds"],['text'=>"📤 Stars yechish",'callback_data'=>"solving"]],
[['text'=>"📜 Ma'lumotlar",'callback_data'=>"user_info"]],
]]));
}

if($data == "exchange"){
del();
sms($cid2,"⭐️ Nechta starsni asosiy balansga o'tqazmoqchisiz?",$back);
put("step/$cid2.step","exchange");
}


if($step == "exchange" and is_numeric($text)){
if (user($cid)['balance'] < $text) {
sms($cid, "📛 Yechish balansingizda stars yetarli emas!", $menu);
unlink("step/$cid.step");
exit();
}
mysqli_query($connect, "UPDATE users SET main_balance = main_balance + $text, balance = balance - $text WHERE user_id = '$cid'");
sms($cid," Asosiy balansga $text ⭐️ o'tqazildi!",$menu);
}



if($text == "📥 Stars kiritish" && joinchat($cid) == true && phone($cid) == true){
$a = mysqli_query_new($connect,"SELECT * FROM `card`");
$k = [];
while($s = mysqli_fetch_assoc($a)){
$k[] = ['text' => $s['name'], 'callback_data' => "pay=" . $s['id']];
}
$keyboard2 = array_chunk($k, 2);
$keyboard2[] = [['text' => "⭐️ Stars orqali to'lov", 'callback_data' => "stars"]];
$keyboard2[] = [['text' => "🔵 Click [ avto ]", 'callback_data' => "transAPI^🔵 Click [ avto ]"],['text' => "⚪️ Payme [ avto ]", 'callback_data' => "transAPI^⚪️ Payme [ avto ]"]];
$keyboard2[] = [['text' => "🟢 Xazna [ avto ]", 'callback_data' => "transAPI^🟢 Xazna [ avto ]"],['text' => "🟢 Zoomrad [ avto ]", 'callback_data' => "transAPI^🟢 Zoomrad [ avto ]"]];
$keyboard2[] = [['text' => "🟣 Uzum bank [ avto ]", 'callback_data' => "transAPI^🟣 Uzum bank [ avto ]"],['text' => "🔴 Anorbank [ avto ]", 'callback_data' => "ttransAPI^🔴 Anorbank [ avto ]"]];
$keyboard2[] = [['text' => "🟢 Paynet [ avto ]", 'callback_data' => "transAPI^🟢 Paynet [ avto ]"],['text' => "🟤 Beepul [ avto ]", 'callback_data' => "transAPI^🟤 Beepul [ avto ]"]];
$keyboard2[] = [['text' => "⚪️ Oson [ avto ]", 'callback_data' => "transAPI^⚪️ Oson [ avto ]"],['text' => "🟢 Tenge24 [ avto ]", 'callback_data' => "transAPI^🟢 Tenge24 [ avto ]"]];
$keyboard2[] = [['text' => "🟢 Alif [ avto ]", 'callback_data' => "transAPI^🟢 Alif [ avto ]"],['text' => "🔴 Uzcard [ avto ]", 'callback_data' => "transAPI^🔴 Uzcard [ avto ]"]];
$kb = json_encode(['inline_keyboard' => $keyboard2]);
sms($cid, "💳 Kerakli to'lov tizimini tanlang:", $kb);
exit();
}

if($data == "addfunds"){
$a = mysqli_query_new($connect,"SELECT * FROM `card`");
$k = [];
while($s = mysqli_fetch_assoc($a)){
$k[] = ['text' => $s['name'], 'callback_data' => "pay=" . $s['id']];
}
$keyboard2 = array_chunk($k, 2);
$keyboard2[] = [['text' => "⭐️ Stars orqali to'lov", 'callback_data' => "stars"]];
$keyboard2[] = [['text' => "🔵 Click [ avto ]", 'callback_data' => "transAPI^🔵 Click [ avto ]"],['text' => "⚪️ Payme [ avto ]", 'callback_data' => "transAPI^⚪️ Payme [ avto ]"]];
$keyboard2[] = [['text' => "🟢 Xazna [ avto ]", 'callback_data' => "transAPI^🟢 Xazna [ avto ]"],['text' => "🟢 Zoomrad [ avto ]", 'callback_data' => "transAPI^🟢 Zoomrad [ avto ]"]];
$keyboard2[] = [['text' => "🟣 Uzum bank [ avto ]", 'callback_data' => "transAPI^🟣 Uzum bank [ avto ]"],['text' => "🔴 Anorbank [ avto ]", 'callback_data' => "transAPI^🔴 Anorbank [ avto ]"]];
$keyboard2[] = [['text' => "🟢 Paynet [ avto ]", 'callback_data' => "transAPI^🟢 Paynet [ avto ]"],['text' => "🟤 Beepul [ avto ]", 'callback_data' => "transAPI^🟤 Beepul [ avto ]"]];
$keyboard2[] = [['text' => "⚪️ Oson [ avto ]", 'callback_data' => "transAPI^⚪️ Oson [ avto ]"],['text' => "🟢 Tenge24 [ avto ]", 'callback_data' => "transAPI^🟢 Tenge24 [ avto ]"]];
$keyboard2[] = [['text' => "🟢 Alif [ avto ]", 'callback_data' => "transAPI^🟢 Alif [ avto ]"],['text' => "🔴 Uzcard [ avto ]", 'callback_data' => "transAPI^🔴 Uzcard [ avto ]"]];
$kb = json_encode(['inline_keyboard' => $keyboard2]);
edit($cid2, $mid2, "💳 Kerakli to'lov tizimini tanlang:", $kb);
exit();
}


if (isset($update->pre_checkout_query)) {
    $query_id = $update->pre_checkout_query->id;

    // Har doim OK qaytaring
    bot('answerPreCheckoutQuery', [
        'pre_checkout_query_id' => $query_id,
        'ok' => true
    ]);
}



$successful_payment = $update->message->successful_payment;
$payment_currency = $successful_payment->currency;
$payment_total_amount = $successful_payment->total_amount;
$payment_invoice_payload = $successful_payment->invoice_payload;
$user_id = $update->message->from->id;
if (isset($update->message->successful_payment)) {
$payment = $update->message->successful_payment;
$payload = $payment->invoice_payload;
if (preg_match('/stars_user_(\d+)/', $payload, $match)) {
$user_id = $match[1];
}
$total_amount = $payment->total_amount;
mysqli_query($connect, "UPDATE users SET main_balance = main_balance + $total_amount WHERE user_id = '$user_id'");
sms($user_id, "✅ $total_amount ⭐️ yulduz hisobingizga qo‘shildi.");
}




if ($data == "stars") {
del();
sms($cid2, "💵 Balansizni necha so'mga to'ldirmoqchisiz?
📰 Minimal miqdor: 10 ⭐️", $back);
file_put_contents("step/$cid2.step", "stars");
}




if ($step == "stars" and $text != "🔙 Orqaga") {
$amount = intval(trim($text));
if (is_numeric($amount)) {
if ($amount >= 10 && $amount <= 10000000) {
$uniqd =md5(uniqid());
$payload = "stars_user_{$cid}";
bot('sendInvoice', [
'chat_id' => $cid,
'title' => "⭐️ Yulduz xaridi",
'description' => "$amount ⭐️ yulduz uchun to‘lov",
'payload' => $payload,
'currency' => "XTR",
'prices' => json_encode([['label' => '⭐️ Yulduz', 'amount' => $amount]]),
]);
unlink("step/$cid.step");
} else {
sms($cid, "<u>⬇️Minimal:</u> 10⭐️", null);
}
} else {
sms($cid, "• Faqat raqamlardan foydalaning!", null);
}
}


if (stripos($data, "transAPI^") !== false) {
$payment_type = explode("^", $data)[1];
del();
sms($cid2, "💵 Balansizni necha so'mga to'ldirmoqchisiz?
📰 Minimal miqdor: 1000 so'm
1 ⭐️ = 150 so'm", $back);
file_put_contents("step/$cid2.step", "send_amount^$payment_type");
}



if (stripos($step, "send_amount^") !== false and $text != "🔙 Orqaga") {
$payment_type = explode("^", $step)[1];
$amount = intval(trim($text));
if (is_numeric($amount)) {
if ($amount >= 1000 && $amount <= 10000000) {
$rew = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM review WHERE amount = '$amount' AND status = 'pending'"));
if($rew){
sms($cid,"⚠️ Bu miqdordagi to‘lov allaqachon mavjud.

💵 Miqdorni biroz o‘zgartiring masalan: ".($amount+500)." so‘m",$back);
exit;
}
$ch = curl_init("https://api-url.uz/payment/create");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['shop_id' => 1,'shop_key' => "6c3a1e542955",'amount' => $amount]));
$response = curl_exec($ch);
$response = json_decode($response, true);
$payment_id = $response['payment_id'];
$sav = date("H:i:s | Y-m-d");
mysqli_query($connect, "INSERT INTO review(`user_id`,`payment_id`,`amount`,`status`,`method`,`date`) VALUES ('$cid','$payment_id', '$amount', 'pending','$payment_type','$sav')");
$insert_id = $connect->insert_id;
$rub = intval($amount/200);
sms($cid, "✅ To‘lov miqdori qabul qilindi!

🆔 To‘lov ID: <code>$insert_id</code>
💵 Miqdori: $amount so‘m
📥 Qabul qilish: $rub ⭐️
💳 To‘lov uchun: <code>9860176601638974</code>
⏰ To‘lovni kutish: 5 daqiqa",inline([
[['text'=>"♻️ To'lov tekshirish",'callback_data'=>"PayCheck^$payment_id"]],
[['text'=>"❌ To'lovni bekor qilish",'callback_data'=>"CancelPay^$payment_id"]],
]));
unlink("step/$cid.step");
} else {
sms($cid, "<u>⬇️Minimal:</u> 1.000 so‘m\n<u>⬆️Maksimal:</u> 10.000.000 so‘m", null);
}
} else {
sms($cid, "• Faqat raqamlardan foydalaning!", null);
}
}


if (stripos($data, "PayCheck^") !== false) {
$payment_id = explode("^", $data)[1];
$row = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `review` WHERE payment_id = '$payment_id'"));
$pay_id = $row['payment_id'];
$summa = $row['amount'];
$rub = intval($summa/200);
$user_id = $row['user_id'];
$sav = date("H:i:s | Y-m-d");
$response = json_decode(file_get_contents("https://api-url.uz/merchant/$pay_id/json"), true);
$status = $response['status'] ?? null;
if ($status === "paid") {
mysqli_query($connect, "UPDATE `review` SET `status` = 'paid' WHERE `payment_id` = '$pay_id'");
del();
sms($cid2,"✅ Hisobingizga $summa so'm qo'shildi.", $menu);
mysqli_query($connect, "UPDATE `users` SET main_balance = main_balance + $rub WHERE `user_id` = '$user_id'");
sms($admin, "🎉 Foydalanuvchi botga to'lov qilindi.

👤 User: <a href='tg://user?id=$user_id'>".name($user_id)."</a>
💰 Balansi: ".user($user_id)['balance']." ⭐️
💳 To'lov miqdori: $summa so'm ($rub ⭐️)
💾 ID raqami: [ <code>$user_id</code> ]

⏰ $sav");
} elseif ($status === "cancel") {
mysqli_query($connect, "UPDATE `review` SET `status` = 'cancel' WHERE `payment_id` = '$pay_id'");
del();
sms($cid2, "❌ Sizning $summa so'mlik to'lovingiz bekor qilindi!\n\n⚠️ Qayta to'lov qilishingiz mumkin!", $menu);
} elseif ($status === "pending") {
accl($qid, "❌ To'lov qilinmagan.",true);
}
}



if (stripos($data, "CancelPay^") !== false) {
$payment_id = explode("^", $data)[1];
del();
mysqli_query($connect, "UPDATE `review` SET `status` = 'cancel' WHERE `payment_id` = '$payment_id'");
sms($cid2, "❌ Sizning to'lovingiz bekor qilindi!", $menu);
}



if($text == "💼 Stars ishlash" && joinchat($cid) == true && phone($cid) == true){
$result = mysqli_query_new($connect, "SELECT * FROM settings WHERE id = '1'");
$setting = mysqli_fetch_assoc($result);
 $code = user($cid)['id'];
$link = "https://t.me/$bot?start=R$code";
$referal_price = $setting['referal_price'];
    bot('sendPhoto',[
        'photo'=>"https://t.me/code_detal/46",
'chat_id'=>$cid,
'caption'=>"<b>⚡️ Sizning taklif havolangiz:

$link

🖇️ 1 ta taklif uchun $referal_price ⭐️ beriladi.
💸 Do'stlaringizni va tanishlaringizni taklif qiling!

👤 Sizning takliflaringiz: ".user($cid)['referal']." ta</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🔄 Do'stlarga Yuborish",'url'=>"https://t.me/share/url?url=https://t.me/$bot?start=R$code"]],
[['text'=>"🏆 TOP 10",'callback_data'=>"top10"]],
]])
]);
}




if($data == "top10"){
    del($cid2, $mid2);
    $result = mysqli_query_new($connect, "SELECT * FROM `users` ORDER BY CAST(`referal` AS UNSIGNED) DESC LIMIT 10");
    if(!$result || mysqli_num_rows($result) == 0){
        accl($qid, "🚫 Mavjud Emas!", true);
        exit();
    }
    $num_row = mysqli_num_rows($result);
    $text = "🏆 TOP 100 - natijalar";
    $text .= "\n\n";
    $number = 1;
    while($row = mysqli_fetch_assoc($result)){
        $emoji = ($number === '1') ? "🥇" : (($number === '2') ? "🥈" : (($number === '3') ? "🥉" : "🏅"));
        $user_id = $row['user_id'];
        $ref_count = $row['referal'];
        $text .= "$emoji $number ] - ".name($user_id)." - $ref_count ta\n";
        $number++;
    }
    $text .= "\n";
    sms($cid2, $text);
    exit();
}


if($data == "solving"){
$a = mysqli_query_new($connect,"SELECT * FROM `solving_type`");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$k[]=['text'=>enc("decode",$s['name'])." - ".$s['minium']." ⭐️",'callback_data'=>"solving_get_quantity|".$s['id']."|".$s['minium']];
}
if(!$c){
sms($cid2,"⛔️ Yechish usullari topilmadi.");
    }else{
$keyboard2=array_chunk($k,1);
$type=json_encode([
'inline_keyboard'=>$keyboard2,
]);
edit($cid2,$mid2,"📋 ⭐️ yechish usullaridan birini tanlang:",$type);
}}


if($text == "📤 Stars yechish" && joinchat($cid) == true && phone($cid) == true){
$a = mysqli_query_new($connect,"SELECT * FROM `solving_type`");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$k[]=['text'=>enc("decode",$s['name'])." - ".$s['minium']." ⭐️",'callback_data'=>"solving_get_quantity|".$s['id']."|".$s['minium']];
}
if(!$c){
sms($cid,"⛔️ Yechish usullari topilmadi.");
    }else{
$keyboard2=array_chunk($k,1);
$type=json_encode([
'inline_keyboard'=>$keyboard2,
]);
sms($cid,"📋 ⭐️ yechish usullaridan birini tanlang:",$type);
}}




if (stripos($data, "solving_get_quantity|") !== false) {
$type_id = explode("|", $data)[1];
$minium = explode("|", $data)[2];
if (user($cid2)['balance'] < $minium) {
accl($qid, "⛔️ Yetarli mablag' mavjud emas!", true);
unlink("step/$cid.step");
exit();
} else {
del();
sms($cid2, "📋 Yechib olmoqchi bo'lgan miqdoringizni kiriting:",$back);
put("step/$cid2.step","solving_get_receiver|$type_id|$minium");
}}


if (stripos($step, "solving_get_receiver|") !== false and is_numeric($text)) {
    $type_id = explode("|", $step)[1];
    $minium = explode("|", $step)[2];

    if ($text < $minium) {
        sms($cid, "❗️ Minimal yechib olish miqdori $minium ⭐️ bo'lishi kerak!", $menu);
        unlink("step/$cid.step");
        exit();
    }

    if (user($cid)['balance'] < $text) {
        sms($cid, "⛔️ Hisobingizda yetarli mablag' mavjud emas!", $menu);
        unlink("step/$cid.step");
        exit();
    }

    $rew = mysqli_fetch_assoc(mysqli_query_new($connect, "SELECT * FROM solving_type WHERE id = $type_id"));
    sms($cid, " 📋 « " . enc("decode", $rew['name']) . " » - raqamingizni kiriting:", $back);
    put("step/$cid.step", "solving_over|$type_id|$minium|$text");
}


if (stripos($step, "solving_over|") !== false and $text != "🔙 Orqaga") {
$type_id = explode("|", $step)[1];
$minium = explode("|", $step)[2];
$quantity = explode("|", $step)[3];
if (user($cid)['balance'] < $quantity) {
sms($cid, "⛔️ Yetarli mablag' mavjud emas!",$menu);
unlink("step/$cid.step");
exit();
} else {
mysqli_query_new($connect,"UPDATE users SET balance = balance - $quantity WHERE user_id = $cid");
$rew = mysqli_fetch_assoc(mysqli_query_new($connect,"SELECT * FROM solving_type WHERE id = $type_id"));
mysqli_query_new($connect,"INSERT INTO solver(`user_id`,`type`,`quantity`,`status`,`receiver`,`date`) VALUES ('$cid','$type_id','$quantity','🔄 Kutilmoqda...','$text','$sana|$soat')");
$insert_id = $connect->insert_id;
sms($cid,"✅ ⭐️ yechishga bosh adminga ariza berildi.

📋 Ariza ID: <code>$insert_id</code>
💰 Miqdor: $quantity ⭐️
📦 Turi: ".enc("decode",$rew['name'])."
🔢 Hamyon: <code>$text</code>
📯 Holati: 🔄 Kutilmoqda...

⏰ $sana | $soat",$menu);
sms($admin,"🆕 Yangi ariza keldi, Foydalanuvchi ⭐️ yechib olmoqchi:

🆔 Foydalanuvchi ID: <code>$cid</code>
👥 Foydalanuvchi ismi: ".name($cid)."
📋 Ariza ID: <code>$insert_id</code>
💰 Miqdor: $quantity ⭐️
📦 Turi: ".enc("decode",$rew['name'])."
🔢 Hamyoni: <code>$text</code>


⏰ $sana | $soat",inline([
    [['text' => '📋 Hamyonni nusxalash', 'copy_text' => ['text' =>$text]]],
    [['text'=>"👁 Ko'rish",'url'=>"tg://user?id=$cid"]],
    [['text'=>"✅ Tasdiqlash",'callback_data'=>"ok_solve|$insert_id"]],
    ]));
unlink("step/$cid.step");
}}



if((stripos($data,"ok_solve|")!==false)){
$solve_id=explode("|",$data)[1];
mysqli_query_new($connect,"UPDATE solver SET status='✅ To\'langan !' WHERE id =$solve_id");
$info = mysqli_fetch_assoc(mysqli_query_new($connect,"SELECT * FROM solver WHERE id = $solve_id"));
$type = mysqli_fetch_assoc(mysqli_query_new($connect,"SELECT * FROM solving_type WHERE id = ".$info['type'].""));
edit($cid2,$mid2,"✅ Ariza Qabul qilindi.

👤 ID: <code>".$info['user_id']."</code>");
sms($info['user_id'],"".$info['quantity']." ⭐️ kartangizga o'tqazildi.✅");
sms(settings()['proof'],"📋 Bot orqali ⭐️ yechib olindi.

👤 Foydalanuvchi: ".name($info['user_id'])."
💰 Miqdor: ".$info['quantity']." ⭐️
🔍 Yechish turi: ".enc("decode",$type['name'])."
📤 Holati: ✅ To'langan !

⏰ $sana | $soat",inline([
        [['text'=>"👁 Ko'rish",'url'=>"tg://user?id={$info['user_id']}"]],
    [['text'=>"🤖 Botga O'tish",'url'=>"https://t.me/$bot"]],
    ]));
}


#---------------------------------------------

if ($text == "📋 To'lovlar" && joinchat($cid) == true && phone($cid) == true) {
    $rew = mysqli_query_new($connect, "SELECT * FROM solver WHERE user_id = '$cid'");
    $c = mysqli_num_rows($rew);

    if (!$c) {
        sms($cid, "⛔️ Siz botdan ⭐️ yechib olmagansiz.", inline([]));
    } else {
        $message = "📋 Bot orqali yechib olingan ⭐️laringiz:\n\n";
        while ($a = mysqli_fetch_assoc($rew)) {
            $solve_id = $a['id'];
            $quantity = $a['quantity'];
            $status = $a['status'];
            $card = $a['receiver'];
            $type = $a['type'];
$solve_type = enc("decode",mysqli_fetch_assoc(mysqli_query_new($connect,"SELECT * FROM solving_type WHERE id = $type"))['name']);
            $date = $a['date'];
            
            $message .= "📑 Ariza ID: <code>$solve_id</code>
🔢 Miqdor: $quantity ⭐️ 
💳 Yechish usuli: $solve_type
🎊 Holati: $status
📝 Tushirilgan: <code>$card</code>
⏰ $date\n";
$message .= "➖➖➖➖➖➖➖➖➖➖➖➖➖➖➖\n";
        }
        sms($cid, $message, inline([]));
    }
}





#panel-------------------------------------------------


if($text == "📤 Yechish usullari"){
sms($cid,"📋 Quyidagi amalllardan birini tanlang:",inline([
    [['text'=>"➕",'callback_data'=>"add_type"]],
    [['text'=>"🗑",'callback_data'=>"delete_type"]],
    ]));
}


if($data == "delete_type"){
$a = mysqli_query_new($connect,"SELECT * FROM `solving_type`");
$c = mysqli_num_rows($a);
while($s = mysqli_fetch_assoc($a)){
$k[]=['text'=>enc("decode",$s['name'])." 🗑",'callback_data'=>"delete_type=".$s['id']];
}
if(!$c){
edit($cid2,$mid2,"⛔️ Yechish usullari topilmadi!");
    }else{
$keyboard2=array_chunk($k,1);
$kb=json_encode([
'inline_keyboard'=>$keyboard2,
]);
edit($cid2,$mid2,"📋 O'chirmoqchi bo'lganizni ustiga bosing:",$kb);
exit(); 
}
}


if((stripos($data,"delete_type=")!==false)){
$card_id=explode("=",$data)[1];
mysqli_query_new($connect,"DELETE FROM solving_type WHERE id = $card_id");
edit($cid2,$mid2,"⛔️ O'chirib tashlandi.");
}

if($data == "add_type"){
sms($cid2,"✉️ Yechish usuli uchun nom yozing:

🔤Misol uchun: <code>Telefon raqam</code>",$boshqarish);
put("step/$cid2.step","add_type");
}


if($step == "add_type"  and $text != "🗄 Boshqarish"){
sms($cid,"« $text » - ✅ Nomi qabul qilindi.

🔢 Minimal yechish miqdorini yuboring:

📄Misol uchun: <code>10000</code>",$boshqarish);
put("step/$cid.step","add_type^$text");
}

if((stripos($step,"add_type^")!==false  and $text != "🗄 Boshqarish")){
$name=enc("encode",explode("^",$step)[1]);
mysqli_query_new($connect,"INSERT INTO solving_type(`name`,`minium`) VALUES ('$name','$text')");
sms($cid,"✅ Muvvafaqiyatli yangi yechish usuli qo'shildi.",$panel);
unlink("step/$cid.step");
}




if ($text == "🤖 Bot holati" and $cid == $admin) {
    $status = settings()['bot_status'] == "off" ? "⛔️ Bot O'chirilgan!" : "✅ Bot Yoqilgan!";
    $status2 = settings()['bot_status'] == "off" ? "✅ Botni yoqish" : "⛔️ Botni O'chirish";
    sms($admin, "[ $status ]", inline([
        [['text' => $status2, 'callback_data' => "toggle_bot_status"]],
    ]));
}

if ($data == "toggle_bot_status") {
    $new_status = settings()['bot_status'] == "off" ? "on" : "off";
    $status = $new_status == "on" ? "✅ Bot Yoqilgan!" : "⛔️ Bot O'chirilgan!";
    $status2 = $new_status == "on" ? "⛔️ Botni O'chirish" : "✅ Botni yoqish";
    mysqli_query_new($connect, "UPDATE settings SET bot_status ='$new_status' WHERE id = '1'");
    edit($admin, $mid2, "[ $status ]", inline([
        [['text' => $status2, 'callback_data' => "toggle_bot_status"]],
    ]));
}


if ($text == "📊 Hisobot") {
    $stat = mysqli_num_rows(mysqli_query_new($connect, "SELECT * FROM `users`"));
    $stat2 = mysqli_num_rows(mysqli_query_new($connect, "SELECT * FROM `users` WHERE date = '$sana'"));
    $stat3 = mysqli_num_rows(mysqli_query_new($connect, "SELECT * FROM `users` WHERE action = 'member'"));
    $stat4 = mysqli_num_rows(mysqli_query_new($connect, "SELECT * FROM `users` WHERE action = 'kicked'"));
    $stat5 = mysqli_num_rows(mysqli_query_new($connect, "SELECT * FROM `orders`"));
    $stat6 = mysqli_num_rows(mysqli_query_new($connect, "SELECT * FROM `orders` WHERE status = '✅ Tushirilgan'"));
    $row = mysqli_fetch_assoc(mysqli_query_new($connect, "SELECT SUM(solved) AS total_solving FROM `users`"));
    $total_solving = $row['total_solving'] ?? 0;    
    bot('SendMessage', [
        'chat_id' => $admin,
        'text' => "<b>📋<u> Bot haqida to'liq hisobot natijalari</u>:

👥 <u>Barcha bot a'zolar</u>: <code>$stat</code> ta
🆕 <u>Bugungi qo'shilgan A'zolar</u>: <code>$stat2</code> ta
♻️ <u>Faol a'zolar soni</u>: <code>$stat3</code> ta
⛔️ <u>Tark etgan a'zolar</u>: <code>$stat4</code> ta
💰 <u>Barcha chiqarilgan</u>: <code>$total_solving</code> ⭐️

📅 Sana: <code>$sana</code> | ⏰ Soat: <code>$soat</code></b>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
[['text' => "🏆 TOP 100 REFERAL", 'callback_data' => "stat_referal"],['text' => "🏆 TOP 100 BALANS", 'callback_data' => "stat_balans"]],
            ]
        ])
    ]);

    exit();
}

if($data == "stat_balans"){
    del($cid2, $mid2);
    $result = mysqli_query_new($connect, "SELECT * FROM `users` ORDER BY CAST(`main_balance` AS UNSIGNED) DESC LIMIT 100");
    if(!$result || mysqli_num_rows($result) == 0){
        accl($qid, "🚫 Mavjud Emas!", true);
        exit();
    }
    $num_row = mysqli_num_rows($result);
    $text = "🏆 TOP 100 - natijalar";
    $text .= "\n\n";
    $number = 1;
    while($row = mysqli_fetch_assoc($result)){
        $emoji = ($number === '1') ? "🥇" : (($number === '2') ? "🥈" : (($number === '3') ? "🥉" : "🏅"));
        $user_id = $row['user_id'];
        $ref_count = $row['main_balance'];
        $text .= "$emoji $number ] - <a href='tg://user?id=$user_id'>$user_id</a> - $ref_count ⭐️\n";
        $number++;
    }
    $text .= "\n";
    sms($cid2, $text);
    exit();
}

if($data == "stat_referal"){
    del($cid2, $mid2);
    $result = mysqli_query_new($connect, "SELECT * FROM `users` ORDER BY CAST(`referal` AS UNSIGNED) DESC LIMIT 100");
    if(!$result || mysqli_num_rows($result) == 0){
        accl($qid, "🚫 Mavjud Emas!", true);
        exit();
    }
    $num_row = mysqli_num_rows($result);
    $text = "🏆 TOP 100 - natijalar";
    $text .= "\n\n";
    $number = 1;
    while($row = mysqli_fetch_assoc($result)){
        $emoji = ($number === '1') ? "🥇" : (($number === '2') ? "🥈" : (($number === '3') ? "🥉" : "🏅"));
        $user_id = $row['user_id'];
        $ref_count = $row['referal'];
        $text .= "$emoji $number ] - <a href='tg://user?id=$user_id'>$user_id</a> - $ref_count ta\n";
        $number++;
    }
    $text .= "\n";
    sms($cid2, $text);
    exit();
}



if ($text == "📢 Kanallar" and $cid == $admin) {
    sms($cid, "<b>👉 Qo‘shmoqchi bo‘lgan kanal turini tanlang:</b>", json_encode([
        'inline_keyboard' => [
            [['text' => "Ommaviy", 'callback_data' => "request-false"]],
            [['text' => "So‘rov qabul qiluvchi", 'callback_data' => "request-true"]],
            [['text' => "Ixtiyoriy havola", 'callback_data' => "socialnetwork"]]
        ]
    ]));
}

if ($data == "socialnetwork") {
    del();
    sms($cid2, "<b>Havola uchun nom yuboring:</b>", $boshqarish);
    put("step/$cid2.step", "socialnetwork_step1");
}

if ($step == "socialnetwork_step1") {
    if (isset($text)) {
        file_put_contents("step/trash_social.txt", $text);
        sms($cid, "<b>✅ $text qabul qilindi!</b>

<i>ixtiyorit havolani kiriting:</i>", null);
        put("step/$cid.step", "socialnetwork_step2");
    } else {
        sms($cid, "Faqat matnlardan foydalaning", null);
    }
}

if ($step == "socialnetwork_step2") {
    if (isset($text)) {
        $nom = file_get_contents("step/trash_social.txt");
        if ($nom !== false) {
            $nom = base64_encode($nom);
            $sql = "INSERT INTO `channels` (`type`, `link`, `title`, `channelID`) VALUES ('social', '$text', '$nom', '')";
            if ($connect->query($sql)) {
                sms($cid, "<b>✅ Kanal muvoffaqiyatli qo‘shildi</b>", $panel);
                put("step/$cid.step", "none");
            } else {
                sms($cid, "<b>⚠️ Kanal qo‘shishda xatolik!</b>\n\n<code>{$connect->error}</code>", $panel);
                put("step/$cid.step", "none");
            }
        } else {
            sms($cid, "<b>Havola uchun nom yuboring:</b>", $aort);
            put("step/$cid.step", "socialnetwork_step1");
        }
    }

}

if (mb_stripos($data, "request-") !== false) {
    $type = explode("-", $data)[1];
    file_put_contents("step/$cid2.type", $type);
    sms($cid2, "<b>Endi kanalizdan \"forward\" xabar yuboring:</b>", $aort);
    put("step/$cid2.step", "qosh");
}

if ($step == "qosh" and isset($message->forward_origin)) {
    $kanal_id = $message->forward_origin->chat->id;
    $type = file_get_contents("step/$cid.type");
    if ($type == "true") {
        $link = bot('createChatInviteLink', [
            'chat_id' => $kanal_id,
            'creates_join_request' => true
        ])->result->invite_link;
        $sql = "INSERT INTO `channels` (`channelID`, `link`, `type`) VALUES ('$kanal_id', '$link', 'request')";
    } elseif ($type == "false") {
        $link = "https://t.me/" . $message->forward_origin->chat->username;
        $sql = "INSERT INTO `channels` (`channelID`, `link`, `type`) VALUES ('$kanal_id', '$link', 'lock')";
    }
    if ($connect->query($sql)) {
        sms($cid, "<b>✅ Kanal muvoffaqiyatli qo‘shildi</b>", $panel);
    } else {
        sms($cid, "<b>⚠️ Kanal qo‘shishda xatolik!</b>\n\n<code>{$connect->error}</code>", $panel);
    }
    unlink("step/$cid.type");
}



if ($text == "🗑️ Kanal o'chirish") {
    $result = $connect->query("SELECT * FROM `channels`");
    if ($result->num_rows > 0) {
        $button = [];
        while ($row = $result->fetch_assoc()) {
            $type = $row['type'];
            $channelID = $row['channelID'];
            if ($type == "lock" or $type == "request") {
                $gettitle = bot('getchat', ['chat_id' => $channelID])->result->title;
                $button[] = ['text' => "🗑️ " . $gettitle, 'callback_data' => "delchan=" . $channelID];
            } else {
                $gettitle = $row['title'];
                $button[] = ['text' => "🗑️ " . $gettitle, 'callback_data' => "delchan=" . $channelID];
            }
        }
        $keyboard2 = array_chunk($button, 1);
        $keyboard2[] = [['text' => "◀️ Orqaga", 'callback_data' => "setchannels"]];
        $keyboard = json_encode([
            'inline_keyboard' => $keyboard2,
        ]);
        sms($cid, "<b>Kerakli kanalni tanlang va u o‘chiriladi:</b>", $keyboard);
    } else {
        sms($cid, "<b>Hech qanday kanal ulanmagan!</b>", null);
    }
}

if (stripos($data, "delchan=") !== false) {
    $ex = explode("=", $data)[1];
    $result = $connect->query("SELECT * FROM `channels` WHERE channelID = '$ex'");
    $row = $result->fetch_assoc();
    if ($row['requestchannel'] == "true") {
        $connect->query("DELETE FROM requests WHERE chat_id = '$ex'");
    }
    $connect->query("DELETE FROM channels WHERE channelID = '$ex'");
    bot('editMessageText', [
        'chat_id' => $cid2,
        'message_id' => $mid2,
        'text' => "<b>✅ Kanal o'chirildi!</b>",
        'parse_mode' => 'html',
    ]);
}


if($text == "⚙️ Sozlama"){
sms($cid,"📋 Hozirgi holat:


1. Referal narxi: ".settings()['referal_price']." ⭐️
2. Admin user: ".settings()['admin_user']."
3. Isbotlar kanali: ".settings()['proof']."",inline([
[['text'=>"1. ✏️",'callback_data'=>"edit^referal_price"]],
[['text'=>"2. ✏️",'callback_data'=>"edit^admin_user"],['text'=>"3. ✏️",'callback_data'=>"edit^proof"]],
]));
}

if(mb_stripos($data, "edit^")!==false){
    $a = explode("^",$data)[1];
del();
sms($cid2,"Yangi qiymatni kiriting:",$boshqarish);
put("step/$cid2.step","edit^$a");
}

if(mb_stripos($step, "edit^")!==false){
$aa = explode("^",$step)[1];
if($cid == $admin and isset($text)){
sms($cid,"<b> Muvaffaqiyatli o'zgartirildi.</b>",$panel);
mysqli_query_new($connect,"UPDATE settings SET $aa='$text'");
unlink("step/$cid.step");
}
}






$time = date('H:i');
if($text == "📧 Xabar yuborish" and $cid == $admin){
$result = mysqli_query_new($connect, "SELECT * FROM `send`");
$row = mysqli_fetch_assoc($result);
if(!$row){
sms($cid,"<b>📤 Foydalanuvchilarga yuboriladigan xabarni botga yuboring!</b>",$boshqarish);
put("step/$cid.step","send");
}else{
sms($cid,"<b>📑 Hozirda botda xabar yuborish jarayoni davom etmoqda.</b>",$panel);
}}

if($step== "send" and $text != "🗄 Boshqarish"){
$result = mysqli_query_new($connect, "SELECT * FROM users");
$stat = mysqli_num_rows($result);
$res = mysqli_query_new($connect,"SELECT * FROM users WHERE id = '$stat'");
$row = mysqli_fetch_assoc($res);
$user_id = $row['user_id'];
$time1 = date('H:i', strtotime('+1 minutes'));
$time2 = date('H:i', strtotime('+2 minutes'));
$time3 = date('H:i', strtotime('+3 minutes'));
$time4 = date('H:i', strtotime('+4 minutes'));
$time5 = date('H:i', strtotime('+5 minutes'));
$tugma = json_encode($update->message->reply_markup);
$reply_markup = base64_encode($tugma);
mysqli_query_new($connect, "INSERT INTO `send` (`time1`,`time2`,`time3`,`time4`,`time5`,`start_id`,`stop_id`,`admin_id`,`message_id`,`reply_markup`,`step`) VALUES ('$time1','$time2','$time3','$time4','$time5','0','$user_id','$admin','$mid','$reply_markup','send')");
sms($admin,"<b>📋 Saqlandi!
📑 Xabar foydalanuvchilarga $time1 da yuborish boshlanadi!</b>",$panel);
unlink("step/$cid.step");
}


$saved = file_get_contents("step/us.id");

if($text == "🔍 User ko'rish"){
if($cid == $admin){
$keybot = json_encode([
'inline_keyboard'=>[
[['text'=>"🔹 ID orqali",'callback_data'=>"orqali=user_id"]],
[['text'=>"🔹 Tartib raqam raqam orqali",'callback_data'=>"orqali=id"]],
]]);
sms($cid,"<b>📋 Quyidagilardan birini tanlang:</b>",$keybot);
}
}

if(mb_stripos($data,"orqali=")!==false){
$by=explode("=",$data)[1];
if($by=="user_id"){$k="ID";}else{$k="tartib";}
del();
sms($cid2,"<u>Foydalanuvchi $k raqamini kiriting:</u>",$boshqarish);
put("step/$cid2.step","by—$by");
exit();
}
if(mb_stripos($step,"by—")!==false){
$bz=explode("—",$step)[1];
if($cid == $admin){
$rew = mysqli_fetch_assoc(mysqli_query_new($connect,"SELECT * FROM users WHERE $bz = $text"));
if($rew){
$idi = $rew['user_id'];
file_put_contents("step/us.id",$idi);
$⭐️ = $rew['balance'];
$referal = $rew['referal'];
$solved = $rew['solved'];
$ban = $rew['status'];
$phone = $rew['phone'];
if($ban == "active"){
    $bans = "🔔 Banlash";
}
if($ban == "off"){
    $bans = "🔕 Bandan olish";
}
sms($cid,"<u>🗄Foydalanuvchi topildi!</u> [<code>$idi</code>]

 <a href='tg://user?id=$idi'>👥 Foydalanuvchi</a>

<u>💰 Balansi: </u>$⭐️ ⭐️
<u>💳 Yechib olgan: </u>$solved ⭐️
<u>👥 Referali: </u>$referal ta
<u>📞 Raqami: </u>$phone
",json_encode([
    'inline_keyboard'=>[
        [['text'=>"📑 Referallarini Ko'rish",'callback_data'=>"user_referal"]],
[['text'=>"$bans",'callback_data'=>"ban"]],
[['text'=>"➕ ⭐️ qo'shish",'callback_data'=>"plus"],['text'=>"➖ ⭐️ ayirish",'callback_data'=>"minus"]],
]]));
unlink("step/$cid.step");
}else{
sms($cid,"<b>Foydalanuvchi topilmadi.</b>

Qayta urinib ko'ring:");
}}}






if($data == "user_referal"){
$as=1;
$result = mysqli_query_new($connect, "SELECT * FROM referals WHERE ref_id = $saved");
if(mysqli_num_rows($result) > 0) {
$keyboard = [];
while($row = mysqli_fetch_assoc($result)) {
$d = $as++;
$user_id = $row['user_id'];
$txt .= "$d]. <a href='tg://user?id=$user_id'>".name($user_id)."</a>";
}
edit($cid2,$mid2,$txt);
} else {
edit($cid2,$mid2,"🚫 Foydalanuvchi referal chaqirmagan!");
}
exit();
}




if($data == "plus"){
sms($cid2,"<a href='tg://user?id=$saved'>$saved</a> <b>ning hisobiga qancha ⭐️ qo'shmoqchisiz?</b>",$boshqarish);
file_put_contents("step/$cid2.step",'plus');
}

if($step == "plus"){
if($cid == $admin){
if(is_numeric($text)=="true"){
sms($saved,"<b><u>✅ Hisobingiz $text ⭐️ Qo'shildi</u>.</b>");
sms($cid,"<b>Foydalanuvchi hisobiga $text so‘m qo'shildi!</b>",$panel);
mysqli_query_new($connect,"UPDATE users SET balance=balance+$text WHERE user_id =$saved");
unlink("step/$cid.step");
}else{
sms($cid,"<b>Faqat raqamlardan foydalaning!</b>");
}}}

if($data == "minus"){
sms($cid2,"<a href='tg://user?id=$saved'>$saved</a> <b>ning hisobidan qancha ⭐️ ayirmoqchisiz?</b>",$boshqarish);
file_put_contents("step/$cid2.step",'minus');
}

if($step == "minus"){
if($cid == $admin){
if(is_numeric($text)=="true"){
sms($saved,"<b><u>🚫 Balansingizda $text Yechib Olindi</u>.</b>");
sms($cid,"<b>Foydalanuvchi hisobidan $text so‘m olindi!</b>",$panel);
$rew = mysqli_fetch_assoc(mysqli_query_new($connect,"SELECT * FROM users WHERE user_id = $saved"));
$miqdor =$rew['balance'] - $text;
mysqli_query_new($connect,"UPDATE users SET balance=$miqdor WHERE user_id =$saved");
unlink("step/$cid.step");
}else{
sms($cid,"<b>Faqat raqamlardan foydalaning!</b>");
}}}

if($data=="ban"){
$rew = mysqli_fetch_assoc(mysqli_query_new($connect,"SELECT * FROM users WHERE user_id = $saved"));
if($admin!=$saved){
if($rew['status'] == "off"){
mysqli_query_new($connect,"UPDATE users SET status='active' WHERE user_id =$saved");
sms($cid2,"<b>Foydalanuvchi ($saved) bandan olindi!</b>",$panel);
}else{
mysqli_query_new($connect,"UPDATE users SET status='off' WHERE user_id =$saved");
sms($cid2,"<b>Foydalanuvchi ($saved) banlandi!</b>",$panel);
}}else{
accl($qid,"Bloklash mumkin emas!",true);
}}


if($_GET['update']=="message"){
$result = mysqli_query_new($connect, "SELECT * FROM `send`"); 
$row = mysqli_fetch_assoc($result);
$sendstep = $row['step'];
$row1 = $row['time1'];
$row2 = $row['time2'];
$row3 = $row['time3'];
$row4 = $row['time4'];
$row5 = $row['time5'];
$start_id = $row['start_id'];
$stop_id = $row['stop_id'];
$admin_id = $row['admin_id'];
$mied = $row['message_id'];
$tugma = $row['reply_markup'];
if($tugma == "bnVsbA=="){
$reply_markup = "";
}else{
$reply_markup = base64_decode($tugma);
}
$time1 = date('H:i', strtotime('+1 minutes'));
$time2 = date('H:i', strtotime('+2 minutes'));
$time3 = date('H:i', strtotime('+3 minutes'));
$time4 = date('H:i', strtotime('+4 minutes'));
$time5 = date('H:i', strtotime('+5 minutes'));
$limit = 150;

if($time == $row1 or $time == $row2 or $time == $row3 or $time == $row4 or $time == $row5){
$sql = "SELECT * FROM `users` LIMIT $start_id,$limit";
$res = mysqli_query_new($connect,$sql);
while($a = mysqli_fetch_assoc($res)){
$id = $a['user_id'];
if($id == $stop_id){
bot('forwardMessage', [
    'chat_id' => $id,
    'from_chat_id' => $admin_id,
    'message_id' => $mied
]);
sms($admin_id,"<b>✅ ️Xabar barcha bot foydalanuvchilariga yuborildi!</b>");
mysqli_query_new($connect, "DELETE FROM `send`");
exit;
}else{
bot('forwardMessage', [
    'chat_id' => $id,
    'from_chat_id' => $admin_id,
    'message_id' => $mied
]);
}
}
mysqli_query_new($connect, "UPDATE `send` SET `time1` = '$time1'");
mysqli_query_new($connect, "UPDATE `send` SET `time2` = '$time2'");
mysqli_query_new($connect, "UPDATE `send` SET `time3` = '$time3'");
mysqli_query_new($connect, "UPDATE `send` SET `time4` = '$time4'");
mysqli_query_new($connect, "UPDATE `send` SET `time5` = '$time5'");
$get_id = $start_id + $limit;
mysqli_query_new($connect, "UPDATE `send` SET `start_id` = '$get_id'");
bot('sendMessage',[
'chat_id'=>$admin_id,
'text'=>"<b>✅ Yuborildi: $get_id</b>",
'parse_mode'=>'html'
]);
}
echo json_encode(["status"=>true,"cron"=>"Sending message"]);
$res = mysqli_query($connect, "SELECT * FROM review WHERE status='pending'");
while ($row = mysqli_fetch_assoc($res)) {
$pay_id = $row['payment_id'];
$summa = $row['amount'];
$user_id = $row['user_id'];
$response = json_decode(file_get_contents("https://api-url.uz/merchant/$pay_id/json"), true);
$status = $response['status'];
if($status == "cancel"){
mysqli_query($connect, "UPDATE review SET status = 'cancel' WHERE payment_id = '$pay_id'");
sms($user_id, "❌ Sizning $summa so'mlik to'lovingiz bekor qilindi!\n\n⚠️ Qayta to'lov qilishingiz mumkin!", $m);
}
}
}

?>