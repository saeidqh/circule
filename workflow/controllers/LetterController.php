<?php

namespace app\modules\workflow\controllers;

use Yii;
use yii\web\Controller;
use app\modules\workflow\models\ProcessEntity;
use app\modules\workflow\models\Letter;
use \app\components\Utility;
use yii\helpers\Html;

/**
 * Description of WarrantyAsset
 *
 * @author <kamali:09366263397>
 */
class LetterController extends Controller {

    public $access = null;

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['generate-letter', 'create-form'],
                'rules' => [
                    // allow authenticated users
                    [
                        'actions' => ['generate-letter', 'create-form', 'usersignature'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule, $action, $exteraData = []) {
                    $peid = isset($exteraData['peid']) ? $exteraData['peid'] : Yii::$app->request->get('peid');
                    $peid = Html::encode($peid);
                    return \app\access\AccessControl::isAccess(
                                    ['processEntity', 'peid' => $peid, 'type' => 'All']
                    );
                },
                    ],
                    [
                        'actions' => ['usersignature'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                // everything else is denied
                ],
            ],
        ];
    }

    public function actionCreateForm($lid, $peid) {
        $this->layout = 'blank';
        $activityManagement = new \app\components\ActivityManagement();
        if ($lid == 19) {
            $description = "پرینت فرم گردش کار صورت وضعیت";
            $activityManagement->sendCreateFormActitvityLog($description);

            return $this->createForm19($peid);
        } elseif ($lid == 21) {
            $description = "پرینت فرم گردش کار مجوز";
            $activityManagement->sendCreateFormActitvityLog($description);

            return $this->createForm21($peid);
        } else {
            throw new \yii\web\HttpException(404, "This letter No not valid!!");
        }
    }

    private function createForm19($peid) {
        $peid = Html::encode($peid);

        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $processEntityTable = ProcessEntity::tableName();

        $type = 'cr';

        $userTable = \app\modules\account\models\User::tableName();

        $companyId = 258;      // pksk
        $pic_file = \app\components\Templates::getTemplate("logo", $companyId . ".png");
        if ($pic_file != "") {
            $file = file_get_contents($pic_file);
            $logo = "data:image/" . $type . ';base64,' . base64_encode($file);
        } else {
            $logo = "";
        }


        $companyIdAdmin = 606; // Cando Admin
        $pic_fileAdmin = \app\components\Templates::getTemplate("logo", $companyIdAdmin . ".png");
        if ($pic_fileAdmin != "") {
            $file_admin = file_get_contents($pic_fileAdmin);
            $logo_admin = "data:image/" . $type . ';base64,' . base64_encode($file_admin);
        } else {
            $logo_admin = "";
        }

//        $pic_fileAdmin = \app\components\Templates::getTemplate("logo", $companyIdAdmin . ".png");
        $header_pic = Yii::$app->basePath . '/modules/workflow/bower/img/pksk.png';
        $file_header = file_get_contents($header_pic);
        $image_header = "data:image/" . $type . ';base64,' . base64_encode($file_header);

        $footer_pic = Yii::$app->basePath . '/modules/workflow/bower/img/letter-footer.jpg';
        $file_footer = file_get_contents($footer_pic);
        $image_footer = "data:image/" . $type . ';base64,' . base64_encode($file_footer);


        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();
        if (!isset($processEntityModel)) {
            return \yii\web\HttpException(500, "Process Entity not found!!");
        }

        $sgn_non = $this->Signature($peid, [], []);
        if ($processEntityModel->ProcessIDRef == 5) {
            $sgn_01 = $this->Signature($peid, [125], [126]); // maneger
            $sgn_02 = $this->Signature($peid, [128], [136]); // Cost Controller
            $sgn_0 = $this->Signature($peid, [124], [125]); // Cost Controller
        } else if ($processEntityModel->ProcessIDRef == 6){
            $sgn_01 = $this->Signature($peid, [142], [143]); // maneger  
            $sgn_02 = $this->Signature($peid, [145], [153]); // Cost Controller 
            $sgn_0 = $this->Signature($peid, [141], [142]); // Cost Controller
        }

        // Fill Document Number File
        $countDocOriginalFile = [];
        $countDocOriginalFile[1] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 10])->andWhere("COALESCE(isDeleted,0)<>1")->count();
        $countDocOriginalFile[2] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 6])->andWhere("COALESCE(isDeleted,0)<>1")->count();
        $countDocOriginalFile[3] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 5])->andWhere("COALESCE(isDeleted,0)<>1")->count();
        $countDocOriginalFile[4] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 12])->andWhere("COALESCE(isDeleted,0)<>1")->count();
        $countDocOriginalFile[5] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 11])->andWhere("COALESCE(isDeleted,0)<>1")->count();
        $countDocOriginalFile[6] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 7])->andWhere("COALESCE(isDeleted,0)<>1")->count();
        $countDocOriginalFile[7] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 15])->andWhere("COALESCE(isDeleted,0)<>1")->count();
        $countDocOriginalFile[8] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 16])->andWhere("COALESCE(isDeleted,0)<>1")->count();
        $countDocOriginalFile[9] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 17])->andWhere("COALESCE(isDeleted,0)<>1")->count();
        $countDocOriginalFile[10] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 18])->andWhere("COALESCE(isDeleted,0)<>1")->count();
        $countDocOriginalFile[11] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["in", "InvoiceDocumentTypeIDref", [19, 20]])->andWhere("COALESCE(isDeleted,0)<>1")->count();
        $countDocOriginalFile[12] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["in", "InvoiceDocumentTypeIDref", [21, 22]])->andWhere("COALESCE(isDeleted,0)<>1")->count();
        $countDocOriginalFile[13] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 13])->andWhere("COALESCE(isDeleted,0)<>1")->count();


