<?php
include('../../includes/header.php');


if (isset($_GET['verify'])) {
    $doc_id = intval($_GET['verify']);
    
    
    $stmt = mysqli_prepare($conn, "UPDATE documents SET status='Verified' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $doc_id);
    mysqli_stmt_execute($stmt);
    echo "<script>window.location.href='tracking.php';</script>";
}
?>

<div class="panel-header" style="border-bottom: none;">
    <div>
        <h1 style="font-size: 1.5rem; color: var(--deep-navy);">Regulatory Compliance</h1>
        <p style="color: var(--text-light);">Track mandatory permits and certificates.</p>
    </div>
    <a href="upload_doc.php" class="btn btn-primary"><i class="fas fa-file-upload"></i> Upload Document</a>
</div>

<div class="dashboard-grid"> 
    <?php
    
    $sql = "SELECT id, deceased_name, start_date FROM reservations WHERE end_date >= CURDATE() ORDER BY start_date ASC";
    $result = mysqli_query($conn, $sql); 

    while($row = mysqli_fetch_assoc($result)):
        $res_id = $row['id'];
        
        
        $docs = [];
        $stmt_docs = mysqli_prepare($conn, "SELECT document_type, status, id FROM documents WHERE reservation_id = ?");
        mysqli_stmt_bind_param($stmt_docs, "i", $res_id);
        mysqli_stmt_execute($stmt_docs);
        $res_docs = mysqli_stmt_get_result($stmt_docs);
        
        while($d = mysqli_fetch_assoc($res_docs)) {
            $docs[$d['document_type']] = $d;
        }

        
        $has_death_cert = isset($docs['Death Certificate']) && $docs['Death Certificate']['status'] == 'Verified';
        $has_permit = isset($docs['Burial Permit']) && $docs['Burial Permit']['status'] == 'Verified';
        $is_compliant = $has_death_cert && $has_permit;
    ?>
    
    <div class="panel" style="border-left: 5px solid <?= $is_compliant ? 'var(--success)' : 'var(--danger)' ?>;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
            <h3 style="margin: 0; color: var(--deep-navy);"><?= htmlspecialchars($row['deceased_name']) ?></h3>
            <span style="font-size: 0.8rem; color: #777;">Service: <?= date('M d', strtotime($row['start_date'])) ?></span>
        </div>

        <div style="display: grid; gap: 10px;"> 
            <div style="display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; padding: 10px; border-radius: 6px;">
                <span><i class="fas fa-file-medical"></i> Death Certificate</span>
                <?php if(isset($docs['Death Certificate'])): ?>
                    <?php if($docs['Death Certificate']['status'] == 'Verified'): ?>
                        <span style="color: var(--success); font-weight: 600;"><i class="fas fa-check-circle"></i> Verified</span>
                    <?php else: ?>
                        <a href="tracking.php?verify=<?= $docs['Death Certificate']['id'] ?>" class="btn" style="padding: 2px 8px; font-size: 0.7rem; background: var(--slate-blue); color: white;">Verify Now</a>
                    <?php endif; ?>
                <?php else: ?>
                    <span style="color: var(--danger); font-size: 0.8rem;">Missing</span>
                <?php endif; ?>
            </div>
 
            <div style="display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; padding: 10px; border-radius: 6px;">
                <span><i class="fas fa-scroll"></i> Burial Permit</span>
                <?php if(isset($docs['Burial Permit'])): ?>
                    <?php if($docs['Burial Permit']['status'] == 'Verified'): ?>
                        <span style="color: var(--success); font-weight: 600;"><i class="fas fa-check-circle"></i> Verified</span>
                    <?php else: ?>
                        <a href="tracking.php?verify=<?= $docs['Burial Permit']['id'] ?>" class="btn" style="padding: 2px 8px; font-size: 0.7rem; background: var(--slate-blue); color: white;">Verify Now</a>
                    <?php endif; ?>
                <?php else: ?>
                    <span style="color: var(--danger); font-size: 0.8rem;">Missing</span>
                <?php endif; ?>
            </div>
        </div>

        <?php if(!$is_compliant): ?>
        <div style="margin-top: 15px; background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; font-size: 0.8rem; text-align: center;">
            <i class="fas fa-exclamation-triangle"></i> <strong>Compliance Alert:</strong> Do not proceed with burial.
        </div>
        <?php endif; ?>
    </div>

    <?php endwhile; ?>
</div>

<?php include('../../includes/footer.php'); ?>