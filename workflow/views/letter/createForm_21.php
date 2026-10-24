<?php


use app\components\Utility;
use yii\helpers\Html;

$currency1PersianTitle = $processEntityModel->permission->currency1->CurrencyTitle;
$currency2PersianTitle = (isset($processEntityModel->permission->currency2->CurrencyTitle) ? $processEntityModel->permission->currency2->CurrencyTitle : '');
$jDate = new app\components\jDateTime(true, true, 'Asia/Tehran');
$dateNow = $jDate->date("Y/m/d", false, false);

?>

<!DOCTYPE html>
<script type="text/javascript">
    $(document).ready(function () {

        $(document).keydown(function (event) {
            if (event.which == 123 || event.which == 81 || event.which == 83) {
                event.preventDefault();
            }
        });
    });
    
    function cancelCrateForm() {
        $(".no-print").css("display", "none");
        return false;
    }

</script>
<html lang=en>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <head>
        <meta charset=utf-8 />
                <!--// Style CheckBox-->
        <style>
            /* The container */
            .container {
              display: block;
              position: relative;
              padding-left: 35px;
              margin-bottom: 12px;
              cursor: pointer;
              font-size: 22px;
              -webkit-user-select: none;
              -moz-user-select: none;
              -ms-user-select: none;
              user-select: none;
            }

            /* Hide the browser's default checkbox */
            .container input {
              position: absolute;
              opacity: 0;
              cursor: pointer;
              height: 0;
              width: 0;
            }

            /* Create a custom checkbox */
            .checkmark {
              position: absolute;
              top: 0;
              left: 0;
              height: 15px;
              width: 15px;
              background-color: #0b436f;
            }

            /* On mouse-over, add a grey background color */
            .container:hover input ~ .checkmark {
              background-color: #ccc;
            }

            /* When the checkbox is checked, add a blue background */
            .container input:checked ~ .checkmark {
              background-color: #0b436f;
            }

            /* Create the checkmark/indicator (hidden when not checked) */
            .checkmark:after {
              content: "";
              position: absolute;
              display: none;
            }

            /* Show the checkmark when checked */
            .container input:checked ~ .checkmark:after {
              display: block;
            }

            /* Style the checkmark/indicator */
            .container .checkmark:after {
                left: 4px;
                top: 2px;
                width: 4px;
                height: 6px;
/*              left: 9px;
              top: 5px;
              width: 5px;
              height: 10px;*/
              border: solid white;
              border-width: 0 3px 3px 0;
              -webkit-transform: rotate(45deg);
              -ms-transform: rotate(45deg);
              transform: rotate(45deg);
            }
        </style>
        <style>
            .divTable {
                display: table;
                width: 100%;
            }

            .divTableRow {
                display: table-row;
            }
            td.image-right {
                width: 77px;
                height: 77px;
                background-size: 96px 96px;
                background-repeat: no-repeat;
                background-position: right top;
            }
            td.image-left {
                width: 77px;
                height: 77px;
                background-size: 96px 96px;
                background-repeat: no-repeat;
                background-position:left top;
            }
            td.image-center {
                width: 77px;
                height: 77px;
                background-size: 96px 96px;
                background-repeat: no-repeat;
                background-position: center top;
            }
            td.bottom {
                vertical-align: bottom;
            }
        </style>

        <style type="text/css">

            .rotateForm {
                -webkit-transform: rotate(-90deg);
                float: left;
            }

            td{
                word-wrap: break-word;
            }

            body{margin:0;padding:0;}
            *{
                -webkit-print-color-adjust: exact;
                font-size:14px;
            }

            table tbody tr td{line-height: 1;font-size: 14px;}
            span{display: block;}
            .backgroundcolor1{background-color: #f1eeef;}
            .backgroundcolor2{background-color: #f9f5f4;}
            
            .font-size-12{font-size: 12px;}
            .font-size-10{font-size: 10px;}
            .font-size-11{font-size: 11px;}
            .font-size-8{font-size: 8px;}
            .font-bnazanin{font-family: 'B Nazanin','B Mitra','Tahoma'; font-size:14px;}
            .font-bmitra{font-family:'B Mitra','Tahoma'; font-size:14px;}
            .font-calibri{font-family: 'Calibri';}
            .font-tahoma{font-family: Tahoma;}
            .font-cambria{font-family: Cambria;}
            .font-bold{font-weight:bold;}
            .font-inherit{font-weight:inherit;}
            .font-italic{font-style:italic;}
            .font-size-10{font-size:10px;}
            .font-size-12{font-size:12px;}
            .font-size-14{font-size:14px;}
            .p5{padding:5px;}
            .pr5{padding-right:5px;}
            .pl5{padding-left:5px;}

            .pr25{padding-right:25px !important;}
            .pl25{padding-left:25px !important;}

            .mt10{margin-top:10px;}

            .main-print-box{
                margin:0;
                padding:0;
                height:895px;
                width:680px;
            }
            .dir-rtl{direction:rtl}
            .dir-ltr{direction:ltr}

            .text-center{text-align:center}
            .text-right{text-align:right}
            .text-left{text-align:left}
            .border-trbl{border:solid 1px black;}
            .border-t{border-top:solid 1px black;}
            .border-r{border-right:solid 1px black;}
            .border-b{border-bottom:solid 1px black;}
            .border-l{border-left:solid 1px black;}
            .border-tb{border-bottom:solid 1px black;border-top:solid 1px black;}
            .border-lr{border-left:solid 1px black;border-right:solid 1px black;}
            .border-rbl{border-left:solid 1px black;border-right:solid 1px black;border-bottom:solid 1px black;}
            .border-trl{border-left:solid 1px black;border-right:solid 1px black;border-top:solid 1px black;}
            .border-tbl{border-left:solid 1px black;border-right:solid 1px black;border-top:solid 1px black;}

            .table-header-bg{background-color: #eeece1;}

            .blue-main-head{background-color: #95b3d7;}
            .blue-light-main-head{background-color: #cad8e6;;}
            .blue-sub-head{background-color: #95b3d7;}
            .blue-sub-content{background-color: #dbe5f1;}

            .green-main-head{background-color: #c2d69a;}
            .green-sub-head{background-color: #c2d69a;}
            .green-sub-content{background-color: #eaf1dd;}

            .lblue-main-head{background-color: #93cddd;}
            .lblue-sub-head{background-color: #93cddd;}
            .lblue-sub-content{background-color: #dbeef3;}

            .olive-main-head{background-color: #948b54;}
            .olive-sub-head{background-color: #c5be97;}
            .olive-sub-content{background-color: #eeece1;}
            .olive-check{background-color: #73c153;}

            .sign-box{display:block; z-index: 1;margin-bottom:-25px; }
            .sign-box img { width: 70px; height:55px; z-index: 1;}

            .signer-name{z-index: 10;margin-top:10px;margin-bottom:5px;}
            .logo-psa img{width:78px;height:60px;}
            .logo-psa-admin img{width:78px;}
            .logo-cando img{width:98px;height:20px;}

            .fake-lock{
                position:fixed; 
                top:0px; 
                right:0;
                bottom: 0;
                left:0;
                z-index: 100;
            }

        </style>

    </head>

    <body>
        <div class='main-print-box font-tahoma' dir="rtl" >
            <table class="text-center" style="width: 95%;margin-right: 12px;;table-layout: fixed;" cellspacing="0" width="595px"  heigh="100%">

                <colgroup>
                    <col style='width:13px;'/>  <!--Line (1)-->
                    <col style='width:13px;'/>  <!--Line (2)-->
                    <col style='width:13px;'/>  <!--Line (3)-->
                    <col style='width:13px;'/>  <!--Line (4)-->
                    <col style='width:13px;'/>  <!--Line (5)-->
                    <col style='width:13px;'/>  <!--Line (6)-->
                    <col style='width:13px;'/>  <!--Line (7)-->
                    <col style='width:13px;'/>  <!--Line (8)-->
                    <col style='width:13px;'/>  <!--Line (9)-->
                    <col style='width:13px;'/>  <!--Line (10)-->
                    <col style='width:13px;'/>  <!--Line (11)-->
                    <col style='width:13px;'/>  <!--Line (12)-->
                    <col style='width:13px;'/>  <!--Line (13)-->
                    <col style='width:13px;'/>  <!--Line (14)-->
                    <col style='width:13px;'/>  <!--Line (15)-->
                    <col style='width:13px;'/>  <!--Line (16)-->
                    <col style='width:13px;'/>  <!--Line (17)-->
                    <col style='width:13px;'/>  <!--Line (18)-->
                    <col style='width:13px;'/>  <!--Line (19)-->
                    <col style='width:13px;'/>  <!--Line (20)-->
                    <col style='width:13px;'/>  <!--Line (21)-->
                    <col style='width:13px;'/>  <!--Line (22)-->
                    <col style='width:13px;'/>  <!--Line (23)-->
                    <col style='width:13px;'/>  <!--Line (24)-->
                    <col style='width:13px;'/>  <!--Line (25)-->
                    <col style='width:13px;'/>  <!--Line (26)-->
                    <col style='width:13px;'/>  <!--Line (27)-->
                    <col style='width:13px;'/>  <!--Line (28)-->
                    <col style='width:13px;'/>  <!--Line (29)-->
                    <col style='width:13px;'/>  <!--Line (30)-->
                    <col style='width:13px;'/>  <!--Line (31)-->
                    <col style='width:13px;'/>  <!--Line (32)-->
                    <col style='width:13px;'/>  <!--Line (33)-->
                    <col style='width:13px;'/>  <!--Line (34)-->
                    <col style='width:13px;'/>  <!--Line (35)-->
                    <col style='width:13px;'/>  <!--Line (36)-->
                    <col style='width:13px;'/>  <!--Line (37)-->
                    <col style='width:13px;'/>  <!--Line (38)-->
                    <col style='width:13px;'/>  <!--Line (39)-->
                    <col style='width:13px;'/>  <!--Line (40)-->
                    <col style='width:13px;'/>  <!--Line (41)-->
                    <col style='width:13px;'/>  <!--Line (42)-->
                    <col style='width:13px;'/>  <!--Line (43)-->
                    <col style='width:13px;'/>  <!--Line (44)-->
                    <col style='width:13px;'/>  <!--Line (45)-->
                    <col style='width:13px;'/>  <!--Line (46)-->
                    <col style='width:13px;'/>  <!--Line (47)-->
                    <col style='width:13px;'/>  <!--Line (48)-->
                    <col style='width:13px;'/>  <!--Line (49)-->
                    <col style='width:13px;'/>  <!--Line (50)-->
                </colgroup>

                <thead>
                    <tr>
                        <th colspan="45" style="height:73px;" >
                            <span style="margin-left: -61px;">بسمه تعالی</span>
                            <span style="text-align: left;margin-bottom: 0px;margin-left: 26px;font-family: serif;">شماره :</span>
                            <span style="text-align: left;margin-bottom: 0px;margin-left: 26px;font-family: serif;">تاریخ:</span>
                            <span style="text-align: left;margin-bottom: 0px;margin-left: 26px;font-family: serif;">پیوست:</span>
                            <!--<img src="<?= $image_header ?>" style="width:683px;margin-top: -35px;" />-->
                        </th>
                        <th colspan="5" style="" >
                            <!--<span>dddd</span>-->
                            <span id="lbl-letter-no" style="height: 12px;position: absolute;margin-top: -15px;margin-right: -20px;font-family: -webkit-body;border-bottom: dotted;border-bottom-width: thin;"><?= $processEntityModel->permission->id ?></span>
                            <span id="lbl-letter-date" style="height: 12px;position: absolute;margin-top: -0px;margin-right: -20px;font-family: -webkit-body;border-bottom: dotted;border-bottom-width: thin;"><?= $dateNow ?></span>
                            <span style="height: 12px;position: absolute;margin-top: 15px;margin-right: -20px;border-bottom: dotted;border-bottom-width: thin;">دارد</span>
                        </th>
                    </tr>
                    <tr class="" style="width:100%">
                        <th rowspan="3" colspan="8" class="border-t border-lr"  style="line-height: 4px">

                            <span class='logo-psa-admin' ><img src="<?= $logo_admin; ?>" style="" /></span>

                <p style="font-size: 10px;margin-bottom: -11px;margin-top: 27px;">
<?= "REV. No:03 Class:0" ?>
                </p>
                &nbsp;
                </th>
                <th class="border-t border-lr  " colspan="34" style="line-height: 22px">
                <p style="font-size: 10px;margin-bottom: -2px;margin-top: -7px;"><?= 'Integrated Management system'; ?></p>
                <p style="font-size: 16px;margin-bottom: -42px;margin-top: -2px;"><?= 'PERMISSION'; ?></p>
                &nbsp;</p>
                </th>
                <th rowspan="3"  colspan="8" class="border-t border-lr font-calibri font-italic font-bold font-size-10" width='15%'  style="line-height: 12px">
                    <span class='logo-psa' style="height: 86px;"><img src="<?= $logo; ?>" style="width: 78px;height: 98px;" /></span>
<!--                <p style="font-size: 6px;margin-bottom: -26px"><?= 'P.K.S.K' ?></p>-->
                &nbsp;</th>
                </tr>
                <tr class="font-size-10"  style="line-height: 10px">
                    <th class="border-t border-lr font-inherit font-size-10" colspan="7"><?= 'Serial' ?>&nbsp;</th>
                    <th class="border-t border-l font-inherit font-size-10" colspan="7"><?= 'Doc Type' ?>&nbsp;</th>
                    <th class="border-t border-l font-inherit font-size-10" colspan="6" ><?= 'Descipline' ?>&nbsp;</th>
                    <th class="border-t border-l font-inherit font-size-10" colspan="7"><?= 'Field Code' ?>&nbsp;</th>
                    <th class="border-t border-l font-inherit font-size-10" colspan="7"><?= 'Company' ?>&nbsp;</th>
                </tr>
                <tr class="font-size-10"  style="line-height: 10px">
                    <th class="border-t border-lr font-inherit font-size-10" colspan="7" style="font-family: initial;font-weight: bold;"><span>514</span></th>
                    <th class="border-t border-l font-inherit font-size-10" colspan="7" style="font-family: initial;font-weight: bold;"><span>FR</span></th>
                    <th class="border-t border-l font-inherit font-size-10" colspan="6" style="font-family: initial;font-weight: bold;"><span>EX</span></th>
                    <th class="border-t border-l font-inherit font-size-10" colspan="7" style="font-family: initial;font-weight: bold;"><span>000</span></th>
                    <th class="border-t border-l font-inherit font-size-10 font-cambria" colspan="7" style="font-family: initial;font-weight: bold;"><span>PKSK</span></th>
                </tr>


                </thead>
                <tbody>
                    <tr class="font-size-12 border-t font-cambria"  style="height: 25px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="border-t font-inherit font-size-12 text-left" colspan="25"><?= Html::encode($processEntityModel->permission->contract->ContractNo); ?>&nbsp;</td>
                        <td class="border-t font-inherit font-size-12 text-left" colspan="23" ><?= 'Contract / PO No' ?>&nbsp;</td>
                        <td class="border-t font-inherit font-size-12 text-right" colspan="2"><?= '.1' ?>&nbsp;</td>
                    </tr>

                    <tr class="font-size-12 font-cambria backgroundcolor1"  style="height: 25px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="25"><?= Html::encode($processEntityModel->permission->contract->subContractor->PersonTitle); ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="23" ><?= 'Subcontractor / vendor Name' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right font-cambria" colspan="2"><?= '.2' ?>&nbsp;</td>
                    </tr>

                    <tr class="font-size-12 font-cambria"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="25"><?= Html::encode($processEntityModel->permission->contract->ContractSubject); ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="23" ><?= 'Contract Subject' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right font-cambria" colspan="2"><?= '.3' ?>&nbsp;</td>
                    </tr>

                    <tr class="font-size-12 font-cambria backgroundcolor1"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="25"><?= Html::encode($processEntityModel->permission->commericalNo); ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="23" ><?= 'Commerical No' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right font-cambria" colspan="2"><?= '.4' ?>&nbsp;</td>
                    </tr>

                    <tr class="font-size-12 font-cambria"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="25"><?= '.'; ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="23" ><?= 'Shipment Part' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right font-cambria" colspan="2"><?= '.5' ?>&nbsp;</td>
                    </tr>

                    <tr class="font-size-12 font-cambria backgroundcolor1"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="25"><?= Html::encode($processEntityModel->permission->title); ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="23" ><?= 'Permission Title' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right font-cambria" colspan="2"><?= '.6' ?>&nbsp;</td>
                    </tr>

                    <tr class="font-size-12 font-cambria"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="25"><?= Html::encode($processEntityModel->permission->type->title); ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="23" ><?= 'Permission Type' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right font-cambria" colspan="2"><?= '.7' ?>&nbsp;</td>
                    </tr>

                    <tr class="font-size-12 font-cambria backgroundcolor1"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="25"><?= Html::encode($processEntityModel->permission->cutOffDateHijri); ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="23" ><?= 'Cut off Date' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right font-cambria" colspan="2"><?= '.8' ?>&nbsp;</td>
                    </tr>
                    

                    <tr class="font-size-12 font-cambria"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="25"><?= '.'; ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="23" ><?= 'Packing List No' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right font-cambria" colspan="2"><?= '.9' ?>&nbsp;</td>
                    </tr>


                    <tr class="font-size-12 font-cambria backgroundcolor1"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="12"><?= Html::encode($currency1PersianTitle); ?></td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="13"><?= Utility::moneyFormatByCurrency($processEntityModel->permission->contract->lastRider->Price1, $currency1PersianTitle); ?></td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="23" rowspan="2"><?= 'Contract / Po Price' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right font-cambria" colspan="2" rowspan="2"><?= '.10' ?>&nbsp;</td>
                    </tr>
                    <tr class="font-size-12  backgroundcolor1"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="12"><?= Html::encode($currency2PersianTitle); ?></td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="13"><?= Utility::moneyFormatByCurrency($processEntityModel->permission->contract->lastRider->Price2, $currency2PersianTitle); ?></td>
                    </tr>

                    <tr class="font-size-12"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="12"><?= Html::encode($currency1PersianTitle); ?></td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="13"><?= Utility::moneyFormatByCurrency($processEntityModel->permission->approved1, $currency1PersianTitle); ?></td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="23" rowspan="2"><?= 'Periodic Gross Value' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right font-cambria" colspan="2" rowspan="2"><?= '.11' ?>&nbsp;</td>
                    </tr>
                    <tr class="font-size-12"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="12"><?= Html::encode($currency2PersianTitle); ?></td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="13"><?= Utility::moneyFormatByCurrency($processEntityModel->permission->approved2, $currency2PersianTitle); ?></td>
                    </tr>

                    <tr class="font-size-12 backgroundcolor1"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="12"><?= Html::encode($currency1PersianTitle); ?></td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="13"><?= Utility::moneyFormatByCurrency($processEntityModel->permission->cumulativeApproved1(6), $currency1PersianTitle); ?></td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="23" rowspan="2"><?= 'Cumulative Gross Value' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right font-cambria" colspan="2" rowspan="2"><?= '.12' ?>&nbsp;</td>
                    </tr>
                    
                    <tr class="font-size-12 backgroundcolor1"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="12"><?= Html::encode($currency2PersianTitle); ?></td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="13"><?= Utility::moneyFormatByCurrency($processEntityModel->permission->cumulativeApproved2(6), $currency2PersianTitle); ?></td>
                    </tr>
                    
                    <tr class="font-size-12 font-cambria"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="25"><?= Html::encode($processEntityModel->permission->description); ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-left font-cambria" colspan="23" ><?= 'Description' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right font-cambria" colspan="2"><?= '.13' ?>&nbsp;</td>
                    </tr>

                    <tr class="font-size-12 dir-ltr"  style="height: 40px;text-align: left;">
                        <td class="font-inherit font-bold font-cambria text-left" colspan="50"><span style="font-size: 18px;margin-left: 11px;" id="total-number">Refer to receipt of following documents:(<?php echo intval($docPageCount[1])+intval($docPageCount[2])+intval($docPageCount[3])+intval($docPageCount[4])+intval($docPageCount[5])+intval($docPageCount[6])+intval($docPageCount[7])+intval($docPageCount[8])+intval($docPageCount[9])+intval($docPageCount[10])+intval($docPageCount[11])?> Pages)</span></td>
                    </tr>

                    <!--Check Box-->
                    <!--Part 01-->
                    <tr class="font-size-12 dir-ltr"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left" colspan="25"><span id="text-input01"><?= (($countDocOriginal[1]==0)?"":$countDocOriginal[1].' Sets Original').(($countDocOriginal[1]>0 && $countDocCopy[1]>0)?"-":'').(($countDocCopy[1]==0)?"":$countDocCopy[1].' Sets Copy').($docPageCount[1]==0?"":" (each set $docPageCount[1] Pages)") ?></span></td>
                        <td class="font-inherit font-size-12 text-left" colspan="21" ><?= 'Commercial Invoice' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right" colspan="4">
                            <label class="container">
                                <input type="checkbox" disabled="disabled" <?= ($countDocOriginalFile[1]==0)?"":'checked="checked"'; ?>>
                                <span class="checkmark" style="left: 18px;"></span>
                            </label>
                        </td>
                    </tr>
                    <!--Part 02-->
                    <tr class="font-size-12 dir-ltr backgroundcolor2"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left" colspan="25"><span id="text-input02"><?= (($countDocOriginal[2]==0)?"":$countDocOriginal[2].' Sets Original').(($countDocOriginal[2]>0 && $countDocCopy[2]>0)?"-":'').(($countDocCopy[2]==0)?"":$countDocCopy[2].' Sets Copy').($docPageCount[2]==0?"":" (each set $docPageCount[2] Pages)") ?></span></td>
                        <td class="font-inherit font-size-12 text-left" colspan="21" ><?= 'Inspection Certificate (IC)' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right" colspan="4">
                            <label class="container">
                                <input type="checkbox" disabled="disabled" <?= ($countDocOriginalFile[2]==0)?"":'checked="checked"'; ?>>
                                <span class="checkmark" style="left: 18px;"></span>
                            </label>
                        </td>
                    </tr>
                    <!--Part 03-->
                    <tr class="font-size-12 dir-ltr"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left" colspan="25"><span id="text-input03"><?= (($countDocOriginal[3]==0)?"":$countDocOriginal[3].' Sets Original').(($countDocOriginal[3]>0 && $countDocCopy[3]>0)?"-":'').(($countDocCopy[3]==0)?"":$countDocCopy[3].' Sets Copy').($docPageCount[3]==0?"":" (each set $docPageCount[3] Pages)") ?></span></td>
                        <td class="font-inherit font-size-12 text-left" colspan="21" ><?= 'Inspection Release Note (IRN)' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right" colspan="4">
                            <label class="container">
                                <input type="checkbox" disabled="disabled" <?= ($countDocOriginalFile[3]==0)?"":'checked="checked"'; ?>>
                                <span class="checkmark" style="left: 18px;"></span>
                            </label>
                        </td>
                    </tr>
                    <!--Part 04-->
                    <tr class="font-size-12 dir-ltr backgroundcolor2"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left" colspan="25"><span id="text-input04"><?= (($countDocOriginal[4]==0)?"":$countDocOriginal[4].' Sets Original').(($countDocOriginal[4]>0 && $countDocCopy[4]>0)?"-":'').(($countDocCopy[4]==0)?"":$countDocCopy[4].' Sets Copy').($docPageCount[4]==0?"":" (each set $docPageCount[4] Pages)") ?></span></td>
                        <td class="font-inherit font-size-12 text-left" colspan="21" ><?= 'Bill of lading (B/L)' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right" colspan="4">
                            <label class="container">
                                <input type="checkbox" disabled="disabled" <?= ($countDocOriginalFile[4]==0)?"":'checked="checked"'; ?>>
                                <span class="checkmark" style="left: 18px;"></span>
                            </label>
                        </td>
                    </tr>
                    <!--Part 05-->
                    <tr class="font-size-12 dir-ltr"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left" colspan="25"><span id="text-input05"><?= (($countDocOriginal[5]==0)?"":$countDocOriginal[5].' Sets Original').(($countDocOriginal[5]>0 && $countDocCopy[5]>0)?"-":'').(($countDocCopy[5]==0)?"":$countDocCopy[5].' Sets Copy').($docPageCount[5]==0?"":" (each set $docPageCount[5] Pages)") ?></span></td>
                        <td class="font-inherit font-size-12 text-left" colspan="21" ><?= 'Packing list (P/L)' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right" colspan="4">
                            <label class="container">
                                <input type="checkbox" disabled="disabled" <?= ($countDocOriginalFile[5]==0)?"":'checked="checked"'; ?>>
                                <span class="checkmark" style="left: 18px;"></span>
                            </label>
                        </td>
                    </tr>
                    <!--Part 12-->
                    <tr class="font-size-12 dir-ltr"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left" colspan="25"><span id="text-input09"><?= (($countDocOriginal[12]==0)?"":$countDocOriginal[12].' Sets Original').(($countDocOriginal[12]>0 && $countDocCopy[12]>0)?"-":'').(($countDocCopy[12]==0)?"":$countDocCopy[12].' Sets Copy').($docPageCount[12]==0?"":" (each set $docPageCount[12] Pages)") ?></span></td>
                        <td class="font-inherit font-size-12 text-left" colspan="21" ><?= "MSR" ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right" colspan="4">
                            <label class="container">
                                <input type="checkbox" disabled="disabled" <?= ($countDocOriginalFile[12]==0)?"":'checked="checked"'; ?>>
                                <span class="checkmark" style="left: 18px;"></span>
                            </label>
                        </td>
                    </tr>
                    
                    <!--Part 06-->
                    <tr class="font-size-12 dir-ltr backgroundcolor2"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left" colspan="25"><span id="text-input06"><?= (($countDocOriginal[6]==0)?"":$countDocOriginal[6].' Sets Original').(($countDocOriginal[6]>0 && $countDocCopy[6]>0)?"-":'').(($countDocCopy[6]==0)?"":$countDocCopy[6].' Sets Copy').($docPageCount[6]==0?"":" (each set $docPageCount[6] Pages)") ?></span></td>
                        <td class="font-inherit font-size-12 text-left" colspan="21" ><?= 'Certificate of Origin (COO)' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right" colspan="4">
                            <label class="container">
                                <input type="checkbox" disabled="disabled" <?= ($countDocOriginalFile[6]==0)?"":'checked="checked"'; ?>>
                                <span class="checkmark" style="left: 18px;"></span>
                            </label>
                        </td>
                    </tr>
                    <!--Part 07-->
                    <tr class="font-size-12 dir-ltr"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left" colspan="25"><span id="text-input07"><?= (($countDocOriginal[7]==0)?"":$countDocOriginal[7].' Sets Original').(($countDocOriginal[7]>0 && $countDocCopy[7]>0)?"-":'').(($countDocCopy[7]==0)?"":$countDocCopy[7].' Sets Copy').($docPageCount[7]==0?"":" (each set $docPageCount[7] Pages)") ?></span></td>
                        <td class="font-inherit font-size-12 text-left" colspan="21" ><?= 'FDS' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right" colspan="4">
                            <label class="container">
                                <input type="checkbox" disabled="disabled" <?= ($countDocOriginalFile[7]==0)?"":'checked="checked"'; ?>>
                                <span class="checkmark" style="left: 18px;"></span>
                            </label>
                        </td>
                    </tr>
                    <!--Part 08-->
                    <tr class="font-size-12 dir-ltr backgroundcolor2"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left" colspan="25"><span id="text-input08"><?= (($countDocOriginal[8]==0)?"":$countDocOriginal[8].' Sets Original').(($countDocOriginal[8]>0 && $countDocCopy[8]>0)?"-":'').(($countDocCopy[8]==0)?"":$countDocCopy[8].' Sets Copy').($docPageCount[8]==0?"":" (each set $docPageCount[8] Pages)") ?></span></td>
                        <td class="font-inherit font-size-12 text-left" colspan="21" ><?= 'MOM' ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right" colspan="4">
                            <label class="container">
                                <input type="checkbox" disabled="disabled" <?= ($countDocOriginalFile[8]==0)?"":'checked="checked"'; ?>>
                                <span class="checkmark" style="left: 18px;"></span>
                            </label>
                        </td>
                    </tr>
                    <!--Part 09-->
                    <tr class="font-size-12 dir-ltr"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left" colspan="25"><span id="text-input09"><?= (($countDocOriginal[9]==0)?"":$countDocOriginal[9].' Sets Original').(($countDocOriginal[9]>0 && $countDocCopy[9]>0)?"-":'').(($countDocCopy[9]==0)?"":$countDocCopy[9].' Sets Copy').($docPageCount[9]==0?"":" (each set $docPageCount[9] Pages)") ?></span></td>
                        <td class="font-inherit font-size-12 text-left" colspan="21" ><?= "Other($otherDescription)" ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right" colspan="4">
                            <label class="container">
                                <input type="checkbox" disabled="disabled" <?= ($countDocOriginalFile[9]==0)?"":'checked="checked"'; ?>>
                                <span class="checkmark" style="left: 18px;"></span>
                            </label>
                        </td>
                    </tr>
                    <!--Part 10-->
                    <tr class="font-size-12 dir-ltr"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left" colspan="25"><span id="text-input09"><?= (($countDocOriginal[10]==0)?"":$countDocOriginal[10].' Sets Original').(($countDocOriginal[10]>0 && $countDocCopy[10]>0)?"-":'').(($countDocCopy[10]==0)?"":$countDocCopy[10].' Sets Copy').($docPageCount[10]==0?"":" (each set $docPageCount[10] Pages)") ?></span></td>
                        <td class="font-inherit font-size-12 text-left" colspan="21" ><?= "APG (Approved Legal/Financial)" ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right" colspan="4">
                            <label class="container">
                                <input type="checkbox" disabled="disabled" <?= ($countDocOriginalFile[10]==0)?"":'checked="checked"'; ?>>
                                <span class="checkmark" style="left: 18px;"></span>
                            </label>
                        </td>
                    </tr>
                    <!--Part 11-->
                    <tr class="font-size-12 dir-ltr"  style="height: 20px;text-align: left;font: message-box;font-family: -webkit-body;">
                        <td class="font-inherit font-size-12 text-left" colspan="25"><span id="text-input09"><?= (($countDocOriginal[11]==0)?"":$countDocOriginal[11].' Sets Original').(($countDocOriginal[11]>0 && $countDocCopy[11]>0)?"-":'').(($countDocCopy[11]==0)?"":$countDocCopy[11].' Sets Copy').($docPageCount[11]==0?"":" (each set $docPageCount[11] Pages)") ?></span></td>
                        <td class="font-inherit font-size-12 text-left" colspan="21" ><?= "PG (Approved Legal/Financial)" ?>&nbsp;</td>
                        <td class="font-inherit font-size-12 text-right" colspan="4">
                            <label class="container">
                                <input type="checkbox" disabled="disabled" <?= ($countDocOriginalFile[11]==0)?"":'checked="checked"'; ?>>
                                <span class="checkmark" style="left: 18px;"></span>
                            </label>
                        </td>
                    </tr>
                    
                    
                    
                    <tr style="height: 48px;">
                        
                    </tr>
                    
                    

                </tbody>       
                
              
                <!--Footer-->
                        
                <tfoot>
                    <tr class="">
                        <td class="" colspan="15" >
                            <p style="margin-top: -2px;font-family: serif;font-size: medium;">Head Of Cost Control</p>
                                <?= !empty($sgn_02['uid']) ? yii\helpers\Html::img(['usersignature', 'uid' => $sgn_02['uid'], 'oid' => $sgn_02['oid']], [
                                    'style' => 'width:108px;margin-top: -30px;margin-bottom: -54px'
                                ]):null; ?>
                            <span class="font-bold pr5 pl5 font-size-8" style="margin-left: 136px;"> 
                                <?php echo!empty($sgn_02['UserName']) ? $sgn_02['UserName'] : ''; ?>
                            </span>
                            <span class="font-bold pr5 pl5 font-size-8" style="margin-left: 136px;"> 
                                <?php echo!empty($sgn_02['ActionDateHijri']) ? $sgn_02['ActionDateHijri'] : ''; ?>
                            </span>
                        </td>
                        <td class="" colspan="20" >
                            <p style="margin-top: -2px;font-family: serif;font-size: medium;">Manager</p>
                                <?= !empty($sgn_01['uid']) ? yii\helpers\Html::img(['usersignature', 'uid' => $sgn_01['uid'], 'oid' => $sgn_01['oid']], [
                                    'style' => 'width:108px;margin-top: -30px;margin-bottom: -54px'
                                ]):null; ?>
                            <span class="font-bold pr5 pl5 font-size-8" style="margin-left: -180px;"> 
                                <?php echo!empty($sgn_01['UserName']) ? $sgn_01['UserName'] : ''; ?>
                            </span>
                            <span class="font-bold pr5 pl5 font-size-8" style="margin-left: -180px;"> 
                                <?php echo!empty($sgn_01['ActionDateHijri']) ? $sgn_01['ActionDateHijri'] : ''; ?>
                            </span>
                        </td>
                        <td class="" colspan="15">
                            <p style="margin-top: -2px;font-family: serif;font-size: medium;">Expert</p>
                                <?= !empty($sgn_0['uid']) ? yii\helpers\Html::img(['usersignature', 'uid' => $sgn_0['uid'], 'oid' => $sgn_0['oid']], [
                                    'style' => 'width:108px;margin-top: -30px;margin-bottom: -54px'
                                ]):null; ?>
                            <span class="font-bold pr5 pl5 font-size-8" style="margin-right: 115px;"> 
                                <?php echo!empty($sgn_0['UserName']) ? $sgn_0['UserName'] : ''; ?>
                            </span>
                            <span class="font-bold pr5 pl5 font-size-8" style="margin-right: 115px;"> 
                                <?php echo!empty($sgn_0['ActionDateHijri']) ? $sgn_0['ActionDateHijri'] : ''; ?>
                            </span>
                        </td>
                    </tr>
                    <tr style="height: 30px;">
                    </tr>
                    
                    <tr class="">
                        <td colspan="50">
                            <img src="<?= $image_footer ?>" style="width: 102px;margin-top: -62px;margin-bottom: -11px;" />
                        </td>
                    </tr>
                    <tr class="font-calibri font-bold">
                        <td colspan="50" style="text-align: -webkit-center;">
                            <span style="width: 95%;">
                                <hr style="height: 2px;background-color: black;">
                            </span>
                            <br>
                            <span style="margin-top: -23px;">
                                <?php echo "تهران خیابان شهید با هنر خیابان شهید مقدسی ساختمان مژده شماره 37 "; ?> 
                                &nbsp;
                                &nbsp;
                                &nbsp;
                                <?php echo "تلفن: 22722655-021"; ?> 
                                &nbsp;
                                &nbsp;
                                <?php echo "نمابر: 22713735-021"; ?> 
                            </span>

                        </td>
                    </tr>
                </tfoot>
                <!--Footer-->
                
                    
            </table>
            <!----------------------------------------------------------------------------->

        </div>

    </div>
</body>

<script>
    $(window).load(function(){
        window.print();
    });
    
</script>