// Fill Document Number
        $countDocOriginal = [];
        $countDocOriginal[1] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 10])->andWhere("COALESCE(isDeleted,0)<>1")->sum("originalNumber");
        $countDocOriginal[2] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 6])->andWhere("COALESCE(isDeleted,0)<>1")->sum("originalNumber");
        $countDocOriginal[3] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 5])->andWhere("COALESCE(isDeleted,0)<>1")->sum("originalNumber");
        $countDocOriginal[4] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 12])->andWhere("COALESCE(isDeleted,0)<>1")->sum("originalNumber");
        $countDocOriginal[5] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 11])->andWhere("COALESCE(isDeleted,0)<>1")->sum("originalNumber");
        $countDocOriginal[6] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 7])->andWhere("COALESCE(isDeleted,0)<>1")->sum("originalNumber");
        $countDocOriginal[7] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 15])->andWhere("COALESCE(isDeleted,0)<>1")->sum("originalNumber");
        $countDocOriginal[8] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 16])->andWhere("COALESCE(isDeleted,0)<>1")->sum("originalNumber");
        $countDocOriginal[9] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 17])->andWhere("COALESCE(isDeleted,0)<>1")->sum("originalNumber");
        $countDocOriginal[10] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 18])->andWhere("COALESCE(isDeleted,0)<>1")->sum("originalNumber");
        $countDocOriginal[11] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["in", "InvoiceDocumentTypeIDref", [19, 20]])->andWhere("COALESCE(isDeleted,0)<>1")->sum("originalNumber");
        $countDocOriginal[12] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["in", "InvoiceDocumentTypeIDref", [21, 22]])->andWhere("COALESCE(isDeleted,0)<>1")->sum("originalNumber");

        $countDocOriginal[13] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 13])->andWhere("COALESCE(isDeleted,0)<>1")->sum("originalNumber");

        $countDocCopy = [];
        $countDocCopy[1] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 10])->andWhere("COALESCE(isDeleted,0)<>1")->sum("copyNumber");
        $countDocCopy[2] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 6])->andWhere("COALESCE(isDeleted,0)<>1")->sum("copyNumber");
        $countDocCopy[3] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 5])->andWhere("COALESCE(isDeleted,0)<>1")->sum("copyNumber");
        $countDocCopy[4] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 12])->andWhere("COALESCE(isDeleted,0)<>1")->sum("copyNumber");
        $countDocCopy[5] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 11])->andWhere("COALESCE(isDeleted,0)<>1")->sum("copyNumber");
        $countDocCopy[6] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 7])->andWhere("COALESCE(isDeleted,0)<>1")->sum("copyNumber");
        $countDocCopy[7] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 15])->andWhere("COALESCE(isDeleted,0)<>1")->sum("copyNumber");
        $countDocCopy[8] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 16])->andWhere("COALESCE(isDeleted,0)<>1")->sum("copyNumber");
        $countDocCopy[9] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 17])->andWhere("COALESCE(isDeleted,0)<>1")->sum("copyNumber");
        $countDocCopy[10] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 18])->andWhere("COALESCE(isDeleted,0)<>1")->sum("copyNumber");
        $countDocCopy[11] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["in", "InvoiceDocumentTypeIDref", [19, 20]])->andWhere("COALESCE(isDeleted,0)<>1")->sum("copyNumber");
        $countDocCopy[12] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["in", "InvoiceDocumentTypeIDref", [21, 22]])->andWhere("COALESCE(isDeleted,0)<>1")->sum("copyNumber");

        $countDocCopy[13] = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 13])->andWhere("COALESCE(isDeleted,0)<>1")->sum("copyNumber");

        // Document Count Page

        $docPageCount = [];
        // File Doc 01


        $docOriginalModel = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 10])->orderBy(["InvoiceDocumentID" => SORT_DESC])->andWhere("COALESCE(isDeleted,0)<>1")->one();


        $docPageCount[1] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('invoice', $docOriginalModel->InvoiceDocumentID, "Pages");
            $docPageCount[1] = intval($infoFile);
        }

        // File Doc 02
        $docOriginalModel = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 6])->orderBy(["InvoiceDocumentID" => SORT_DESC])->andWhere("COALESCE(isDeleted,0)<>1")->one();
        $docPageCount[2] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('invoice', $docOriginalModel->InvoiceDocumentID, "Pages");
            $docPageCount[2] = intval($infoFile);
        }
        // File Doc 03
        $docOriginalModel = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 5])->orderBy(["InvoiceDocumentID" => SORT_DESC])->andWhere("COALESCE(isDeleted,0)<>1")->one();
        $docPageCount[3] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('invoice', $docOriginalModel->InvoiceDocumentID, "Pages");
            $docPageCount[3] = intval($infoFile);
        }
        // File Doc 04
        $docOriginalModel = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 12])->orderBy(["InvoiceDocumentID" => SORT_DESC])->andWhere("COALESCE(isDeleted,0)<>1")->one();
        $docPageCount[4] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('invoice', $docOriginalModel->InvoiceDocumentID, "Pages");
            $docPageCount[4] = intval($infoFile);
        }
        // File Doc 05
        $docOriginalModel = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 11])->orderBy(["InvoiceDocumentID" => SORT_DESC])->andWhere("COALESCE(isDeleted,0)<>1")->one();
        $docPageCount[5] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('invoice', $docOriginalModel->InvoiceDocumentID, "Pages");
            $docPageCount[5] = intval($infoFile);
        }
        // File Doc 06
        $docOriginalModel = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 7])->orderBy(["InvoiceDocumentID" => SORT_DESC])->andWhere("COALESCE(isDeleted,0)<>1")->one();
        $docPageCount[6] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('invoice', $docOriginalModel->InvoiceDocumentID, "Pages");
            $docPageCount[6] = intval($infoFile);
        }

        // File Doc 07
        $docOriginalModel = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 15])->orderBy(["InvoiceDocumentID" => SORT_DESC])->andWhere("COALESCE(isDeleted,0)<>1")->one();
        $docPageCount[7] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('invoice', $docOriginalModel->InvoiceDocumentID, "Pages");
            $docPageCount[7] = intval($infoFile);
        }

        // File Doc 08
        $docOriginalModel = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 16])->orderBy(["InvoiceDocumentID" => SORT_DESC])->andWhere("COALESCE(isDeleted,0)<>1")->andWhere("COALESCE(isDeleted,0)<>1")->one();
        $docPageCount[8] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('invoice', $docOriginalModel->InvoiceDocumentID, "Pages");
            $docPageCount[8] = intval($infoFile);
        }

        // File Doc 09
        $docOriginalModel = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 17])->orderBy(["InvoiceDocumentID" => SORT_DESC])->andWhere("COALESCE(isDeleted,0)<>1")->one();
        $docPageCount[9] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('invoice', $docOriginalModel->InvoiceDocumentID, "Pages");
            $docPageCount[9] = intval($infoFile);
        }
        // File Doc 10 << Time Sheet >>
        $docOriginalModel = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 18])->orderBy(["InvoiceDocumentID" => SORT_DESC])->andWhere("COALESCE(isDeleted,0)<>1")->one();
        $docPageCount[10] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('invoice', $docOriginalModel->InvoiceDocumentID, "Pages");
            $docPageCount[10] = intval($infoFile);
        }




        // File Doc 11 << APG >>
        $docOriginalModel = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["in", "InvoiceDocumentTypeIDref", [19, 20]])->orderBy(["InvoiceDocumentID" => SORT_DESC])->andWhere("COALESCE(isDeleted,0)<>1")->one();
        $docPageCount[11] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('invoice', $docOriginalModel->InvoiceDocumentID, "Pages");
            $docPageCount[11] = intval($infoFile);
        }
        // File Doc 12 << PG >>
        $docOriginalModel = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["in", "InvoiceDocumentTypeIDref", [21, 22]])->orderBy(["InvoiceDocumentID" => SORT_DESC])->andWhere("COALESCE(isDeleted,0)<>1")->one();
        $docPageCount[12] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('invoice', $docOriginalModel->InvoiceDocumentID, "Pages");
            $docPageCount[12] = intval($infoFile);
        }

        // File Doc 13 << MSR >>
        $docOriginalModel = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 13])->orderBy(["InvoiceDocumentID" => SORT_DESC])->andWhere("COALESCE(isDeleted,0)<>1")->one();
        $docPageCount[13] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('invoice', $docOriginalModel->InvoiceDocumentID, "Pages");
            $docPageCount[13] = intval($infoFile);
        }

