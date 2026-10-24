<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\bootstrap\Tabs;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">
                    خلاصه اقدامات
                    <span class="pull-left">
                        <?= Html::a('×', '#', ['class' => 'close', 'data-dismiss' => 'modal', 'style' => 'color:#fff;opacity:1;']) ?>
                    </span>
                </h4>
            </div>
            <div class="panel-body">
                
                <?= Tabs::widget([
                    'items' => [
                        [
                            'label' => 'تاریخچه اقدامات',
                            'content' => $this->render('_actionHistory', [
                                'actionEntityProvider' => $actionEntityProvider,
                                'processEntityModel' => $processEntityModel,
                            ]),
                            'active' => true,
                        ],
                        [
                            'label' => 'مسیر گردش کار',
                            'content' => '<div id="workflow-steps-container">در حال بارگذاری...</div>',
                            'linkOptions' => ['id' => 'workflow-steps-tab'],
                        ],
                    ],
                ]); ?>
                
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#workflow-steps-tab').on('click', function() {
        var stampId = <?= $processEntityModel->getStampDocument()->one()->StampId ?? 0 ?>;
        var currentActionId = <?= $processEntityModel->LastActionIDRef ?? 0 ?>;
        
        if ($('#workflow-steps-container').html() == 'در حال بارگذاری...') {
            // نشان دادن لودینگ
            $('#workflow-steps-container').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...</div>');
            
            $.ajax({
                url: '<?= Url::to(['/workflow/workflow/get-workflow-steps']) ?>',
                type: 'GET',
                data: {
                    stampId: stampId,
                    currentActionId: currentActionId,
                    peid: <?= $processEntityModel->ProcessEntityID ?? 0 ?>
                },
                dataType: 'json',
                success: function(data) {
                    if (data.error) {
                        $('#workflow-steps-container').html('<div class="alert alert-danger">' + data.error + '</div>');
                        return;
                    }
                    
                    if (!data.steps || data.steps.length === 0) {
                        $('#workflow-steps-container').html('<div class="alert alert-warning">هیچ مرحله‌ای برای نمایش وجود ندارد</div>');
                        return;
                    }
                    
                    var html = '<div class="table-responsive">';
                    html += '<table class="table table-bordered table-striped table-hover">';
                    html += '<thead>';
                    html += '<tr>';
                    html += '<th style="width:50px">#</th>';
                    html += '<th>کد مرحله</th>';
                    html += '<th>عنوان مرحله</th>';
                    html += '<th>وضعیت</th>';
                    html += '<th>نام و نام خانوادگی</th>';
                    html += '<th>پست سازمانی</th>';
                    html += '</tr>';
                    html += '</thead>';
                    html += '<tbody>';
                    
                    for (var i = 0; i < data.steps.length; i++) {
                        var step = data.steps[i];
                        var status = '';
                        var rowClass = '';
                        var actionId = parseInt(step.actionId);
                        var endActionIds = [1184];

                        if (step.isCurrent && endActionIds.indexOf(actionId) !== -1) {
                            status = '<span class="label label-success">✓ تکمیل شده</span>';
                            rowClass = 'success';
                        } else if (step.isCurrent) {
                            status = '<span class="label label-warning">► جاری</span>';
                            rowClass = 'warning';
                        } else if (step.isPast) {
                            status = '<span class="label label-success">✓ بررسی شده</span>';
                            rowClass = 'success';
                        } else {
                            status = '<span class="label label-default">⚪ آینده</span>';
                            rowClass = '';
                        }

                        // برای مرحله 1184، حتی اگر کاربر دسترسی داشته باشد، اسم افراد نمایش داده نشود
                        if (actionId === 1184) {
                            html += '<tr class="' + rowClass + '">';
                            html += '<td class="text-center">' + (i + 1) + '</td>';
                            html += '<td class="text-center">' + step.actionId + '</td>';
                            html += '<td>' + escapeHtml(step.title) + '</td>';
                            html += '<td class="text-center">' + status + '</td>';
                            html += '<td class="text-center text-success">اقدام سیستمی</td>';
                            html += '<td class="text-center text-success">تحویل و ثبت نهایی</td>';
                            html += '</tr>';

                            continue;
                        }

                        
                        // نمایش کاربران (هر کاربر در یک ردیف جداگانه)
                        if (step.users && step.users.length > 0) {
                            for (var j = 0; j < step.users.length; j++) {
                                var user = step.users[j];
                                var userName = user.name || (user.FirstName + ' ' + user.LastName);
                                var positionTitle = (user.positionTitle && user.positionTitle !== 'نامشخص') ? user.positionTitle : '-';
                                
                                html += '<tr class="' + rowClass + '">';
                                
                                // ستون شماره (برای اولین کاربر، شماره نشان داده شود)
                                if (j === 0) {
                                    html += '<td class="text-center" rowspan="' + step.users.length + '">' + (i + 1) + '</td>';
                                    html += '<td class="text-center" rowspan="' + step.users.length + '">' + step.actionId + '</td>';
                                    html += '<td rowspan="' + step.users.length + '">' + escapeHtml(step.title) + '</td>';
                                    html += '<td class="text-center" rowspan="' + step.users.length + '">' + status + '</td>';
                                }
                                
                                html += '<td>' + escapeHtml(userName) + '</td>';
                                html += '<td>' + escapeHtml(positionTitle) + '</td>';
                                html += '</tr>';
                            }
                        } else {
                            // اگر کاربری وجود نداشت
                            html += '<tr class="' + rowClass + '">';
                            html += '<td class="text-center">' + (i + 1) + '</td>';
                            html += '<td class="text-center">' + step.actionId + '</td>';
                            html += '<td>' + escapeHtml(step.title) + '</td>';
                            html += '<td class="text-center">' + status + '</td>';

                            if (parseInt(step.actionId) === 1184) {
                                html += '<td class="text-center text-success">ثبت بهره‌بردار انجام شد</td>';
                                html += '<td class="text-center text-success">مرحله پایانی فرآیند</td>';
                            } else {
                                html += '<td colspan="2" class="text-muted text-center">هیچ کاربری</td>';
                            }

                            html += '</tr>';
                        }
                    }
                    
                    html += '</tbody>';
                    html += '</table>';
                    html += '</div>';
                    
                    $('#workflow-steps-container').html(html);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response:', xhr.responseText);
                    $('#workflow-steps-container').html('<div class="alert alert-danger">خطا در بارگذاری اطلاعات: ' + error + '</div>');
                }
            });
        }
    });
});

// تابع کمکی برای encode امن در جاوااسکریپت
function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    }).replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, function(c) {
        return c;
    });
}

// همچنین برای دسترسی در کنسول
var Html = {
    encode: function(str) {
        return escapeHtml(str);
    }
};
</script>

<style>
.workflow-steps-container .table-responsive {
    max-height: 500px;
    overflow-y: auto;
}
.workflow-steps-container .label {
    font-size: 12px;
    padding: 3px 8px;
}
.workflow-steps-container .label-warning {
    background-color: #f0ad4e;
}
.workflow-steps-container .label-success {
    background-color: #5cb85c;
}
.workflow-steps-container .label-default {
    background-color: #777;
}
.workflow-steps-container .table-hover tbody tr:hover {
    background-color: #f5f5f5;
}
</style>
<style>
.user-name {
    display: inline-block;
    background: #f5f5f5;
    padding: 2px 5px;
    margin: 2px;
    border-radius: 3px;
    font-size: 11px;
}
</style>