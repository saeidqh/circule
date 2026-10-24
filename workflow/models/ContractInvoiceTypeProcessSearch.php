<?php

namespace app\modules\workflow\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\contract\models\Contract;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ContractInvoiceTypeProcessSearch extends ContractInvoiceTypeProcess {

    public $invoiceType;
    public $contractNo;
    public $contractSubject;
    

    public static function getDb() {
        return \app\components\Utility::db();
    }

    public function rules() {
        return [
            [['contractNo', 'contractSubject', 'invoiceType'], 'safe','on' => 'search']
        ];
    }

    public function search($params, $pid) {
        $companyId = Yii::$app->session->get('CompanyID');

        $activeQuery = \app\components\ActiveModel::find('\app\modules\workflow\models\ContractInvoiceTypeProcess')->where(['ProcessIDRef' => $pid])
                ->joinWith(['contract'])
                ->joinWith(['invoiceTypes'])
                ->andWhere([Contract::tableName() . '.SignerPersonIDRef' => $companyId])
                ->andWhere(static::tableName() .'.isDeleted is null '
                        . 'or '. static::tableName().'.isDeleted != 1');
//        echo $activeQuery->createCommand()->rawSql;
        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
        ]);
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->andFilterWhere(['LIKE', \app\modules\invoice\models\InvoiceTypes::tableName(). '.InvoiceType', $this->invoiceType])
                ->andFilterWhere(['LIKE', \app\modules\contract\models\Contract::tableName(). '.ContractNo', $this->contractNo])
                ->andFilterWhere(['LIKE', \app\modules\contract\models\Contract::tableName(). '.ContractSubject', $this->contractSubject]);
            

        return $dataProvider;
    }

    

}
