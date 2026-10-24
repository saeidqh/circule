<div class="row">

                <div class="col-md-6">
                    <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                        <tbody>
                            <tr><th width='40%'>Contract NO:</th><td width='10'></td><td><?= $peModel->debitmemo->contract->ContractNo; ?></td></tr>  
                            <tr><th width='40%'>Debitmemo ID:</th><td width='10'></td><td><?= $peModel->debitmemo->id; ?></td></tr>  
                            <tr><th width='40%'>Description:</th><td width='10'></td><td><?= $peModel->debitmemo->description; ?></td></tr>  
                        </tbody>    
                    </table>

                </div>
                <div class="col-md-6">
                    <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                        <tbody>
                            <tr><th width='40%'>Contract Subject:</th><td width='10'></td><td><?= $peModel->debitmemo->contract->ContractSubject; ?></td></tr>  
                            <tr><th width='40%'>Debitmemo NO:</th><td width='10'></td><td><?= $peModel->debitmemo->debitmemoNo; ?></td></tr>  
                            <tr><th width='40%'>Cut Off Date:</th><td width='10'></td><td><?= $peModel->debitmemo->debitmemoDateHijri; ?></td></tr>  
                        </tbody>      
                    </table>
                </div>
</div>
