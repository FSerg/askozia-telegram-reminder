# askozia-telegram-reminder

Пример простейшего PHP-скрипта для отправки уведомлений в Telegram о пропущенных звонках в АТС [Askozia](https://www.askozia.ru/) (Должно работать и в обычном [Asterisk`е](http://www.asterisk.org/))

Как сделать для АТС Askozia простейшую систему уведомлений о пропущенных звонках?
===
Вообще в Askozia для каждого модуля очереди звонков ([Queue](http://wiki.askozia.ru/handbook:cfe:modules#queue)) генерируется симпатичная web-страничка ([Wallboard](http://wiki.askozia.ru/handbook:cfe:modules#wallboard)) со статистикой в реальном времени (в том числе видно и пропущенные звонки).
Возможно, в небольшом коллективе, в одном помещении этим даже как-то можно пользоваться для отработки пропущенных звонков. Но организация доступа к этой страничке не очень удобная.
А если у вас много сотрудников в разных помещениях обрабатывают массу входящих звонков и при этом еще и пользуются групповыми чатами Telegram, то так и просится, чтобы Askozia в этот общий чат присылала уведомления.

![Пример уведомления в Telegram](https://img-fotki.yandex.ru/get/66745/58143147.c8/0_a8ef2_a4929626_orig.png)

Делается это довольно легко
---
1. Регистрируем (https://telegram.me/botfather) Telegram bot’а, к примеру у нас будет @**askozia_ats_bot**
- Записываем полученный API token, к примеру у нас будет: “000111222333444555666777888999000”.
- Делаем нашего бота участником группы.
- Пишем в чате группы сообщение с указанием имени бота, что-то типа: “*@**askozia_ats_bot** привет!*”.
Нам это нужно чтобы получить идентификатор чата, можно CURLом, а можно просто в браузере открыть линк:
https://api.telegram.org/bot000111222333444555666777888999000/getUpdates
Увидим JSON-ответ в котором есть нужный нам **chat_id** (к примеру, у нас будет: 123456789). Зная **chat_id** можно отправлять в чат сообщения от имени бота простым GET-запросом:
[https://api.telegram.org/bot000111222333444555666777888999000/sendMessage?chat_id=123456789&text=ТекстСообщения](https://api.telegram.org/bot000111222333444555666777888999000/sendMessage?chat_id=123456789&text=ТекстСообщения)

2. В приложениях Askozia добавляем новые PHP-скрипт
![Добавляем PHP-скрипт в приложения Askozia](https://img-fotki.yandex.ru/get/4214/58143147.c8/0_a8ef3_cfd1624c_orig.png)

Сам текст скрипта:
```
<?php
require('phpagi.php');
  function GetVarChannnel($agi, $_varName){
    $v = $agi->get_variable($_varName);
    if(!$v['result'] == 0){
        $agi->verbose($_varName.' ---> '.$v['data'], 10);
        return $v['data'];
    }
    else{
        $agi->verbose($_varName.' not set', 10);
        return "";
    }
  } // GetVarChannnel($_agi, $_varName)

  $agi = new AGI();
  $phonenumber = GetVarChannnel($agi, "CALLERID(num)");
  $mytime = GetVarChannnel($agi, "STRFTIME(${EPOCH},,%Y.%m.%d-%H:%M:%S)");

  $telegram_bot_token = "000111222333444555666777888999000";
  $chat_id = "123456789";
  $textMessage = "Пропущенный звонок: ".$phonenumber." (".$mytime.")";

  $content=file_get_contents("https://api.telegram.org/bot".$telegram_bot_token."/sendMessage?chat_id=".$chat_id."&text=".$textMessage,true);

?>​
```

PHP в Askozia старенький, поэтому приходится запрос делать через file_get_contents

3. Запоминаем идентификатор скрипта, его нам нужно будет прописать далее в редакторе маршрутов.
![Запоминаем идентифкатор PHP-скрипта](https://img-fotki.yandex.ru/get/25826/58143147.c8/0_a8ef4_37cd2397_orig.png)

4. В редакторе маршрутов добавляем пару блоков и все! :)
![Добавляем пару блокв в редакторе маршрутов Askozia](https://img-fotki.yandex.ru/get/9090/58143147.c9/0_a8ef5_af4c8665_orig.png)
