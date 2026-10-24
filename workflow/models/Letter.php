<?php

namespace app\modules\workflow\models;

use Yii;
use app\extensions\phpWordCando\phpWordCando;
use app\extensions\phpexcel\XPHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use app\components\Templates;
use app\modules\invoice\models\InvoiceEdit;
use app\components\jDateTime;
use app\components\Utility;

/**
 * This is the model class for table "WorkFlow.tblLetter".
 * @author <kamali:09366263397>
 */
class Letter extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'workflow_table_letter';
        } else {
            return 'WorkFlow.tblLetter';
        }
    }

    public static function primaryKey()
    {
        return ['letterId'];
    }

    public static function getDb()
    {
        return \app\components\Utility::centralDb();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['letterId'], 'required'],
            [['letterId', 'OBSCategory3CompanyIdRef', 'processTypeIdRef', 'fileIdRef'], 'integer'],
            [['letterTitle'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'letterId' => 'Letter ID',
            'letterTitle' => 'Letter Title',
            'OBSCategory3CompanyIdRef' => 'Obscategory3 Company Id Ref',
            'processTypeIdRef' => 'Process Type Id Ref',
            'fileIdRef' => 'File Id Ref',
        ];
    }

    public static function letterItemTitle($from, $to)
    {
        // and workflow_table_letter.OBSCategory3CompanyIdRef=248
        $processTable = Process::tableName();
        $actionTable = Action::tableName();

        $company = \Yii::$app->session->get('CompanyID');
        $letterTable = Letter::tableName();

        $processModel = (new \yii\db\Query())->from($processTable)
            ->innerJoin($actionTable, "$processTable.ProcessID=$actionTable.ProcessIDRef")
            ->addSelect('OBSCategory3CompanyIDRef')
            ->where(["ActionID" => $from])->one();

        if (isset($processModel->OBSCategory3CompanyIDRef)) {
            $sql = "SELECT " . ActionOrder::tableName() . ".letterIdRef1," . static::tableName() . ".letterTitle," . static::tableName() . ".fileIdRef FROM " . ActionOrder::tableName() . " inner join " . static::tableName() . " on " . Letter::tableName() . ".letterId=" . ActionOrder::tableName() . ".letterIdRef1 where ActionIDRef = $from And NextActionIDRef = $to And letterIdRef1 Is Not Null "
                . " Union SELECT " . ActionOrder::tableName() . ".letterIdRef2, " . Letter::tableName() . ".letterTitle, " . Letter::tableName() . ".fileIdRef FROM " . ActionOrder::tableName() . " inner join " . Letter::tableName() . " on " . Letter::tableName() . ".letterId=" . ActionOrder::tableName() . ".letterIdRef2 where ActionIDRef = $from And NextActionIDRef = $to And letterIdRef2 Is Not Null "
                . " Union SELECT " . ActionOrder::tableName() . ".letterIdRef3," . static::tableName() . ".letterTitle," . static::tableName() . ".fileIdRef FROM " . ActionOrder::tableName() . " inner join " . Letter::tableName() . " on " . static::tableName() . ".letterId=" . ActionOrder::tableName() . ".letterIdRef3 where ActionIDRef = $from And NextActionIDRef = $to And letterIdRef3 Is Not Null ";
        } else {
            $sql = "SELECT " . ActionOrder::tableName() . ".letterIdRef1," . static::tableName() . ".letterTitle," . static::tableName() . ".fileIdRef FROM " . ActionOrder::tableName() . " inner join " . static::tableName() . " on " . Letter::tableName() . ".letterId=" . ActionOrder::tableName() . ".letterIdRef1 where ActionIDRef = $from And NextActionIDRef = $to And letterIdRef1 Is Not Null and $letterTable.OBSCategory3CompanyIdRef=$company"
                . " Union SELECT " . ActionOrder::tableName() . ".letterIdRef2, " . Letter::tableName() . ".letterTitle, " . Letter::tableName() . ".fileIdRef FROM " . ActionOrder::tableName() . " inner join " . Letter::tableName() . " on " . Letter::tableName() . ".letterId=" . ActionOrder::tableName() . ".letterIdRef2 where ActionIDRef = $from And NextActionIDRef = $to And letterIdRef2 Is Not Null  and $letterTable.OBSCategory3CompanyIdRef=$company "
                . " Union SELECT " . ActionOrder::tableName() . ".letterIdRef3," . static::tableName() . ".letterTitle," . static::tableName() . ".fileIdRef FROM " . ActionOrder::tableName() . " inner join " . Letter::tableName() . " on " . static::tableName() . ".letterId=" . ActionOrder::tableName() . ".letterIdRef3 where ActionIDRef = $from And NextActionIDRef = $to And letterIdRef3 Is Not Null  and $letterTable.OBSCategory3CompanyIdRef=$company ";
        }

        $rawData = Yii::$app->db->createCommand($sql)->queryAll();
        return $rawData;
    }

    public function generateLetter26($letterFormModel, $invoiceModel)
    {
        $templatePath = Templates::getTemplate('word', "LetterID{$this->fileIdRef}.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $invoiceModel->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($invoiceModel->currency2->CurrencyPersianTitle) ? $invoiceModel->currency2->CurrencyPersianTitle : '');

        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        $word->setBookmarkValue('bmSubContractor', str_replace('&', '-', $invoiceModel->contract->subContractor->PersonTitle));
        $word->setBookmarkValue('bmInvoiceType', $invoiceModel->type->InvoiceType);
        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($invoiceModel->contract->ContractNo)));
        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($invoiceModel->Title, '-'));
        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $invoiceModel->contract->ContractSubject));
        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($invoiceModel->CutOffDateHijri, '/'));

        if ($invoiceModel->InvoiceType == 17 || $invoiceModel->InvoiceType == 18 || $invoiceModel->InvoiceType == 19 || $invoiceModel->InvoiceType == 20 || $invoiceModel->InvoiceType == 21) {
            $lateral = $this->cumulativeAditionalCost($invoiceModel);

            $word->setBookmarkValue('bmComulativeApproved1', '');
            $word->setBookmarkValue('bmComulativeApproved1Currency', '');
            $word->setBookmarkValue('bmComulativeApproved1Text', $lateral);
        } else {
            $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle));
            $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
            $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);
        }

        $word->setBookmarkValue('bmApproved1', Utility::moneyFormatWord($invoiceModel->approved1, $currency1PersianTitle));
        $word->setBookmarkValue('bmApproved1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        $word->setBookmarkValue('bmcumulativePenalty1', Utility::moneyFormatWord($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle));
        $word->setBookmarkValue('bmcumulativePenalty1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmcumulativePenalty1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);


        $word->setBookmarkValue('bmComulativeAdjustment1', Utility::invertString($invoiceModel->cumulativeAdjustment1(6), '/'));
        $word->setBookmarkValue('bmComulativeAdjustment1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmComulativeAdjustment1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeAdjustment1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        if ($letterFormModel->Evidence4Chek == 1) {
            $word->setBookmarkValue('bmEvidence4Date', ($letterFormModel->Evidence4Date));
        }

        if ($letterFormModel->Evidence5Chek == 1) {
            $word->setBookmarkValue('bmEvidence5Date', ($letterFormModel->Evidence5Date));
        }

        $letterName = "LetterID{$this->fileIdRef}.docx";
        $activityManagement = new \app\components\ActivityManagement();
        $activityManagement->sendInvoiceLetterActitvityLog($letterName);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    public function generateLetter20($invoiceModel)
    {
        $jDate = new jDateTime(true, true, 'Asia/Tehran');
        $dateNow = $jDate->date("Y.m.d", false, false);

        $useXlsxFormat = true;
        $xlBook = new \ExcelBook(Yii::$app->params['libxlKey']['key'], Yii::$app->params['libxlKey']['pass'], $useXlsxFormat);

        $xlBook->setLocale('UTF-8');

        $xlBook->loadFile(\app\components\Templates::getTemplate('excel', 'TemplateID20.xlsx'));

        $invid = $invoiceModel->InvoiceID;
        $data = \app\modules\contract\models\Contract::incomeList($invid);

        $s0 = $xlBook->getSheet(0);
        $s1 = $xlBook->getSheet(1);

        $intExcelRow = 5;
        $flagSheetActive = 0;
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['Currency2ID'] > 0) {

                $s1->write($intExcelRow, 1, strval($data[$i]['OwnerSubContractorTitle']));
                $s1->write($intExcelRow, 2, strval($data[$i]['OwnerContractNo']));
                $s1->write(($intExcelRow), 4, floatval($data[$i]['Income1']));
                $s1->write(($intExcelRow + 1), 4, floatval($data[$i]['Income2']));
                $s1->write($intExcelRow, 5, floatval($data[$i]['TotalIncome1']));
                $s1->write(($intExcelRow + 1), 5, floatval($data[$i]['TotalIncome2']));
                $s1->write($intExcelRow, 6, strval($data[$i]['Currency1PersianTitle']));
                $s1->write(($intExcelRow + 1), 6, strval($data[$i]['Currency2PersianTitle']));
                $flagSheetActive = 1;
                $intExcelRow = $intExcelRow + 2;
            } else {
                $s0->write($intExcelRow, 1, strval($data[$i]['OwnerSubContractorTitle']));
                $s0->write($intExcelRow, 2, strval($data[$i]['OwnerContractNo']));
                $s0->write(($intExcelRow), 4, floatval($data[$i]['Income1']));
                $s0->write($intExcelRow, 5, floatval($data[$i]['TotalIncome1']));
                $s0->write($intExcelRow, 6, strval($data[$i]['Currency1PersianTitle']));
                $intExcelRow = $intExcelRow + 1;
            }
        }

// Delete Sheet 

        $invoiceModel = InvoiceEdit::find()->where(['InvoiceID' => $invid])->one();

        $font = $xlBook->addFont();
        $font->name("B Mitra");
        $font->size(9);
        $font->bold(true);

        $fontSum = $xlBook->addFormat();
        //        $fontSum->numberFormat($format);
        $fontSum->numberFormat(\ExcelFormat::NUMFORMAT_NUMBER_SEP);
        $fontSum->setFont($font);
        $fontSum->horizontalAlign(\ExcelFormat::ALIGNH_CENTER);
        $fontSum->verticalAlign(\ExcelFormat::ALIGNV_CENTER);
        $fontSum->fillPattern(\ExcelFormat::FILLPATTERN_SOLID);
        $fontSum->patternForegroundColor(44);
        $fontSum->borderStyle(\ExcelFormat::BORDERSTYLE_THIN);
        $fontSum->borderColor(0);
        $fontSum->borderStyle(\ExcelFormat::BORDERSTYLE_THIN);

        $cur1 = $intExcelRow + 1;
        $cur2 = $intExcelRow + 2;

        if ($flagSheetActive == 0) {
            $s0->removeRow($intExcelRow, 250);

            $pic_file = \app\components\Templates::getTemplate('logo', Yii::$app->session->get('CompanyID') . '.PNG');
            if (!empty($pic_file)) {
                $pic = $xlBook->addPictureFromFile($pic_file);
                $s0->addPictureDim(1, 7, $pic, 65, 80);
//                $s0->addPictureScaled(0, 7, $pic, 0.4);
            }

            $s0->write((2), 3, strval($invoiceModel->contract->ContractNo . ' ' . $invoiceModel->contract->ContractSubject));
            $s0->write((3), 4, strval($invoiceModel->CutOffDateHijri));
            $s0->write((3), 6, strval($invoiceModel->Title));

            $s0->write(($intExcelRow), 3, "=SUMIF(G6:G$intExcelRow,G$cur1,D6:D$intExcelRow)", $fontSum, \ExcelFormat::AS_FORMULA);

            $s0->write(($intExcelRow), 4, "=SUMIF(G6:G$intExcelRow,G$cur1,E6:E$intExcelRow)", $fontSum, \ExcelFormat::AS_FORMULA);

            $s0->write(($intExcelRow), 5, "=SUMIF(G6:G$intExcelRow,G$cur1,F6:F$intExcelRow)", $fontSum, \ExcelFormat::AS_FORMULA);

            $s0->clearPrintArea();
            $xlBook->deleteSheet(1);
        } else {
            $s1->removeRow($intExcelRow, 250);

            $pic_file = \app\components\Templates::getTemplate('logo', Yii::$app->session->get('CompanyID') . '.PNG');
            if (!empty($pic_file)) {
                $pic = $xlBook->addPictureFromFile($pic_file);
                $s1->addPictureDim(1, 7, $pic, 65, 80);
//                $s1->addPictureScaled(0, 7, $pic, 0.4);
            }

            $s1->write((2), 3, strval($invoiceModel->contract->ContractNo . ' ' . $invoiceModel->contract->ContractSubject));
            $s1->write((3), 4, strval($invoiceModel->CutOffDateHijri));
            $s1->write((3), 6, strval($invoiceModel->Title));


            $s1->write(($intExcelRow), 3, "=SUMIF(G6:G$intExcelRow,G$cur1,D6:D$intExcelRow)", $fontSum, \ExcelFormat::AS_FORMULA);
            $s1->write(($intExcelRow + 1), 3, "=SUMIF(G6:G$intExcelRow,G$cur2,D6:D$intExcelRow)", $fontSum, \ExcelFormat::AS_FORMULA);

            $s1->write(($intExcelRow), 4, "=SUMIF(G6:G$intExcelRow,G$cur1,E6:E$intExcelRow)", $fontSum, \ExcelFormat::AS_FORMULA);
            $s1->write(($intExcelRow + 1), 4, "=SUMIF(G6:G$intExcelRow,G$cur2,E6:E$intExcelRow)", $fontSum, \ExcelFormat::AS_FORMULA);

            $s1->write(($intExcelRow), 5, "=SUMIF(G6:G$intExcelRow,G$cur1,F6:F$intExcelRow)", $fontSum, \ExcelFormat::AS_FORMULA);
            $s1->write(($intExcelRow + 1), 5, "=SUMIF(G6:G$intExcelRow,G$cur2,F6:F$intExcelRow)", $fontSum, \ExcelFormat::AS_FORMULA);
            $s1->clearPrintArea();
            $xlBook->deleteSheet(0);
        }

        header('Content-type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Export-Income-List-' . $dateNow . ' .xlsx"');
        $xlBook->save('php://output');

        $activityManagement = new \app\components\ActivityManagement();
        $actionType = "Report";
        $subType = "ExportFile";
        $subTypeDescription["ReportName"] = "DebitmemoReport";
        $subTypeDescription["FileName"] = "TemplateID20.xlsm";
        $subTypeDescription["FilePatch"] = \app\components\Templates::getTemplate('excel', 'TemplateID20.xlsm');
        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

        exit();
    }

    private function generateLetter60($letterFormModel, $invoiceModel)
    {
        $templatePath = Templates::getTemplate('word', "LetterID{$this->fileIdRef}.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $invoiceModel->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($invoiceModel->currency2->CurrencyPersianTitle) ? $invoiceModel->currency2->CurrencyPersianTitle : '');

        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        $word->setBookmarkValue('bmSubContractor', str_replace('&', '-', $invoiceModel->contract->subContractor->PersonTitle));
        $word->setBookmarkValue('bmInvoiceType', $invoiceModel->type->InvoiceType);
        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($invoiceModel->contract->ContractNo)));
        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($invoiceModel->Title, '-'));

        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $invoiceModel->contract->ContractSubject));   //@ToDo: در مقدار این فیلد هرگاه کاراکتر (&)باشد نامه کلا باز نمیشود  
        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($invoiceModel->CutOffDateHijri, '/'));

        if ($invoiceModel->contract->SignerPersonIDRef == 139) {
            $word->setBookmarkValue('bmManegment', 'مربوط به هزینه‏ بخش پیمانکاران شرکت تکنو انرژی سپهر کیش و با توجه به تائید مدیر عامل محترم این شرکت');
        } else {
            if ($letterFormModel->Evidence8Chek == 1) {
                $word->setBookmarkValue('bmManegment', 'با توجه به تائید مدیر محترم کالا و پشتیبانی ');
            } else {
                $word->setBookmarkValue('bmManegment', 'با توجه به تائید ریاست محترم کارگاه ');
            }
        }

        if ($invoiceModel->InvoiceType == 17 || $invoiceModel->InvoiceType == 18 || $invoiceModel->InvoiceType == 19 || $invoiceModel->InvoiceType == 20 || $invoiceModel->InvoiceType == 21) {
            $lateral = $this->cumulativeAditionalCost($invoiceModel);

            $word->setBookmarkValue('bmComulativeApproved1', '');
            $word->setBookmarkValue('bmComulativeApproved1Currency', '');
            $word->setBookmarkValue('bmComulativeApproved1Text', $lateral);

            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmApproved2', '');
            }
        } else {
            $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle));
            $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
            $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);


            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmComulativeApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle) . ' ' . $currency2PersianTitle
                    . ' (' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmComulativeApproved2', '');
                $word->setBookmarkValue('bmApproved2', '');
            }
        }


        $word->setBookmarkValue('bmApproved1', Utility::moneyFormatWord($invoiceModel->approved1, $currency1PersianTitle));

        $word->setBookmarkValue('bmApproved1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        if ($letterFormModel->Description) {
            $word->setVarValue('توضیحات', $this->convertNumbers($letterFormModel->Description));
            $word->setBookmarkValue('bmDescription', $this->convertNumbers($letterFormModel->Description));
        } else {
            $word->deleteBlock('بلاکتوضیحات');
        }

        //    جریمه
        if ($invoiceModel->cumulativePenalty1(6) <> 0 && $letterFormModel->Penalty == 1) {
            $contractPenalty1Text = trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle;

            $word->setVarValue('متنکاملبخشجرایم', "همچنین مبلغ تجمعی " . Utility::moneyFormatWord($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle) . " $currency1PersianTitle($contractPenalty1Text) بابت جرائم (مستندات پیوست) می بایستی از حساب پیمانکار فوق کسر گردد.");
        } else {
            $word->deleteBlock('بلاکجریمه');
        }

        // بدهی
        if ($invoiceModel->cumulativeIncome1(6) <> 0 && $letterFormModel->Income == 1) {
            $cumulative2 = '';
            if ($invoiceModel->cumulativeIncome2(6) <> 0 && $currency2PersianTitle <> '') {
                $cumulative2 = 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle) . " $currency2PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle, ''), 2) . " $currency2PersianTitle)";
            }
            $income = "لازم به ذکر است ليست بدهي پيمانکاران به مبلغ تجمعي ";
            $income .= Utility::moneyFormatWord($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle) . " $currency1PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle, ''), 2) . " $currency1PersianTitle)";
            $income .= $cumulative2;
            $income .= "و مبالغ دوره اي مربوطه، به پيوست اين صورت حساب جهت درج در حساب پيمانکاران ذکر شده ارسال مي گردد.";
            $word->setVarValue('متنکاملبخشبدهی', $income);
        } else {
            $word->deleteBlock('بلاکبدهی');
        }


        if ($letterFormModel->Evidence1Chek == 1) {
            $word->setBookmarkValue('bmEvidence1No', ($letterFormModel->Evidence1No));
            $word->setBookmarkValue('bmEvidence1Date', $this->convertNumbers($letterFormModel->Evidence1Date));
        } else {
            $word->deleteBlock("مستنداتپیمانکار");
        }

        if ($letterFormModel->Evidence8Chek == 1) {
            $word->setBookmarkValue('bmEvidence4No', ($letterFormModel->Evidence8No));
            $word->setBookmarkValue('bmEvidence4Date', $this->convertNumbers($letterFormModel->Evidence8Date));
        } else {
            $word->deleteBlock("مستنداتکالا");
        }

        if ($letterFormModel->Evidence2Chek == 1) {
            $word->setBookmarkValue('bmEvidence2No', ($letterFormModel->Evidence2No));
            $word->setBookmarkValue('bmEvidence2Date', $this->convertNumbers($letterFormModel->Evidence2Date));
        } else {
            $word->deleteBlock("مستنداتریاستکارگاه");
        }

        if ($letterFormModel->Evidence3Chek == 1) {
            $word->setBookmarkValue('bmEvidence3No', ($letterFormModel->Evidence3No));
            $word->setBookmarkValue('bmEvidence3Date', $this->convertNumbers($letterFormModel->Evidence3Date));
        } else {
            $word->deleteBlock("مستنداتمدیرهماهنگکننده");
        }

        //CC Part

        if ($letterFormModel->CcCheckModirAmelalc == 1) {
            $word->setVarValue('مدیرعامل', $letterFormModel->CcModirAmelalcPPI);
        } else {
            $word->deleteBlock("رونوشتمدیرعامل");
        }

        if ($letterFormModel->CcCheckRaeisKargah == 1) {
            $word->setVarValue('رییسکارگاه', $letterFormModel->CcRaeisKargahPPI);
        } else {
            $word->deleteBlock("رونوشترییسکارگاه");
        }

        if ($letterFormModel->CcCheckModirHoghooghi == 1) {
            $word->setVarValue('مدیرحقوقی', $letterFormModel->CcModirHoghooghiPPI);
        } else {
            $word->deleteBlock("رونوشتمدیرحقوقی");
        }

        if ($letterFormModel->CcCheckModirPeymankar == 1) {
            $word->setVarValue('مدیرپیمانکار', $letterFormModel->CcModirPeymankarPPI);
        } else {
            $word->deleteBlock("رونوشتمدیرپیمانکار");
        }

        if ($letterFormModel->CcCheckModirBarnameRizi == 1) {
            $word->setVarValue('مدیربرنامهریزی', $letterFormModel->CcModirBarnameRiziPPI);
        } else {
            $word->deleteBlock("رونوشتمدیربرنامهریزی");
        }

        if ($letterFormModel->CcCheckModirMohandesi == 1) {
            $word->setVarValue('مدیرمهندسی', $letterFormModel->CcModirMohandesiPPI);
        } else {
            $word->deleteBlock("رونوشتمدیرمهندسی");
        }

        $letterName = "LetterID{$this->fileIdRef}.docx";
        $activityManagement = new \app\components\ActivityManagement();
        $activityManagement->sendInvoiceLetterActitvityLog($letterName);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    private function generateLetter13($letterFormModel, $invoiceModel)
    {
        $templatePath = Templates::getTemplate('word', "LetterID{$this->fileIdRef}.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $invoiceModel->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($invoiceModel->currency2->CurrencyPersianTitle) ? $invoiceModel->currency2->CurrencyPersianTitle : '');

        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        $word->setBookmarkValue('bmSubContractor', str_replace('&', '-', $invoiceModel->contract->subContractor->PersonTitle));
        $word->setBookmarkValue('bmInvoiceType', $invoiceModel->type->InvoiceType);
        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($invoiceModel->contract->ContractNo)));
        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($invoiceModel->Title, '-'));

        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $invoiceModel->contract->ContractSubject));
        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($invoiceModel->CutOffDateHijri, '/'));


        if ($invoiceModel->InvoiceType == 17 || $invoiceModel->InvoiceType == 18 || $invoiceModel->InvoiceType == 19 || $invoiceModel->InvoiceType == 20 || $invoiceModel->InvoiceType == 21) {
            $lateral = $this->cumulativeAditionalCost($invoiceModel);

            $word->setBookmarkValue('bmComulativeApproved1', '');
            $word->setBookmarkValue('bmComulativeApproved1Currency', '');
            $word->setBookmarkValue('bmComulativeApproved1Text', $lateral);

            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmApproved2', '');
            }
        } else {
            $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle));
            $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
            $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);


            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmComulativeApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle) . ' ' . $currency2PersianTitle . ' (' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmComulativeApproved2', '');
            }
        }

        $word->setBookmarkValue('bmInvoiceTitleSecond', Utility::invertString($invoiceModel->Title, '-'));
        $word->setBookmarkValue('bmSubContractorSecond', str_replace('&', '-', $invoiceModel->contract->subContractor->PersonTitle));

        if ($letterFormModel->Description) {
            $word->setVarValue('توضیحات', $this->convertNumbers($letterFormModel->Description));
            $word->setBookmarkValue('bmDescription', $this->convertNumbers($letterFormModel->Description));
        } else {
            $word->deleteBlock('بلاکتوضیحات');
        }

        //    جریمه
        if ($invoiceModel->cumulativePenalty1(6) <> 0 && $letterFormModel->Penalty == 1) {
            $contractPenalty1Text = trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle;

            $word->setVarValue('متنکاملبخشجرایم', "همچنین مبلغ تجمعی " . Utility::moneyFormatWord($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle) . " $currency1PersianTitle($contractPenalty1Text) بابت جرائم (مستندات پیوست) می بایستی از حساب پیمانکار فوق کسر گردد.");
        } else {
            $word->deleteBlock('بلاکجریمه');
        }
        // بدهی

        if ($invoiceModel->cumulativeIncome1(6) <> 0 && $letterFormModel->Income == 1) {
            $cumulative2 = '';
            if ($invoiceModel->cumulativeIncome2(6) <> 0 && $currency2PersianTitle <> '') {
                $cumulative2 = 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle) . " $currency2PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle, ''), 2) . " $currency2PersianTitle)";
            }
            $income = "لازم به ذکر است ليست بدهي پيمانکاران به مبلغ تجمعي ";
            $income .= Utility::moneyFormatWord($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle) . " $currency1PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle, ''), 2) . " $currency1PersianTitle)";
            $income .= $cumulative2;
            $income .= "و مبالغ دوره اي مربوطه، به پيوست اين صورت وضعيت جهت درج در حساب پيمانکاران ذکر شده ارسال مي گردد.";
            $word->setVarValue('متنکاملبخشبدهی', $income);
        } else {
            $word->deleteBlock('بلاکبدهی');
        }

        $letterName = "LetterID{$this->fileIdRef}.docx";
        $activityManagement = new \app\components\ActivityManagement();
        $activityManagement->sendInvoiceLetterActitvityLog($letterName);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    private function generateLetter15($letterFormModel, $invoiceModel)
    {
        $templatePath = Templates::getTemplate('word', "LetterID{$this->fileIdRef}.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $invoiceModel->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($invoiceModel->currency2->CurrencyPersianTitle) ? $invoiceModel->currency2->CurrencyPersianTitle : '');

        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        $word->setBookmarkValue('bmSubContractor', str_replace('&', '-', $invoiceModel->contract->subContractor->PersonTitle));
        $word->setBookmarkValue('bmInvoiceType', $invoiceModel->type->InvoiceType);
        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($invoiceModel->contract->ContractNo)));
        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($invoiceModel->Title, '-'));

        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $invoiceModel->contract->ContractSubject));   //@ToDo: در مقدار این فیلد هرگاه کاراکتر (&)باشد نامه کلا باز نمیشود  
        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($invoiceModel->CutOffDateHijri, '/'));


        if ($invoiceModel->InvoiceType == 17 || $invoiceModel->InvoiceType == 18 || $invoiceModel->InvoiceType == 19 || $invoiceModel->InvoiceType == 20 || $invoiceModel->InvoiceType == 21) {
            $lateral = $this->cumulativeAditionalCost($invoiceModel);

            $word->setBookmarkValue('bmComulativeApproved1', '');
            $word->setBookmarkValue('bmComulativeApproved1Currency', '');
            $word->setBookmarkValue('bmComulativeApproved1Text', $lateral);

            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmApproved2', '');
            }
        } else {
            $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle));
            $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
            $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);


            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmComulativeApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle) . ' ' . $currency2PersianTitle
                    . ' (' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmComulativeApproved2', '');
                $word->setBookmarkValue('bmApproved2', '');
            }
        }

        $word->setBookmarkValue('bmApproved1', Utility::moneyFormatWord($invoiceModel->approved1, $currency1PersianTitle));

        $word->setBookmarkValue('bmApproved1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        // بررسي وجود هزينه بین شرکتی
        $modelInvoice = InvoiceEdit::amountByOwnerAndProject($invoiceModel->InvoiceID);
        if (count($modelInvoice) > 1) {
            $costTable = '<w:tbl><w:tblPr><w:bidiVisual/><w:tblW w:w="0" w:type="auto"/><w:tblBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:left w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:right w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:insideH w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:insideV w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tblBorders><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="2331"/><w:gridCol w:w="1906"/><w:gridCol w:w="664"/><w:gridCol w:w="2155"/><w:gridCol w:w="2187"/></w:tblGrid>';
            $costTable .= '<w:tr w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidTr="00820A6E"><w:trPr><w:trHeight w:val="810"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="2331" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008037F1" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="24"/><w:szCs w:val="24"/><w:rtl/></w:rPr></w:pPr><w:bookmarkStart w:id="0" w:name="bmInvoiceAmountByOwner"/><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>نام شرکت</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1906" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>عنوان پروژه</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="664" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr><w:t>واحد</w:t></w:r></w:p><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr><w:t>پولی</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2155" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>مبلغ دوره ای</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2187" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:ind w:firstLine="162"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>مبلغ تجمعی</w:t></w:r></w:p></w:tc></w:tr>';

            foreach ($modelInvoice as $value) {
                $costTable .= '<w:tr w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidTr="00820A6E"><w:trPr><w:trHeight w:val="454"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="2331" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr><w:t>' . $value['mainOwnerTitle'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1906" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $value['nodeText'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="664" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $value['currencyTitle'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2155" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . Utility::moneyFormat($value['approved']) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2187" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . Utility::moneyFormat($value['cumulativeApproved']) . '</w:t></w:r></w:p></w:tc></w:tr>';
            }
            $costTable .= '</w:tbl>';
            $word->setBookmarkValue('bmInvoiceAmountByOwner', $costTable);
        } else {
            $word->deleteBlock('بلاکهزینه');
        }

        if ($letterFormModel->Description) {
            $word->setVarValue('توضیحات', $this->convertNumbers($letterFormModel->Description));
            $word->setBookmarkValue('bmDescription', $this->convertNumbers($letterFormModel->Description));
        } else {
            $word->deleteBlock('بلاکتوضیحات');
        }

        //    جریمه
        if ($invoiceModel->cumulativePenalty1(6) <> 0 && $letterFormModel->Penalty == 1) {
            $contractPenalty1Text = trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle;

            $word->setVarValue('متنکاملبخشجرایم', "همچنین مبلغ تجمعی " . Utility::moneyFormatWord($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle) . " $currency1PersianTitle($contractPenalty1Text) بابت جرائم (مستندات پیوست) می بایستی از حساب پیمانکار فوق کسر گردد.");
        } else {
            $word->deleteBlock('بلاکجریمه');
        }
        // بدهی

        if ($invoiceModel->cumulativeIncome1(6) <> 0 && $letterFormModel->Income == 1) {
            $cumulative2 = '';
            if ($invoiceModel->cumulativeIncome2(6) <> 0 && $currency2PersianTitle <> '') {
                $cumulative2 = 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle) . " $currency2PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle, ''), 2) . " $currency2PersianTitle)";
            }
            $income = "لازم به ذکر است ليست بدهي پيمانکاران به مبلغ تجمعي ";
            $income .= Utility::moneyFormatWord($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle) . " $currency1PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle, ''), 2) . " $currency1PersianTitle)";
            $income .= $cumulative2;
            $income .= "و مبالغ دوره اي مربوطه، به پيوست اين صورت وضعيت جهت درج در حساب پيمانکاران ذکر شده ارسال مي گردد.";
            $word->setVarValue('متنکاملبخشبدهی', $income);
        } else {
            $word->deleteBlock('بلاکبدهی');
        }


        if ($letterFormModel->Evidence1Chek == 1) {
            $word->setBookmarkValue('bmEvidence1No', ($letterFormModel->Evidence1No));
            $word->setBookmarkValue('bmEvidence1Date', $this->convertNumbers($letterFormModel->Evidence1Date));
        } else {
            $word->deleteBlock("مستنداتپیمانکار");
        }

        if ($letterFormModel->Evidence2Chek == 1) {
            $word->setBookmarkValue('bmEvidence2No', ($letterFormModel->Evidence2No));
            $word->setBookmarkValue('bmEvidence2Date', $this->convertNumbers($letterFormModel->Evidence2Date));
        } else {
            $word->deleteBlock("مستنداتریاستکارگاه");
        }

        if ($letterFormModel->Evidence3Chek == 1) {
            $word->setBookmarkValue('bmEvidence3No', ($letterFormModel->Evidence3No));
            $word->setBookmarkValue('bmEvidence3Date', $this->convertNumbers($letterFormModel->Evidence3Date));
        } else {
            $word->deleteBlock("مستنداتمدیرهماهنگکننده");
        }

        //CC Part

        if ($letterFormModel->CcCheckModirAmelalc == 1) {
            $word->setVarValue('مدیرعامل', $letterFormModel->CcModirAmelalc);
        } else {
            $word->deleteBlock("رونوشتمدیرعامل");
        }

        if ($letterFormModel->CcCheckRaeisKargah == 1) {
            $word->setVarValue('رییسکارگاه', $letterFormModel->CcRaeisKargah);
        } else {
            $word->deleteBlock("رونوشترییسکارگاه");
        }

        if ($letterFormModel->CcCheckModirHoghooghi == 1) {
            $word->setVarValue('مدیرحقوقی', $letterFormModel->CcModirHoghooghi);
        } else {
            $word->deleteBlock("رونوشتمدیرحقوقی");
        }

        if ($letterFormModel->CcCheckModirPeymankar == 1) {
            $word->setVarValue('مدیرپیمانکار', $letterFormModel->CcModirPeymankar);
        } else {
            $word->deleteBlock("رونوشتمدیرپیمانکار");
        }

        if ($letterFormModel->CcCheckModirBarnameRizi == 1) {
            $word->setVarValue('مدیربرنامهریزی', $letterFormModel->CcModirBarnameRizi);
        } else {
            $word->deleteBlock("رونوشتمدیربرنامهریزی");
        }

        if ($letterFormModel->CcCheckModirMohandesi == 1) {
            $word->setVarValue('مدیرمهندسی', $letterFormModel->CcModirMohandesi);
        } else {
            $word->deleteBlock("رونوشتمدیرمهندسی");
        }

        $letterName = "LetterID{$this->fileIdRef}.docx";
        $activityManagement = new \app\components\ActivityManagement();
        $activityManagement->sendInvoiceLetterActitvityLog($letterName);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    private function generateLetter21($letterFormModel, $invoiceModel)
    {
        $templatePath = Templates::getTemplate('word', "LetterID{$this->fileIdRef}.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $invoiceModel->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($invoiceModel->currency2->CurrencyPersianTitle) ? $invoiceModel->currency2->CurrencyPersianTitle : '');

        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        $word->setBookmarkValue('bmSubContractor', str_replace('&', '-', $invoiceModel->contract->subContractor->PersonTitle));
        $word->setBookmarkValue('bmInvoiceType', $invoiceModel->type->InvoiceType);
        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($invoiceModel->contract->ContractNo)));
        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($invoiceModel->Title, '-'));

        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $invoiceModel->contract->ContractSubject));   //@ToDo: در مقدار این فیلد هرگاه کاراکتر (&)باشد نامه کلا باز نمیشود  
        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($invoiceModel->CutOffDateHijri, '/'));

        if ($invoiceModel->InvoiceType == 17 || $invoiceModel->InvoiceType == 18 || $invoiceModel->InvoiceType == 19 || $invoiceModel->InvoiceType == 20 || $invoiceModel->InvoiceType == 21) {
            $lateral = $this->cumulativeAditionalCost($invoiceModel);

            $word->setBookmarkValue('bmComulativeApproved1', '');
            $word->setBookmarkValue('bmComulativeApproved1Currency', '');
            $word->setBookmarkValue('bmComulativeApproved1Text', $lateral);

            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmApproved2', '');
            }
        } else {
            $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle));
            $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
            $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmComulativeApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle) . ' ' . $currency2PersianTitle . ' (' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmComulativeApproved2', '');
            }
        }

        //    جریمه
        if ($invoiceModel->cumulativePenalty1(6) <> 0 && $letterFormModel->Penalty == 1) {
            $contractPenalty1Text = trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle;

            $word->setVarValue('متنکاملبخشجرایم', "همچنین مبلغ تجمعی " . Utility::moneyFormatWord($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle) . " $currency1PersianTitle($contractPenalty1Text) بابت جرائم (مستندات پیوست) می بایستی از حساب پیمانکار فوق کسر گردد.");
        } else {
            $word->deleteBlock('بلاکجریمه');
        }
        // بدهی

        if ($invoiceModel->cumulativeIncome1(6) <> 0 && $letterFormModel->Income == 1) {
            $cumulative2 = '';
            if ($invoiceModel->cumulativeIncome2(6) <> 0 && $currency2PersianTitle <> '') {
                $cumulative2 = 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle) . " $currency2PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle, ''), 2) . " $currency2PersianTitle)";
            }
            $income = "لازم به ذکر است ليست بدهي پيمانکاران به مبلغ تجمعي ";
            $income .= Utility::moneyFormatWord($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle) . " $currency1PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle, ''), 2) . " $currency1PersianTitle)";
            $income .= $cumulative2;
            $income .= "و مبالغ دوره اي مربوطه، به پيوست اين صورت وضعيت جهت درج در حساب پيمانکاران ذکر شده ارسال مي گردد.";
            $word->setVarValue('متنکاملبخشبدهی', $income);
        } else {
            $word->deleteBlock('بلاکبدهی');
        }

        if ($letterFormModel->Description) {
            $word->setVarValue('توضیحات', $this->convertNumbers($letterFormModel->Description));
            $word->setBookmarkValue('bmDescription', $this->convertNumbers($letterFormModel->Description));
        } else {
            $word->deleteBlock('بلاکتوضیحات');
        }

        $letterName = "LetterID{$this->fileIdRef}.docx";
        $activityManagement = new \app\components\ActivityManagement();
        $activityManagement->sendInvoiceLetterActitvityLog($letterName);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    private function generateLetter22($letterFormModel, $invoiceModel)
    {
        $templatePath = Templates::getTemplate('word', "LetterID{$this->fileIdRef}.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $invoiceModel->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($invoiceModel->currency2->CurrencyPersianTitle) ? $invoiceModel->currency2->CurrencyPersianTitle : '');


        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        $word->setBookmarkValue('bmSubContractor', str_replace('&', '-', $invoiceModel->contract->subContractor->PersonTitle));
        $word->setBookmarkValue('bmInvoiceType', $invoiceModel->type->InvoiceType);
        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($invoiceModel->contract->ContractNo)));
        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($invoiceModel->Title, '-'));

        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $invoiceModel->contract->ContractSubject));   //@ToDo: در مقدار این فیلد هرگاه کاراکتر (&)باشد نامه کلا باز نمیشود  
        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($invoiceModel->CutOffDateHijri, '/'));

        if ($invoiceModel->InvoiceType == 17 || $invoiceModel->InvoiceType == 18 || $invoiceModel->InvoiceType == 19 || $invoiceModel->InvoiceType == 20 || $invoiceModel->InvoiceType == 21) {
            $lateral = $this->cumulativeAditionalCost($invoiceModel);

            $word->setBookmarkValue('bmComulativeApproved1', '');
            $word->setBookmarkValue('bmComulativeApproved1Currency', '');
            $word->setBookmarkValue('bmComulativeApproved1Text', $lateral);

            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmApproved2', '');
            }
        } else {
            $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle));
            $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
            $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmComulativeApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle) . ' ' . $currency2PersianTitle . ' (' . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle, ''), 2) . ' ' . $currency2PersianTitle . ')');
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmComulativeApproved2', '');
                $word->setBookmarkValue('bmApproved2', '');
            }
        }

        $word->setBookmarkValue('bmApproved1', Utility::moneyFormatWord($invoiceModel->approved1, $currency1PersianTitle));
        $word->setBookmarkValue('bmApproved1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        // بررسي وجود هزينه در فاز 13
        $phase13ComulativeApproved1 = $invoiceModel->cumulativeApproved1ByCostCode(6, '101104');
        $phase2224ComulativeApproved1 = $invoiceModel->cumulativeApproved1(6) - $phase13ComulativeApproved1;
        $phase13Approved1 = $invoiceModel->approved1ByCostCode('101104');
        $phase2224Approved1 = $invoiceModel->approved1 - $phase13Approved1;
        if ($phase13ComulativeApproved1 != 0) {
            $word->setBookmarkValue('phase2224ComulativeApproved1', Utility::moneyFormatWord($phase2224ComulativeApproved1, $currency1PersianTitle) . ' ' . $currency1PersianTitle);
            $word->setBookmarkValue('phase2224Approved1', Utility::moneyFormatWord($phase2224Approved1, $currency1PersianTitle) . ' ' . $currency1PersianTitle);
            $word->setBookmarkValue('phase13ComulativeApproved1', Utility::moneyFormatWord($phase13ComulativeApproved1, $currency1PersianTitle) . ' ' . $currency1PersianTitle);
            $word->setBookmarkValue('phase13Approved1', Utility::moneyFormatWord($phase13Approved1, $currency1PersianTitle) . ' ' . $currency1PersianTitle);

            $phase13ComulativeApproved2 = $invoiceModel->cumulativeApproved2ByCostCode(6, '101104');
            $phase2224ComulativeApproved2 = $invoiceModel->cumulativeApproved2(6) - $phase13ComulativeApproved2;
            $phase13Approved2 = $invoiceModel->approved2ByCostCode('101104');
            $phase2224Approved2 = $invoiceModel->approved2 - $phase13Approved2;
            $word->setBookmarkValue('phase2224ComulativeApproved2', Utility::moneyFormatWord($phase2224ComulativeApproved2, (isset($currency2PersianTitle)) ? $currency2PersianTitle : 0)
                . ' ' . (isset($currency2PersianTitle) ? $currency2PersianTitle : ''));
            $word->setBookmarkValue('phase2224Approved2', Utility::moneyFormatWord($phase2224Approved2, (isset($currency2PersianTitle)) ? $currency2PersianTitle : 0)
                . ' ' . (isset($currency2PersianTitle) ? $currency2PersianTitle : ''));
            $word->setBookmarkValue('phase13ComulativeApproved2', Utility::moneyFormatWord($phase13ComulativeApproved2, (isset($currency2PersianTitle)) ? $currency2PersianTitle : 0)
                . ' ' . (isset($currency2PersianTitle) ? $currency2PersianTitle : ''));
            $word->setBookmarkValue('phase13Approved2', Utility::moneyFormatWord($phase13Approved2, (isset($currency2PersianTitle)) ? $currency2PersianTitle : 0)
                . ' ' . (isset($currency2PersianTitle) ? $currency2PersianTitle : ''));
        } else {
            $word->deleteBlock('بلاکهزینه');
        }

        if ($letterFormModel->Description) {
            $word->setVarValue('توضیحات', $this->convertNumbers($letterFormModel->Description));
            $word->setBookmarkValue('bmDescription', $this->convertNumbers($letterFormModel->Description));
        } else {
            $word->deleteBlock('بلاکتوضیحات');
        }

        //             جریمه
        if ($invoiceModel->cumulativePenalty1(6) <> 0 && $letterFormModel->Penalty == 1) {
            $contractPenalty1Text = trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle;
            $word->setVarValue('متنکاملبخشجرایم', "همچنین مبلغ تجمعی " . Utility::moneyFormatWord($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle) . " $currency1PersianTitle($contractPenalty1Text) بابت جرائم (مستندات پیوست) می بایستی از حساب پیمانکار فوق کسر گردد.");
        } else {
            $word->deleteBlock('بلاکجریمه');
        }

        if ($invoiceModel->cumulativeIncome1(6) <> 0 && $letterFormModel->Income == 1) {
            $cumulative2 = '';
            if ($invoiceModel->cumulativeIncome2(6) <> 0 && $currency2PersianTitle <> '') {
                $cumulative2 = 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle) . " $currency2PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle, ''), 2) . " $currency2PersianTitle)";
            }
            $income = "لازم به ذکر است ليست بدهي پيمانکاران به مبلغ تجمعي ";
            $income .= Utility::moneyFormatWord($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle) . " $currency1PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle, ''), 2) . " $currency1PersianTitle)";
            $income .= $cumulative2;
            $income .= "و مبالغ دوره اي مربوطه، به پيوست اين صورت وضعيت جهت درج در حساب پيمانکاران ذکر شده ارسال مي گردد.";
            $word->setVarValue('متنکاملبخشبدهی', $income);
        } else {
            $word->deleteBlock("بلاکبدهی");
        }

        if ($letterFormModel->Evidence1Chek == 1) {
            $word->setBookmarkValue('bmEvidence1No', ($letterFormModel->Evidence1No));
            $word->setBookmarkValue('bmEvidence1Date', $this->convertNumbers($letterFormModel->Evidence1Date));
        } else {
            $word->deleteBlock("مستنداتپیمانکار");
        }

        if ($letterFormModel->Evidence2Chek == 1) {
            $word->setBookmarkValue('bmEvidence2No', ($letterFormModel->Evidence2No));
            $word->setBookmarkValue('bmEvidence2Date', $this->convertNumbers($letterFormModel->Evidence2Date));
        } else {
            $word->deleteBlock("مستنداتریاستکارگاه");
        }

        if ($letterFormModel->Evidence3Chek == 1) {
            $word->setBookmarkValue('bmEvidence3No', ($letterFormModel->Evidence3No));
            $word->setBookmarkValue('bmEvidence3Date', $this->convertNumbers($letterFormModel->Evidence3Date));
        } else {
            $word->deleteBlock("مستنداتمدیرهماهنگکننده");
        }

        //  CC Part 

        if ($letterFormModel->CcCheckModirAmelalc == 1) {
            $word->setVarValue('مدیرعامل', $letterFormModel->CcModirAmelalc);
        } else {
            $word->deleteBlock("رونوشتمدیرعامل");
        }

        if ($letterFormModel->CcCheckRaeisKargah == 1) {
            $word->setVarValue('رییسکارگاه', $letterFormModel->CcRaeisKargah);
        } else {
            $word->deleteBlock("رونوشترییسکارگاه");
        }

        if ($letterFormModel->CcCheckModirHoghooghi == 1) {
            $word->setVarValue('مدیرحقوقی', $letterFormModel->CcModirHoghooghi);
        } else {
            $word->deleteBlock("رونوشتمدیرحقوقی");
        }

        if ($letterFormModel->CcCheckModirPeymankar == 1) {
            $word->setVarValue('مدیرپیمانکار', $letterFormModel->CcModirPeymankar);
        } else {
            $word->deleteBlock("رونوشتمدیرپیمانکار");
        }

        if ($letterFormModel->CcCheckModirBarnameRizi == 1) {
            $word->setVarValue('مدیربرنامهریزی', $letterFormModel->CcModirBarnameRizi);
        } else {
            $word->deleteBlock("رونوشتمدیربرنامهریزی");
        }

        if ($letterFormModel->CcCheckModirMohandesi == 1) {
            $word->setVarValue('مدیرمهندسی', $letterFormModel->CcModirMohandesi);
        } else {
            $word->deleteBlock("رونوشتمدیرمهندسی");
        }
        $letterName = "LetterID{$this->fileIdRef}.docx";
        $activityManagement = new \app\components\ActivityManagement();
        $activityManagement->sendInvoiceLetterActitvityLog($letterName);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    private function generateLetter28($letterFormModel, $invoiceModel)
    {
        $templatePath = Templates::getTemplate('word', "LetterID{$this->fileIdRef}.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $invoiceModel->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($invoiceModel->currency2->CurrencyPersianTitle) ? $invoiceModel->currency2->CurrencyPersianTitle : '');

        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        $word->setBookmarkValue('bmSubContractor', str_replace('&', '-', $invoiceModel->contract->subContractor->PersonTitle));
        $word->setBookmarkValue('bmInvoiceType', $invoiceModel->type->InvoiceType);
        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($invoiceModel->contract->ContractNo)));
        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($invoiceModel->Title, '-'));

        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $invoiceModel->contract->ContractSubject));   //@ToDo: در مقدار این فیلد هرگاه کاراکتر (&)باشد نامه کلا باز نمیشود  
        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($invoiceModel->CutOffDateHijri, '/'));

        if ($invoiceModel->InvoiceType == 17 || $invoiceModel->InvoiceType == 18 || $invoiceModel->InvoiceType == 19 || $invoiceModel->InvoiceType == 20 || $invoiceModel->InvoiceType == 21) {
            $lateral = $this->cumulativeAditionalCost($invoiceModel);

            $word->setBookmarkValue('bmComulativeApproved1', '');
            $word->setBookmarkValue('bmComulativeApproved1Currency', '');
            $word->setBookmarkValue('bmComulativeApproved1Text', $lateral);

            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmApproved2', '');
            }
        } else {
            $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle));
            $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
            $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmComulativeApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle) . ' ' . $currency2PersianTitle . ' (' . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle, ''), 2) . ' ' . $currency2PersianTitle . ')');
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmComulativeApproved2', '');
                $word->setBookmarkValue('bmApproved2', '');
            }
        }

        $word->setBookmarkValue('bmApproved1', Utility::moneyFormatWord($invoiceModel->approved1, $currency1PersianTitle));
        $word->setBookmarkValue('bmApproved1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        $jDate = new jDateTime(true, true, 'Asia/Tehran');
        $nowDateFa = $jDate->date("Y/m/d-Hi", false, false);

        $word->setBookmarkValue('bmLegalDate', $this->convertNumbers($nowDateFa));

        $phase13ComulativeApproved1 = $invoiceModel->cumulativeApproved1ByCostCode(6, '101104');
        $phase2224ComulativeApproved1 = $invoiceModel->cumulativeApproved1(6) - $phase13ComulativeApproved1;
        $phase13Approved1 = $invoiceModel->approved1ByCostCode('101104');
        $phase2224Approved1 = $invoiceModel->approved1 - $phase13Approved1;
        if ($phase13ComulativeApproved1 != 0) {
            $word->setBookmarkValue('phase2224ComulativeApproved1', Utility::moneyFormatWord($phase2224ComulativeApproved1, $currency1PersianTitle) . ' ' . $currency1PersianTitle);
            $word->setBookmarkValue('phase2224Approved1', Utility::moneyFormatWord($phase2224Approved1, $currency1PersianTitle) . ' ' . $currency1PersianTitle);
            $word->setBookmarkValue('phase13ComulativeApproved1', Utility::moneyFormatWord($phase13ComulativeApproved1, $currency1PersianTitle) . ' ' . $currency1PersianTitle);
            $word->setBookmarkValue('phase13Approved1', Utility::moneyFormatWord($phase13Approved1, $currency1PersianTitle) . ' ' . $currency1PersianTitle);

            $phase13ComulativeApproved2 = $invoiceModel->cumulativeApproved2ByCostCode(6, '101104');
            $phase2224ComulativeApproved2 = $invoiceModel->cumulativeApproved2(6) - $phase13ComulativeApproved2;
            $phase13Approved2 = $invoiceModel->approved2ByCostCode('101104');
            $phase2224Approved2 = $invoiceModel->approved2 - $phase13Approved2;
            $word->setBookmarkValue('phase2224ComulativeApproved2', Utility::moneyFormatWord($phase2224ComulativeApproved2, (isset($currency2PersianTitle)) ? $currency2PersianTitle : 0)
                . ' ' . (isset($currency2PersianTitle) ? $currency2PersianTitle : ''));
            $word->setBookmarkValue('phase2224Approved2', Utility::moneyFormatWord($phase2224Approved2, (isset($currency2PersianTitle)) ? $currency2PersianTitle : 0)
                . ' ' . (isset($currency2PersianTitle) ? $currency2PersianTitle : ''));
            $word->setBookmarkValue('phase13ComulativeApproved2', Utility::moneyFormatWord($phase13ComulativeApproved2, (isset($currency2PersianTitle)) ? $currency2PersianTitle : 0)
                . ' ' . (isset($currency2PersianTitle) ? $currency2PersianTitle : ''));
            $word->setBookmarkValue('phase13Approved2', Utility::moneyFormatWord($phase13Approved2, (isset($currency2PersianTitle)) ? $currency2PersianTitle : 0)
                . ' ' . (isset($currency2PersianTitle) ? $currency2PersianTitle : ''));
        } else {
            $word->deleteBlock('بلاکهزینه');
        }

        if ($letterFormModel->Description) {
            $word->setVarValue('توضیحات', $this->convertNumbers($letterFormModel->Description));
            $word->setBookmarkValue('bmDescription', $this->convertNumbers($letterFormModel->Description));
        } else {
            $word->deleteBlock('بلاکتوضیحات');
        }


        if ($invoiceModel->cumulativePenalty1(6) <> 0 && $letterFormModel->Penalty == 1) {
            $contractPenalty1Text = trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle;
            $word->setVarValue('متنکاملبخشجرایم', "همچنین مبلغ تجمعی " . Utility::moneyFormatWord($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle) . " $currency1PersianTitle($contractPenalty1Text) بابت جرائم (مستندات پیوست) می بایستی از حساب پیمانکار فوق کسر گردد.");
        } else {
            $word->deleteBlock('بلاکجریمه');
        }


        if ($invoiceModel->cumulativeIncome1(6) <> 0 && $letterFormModel->Income == 1) {
            $cumulative2 = '';
            if ($invoiceModel->cumulativeIncome2(6) <> 0 && $currency2PersianTitle <> '') {
                $cumulative2 = 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle) . " $currency2PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle, ''), 2) . " $currency2PersianTitle)";
            }
            $income = "لازم به ذکر است ليست بدهي پيمانکاران به مبلغ تجمعي ";
            $income .= Utility::moneyFormatWord($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle) . " $currency1PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle, ''), 2) . " $currency1PersianTitle)";
            $income .= $cumulative2;
            $income .= "و مبالغ دوره اي مربوطه، به پيوست اين صورت وضعيت جهت درج در حساب پيمانکاران ذکر شده ارسال مي گردد.";
            $word->setVarValue('متنکاملبخشبدهی', $income);
        } else {
            $word->deleteBlock("بلاکبدهی");
        }

        if ($letterFormModel->Evidence1Chek == 1) {
            $word->setBookmarkValue('bmEvidence1No', ($letterFormModel->Evidence1No));
            $word->setBookmarkValue('bmEvidence1Date', $this->convertNumbers($letterFormModel->Evidence1Date));
        } else {
            $word->deleteBlock("مستنداتپیمانکار");
        }

        if ($letterFormModel->Evidence2Chek == 1) {
            $word->setBookmarkValue('bmEvidence2No', ($letterFormModel->Evidence2No));
            $word->setBookmarkValue('bmEvidence2Date', $this->convertNumbers($letterFormModel->Evidence2Date));
        } else {
            $word->deleteBlock("مستنداتریاستکارگاه");
        }

        if ($letterFormModel->Evidence3Chek == 1) {
            $word->setBookmarkValue('bmEvidence3No', ($letterFormModel->Evidence3No));
            $word->setBookmarkValue('bmEvidence3Date', $this->convertNumbers($letterFormModel->Evidence3Date));
        } else {
            $word->deleteBlock("مستنداتمدیرهماهنگکننده");
        }
        $letterName = "LetterID{$this->fileIdRef}.docx";
        $activityManagement = new \app\components\ActivityManagement();
        $activityManagement->sendInvoiceLetterActitvityLog($letterName);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    // از این گزارش دیده نشده استفاده شود
    private function generateLetter31($letterFormModel, $invoiceModel)
    {
        $templatePath = Templates::getTemplate('word', "LetterID{$this->fileIdRef}.docx");
        $word = new phpWordCando($templatePath);
        $currency1PersianTitle = $invoiceModel->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($invoiceModel->currency2->CurrencyPersianTitle) ? $invoiceModel->currency2->CurrencyPersianTitle : '');


        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        $word->setBookmarkValue('bmSubContractor', str_replace('&', '-', $invoiceModel->contract->subContractor->PersonTitle));
        $word->setBookmarkValue('bmInvoiceType', $invoiceModel->type->InvoiceType);
        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($invoiceModel->contract->ContractNo)));
        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($invoiceModel->Title, '-'));

        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $invoiceModel->contract->ContractSubject));   //@ToDo: در مقدار این فیلد هرگاه کاراکتر (&)باشد نامه کلا باز نمیشود  
        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($invoiceModel->CutOffDateHijri, '/'));

        if ($invoiceModel->InvoiceType == 17 || $invoiceModel->InvoiceType == 18 || $invoiceModel->InvoiceType == 19 || $invoiceModel->InvoiceType == 20 || $invoiceModel->InvoiceType == 21) {
            $lateral = $this->cumulativeAditionalCost($invoiceModel);

            $word->setBookmarkValue('bmComulativeApproved1', '');
            $word->setBookmarkValue('bmComulativeApproved1Currency', '');
            $word->setBookmarkValue('bmComulativeApproved1Text', $lateral);

            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmApproved2', '');
            }
        } else {
            $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle));
            $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
            $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmComulativeApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle) . ' ' . $currency2PersianTitle . ' (' . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle, ''), 2) . ' ' . $currency2PersianTitle . ')');
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmComulativeApproved2', '');
                $word->setBookmarkValue('bmApproved2', '');
            }
        }

        $word->setBookmarkValue('bmApproved1', Utility::moneyFormatWord($invoiceModel->approved1, $currency1PersianTitle));
        $word->setBookmarkValue('bmApproved1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        $letterName = "LetterID{$this->fileIdRef}.docx";
        $activityManagement = new \app\components\ActivityManagement();
        $activityManagement->sendInvoiceLetterActitvityLog($letterName);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    private function generateLetter32($letterFormModel, $invoiceModel)
    {
        $templatePath = Templates::getTemplate('word', "LetterID{$this->fileIdRef}.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $invoiceModel->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($invoiceModel->currency2->CurrencyPersianTitle) ? $invoiceModel->currency2->CurrencyPersianTitle : '');

        //            متغییر
        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        $word->setBookmarkValue('bmSubContractor', str_replace('&', '-', $invoiceModel->contract->subContractor->PersonTitle));
        $word->setBookmarkValue('bmInvoiceType', $invoiceModel->type->InvoiceType);
        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($invoiceModel->contract->ContractNo)));
        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($invoiceModel->Title, '-'));

        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $invoiceModel->contract->ContractSubject));
        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($invoiceModel->CutOffDateHijri, '/'));

        if ($invoiceModel->InvoiceType == 17 || $invoiceModel->InvoiceType == 18 || $invoiceModel->InvoiceType == 19 || $invoiceModel->InvoiceType == 20 || $invoiceModel->InvoiceType == 21) {
            $lateral = $this->cumulativeAditionalCost($invoiceModel);

            $word->setBookmarkValue('bmComulativeApproved1', '');
            $word->setBookmarkValue('bmComulativeApproved1Currency', '');
            $word->setBookmarkValue('bmComulativeApproved1Text', $lateral);

            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmApproved2', '');
            }
        } else {
            $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle));
            $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
            $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmComulativeApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle) . ' ' . $currency2PersianTitle . ' (' . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle, ''), 2) . ' ' . $currency2PersianTitle . ')');
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmComulativeApproved2', '');
                $word->setBookmarkValue('bmApproved2', '');
            }
        }

        $word->setBookmarkValue('bmApproved1', Utility::moneyFormatWord($invoiceModel->approved1, $currency1PersianTitle));
        $word->setBookmarkValue('bmApproved1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        if ($letterFormModel->Description) {
            $word->setBookmarkValue('bmDescription', $this->convertNumbers($letterFormModel->Description));
        } else {
            $word->deleteBlock('بلاکتوضیحات');
        }
