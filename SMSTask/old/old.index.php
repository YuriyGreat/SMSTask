<?php
declare(strict_types=1);
require_once('./config.php.ini');
require_once('./mysql.class.php');
require_once('./ExMysql.class.php');

set_time_limit(0);

echo DB_HOST;
echo DB_PASSWORD;


$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME); // connecting to database

header('Content-Type: text/html; charset=utf-8');



class SmsSender
{
    const TABLE = 'sms_list_tz';

    static function createNew($text)
    {
        $insert = array('TEXT' => self::clearText($text));
        SQLInsert(self::TABLE, $insert);
    }

    static function clearText($text)
    {
        $d = explode('©', $text);
        $text = $d[0];

        $text = str_replace(array("\r\n"), "", $text);
        $text = trim($text);

        return $text;
    }

    static function notify()
    {
        $sql = 'SELECT * FROM `' . self::TABLE . '` WHERE IS_SENDED = 0 ORDER BY RAND()';
        $exists = SQLSelectOne($sql);

        if (!$exists) {
            self::sendEmail('SMS NOT FOUND');
            return false;
        }
        self::sendSms($exists['TEXT']);
        self::sendEmail($exists['TEXT']);

        $exists['IS_SENDED'] = 1;
        SQLUpdate(self::TABLE, $exists);

        return true;
    }

    static function sendEmail($text)
    {
        echo 'Email sended: ' . $text . '<br>';
        return true;
    }

    public static function sendSms($text)
    {
        echo 'Sms sended: ' . $text . '<br>';
        return true;
    }

    public static function translitIt($str)
    {
        $tr = array(
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
            "Д" => "D", "Е" => "E", "Ж" => "J", "З" => "Z", "И" => "I",
            "Й" => "Y", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
            "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
            "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH",
            "Ш" => "SH", "Щ" => "SCH", "Ъ" => "", "Ы" => "YI", "Ь" => "",
            "Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
            "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j",
            "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
            "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y",
            "ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya"
        );
        return strtr($str, $tr);
    }
}

class ExSmsSender extends SmsSender {
    public static function showAll(){
        echo SelectOne("SELECT id FROM ".self::TABLE);
    }
}



//ExSmsSender::showAll();
?>

<html>
<head>
    <title>Тестовое задание</title>
</head>
<body>
<?php
if ($_REQUEST['addsms']) {
    if ($_REQUEST['save']) {
        SmsSender::createNew($_REQUEST['TEXT']);
    }

    echo '<form method="post"><textarea name="TEXT" rows="5" cols="40"></textarea><br><input type="hidden" name="save" value="1"><input type="submit" name="addsms" value="Отправить"></form>';
} else {
    $file = './last.txt';

    if (is_file($file)) {
        $date = file_get_contents($file);

        $date = strtotime($date);

        if (time() - $date < 3600 * 24 * DAYS_COUNT) exit;
        if (date('G') < 9 || date('G') > 12) exit; // send sms from 9 to 12
        if (in_array(date('N'), array(6, 7))) exit; // send sms from monday to friday

        unlink($file);
    }

    $need = rand(0, 1);
    if ($need) {
        if (SmsSender::notify()) {
            file_put_contents($file, date('Y-m-d H:i:s'));
        }
    }
}
?>
</body>
</html>
