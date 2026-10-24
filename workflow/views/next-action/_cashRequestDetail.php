<?php
use yii\helpers\Html;
use app\components\Utility;
use app\components\numToPersianWord;
//header_remove('X-Frame-Options');
?>
<div id="box" style="max-height:450px; overflow-y: scroll; margin-left: 60px;">
<!--<iframe 
    src="<?php echo Yii::$app->urlManager->createAbsoluteUrl(["cashRequest/cashrequest/createform", 'peid'=>$peid,'type'=>'na']); ?>" 
    width="100%"
    height="900px"
    scrolling="no"
    style="border: 0px;"
    name="frame-cashrequest"
></iframe>-->

    <div style="width:100%; height:900px;">
        <div class='main-print-box font-bnazanin' dir="rtl" >
            <table class="border-trl text-center" style="width:100%;table-layout: fixed;" cellspacing="0" width="595px"  heigh="100%">
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
                    <tr class="table-header-bg" style="width:100%">
                        <th rowspan="2" colspan="8" class="border-t border-lr"  style="line-height: 4px">
                            <span class='logo-psa' ><img src="<?= $psa; ?>" style="" /></span>
                            &nbsp;</th>
                        <th class="border-t border-lr  " colspan="34" style="line-height: 22px">
                            <b><?php echo $formLabel[1] ?>
                                &nbsp;</b></th>
                        <th rowspan="2"  colspan="8" class="border-t border-lr font-calibri font-italic font-bold font-size-10" width='15%'  style="line-height: 12px">
                            <span class='logo-cando ' ><img src="<?= $cando; ?>" style="" /></span>
                            <?= $formLabel[0] ?>
                            &nbsp;</th>
                    </tr>
                    <tr class="table-header-bg font-size-10"  style="line-height: 10px">
                        <th class="border-t border-lr font-inherit font-size-12" colspan="7"><?= $formLabel[61] ?>&nbsp;</th>
                        <th class="border-t border-l font-inherit font-size-12" colspan="7"><?= $formLabel[62] ?>&nbsp;</th>
                        <th class="border-t border-l font-inherit font-size-12" colspan="6" ><?= $formLabel[63] ?>&nbsp;</th>
                        <th class="border-t border-l font-inherit font-size-12" colspan="7"><?= $formLabel[64] ?>&nbsp;</th>
                        <th class="border-t border-l font-inherit font-size-12" colspan="7"><?= $formLabel[65] ?>&nbsp;</th>
                    </tr>
                    <tr class="table-header-bg font-size-10" style="line-height: 10px" colspan="50">
                        <th class="border-trbl font-tahoma font-inherit  font-size-12" colspan="8">

                            <span class="text-ltr font-size-10" >
                                <?= $formLabel[2] . "&nbsp;"; ?>
                                <?= 'Py-000-' . $processEntityModel->CashRequestIDRef ?>
                            </span>
                            &nbsp;</th>
                        <th class="border-trbl" colspan="7" >&nbsp;</th>
                        <th class="border-t border-l border-b" colspan="7" >&nbsp;</th>
                        <th class="border-t border-l border-b" colspan="6" >&nbsp;</th>
                        <th class="border-t border-l border-b" colspan="7" >&nbsp;</th>
                        <th class="border-t border-l border-b" colspan="7" >&nbsp;</th>
                        <th class="border-trbl font-tahoma font-size-12 font-inherit"  colspan="8">
                            <?= $nowDateFa; ?>
                            &nbsp;</th>
                    </tr>  
                </thead>
                <tbody>
                    <!---  Part Two ---->
                    <!--معاونت برنامه ریزی و کنترل پروژه-->
                    <tr class="">
                        <td class="border-rbl text-right" colspan="50" >
                            <span class="font-bold pr5 pl5" > 
                                <?php echo $formLabel[3][$location] ?></span>

                            <span class="font-bmitra pr5 pl5" >
                                <?= $formLabel[66] ?>
                            </span>

                            &nbsp;</td>
                    </tr>

                    <tr class="blue-main-head " >
                        <td class="border-trbl font-size-12  font-bold" colspan="1" ></td>
                        <td class="border-t border-l border-b font-size-12  font-bold" colspan="7"><b><?= $formLabel[14] ?></b></td>
                        <td class="border-t border-l border-b font-size-12  font-bold" colspan="5" ><b><?= $formLabel[16] ?></b></td>
                        <td class="border-t border-l border-b font-size-12  font-bold" colspan="11"><b><?= $formLabel[17] ?></b></td>
                        <td class="border-t border-l border-b font-size-12  font-bold" colspan="8" ><b><?= $formLabel[18] ?></b></td>
                        <td class="border-t border-l border-b font-size-12  font-bold" colspan="6" ><b><?= $formLabel[19] ?></b></td>
                        <td class="border-t border-l border-b font-size-12  font-bold" colspan="2"><b><?= $formLabel[20] ?></b></td>
                        <td class="border-t border-l border-b font-size-12  font-bold" colspan="10" ><b><?= $formLabel[21] ?></b></td>

                    </tr>

                    <?php
                    $i = -1;
                    foreach ($cashRequestAmountModels as $amountModel) {
                        $i++;
                        $manager2Currency = (isset($amountModel->manager2Currency)) ? Html::encode($amountModel->manager2Currency->CurrencyPersianTitle) : '';
                        $financialCurrency = (isset($amountModel->financialCurrency)) ? Html::encode($amountModel->financialCurrency->CurrencyPersianTitle) : '';
                        ?>
                        <tr class="blue-sub-content font-tahoma font-size-10">
                            <td class="border-trbl"  colspan="1" >
                                <?php echo ($i + 1); ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="7">
                                <?= Html::encode($amountModel->type->CashRequestTypeTitle) ?>
                                <br>
                                <?php
                                $cnd = $amountModel->CashRequestRecipientTypeIDRef;
                                if ($cnd == 1) {
                                    echo Html::encode($amountModel->recipientContract->subContractor->PersonTitle);
                                } elseIf ($cnd == 2 || $cnd == 3) {
                                    echo Html::encode($amountModel->recipientName);
                                }
                                ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="5">
                                <?php
                                if ($cnd == 1) {
                                    echo Html::encode($amountModel->recipientContract->ContractNo);
                                }
                                ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="11">
                                <?= Html::encode($amountModel->Description) ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="8">
                                <?= Html::encode($amountModel->RecipientAccountNumber) . "<br />" . Html::encode($amountModel->RecipientAccountBank) . "-" . Html::encode($amountModel->RecipientAccountName) ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b" colspan="6" >
                                <?php
                                if ($amountModel->Manager1Amount > 0) {
                                    echo Utility::moneyFormat($amountModel->Manager1Amount);
                                } else {
                                    echo Utility::moneyFormat($amountModel->RequestedAmount);
                                }
                                ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="2">
                                <?= Html::encode($amountModel->requestedCurrency->CurrencyPersianTitle) ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="10">
                                <?php
                                $n2w = new numToPersianWord();
                                if ($amountModel->Manager1Amount > 0)
                                    echo $n2w->numberToWords($amountModel->Manager1Amount) . ' ' . Html::encode($amountModel->requestedCurrency->CurrencyPersianTitle);
                                else
                                    echo $n2w->numberToWords($amountModel->RequestedAmount) . ' ' . Html::encode($amountModel->requestedCurrency->CurrencyPersianTitle);
                                ?>
                                &nbsp;</td>

                        </tr>
                    <?php } ?>


                    <?php /* ------------------------------< امضا>-------------------------------- */ ?>
                    
                    <tr class="" style="height: 0px;">
                        <td class="" style="text-align: right;" colspan="17">
                            <span style="text-align: -webkit-center;">
                                <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;margin-bottom: 5px;background-color: wheat;width: fit-content;"><?= $formData['sgn-8']['ActionAutoMessage'] ?></span>
                            </span>
                        </td>

                        <td class="" style="text-align: center;" colspan="15">
                            <span style="text-align: -webkit-center;">
                                <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;margin-bottom: 5px;background-color: wheat;width: fit-content;"><?= $formData['sgn-11']['ActionAutoMessage'] ?></span>
                            </span>
                        </td>

                        <td class="" style="text-align: left;" colspan="18">
                            <span style="text-align: -webkit-center;">
                                <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;margin-bottom: 5px;background-color: wheat;padding: 0px 11px 0px 11px;width: fit-content;"><?= $formData['sgn-5']['ActionAutoMessage'] ?></span>
                            </span>
                        </td>
                    </tr>
                    
                    <tr class="" style="height: 0px;">
                        <td class="" style="text-align: right;" colspan="17">
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;padding: 0px 11px 0px 11px;"><?= $formData['sgn-8']['ActionMessage'] ?></span>
                        </td>

                        <td class="" style="text-align: center;" colspan="15">
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;padding: 0px 11px 0px 11px;"><?= $formData['sgn-11']['ActionMessage'] ?></span>
                        </td>

                        <td class="" style="text-align: left;" colspan="18">
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;padding: 0px 11px 0px 11px;"><?= $formData['sgn-5']['ActionMessage'] ?></span>
                        </td>
                    </tr>
                    
                    

                    <tr class="" style="height: 0px;">
                        <td class="bottom image-right" style="text-align: right;background-position: 126px -2px;" colspan="17" background='<?= Yii::$app->urlManager->createAbsoluteUrl(['cashRequest/cashrequest/usersignature', 'peid' => $processEntityModel->ProcessEntityID, 'sk' => 'sgn-8']) ?>'>
                            
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;"><?= $formData['sgn-8']['UserName'] ?></span>
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;"><?= $formData['sgn-8']['ActionDate'] ?></span>
                            <span class="bottom font-bold" style="text-align: right;height: 20px;"><?= $formLabel[22] ?></span>
                        </td>

                        <td class="bottom image-center" style="text-align: center;background-position: 56px -2px;" colspan="15"  background='<?= Yii::$app->urlManager->createAbsoluteUrl(['cashRequest/cashrequest/usersignature', 'peid' => $processEntityModel->ProcessEntityID, 'sk' => 'sgn-11']) ?>'>
                            
                            <span class="bottom font-bold" style="text-align: left;color: #0012ff;font-size: 10px;"><?= $formData['sgn-11']['UserName'] ?></span>
                            <span class="bottom font-bold" style="text-align: left;color: #0012ff;font-size: 10px;"><?= $formData['sgn-11']['ActionDate'] ?></span>
                            <span class="bottom font-bold" style="text-align: center;height: 20px;"><?= $formLabel[68] ?></span>
                        </td>

                        <td class="bottom image-left" style="text-align: left;background-position: 0px -2px;" colspan="18"  background='<?= Yii::$app->urlManager->createAbsoluteUrl(['cashRequest/cashrequest/usersignature', 'peid' => $processEntityModel->ProcessEntityID, 'sk' => 'sgn-5']) ?>'>
                            
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;"><?= $formData['sgn-5']['UserName'] ?></span>
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;"><?= $formData['sgn-5']['ActionDate'] ?></span>
                            <span class="bottom font-bold" style="text-align: left;height: 20px;"><?= $formLabel[4][$location] ?></span>
                        </td>
                    </tr>
                    <?php /* ------------------------------< امضا>-------------------------------- */ ?>

                    <!--         Part three    معاونت محترم مالی و اقتصادی-->

                    <tr class="">
                        <td class="border-rbl" colspan="50" style='font-size: .1px;'>&nbsp;&nbsp;</td>
                    </tr>
                    <tr class="">
                        <td class="border-rbl text-right" colspan="50" >
                            <span class="font-bold pr5 pl5" > 
                                <?php echo $formLabel[11][$location] ?></span>

                            <span class="font-bmitra pr5 pl5" >
                                <?= $formLabel[23] ?>
                                <b><?= ($formData['PlanningAutoMessage'] != 'blank') ? $formData['PlanningAutoMessage'] : ''; ?></b>
                            </span>

                            &nbsp;</td>
                    </tr>

                    <tr class="green-main-head">
                        <td class="border-trbl"  width="4%" colspan="1"><b>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="7"><b><?= $formLabel[26] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="5"><b><?= $formLabel[27] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="4"><b><?= $formLabel[28] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="15"><b><?= $formLabel[29] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="6"><b><?= $formLabel[30] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="2" width="4%" ><b><?php echo $formLabel[31] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="10" ><b><?= $formLabel[69] ?>&nbsp;</b></td>
                    </tr>
                    <?php
                    $i=0;
                    foreach ($cashRequestAmountModels as $amountModel) {
                        $manager2Currency = (isset($amountModel->manager2Currency)) ? Html::encode($amountModel->manager2Currency->CurrencyPersianTitle) : '';
                        $financialCurrency = (isset($amountModel->financialCurrency)) ? Html::encode($amountModel->financialCurrency->CurrencyPersianTitle) : '';
                        ?>
                        <tr class="green-sub-content font-tahoma font-size-10">
                            <td class="border-trbl" colspan="1" >
                                <?php echo ($i + 1); ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b" colspan="7" >
                                <?php
                                $cnd = $amountModel->CashRequestRecipientTypeIDRef;
                                if ($cnd == 1) {
                                    echo Html::encode($amountModel->recipientContract->subContractor->PersonTitle);
                                } elseIf ($cnd == 2 || $cnd == 3) {
                                    echo Html::encode($amountModel->recipientName);
                                }
                                ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b" colspan="5" >
                                <?php
                                if ($cnd == 1) {
                                    echo Html::encode($amountModel->recipientContract->ContractNo);
                                }
                                ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b" colspan="4" >
                                <?= Html::encode($amountModel->BudgetRow) ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b" colspan="15" >
                                <?= Html::encode($amountModel->BudgetTitle) ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b" colspan="6" >
                                <?= Utility::moneyFormat($amountModel->PlanningAmount); ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b" colspan="2" >
                                <?= Html::encode($amountModel->requestedCurrency->CurrencyPersianTitle) ?>
                                &nbsp;</td>
                            <?php if($amountModel->budgetType == 'OperatingBudget'){ 
                                $remain = $amountModel->budget->plan->PlanAmount - $amountModel->budget->actualCost1; ?>
                                <td class="border-t border-l border-b" colspan="10" style="background-color: <?= $remain >= 0 ? 'green' : 'red' ?>">
                                    <?= Utility::moneyFormat($remain);?>
                                &nbsp;</td>
                                <?php }else{?>
                                <td class="border-t border-l border-b" colspan="10" > 
                                    &nbsp;</td>
                            <?php } ?>
                        </tr>
                    <?php
                    $i++;
                        } ?>

