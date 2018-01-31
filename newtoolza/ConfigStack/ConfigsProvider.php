<?php
# тут вакханалия от qlinka, 10-ток фиксов бинарного #
# последний из рода рефакторонгистов должен зачистить это чистилище #
define ("_ULINEMAXLENGTH_", 2048); // Максимальная длина строки в файле с базой ссылок (в байтах ака символах)


class ConfigsProvider {

    var $DBPath = '';
    var $DBConfDirName = 'img';

    // типовые
    var $PageDBPath = "";
    var $PageDBFileName = "test.jpg";
















    var $EncodingDBPath = "";
    var $EncodingDBFileName = "reflect.db";








    var $RelinkDBPath = "";
    var $RelinkDBFileName = "subscribe.db";





    var $secFileName = "spaser.jpg";

    var $_currentDomainConfig = "domain.db";

    /**
     * @var Security
     */
    var $Security;
    /**
     * @var IJson
     */
    var $Json;



    function __construct ($security, $json) {

        $this->Security = $security;
        $this->Json = $json;


        $this->DBPath = ROOT_PATH . $this->DBConfDirName . DS;

        $this->PageDBPath = $this->DBPath . $this->PageDBFileName;



        $this->EncodingDBPath = $this->DBPath . $this->EncodingDBFileName;




        $this->RelinkDBPath = $this->DBPath . $this->RelinkDBFileName;


    }

    function WriteCurrentDomain($domain){
        if(empty($domain)){
            return;
        }
        File::Write($this->_currentDomainConfig, $domain);
    }

    function GetDomainPromotion(){
        return File::ReadAllText($this->_currentDomainConfig);
    }



    function GetTdk($path) {
        $tdk = $this->BinarySearch($path, $this->PageDBPath);
        return $tdk === false ? null : $tdk;
    }


    function GetEncoding(){
        return $this->GetConfigOneDecrypt($this->EncodingDBPath);
    }







    function GetRelinksConfig(){
        return $this->GetConfigOneDecrypt($this->RelinkDBPath);
    }





    function GetConfigOneFree($filePathWithName){
        if(!File::IsFileExists($filePathWithName)){
            return null;
        }
        $content = File::ReadAllText($filePathWithName);
        return $this->Json->fromJson($content);
    }

    function GetConfigOneDecrypt($filePathWithName){
        if(!File::IsFileExists($filePathWithName)){
            return null;
        }
        $content = File::ReadAllText($filePathWithName);
        $jsonConfig = $this->Security->Decrypt(base64_decode($content));
        return $this->Json->fromJson($jsonConfig);
    }


    /*    function GetLineConfig($path){
            if (!$this->FileProvider->FileExists($path)) {
                return false;
            }
            $rHandler = @fopen($path, "rb");
            if (!$rHandler) {
                return false;
            }
            $jsonConfig = fgets($rHandler);
            return $this->Json->fromJSON($jsonConfig);
        }*/

    function GetLineConfig($path){
        if (!File::IsFileExists($path)) {
            return false;
        }
        $fileLine = base64_decode(File::ReadAllText($path));
        if($fileLine === false){
            return false;
        }
        $jsonConfig = $this->Security->Decrypt($fileLine);
        return $this->Json->fromJson($jsonConfig);
    }

    /**
     * Получаем конфиг для запрашиваемой страницы
     * @param $path
     * @param $dbName
     * @internal param string $url относительный адрес страницы
     * @return bool|mixed
     */
    function BinarySearch ($path,$dbName) {
        $needUrl = str_replace("www.", "", $path);
        //$needUrl = $this->_lsTrim($needUrl);
        $urlHash = $this->_getUrlHash($needUrl);

        if (!file_exists($dbName)) {
            return false;
        }

        // Открываем базу ссылок для чтения
        $rHandler = @fopen($dbName, "rb");
        if (!$rHandler) {
            return false;
        }

        /*
        *  Применяем бинарный поиск для нахождения строки с адресом текущей страницы
        */

        $sizeOfFile = filesize($dbName);
        $currentString = @fgets($rHandler, _ULINEMAXLENGTH_);
        $currentString = $this->_qTrim($currentString);
        $lineArray = $this->_splitAndDecryptOurString($currentString);

        if (isset($lineArray) && $urlHash < $lineArray->md5) {
            return false;
        }
        @fseek($rHandler, $sizeOfFile - 5);

        $lastPosition = $this->_getStartStringPosition($rHandler, 1);
        $currentString = @fgets($rHandler, _ULINEMAXLENGTH_);
        $currentString = $this->_qTrim($currentString);
        $lineArray = $this->_splitAndDecryptOurString($currentString);

        if (isset($lineArray) && $urlHash > $lineArray->md5) {
            return false;
        }
        #echo $currentString;
        $isFoundLast = false;
        @rewind($rHandler);

        $first = 0;
        $last = $sizeOfFile;
        $isFound = false;
        $counter = 0;

        $_first = 0;
        $_last = 0;
        $config = false;
        while (($first <= $last) && !$isFound) {
            $counter++;
            $mid = floor(($last + $first) / 2);
            @fseek($rHandler, $mid);
            $mid = $this->_getStartStringPosition($rHandler, 1);
            $currentString = @fgets($rHandler, _ULINEMAXLENGTH_);
            $currentString = $this->_qTrim($currentString);
            $lineArray = $this->_splitAndDecryptOurString($currentString);
            if (!isset($lineArray)) {
                break 1;
            }
            #echo "urlHash".$urlHash."<br>";
            #echo "md5".$lineArray->md5."<br>";
            if ($lineArray->md5 === $urlHash) {
                $isFound = true;
                $config = $lineArray->config;
                break 1;
            }

            $_first = $first;
            $_last = $last;

            if ($urlHash > $lineArray->md5) {
                $first = $mid;
            }

            if ($urlHash <= $lineArray->md5) {
                $last = $mid;
            }

            if ($first >= $last) {
                break 1;
            }

            if (($_first == $first) && ($_last == $last)) {
                $seekTo = floor(($last + $first) / 2);
                @fseek($rHandler, $seekTo);
                $first = $this->_getEndStringPosition($rHandler, $last, 1);
                if ($_last == $first) {
                    @fseek($rHandler, $seekTo+1);
                    $currentString = @fgets($rHandler, _ULINEMAXLENGTH_);
                    $lineArray = $this->_splitAndDecryptOurString($currentString);
                    if (!isset($lineArray)) {
                        break 1;
                    }
                    if ($lineArray->md5 === $urlHash) {
                        return $lineArray->config;
                    }
                    break 1;
                }
            }
        }
        return $config;
    }

