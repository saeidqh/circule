<?php

namespace app\modules\workflow\models;

/*
 * @author <mortezakml@gmail.com:09366263397>
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Yii;
use app\components\Utility;
use app\components\Templates;
use app\modules\workflow\models\ProcessEntity;
use app\extensions\phpWordCando\phpWordCando;

/**
 * Description of Letter
 *
 * @author nima shokri
*/

class LetterForm extends \yii\base\Model {

    public $LetterTitle;
    public $LetterDate;
    public $LetterNumber;
    public $Description;
    public $Evidence1Date;
    public $Evidence1No;
    public $Evidence1Chek;
    public $Evidence2Date;
    public $Evidence2No;
    public $Evidence2Chek;
    public $Evidence3Date;
    public $Evidence3No;
    public $Evidence3Chek;
    public $Evidence4Date;
    public $Evidence4No;
    public $Evidence4Chek;
    public $Evidence5Date;
    public $Evidence5No;
    public $Evidence5Chek;
    public $Evidence6Date;
    public $Evidence6No;
    public $Evidence6Chek;
    public $Evidence7Date;
    public $Evidence7No;
    public $Evidence7Chek;
    public $Evidence8Date;
    public $Evidence8No;
    public $Evidence8Chek;
    public $Evidence9Date;
    public $Evidence9No;
    public $Evidence9Chek;
    public $Evidence10Date;
    public $Evidence10No;
    public $Evidence10Chek;
    public $Evidence11Date;
    public $Evidence11No;
    public $Evidence11Chek;
    public $Evidence12Date;
    public $Evidence12No;
    public $Evidence12Chek;
    public $Evidence13Date;
    public $Evidence13No;
    public $Evidence13Chek;
    public $Evidence14Date;
    public $Evidence14No;
    public $Evidence14Chek;
    public $Evidence15Date;
    public $Evidence15No;
    public $Evidence15Chek;
    public $Penalize;
    public $Penalty;
    public $Income;
    public $checkBox01;
    public $checkBox02;
    public $checkBox03;
    public $checkBox04;
    public $checkBox05;
    public $checkBox06;
    public $checkBox07;
    public $checkBox08;
    public $checkBox09;
    public $checkBox10;
    
    public $number01;
    public $number02;
    public $number03;
    public $number04;
    public $number05;
    public $number06;
    public $number07;
    public $number08;
    public $number09;
    public $number10;

    
    public $textBox01;
    public $textBox02;
    public $textBox03;
    public $textBox04;
    public $textBox05;
    public $textBox06;
    public $textBox07;
    public $textBox08;
    public $textBox09;
    public $textBox10;
    