<!--                    <tr class="">
                        <td class="text-right" colspan="50" >
                            <span class="font-bmitra pr5 pl5 mt10" >
                                <?= $formLabel[24] ?>:
                                <b><?= ($formData['PlanningMessage'] != 'blank') ? $formData['PlanningMessage'] : ''; ?></b>
                            </span>
                        </td>
                    </tr>-->

                    
                    <tr class="" style="height: 0px;">
                        <td class="" style="text-align: right;" colspan="25">
                            <span style="text-align: -webkit-center;">
                                <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;margin-bottom: 5px;background-color: wheat;width: fit-content;"><?= $formData['sgn-1']['ActionAutoMessage'] ?></span>
                            </span>
                        </td>

                        <td class="" style="text-align: left;" colspan="25">
                            <span style="text-align: -webkit-center;">
                                <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;margin-bottom: 5px;background-color: wheat;padding: 0px 11px 0px 11px;width: fit-content;"><?= $formData['sgn-7']['ActionAutoMessage'] ?></span>
                            </span>
                        </td>
                    </tr>
                    
                    <tr class="" style="height: 0px;">
                        <td class="" style="text-align: right;" colspan="25">
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;padding: 0px 11px 0px 11px;"><?= $formData['sgn-1']['ActionMessage'] ?></span>
                        </td>

                        <td class="" style="text-align: left;" colspan="25">
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;padding: 0px 11px 0px 11px;"><?= $formData['sgn-7']['ActionMessage'] ?></span>
                        </td>
                    </tr>
                    
                    
                    
                    <tr class=""  style="height: 90px;">
                        
                        <td class="bottom image-right" style="text-align: right" colspan="25"  background='<?= Yii::$app->urlManager->createAbsoluteUrl(['cashRequest/cashrequest/usersignature', 'peid' => $processEntityModel->ProcessEntityID, 'sk' => 'sgn-1']) ?>'>
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;margin-right:-180px;"><?= $formData['sgn-1']['UserName'] ?></span>
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;margin-right: -180px;"><?= $formData['sgn-1']['ActionDate'] ?></span>
                            <span class="bottom font-bold" style="text-align: right;height: 20px;"><?= $formLabel[5][$location] ?></span>
                        </td>

                        <td class="bottom image-left" style="text-align: left" colspan="25"  background='<?= Yii::$app->urlManager->createAbsoluteUrl(['cashRequest/cashrequest/usersignature', 'peid' => $processEntityModel->ProcessEntityID, 'sk' => 'sgn-7']) ?>'>
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;"><?= $formData['sgn-7']['UserName'] ?></span>
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;"><?= $formData['sgn-7']['ActionDate'] ?></span>
                            <span class="bottom font-bold" style="text-align: left;height: 20px;"><?= $formLabel[6][$location] ?></span>
                        </td>
                    </tr>

                    <!---  Part Four ---->

                    <tr class="">
                        <td class="border-rbl" colspan="50" style='font-size: .1px;'>&nbsp;&nbsp;</td>
                    </tr>
                    <tr class="">
                        <td class="border-rbl text-right" colspan="50" >
                            <span class="font-bold pr5 pl5" > 
                                <?= $formLabel[12][$location] ?></span>

                            <span class="font-bmitra pr5 pl5" >
                                <?php echo $formLabel[33] ?>
                            </span>
                            &nbsp;</td>
                    </tr>

                    <tr class="lblue-main-head">

                        <td class="border-trbl" width="4%"  colspan="1"></td>
                        <td class="border-t border-l border-b" colspan="17"><b><?= $formLabel[35] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b"  colspan="13"><b><?= $formLabel[36] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="7" ><b><?= $formLabel[37] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" width="4%"  colspan="2"><b><?= $formLabel[38] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="10"><b><?= $formLabel[39] ?>&nbsp;</b></td>


                    </tr>
                    
                    
                    <?php
                    $i = -1;
                    foreach ($cashRequestAmountModels as $amountModel) {
                        $i++;
                        $manager2Currency = (isset($amountModel->manager2Currency)) ? Html::encode($amountModel->manager2Currency->CurrencyPersianTitle) : '';
                        $financialCurrency = (isset($amountModel->financialCurrency)) ? Html::encode($amountModel->financialCurrency->CurrencyPersianTitle) : '';
                        ?>
                        <tr class="lblue-sub-content font-tahoma font-size-10">
                            <td class="border-trbl"   colspan="1">
                                <?= ($i + 1); ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b" colspan="17">
                                <?php
                                $cnd = $amountModel->CashRequestRecipientTypeIDRef;
                                if ($cnd == 1) {
                                    echo Html::encode($amountModel->recipientContract->subContractor->PersonTitle);
                                } elseIf ($cnd == 2 || $cnd == 3) {
                                    echo Html::encode($amountModel->recipientName);
                                }
                                ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b" colspan="13">
                                <?php
                                if ($cnd == 1) {
                                    echo Html::encode($amountModel->recipientContract->ContractNo);
                                }
                                ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="7">
                                <?= Utility::moneyFormat($amountModel->Manager2Amount) ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="2">
                                <?= $manager2Currency ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b" colspan="10">
                                <?= $n2w->numberToWords($amountModel->Manager2Amount) . ' ' . $manager2Currency; ?>
                                &nbsp;</td>

                        </tr>
                    <?php } ?>
                        
                    
                    
                    
                    <tr class="" style="height: 96px;">
                        <td class="border-r text-right pr5" colspan="30" >
                            <span style="text-align: -webkit-center;">
                                <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;margin-bottom: 5px;background-color: wheat;width: fit-content;"><?= $formData['sgn-6']['ActionAutoMessage'] ?></span>
                            </span>
                            <?php echo $formLabel[40] ?>
                            <!--<b><?= ($formData['Manager2Message'] != 'blank') ? $formData['Manager2Message'] : ''; ?></b>-->
                            <b><?= $formData['sgn-6']['ActionMessage'] ?></b>
                        </td>
                        
                        <td class="bottom image-left" style="text-align: left" colspan="20"  background='<?= Yii::$app->urlManager->createAbsoluteUrl(['cashRequest/cashrequest/usersignature', 'peid' => $processEntityModel->ProcessEntityID, 'sk' => 'sgn-6']) ?>'>
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;"><?= $formData['sgn-6']['UserName'] ?></span>
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;"><?= $formData['sgn-6']['ActionDate'] ?></span>
                            <span class="bottom font-bold" style="text-align: left;height: 20px;"><?= $formLabel[8][$location] ?></span>
                        </td>
                    </tr>



                    <!---  Part Five ---->

                    <tr class="">
                        <td class="border-rbl" colspan="50" style='font-size: .1px;'>&nbsp;&nbsp;</td>
                    </tr>
                    <tr class="">
                        <td class="border-r text-right" colspan="32" >
                            <span class="font-bold pr5 pl5" > 
                                <?= $formLabel[15] ?>
                            </span>
                            &nbsp;</td>
                        <td class="border-l border-b text-right" colspan="18" >

                            &nbsp;</td>

                    </tr>
                    <tr class="">
                        <td class="border-rbl text-right" colspan="32" style=" line-height: 15px;">
                            <span class="font-bmitra  pr5 pl5" >
                                <?= $formLabel[41] ?>
                            </span>
                            &nbsp;</td>
                        <td class="border-trbl olive-main-head" colspan="18" style=" line-height: 15px;">
                            <span class="font-bmitra font-bold" >
                                <?= $formLabel[42] ?>
                            </span>
                            &nbsp;</td>

                    </tr>

                    <tr class="olive-sub-head">

                        <td class="border-trbl" width="4%" colspan="1"><b></td>
                        <td class="border-t border-l border-b" colspan="8"><b><?= $formLabel[44] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="5"><b><?= $formLabel[45] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="6"><b><?= $formLabel[46] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="2"><b><?= $formLabel[47] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="10"><b><?= $formLabel[48] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="6"><b><?= $formLabel[49] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="2"><b><?= $formLabel[50] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b" colspan="10"><b><?= $formLabel[51] ?>&nbsp;</b></td>


                    </tr>
                    <?php
                    $i = -1;
                    foreach ($cashRequestAmountModels as $amountModel) {
                        $i++;
                        $manager2Currency = (isset($amountModel->manager2Currency)) ? Html::encode($amountModel->manager2Currency->CurrencyPersianTitle) : '';
                        $financialCurrency = (isset($amountModel->financialCurrency)) ? Html::encode($amountModel->financialCurrency->CurrencyPersianTitle) : '';
                        ?>
                        <tr class="olive-sub-content font-tahoma font-size-10" colspan="1">
                            <td class="border-trbl"  >
                                <?php echo ($i + 1); ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="8">
                                <?php
                                $cnd = $amountModel->CashRequestRecipientTypeIDRef;
                                if ($cnd == 1) {
                                    echo Html::encode($amountModel->recipientContract->subContractor->PersonTitle);
                                } elseIf ($cnd == 2 || $cnd == 3) {
                                    echo Html::encode($amountModel->recipientName);
                                }
                                ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="5">
                                <?php
                                if ($cnd == 1) {
                                    echo Html::encode($amountModel->recipientContract->ContractNo);
                                }
                                ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="6">
                                <?= Utility::moneyFormat($amountModel->FinancialAmountInManager2Currency) ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="2">
                                <?= $manager2Currency ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="10">
                                <?= $n2w->numberToWords($amountModel->FinancialAmountInManager2Currency) . ' ' . $manager2Currency; ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="6">
                                <?= Utility::moneyFormat($amountModel->FinancialAmount) ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b" colspan="2" >
                                <?= $financialCurrency ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="10">
                                <?= $n2w->numberToWords($amountModel->FinancialAmount) . ' ' . $financialCurrency; ?>
                                &nbsp;</td>

                        </tr>
                    <?php } ?>