//        Other Description
        $invoiceDocModel17 = \app\modules\invoice\models\InvoiceDocument::find()
                        ->andWhere(["InvoiceIDref" => $processEntityModel->invoice->InvoiceID])->andWhere(["InvoiceDocumentTypeIDref" => 17])->orderBy(["InvoiceDocumentID" => SORT_DESC])->all();
        $otherDescription = "";
        foreach ($invoiceDocModel17 as $key => $value) {
            if (!empty($value->Description)) {
                $otherDescription.=$value->Description . " / ";
            }
        }
        if ($otherDescription !== "") {
            $otherDescription = substr($otherDescription, 0, strlen($otherDescription) - 3);
        }
        $InvoicePkskModel = \app\modules\invoice\models\InvoicePksk::find()
                ->andWhere(["InvoiceIDRef" => $processEntityModel->invoice->InvoiceID])
                ->groupBy(["InvoiceIDRef"])
                ->addSelect([
                    "CI_No" => "GROUP_CONCAT(CI_No)",
                    "PL_No" => "GROUP_CONCAT(PL_No)",
                ])
                ->one();
        $pl_No = $InvoicePkskModel['PL_No'];
        $cl_No = $InvoicePkskModel['CI_No'];



        return $this->render('createForm_19', [
                    'processEntityModel' => $processEntityModel,
                    'logo' => $logo,
                    'logo_admin' => $logo_admin,
                    'sgn_non' => $sgn_non,
                    'sgn_01' => $sgn_01,
                    'sgn_02' => $sgn_02,
                    'sgn_0' => $sgn_0,
                    'image_header' => $image_header,
                    'image_footer' => $image_footer,
                    'countDocOriginal' => $countDocOriginal,
                    'countDocCopy' => $countDocCopy,
                    'docPageCount' => $docPageCount,
                    'cl_No' => $cl_No,
                    'pl_No' => $pl_No,
                    'otherDescription' => $otherDescription,
                    'countDocOriginalFile' => $countDocOriginalFile,
        ]);
    }

    // for permission
    private function createForm21($peid) {
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $processEntityTable = ProcessEntity::tableName();

        $type = 'cr';

        $userTable = \app\modules\account\models\User::tableName();

        $companyId = 258;      // pksk
        $pic_file = \app\components\Templates::getTemplate("logo", $companyId . ".png");
        if ($pic_file != "") {
            $file = file_get_contents($pic_file);
            $logo = "data:image/" . $type . ';base64,' . base64_encode($file);
        } else {
            $logo = "";
        }

        $companyIdAdmin = 606; // Cando Admin
        $pic_fileAdmin = \app\components\Templates::getTemplate("logo", $companyIdAdmin . ".png");
        if ($pic_fileAdmin != "") {
            $file_admin = file_get_contents($pic_fileAdmin);
            $logo_admin = "data:image/" . $type . ';base64,' . base64_encode($file_admin);
        } else {
            $logo_admin = "";
        }

//        $pic_fileAdmin = \app\components\Templates::getTemplate("logo", $companyIdAdmin . ".png");
        $header_pic = Yii::$app->basePath . '/modules/workflow/bower/img/pksk.png';
        $file_header = file_get_contents($header_pic);
        $image_header = "data:image/" . $type . ';base64,' . base64_encode($file_header);

        $footer_pic = Yii::$app->basePath . '/modules/workflow/bower/img/letter-footer.jpg';
        $file_footer = file_get_contents($footer_pic);
        $image_footer = "data:image/" . $type . ';base64,' . base64_encode($file_footer);

        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();

        if (!isset($processEntityModel)) {
            return \yii\web\HttpException(500, "Process Entity not found!!");
        }

        $sgn_non = $this->Signature($peid, [], []);
        $sgn_0 = $this->Signature($peid, [869], [870]); // maneger
        $sgn_01 = $this->Signature($peid, [870], [871]); // maneger
        $sgn_02 = $this->Signature($peid, [873], [881]); // Cost Controller
        // Fill Document Number File
        $countDocOriginalFile = [];
        $countDocOriginalFile[1] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 10])->count();
        $countDocOriginalFile[2] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 6])->count();
        $countDocOriginalFile[3] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 5])->count();
        $countDocOriginalFile[4] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 12])->count();
        $countDocOriginalFile[5] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 11])->count();
        $countDocOriginalFile[6] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 7])->count();
        $countDocOriginalFile[7] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 15])->count();
        $countDocOriginalFile[8] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 16])->count();
        $countDocOriginalFile[9] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 17])->count();
        $countDocOriginalFile[10] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["in", "typeId", [18, 19]])->count();
        $countDocOriginalFile[11] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["in", "typeId", [20, 21]])->count();
        $countDocOriginalFile[12] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["in", "typeId", [13]])->count();


        // Fill Document Number
        $countDocOriginal = [];
        $countDocOriginal[1] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 10])->sum("originalNumber");
        $countDocOriginal[2] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 6])->sum("originalNumber");
        $countDocOriginal[3] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 5])->sum("originalNumber");
        $countDocOriginal[4] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 12])->sum("originalNumber");
        $countDocOriginal[5] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 11])->sum("originalNumber");
        $countDocOriginal[6] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 7])->sum("originalNumber");
        $countDocOriginal[7] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 15])->sum("originalNumber");
        $countDocOriginal[8] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 16])->sum("originalNumber");
        $countDocOriginal[9] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 17])->sum("originalNumber");
        $countDocOriginal[10] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["in", "typeId", [18, 19]])->sum("originalNumber");
        $countDocOriginal[11] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["in", "typeId", [20, 21]])->sum("originalNumber");
        $countDocOriginal[12] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["in", "typeId", [13]])->sum("originalNumber");


        $countDocCopy = [];
        $countDocCopy[1] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 10])->sum("copyNumber");
        $countDocCopy[2] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 6])->sum("copyNumber");
        $countDocCopy[3] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 5])->sum("copyNumber");
        $countDocCopy[4] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 12])->sum("copyNumber");
        $countDocCopy[5] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 11])->sum("copyNumber");
        $countDocCopy[6] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 7])->sum("copyNumber");
        $countDocCopy[7] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 15])->sum("copyNumber");
        $countDocCopy[8] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 16])->sum("copyNumber");
        $countDocCopy[9] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 17])->sum("copyNumber");
        $countDocCopy[10] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["in", "typeId", [18, 19]])->sum("copyNumber");
        $countDocCopy[11] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["in", "typeId", [20, 21]])->sum("copyNumber");
        $countDocCopy[12] = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["in", "typeId", [13]])->sum("copyNumber");

        // Document Count Page
        $docPageCount = [];
        // File Doc 01
        $docOriginalModel = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 10])->orderBy(["id" => SORT_DESC])->one();
        $docPageCount[1] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('permission', $docOriginalModel->id, "Pages");
            $docPageCount[1] = intval($infoFile);
        }

        // File Doc 02
        $docOriginalModel = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 6])->orderBy(["id" => SORT_DESC])->one();
        $docPageCount[2] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('permission', $docOriginalModel->id, "Pages");
            $docPageCount[2] = intval($infoFile);
        }
        // File Doc 03
        $docOriginalModel = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 5])->orderBy(["id" => SORT_DESC])->one();
        $docPageCount[3] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('permission', $docOriginalModel->id, "Pages");
            $docPageCount[3] = intval($infoFile);
        }
        // File Doc 04
        $docOriginalModel = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 12])->orderBy(["id" => SORT_DESC])->one();
        $docPageCount[4] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('permission', $docOriginalModel->id, "Pages");
            $docPageCount[4] = intval($infoFile);
        }
        // File Doc 05
        $docOriginalModel = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 11])->orderBy(["id" => SORT_DESC])->one();
        $docPageCount[5] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('permission', $docOriginalModel->id, "Pages");
            $docPageCount[5] = intval($infoFile);
        }
        // File Doc 06
        $docOriginalModel = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 7])->orderBy(["id" => SORT_DESC])->one();
        $docPageCount[6] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('permission', $docOriginalModel->id, "Pages");
            $docPageCount[6] = intval($infoFile);
        }

        // File Doc 07
        $docOriginalModel = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 15])->orderBy(["id" => SORT_DESC])->one();
        $docPageCount[7] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('permission', $docOriginalModel->id, "Pages");
            $docPageCount[7] = intval($infoFile);
        }

        // File Doc 08
        $docOriginalModel = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 16])->orderBy(["id" => SORT_DESC])->one();
        $docPageCount[8] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('permission', $docOriginalModel->id, "Pages");
            $docPageCount[8] = intval($infoFile);
        }

        // File Doc 09
        $docOriginalModel = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 17])->orderBy(["id" => SORT_DESC])->one();
        $docPageCount[9] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('permission', $docOriginalModel->id, "Pages");
            $docPageCount[9] = intval($infoFile);
        }

        // File Doc 10
        $docOriginalModel = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["in", "typeId", [18, 19]])->orderBy(["id" => SORT_DESC])->one();
        $docPageCount[10] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('permission', $docOriginalModel->id, "Pages");
            $docPageCount[10] = intval($infoFile);
        }

        // File Doc 11
        $docOriginalModel = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["in", "typeId", [20, 21]])->orderBy(["id" => SORT_DESC])->one();
        $docPageCount[11] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('permission', $docOriginalModel->id, "Pages");
            $docPageCount[11] = intval($infoFile);
        }
        // File Doc 12
        $docOriginalModel = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["in", "typeId", [13]])->orderBy(["id" => SORT_DESC])->one();
        $docPageCount[12] = 0;
        IF (isset($docOriginalModel)) {
            $infoFile = \app\components\Utility::getFilePdfInfo('permission', $docOriginalModel->id, "Pages");
            $docPageCount[12] = intval($infoFile);
        }