    public $CcModirAmelalc = "مدير عامل محترم شرکت پترو سينا آريا، جناب آقاي مهندس زندی ، جهت استحضار";
    public $CcModirAmelalcAddr = "";
    public $CcRaeisKargahAddr = "";
    public $CcModirHoghooghiAddr = "";
    public $CcModirPeymankar = "مدير محترم پيمان و رسيدگي، جناب آقاي مهندس محمدي ثابت، جهت اطلاع";
    public $CcModirPeymankarAddr = "";
    public $CcModirBarnameRiziAddr = "";
    public $CcModirMohandesi = "مدير محترم مهندسي ، جناب آقاي مهندس مير جعفري ، جهت اطلاع";
    public $CcModirMohandesiAddr = "";
    public $CcMoavenMarbote = "هنوز مشخص نشده است";
    public $CcModirAmelNSR = "هنوز مشخص نشده است";
    public $CcModirHoghooghiNSR = "هنوز مشخص نشده است";
    public $CcModieMohandesiNSR = "هنوز مشخص نشده است";
    public $CcCheckModirAmelalc;
    public $CcCheckRaeisKargah;
    public $CcCheckModirHoghooghi;
    public $CcCheckModirPeymankar;
    public $CcCheckModirBarnameRizi;
    public $CcCheckModirMohandesi;
    public $CcCheckMoavenMarbote;
    public $CcModirAmelalcAddrPPI = "";
    public $CcRaeisKargahPPI = "رياست محترم کارگاه ، جناب آقاي مهندس کیا دلیری، (به همراه 2 نسخه صورت وضعيت ، جهت اطلاع و ابلاغ به پيمانکار)";
    public $CcRaeisKargahAddrPPI = "";
    public $CcModirHoghooghiPPI = "سرپرست محترم حقوقي و امور قراردادها، جناب آقاي مهندس عبادت طلب جهت اطلاع";
    public $CcModirHoghooghiAddrPPI = "";
    public $CcModirPeymankarPPI = "مدير محترم پيمان و رسيدگي، جناب آقاي مهندس صابری، جهت اطلاع";
    public $CcModirPeymankarAddrPPI = "";
    public $CcModirBarnameRiziAddrPPI = "";
    public $CcModirMohandesiPPI = "مدير محترم مهندسي ، جناب آقاي مهندس حسن طهماسبی ، جهت اطلاع";
    public $CcRaeisKargah = "رياست محترم کارگاه ، جناب آقاي مهندس شریف باوی، (به همراه 2 نسخه صورت وضعيت ، جهت اطلاع و ابلاغ به پيمانکار)";
    public $CcModirHoghooghi = "سرپرست محترم حقوقي و امور قراردادها، جناب آقاي دکتر آریان پور جهت اطلاع";
    public $CcModirBarnameRizi = "مدير محترم برنامه ريزي و کنترل پروژه کارگاه ، جناب آقاي مهندس کبیری ، جهت اطلاع";
    public $CcModirAmelalcPPI = "مدير عامل محترم شرکت پترو پایدار ایرانیان، جناب آقاي مهندس تیمور حیدری ، جهت استحضار";
    public $CcModirBarnameRiziPPI = "مدير محترم برنامه ريزي و کنترل پروژه کارگاه ، جناب آقاي مهندس نادری ، جهت اطلاع";

    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            [
                ['LetterTitle', 'LetterDate', 'LetterNumber', 'Description', 'Evidence1Date', 'Evidence1No', 'Evidence1Chek', 'Evidence2Date', 'Evidence2No', 'Evidence2Chek', 'Evidence3Date', 'Evidence3No', 'Evidence3Chek', 'Evidence4Date', 'Evidence4No', 'Evidence4Chek', 'Evidence5Date', 'Evidence5No', 'Evidence5Chek', 'Evidence6Date', 'Evidence6No', 'Evidence6Chek', 'Evidence7Date', 'Evidence7No', 'Evidence7Chek'
                    , 'Evidence8Date', 'Evidence8No', 'Evidence8Chek'
                    , 'Evidence9Date', 'Evidence9No', 'Evidence9Chek'
                    , 'Evidence10Date', 'Evidence10No', 'Evidence10Chek'
                    , 'Evidence11Date', 'Evidence11No', 'Evidence11Chek'
                    , 'Evidence12Date', 'Evidence12No', 'Evidence12Chek'
                    , 'Evidence13Date', 'Evidence13No', 'Evidence13Chek'
                    , 'Evidence14Date', 'Evidence14No', 'Evidence14Chek'
                    , 'Evidence15Date', 'Evidence15No', 'Evidence15Chek'
                    , 'Penalize', 'Income', 'Penalty'
                    
                    ,'checkBox01'
                    ,'checkBox02'
                    ,'checkBox03'
                    ,'checkBox04'
                    ,'checkBox05'
                    ,'checkBox06'
                    ,'checkBox07'
                    ,'checkBox08'
                    ,'checkBox09'
                    ,'checkBox10'
                    
                    ,'number01'
                    ,'number02'
                    ,'number03'
                    ,'number04'
                    ,'number05'
                    ,'number06'
                    ,'number07'
                    ,'number08'
                    ,'number09'
                    ,'number10'
                    
                    ,'textBox01'
                    ,'textBox02'
                    ,'textBox03'
                    ,'textBox04'
                    ,'textBox05'
                    ,'textBox06'
                    ,'textBox07'
                    ,'textBox08'
                    ,'textBox09'
                    ,'textBox10'
                    
                    ,'CcModirAmelalc', 'CcModirAmelalcAddr', 'CcRaeisKargah', 'CcRaeisKargahAddr', 'CcModirHoghooghi', 'CcModirHoghooghiAddr', 'CcModirPeymankar', 'CcModirPeymankarAddr', 'CcModirBarnameRizi', 'CcModirBarnameRiziAddr', 'CcModirMohandesi', 'CcModirMohandesiAddr', 'CcCheckModirAmelalc', 'CcCheckRaeisKargah', 'CcCheckModirHoghooghi', 'CcCheckModirPeymankar', 'CcCheckModirBarnameRizi', 'CcCheckModirMohandesi',
                    'CcMoavenMarbote','CcModirAmelNSR','CcModirHoghooghiNSR','CcModieMohandesiNSR','CcCheckMoavenMarbote']
                , 'safe'
                , 'on' => 'new'
            ]
        ];
    }

    public function attributeLabels() {
        return array(
            "LetterTitle" => "Letter Title:",
            "LetterDate" => "Letter Date:",
            "LetterNumber" => "Letter Number:",
            "Description" => "Description:",
            "Evidence1Date" => "Date...",
            "Evidence1No" => "No...",
            "Evidence1Chek" => "درخواست پیمانکار",
            "Evidence2Date" => "Date...",
            "Evidence2No" => "No...",
            "Evidence2Chek" => "نامه رئیس کارگاه",
            "Evidence3Date" => "Date...",
            "Evidence3No" => "No...",
            "Evidence3Chek" => "نامه مدیر پروژه",
            "Evidence4Date" => "Date...",
            "Evidence4No" => "No...",
            "Evidence4Chek" => "تشکیل کمیسیون پایان قرارداد",
            "Evidence5Date" => "Date...",
            "Evidence5No" => "No...",
            "Evidence5Chek" => "ارسال صورتجلسه کمیسیون",
            "Evidence6Date" => "Date...",
            "Evidence6No" => "No...",
            "Evidence6Chek" => "نامه مدیر محترم مهندسی",
            "Evidence7Date" => "Date...",
            "Evidence7No" => "No...",
            "Evidence7Chek" => "نامه مدیر عامل محترم شرکت پتروگوهرفراساحل کیش",
            "Evidence8Date" => "Date...",
            "Evidence8No" => "No...",
            "Evidence8Chek" => "مدیر کالا",
            "Evidence9Date" => "Date...",
            "Evidence9No" => "No...",
            "Evidence9Chek" => "مدیر بخش دریا",
            "Evidence10Date" => "Date...",
            "Evidence10No" => "No...",
            "Evidence10Chek" => "مدیریت بخش حفاری",
            "Evidence11Date" => "Date...",
            "Evidence11No" => "No...",
            "Evidence11Chek" => "کارشناس کنترل هزینه",
            "Evidence12Date" => "Date...",
            "Evidence12No" => "No...",
            "Evidence12Chek" => "مدیر کنترل هزینه",
            "Evidence13Date" => "Date...",
            "Evidence13No" => "No...",
            "Evidence13Chek" => "مدیر برنامه ریزی",
            "Evidence14Date" => "Date...",
            "Evidence14No" => "No...",
            "Evidence14Chek" => "مدیر مالی",
            "Evidence15Date" => "Date...",
            "Evidence15No" => "No...",
            "Evidence15Chek" => "درخواست شرکت ",
            "Penalize" => "Penalize",
            "Penalty" => "Penalty",

            "checkBox01" => "",
            "checkBox02" => "",
            "checkBox03" => "",
            "checkBox04" => "",
            "checkBox05" => "",
            "checkBox06" => "",
            "checkBox07" => "",
            "checkBox08" => "",
            "checkBox09" => "",
            "checkBox10" => "",
            
            "number01" => "",
            "number02" => "",
            "number03" => "",
            "number04" => "",
            "number05" => "",
            "number06" => "",
            "number07" => "",
            "number08" => "",
            "number09" => "",
            "number10" => "",

            "textBox01" => "",
            "textBox02" => "",
            "textBox03" => "",
            "textBox04" => "",
            "textBox05" => "",
            "textBox06" => "",
            "textBox07" => "",
            "textBox08" => "",
            "textBox09" => "",
            "textBox10" => "",
            
            
            "Income" => "Income",
            "CcModirAmelalc" => "مدیر عامل",
            "CcModirAmelalcAddr" => "CcModirAmelalcAddr",
            "CcRaeisKargah" => "رئیس کارگاه",
            "CcRaeisKargahAddr" => "CcRaeisKargahAddr",
            "CcModirHoghooghi" => "مدیر حقوقی",
            "CcModirHoghooghiAddr" => "CcModirHoghooghiAddr",
            "CcModirPeymankar" => "مدیر پیمان",
            "CcModirPeymankarAddr" => "CcModirPeymankarAddr",
            "CcModirBarnameRizi" => "مدیر برنامه ریزی",
            "CcModirBarnameRiziAddr" => "CcModirBarnameRiziAddr",
            "CcModirMohandesi" => "مدیر مهندسی",
            "CcModirMohandesiAddr" => "CcModirMohandesiAddr",
            'CcCheckModirAmelalc' => "مدیرعامل",
            'CcCheckRaeisKargah' => "رئیس کارگاه",
            'CcCheckModirHoghooghi' => "مدیر حقوقی",
            'CcCheckModirPeymankar' => "مدیر پیمان و رسیدگی",
            'CcCheckModirBarnameRizi' => "مدیر برنامه ریزی",
            'CcCheckModirMohandesi' => "مدیر مهندسی",
            
            'CcCheckTesco' => "درخواست شرکت تسکو",
            
            'CcCheckMoavenMarbote' => "معاون مربوطه",
            "CcMoavenMarbote" => "معاون مربوطه",
            "CcMoavenMarboteAddr" => "MoavenMarboteAddr",
        );
    }

    public function getLetterOptions($letterId) {
        return $this->letterOptions[$letterId];
    }

    /**
     * بر اساس این مقادیر کلید آرایه خروجی نامه متغییر است
     * @var array
     */
    private $letterOptions = [
        13 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => true, 'Evidence1' => false, 'Evidence2' => false, 'Evidence3' => false, 'Evidence4' => false, 'Evidence5' => false, 'Evidence6' => false, 'Evidence7' => false, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false, 'Evidence15' => false, 'Income' => true, 'Penalty' => true, 'cc1' => false, 'cc2' => false, 'cc3' => false, 'cc4' => false, 'cc5' => false, 'cc6' => false, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => false
                ,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false
                
            ),
        15 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => true, 'Evidence1' => true, 'Evidence2' => true, 'Evidence3' => true, 'Evidence4' => false, 'Evidence5' => false, 'Evidence6' => false, 'Evidence7' => false, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false,'Evidence15' => false, 'Income' => true, 'Penalty' => true, 'cc1' => true, 'cc2' => true, 'cc3' => true, 'cc4' => true, 'cc5' => true, 'cc6' => true, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => false
                ,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false),
        21 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => true, 'Evidence1' => false, 'Evidence2' => false, 'Evidence3' => false, 'Evidence4' => false, 'Evidence5' => false, 'Evidence6' => false, 'Evidence7' => false, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false,'Evidence15' => false, 'Income' => true, 'Penalty' => true, 'cc1' => false, 'cc2' => false, 'cc3' => false, 'cc4' => false, 'cc5' => false, 'cc6' => false, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => false
                ,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false),
        22 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => true, 'Evidence1' => true, 'Evidence2' => true, 'Evidence3' => true, 'Evidence4' => false, 'Evidence5' => false, 'Evidence6' => false, 'Evidence7' => false, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false,'Evidence15' => false, 'Income' => true, 'Penalty' => true, 'cc1' => true, 'cc2' => true, 'cc3' => true, 'cc4' => true, 'cc5' => true, 'cc6' => true, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => false,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false),
        26 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => false, 'Evidence1' => false, 'Evidence2' => false, 'Evidence3' => false, 'Evidence4' => true, 'Evidence5' => true, 'Evidence6' => false, 'Evidence7' => false, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false,'Evidence15' => false, 'Income' => false, 'Penalty' => false, 'cc1' => false, 'cc2' => false, 'cc3' => false, 'cc4' => false, 'cc5' => false, 'cc6' => false, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => false,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false),
        28 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => true, 'Evidence1' => true, 'Evidence2' => true, 'Evidence3' => true, 'Evidence4' => false, 'Evidence5' => false, 'Evidence6' => false, 'Evidence7' => false, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false,'Evidence15' => false, 'Income' => true, 'Penalty' => true, 'cc1' => false, 'cc2' => false, 'cc3' => false, 'cc4' => false, 'cc5' => false, 'cc6' => false, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => false,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false),
        31 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => false, 'Evidence1' => false, 'Evidence2' => false, 'Evidence3' => false, 'Evidence4' => false, 'Evidence5' => false, 'Evidence6' => false, 'Evidence7' => false, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false,'Evidence15' => false, 'Income' => false, 'Penalty' => false, 'cc1' => false, 'cc2' => false, 'cc3' => false, 'cc4' => false, 'cc5' => false, 'cc6' => false, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => false,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false),
        32 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => true, 'Evidence1' => false, 'Evidence2' => false, 'Evidence3' => false, 'Evidence4' => false, 'Evidence5' => false, 'Evidence6' => true, 'Evidence7' => false, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false,'Evidence15' => false, 'Income' => true, 'Penalty' => true, 'cc1' => false, 'cc2' => false, 'cc3' => false, 'cc4' => false, 'cc5' => false, 'cc6' => false, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => false,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false),
        33 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => true, 'Evidence1' => false, 'Evidence2' => false, 'Evidence3' => false, 'Evidence4' => false, 'Evidence5' => false, 'Evidence6' => false, 'Evidence7' => true, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false,'Evidence15' => false, 'Income' => true, 'Penalty' => true, 'cc1' => false, 'cc2' => false, 'cc3' => false, 'cc4' => false, 'cc5' => false, 'cc6' => false, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => false,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false),
        60 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => true, 'Evidence1' => true, 'Evidence2' => true, 'Evidence3' => true, 'Evidence4' => false, 'Evidence5' => false, 'Evidence6' => false, 'Evidence7' => false, 'Evidence8' => true, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false,'Evidence15' => false, 'Income' => true, 'Penalty' => true, 'cc1' => false, 'cc2' => false, 'cc3' => false, 'cc4' => false, 'cc5' => false, 'cc6' => false, 'cc7' => true, 'cc8' => true, 'cc9' => true, 'cc10' => true, 'cc11' => true, 'cc12' => true, 'cc13' => false,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false),
        701 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => true, 'Evidence1' => false, 'Evidence2' => false, 'Evidence3' => false, 'Evidence4' => false, 'Evidence5' => false, 'Evidence6' => false, 'Evidence7' => false, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => true,'Evidence15' => false, 'Income' => true, 'Penalty' => true, 'cc1' => false, 'cc2' => false, 'cc3' => false, 'cc4' => false, 'cc5' => false, 'cc6' => false, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => false,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false),
        703 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => true, 'Evidence1' => false, 'Evidence2' => false, 'Evidence3' => false, 'Evidence4' => false, 'Evidence5' => false, 'Evidence6' => false, 'Evidence7' => false, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false,'Evidence15' => true, 'Income' => false, 'Penalty' => false, 'cc1' => false, 'cc2' => false, 'cc3' => false, 'cc4' => false, 'cc5' => false, 'cc6' => false, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => false,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false),
        704 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => true, 'Evidence1' => false, 'Evidence2' => false, 'Evidence3' => false, 'Evidence4' => false, 'Evidence5' => false, 'Evidence6' => false, 'Evidence7' => false, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false,'Evidence15' => true, 'Income' => false, 'Penalty' => false, 'cc1' => false, 'cc2' => false, 'cc3' => false, 'cc4' => false, 'cc5' => false, 'cc6' => false, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => false,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false),
        705 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => true, 'Evidence1' => true, 'Evidence2' => true, 'Evidence3' => true, 'Evidence4' => false, 'Evidence5' => false, 'Evidence6' => false, 'Evidence7' => false, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false,'Evidence15' => false, 'Income' => true, 'Penalty' => true, 'cc1' => true, 'cc2' => false, 'cc3' => true, 'cc4' => false, 'cc5' => false, 'cc6' => true, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => true,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false),
        706 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => false, 'Evidence1' => false, 'Evidence2' => false, 'Evidence3' => false, 'Evidence4' => false, 'Evidence5' => false, 'Evidence6' => false, 'Evidence7' => false, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false,'Evidence15' => false, 'Income' => false, 'Penalty' => false, 'cc1' => false, 'cc2' => false, 'cc3' => false, 'cc4' => false, 'cc5' => false, 'cc6' => false, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => false
                ,'checkBox01'=>true,'checkBox02'=>true,'checkBox03'=>true,'checkBox04'=>true,'checkBox05'=>true,'checkBox06'=>true,'checkBox07'=>true,'checkBox08'=>true,'checkBox09'=>true,'checkBox10'=>false
                ,'textBox01'=>true,'textBox02'=>true,'textBox03'=>true,'textBox04'=>true,'textBox05'=>true,'textBox06'=>true,'textBox07'=>true,'textBox08'=>true,'textBox09'=>true,'textBox10'=>false
            ),
        707 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => false, 'Evidence1' => false, 'Evidence2' => false, 'Evidence3' => false, 'Evidence4' => true, 'Evidence5' => true, 'Evidence6' => false, 'Evidence7' => false, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false,'Evidence15' => false, 'Income' => false, 'Penalty' => false, 'cc1' => false, 'cc2' => false, 'cc3' => false, 'cc4' => false, 'cc5' => false, 'cc6' => false, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => false,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false),
        1201 => array('LetterTitle' => true, 'LetterDate' => true, 'LetterNumber' => true, 'Description' => false, 'Evidence1' => false, 'Evidence2' => false, 'Evidence3' => false, 'Evidence4' => false, 'Evidence5' => false, 'Evidence6' => false, 'Evidence7' => false, 'Evidence8' => false, 'Evidence9' => false, 'Evidence10' => false, 'Evidence11' => false, 'Evidence12' => false, 'Evidence13' => false, 'Evidence14' => false,'Evidence15' => false, 'Income' => false, 'Penalty' => false, 'cc1' => false, 'cc2' => false, 'cc3' => false, 'cc4' => false, 'cc5' => false, 'cc6' => false, 'cc7' => false, 'cc8' => false, 'cc9' => false, 'cc10' => false, 'cc11' => false, 'cc12' => false, 'cc13' => false,'checkBox01'=>false,'checkBox02'=>false,'checkBox03'=>false,'checkBox04'=>false,'checkBox05'=>false,'checkBox06'=>false,'checkBox07'=>false,'checkBox08'=>false,'checkBox09'=>false,'checkBox10'=>false),
    ];

    public static function evidenceActionId($pid, $evidenceName) {
        switch ($evidenceName) {
            case 'Evidence1':

                break;
            case 'Evidence2':
                if ($pid == 1)
                    return '16';
                if ($pid == 8)
                    return '210';
                if ($pid == 10)
                    return '239';
                if ($pid == 13)
                    return '355';
            case 'Evidence3':
//                if($pid = 10) return '108';
//                if($pid = 11) return '108';
//                if($pid = 12) return '108';
            case 'Evidence4':
//                if($pid = 10) return '108';
//                if($pid = 11) return '108';
//                if($pid = 12) return '108';
            case 'Evidence5':
//                if($pid = 10) return '108';
//                if($pid = 11) return '108';
//                if($pid = 12) return '108';
            case 'Evidence6':
                if ($pid == 4)
                    return '108';

            case 'Evidence7':
                if ($pid == 21)
                    return '449';
            case 'Evidence8':
            case 'Evidence9':
            case 'Evidence10':
            case 'Evidence11':
            case 'Evidence12':
            case 'Evidence13':
            case 'Evidence14':
            case 'Evidence15':
                if ($pid == 60)
                    return '808';
            default:
                return null;
        }
    }

    Public function fillEvidence($fileId, $processEntity) {
        $options = $this->letterOptions[$fileId];
        foreach ($options as $k => $v) {
            if ($v) {
                if (substr($k, 0, 8) == 'Evidence') {
                    $evidenceActioId = $this->evidenceActionId($processEntity->ProcessIDRef, $k);
                    $data = \app\modules\workflow\models\ActionEntity::find()
                                    ->where([
                                        'ProcessEntityIDRef' => $processEntity->ProcessEntityID,
                                        'ActionIDRef' => $evidenceActioId
                                    ])->one();
                    if ($data) {
                        $ActionDateHijri = explode('-', $data->ActionDateHijri);
                        $ActionDateHijri = $ActionDateHijri[0];
                        switch ($k) {
                            case 'Evidence1':
                                $this->Evidence1No = $data->LetterNumber;
                                $this->Evidence1Date = $ActionDateHijri;
                                break;
                            case 'Evidence2':
                                $this->Evidence2No = $data->LetterNumber;
                                $this->Evidence2Date = $ActionDateHijri;
                                break;
                            case 'Evidence3':
                                $this->Evidence3No = $data->LetterNumber;
                                $this->Evidence3Date = $ActionDateHijri;
                                break;
                            case 'Evidence4':
                                $this->Evidence4No = $data->LetterNumber;
                                $this->Evidence4Date = $ActionDateHijri;
                                break;
                            case 'Evidence5':
                                $this->Evidence5No = $data->LetterNumber;
                                $this->Evidence5Date = $ActionDateHijri;
                                break;
                            case 'Evidence6':
                                $this->Evidence6No = $data->LetterNumber;
                                $this->Evidence6Date = $ActionDateHijri;
                                break;
                            case 'Evidence7':
                                $this->Evidence7No = $data->LetterNumber;
                                $this->Evidence7Date = $ActionDateHijri;
                                break;
                            case 'Evidence8':
                                $this->Evidence8No = $data->LetterNumber;
                                $this->Evidence8Date = $ActionDateHijri;
                                break;
                            case 'Evidence9':
                                $this->Evidence9No = $data->LetterNumber;
                                $this->Evidence9Date = $ActionDateHijri;
                                break;
                            case 'Evidence10':
                                $this->Evidence10No = $data->LetterNumber;
                                $this->Evidence10Date = $ActionDateHijri;
                                break;
                            case 'Evidence11':
                                $this->Evidence11No = $data->LetterNumber;
                                $this->Evidence11Date = $ActionDateHijri;
                                break;
                            case 'Evidence12':
                                $this->Evidence12No = $data->LetterNumber;
                                $this->Evidence12Date = $ActionDateHijri;
                                break;
                            case 'Evidence13':
                                $this->Evidence13No = $data->LetterNumber;
                                $this->Evidence13Date = $ActionDateHijri;
                                break;
                            case 'Evidence14':          // Add
                                $this->Evidence14No = $data->LetterNumber;
                                $this->Evidence14Date = $ActionDateHijri;
                                break;
                            case 'Evidence15':          // Add
                                $this->Evidence15No = $data->LetterNumber;
                                $this->Evidence15Date = $ActionDateHijri;
                                break;
                        }
                    }
                }
            }
        }
    }

}