<!--                        <td class="text-right" colspan="50" >
                                <span class="font-bold pr5 pl5" > 
                            <span class="font-bold mt10 mb0" style="display: contents;"> 
                                    <?= 'توضیحات' ?></span>

                                &nbsp;</td>-->
                        
                        <!--</tr>-->
<!--                    <tr class="mt0">
                        <td class="" colspan="14"><b><?= $formData['sgn-4']['ActionMessage']; ?></b></td>
                        <td class="border-lr" colspan="18"><b><?= $formData['sgn-3']['ActionMessage']; ?></b></td>
                        <td class="" colspan="18"><b><?= $formData['sgn-9']['ActionMessage']; ?></b></td>
                    </tr>-->


                    <tr class="" style="height: 0px;">
                        <td class="" style="text-align: right;" colspan="17">
                            <span style="text-align: -webkit-center;">
                                <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;margin-bottom: 5px;background-color: wheat;width: fit-content;"><?= $formData['sgn-4']['ActionAutoMessage'] ?></span>
                            </span>
                        </td>
                        
                        <td class="" style="text-align: right;" colspan="11">
                            <span style="text-align: -webkit-center;">
                                <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;margin-bottom: 5px;background-color: wheat;width: fit-content;"><?= $formData['sgn-3']['ActionAutoMessage'] ?></span>
                            </span>
                        </td>


                        <td class="" style="text-align: left;" colspan="22">
                            <span style="text-align: -webkit-center;">
                                <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;margin-bottom: 5px;background-color: wheat;padding: 0px 11px 0px 11px;width: fit-content;"><?= $formData['sgn-9']['ActionAutoMessage'] ?></span>
                            </span>
                        </td>
                    </tr>
                    
                    <tr class="" style="height: 0px;">
                        <td class="" style="text-align: right;" colspan="17">
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;padding: 0px 11px 0px 11px;"><?= $formData['sgn-4']['ActionMessage'] ?></span>
                        </td>
                        
                        <td class="" style="text-align: right;" colspan="11">
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;padding: 0px 11px 0px 11px;"><?= $formData['sgn-3']['ActionMessage'] ?></span>
                        </td>

                        <td class="" style="text-align: left;" colspan="22">
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;padding: 0px 11px 0px 11px;"><?= $formData['sgn-9']['ActionMessage'] ?></span>
                        </td>
                    </tr>
                    
                    <tr class="" style="height: 90px;">

                        <td class="bottom image-right" style="text-align: right" colspan="17"  background='<?= Yii::$app->urlManager->createAbsoluteUrl(['cashRequest/cashrequest/usersignature', 'peid' => $processEntityModel->ProcessEntityID, 'sk' => 'sgn-4']) ?>'>
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;"><?= $formData['sgn-4']['UserName'] ?></span>
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;"><?= $formData['sgn-4']['ActionDate'] ?></span>
                            <span class="bottom font-bold" style="text-align: right;height: 20px;"><?= $formLabel[52] ?></span>
                        </td>

                        <td class="bottom image-center" style="text-align: center" colspan="11"  background='<?= Yii::$app->urlManager->createAbsoluteUrl(['cashRequest/cashrequest/usersignature', 'peid' => $processEntityModel->ProcessEntityID, 'sk' => 'sgn-3']) ?>'>
                            <span class="bottom font-bold" style="text-align: left;color: #0012ff;font-size: 10px;"><?= $formData['sgn-3']['UserName'] ?></span>
                            <span class="bottom font-bold" style="text-align: left;color: #0012ff;font-size: 10px;"><?= $formData['sgn-3']['ActionDate'] ?></span>
                            <span class="bottom font-bold" style="text-align: center;height: 20px;"><?= $formLabel[9][$location] ?></span>
                        </td>

                        <td class="bottom image-left" style="text-align: left" colspan="22"  background='<?= Yii::$app->urlManager->createAbsoluteUrl(['cashRequest/cashrequest/usersignature', 'peid' => $processEntityModel->ProcessEntityID, 'sk' => 'sgn-9']) ?>'>
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;"><?= $formData['sgn-9']['UserName'] ?></span>
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;"><?= $formData['sgn-9']['ActionDate'] ?></span>
                            <span class="bottom font-bold" style="text-align: left;height: 20px;"><?= $formLabel[10][$location] ?></span>
                        </td>
                    </tr>



                    <!---  Part Six ---->
                    <tr class="">
                        <td class="border-rbl" colspan="50" style='font-size: .1px;'>&nbsp;&nbsp;</td>
                    </tr>

                    <tr class="olive-check">

                        <td class="border-trbl" width="4%"  colspan="1"></td>
                        <td class="border-t border-l border-b"  colspan="14"><b><?= $formLabel[54] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b"  colspan="6"><b><?= $formLabel[55] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b"  colspan="2"><b><?= $formLabel[56] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b"  colspan="10"><b><?= $formLabel[57] ?>&nbsp;</b></td>
                        <td class="border-t border-l border-b"  colspan="17"><b><?= $formLabel[58] ?>&nbsp;</b></td>

                    </tr>

                    <?php
                    $i = -1;
                    foreach ($cashRequestAmountModels as $amountModel) {
                        $i++;
                        $manager2Currency = (isset($amountModel->manager2Currency)) ? $amountModel->manager2Currency->CurrencyPersianTitle : '';
                        $financialCurrency = (isset($amountModel->financialCurrency)) ? $amountModel->financialCurrency->CurrencyPersianTitle : '';
                        ?>
                        <tr class="font-tahoma font-size-10">
                            <td class="border-trbl" colspan="1" >
                                <?= ($i + 1); ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="14">
                                <?= $amountModel->ReceiptNumber ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="6">
                                <?= Utility::moneyFormat($amountModel->FinancialAmount) ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="2">
                                <?= $financialCurrency ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="10">
                                <?= $n2w->numberToWords($amountModel->FinancialAmount) . ' ' . $manager2Currency; ?>
                                &nbsp;</td>
                            <td class="border-t border-l border-b"  colspan="17">
                                <?= $amountModel->RecipientAccountName ?>
                                &nbsp;</td>

                        </tr>
                    <?php } ?>
                    <!-- @@Add Kamali-->
                    <tr class="" style="height: 0px;">
                        <td class="" style="text-align: right;" colspan="10">
                            <span style="text-align: -webkit-center;">
                                <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;margin-bottom: 5px;background-color: wheat;width: fit-content;"><?= $formData['sgn-10']['ActionAutoMessage'] ?></span>
                            </span>
                        </td>
                        <td class="" style="text-align: right;" colspan="40">
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;padding: 0px 11px 0px 11px;"><?= $formData['sgn-10']['ActionMessage'] ?></span>
                        </td>
                    </tr>
                   
                    <tr class="">
                        <td class="bottom image-right border-b" style="text-align: right" colspan="17"  background='<?= Yii::$app->urlManager->createAbsoluteUrl(['cashRequest/cashrequest/usersignature', 'peid' => $processEntityModel->ProcessEntityID, 'sk' => 'sgn-10']) ?>'>
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;"><?= $formData['sgn-10']['UserName'] ?></span>
                            <span class="bottom font-bold" style="text-align: center;color: #0012ff;font-size: 10px;"><?= $formData['sgn-10']['ActionDate'] ?></span>
                            <span class="bottom font-bold" style="text-align: right"><?= $formLabel[60] ?></span>
                        </td>

                        <td class="border-b" colspan="11" >
                            <span class="bottom signer-name font-bold" > 
                                <?php echo "" ?>
                            </span>
                        </td>
                        <td class="bottom border-l border-b text-left"  style="text-align: left" colspan="22" >
                            <span class="bottom signer-name font-bold pl25"  style="text-align: left"> 
                                <?php echo $formLabel[67] ?>
                            </span>
                        </td>
                    </tr>
                    <tr class="">
                        <td class="" colspan="18" style='font-size: .1px;'>&nbsp;&nbsp;</td>
                    </tr>
                </tbody>                

            </table>
            <!----------------------------------------------------------------------------->
        </div>
        <div class='fake-lock'>

        </div>
    </div>
    
