<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>Словарик</title>
    <link href="/q/style.css" rel="stylesheet"/>
    <link href="/q/css.css" rel="stylesheet"/>
	<link href="http://xtoolza.info/newcss4.css" rel="stylesheet"/>
</head>
<body id="linearBg1">
<?
header('Content-Type: text/html; charset=utf-8');?>
<h1 class='jumbotron'>Словарь</h1><br /> <br><br>
<p><b>GET</b> - При использовании метода GET данные передаются путем добавления к URL-адресу. Таким образом, они будут видны пользователю, что с точки зрения безопасности не всегда хорошо. Также максимальный объем передаваемых данных будет зависеть от браузера - от максимально-допустимого количества символов адресной строке браузера.</p>
<p><b>POST</b> - При использовании метода POST данные не будут видны пользователю (не отображаются в адресной строке браузера). И поэтому они более защищены, а, следовательно, и программа обрабатывающая эти данные более защищена в плане безопасности. Также объем передаваемых данных практически ни чем не ограничен.</p>
<p><b>CGI</b> - Common Gateway Interface является стандартом интерфейса, который служит для связи внешней программы с веб-сервером. Программу, которая работает по такому интерфейсу совместно с веб-сервером, принято называть шлюзом, многие больше любят названия скрипт или CGI-программа. Сам протокол разработан таким образом, чтобы можно было использовать любой язык программирования, который может работать со стандартными устройствами ввода/вывода. А это умеет даже сама операционная система, поэтому часто если вам не требуется сложный скрипт, его можно просто сделать в виде командного файла.</p>
<p><b>CURL</b>. libcurl это библиотека функций, которая позволяет взаимодействовать (обмениваться информацией) с различными серверами по различным протоколам. В настоящее время libcurl поддерживает протоколы http, https, ftp, gopher, telnet, dict, file, и ldap. libcurl также умеет работать с сертификатами HTTPS, посылать запросы к HTTP серверам методами POST и PUT, закачивать файлы по протоколам HTTP и FTP (последнее можно сделать также используя модуль FTP ), использовать прокси-серверы, cookies и аутентификацию пользователей.</p>
<p><b>PHP</b> - Hypertext Preprocessor (Препроцессор Гипертекста). Это широко используемый язык сценариев общего назначения с открытым исходным кодом.</p>
<p><b>MySQL</b> - это популярная система управления базами данных (СУБД).</p>
<p><b>.htaccess</b> - это файл-конфигуратор Apache-серверов, который дает возможность конфигурировать работу сервера в отдельных директориях (папках), не предоставляя доступа к главному конфигурационному файлу (apache/conf/httpd.conf).</p>
<p><b>Mod_rewrite</b>  - модуль, используемый веб-серверами для преобразования URL'ов.</p>
<p><b>JavaScript</b> - предназначен для написания сценариев для активных HTML-страниц. Язык JavaScript не имеет никакого отношения к языку Java. JavaScript не предназначен для создания автономных приложений. Программа на JavaScript встраивается непосредственно в исходный текст HTML-документа и интерпретируется брaузером по мере загрузки этого документа. С помощью JavaScript можно динамически изменять текст загружаемого HTML-документа и реагировать на события, связанные с действиями посетителя или изменениями состоятия документа или окна.</p>
<p><b>Ajax</b> расшифровывается как Asynchronous Javascript And XML (Асинхронные Javascript И XML) и технологией в строгом смысле слова не является. Это просто аббревиатура, обозначающая подход к созданию веб-приложений с помощью следующих технологий:<br/>
<ul>
<li>стандартизированное представление силами XHTML и CSS;</li>
<li>динамическое отображение и взаимодействие с пользователем с помощью DOM;</li>
<li>обмен и обработка данных в виде XML и XSLT;</li>
<li>JavaScript;</li>
<li>асинхронные запросы с помощью объекта XMLHttpRequest.</li>
</ul></p>
<p><b>HTML</b> HyperТext Markup Language. HTML – это язык разметки. Вы используете HTML для разметки текстового документа, точно так же, как это делает редактор при помощи жирного красного карандаша. Эти пометки служат для определения формата (или стиля), который будет использован при выводе текста на экран монитора.</p>
<p><b>SSH</b> — это специальный сетевой протокол, позволяющий получать удаленный доступ к компьютеру с большой степенью безопасности соединения. </p>
<p><b>FTP</b> — File Transfer Protocol — протокол передачи файлов) — стандартный протокол, предназначенный для передачи файлов в Интернет. Использует 21й порт. FTP часто используется для загрузки сетевых страниц и других документов с частного устройства на открытые сервера хостинга. </p>
<p><b>SFTP</b> — расшифровывается как SSH File Transfer Protocol — SSH-протокол для передачи файлов. Он предназначен для копирования и выполнения других операций с файлами поверх надёжного и безопасного соединения. Используется 22 порт</p>
<p><b>SaaS</b> (Software as a Service) - это модель использования бизнес-приложений в формате интернет-сервисов. <br />
SaaS приложения работают на сервере SaaS-провайдера, а пользователи получают к ним доступ через интернет-браузер. Пользователь не покупает SaaS-приложение, а арендует его - платит за его использование некоторую сумму в месяц. Таким образом достигается экономический эффект, который считается одним из главных преимуществ SaaS. </p>
<p><b>Apache</b> - это так называемый свободный веб-сервер, представляющий  собой кросплатформенное программное обеспечение. Обычно настройка Apache-сервера происходит через файл .htaccess. Этот файл лежит на сервере, на котором находится Ваш сайт. А сам сервер считывает его содержимое и применяет настройки, которые там указаны.</p>
<p><b>Nginx</b> - веб-сервер и почтовый прокси-сервер, работающий на Unix-подобных операционных системах. Обычно настраивается связка nginx+apache, в которой nginx обслуживает все входящие на сервер запросы, статические файлы отдает своими силами, а запросы на динамическое содержимое проксирует на apache.</p>
<p><b>IIS Web Server</b> - веб-сервер в операционной системе Windows Server.</p>
</body>
<html>
