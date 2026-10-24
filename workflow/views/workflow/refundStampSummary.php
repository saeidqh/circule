<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\bootstrap\Tabs;

$stampDocument = $processEntityModel->getStampDocument()->one();

$stampId = $stampDocument ? (int)$stampDocument->StampId : 0;
$currentActionId = (int)($processEntityModel->LastActionIDRef ?? 0);
$peid = (int)($processEntityModel->ProcessEntityID ?? 0);
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">
                    خلاصه اقدامات – امحا نشان سازمانی
                    <span class="pull-left">
                        <?= Html::a('×', '#', [
                            'class' => 'close',
                            'data-dismiss' => 'modal',
                            'style' => 'color:#fff;opacity:1;',
                        ]) ?>
                    </span>
                </h4>
            </div>

            <div class="panel-body">
                <?= Tabs::widget([
                    'items' => [
                        [
                            'label' => 'تاریخچه اقدامات',
                            'content' => $this->render('_refundActionHistory', [
                                'actionEntityProvider' => $actionEntityProvider,
                                'processEntityModel' => $processEntityModel,
                            ]),
                            'active' => true,
                        ],
                        [
                            'label' => 'مسیر گردش کار',
                            'content' => '<div id="refund-workflow-steps-container">در حال بارگذاری...</div>',
                            'linkOptions' => ['id' => 'refund-workflow-steps-tab'],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#refund-workflow-steps-tab').on('click', function() {
        var stampId = <?= Json::htmlEncode($stampId) ?>;
        var currentActionId = <?= Json::htmlEncode($currentActionId) ?>;
        var peid = <?= Json::htmlEncode($peid) ?>;

        var container = $('#refund-workflow-steps-container');

        if (container.data('loaded') === 1) {
            return;
        }

        container.html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...</div>');

        $.ajax({
            url: '<?= Url::to(['/workflow/workflow/get-refund-workflow-steps']) ?>',
            type: 'GET',
            data: {
                stampId: stampId,
                currentActionId: currentActionId,
                peid: peid
            },
            dataType: 'json',

            success: function(data) {
                if (data.error) {
                    container.html('<div class="alert alert-danger">' + escapeHtml(data.error) + '</div>');
                    return;
                }

                if (!data.steps || data.steps.length === 0) {
                    container.html('<div class="alert alert-warning">هیچ مرحله‌ای برای نمایش وجود ندارد</div>');
                    return;
                }

                /*
                 * مرحله تایید و ثبت نهایی 1201 در مسیر نمایش داده نشود.
                 * اگر فرآیند رد شده باشد، رد درخواست زیر همان مرحله رد شده نمایش داده می‌شود.
                 */
                var hiddenActionIds = [1201];

                data.steps = data.steps.filter(function(step) {
                    var actionId = parseInt(step.actionId);
                    return hiddenActionIds.indexOf(actionId) === -1;
                });

                if (!data.steps || data.steps.length === 0) {
                    container.html('<div class="alert alert-warning">هیچ مرحله‌ای برای نمایش وجود ندارد</div>');
                    return;
                }

                var html = '';

                if (data.isRejected) {
                    html += '<div class="alert alert-danger">';
                    html += '<strong>وضعیت فرآیند:</strong> رد درخواست - پایان فرآیند';
                    html += '</div>';
                }

                html += '<div class="table-responsive refund-workflow-table-wrapper">';
                html += '<table class="table table-bordered table-striped table-hover">';
                html += '<thead>';
                html += '<tr>';
                html += '<th style="width:50px">#</th>';
                html += '<th style="width:90px">کد مرحله</th>';
                html += '<th>عنوان مرحله</th>';
                html += '<th style="width:130px">وضعیت</th>';
                html += '<th>نام و نام خانوادگی</th>';
                html += '<th>پست سازمانی</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                for (var i = 0; i < data.steps.length; i++) {
                    var step = data.steps[i];
                    var actionId = parseInt(step.actionId);

                    var statusInfo = getStepStatus(step, actionId);
                    var status = statusInfo.status;
                    var rowClass = statusInfo.rowClass;

                    /*
                     * اگر فرآیند رد شده و این همان مرحله رد شده است،
                     * بهتر است ردیف اصلی مرحله با رنگ قرمز نمایش داده شود.
                     */
                    if (step.isRejectedHere) {
                        rowClass = 'danger';
                        status = '<span class="label label-danger">✖ رد درخواست</span>';
                    }

                    /*
                     * نمایش کاربران مرحله
                     */
                    if (step.users && step.users.length > 0) {
                        for (var j = 0; j < step.users.length; j++) {
                            var user = step.users[j];

                            var userName = user.name || ((user.FirstName || '') + ' ' + (user.LastName || ''));
                            userName = $.trim(userName) !== '' ? userName : 'نامشخص';

                            var positionTitle = (
                                user.positionTitle &&
                                user.positionTitle !== 'نامشخص'
                            ) ? user.positionTitle : '-';

                            html += '<tr class="' + rowClass + '">';

                            if (j === 0) {
                                html += '<td class="text-center" rowspan="' + step.users.length + '">' + (i + 1) + '</td>';
                                html += '<td class="text-center" rowspan="' + step.users.length + '">' + escapeHtml(step.actionId) + '</td>';
                                html += '<td rowspan="' + step.users.length + '">' + escapeHtml(step.title) + '</td>';
                                html += '<td class="text-center" rowspan="' + step.users.length + '">' + status + '</td>';
                            }

                            html += '<td>' + escapeHtml(userName) + '</td>';
                            html += '<td>' + escapeHtml(positionTitle) + '</td>';
                            html += '</tr>';
                        }
                    } else {
                        html += '<tr class="' + rowClass + '">';
                        html += '<td class="text-center">' + (i + 1) + '</td>';
                        html += '<td class="text-center">' + escapeHtml(step.actionId) + '</td>';
                        html += '<td>' + escapeHtml(step.title) + '</td>';
                        html += '<td class="text-center">' + status + '</td>';

                        if (step.isRejectedHere) {
                            html += '<td colspan="2" class="text-center text-danger">فرآیند در این مرحله رد شده است</td>';
                        } else if (step.isStopped) {
                            html += '<td colspan="2" class="text-muted text-center">فرآیند قبل از این مرحله پایان یافته است</td>';
                        } else {
                            html += '<td colspan="2" class="text-muted text-center">هیچ کاربری</td>';
                        }

                        html += '</tr>';
                    }

                    /*
                     * نمایش رد درخواست زیر همان مرحله‌ای که رد از آنجا انجام شده.
                     */
                    if (step.isRejectedHere && step.rejectInfo) {
                        html += renderRejectRow(step.rejectInfo);
                    }
                }

                html += '</tbody>';
                html += '</table>';
                html += '</div>';

                container.html(html);
                container.data('loaded', 1);
            },

            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);

                container.html(
                    '<div class="alert alert-danger">' +
                    'خطا در بارگذاری اطلاعات: ' + escapeHtml(error) +
                    '</div>'
                );
            }
        });
    });
});