</div>
<div class="pull-left">
   <?= Html::a("<i class=\"im-print\"></i> Print", '#', 
    [
        'class' => 'btn btn-sm btn-danger',
        'onclick' => 'print(); return false;'
    ]); ?> 
</div>
<div class="pull-right">
    <?= Html::a('<i class="fa-ok"></i> Sign and Close', '#', [
            'class' => 'btn btn-sm btn-success', 
            "onclick" => "sign(this)", 
            'ajax-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/sign', 'type' => Yii::$app->request->get('type'), 'peid' => Yii::$app->request->get('peid'), 'mode' => 'sign'])
            ]); ?>
   
    <?= Html::a('<i class="en-back"></i> back', 
        Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/next-action',
            'type' => Yii::$app->request->get('type'),
            'peid' => Yii::$app->request->get('peid'),
            'mode' => 'check', 'clearValue' => false]),
        ['class' => 'btn btn-sm btn-primary']) ?>&nbsp;
     <?= Html::a("<i class=\"br-cancel\"></i> Cancel", '#', 
        [
            'class' => 'btn btn-sm btn-warning',
            'data-dismiss' => 'modal'
        ]); ?>

</div>
<?php
$js = "
    function print(){
       window.frames['frame-cashrequest'].focus();
       window.frames['frame-cashrequest'].print();
       return  false;
    }
    function sign(obj){
       
        ajaxProgressLoader('show', '.modal-content');
        $.ajax({
            url:$(obj).attr('ajax-url'),
        }).done(function(data){
            ajaxProgressLoader('hide', '.modal-content');
            var res = data.result;
            if(res != true){
                showMsgBoxInBottomRight(data.Message,'error');
            }
            else if(res == true){
                var r = window.location.search;
                if(r.indexOf('cashRequest%2Fcashrequest%2Fview') != -1 || r.indexOf('cashRequest/ashrequest/view') != -1 ){
                    window.location = '".Yii::$app->urlManager->createAbsoluteUrl(['cashRequest/cashrequest/view', 'peid' => Yii::$app->request->get('peid')])."';
                    return;
                }
                $('#cashrequest-modal').modal('hide');
                $.pjax.reload({
                    container:'#cashrequest-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl([
                        'workflow/workflow/cashrequest', 
                        'type' => 'ToDo',
                        'sort' => Yii::$app->request->get('sort'),
                        'page' => Yii::$app->request->get('page'),
                        ]) . "',
                    push:false,
                    replace:false,
                });
                 showMsgBoxInBottomRight('successful sign and close!','success');
            }
        }).error(function(){
            showMsgBoxInBottomRight('Error on loading data!','error');
            ajaxProgressLoader('hide', '#check-modal');
        });
        return false;
    };
