<?php
echo '11111111<br>';
if (isDomainAvailible('http://www.ruseller.com')){
	echo "Работает и готов отвечать на запросы!";
} else {
    echo "Ой, сайт не доступен.";
}

//Возвращает true, если домен доступен
function isDomainAvailible($domain){
    //Проверка на правильность URL 
    if(!filter_var($domain, FILTER_VALIDATE_URL)){
        return false;
    }

    //Инициализация curl
    $curlInit = curl_init($domain);
    curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
    curl_setopt($curlInit,CURLOPT_HEADER,true);
    curl_setopt($curlInit,CURLOPT_NOBODY,true);
    curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);
    
    //Получаем ответ
    $response = curl_exec($curlInit);
    curl_close($curlInit);
    if ($response) return true;

    return false;
}
echo '2222<br>';
?>