//             جریمه

        if ($invoiceModel->cumulativePenalty1(6) <> 0 && $letterFormModel->Penalty == 1) {
            $contractPenalty1Text = trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle;
            $word->setVarValue('متنکاملبخشجرایم', "همچنین مبلغ تجمعی " . Utility::moneyFormatWord($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle) . " $currency1PersianTitle($contractPenalty1Text) بابت جرائم (مستندات پیوست) می بایستی از حساب پیمانکار فوق کسر گردد.");
        } else {
            $word->deleteBlock('بلاکجریمه');
        }

//          بدهی
        if ($invoiceModel->cumulativeIncome1(6) <> 0 && $letterFormModel->Income == 1) {
            $cumulative2 = '';
            if ($invoiceModel->cumulativeIncome2(6) <> 0 && $currency2PersianTitle <> '') {
                $cumulative2 = 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle) . " $currency2PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle, ''), 2) . " $currency2PersianTitle)";
            }
            $income = "لازم به ذکر است ليست بدهي پيمانکاران به مبلغ تجمعي ";
            $income .= Utility::moneyFormatWord($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle) . " $currency1PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle, ''), 2) . " $currency1PersianTitle)";
            $income .= $cumulative2;
            $income .= "و مبالغ دوره اي مربوطه، به پيوست اين صورت وضعيت جهت درج در حساب پيمانکاران ذکر شده ارسال مي گردد.";
            $word->setVarValue('متنکاملبخشبدهی', $income);
        } else {
            $word->deleteBlock("بلاکبدهی");
        }

