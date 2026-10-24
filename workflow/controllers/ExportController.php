<?php

namespace app\modules\workflow\controllers;

use Yii;
use yii\web\Controller;
use app\components\jDateTime;
use app\components\Utility;
use app\components\CandoHelper;
use app\modules\contract\models\Contract;
use app\modules\workflow\models\ProcessEntity;
use app\modules\OBS\models\Person;
use app\modules\invoice\models\InvoiceEdit;

class ExportController extends Controller {

    public $defaultAction = "list";
    public $access = null;

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['action-entity', 'export-invoice-duration'],
                'rules' => [
                    // allow authenticated users
                    [
                        'actions' => ['action-entity'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action, $exteraData = []) {
                            $peid = isset($exteraData['peid']) ? $exteraData['peid'] : Yii::$app->request->get('peid');
                            return \app\access\AccessControl::ProcessEntity(
                                    ['peid' => $peid, 'type' => 'All']
                            )->isAccess();
                        },
                    ],
                    [
                        'actions' => ['export-invoice-duration'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionActionEntity($peid) {
        $personTable = \app\modules\OBS\models\Person::tableName();
        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $userTable = CandoHelper::braketTable(\app\modules\account\models\User::tableName());

        $processEntityModel = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')->where(['ProcessEntityID' => $peid])->one();

        if (!($processEntityModel->parentProcessEntityIDRef > 0)) {
            $actionEntityData = \app\components\ActiveModel::find('\app\modules\workflow\models\ActionEntity')
                            ->joinWith(['action'])
                            ->leftJoin($userTable, "$userTable.UserID=$actionEntityTable.UserIDRef")
                            ->leftJoin($obsTable, "$obsTable.obsId=$actionEntityTable.OBSCategory3IDRef")
                            ->leftJoin($personTable, "$personTable.PersonID=$obsTable.companyIdRef")
                            ->where(['IN', 'ProcessEntityIDRef', (new \yii\db\Query())
                                ->select(['ProcessEntityId'])
                                ->from([\app\modules\workflow\models\ProcessEntity::tableName()])
                                ->where(['ProcessEntityId' => $peid])
                                ->orWhere(['parentProcessEntityIdRef' => $peid])])
                            ->addSelect([
                                'ActionDateHijri' => 'ActionDateHijri',
                                'ActionTitle2' => \app\modules\workflow\models\Action::tableName() . '.ActionTitle2',
                                'ActionIDRef' => 'ActionIDRef',
                                'UserName' => "concat($userTable.FirstName, ' ', $userTable.LastName)",
                                'Company' => "$personTable.PersonTitle",
                                'ActionMessage' => 'ActionMessage',
                                'ActionAutoMessage' => 'ActionAutoMessage',
                                'LetterNumber' => 'LetterNumber'
                            ])
                            ->orderBy(['ActionDateHijri' => SORT_ASC])->asArray()->all();
        } else {
            $actionEntityData = \app\components\ActiveModel::find('\app\modules\workflow\models\ActionEntity')
                            ->joinWith(['action'])
                            ->leftJoin($userTable, "$userTable.UserID=$actionEntityTable.UserIDRef")
                            ->leftJoin($obsTable, "$obsTable.obsId=$actionEntityTable.OBSCategory3IDRef")
                            ->leftJoin($personTable, "$personTable.PersonID=$obsTable.companyIdRef")
                            ->where(['IN', 'ProcessEntityIDRef', (new \yii\db\Query())
                                ->select(['ProcessEntityId'])
                                ->from([\app\modules\workflow\models\ProcessEntity::tableName()])
                                ->where(['ProcessEntityId' => $processEntityModel->parentProcessEntityIDRef])
                                ->orWhere(['parentProcessEntityIdRef' => $processEntityModel->parentProcessEntityIDRef])])
                            ->addSelect([
                                'ActionDateHijri' => 'ActionDateHijri',
                                'ActionTitle2' => \app\modules\workflow\models\Action::tableName() . '.ActionTitle2',
                                'ActionIDRef' => 'ActionIDRef',
                                'UserName' => "concat($userTable.FirstName, ' ', $userTable.LastName)",
                                'Company' => "$personTable.PersonTitle",
                                'ActionMessage' => 'ActionMessage',
                                'ActionAutoMessage' => 'ActionAutoMessage',
                                'LetterNumber' => 'LetterNumber'
                            ])
                            ->orderBy(['ActionDateHijri' => SORT_ASC])->asArray()->all();
        }


        $data = [];
        $row = 0;
        foreach ($actionEntityData as $model) {
            $data[$row] = [
                'ActionIDRef' => $model['ActionIDRef'],
                'ActionTitle2' => $model['ActionTitle2'],
                'UserName' => $model['UserName'],
                'Company' => $model['Company'],
                'ActionDateHijri' => $model['ActionDateHijri'],
                'ActionMessage' => $model['ActionMessage'],
                'ActionAutoMessage' => $model['ActionAutoMessage'],
                'LetterNumber' => $model['LetterNumber'],
            ];
            $row++;
        }
        if (count($data) == 0) {
            throw new \yii\web\HttpException(404, 'Record Source has Empty!!');
        }
        
        if ($processEntityModel->process->ProccessTypeIDRef != \app\modules\workflow\models\Process::EMPLOYEESETTLEMENT_PROCESS_TYPE) {
            for ($i = 0; $i < count($actionEntityData) - 1; $i++) {
                $data[$i]['UserName'] = $actionEntityData[$i + 1]['UserName'];
                $data[$i]['Company'] = $actionEntityData[$i + 1]['Company'];
                $data[$i]['ActionMessage'] = $actionEntityData[$i + 1]['ActionMessage'];
                $data[$i]['ActionAutoMessage'] = $actionEntityData[$i + 1]['ActionAutoMessage'];
                $data[$i]['ActionDateHijri'] = $actionEntityData[$i + 1]['ActionDateHijri'];
                $data[$i]['LetterNumber'] = $actionEntityData[$i + 1]['LetterNumber'];
            }
            $data[$i]['UserName'] = null;
            $data[$i]['Company'] = null;
            $data[$i]['ActionMessage'] = null;
            $data[$i]['ActionAutoMessage'] = null;
            $data[$i]['ActionDateHijri'] = null;
            $data[$i]['LetterNumber'] = null;
        }

        $format = ['0' => 'string',
            '1' => 'string',
            '2' => 'string',
            '3' => 'string',
            '4' => 'string',
            '5' => 'string',
            '6' => 'string',
            '7' => 'string',
            '8' => 'string',
        ];

        $activityManagement = new \app\components\ActivityManagement();
        $actionType = "Report";
        $subType = "ExportFile";
        $subTypeDescription["ReportName"] = "ActionEntityReport";
        $subTypeDescription["FileName"] = "mainTemplate.xlsm";
        $subTypeDescription["FilePatch"] = \app\components\Templates::getTemplate('excel', 'mainTemplate.xlsm');
        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

        Utility::exportDataAsLibxl($data, "Action Entity", 'action-entity', $format);
    }

    public function actionExportInvoiceDuration($maincontract = 0) {
        $type = "All";
        $proccessEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $actionTable = \app\modules\workflow\models\Action::tableName();
        $contractTable = \app\modules\contract\models\Contract::tableName();
        $subContractorTable = \app\modules\OBS\models\Person::tableName();
        $invoiceTable = \app\modules\invoice\models\InvoiceEdit::tableName();

        $proccessEntityQuery = \app\modules\workflow\models\ProcessEntity::find()
                        ->invoice($type, $maincontract)
                        ->select(['InvoiceIDRef', 'ProcessEntityId'])->asArray()->all();

        $proccessEntityIds = [];
        foreach ($proccessEntityQuery as $proccessEntity) {
            array_push($proccessEntityIds, intval($proccessEntity['ProcessEntityId']));
        }

        $actionEntityData = \app\components\ActiveModel::find('\app\modules\workflow\models\ActionEntity')
                        ->joinWith(['action'])
                        ->innerJoin($proccessEntityTable, "$proccessEntityTable.ProcessEntityID=$actionEntityTable.ProcessEntityIDRef")
                        ->leftJoin(InvoiceEdit::tableName(), InvoiceEdit::tableName() . '.InvoiceID=' . ProcessEntity::tableName() . '.InvoiceIDRef')
                        ->leftJoin(Contract::tableName(), InvoiceEdit::tableName() . '.ContractID=' . Contract::tableName() . '.ContractID')
                        ->leftJoin(Person::tableName(), Contract::tableName() . '.SubContractorPersonIDRef=' . Person::tableName() . '.PersonID')
                        ->where(['IN', 'ProcessEntityIDRef', $proccessEntityIds])
//                        ->select(['InvoiceIDRef', 'ProcessEntityIDRef', 'isEndAction', 'ActionDate', 'ActionDateHijri', \app\modules\workflow\models\Action::tableName() . '.ActionTitle2', 'ActionIDRef', 'ActionMessage', 'ActionAutoMessage'])
                        ->select(['InvoiceIDRef',
                            "$proccessEntityTable.ProcessIDRef",
                            "$contractTable.ContractNo",
                            "$contractTable.ContractSubject",
                            "$subContractorTable.PersonTitle",
                            "$invoiceTable.Title",
                            "$invoiceTable.CutOffDateHijri",
                            "$actionTable.ActionTitle2",
                            "$actionEntityTable.ProcessEntityIDRef",
                            "$actionTable.isEndAction",
                            "$actionEntityTable.ID",
                            "$actionEntityTable.ActionDate",
                            "$actionEntityTable.ActionDateHijri",
                            "$actionEntityTable.ActionIDRef",
                            "$actionEntityTable.ActionMessage",
                            "$actionEntityTable.ActionAutoMessage"])
                        ->orderBy(['ProcessEntityIDRef' => SORT_ASC, 'ActionDateHijri' => SORT_ASC])
                        ->asArray()->all();

        $jDate = new jDateTime(true, true, 'Asia/Tehran');
        for ($i = 0; $i < count($actionEntityData) - 1; $i++) {
            $actionEntity1 = $actionEntityData[$i];
            $actionEntity2 = $actionEntityData[$i + 1];
            if (!($actionEntity1['isEndAction'] == 1) && $actionEntity1['ActionDateHijri'] != null && $actionEntity2['ActionDateHijri'] != null) {
                $duration = "";
                $yer1 = intval(substr($actionEntity1['ActionDateHijri'], 0, 4));
                $month1 = intval(substr($actionEntity1['ActionDateHijri'], 5, 2));
                $day1 = intval(substr($actionEntity1['ActionDateHijri'], 8, 2));
                if ($actionEntity1['ProcessEntityIDRef'] == $actionEntity2['ProcessEntityIDRef']) {
                    $actionEntityData[$i]['ActionOutputDateHijri'] = $actionEntity2['ActionDateHijri'];
                    $yer2 = intval(substr($actionEntity2['ActionDateHijri'], 0, 4));
                    $month2 = intval(substr($actionEntity2['ActionDateHijri'], 5, 2));
                    $day2 = intval(substr($actionEntity2['ActionDateHijri'], 8, 2));
                    $timeBetween = $jDate->timeBetweenDate($yer1, $month1, $day1, $yer2, $month2, $day2);
                    if ($timeBetween[0] > 0) {
                        $duration = $duration . $timeBetween[0] . ' month ';
                    }
                    if ($timeBetween[1] > 0) {
                        $duration = $duration . $timeBetween[1] . ' day ';
                    }
                    if ((strlen($actionEntity1['ActionDateHijri']) >= 15) && (strlen($actionEntity2['ActionDateHijri']) >= 15)) {
                        $firstTimeString = substr($actionEntity1['ActionDateHijri'], -4);
                        $firstTimeString = substr($firstTimeString, 0, 2) . ':' . substr($firstTimeString, 2) . ':00';
                        $secondTimeString = substr($actionEntity2['ActionDateHijri'], -4);
                        $secondTimeString = substr($secondTimeString, 0, 2) . ':' . substr($secondTimeString, 2) . ':00';
                        $firstTime = new \DateTime($firstTimeString);
                        $secondTime = new \DateTime($secondTimeString);
                        $interval2 = $firstTime->diff($secondTime);
                        $hore = ($interval2->h >= 10) ? $interval2->h : '0' . $interval2->h;
                        $minut = ($interval2->i >= 10) ? $interval2->i : '0' . $interval2->i;
                        $duration = $duration . $hore . ':' . $minut . ':00';
                    }
                    $actionEntityData[$i]['Duration'] = $duration;
                } else {
                    $currentDateHijri = $jDate->date("Y/m/d-hi", false, false); // SHAMSI
                    $actionEntityData[$i]['ActionOutputDateHijri'] = $currentDateHijri;
                    $yer2 = intval(substr($currentDateHijri, 0, 4));
                    $month2 = intval(substr($currentDateHijri, 5, 2));
                    $day2 = intval(substr($currentDateHijri, 8, 2));

                    $timeBetween = $jDate->timeBetweenDate($yer1, $month1, $day1, $yer2, $month2, $day2);
                    if ($timeBetween[0] > 0) {
                        $duration = $duration . $timeBetween[0] . ' month ';
                    }
                    if ($timeBetween[1] > 0) {
                        $duration = $duration . $timeBetween[1] . ' day ';
                    }
                    if ((strlen($actionEntity1['ActionDateHijri']) >= 15)) {
                        $firstTimeString = substr($actionEntity1['ActionDateHijri'], -4);
                        $firstTimeString = substr($firstTimeString, 0, 2) . ':' . substr($firstTimeString, 2) . ':00';
                        $firstTime = new \DateTime($firstTimeString);
                        $currentTimeString = $jDate->date("H:i:s", false, false); // SHAMSI
                        $currentTime = new \DateTime($currentTimeString);
                        $interval2 = $firstTime->diff($currentTime);
                        $hore = ($interval2->h >= 10) ? $interval2->h : '0' . $interval2->h;
                        $minut = ($interval2->i >= 10) ? $interval2->i : '0' . $interval2->i;
                        $duration = $duration . $hore . ':' . $minut . ':00';
                    }
                    $actionEntityData[$i]['Duration'] = $duration;
                }
            } else {
                $actionEntityData[$i]['Duration'] = "";
                $actionEntityData[$i]['ActionOutputDateHijri'] = "";
            }
        }
        $actionEntityData[$i]['Duration'] = "";
        $actionEntityData[$i]['ActionOutputDateHijri'] = "";

        $jDate = new \app\components\jDateTime(true, true, 'Asia/Tehran');
        $dateNow = $jDate->date("Y.m.d", false, false);
        $useXlsxFormat = true;
// فعال‌سازی کش روی دیسک برای کاهش شدید مصرف RAM
$cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
$cacheSettings = array('memoryCacheSize' => '32MB');
\PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

// خواندن قالب
$templatePath = \app\components\Templates::getTemplate('excel', 'WorkflowDurationTemplate.xlsx');
$objPHPExcel = \PHPExcel_IOFactory::load($templatePath);
        $sheet = $objPHPExcel->getActiveSheet();

        // 2. نوشتن داده‌ها در شیت اکسل
        $i = 0;
        foreach ($actionEntityData as $row) {
            $rowIndex = $i + 4; // در PHPExcel شماره ردیف‌ها از 1 شروع می‌شود (ردیف 4 معادل ردیف 3 در کد قبلی)
            
            $sheet->setCellValueByColumnAndRow(0, $rowIndex, strval($i + 1));
            $sheet->setCellValueByColumnAndRow(1, $rowIndex, strval($row['InvoiceIDRef']));
            $sheet->setCellValueByColumnAndRow(2, $rowIndex, strval($row['ContractNo']));
            $sheet->setCellValueByColumnAndRow(3, $rowIndex, strval($row['ContractSubject']));
            $sheet->setCellValueByColumnAndRow(4, $rowIndex, strval($row['PersonTitle']));
            $sheet->setCellValueByColumnAndRow(5, $rowIndex, strval($row['Title']));
            $sheet->setCellValueByColumnAndRow(6, $rowIndex, strval($row['CutOffDateHijri']));
            $sheet->setCellValueByColumnAndRow(7, $rowIndex, strval($row['ActionDate']));
            $sheet->setCellValueByColumnAndRow(8, $rowIndex, strval($row['ActionDateHijri']));
            $sheet->setCellValueByColumnAndRow(9, $rowIndex, strval($row['ActionOutputDateHijri']));
            $sheet->setCellValueByColumnAndRow(10, $rowIndex, strval($row['ProcessIDRef']));
            $sheet->setCellValueByColumnAndRow(11, $rowIndex, strval($row['ID']));
            $sheet->setCellValueByColumnAndRow(12, $rowIndex, strval($row['ActionTitle2']));
            $sheet->setCellValueByColumnAndRow(13, $rowIndex, strval($row['ActionMessage']));
            $sheet->setCellValueByColumnAndRow(14, $rowIndex, strval($row['ActionAutoMessage']));
            
            $i++;
        }

        // 3. ارسال خروجی اکسل به مرورگر جهت دانلود
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Workflow-duration-' . $dateNow . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

        // ثبت لاگ فعالیت
        $activityManagement = new \app\components\ActivityManagement();
        $actionType = "Report";
        $subType = "ExportFile";
        $subTypeDescription["ReportName"] = "InvoiceDurationReport";
        $subTypeDescription["FileName"] = "mainTemplate.xlsm";
        $subTypeDescription["FilePatch"] = \app\components\Templates::getTemplate('excel', 'WorkflowDurationTemplate.xlsm');
        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

    exit();}

}
