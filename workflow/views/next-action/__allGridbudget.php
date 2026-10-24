<?php
use kartik\grid\GridView;
use app\components\Utility;
use yii\helpers\ArrayHelper;
use app\modules\budget\models\BudgetNodeAmount;
use app\modules\budget\models\BudgetNode;
use app\modules\budget\models\Budget;
//این کوری نباید در ویو زده می شد اما بدلیل اینکه سرعت بالا برود در ویو زده شد که فقط برای ورک فلو های
//بودجه این کوری اجرا شود
$budgetIds = ArrayHelper::getColumn($processEntityModels, 'BudgetIDRef');
$budgetTable = Budget::tableName();
$budgetNodeTable=  BudgetNode::tableName();
$budgetNodeAmountTable= BudgetNodeAmount::tableName();
$currencyTable = app\modules\currency\models\Currency::tableName();
$budgetNodeAmount1 = BudgetNodeAmount::find()
        ->select([
            'CurrencyTitle' => "$currencyTable.CurrencyTitle",
            'Budget1Amount'=>'SUM(Budget1Amount)'
        ])
        ->innerJoin("$budgetNodeTable", "$budgetNodeAmountTable.BudgetNodeIDRef=$budgetNodeTable.BudgetNodeID")
        ->innerJoin($budgetTable, "$budgetTable.BudgetID=$budgetNodeTable.BudgetIDRef")
        ->innerJoin($currencyTable, "$currencyTable.CurrencyID=$budgetTable.Currency1IDRef")
        ->andWhere(['IN', 'BudgetIDRef', $budgetIds])
        ->andWhere("$budgetNodeAmountTable.isDeleted is null or $budgetNodeAmountTable.isDeleted != 1")
        ->andWhere("$budgetNodeTable.isDeleted is null or $budgetNodeTable.isDeleted != 1")
        ->groupBy(['Currency1IDRef'])
        ->asArray()
        ->one();

$budgetNodeAmount2 = BudgetNodeAmount::find()
        ->select([
            'CurrencyTitle' => "$currencyTable.CurrencyTitle",
            'Budget1Amount'=>'SUM(Budget2Amount)'
        ])
        ->innerJoin("$budgetNodeTable", "$budgetNodeAmountTable.BudgetNodeIDRef=$budgetNodeTable.BudgetNodeID")
        ->innerJoin($budgetTable, "$budgetTable.BudgetID=$budgetNodeTable.BudgetIDRef")
         ->innerJoin($currencyTable, "$currencyTable.CurrencyID=$budgetTable.Currency2IDRef")
        ->andWhere(['IN', 'BudgetIDRef', $budgetIds])
        ->andWhere("$budgetNodeAmountTable.isDeleted is null or $budgetNodeAmountTable.isDeleted != 1")
        ->andWhere("$budgetNodeTable.isDeleted is null or $budgetNodeTable.isDeleted != 1")
        ->groupBy(['Currency2IDRef'])
        ->asArray()
        ->one();
?>
<?= GridView::widget([
    'hover' => true,
    'showFooter' => true,
    'dataProvider' => $dataProvider,
    'columns' => [
           
            [
                'attribute' => 'fullId',
                'label' => 'ID',
                'value' => 'budget.fullId',
                'footer' => Yii::t('app', 'SUM')
            ],
            [
                'attribute' => 'type',
                'label' => 'Type',
                'value' => 'budget.type',
                'options' => [
                    'width' => '85px',
                ],
            ],
            [
                'attribute' => 'title',
                'label' => 'Title',
                'value' => 'budget.title'
            ],
            [
                'attribute' => 'budgetUserName',
                'label' => 'UserName',
//                'value' => 'budget.user.UserName',
                'value' => 'budget.user.fullName',
            ],
            [
                'attribute' => 'budgetObs',
                'label' => 'OBS',
                'value' => 'budget.obs.obsTitle',
            ],
            [
                'attribute' => 'LastActionDateHijri',
                'label' => 'Last Action Date',
                'value' => 'LastActionDateHijri',
            ],
            [
                'attribute' => 'lastActionTitle',
                'label' => 'Last Action Title',
                'value' => 'lastAction.ActionTitle1',
            ],
            [
                'attribute' => 'epsTitle',
                'label' => 'EPS',
                'value' => 'budget.lastEps.EPSTitle',
            ],
            [
                'attribute' => 'SystemDescription',
                'format' => 'raw',
                'value' => function($model) {
                    return Utility::generateTextShorterSpan($model->budget->SystemDescription, 30);
                },
                'options' => [
                    'width' => '250px',
                ]
            ],
             [
                'label' => 'Annual Budget',
                'format' => 'raw',
                'options' => [
                    'width' => '150px',
                ],
                'value' => function($model){
                    return Utility::moneyFormat($model->budget->totalBudget1).' '.$model->budget->currency1->CurrencyTitle.
                        (isset($model->budget->currency2) ? $model->budget->totalBudget2.' '.$model->budget->currency2->CurrencyTitle:null);
                },
                'footer' => '<table class="inner-table">'
                                . '<tr><td width=\'200\'>'
                                . Utility::moneyFormat($budgetNodeAmount1['Budget1Amount']).' '.$budgetNodeAmount1['CurrencyTitle']
                                . '<tr class="top-border"><td >'
                                . Utility::moneyFormat($budgetNodeAmount2['Budget2Amount']).' '.$budgetNodeAmount2['CurrencyTitle']
                                . '</table>'
            ],
        ],
        
]);?>
<?php
$css = "
    .kv-table-footer{
        background:#75b9e6;
        color:#fff;
    }
    ";
$this->registerCss($css);