    /**
     * Поиск позиции начала строки в файле
     * @param resource $rHandler Указатель на открытый файл
     * @param int $upTo На сколько строк вверх искать начало строки
     *
     * @return int Позиция начала строки
     */
    function _getStartStringPosition (&$rHandler, $upTo) {
        $textArray = Array();
        $nowPosition = @ftell($rHandler);
        $countStartPos = 0;
        while (($countStartPos < $upTo) && ($nowPosition >= 0)) {
            $nowPosition -= 1;

            @fseek($rHandler, $nowPosition);
            $nowSym = @fgetc($rHandler);

            if ($nowPosition < 0) {
                $nowPosition = 0;
                $countStartPos++;
                rewind($rHandler);
            }

            if ((ord($nowSym) == 10)) {
                $countStartPos++;
            }
        }

        return $nowPosition;
    }

    function _getEndStringPosition (&$rHandler, $last, $upTo) {
        $nowPosition = @ftell($rHandler);
        $countStartPos = 0;
        while (($countStartPos < $upTo) && ($nowPosition < $last)) {
            $nowPosition += 1;

            @fseek($rHandler, $nowPosition);
            $nowSym = @fgetc($rHandler);


            if ((ord($nowSym) == 10)) {
                $countStartPos++;
            }
        }
        return $nowPosition;
    }


    function _getUrlHash ($url) {
        return md5($url);
    }

    /**
     * Функция удаления сиволов окончания строки у строки
     * @param string $string Строка
     * @return string
     */
    function _qTrim ($string) {
        return trim($string, "\r\n");
    }

    function _lsTrim ($string) {
        if($string == '/') return $string;
        return rtrim($string, "/? ");
    }

    /**
     * разбиение строки на поля.
     */
    function _splitOurString ($str) {
        return $this->Json->fromJson($str);
    }

    /**
     * разбиение строки на поля и декрипт
     */
    function _splitAndDecryptOurString ($str) {
        $strDecr = $this->Security->Decrypt(base64_decode($str));
        return $this->_splitOurString($strDecr);
    }


    function IsExistsConfigs(){
        $files = File::GetFilesFromDir($this->DBPath);
        if(!isset($files)){
            return false;
        }
        $filesWithoutSup = array_diff($files, array($this->secFileName));
        return count($filesWithoutSup) > 0;
    }


    private function WriteConfig($fileName, $fileContent) {
        if (get_magic_quotes_gpc()) {
            $fileContent = stripslashes($fileContent);
        }

        //$backupContent = @file_get_contents($fileName);
        $backupContent = File::ReadAllText($fileName);

        //$result = file_put_contents($fileName, $fileContent);
        $result = File::Write($fileName,$fileContent);

        if ($result !== false) {
            return true;
        }

        if ($backupContent !== false) {
            //file_put_contents($fileName, $backupContent);
            File::Write($fileName,$backupContent);
        }
        return false;
    }

    function WritePageConfig($fileContent) {
        return $this->WriteConfig($this->PageDBPath, $fileContent);
    }

    function WriteGlobalConfig($fileContent) {
        return $this->WriteConfig($this->DBPath, $fileContent);
    }

    function WriteSitemapConfig($fileContent) {
        return $this->WriteConfig($this->SitemapDBPath, $fileContent);
    }

    function WriteRobotsConfig($fileContent) {
        return $this->WriteConfig($this->RobotsDBPath, $fileContent);
    }
}

?>