//        Other Description
        $invoiceDocModel17 = \app\modules\permission\models\PermissionDocument::find()
                        ->andWhere(["permissionId" => $processEntityModel->permissionId])->andWhere(["typeId" => 17])->orderBy(["id" => SORT_DESC])->all();
        $otherDescription = "";
        foreach ($invoiceDocModel17 as $key => $value) {
            if (!empty($value->description)) {
                $otherDescription.=$value->description . " / ";
            }
        }
        if ($otherDescription !== "") {
            $otherDescription = substr($otherDescription, 0, strlen($otherDescription) - 3);
        }

        return $this->render('createForm_21', [
                    'processEntityModel' => $processEntityModel,
                    'logo' => $logo,
                    'logo_admin' => $logo_admin,
                    'sgn_non' => $sgn_non,
                    'sgn_01' => $sgn_01,
                    'sgn_02' => $sgn_02,
                    'sgn_0' => $sgn_0,
                    'image_header' => $image_header,
                    'image_footer' => $image_footer,
                    'countDocOriginal' => $countDocOriginal,
                    'countDocCopy' => $countDocCopy,
                    'docPageCount' => $docPageCount,
                    'otherDescription' => $otherDescription,
                    'countDocOriginalFile' => $countDocOriginalFile,
        ]);
    }

    public function actionUsersignature($uid, $oid) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;

        $userObs = \app\modules\account\models\UserObs::find()
                ->where(['UserIDRef' => $uid, 'OBSCategory3IDRef' => $oid])
                ->one();
        $userObs->loadImage();
        exit();
    }

    public function Signature($peid, $from, $to) {
        $processEntityTable = ProcessEntity::tableName();
        $actionTable = \app\modules\workflow\models\Action::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $userTable = \app\modules\account\models\User::tableName();

        $modelProcessEntity = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')
                ->where(["ProcessEntityID" => $peid])
                ->one();
        $CurrentActionID = $modelProcessEntity->LastActionIDRef;
        $modelAction = \app\components\ActiveModel::find('\app\modules\workflow\models\Action')
                ->where(["ActionID" => $CurrentActionID])
                ->one();

        $signOrder = isset($modelAction->SignOrder) ? $modelAction->SignOrder : 0;

        $processEntityModel = (new \yii\db\Query())
                ->from($processEntityTable)
                ->innerJoin($actionEntityTable, "$actionEntityTable.ProcessEntityIDRef=$processEntityTable.ProcessEntityID")
                ->innerJoin($actionTable, "$actionTable.ActionID=$actionEntityTable.ActionIDRef")
                ->innerJoin($userTable, "$userTable.UserID=$actionEntityTable.UserIDRef")
                ->andWhere(["$actionEntityTable.ProcessEntityIDRef" => $peid])
                ->andFilterWhere(["in", 'FromActionIDRef', $from])
                ->andWhere(["in", 'ActionIDRef', $to])
                ->andWhere(['<=', 'SignOrder', $signOrder])
                ->orderBy(["$actionEntityTable.ActionDateHijri" => SORT_DESC])
                ->one();

        if (isset($processEntityModel['UserIDRef'])) {
            $val = json_decode($processEntityModel['log']);
        }

        if (isset($processEntityModel)) {
            $signature = [
                'uid' => $processEntityModel['UserIDRef'],
                'oid' => $processEntityModel['OBSCategory3IDRef'],
                'UserName' => $processEntityModel['UserName'],
                'UserName' => $processEntityModel['FirstName'] . ' ' . $processEntityModel['LastName'],
                'ActionDateHijri' => $processEntityModel['ActionDateHijri'],
                'description' => $processEntityModel['ActionMessage'],
            ];
        } else {
            $signature = [
                'uid' => null,
                'oid' => null,
                'UserName' => null,
                'UserName' => null,
                'ActionDateHijri' => null,
                'description' => null,
            ];
        }
        return $signature;
    }

    public function actionGenerateLetter($lid, $peid) {

        $processEntityModel = ProcessEntity::find()->where(['ProcessEntityID' => $peid])->one($db = Utility::db());
        if (empty($processEntityModel)) {
            throw new \yii\web\HttpException(500, 'not find Process entity');
        }

        $letterModel = Letter::find()->where(['letterId' => $lid])->one($db = Utility::db());
        if (empty($letterModel->fileIdRef)) {
            throw new \yii\web\HttpException(500, 'letter id not find');
        }
        $fileId = $letterModel->fileIdRef;

        if ($fileId == 20) {   // خروجی اکسل لیست اعلام بدهی ها
            $letterModel->generateLetter20($processEntityModel->invoice);
            return;
        }
        if ($fileId == 40) {  // export Excel (خروجی اکسل صورت وضعیت برای مالی)
            $letterModel->generateLetter40($processEntityModel->invoice);
            return;
        }
        if ($fileId == 702) {  // Export Excel PSA/PPI (PKSK Info)
            $letterModel->generateLetter702($processEntityModel->invoice);
            return;
        }

        $letterFormModel = new \app\modules\workflow\models\LetterForm();
        $letterFormModel->setScenario('new');
        $letterFormModel->LetterTitle = $letterModel->letterTitle;

        //هاEvidence دریافت مقادیر پیشفرض 
//        if ($letterModel->letterId = 22 ){
//            return $letterModel->generate($letterFormModel, $processEntityModel);
//        }
        if ($letterFormModel->load(Yii::$app->request->post()) && $letterFormModel->validate()) {
            return $letterModel->generate($letterFormModel, $processEntityModel);
        }
        $letterFormModel->fillEvidence($fileId, $processEntityModel);

        if ($lid == 19) {
            return $this->renderAjax('letterForm19', [
                        'lid' => $lid,
                        'fileId' => $fileId,
                        'peid' => $peid,
                        'invoiceModel' => $processEntityModel->invoice,
                        'model' => $letterFormModel,
                        'options' => $letterFormModel->getLetterOptions($fileId),
            ]);
        }

        return $this->renderAjax('letterForm', [
                    'lid' => $lid,
                    'fileId' => $fileId,
                    'peid' => $peid,
                    'invoiceModel' => $processEntityModel->invoice,
                    'model' => $letterFormModel,
                    'options' => $letterFormModel->getLetterOptions($fileId),
        ]);
    }

}