function getStepStatus(step, actionId) {
    var status = '';
    var rowClass = '';

    if (step.isRejectedHere) {
        status = '<span class="label label-danger">✖ رد درخواست</span>';
        rowClass = 'danger';
    } else if (step.isStopped) {
        status = '<span class="label label-default">متوقف شده</span>';
        rowClass = 'active';
    } else if (step.isCurrent) {
        status = '<span class="label label-warning">► جاری</span>';
        rowClass = 'warning';
    } else if (step.isPast) {
        status = '<span class="label label-success">✓ بررسی شده</span>';
        rowClass = 'success';
    } else if (step.isFuture) {
        status = '<span class="label label-default">⚪ آینده</span>';
        rowClass = '';
    } else {
        status = '<span class="label label-default">⚪ آینده</span>';
        rowClass = '';
    }

    return {
        status: status,
        rowClass: rowClass
    };
}

function renderRejectRow(rejectInfo) {
    var rejectTitle = rejectInfo.title || 'رد درخواست';
    var rejectSubtitle = rejectInfo.subtitle || 'پایان فرآیند';
    var rejectDate = rejectInfo.date || '';
    var rejectUser = rejectInfo.userName || '';
    var rejectMessage = rejectInfo.message || '';
    var rejectAutoMessage = rejectInfo.autoMessage || '';

    var html = '';

    html += '<tr class="danger refund-reject-row">';
    html += '<td></td>';
    html += '<td class="text-center"><strong>1202</strong></td>';
    html += '<td colspan="4">';

    html += '<div class="refund-reject-box">';
    html += '<div class="refund-reject-title">';
    html += '<span class="glyphicon glyphicon-remove-circle"></span> ';
    html += '<strong>' + escapeHtml(rejectTitle) + '</strong>';
    html += '<span class="refund-reject-subtitle"> - ' + escapeHtml(rejectSubtitle) + '</span>';
    html += '</div>';

    if (rejectDate) {
        html += '<div class="refund-reject-detail">';
        html += '<strong>تاریخ:</strong> ' + escapeHtml(rejectDate);
        html += '</div>';
    }

    if (rejectUser) {
        html += '<div class="refund-reject-detail">';
        html += '<strong>کاربر:</strong> ' + escapeHtml(rejectUser);
        html += '</div>';
    }

    if (rejectMessage && rejectMessage !== '-') {
        html += '<div class="refund-reject-detail">';
        html += '<strong>پیام:</strong> ' + escapeHtml(rejectMessage);
        html += '</div>';
    }

    if (rejectAutoMessage && rejectAutoMessage !== '-') {
        html += '<div class="refund-reject-detail">';
        html += '<strong>پیام سیستمی:</strong> ' + escapeHtml(rejectAutoMessage);
        html += '</div>';
    }

    html += '</div>';
    html += '</td>';
    html += '</tr>';

    return html;
}

