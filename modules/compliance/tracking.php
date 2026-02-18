<?php
include('../../includes/header.php');


if (isset($_GET['verify'])) {
    $doc_id = intval($_GET['verify']);
    mysqli_query($conn, "UPDATE documents SET status='Verified' WHERE id = $doc_id");
    $_SESSION['success_msg'] = "Document marked as Verified successfully.";
    header("Location: tracking.php");
    exit();
}
?>

<div class="panel-header" style="border-bottom: none;">
    <div>
        <h1 style="font-size: 1.5rem; color: var(--deep-navy);">Regulatory Compliance</h1>
        <p style="color: var(--text-light);">Track mandatory permits and certificates.</p>
    </div>
    <a href="upload_doc.php" class="btn btn-primary"><i class="fas fa-file-upload"></i> Upload Document</a>
</div>
 
<?php if (isset($_SESSION['success_msg'])): ?>
    <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 5px solid #166534;">
        <i class="fas fa-check-circle"></i> <?= $_SESSION['success_msg']; ?>
    </div>
    <?php unset($_SESSION['success_msg']); ?>
<?php endif; ?>

<div class="dashboard-grid">
    <?php
    $sql = "SELECT id, deceased_name, start_date FROM reservations WHERE end_date >= CURDATE() ORDER BY start_date ASC";
    $result = mysqli_query($conn, $sql);

    while($row = mysqli_fetch_assoc($result)):
        $res_id = $row['id'];
        
        
        $docs = [];
        $res_docs = mysqli_query($conn, "SELECT * FROM documents WHERE reservation_id = $res_id");
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
            <?php 
            $required_docs = ['Death Certificate', 'Burial Permit'];
            foreach($required_docs as $dtype): 
                $doc_exists = isset($docs[$dtype]);
                $d_status = $doc_exists ? $docs[$dtype]['status'] : 'Missing';
                $d_id = $doc_exists ? $docs[$dtype]['id'] : 0;
                $file = $doc_exists ? $docs[$dtype]['file_path'] : '';
            ?>
            <div style="display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; padding: 10px; border-radius: 6px;">
                <div>
                    <i class="fas fa-file"></i> <?= $dtype ?>
                    <?php if(!empty($file)): ?>
                        <a href="../../uploads/<?= $file ?>" target="_blank" style="font-size: 0.7rem; color: var(--slate-blue); margin-left: 5px;"><i class="fas fa-eye"></i> View</a>
                    <?php endif; ?>
                </div>

                <div>
                    <?php if($d_status == 'Verified'): ?>
                        <span style="color: var(--success); font-weight: 600;"><i class="fas fa-check-circle"></i> Verified</span>
                    <?php elseif($d_status == 'Pending'): ?>
                        <a href="edit_doc.php?id=<?= $d_id ?>" title="Edit" style="color: #777; margin-right: 5px;"><i class="fas fa-edit"></i></a>
                        <a href="tracking.php?verify=<?= $d_id ?>" class="btn" onclick="return confirm('Verify this document? This cannot be undone.');" style="padding: 2px 8px; font-size: 0.7rem; background: var(--slate-blue); color: white;">Verify</a>
                    <?php else: ?>
                        <span style="color: var(--danger); font-size: 0.8rem;">Missing</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endwhile; ?>
</div>
<?php include('../../includes/footer.php'); ?>