//       ٍEvidence
        if ($letterFormModel->Evidence6Chek == 1) {
//            $word->setBookmarkValue('bmEvidence6No', $this->convertNumbers($letterFormModel->Evidence6No));
            $word->setBookmarkValue('bmEvidence6No', ($letterFormModel->Evidence6No));
            $word->setBookmarkValue('bmEvidence6Date', $this->convertNumbers($letterFormModel->Evidence6Date));
        } else {
            $word->deleteBlock("مستنداتمدیرمهندسی");
        }

        $letterName = "LetterID{$this->fileIdRef}.docx";
        $activityManagement = new \app\components\ActivityManagement();
        $activityManagement->sendInvoiceLetterActitvityLog($letterName);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    private function generateLetter33($letterFormModel, $invoiceModel)
    {
        $templatePath = Templates::getTemplate('word', "LetterID{$this->fileIdRef}.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $invoiceModel->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($invoiceModel->currency2->CurrencyPersianTitle) ? $invoiceModel->currency2->CurrencyPersianTitle : '');

        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        $word->setBookmarkValue('bmSubContractor', str_replace('&', '-', $invoiceModel->contract->subContractor->PersonTitle));
        $word->setBookmarkValue('bmInvoiceType', $invoiceModel->type->InvoiceType);
        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($invoiceModel->contract->ContractNo)));
        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($invoiceModel->Title, '-'));

        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $invoiceModel->contract->ContractSubject));   //@ToDo: در مقدار این فیلد هرگاه کاراکتر (&)باشد نامه کلا باز نمیشود  
        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($invoiceModel->CutOffDateHijri, '/'));


        if ($invoiceModel->InvoiceType == 17 || $invoiceModel->InvoiceType == 18 || $invoiceModel->InvoiceType == 19 || $invoiceModel->InvoiceType == 20 || $invoiceModel->InvoiceType == 21) {
            $lateral = $this->cumulativeAditionalCost($invoiceModel);

            $word->setBookmarkValue('bmComulativeApproved1', '');
            $word->setBookmarkValue('bmComulativeApproved1Currency', '');
            $word->setBookmarkValue('bmComulativeApproved1Text', $lateral);


            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmApproved2', '');
            }
        } else {
            $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle));
            $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
            $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);


            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmComulativeApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle) . ' ' . $currency2PersianTitle . ' (' . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle, ''), 2) . ' ' . $currency2PersianTitle . ')');
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmComulativeApproved2', '');
                $word->setBookmarkValue('bmApproved2', '');
            }
        }


        $word->setBookmarkValue('bmApproved1', Utility::moneyFormatWord($invoiceModel->approved1, $currency1PersianTitle));
        $word->setBookmarkValue('bmApproved1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        if ($letterFormModel->Description) {
            $word->setVarValue('توضیحات', $this->convertNumbers($letterFormModel->Description));
            $word->setBookmarkValue('bmDescription', $this->convertNumbers($letterFormModel->Description));
        } else {
            $word->deleteBlock('بلاکتوضیحات');
        }

        if ($invoiceModel->cumulativePenalty1(6) <> 0 && $letterFormModel->Penalty == 1) {
            $contractPenalty1Text = trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle;
            $word->setVarValue('متنکاملبخشجرایم', "همچنین مبلغ تجمعی " . Utility::moneyFormatWord($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle) . " $currency1PersianTitle($contractPenalty1Text) بابت جرائم (مستندات پیوست) می بایستی از حساب پیمانکار فوق کسر گردد.");
        } else {
            $word->deleteBlock('بلاکجریمه');
        }

        //          بدهی
        if ($invoiceModel->cumulativeIncome1(6) <> 0 && $letterFormModel->Income == 1) {
            $cumulative2 = '';
            if ($invoiceModel->cumulativeIncome2(6) <> 0 && $currency2PersianTitle <> '') {
                $cumulative2 = 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle) . " $currency2PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle, ''), 2) . " $currency2PersianTitle)";
            }
            $income = "لازم به ذکر است ليست بدهي پيمانکاران به مبلغ تجمعي ";
            $income .= Utility::moneyFormatWord($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle) . " $currency1PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle, ''), 2) . " $currency1PersianTitle)";
            $income .= $cumulative2;
            $income .= "و مبالغ دوره اي مربوطه، به پيوست اين صورت وضعيت جهت درج در حساب پيمانکاران ذکر شده ارسال مي گردد.";
            $word->setVarValue('متنکاملبخشبدهی', $income);
        } else {
            $word->deleteBlock("بلاکبدهی");
        }

        if ($letterFormModel->Evidence7Chek == 1) {
            $word->setBookmarkValue('bmEvidence7No', ($letterFormModel->Evidence7No));
            $word->setBookmarkValue('bmEvidence7Date', $this->convertNumbers($letterFormModel->Evidence7Date));
        } else {
            $word->deleteBlock("مستنداتپتروگوهرفراساحل");
        }

        $letterName = "LetterID{$this->fileIdRef}.docx";
        $activityManagement = new \app\components\ActivityManagement();
        $activityManagement->sendInvoiceLetterActitvityLog($letterName);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    public static function convertNumbers($matches)
    {
        $farsi_array = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $english_array = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

        return str_replace($english_array, $farsi_array, $matches);
    }