function escapeHtml(str) {
    if (str === null || str === undefined) {
        return '';
    }

    return String(str).replace(/[&<>"']/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        if (m === '"') return '&quot;';
        if (m === "'") return '&#039;';
        return m;
    });
}
</script>

<style>
#refund-workflow-steps-container .table-responsive,
.refund-workflow-table-wrapper {
    max-height: 500px;
    overflow-y: auto;
}

#refund-workflow-steps-container .label {
    font-size: 12px;
    padding: 4px 8px;
}

#refund-workflow-steps-container .label-warning {
    background-color: #f0ad4e;
}

#refund-workflow-steps-container .label-success {
    background-color: #5cb85c;
}

#refund-workflow-steps-container .label-default {
    background-color: #777;
}

#refund-workflow-steps-container .label-danger {
    background-color: #d9534f;
}

#refund-workflow-steps-container tr.danger td {
    background-color: #f2dede !important;
}

#refund-workflow-steps-container tr.success td {
    background-color: #dff0d8 !important;
}

#refund-workflow-steps-container tr.warning td {
    background-color: #fcf8e3 !important;
}

#refund-workflow-steps-container tr.active td {
    background-color: #eeeeee !important;
    color: #777;
}

.refund-reject-row td {
    vertical-align: middle !important;
}

.refund-reject-box {
    padding: 10px 12px;
    border-right: 5px solid #d9534f;
    background: #fff5f5;
    border-radius: 4px;
}

.refund-reject-title {
    color: #a94442;
    font-size: 13px;
    margin-bottom: 5px;
}

.refund-reject-subtitle {
    color: #777;
    font-weight: normal;
}

.refund-reject-detail {
    font-size: 12px;
    color: #555;
    margin-top: 3px;
}

#refund-workflow-steps-container .table-hover tbody tr:hover td {
    background-color: #f5f5f5 !important;
}
</style>