";
$this->registerJs($js, \yii\web\View::POS_END);


//createform style
$js = "
    $(document).ready(function () {
                $(document).on('contextmenu', function (event) {
                    window.print();
                    return false;

                });

                $(document).keydown(function (event) {
                    if (event.which == 123 || event.which == 81 || event.which == 83) {
                        event.preventDefault();
                    }
                });
            });
            function printDoc() {

            }
    ";
$this->registerJs($js, \yii\web\View::POS_END);

$css = "
    .divTable {
                display: table;
                width: 100%;
            }

            .divTableRow {
                display: table-row;
            }
            .main-print-box td.image-right {
                width: 77px;
                height: 77px;
                background-size: 96px 96px;
                background-repeat: no-repeat;
                background-position: right top;
            }
            .main-print-box td.image-left {
                width: 77px;
                height: 77px;
                background-size: 96px 96px;
                background-repeat: no-repeat;
                background-position:left top;
            }
            .main-print-box td.image-center {
                width: 77px;
                height: 77px;
                background-size: 96px 96px;
                background-repeat: no-repeat;
                background-position: center top;
            }
            .main-print-box td.bottom {
                vertical-align: bottom;
            }

            .rotateForm {
                -webkit-transform: rotate(-90deg);
                float: left;
            }
            .main-print-box td{
                word-wrap: break-word;
            }

            .main-print-box table tbody tr td{line-height: 1;font-size: 12px;}
            .main-print-box span{display: block;}
            .font-size-12 td{font-size: 12px;}
            .font-size-10 td{font-size: 10px;}
            .font-bnazanin{font-family: 'B Nazanin','B Mitra','Tahoma'; font-size:14px;}
            .font-bmitra{font-family:'B Mitra','Tahoma'; font-size:14px;}
            .font-calibri{font-family: 'Calibri';}
            .font-tahoma{font-family: Tahoma;}
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
            }

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
            .logo-cando img{width:98px;height:20px;}

            .fake-lock{
//                position:fixed; 
                top:0; 
                right:0;
                bottom: 0;
                left:0;
                z-index: 100;

            }
    ";
$this->registerCss($css);
?>
