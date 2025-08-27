# Stars Bot

## Server Talablari
- PHP 7.0 yoki undan yuqori
- MySQL 5.7 yoki undan yuqori
- cURL kengaytmasi
- mod_rewrite yoqilgan bo'lishi kerak

## O'rnatish

1. Barcha fayllarni serverga yuklang
2. `config.php` faylini sozlang:
   - Bot tokenini kiriting
   - Admin ID sini kiriting
   - Ma'lumotlar bazasi ma'lumotlarini to'g'rilang
3. Ma'lumotlar bazasini yarating va jadvallarni import qiling
4. `.htaccess` faylini tekshiring
5. Webhook ni sozlang

## Webhook o'rnatish

```bash
curl -F "url=https://siznidomen.uz/path/to/general.php" https://api.telegram.org/botYOUR_BOT_TOKEN/setWebhook
```

## Fayl huquqlari

```bash
chmod 755 -R /path/to/your/bot
chmod 777 -R /path/to/your/bot/step
```