//    از این تابع برای باز گردادند مقادیر نرخ های صورت وضعیت مجازی استفاده  می کنیم 
    private function cumulativeAditionalCost($invoiceModel)
    {
        $lateral = InvoiceEdit::cumulativeAditionalCost($invoiceModel->InvoiceID);
        // محاسبه ی مجموع برای به همراه حروف 
        $persianNum = '';
        foreach ($lateral as $key => $value) {
            $curData = \app\modules\currency\models\Currency::find()
                ->where(['CurrencyID' => $value['Currency']])
                ->one();
            $persianNum .= '*' . Utility::revertString(Utility::moneyFormatByCurrency($value['Approved1cumulative'], $value['Currency'], '/'), '/') . ' ' . $curData->CurrencyPersianTitle;
            $persianNum .= '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($value['Approved1cumulative'], $value['Currency'], ''), 2)) . ' ' . $curData->CurrencyPersianTitle . ')';
        }

        return $persianNum;
    }

    private function generateLetter701($letterFormModel, $invoiceModel)
    {
        $templatePath = Templates::getTemplate('word', "LetterID{$this->fileIdRef}.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $invoiceModel->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($invoiceModel->currency2->CurrencyPersianTitle) ? $invoiceModel->currency2->CurrencyPersianTitle : '');


        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        $word->setBookmarkValue('bmSubContractor', str_replace('&', '-', $invoiceModel->contract->subContractor->PersonTitle));
        $word->setBookmarkValue('bmInvoiceType', $invoiceModel->type->InvoiceType);
        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($invoiceModel->contract->ContractNo)));
        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($invoiceModel->Title, '-'));


        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $invoiceModel->contract->ContractSubject));   //@ToDo: در مقدار این فیلد هرگاه کاراکتر (&)باشد نامه کلا باز نمیشود  
        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($invoiceModel->CutOffDateHijri, '/'));


        if ($invoiceModel->InvoiceType == 17 || $invoiceModel->InvoiceType == 18 || $invoiceModel->InvoiceType == 19 || $invoiceModel->InvoiceType == 20 || $invoiceModel->InvoiceType == 21) {
            $lateral = $this->cumulativeAditionalCost($invoiceModel);

            $word->setBookmarkValue('bmComulativeApproved1', '');
            $word->setBookmarkValue('bmComulativeApproved1Currency', '');
            $word->setBookmarkValue('bmComulativeApproved1Text', $lateral);


            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency1PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency1PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmApproved2', '');
            }
        } else {
            $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle));
            $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
            $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);


            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmComulativeApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle) . ' ' . $currency2PersianTitle . ' (' . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved2(6), $currency1PersianTitle, ''), 2) . ' ' . $currency2PersianTitle . ')');
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency1PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency1PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmComulativeApproved2', '');
                $word->setBookmarkValue('bmApproved2', '');
            }
        }


        $word->setBookmarkValue('bmApproved1', Utility::moneyFormatWord($invoiceModel->approved1, $currency1PersianTitle));
        $word->setBookmarkValue('bmApproved1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        if ($letterFormModel->Description) {
            $word->setVarValue('توضیحات', $this->convertNumbers($letterFormModel->Description));
            $word->setBookmarkValue('bmDescription', $this->convertNumbers($letterFormModel->Description));
        } else {
            $word->deleteBlock('بلاکتوضیحات');
        }

        if ($invoiceModel->cumulativePenalty1(6) <> 0 && $letterFormModel->Penalty == 1) {
            $contractPenalty1Text = trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle;
            $word->setVarValue('متنکاملبخشجرایم', "همچنین مبلغ تجمعی " . Utility::moneyFormatWord($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle) . " $currency1PersianTitle($contractPenalty1Text) بابت جرائم (مستندات پیوست) می بایستی از حساب پیمانکار فوق کسر گردد.");
        } else {
            $word->deleteBlock('بلاکجریمه');
        }

        //          بدهی
        if ($invoiceModel->cumulativeIncome1(6) <> 0 && $letterFormModel->Income == 1) {
            $cumulative2 = '';
            if ($invoiceModel->cumulativeIncome2(6) <> 0 && $currency2PersianTitle <> '') {
                $cumulative2 = 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle) . " $currency2PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle, ''), 2) . " $currency2PersianTitle)";
            }
            $income = "لازم به ذکر است ليست بدهي پيمانکاران به مبلغ تجمعي ";
            $income .= Utility::moneyFormatWord($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle) . " $currency1PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle, ''), 2) . " $currency1PersianTitle)";
            $income .= $cumulative2;
            $income .= "و مبالغ دوره اي مربوطه، به پيوست اين صورت وضعيت جهت درج در حساب پيمانکاران ذکر شده ارسال مي گردد.";
            $word->setVarValue('متنکاملبخشبدهی', $income);
        } else {
            $word->deleteBlock("بلاکبدهی");
        }

        if ($letterFormModel->Evidence14Chek == 1) {
            $word->setBookmarkValue('bmEvidence14No', ($letterFormModel->Evidence14No));
            $word->setBookmarkValue('bmEvidence14Date', $this->convertNumbers($letterFormModel->Evidence14Date));
        } else {
            $word->deleteBlock("مستنداتپتروگوهرفراساحل");
        }

        $activityManagement = new \app\components\ActivityManagement();
        $actionType = "Letter";
        $subType = "ExportLetter";
        $subTypeDescription["LetterName"] = "InvoiceLetter";
        $subTypeDescription["FileName"] = "LetterID{$this->fileIdRef}.docx";
        $subTypeDescription["FilePatch"] = \app\components\Templates::getTemplate('word', "LetterID{$this->fileIdRef}.docx");
        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    public function generateLetter702($invoiceModel)
    {
        $jDate = new \app\components\jDateTime(true, true, 'Asia/Tehran');

        $useXlsxFormat = true;
        $xlBook = new \ExcelBook(Yii::$app->params['libxlKey']['key'], Yii::$app->params['libxlKey']['pass'], $useXlsxFormat);
        $xlBook->setLocale('UTF-8');

        $xlBook->loadFile(\app\components\Templates::getTemplate('excel', 'Export-PKSK-Info(PSA-PPI).xlsx'));

        $s = $xlBook->getSheet(0);

        $pic_file = \app\components\Templates::getTemplate('logo', Yii::$app->session->get('CompanyID') . '.PNG');
        if (!empty($pic_file)) {
            $pic = $xlBook->addPictureFromFile($pic_file);
            $s->addPictureScaled(0, 1, $pic, 0.5);
        }

        if (Yii::$app->session->get('CompanyID') == 240) {
            $s->write(0, 3, 'SOUTH PARS GAS FILED DEVELOPMENT (PHASES 22~24)');
        } elseif (Yii::$app->session->get('CompanyID') == 241) {
            $s->write(0, 3, 'SOUTH PARS GAS FILED DEVELOPMENT (PHASES 13)');
        } else {
            $s->write(0, 3, 'SOUTH PARS GAS FILED DEVELOPMENT');
        }


        $invoiceAmountTable = \app\modules\invoice\models\InvoiceAmountEdit::tableName();
        $invoiceTable = \app\modules\invoice\models\InvoiceEdit::tableName();
        $currencyTable = \app\modules\currency\models\Currency::tableName();
        $costCodeTable = \app\modules\wbs\models\Costcodenew::tableName();
        $invoicePKSKInfoTable = \app\modules\invoice\models\InvoicePksk::tableName();

        $invoicePKSKInfo = \app\modules\invoice\models\InvoicePksk::find()
            ->leftJoin("$invoiceAmountTable", "$invoiceAmountTable.InvoiceID=$invoicePKSKInfoTable.InvoiceIDRef")
            ->leftJoin("$currencyTable as cur1", "cur1.CurrencyID=$invoicePKSKInfoTable.CI_Currency1IDRef")
            ->leftJoin("$currencyTable as cur2", "cur2.CurrencyID=$invoicePKSKInfoTable.CI_Currency2IDRef")
            ->innerJoin("$invoiceTable", "$invoiceAmountTable.InvoiceID=$invoiceTable.InvoiceID")
            ->leftJoin("$costCodeTable as lev1", "lev1.NodeID=left($invoiceAmountTable.CostCode,6)")
            ->leftJoin("$costCodeTable as lev2", "lev2.NodeID=left($invoiceAmountTable.CostCode,9)")
            ->leftJoin("$costCodeTable as lev3", "lev3.NodeID=left($invoiceAmountTable.CostCode,12)")
            ->where(["$invoiceTable.ContractID" => $invoiceModel->contract->ContractID])
            ->andWhere("$invoiceTable.CutOffDateHijri<='" . $invoiceModel->CutOffDateHijri . "'")
            ->andWhere(["$invoiceTable.InvoiceType" => $invoiceModel->InvoiceType])
            ->andWhere("$invoiceTable.AmountStatus<>0")
            ->addSelect([
                "level1" => 'lev1.NodeText',
                'level2' => 'lev2.NodeText',
                'level3' => 'lev3.NodeText',
                'Currency1Title' => 'cur1.CurrencyTitle',
                'Currency2Title' => 'cur2.CurrencyTitle',
                'InvoiceIDRef' => "$invoiceAmountTable.InvoiceID",
                "$invoicePKSKInfoTable.MSR_No",
                "$invoicePKSKInfoTable.PL_No",
                "$invoicePKSKInfoTable.CI_No",
                "$invoicePKSKInfoTable.CI_Amount1",
                "$invoicePKSKInfoTable.CI_Amount2",
                "invoicesTitle" => "$invoiceTable.Title",
                "invoiceID" => "$invoiceTable.InvoiceID",
                "CutOffDateHijri" => "$invoiceTable.CutOffDateHijri",
            ])
            ->orderBy(["$invoiceTable.CutOffDateHijri" => SORT_DESC, "$invoiceTable.InvoiceID" => SORT_DESC])->asArray()
            ->all();

        $currentRow = 23;

        foreach ($invoicePKSKInfo as $key => $value) {
            $s->write($currentRow, 1, strval($invoicePKSKInfo[$key]['invoicesTitle']));
            $s->write($currentRow, 2, strval($invoicePKSKInfo[$key]['Currency1Title']));
            $s->write($currentRow, 3, floatval($invoicePKSKInfo[$key]['CI_Amount1']));
            $s->write($currentRow, 4, strval($invoicePKSKInfo[$key]['CI_No']));
            $s->write($currentRow, 5, strval($invoicePKSKInfo[$key]['PL_No']));
            $s->write($currentRow, 7, strval($invoicePKSKInfo[$key]['MSR_No']));
            $s->write($currentRow, 8, strval($invoicePKSKInfo[$key]['level1']));
            $s->write($currentRow, 9, strval($invoicePKSKInfo[$key]['level2']));
            $s->write($currentRow, 10, strval($invoicePKSKInfo[$key]['level3']));
            $s->write($currentRow, 11, strval($invoicePKSKInfo[$key]['invoiceID']));
            $s->write($currentRow, 12, strval($invoicePKSKInfo[$key]['CutOffDateHijri']));

            if ($invoiceModel->InvoiceID == $invoicePKSKInfo[$key]['InvoiceIDRef']) {
                $s->write($currentRow, 0, strval('THIS PERIOD'));
            } else {
                $s->write($currentRow, 0, strval('Last PERIOD'));
            }
            // این بخش از کد برای درج نرخ دوم صورت وضعیت ها هست در صورت وجود
            if (!empty($invoicePKSKInfo[$key]['Currency2Title'])) {
                $currentRow++;
                $s->write($currentRow, 1, strval($invoicePKSKInfo[$key]['invoicesTitle']));
                $s->write($currentRow, 2, strval($invoicePKSKInfo[$key]['Currency2Title']));
                $s->write($currentRow, 3, floatval($invoicePKSKInfo[$key]['CI_Amount2']));
                $s->write($currentRow, 4, strval($invoicePKSKInfo[$key]['CI_No']));
                $s->write($currentRow, 5, strval($invoicePKSKInfo[$key]['PL_No']));
                $s->write($currentRow, 7, strval($invoicePKSKInfo[$key]['MSR_No']));
                $s->write($currentRow, 8, strval($invoicePKSKInfo[$key]['level1']));
                $s->write($currentRow, 9, strval($invoicePKSKInfo[$key]['level2']));
                $s->write($currentRow, 10, strval($invoicePKSKInfo[$key]['level3']));
                $s->write($currentRow, 11, strval($invoicePKSKInfo[$key]['invoiceID']));
                $s->write($currentRow, 12, strval($invoicePKSKInfo[$key]['CutOffDateHijri']));
                if ($invoiceModel->InvoiceID == $invoicePKSKInfo[$key]['InvoiceIDRef']) {
                    $s->write($currentRow, 0, strval('THIS PERIOD'));
                } else {
                    $s->write($currentRow, 0, strval('LAST PERIOD'));
                }
            }

            $currency[$invoicePKSKInfo[$key]['Currency1Title']] = $invoicePKSKInfo[$key]['Currency1Title'];
            if (isset($invoicePKSKInfo[$key]['Currency2Title'])) {
                $currency[$invoicePKSKInfo[$key]['Currency2Title']] = $invoicePKSKInfo[$key]['Currency2Title'];
            }

            $currentRow++;
        }

        for ($currentRow; $currentRow <= 1020; $currentRow++) {
            $s->setRowHeight($currentRow, 0.1);
        }

        $currency[$invoiceModel->contract->lastRider->currency1->CurrencyTitle] = $invoiceModel->contract->lastRider->currency1->CurrencyTitle;
        if (isset($invoiceModel->contract->lastRider->currency2->CurrencyTitle)) {
            $currency[$invoiceModel->contract->lastRider->currency2->CurrencyTitle] = $invoiceModel->contract->lastRider->currency2->CurrencyTitle;
        }

        unset($currency[""]);

        $s->write(3, 1, $invoiceModel->contract->ContractNo);
        $s->write(3, 4, $invoiceModel->contract->ContractSubject);

        $s->write(3, 7, $invoiceModel->contract->subContractor->PersonTitle);
        $s->write(3, 10, strval($invoiceModel->contract->ContractDateHijri));
        $s->write(1, 2, $invoiceModel->type->InvoiceType);
        $s->write(2, 2, "INV.No: " . $invoiceModel->Title);
        $s->write(2, 4, $invoiceModel->Description);
        $s->write(19, 4, floatval($invoiceModel->approved1IrrF2));

        $arrayCurrency = [
            'IRR' => 1,
            'EUR' => 2,
            'USD' => 3,
            'KRW' => 4,
            'AED' => 5,
            'RMB' => 6,
            'MYR' => 7,
            'GBP' => 8,
            'NOK' => 9,
            'CHF' => 10,
            'INR' => 11,
            'JPY100' => 12,
            'DKK' => 13,
            'SEK' => 14
        ];
        foreach ($arrayCurrency as $key => $value) {
            if ($invoiceModel->contract->lastRider->currency1->CurrencyTitle == $key && $invoiceModel->InvoiceType <= 5) {
                $s->write(4 + $value, 2, floatval($invoiceModel->contract->lastRider->Price1));
            }
            if (isset($invoiceModel->contract->lastRider->currency2->CurrencyTitle) &&
                $invoiceModel->contract->lastRider->currency2->CurrencyTitle == $key && $invoiceModel->InvoiceType <= 5) {
                $s->write(4 + $value, 2, floatval($invoiceModel->contract->lastRider->Price2));
            }
            if (!array_search($key, $currency)) {
                $s->setRowHeight(4 + $value, 0.1);
            }
        }

        $dateNow = $jDate->date("Y.m.d", false, false);
        header('Content-type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="PKSK Info -' . $dateNow . ' .xlsx"');
        $xlBook->save('php://output');

        $activityManagement = new \app\components\ActivityManagement();
        $actionType = "Report";
        $subType = "ExportFile";
        $subTypeDescription["ReportName"] = "DebitmemoPODetailReport";
        $subTypeDescription["FileName"] = "Export-PKSK-Info(PSA-PPI).xlsm";
        $subTypeDescription["FilePatch"] = \app\components\Templates::getTemplate('excel', 'Export-PKSK-Info(PSA-PPI).xlsm');
        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

        exit();
    }

    private function generateLetter703($letterFormModel, $invoiceModel)
    {
        $debitmemoModel = $invoiceModel->debitmemo;

        $cumulative1 = $debitmemoModel->cumulativeApproved1(6);
        $cumulative2 = $debitmemoModel->cumulativeApproved2(6);

        $templatePath = Templates::getTemplate('word', "Letter-DebitPPI.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $debitmemoModel->contract->lastRider->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($debitmemoModel->contract->lastRider->currency2->CurrencyPersianTitle) ? $debitmemoModel->contract->lastRider->currency2->CurrencyPersianTitle : '');

        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        $word->setBookmarkValue('bmSubContractor', str_replace('&', '-', $debitmemoModel->contract->subContractor->PersonTitle));

        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($debitmemoModel->contract->ContractNo)));

        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($debitmemoModel->debitmemoNo, '-'));

        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $debitmemoModel->contract->ContractSubject));   //@ToDo: در مقدار این فیلد هرگاه کاراکتر (&)باشد نامه کلا باز نمیشود  
        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($debitmemoModel->debitmemoDateHijri, '/'));

        $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($cumulative1, $currency1PersianTitle));
        $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($cumulative1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        if (!isset($currency2PersianTitle)) {
            $word->setBookmarkValue('bmComulativeApproved2', 'و ' . Utility::moneyFormatWord($cumulative2, $currency2PersianTitle) . ' ' . $currency2PersianTitle
                . ' (' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($cumulative2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');

            $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($debitmemoModel->approved2, $currency2PersianTitle)
                . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($debitmemoModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
        } else {
            $word->setBookmarkValue('bmComulativeApproved2', '');
            $word->setBookmarkValue('bmApproved2', '');
        }

        $word->setBookmarkValue('bmApproved1', Utility::moneyFormatWord($debitmemoModel->approved1, $currency1PersianTitle));

        $word->setBookmarkValue('bmApproved1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($debitmemoModel->approved1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        $personModel = \app\modules\OBS\models\Person::find()
            ->where(['PersonID' => $debitmemoModel->contract->SignerPersonIDRef])
            ->one();

        if (isset($personModel)) {
            $word->setBookmarkValue('bmCompanyName', $personModel->PersonTitle);
            $word->setBookmarkValue('bmCompanyName1', $personModel->PersonTitle);
        }

        if ($letterFormModel->Description) {
            $word->setVarValue('توضیحات', $this->convertNumbers($letterFormModel->Description));
            $word->setBookmarkValue('bmDescription', $this->convertNumbers($letterFormModel->Description));
        } else {
            $word->deleteBlock('بلاکتوضیحات');
        }

        $invoiceTable = InvoiceEdit::tableName();
        $invoiceAmountTable = \app\modules\invoice\models\InvoiceAmountEdit::tableName();
        $debitMemoAmountTable = \app\modules\debitmemo\models\DebitmemoAmount::tableName();

        $QueryinvocieList = "select $invoiceTable.InvoiceID from $debitMemoAmountTable
            inner join $invoiceAmountTable on $invoiceAmountTable.ID=$debitMemoAmountTable.invoiceAmountIdRef
            inner join $invoiceTable on $invoiceTable.InvoiceID=$invoiceAmountTable.InvoiceID
            Where debitmemoIdRef=" . $debitmemoModel->id . " group by $invoiceTable.InvoiceID
            ;";
        $invoiceList = Yii::$app->db->createCommand($QueryinvocieList)->queryAll();

        // جدول اول
        $costTable = '<w:tbl><w:tblPr><w:bidiVisual/><w:tblW w:w="0" w:type="auto"/><w:tblBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:left w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:right w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:insideH w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:insideV w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tblBorders><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="2331"/><w:gridCol w:w="1906"/><w:gridCol w:w="664"/><w:gridCol w:w="2155"/><w:gridCol w:w="2187"/></w:tblGrid>';
        $costTable .= '<w:tr w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidTr="00820A6E"><w:trPr><w:trHeight w:val="520"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="2331" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008037F1" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="24"/><w:szCs w:val="24"/><w:rtl/></w:rPr></w:pPr><w:bookmarkStart w:id="0" w:name="bmInvoiceAmountByOwner"/><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>شماره ی صورت وضعیت</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1906" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>تاریخ</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="664" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>نرخ</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2155" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>مبلغ صورت وضعیت</w:t></w:r></w:p></w:tc></w:tr>';
        $sumApproved1 = 0;
        $sumApproved2 = 0;
        $currencyTitle1 = NULL;
        $currencyTitle2 = NULL;
        $invoiceType = "";
        foreach ($invoiceList as $key => $value) {
            $newInvocieModel = InvoiceEdit::find()
                ->where(['InvoiceID' => $value['InvoiceID']])
                ->one();

            $invoiceType = $newInvocieModel->type->InvoiceType;
            $costTable .= '<w:tr w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidTr="00820A6E"><w:trPr><w:trHeight w:val="454"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="2331" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr><w:t>' . Utility::invertString(str_replace('&', '-', $newInvocieModel['Title']), '-') . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1906" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $newInvocieModel['CutOffDateHijri'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="664" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $newInvocieModel['Currency1Title'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2155" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($newInvocieModel->approved1, $newInvocieModel['Currency1Title']) . '</w:t></w:r></w:p></w:tc></w:tr>';
            $currencyTitle1 = $newInvocieModel['Currency1Title'];
            if (isset($newInvocieModel['Currency2Title'])) {
                $currencyTitle2 = $newInvocieModel['Currency2Title'];
                $costTable .= '<w:tr w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidTr="00820A6E"><w:trPr><w:trHeight w:val="454"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="2331" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr><w:t>' . Utility::invertString(str_replace('&', '-', $newInvocieModel['Title']), '-') . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1906" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $newInvocieModel['CutOffDateHijri'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="664" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $newInvocieModel['Currency2Title'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2155" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($newInvocieModel->approved2, $newInvocieModel['Currency2Title']) . '</w:t></w:r></w:p></w:tc></w:tr>';
                $sumApproved2 += $newInvocieModel->approved2;
            }
            $sumApproved1 += $newInvocieModel->approved1;
        }


        $costTable .= '<w:tr w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidTr="00820A6E"><w:trPr><w:trHeight w:val="454"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="2331" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr><w:t>' . 'جمع' . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1906" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . '' . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="664" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $currencyTitle1 . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2155" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($sumApproved1, $currencyTitle1) . '</w:t></w:r></w:p></w:tc></w:tr>';
        if (isset($currencyTitle2)) {
            $costTable .= '<w:tr w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidTr="00820A6E"><w:trPr><w:trHeight w:val="454"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="2331" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr><w:t>' . 'جمع' . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1906" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . '' . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="664" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $currencyTitle2 . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2155" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($sumApproved2, $currencyTitle2) . '</w:t></w:r></w:p></w:tc></w:tr>';
        }
        $costTable .= '</w:tbl>';
        $word->setBookmarkValue('bmInvoiceAmountByOwner', $costTable);


        // جدول دوم
        $temp = $this->generateLetter704_dataProvider($debitmemoModel->contractIdRef, $debitmemoModel->debitmemoDateHijri);

        $approvedDataSum = $temp["detail"];
        $approvedDataSumTotal = $temp["total"];

        $costTable2 = '<w:tbl><w:tblPr><w:tblStyle w:val="TableGrid"/><w:bidiVisual/><w:tblW w:w="9652" w:type="dxa"/><w:tblInd w:w="-327" w:type="dxa"/><w:tblLook w:val="04A0" w:firstRow="1" w:lastRow="0" w:firstColumn="1" w:lastColumn="0" w:noHBand="0" w:noVBand="1"/></w:tblPr><w:tblGrid><w:gridCol w:w="2003"/><w:gridCol w:w="704"/><w:gridCol w:w="1984"/><w:gridCol w:w="1843"/><w:gridCol w:w="3118"/></w:tblGrid>';
        $costTable2 .= '<w:tr w:rsidR="00CD0BAB" w:rsidTr="003D52C8"><w:tc><w:tcPr><w:tcW w:w="2003" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="00527ADC" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00527ADC"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>نوع هزینه</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="704" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="00527ADC" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00527ADC"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>نرخ</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1984" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="00527ADC" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00527ADC"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>مبلغ دوره ای</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1843" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="00527ADC" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00527ADC"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>مبلغ تجمعی</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="3118" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="00527ADC" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00527ADC"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>شماره اعلامیه ها</w:t></w:r></w:p></w:tc></w:tr>';

        $debitmemoList = $this->generateLetter704_debitmemoList($debitmemoModel->contractIdRef, $debitmemoModel->debitmemoDateHijri);

        foreach ($approvedDataSum as $key => $value) {
            $listString = isset($debitmemoList[$value["InvoiceType"]][$value["CurrencyTitle"]]) ? $debitmemoList[$value["InvoiceType"]][$value["CurrencyTitle"]] : "";

            if ($invoiceType == $value["InvoiceType"] && $currencyTitle1 == $value["CurrencyTitle"]) {
                $costTable2 .= '<w:tr w:rsidR="00CD0BAB" w:rsidTr="003D52C8"><w:tc><w:tcPr><w:tcW w:w="2003" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr><w:t>' . $value["InvoiceType"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="704" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr><w:t>' . $value["CurrencyTitle"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1984" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatWord($debitmemoModel->approved1, $currency1PersianTitle) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1843" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($value["total"], $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="3118" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . $listString . '</w:t></w:r></w:p></w:tc></w:tr>';
            } elseif ($invoiceType == $value["InvoiceType"] && $currencyTitle2 == $value["CurrencyTitle"]) {
                $costTable2 .= '<w:tr w:rsidR="00CD0BAB" w:rsidTr="003D52C8"><w:tc><w:tcPr><w:tcW w:w="2003" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr><w:t>' . $value["InvoiceType"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="704" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr><w:t>' . $value["CurrencyTitle"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1984" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatWord($debitmemoModel->approved2, $currency2PersianTitle) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1843" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($value["total"], $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="3118" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . $listString . '</w:t></w:r></w:p></w:tc></w:tr>';
            } else {
                $costTable2 .= '<w:tr w:rsidR="00CD0BAB" w:rsidTr="003D52C8"><w:tc><w:tcPr><w:tcW w:w="2003" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr><w:t>' . $value["InvoiceType"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="704" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr><w:t>' . $value["CurrencyTitle"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1984" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>___</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1843" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($value["total"], $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="3118" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . $listString . '</w:t></w:r></w:p></w:tc></w:tr>';
            }
        }

        foreach ($approvedDataSumTotal as $key => $value) {
            if ($currencyTitle1 == $value["CurrencyTitle"]) {
                $costTable2 .= '<w:tr w:rsidR="00CD0BAB" w:rsidTr="003D52C8"><w:tc><w:tcPr><w:tcW w:w="2003" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>جمع</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="704" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . $value["CurrencyTitle"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1984" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatWord($debitmemoModel->approved1, $currency1PersianTitle) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1843" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($value["total"], $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="3118" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>___</w:t></w:r></w:p></w:tc></w:tr>';
            } elseif ($currencyTitle2 == $value["CurrencyTitle"]) {
                $costTable2 .= '<w:tr w:rsidR="00CD0BAB" w:rsidTr="003D52C8"><w:tc><w:tcPr><w:tcW w:w="2003" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>جمع</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="704" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . $value["CurrencyTitle"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1984" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatWord($debitmemoModel->approved2, $currency2PersianTitle) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1843" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($value["total"], $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="3118" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>___</w:t></w:r></w:p></w:tc></w:tr>';
            } else {
                $costTable2 .= '<w:tr w:rsidR="00CD0BAB" w:rsidTr="003D52C8"><w:tc><w:tcPr><w:tcW w:w="2003" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>جمع</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="704" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . $value["CurrencyTitle"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1984" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>___</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1843" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($value["total"], $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="3118" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>___</w:t></w:r></w:p></w:tc></w:tr>';
            }
        }

        $costTable2 .= '</w:tbl>';
        $word->setBookmarkValue('bmDebitmemototal', $costTable2);


        $word->setBookmarkValue('bmEvidence15No', ($letterFormModel->Evidence15No));
        $word->setBookmarkValue('bmEvidence15Date', $this->convertNumbers($letterFormModel->Evidence15Date));

        $activityManagement = new \app\components\ActivityManagement();
        $actionType = "Letter";
        $subType = "ExportLetter";
        $subTypeDescription["LetterName"] = "DebitmemoLetter";
        $subTypeDescription["FileName"] = "Letter-DebitPPI.docx";
        $subTypeDescription["FilePatch"] = \app\components\Templates::getTemplate('word', 'Letter-DebitPPI.docx');
        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    private function generateLetter704($letterFormModel, $invoiceModel)
    {
        $debitmemoModel = $invoiceModel->debitmemo;

        $cumulative1 = $debitmemoModel->cumulativeApproved1(6);
        $cumulative2 = $debitmemoModel->cumulativeApproved2(6);

        $templatePath = Templates::getTemplate('word', "Letter-DebitPSA.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $debitmemoModel->contract->lastRider->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($debitmemoModel->contract->lastRider->currency2->CurrencyPersianTitle) ? $debitmemoModel->contract->lastRider->currency2->CurrencyPersianTitle : '');

        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        $word->setBookmarkValue('bmSubContractor', str_replace('&', '-', $debitmemoModel->contract->subContractor->PersonTitle));

        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($debitmemoModel->contract->ContractNo)));
        //$word->setBookmarkValue('bmContractNumber', $debitmemoModel->contract->ContractNo);

        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($debitmemoModel->debitmemoNo, '-'));


        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $debitmemoModel->contract->ContractSubject));   //@ToDo: در مقدار این فیلد هرگاه کاراکتر (&)باشد نامه کلا باز نمیشود  
        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($debitmemoModel->debitmemoDateHijri, '/'));

        $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($cumulative1, $currency1PersianTitle));
        $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($cumulative1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        if (!isset($currency2PersianTitle)) {
            $word->setBookmarkValue('bmComulativeApproved2', 'و ' . Utility::moneyFormatWord($cumulative2, $currency2PersianTitle) . ' ' . $currency2PersianTitle
                . ' (' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($cumulative2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');

            $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($debitmemoModel->approved2, $currency2PersianTitle)
                . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($debitmemoModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
        } else {
            $word->setBookmarkValue('bmComulativeApproved2', '');
            $word->setBookmarkValue('bmApproved2', '');
        }


        $word->setBookmarkValue('bmApproved1', Utility::moneyFormatWord($debitmemoModel->approved1, $currency1PersianTitle));

        $word->setBookmarkValue('bmApproved1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($debitmemoModel->approved1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        $personModel = \app\modules\OBS\models\Person::find()
            ->where(['PersonID' => $debitmemoModel->contract->SignerPersonIDRef])
            ->one();


        if (isset($personModel)) {
            $word->setBookmarkValue('bmCompanyName', $personModel->PersonTitle);
            $word->setBookmarkValue('bmCompanyName1', $personModel->PersonTitle);
        }

        if ($letterFormModel->Description) {
            $word->setVarValue('توضیحات', $this->convertNumbers($letterFormModel->Description));
            $word->setBookmarkValue('bmDescription', $this->convertNumbers($letterFormModel->Description));
        } else {
            $word->deleteBlock('بلاکتوضیحات');
        }
        $invoiceTable = InvoiceEdit::tableName();
        $invoiceAmountTable = \app\modules\invoice\models\InvoiceAmountEdit::tableName();
        $debitMemoAmountTable = \app\modules\debitmemo\models\DebitmemoAmount::tableName();

        $QueryinvocieList = "select $invoiceTable.InvoiceID from $debitMemoAmountTable
            inner join $invoiceAmountTable on $invoiceAmountTable.ID=$debitMemoAmountTable.invoiceAmountIdRef
            inner join $invoiceTable on $invoiceTable.InvoiceID=$invoiceAmountTable.InvoiceID
            Where debitmemoIdRef=" . $debitmemoModel->id . " group by $invoiceTable.InvoiceID
            ;";
        $invoiceList = Yii::$app->db->createCommand($QueryinvocieList)->queryAll();

        // جدول اول
        $costTable = '<w:tbl><w:tblPr><w:bidiVisual/><w:tblW w:w="0" w:type="auto"/><w:tblBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:left w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:right w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:insideH w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:insideV w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tblBorders><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="2331"/><w:gridCol w:w="1906"/><w:gridCol w:w="664"/><w:gridCol w:w="2155"/><w:gridCol w:w="2187"/></w:tblGrid>';
        $costTable .= '<w:tr w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidTr="00820A6E"><w:trPr><w:trHeight w:val="520"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="2331" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008037F1" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="24"/><w:szCs w:val="24"/><w:rtl/></w:rPr></w:pPr><w:bookmarkStart w:id="0" w:name="bmInvoiceAmountByOwner"/><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>شماره ی صورت وضعیت</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1906" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>تاریخ</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="664" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>نرخ</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2155" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>تائیدی دوره</w:t></w:r></w:p></w:tc></w:tr>';
        $sumApproved1 = 0;
        $sumApproved2 = 0;
        $currencyTitle1 = NULL;
        $currencyTitle2 = NULL;
        $invoiceType = "";
        foreach ($invoiceList as $key => $value) {
            $newInvocieModel = InvoiceEdit::find()
                ->where(['InvoiceID' => $value['InvoiceID']])
                ->one();
            $invoiceType = $newInvocieModel->type->InvoiceType;
            $costTable .= '<w:tr w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidTr="00820A6E"><w:trPr><w:trHeight w:val="454"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="2331" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr><w:t>' . Utility::invertString(str_replace('&', '-', $newInvocieModel['Title']), '-') . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1906" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $newInvocieModel['CutOffDateHijri'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="664" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $newInvocieModel['Currency1Title'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2155" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($newInvocieModel->approved1, $newInvocieModel['Currency1Title']) . '</w:t></w:r></w:p></w:tc></w:tr>';
            $currencyTitle1 = $newInvocieModel['Currency1Title'];
            if (isset($newInvocieModel['Currency2Title'])) {
                $currencyTitle2 = $newInvocieModel['Currency2Title'];
                $costTable .= '<w:tr w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidTr="00820A6E"><w:trPr><w:trHeight w:val="454"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="2331" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr><w:t>' . Utility::invertString(str_replace('&', '-', $newInvocieModel['Title']), '-') . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1906" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $newInvocieModel['CutOffDateHijri'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="664" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $newInvocieModel['Currency2Title'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2155" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($newInvocieModel->approved2, $newInvocieModel['Currency2Title']) . '</w:t></w:r></w:p></w:tc></w:tr>';
                $sumApproved2 += $newInvocieModel->approved2;
            }
            $sumApproved1 += $newInvocieModel->approved1;
        }


        $costTable .= '<w:tr w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidTr="00820A6E"><w:trPr><w:trHeight w:val="454"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="2331" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr><w:t>' . 'جمع' . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1906" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . '' . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="664" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $currencyTitle1 . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2155" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($sumApproved1, $currencyTitle1) . '</w:t></w:r></w:p></w:tc></w:tr>';
        if (isset($currencyTitle2)) {
            $costTable .= '<w:tr w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidTr="00820A6E"><w:trPr><w:trHeight w:val="454"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="2331" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr><w:t>' . 'جمع' . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1906" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . '' . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="664" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $currencyTitle2 . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2155" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($sumApproved2, $currencyTitle2) . '</w:t></w:r></w:p></w:tc></w:tr>';
        }
        $costTable .= '</w:tbl>';
        $word->setBookmarkValue('bmInvoiceAmountByOwner', $costTable);


        // جدول دوم
        $temp = $this->generateLetter704_dataProvider($debitmemoModel->contractIdRef, $debitmemoModel->debitmemoDateHijri);

        $approvedDataSum = $temp["detail"];
        $approvedDataSumTotal = $temp["total"];

        $costTable2 = '<w:tbl><w:tblPr><w:tblStyle w:val="TableGrid"/><w:bidiVisual/><w:tblW w:w="9652" w:type="dxa"/><w:tblInd w:w="-327" w:type="dxa"/><w:tblLook w:val="04A0" w:firstRow="1" w:lastRow="0" w:firstColumn="1" w:lastColumn="0" w:noHBand="0" w:noVBand="1"/></w:tblPr><w:tblGrid><w:gridCol w:w="2003"/><w:gridCol w:w="704"/><w:gridCol w:w="1984"/><w:gridCol w:w="1843"/><w:gridCol w:w="3118"/></w:tblGrid>';
        $costTable2 .= '<w:tr w:rsidR="00CD0BAB" w:rsidTr="003D52C8"><w:tc><w:tcPr><w:tcW w:w="2003" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="00527ADC" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00527ADC"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>نوع هزینه</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="704" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="00527ADC" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00527ADC"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>نرخ</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1984" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="00527ADC" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00527ADC"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>مبلغ دوره ای</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1843" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="00527ADC" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00527ADC"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>مبلغ تجمعی</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="3118" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="00527ADC" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00527ADC"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>شماره اعلامیه ها</w:t></w:r></w:p></w:tc></w:tr>';

        $debitmemoList = $this->generateLetter704_debitmemoList($debitmemoModel->contractIdRef, $debitmemoModel->debitmemoDateHijri);

        foreach ($approvedDataSum as $key => $value) {
            $listString = isset($debitmemoList[$value["InvoiceType"]][$value["CurrencyTitle"]]) ? $debitmemoList[$value["InvoiceType"]][$value["CurrencyTitle"]] : "";

            if ($invoiceType == $value["InvoiceType"] && $currencyTitle1 == $value["CurrencyTitle"]) {
                $costTable2 .= '<w:tr w:rsidR="00CD0BAB" w:rsidTr="003D52C8"><w:tc><w:tcPr><w:tcW w:w="2003" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr><w:t>' . $value["InvoiceType"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="704" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr><w:t>' . $value["CurrencyTitle"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1984" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($sumApproved1, $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1843" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($value["total"], $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="3118" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . $listString . '</w:t></w:r></w:p></w:tc></w:tr>';
            } elseif ($invoiceType == $value["InvoiceType"] && $currencyTitle2 == $value["CurrencyTitle"]) {
                $costTable2 .= '<w:tr w:rsidR="00CD0BAB" w:rsidTr="003D52C8"><w:tc><w:tcPr><w:tcW w:w="2003" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr><w:t>' . $value["InvoiceType"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="704" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr><w:t>' . $value["CurrencyTitle"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1984" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($sumApproved2, $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1843" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($value["total"], $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="3118" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . $listString . '</w:t></w:r></w:p></w:tc></w:tr>';
            } else {
                $costTable2 .= '<w:tr w:rsidR="00CD0BAB" w:rsidTr="003D52C8"><w:tc><w:tcPr><w:tcW w:w="2003" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr><w:t>' . $value["InvoiceType"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="704" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/></w:rPr><w:t>' . $value["CurrencyTitle"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1984" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>___</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1843" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($value["total"], $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="3118" w:type="dxa"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . $listString . '</w:t></w:r></w:p></w:tc></w:tr>';
            }
        }

        foreach ($approvedDataSumTotal as $key => $value) {
            if ($currencyTitle1 == $value["CurrencyTitle"]) {
                $costTable2 .= '<w:tr w:rsidR="00CD0BAB" w:rsidTr="003D52C8"><w:tc><w:tcPr><w:tcW w:w="2003" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>جمع</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="704" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . $value["CurrencyTitle"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1984" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($sumApproved1, $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1843" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($value["total"], $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="3118" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>___</w:t></w:r></w:p></w:tc></w:tr>';
            } elseif ($currencyTitle2 == $value["CurrencyTitle"]) {
                $costTable2 .= '<w:tr w:rsidR="00CD0BAB" w:rsidTr="003D52C8"><w:tc><w:tcPr><w:tcW w:w="2003" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>جمع</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="704" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . $value["CurrencyTitle"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1984" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($sumApproved2, $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1843" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($value["total"], $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="3118" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>___</w:t></w:r></w:p></w:tc></w:tr>';
            } else {
                $costTable2 .= '<w:tr w:rsidR="00CD0BAB" w:rsidTr="003D52C8"><w:tc><w:tcPr><w:tcW w:w="2003" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>جمع</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="704" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . $value["CurrencyTitle"] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1984" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>___</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1843" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>' . Utility::moneyFormatByCurrency($value["total"], $value["CurrencyTitle"]) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="3118" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/></w:tcPr><w:p w:rsidR="00CD0BAB" w:rsidRPr="0077493F" w:rsidRDefault="00CD0BAB" w:rsidP="003D52C8"><w:pPr><w:spacing w:line="276" w:lineRule="auto"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>___</w:t></w:r></w:p></w:tc></w:tr>';
            }
        }

        $costTable2 .= '</w:tbl>';
        $word->setBookmarkValue('bmDebitmemototal', $costTable2);

        if ($letterFormModel->Evidence15Chek == 1) {
            $word->setBookmarkValue('bmEvidence15No', ($letterFormModel->Evidence15No));
            $word->setBookmarkValue('bmEvidence15Date', Utility::invertString($letterFormModel->Evidence15Date, '/'));
        } else {
            $word->deleteBlock("مستنداتپیمانکار");
        }

        $activityManagement = new \app\components\ActivityManagement();
        $actionType = "Letter";
        $subType = "ExportLetter";
        $subTypeDescription["LetterName"] = "DebitmemoLetter";
        $subTypeDescription["FileName"] = "Letter-DebitPSA.docx";
        $subTypeDescription["FilePatch"] = \app\components\Templates::getTemplate('word', 'Letter-DebitPSA.docx');
        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    private function generateLetter704_debitmemoList($cid, $cutOffDate)
    {
        $debitmemoTable = \app\modules\debitmemo\models\Debitmemo::tableName();
        $debitmemoAmountTable = \app\modules\debitmemo\models\DebitmemoAmount::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $invoiceTable = \app\modules\invoice\models\InvoiceEdit::tableName();
        $invoiceTypeTable = \app\modules\invoice\models\InvoiceTypes::tableName();
        $invoiceAmountTable = \app\modules\invoice\models\InvoiceAmountEdit::tableName();
        $currencyTable = \app\modules\currency\models\Currency::tableName();

        $contractId = $cid;
        $cutOffDate = $cutOffDate;

        $amountStatusArray = \app\modules\debitmemo\models\Debitmemo::approvedActionArray();
        $mainSqlQuery = "select $invoiceTable.InvoiceType,$debitmemoAmountTable.currency1IdRef as currency,$debitmemoTable.debitmemoNo from $debitmemoAmountTable
                inner join $debitmemoTable on $debitmemoTable.id=$debitmemoAmountTable.debitmemoIdRef
                inner join $invoiceAmountTable on $invoiceAmountTable.ID=$debitmemoAmountTable.invoiceAmountIdRef
                inner join $invoiceTable on $invoiceTable.InvoiceID=$invoiceAmountTable.InvoiceID
                where debitmemoIdRef in (select id from $debitmemoTable where contractIdRef=$contractId)
                and debitmemoDateHijri<='$cutOffDate' and $debitmemoAmountTable.amountType='cost'
                group By $invoiceTable.InvoiceType,$debitmemoAmountTable.currency1IdRef,$debitmemoTable.debitmemoNo
                union All
                select $invoiceTable.InvoiceType,$debitmemoAmountTable.currency2IdRef as currency,$debitmemoTable.debitmemoNo from $debitmemoAmountTable
                inner join $debitmemoTable on $debitmemoTable.id=$debitmemoAmountTable.debitmemoIdRef
                inner join $invoiceAmountTable on $invoiceAmountTable.ID=$debitmemoAmountTable.invoiceAmountIdRef
                inner join $invoiceTable on $invoiceTable.InvoiceID=$invoiceAmountTable.InvoiceID
                where debitmemoIdRef in (select id from $debitmemoTable where contractIdRef=$contractId)
                and debitmemoDateHijri<='$cutOffDate' and $debitmemoAmountTable.amountType='cost'
                group By $invoiceTable.InvoiceType,$debitmemoAmountTable.currency2IdRef,$debitmemoTable.debitmemoNo";

        $sql = "select $invoiceTypeTable.InvoiceType,$currencyTable.CurrencyTitle,debitmemoNo from ($mainSqlQuery) totalTable
                inner join $currencyTable on $currencyTable.CurrencyID=totalTable.currency
                inner join $invoiceTypeTable on $invoiceTypeTable.InvoiceTypeID=totalTable.InvoiceType
                group by InvoiceType,$currencyTable.CurrencyTitle,debitmemoNo
                order by InvoiceType desc
            ";

        $temp = Yii::$app->db->createCommand($sql)->queryAll();
        $data = [];
//        var_dump($temp);
        foreach ($temp as $key => $value) {
            if (isset($data[$value["InvoiceType"]][$value["CurrencyTitle"]])) {
                $data[$value["InvoiceType"]][$value["CurrencyTitle"]] .= "/" . $value["debitmemoNo"];
            } else {
                $data[$value["InvoiceType"]][$value["CurrencyTitle"]] = $value["debitmemoNo"];
            }

//'InvoiceType' => string 'موقت' (length=8)
//'CurrencyTitle' => string 'USD' (length=3)
//'debitmemoNo' => string 'Debit Memo No. 1' (length=16)
        }

//            var_dump($data);
//        die();

        return $data;
    }

    private function generateLetter704_dataProvider($cid, $cutOffDate)
    {
        $debitmemoTable = \app\modules\debitmemo\models\Debitmemo::tableName();
        $debitmemoAmountTable = \app\modules\debitmemo\models\DebitmemoAmount::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $invoiceTable = \app\modules\invoice\models\InvoiceEdit::tableName();
        $invoiceTypeTable = \app\modules\invoice\models\InvoiceTypes::tableName();
        $invoiceAmountTable = \app\modules\invoice\models\InvoiceAmountEdit::tableName();
        $currencyTable = \app\modules\currency\models\Currency::tableName();

        $contractId = $cid;
        $cutOffDate = $cutOffDate;
        $amountStatusArray = \app\modules\debitmemo\models\Debitmemo::approvedActionArray();
        $mainSqlQuery = "select $invoiceTable.InvoiceType,$debitmemoAmountTable.currency1IdRef as currency,sum(approved1) as total from $debitmemoAmountTable
                inner join $debitmemoTable on $debitmemoTable.id=$debitmemoAmountTable.debitmemoIdRef
                inner join $invoiceAmountTable on $invoiceAmountTable.ID=$debitmemoAmountTable.invoiceAmountIdRef
                inner join $invoiceTable on $invoiceTable.InvoiceID=$invoiceAmountTable.InvoiceID
                where debitmemoIdRef in (select id from $debitmemoTable where contractIdRef=$contractId)
                and debitmemoDateHijri<='$cutOffDate' and $debitmemoAmountTable.amountType='cost'
                group By $invoiceTable.InvoiceType,$debitmemoAmountTable.currency1IdRef
                union All
                select $invoiceTable.InvoiceType,$debitmemoAmountTable.currency2IdRef as currency,sum(approved2) as total from $debitmemoAmountTable
                inner join $debitmemoTable on $debitmemoTable.id=$debitmemoAmountTable.debitmemoIdRef
                inner join $invoiceAmountTable on $invoiceAmountTable.ID=$debitmemoAmountTable.invoiceAmountIdRef
                inner join $invoiceTable on $invoiceTable.InvoiceID=$invoiceAmountTable.InvoiceID
                where debitmemoIdRef in (select id from $debitmemoTable where contractIdRef=$contractId)
                and debitmemoDateHijri<='$cutOffDate' and $debitmemoAmountTable.amountType='cost'
                group By $invoiceTable.InvoiceType,$debitmemoAmountTable.currency2IdRef";

        $sql = "select $invoiceTypeTable.InvoiceType,$currencyTable.CurrencyTitle,sum(total) as total from ($mainSqlQuery) totalTable
                inner join $currencyTable on $currencyTable.CurrencyID=totalTable.currency
                inner join $invoiceTypeTable on $invoiceTypeTable.InvoiceTypeID=totalTable.InvoiceType
                group by InvoiceType,$currencyTable.CurrencyTitle
                order by InvoiceType desc
            ";

        $sqlTatal = "select $currencyTable.CurrencyTitle,sum(total) as total from ($mainSqlQuery) totalTable
                inner join $currencyTable on $currencyTable.CurrencyID=totalTable.currency
                inner join $invoiceTypeTable on $invoiceTypeTable.InvoiceTypeID=totalTable.InvoiceType
                group by $currencyTable.CurrencyTitle
                order by totalTable.InvoiceType desc
            ";

        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $dataTatal = Yii::$app->db->createCommand($sqlTatal)->queryAll();
        return [
            "detail" => $data,
            "total" => $dataTatal
        ];
    }

    private function generateLetter705($letterFormModel, $invoiceModel)
    {
        $templatePath = Templates::getTemplate('word', "Invoice-LetterNSR.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $invoiceModel->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($invoiceModel->currency2->CurrencyPersianTitle) ? $invoiceModel->currency2->CurrencyPersianTitle : '');

        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        $word->setBookmarkValue('bmSubContractor', str_replace('&', '-', $invoiceModel->contract->subContractor->PersonTitle));
        $word->setBookmarkValue('bmInvoiceType', $invoiceModel->type->InvoiceType);
        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($invoiceModel->contract->ContractNo)));
        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($invoiceModel->Title, '-'));

        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $invoiceModel->contract->ContractSubject));   //@ToDo: در مقدار این فیلد هرگاه کاراکتر (&)باشد نامه کلا باز نمیشود  
        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($invoiceModel->CutOffDateHijri, '/'));

        if ($invoiceModel->InvoiceType == 17 || $invoiceModel->InvoiceType == 18 || $invoiceModel->InvoiceType == 19 || $invoiceModel->InvoiceType == 20 || $invoiceModel->InvoiceType == 21) {
            $lateral = $this->cumulativeAditionalCost($invoiceModel);

            $word->setBookmarkValue('bmComulativeApproved1', '');
            $word->setBookmarkValue('bmComulativeApproved1Currency', '');
            $word->setBookmarkValue('bmComulativeApproved1Text', $lateral);

            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmApproved2', '');
            }
        } else {
            $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle));
            $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
            $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);


            if ($currency2PersianTitle <> '') {
                $word->setBookmarkValue('bmComulativeApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle) . ' ' . $currency2PersianTitle
                    . ' (' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
                $word->setBookmarkValue('bmApproved2', 'و ' . Utility::moneyFormatWord($invoiceModel->approved2, $currency2PersianTitle)
                    . ' ' . $currency2PersianTitle . '(' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle . ')');
            } else {
                $word->setBookmarkValue('bmComulativeApproved2', '');
                $word->setBookmarkValue('bmApproved2', '');
            }
        }

        $word->setBookmarkValue('bmApproved1', Utility::moneyFormatWord($invoiceModel->approved1, $currency1PersianTitle));

        $word->setBookmarkValue('bmApproved1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        // بررسي وجود هزينه بین شرکتی
        $modelInvoice = InvoiceEdit::amountByOwnerAndProject($invoiceModel->InvoiceID);
        if (count($modelInvoice) > 1) {
            $costTable = '<w:tbl><w:tblPr><w:bidiVisual/><w:tblW w:w="0" w:type="auto"/><w:tblBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:left w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:right w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:insideH w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:insideV w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tblBorders><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="2331"/><w:gridCol w:w="1906"/><w:gridCol w:w="664"/><w:gridCol w:w="2155"/><w:gridCol w:w="2187"/></w:tblGrid>';
            $costTable .= '<w:tr w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidTr="00820A6E"><w:trPr><w:trHeight w:val="810"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="2331" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="008037F1" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:ind w:firstLine="27"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="24"/><w:szCs w:val="24"/><w:rtl/></w:rPr></w:pPr><w:bookmarkStart w:id="0" w:name="bmInvoiceAmountByOwner"/><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>نام شرکت</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1906" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>عنوان پروژه</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="664" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr><w:t>واحد</w:t></w:r></w:p><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr><w:t>پولی</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2155" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>مبلغ دوره ای</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2187" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="BFBFBF" w:themeFill="background1" w:themeFillShade="BF"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:ind w:firstLine="162"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:rtl/></w:rPr><w:t>مبلغ تجمعی</w:t></w:r></w:p></w:tc></w:tr>';

            foreach ($modelInvoice as $value) {
                $costTable .= '<w:tr w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidTr="00820A6E"><w:trPr><w:trHeight w:val="454"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="2331" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus" w:hint="cs"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr><w:t>' . $value['mainOwnerTitle'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1906" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $value['nodeText'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="664" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . $value['currencyTitle'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2155" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . Utility::moneyFormat($value['approved']) . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2187" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="auto"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00FE60B7" w:rsidRPr="00DC2DC4" w:rsidRDefault="00A17AD9" w:rsidP="00820A6E"><w:pPr><w:spacing w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/><w:rtl/></w:rPr></w:pPr><w:r w:rsidRPr="00DC2DC4"><w:rPr><w:rFonts w:cs="B Lotus"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr><w:t>' . Utility::moneyFormat($value['cumulativeApproved']) . '</w:t></w:r></w:p></w:tc></w:tr>';
            }
            $costTable .= '</w:tbl>';
            $word->setBookmarkValue('bmInvoiceAmountByOwner', $costTable);
        } else {
            $word->deleteBlock('بلاکهزینه');
        }

        if ($letterFormModel->Description) {
            $word->setVarValue('توضیحات', $this->convertNumbers($letterFormModel->Description));
            $word->setBookmarkValue('bmDescription', $this->convertNumbers($letterFormModel->Description));
        } else {
            $word->deleteBlock('بلاکتوضیحات');
        }

        //    جریمه
        if ($invoiceModel->cumulativePenalty1(6) <> 0 && $letterFormModel->Penalty == 1) {
            $contractPenalty1Text = trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle;

            $word->setVarValue('متنکاملبخشجرایم', "همچنین مبلغ تجمعی " . Utility::moneyFormatWord($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle) . " $currency1PersianTitle($contractPenalty1Text) بابت جرائم (مستندات پیوست) می بایستی از حساب پیمانکار فوق کسر گردد.");
        } else {
            $word->deleteBlock('بلاکجریمه');
        }
        // بدهی

        if ($invoiceModel->cumulativeIncome1(6) <> 0 && $letterFormModel->Income == 1) {
            $cumulative2 = '';
            if ($invoiceModel->cumulativeIncome2(6) <> 0 && $currency2PersianTitle <> '') {
                $cumulative2 = 'و ' . Utility::moneyFormatWord($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle) . " $currency2PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome2(6), $currency2PersianTitle, ''), 2) . " $currency2PersianTitle)";
            }
            $income = "لازم به ذکر است ليست بدهي پيمانکاران به مبلغ تجمعي ";
            $income .= Utility::moneyFormatWord($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle) . " $currency1PersianTitle(" . Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeIncome1(6), $currency1PersianTitle, ''), 2) . " $currency1PersianTitle)";
            $income .= $cumulative2;
            $income .= "و مبالغ دوره اي مربوطه، به پيوست اين صورت وضعيت جهت درج در حساب پيمانکاران ذکر شده ارسال مي گردد.";
            $word->setVarValue('متنکاملبخشبدهی', $income);
        } else {
            $word->deleteBlock('بلاکبدهی');
        }

        if ($letterFormModel->Evidence1Chek == 1) {
            $word->setBookmarkValue('bmEvidence1No', ($letterFormModel->Evidence1No));
            $word->setBookmarkValue('bmEvidence1Date', $this->convertNumbers($letterFormModel->Evidence1Date));
        } else {
            $word->deleteBlock("مستنداتپیمانکار");
        }

        if ($letterFormModel->Evidence2Chek == 1) {
            $word->setBookmarkValue('bmEvidence2No', ($letterFormModel->Evidence2No));
            $word->setBookmarkValue('bmEvidence2Date', $this->convertNumbers($letterFormModel->Evidence2Date));
        } else {
            $word->deleteBlock("مستنداتریاستکارگاه");
        }

        if ($letterFormModel->Evidence3Chek == 1) {
            $word->setBookmarkValue('bmEvidence3No', ($letterFormModel->Evidence3No));
            $word->setBookmarkValue('bmEvidence3Date', $this->convertNumbers($letterFormModel->Evidence3Date));
        } else {
            $word->deleteBlock("مستنداتمدیرهماهنگکننده");
        }

        //CC Part

        if ($letterFormModel->CcCheckModirAmelalc == 1) {
            $word->setVarValue('مدیرعامل', $letterFormModel->CcModirAmelNSR);
        } else {
            $word->deleteBlock("رونوشتمدیرعامل");
        }

        if ($letterFormModel->CcCheckMoavenMarbote == 1) {
            $word->setVarValue('رییسکارگاه', $letterFormModel->CcMoavenMarbote);
        } else {
            $word->deleteBlock("رونوشترییسکارگاه");
        }

        if ($letterFormModel->CcCheckModirHoghooghi == 1) {
            $word->setVarValue('مدیرحقوقی', $letterFormModel->CcModirHoghooghiNSR);
        } else {
            $word->deleteBlock("رونوشتمدیرحقوقی");
        }

        if ($letterFormModel->CcCheckModirMohandesi == 1) {
            $word->setVarValue('مدیرمهندسی', $letterFormModel->CcModieMohandesiNSR);
        } else {
            $word->deleteBlock("رونوشتمدیرمهندسی");
        }

        $activityManagement = new \app\components\ActivityManagement();
        $actionType = "Letter";
        $subType = "ExportLetter";
        $subTypeDescription["LetterName"] = "InvoiceLetter";
        $subTypeDescription["FileName"] = "Invoice-LetterNSR.docx";
        $subTypeDescription["FilePatch"] = \app\components\Templates::getTemplate('word', 'Invoice-LetterNSR.docx');
        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    private function generateLetter706($letterFormModel, $invoiceModel)
    {
        $templatePath = Templates::getTemplate('word', "Pksk-Invoice-706.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $invoiceModel->currency1->CurrencyTitle;
        $currency2PersianTitle = (isset($invoiceModel->currency2->CurrencyTitle) ? $invoiceModel->currency2->CurrencyTitle : '');

        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));

        $word->setBookmarkValue('field01', str_replace('&', '-', strtoupper($invoiceModel->contract->ContractNo)));
        $word->setBookmarkValue('field02', str_replace('&', '-', $invoiceModel->contract->subContractor->PersonTitle));
        $word->setBookmarkValue('field03', str_replace('&', '-', $invoiceModel->contract->ContractSubject));
        $word->setBookmarkValue('field04', '');
        $word->setBookmarkValue('field05', '');
        $word->setBookmarkValue('field06', $invoiceModel->Title);
        $word->setBookmarkValue('field07', $invoiceModel->type->InvoiceType);
        $word->setBookmarkValue('field08', $invoiceModel->CutOffDateHijri);
        $word->setBookmarkValue('field09', '');
        $word->setBookmarkValue('field10', Utility::moneyFormatByCurrency($invoiceModel->contract->lastRider->Price1, $currency1PersianTitle) . '       ' . $currency1PersianTitle);
        $word->setBookmarkValue('field11', Utility::moneyFormatByCurrency($invoiceModel->approved1, $currency1PersianTitle) . '       ' . $currency1PersianTitle);
        $word->setBookmarkValue('field12', Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle) . '       ' . $currency1PersianTitle);
        $word->setBookmarkValue('description', $invoiceModel->Description);
        $word->setBookmarkValue('number01', $letterFormModel->number01 != 0 ? $letterFormModel->number01 : '_');
        $word->setBookmarkValue('number02', $letterFormModel->number02 != 0 ? $letterFormModel->number02 : '_');
        $word->setBookmarkValue('number03', $letterFormModel->number03 != 0 ? $letterFormModel->number03 : '_');
        $word->setBookmarkValue('number04', $letterFormModel->number04 != 0 ? $letterFormModel->number04 : '_');
        $word->setBookmarkValue('number05', $letterFormModel->number05 != 0 ? $letterFormModel->number05 : '_');
        $word->setBookmarkValue('number06', $letterFormModel->number06 != 0 ? $letterFormModel->number06 : '_');
        $word->setBookmarkValue('number07', $letterFormModel->number07 != 0 ? $letterFormModel->number07 : '_');
        $word->setBookmarkValue('number08', $letterFormModel->number08 != 0 ? $letterFormModel->number08 : '_');
        $word->setBookmarkValue('number09', $letterFormModel->number09 != 0 ? $letterFormModel->number09 : '_');

        $word->setBookmarkValue('totalNumber', (
            intval($letterFormModel->number01) +
            intval($letterFormModel->number02) +
            intval($letterFormModel->number03) +
            intval($letterFormModel->number04) +
            intval($letterFormModel->number05) +
            intval($letterFormModel->number06) +
            intval($letterFormModel->number07) +
            intval($letterFormModel->number08) +
            intval($letterFormModel->number09)
        )
        );
        if (isset($currency2PersianTitle)) {
            $word->setBookmarkValue('field13', Utility::moneyFormatByCurrency($invoiceModel->contract->lastRider->Price2, $currency2PersianTitle) . '       ' . $currency2PersianTitle);
            $word->setBookmarkValue('field14', Utility::moneyFormatByCurrency($invoiceModel->approved2, $currency2PersianTitle) . '       ' . $currency2PersianTitle);
            $word->setBookmarkValue('field15', Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved2(6), $currency2PersianTitle) . '       ' . $currency2PersianTitle);
        } else {
            $word->setBookmarkValue('field13', '');
            $word->setBookmarkValue('field14', '');
            $word->setBookmarkValue('field15', '');
        }

        $selected = json_decode('"\u29BF"');
        $unSelected = json_decode('"\u29BE"');

        if ($letterFormModel->checkBox01 == '1' || !empty($letterFormModel->textBox01)) {
            $word->setBookmarkValue('checked01', $selected);
            $word->setBookmarkValue('textBox01', $letterFormModel->textBox01);
        } else {
            $word->setBookmarkValue('textBox01', '');
            $word->setBookmarkValue('checked01', $unSelected);
        }

        if ($letterFormModel->checkBox02 == '1' || !empty($letterFormModel->textBox02)) {
            $word->setBookmarkValue('checked02', $selected);
            $word->setBookmarkValue('textBox02', $letterFormModel->textBox02);
        } else {
            $word->setBookmarkValue('checked02', $unSelected);
            $word->setBookmarkValue('textBox02', '');
        }

        if ($letterFormModel->checkBox03 == '1' || !empty($letterFormModel->textBox03)) {
            $word->setBookmarkValue('checked03', $selected);
            $word->setBookmarkValue('textBox03', $letterFormModel->textBox03);
        } else {
            $word->setBookmarkValue('checked03', $unSelected);
            $word->setBookmarkValue('textBox03', '');
        }

        if ($letterFormModel->checkBox04 == '1' || !empty($letterFormModel->textBox04)) {
            $word->setBookmarkValue('checked04', $selected);
            $word->setBookmarkValue('textBox04', $letterFormModel->textBox04);
        } else {
            $word->setBookmarkValue('checked04', $unSelected);
            $word->setBookmarkValue('textBox04', '');
        }

        if ($letterFormModel->checkBox05 == '1' || !empty($letterFormModel->textBox05)) {
            $word->setBookmarkValue('checked05', $selected);
            $word->setBookmarkValue('textBox05', $letterFormModel->textBox05);
        } else {
            $word->setBookmarkValue('checked05', $unSelected);
            $word->setBookmarkValue('textBox05', '');
        }

        if ($letterFormModel->checkBox06 == '1' || !empty($letterFormModel->textBox06)) {
            $word->setBookmarkValue('checked06', $selected);
            $word->setBookmarkValue('textBox06', $letterFormModel->textBox06);
        } else {
            $word->setBookmarkValue('checked06', $unSelected);
            $word->setBookmarkValue('textBox06', '');
        }

        if ($letterFormModel->checkBox07 == '1' || !empty($letterFormModel->textBox07)) {
            $word->setBookmarkValue('checked07', $selected);
            $word->setBookmarkValue('textBox07', $letterFormModel->textBox07);
        } else {
            $word->setBookmarkValue('checked07', $unSelected);
            $word->setBookmarkValue('textBox07', '');
        }

        if ($letterFormModel->checkBox08 == '1' || !empty($letterFormModel->textBox08)) {
            $word->setBookmarkValue('checked08', $selected);
            $word->setBookmarkValue('textBox08', $letterFormModel->textBox08);
        } else {
            $word->setBookmarkValue('checked08', $unSelected);
            $word->setBookmarkValue('textBox08', '');
        }

        if ($letterFormModel->checkBox09 == '1' || !empty($letterFormModel->textBox09)) {
            $word->setBookmarkValue('checked09', $selected);
            $word->setBookmarkValue('textBox09', $letterFormModel->textBox09);
        } else {
            $word->setBookmarkValue('checked09', $unSelected);
            $word->setBookmarkValue('textBox09', '');
        }

//        if ($letterFormModel->checkBox10 == '1') {
//            $word->setBookmarkValue('checked10', $selected);
//        }else{
//            $word->setBookmarkValue('checked10', $unSelected);
//        }
        $activityManagement = new \app\components\ActivityManagement();
        $actionType = "Letter";
        $subType = "ExportLetter";
        $subTypeDescription["LetterName"] = "InvoiceLetter";
        $subTypeDescription["FileName"] = "Pksk-Invoice-706.docx";
        $subTypeDescription["FilePatch"] = \app\components\Templates::getTemplate('word', 'Pksk-Invoice-706.docx');
        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    public function generateLetter707($letterFormModel, $invoiceModel)
    {

        $templatePath = Templates::getTemplate('word', "LetterID707-NSR.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $invoiceModel->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($invoiceModel->currency2->CurrencyPersianTitle) ? $invoiceModel->currency2->CurrencyPersianTitle : '');


        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        $word->setBookmarkValue('bmSubContractor', str_replace('&', '-', $invoiceModel->contract->subContractor->PersonTitle));
        $word->setBookmarkValue('bmInvoiceType', $invoiceModel->type->InvoiceType);
        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($invoiceModel->contract->ContractNo)));
        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($invoiceModel->Title, '-'));
        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $invoiceModel->contract->ContractSubject));

        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($invoiceModel->CutOffDateHijri, '/'));


        if ($invoiceModel->InvoiceType == 17 || $invoiceModel->InvoiceType == 18 || $invoiceModel->InvoiceType == 19 || $invoiceModel->InvoiceType == 20 || $invoiceModel->InvoiceType == 21) {
            $lateral = $this->cumulativeAditionalCost($invoiceModel);

            $word->setBookmarkValue('bmComulativeApproved1', '');
            $word->setBookmarkValue('bmComulativeApproved1Currency', '');
            $word->setBookmarkValue('bmComulativeApproved1Text', $lateral);
        } else {
            $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle));
            $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
            $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);
        }

        $word->setBookmarkValue('bmApproved1', Utility::moneyFormatWord($invoiceModel->approved1, $currency1PersianTitle));
        $word->setBookmarkValue('bmApproved1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);

        $word->setBookmarkValue('bmcumulativePenalty1', Utility::moneyFormatWord($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle));
        $word->setBookmarkValue('bmcumulativePenalty1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmcumulativePenalty1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);


        $word->setBookmarkValue('bmComulativeAdjustment1', Utility::invertString($invoiceModel->cumulativeAdjustment1(6), '/'));
        $word->setBookmarkValue('bmComulativeAdjustment1Currency', $currency1PersianTitle);
        $word->setBookmarkValue('bmComulativeAdjustment1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeAdjustment1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);


        if ($letterFormModel->Evidence4Chek == 1) {
            $word->setBookmarkValue('bmEvidence4Date', Utility::invertString($letterFormModel->Evidence4Date, '/'));
        }

        if ($letterFormModel->Evidence5Chek == 1) {
            $word->setBookmarkValue('bmEvidence5Date', Utility::invertString($letterFormModel->Evidence5Date, '/'));
        }

        $activityManagement = new \app\components\ActivityManagement();
        $actionType = "Letter";
        $subType = "ExportLetter";
        $subTypeDescription["LetterName"] = "InvoiceLetter";
        $subTypeDescription["FileName"] = "LetterID707-NSR.docx";
        $subTypeDescription["FilePatch"] = \app\components\Templates::getTemplate('word', 'LetterID707-NSR.docx');
        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
        return $word->output($wordFileName, true);
    }

    public function generateLetter1201($letterFormModel, $tenderModel)
    {

        $templatePath = Templates::getTemplate('word', "LetterID1201-NSR.docx");
        $word = new phpWordCando($templatePath);

        $currency1PersianTitle = $tenderModel->currency1->CurrencyPersianTitle;
        $currency2PersianTitle = (isset($tenderModel->currency2->CurrencyPersianTitle) ? $tenderModel->currency2->CurrencyPersianTitle : '');

        $projectsTitle = $tenderModel->projectsTitle;
        $Titles = null;
        foreach ($projectsTitle as $k => $projectTitle) {
            if ($Titles != null) {
                $Titles .= "و";
            }
            $Titles .= $projectTitle['EPSTitle'];
        }
        $word->setVarValue('شماره', Utility::invertString($letterFormModel->LetterNumber, '-'));
        $word->setVarValue('تاریخ', Utility::invertString($letterFormModel->LetterDate, '/'));
        if (!empty($tenderModel->subject)) {
            $word->setBookmarkValue('tnContractSubject', $tenderModel->subject);
        }
        if (!empty($tenderModel->company->companyName)) {
            $word->setBookmarkValue('tnCompanyTitle', $tenderModel->company->companyName);
        }
        if (!empty($tenderModel->company->companyAddress)) {
            $word->setBookmarkValue('tnCompanyAddress', $tenderModel->company->companyAddress);
        }
        if (!empty($tenderModel->company->companyPhone)) {
            $word->setBookmarkValue('tnCompanyPhone', $tenderModel->company->companyPhone);
        }
        if (!empty($tenderModel->company->companyFax)) {
            $word->setBookmarkValue('tnCompanyFax', $tenderModel->company->companyFax);
        }
        if (!empty($tenderModel->company->directorManager)) {
            $word->setBookmarkValue('tnCompanyManager', $tenderModel->company->directorManager);
        }
        if (!empty($tenderModel->requestDate)) {
            $word->setBookmarkValue('tnStartDate',  Utility::invertString(\app\components\Utility::convertTimestampToJalali($tenderModel->requestDate), '/'));
        }
        if (!empty($tenderModel->duration)) {
            $word->setBookmarkValue('tnDurationTime',$tenderModel->duration . $tenderModel->getDurationUnit($tenderModel->durationUnit,'FA'));

            $word->setBookmarkValue('tnStopDate',Utility::invertString(\app\components\Utility::convertTimestampToJalali($tenderModel->calculateTenderFinalDate()), '/'));

        }

        if (!empty($Titles)) {
            $word->setBookmarkValue('tnProjectTitle', $Titles);
        }
        $commission = $tenderModel->lastCommision;
        if (!empty($commission->finalWinner->company->companyName)) {

            $word->setBookmarkValue('tnSubContractorTitle', $commission->finalWinner->company->companyName);
        }
        if (!empty($commission->finalWinner->company->companyAddress)) {

            $word->setBookmarkValue('tnSubContractorAddress', $commission->finalWinner->company->companyAddress);
        }
        if (!empty($commission->finalWinner->company->companyPhone)) {
            $word->setBookmarkValue('tnSubContractorPhone', $commission->finalWinner->company->companyPhone);
        }
        if (!empty($commission->finalWinner->company->directorManager)) {
            $word->setBookmarkValue('tnSubContractorManagerName', $commission->finalWinner->company->directorManager);
        }
        if (!empty($commission->finalWinner->company->registrationNumber)) {
            $word->setBookmarkValue('tnSubContractorRegisterNumber', $commission->finalWinner->company->registrationNumber);
        }
        if (!empty($commission->WinnerPrice3Currency1)) {
            if (isset($commission->WinnerPrice3Currency2)) {
                $word->setBookmarkValue('tnSubContractorPriceNumber', Utility::moneyFormatWord($commission->WinnerPrice3Currency1, $currency1PersianTitle) . ' و ' . Utility::moneyFormatWord($commission->WinnerPrice3Currency2, $currency2PersianTitle));
                $word->setBookmarkValue('tnSubContractorPriceText', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($commission->WinnerPrice3Currency1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle . ' و ' . trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($commission->WinnerPrice3Currency2, $currency2PersianTitle, ''), 2)) . ' ' . $currency2PersianTitle);

                $word->setBookmarkValue('tn5PercentOfPrice', Utility::moneyFormatWord($commission->WinnerPrice3Currency1*0.05, $currency1PersianTitle) . ' و ' . Utility::moneyFormatWord($commission->WinnerPrice3Currency2*0.05, $currency2PersianTitle));

            } else {
                $word->setBookmarkValue('tnSubContractorPriceNumber', Utility::moneyFormatWord($commission->WinnerPrice3Currency1, $currency1PersianTitle));
                $word->setBookmarkValue('tnSubContractorPriceText', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($commission->WinnerPrice3Currency1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);
                $word->setBookmarkValue('tn5PercentOfPrice', Utility::moneyFormatWord($commission->WinnerPrice3Currency1*0.05, $currency1PersianTitle));

            }
        }
//        $word->setBookmarkValue('bmContractNumber', str_replace('&', '-', strtoupper($tenderModel->contract->ContractNo)));
//        $word->setBookmarkValue('bmInvoiceTitle', Utility::invertString($tenderModel->Title, '-'));
//        $word->setBookmarkValue('bmContractSubject', str_replace('&', '-', $tenderModel->contract->ContractSubject));
//
//        $word->setBookmarkValue('bmCutOffDateHijri', Utility::invertString($tenderModel->CutOffDateHijri, '/'));


//        if ($tenderModel->InvoiceType == 17 || $tenderModel->InvoiceType == 18 || $tenderModel->InvoiceType == 19 || $tenderModel->InvoiceType == 20 || $tenderModel->InvoiceType == 21) {
//            $lateral = $this->cumulativeAditionalCost($invoiceModel);
//
//            $word->setBookmarkValue('bmComulativeApproved1', '');
//            $word->setBookmarkValue('bmComulativeApproved1Currency', '');
//            $word->setBookmarkValue('bmComulativeApproved1Text', $lateral);
//        } else {
//            $word->setBookmarkValue('bmComulativeApproved1', Utility::moneyFormatWord($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle));
//            $word->setBookmarkValue('bmComulativeApproved1Currency', $currency1PersianTitle);
//            $word->setBookmarkValue('bmComulativeApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeApproved1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);
//        }
//
//        $word->setBookmarkValue('bmApproved1', Utility::moneyFormatWord($invoiceModel->approved1, $currency1PersianTitle));
//        $word->setBookmarkValue('bmApproved1Currency', $currency1PersianTitle);
//        $word->setBookmarkValue('bmApproved1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->approved1, $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);
//
//        $word->setBookmarkValue('bmcumulativePenalty1', Utility::moneyFormatWord($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle));
//        $word->setBookmarkValue('bmcumulativePenalty1Currency', $currency1PersianTitle);
//        $word->setBookmarkValue('bmcumulativePenalty1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativePenalty1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);
//
//
//        $word->setBookmarkValue('bmComulativeAdjustment1', Utility::invertString($invoiceModel->cumulativeAdjustment1(6), '/'));
//        $word->setBookmarkValue('bmComulativeAdjustment1Currency', $currency1PersianTitle);
//        $word->setBookmarkValue('bmComulativeAdjustment1Text', trim(Utility::numToPersianString(Utility::moneyFormatByCurrency($invoiceModel->cumulativeAdjustment1(6), $currency1PersianTitle, ''), 2)) . ' ' . $currency1PersianTitle);
//
//
//        if ($letterFormModel->Evidence4Chek == 1) {
//            $word->setBookmarkValue('bmEvidence4Date', Utility::invertString($letterFormModel->Evidence4Date, '/'));
//        }
//
//        if ($letterFormModel->Evidence5Chek == 1) {
//            $word->setBookmarkValue('bmEvidence5Date', Utility::invertString($letterFormModel->Evidence5Date, '/'));
//        }

        $activityManagement = new \app\components\ActivityManagement();
        $actionType = "Letter";
        $subType = "ExportLetter";
        $subTypeDescription["LetterName"] = "ContractLetter";
        $subTypeDescription["FileName"] = "LetterID1201-NSR.docx";
        $subTypeDescription["FilePatch"] = \app\components\Templates::getTemplate('word', 'LetterID1201-NSR.docx');
        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

        $wordFileName = "letter-{$this->fileIdRef}" . uniqid() . '.docx';
//        var_dump($wordFileName);
//        die();
        return $word->output($wordFileName, true);
    }

    public function generate($letterFormModel, $processEntityModel)
    {
        $letterFormModel->fillEvidence($this->fileIdRef, $processEntityModel);
        \app\components\ChromePhp::log($this->fileIdRef);
//        echo  'klamali022'.$this->fileIdRef;
//            die();
        if ($this->fileIdRef == 26) {
            return $this->generateLetter26($letterFormModel, $processEntityModel->invoice);
        }
        if ($this->fileIdRef == 13) {
            return $this->generateLetter13($letterFormModel, $processEntityModel->invoice);
        }
        if ($this->fileIdRef == 15) {
            return $this->generateLetter15($letterFormModel, $processEntityModel->invoice);
        }
        if ($this->fileIdRef == 21) {
            return $this->generateLetter21($letterFormModel, $processEntityModel->invoice);
        }
        if ($this->fileIdRef == 22) {
            return $this->generateLetter22($letterFormModel, $processEntityModel->invoice);
        }
        if ($this->fileIdRef == 28) {
            return $this->generateLetter28($letterFormModel, $processEntityModel->invoice);
        }
        if ($this->fileIdRef == 31) {
            return $this->generateLetter31($letterFormModel, $processEntityModel->invoice);
        }
        if ($this->fileIdRef == 32) {
            return $this->generateLetter32($letterFormModel, $processEntityModel->invoice);
        }
        if ($this->fileIdRef == 33) {
            return $this->generateLetter33($letterFormModel, $processEntityModel->invoice);
        }
        if ($this->fileIdRef == 60) {

            return $this->generateLetter60($letterFormModel, $processEntityModel->invoice);
        }
        if ($this->fileIdRef == 701) {
            return $this->generateLetter701($letterFormModel, $processEntityModel->invoice);
        }
        if ($this->fileIdRef == 703) {
            return $this->generateLetter703($letterFormModel, $processEntityModel);
        }
        if ($this->fileIdRef == 704) {
            return $this->generateLetter704($letterFormModel, $processEntityModel);
        }
        if ($this->fileIdRef == 705) {
            return $this->generateLetter705($letterFormModel, $processEntityModel->invoice);
        }
        if ($this->fileIdRef == 706) {
            return $this->generateLetter706($letterFormModel, $processEntityModel->invoice);
        }
        if ($this->fileIdRef == 707) {
            return $this->generateLetter707($letterFormModel, $processEntityModel->invoice);
        }
        if ($this->fileIdRef == 1201) {
            return $this->generateLetter1201($letterFormModel, $processEntityModel->tender);
        }
    }

}
