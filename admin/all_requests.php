<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

require_login();
require_admin();


$sql = "SELECT r.*, u.full_name 
        FROM relief_requests r 
        JOIN users u ON r.user_id = u.id 
        ORDER BY r.created_at DESC";

$result = mysqli_query($conn, $sql);


$total_requests = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Requests — Admin</title>
<link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="app-wrapper">

    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        <div class="page-header">
            <h1>All Relief Requests</h1>
            <p>View every relief request submitted in the system</p>
        </div>

        <div class="card">

            <div class="card-header">
                <h2>Requests</h2>
                <span style="font-size:13px;color:var(--text-mid);">
                    Total: <strong><?php echo $total_requests; ?></strong>
                </span>
            </div>

            <div class="table-wrap">

            <?php if ($total_requests > 0) { ?>

                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Submitted By</th>
                            <th>Type</th>
                            <th>District</th>
                            <th>GN Division</th>
                            <th>Contact</th>
                            <th>Family</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php
                    
                    while ($row = mysqli_fetch_assoc($result)) {

                        $request_id   = $row['id'];
                        $name         = htmlspecialchars($row['full_name']);
                        $type         = $row['relief_type'];
                        $district     = htmlspecialchars($row['district']);
                        $gn           = htmlspecialchars($row['gn_division']);
                        $contact      = htmlspecialchars($row['contact_person']);
                        $phone        = htmlspecialchars($row['contact_number']);
                        $family       = $row['family_members'];
                        $severity     = $row['severity'];
                        $status       = $row['status'];
                        $date         = date("d M Y", strtotime($row['created_at']));
                    ?>

                        <tr>
                            <td><strong>#<?php echo $request_id; ?></strong></td>

                            <td><?php echo $name; ?></td>

                            <td>
                                <span class="badge badge-<?php echo strtolower($type); ?>">
                                    <?php echo $type; ?>
                                </span>
                            </td>

                            <td><?php echo $district; ?></td>

                            <td><?php echo $gn; ?></td>

                            <td>
                                <div style="font-size:13px;">
                                    <div><?php echo $contact; ?></div>
                                    <div style="color:var(--text-mid);"><?php echo $phone; ?></div>
                                </div>
                            </td>

                            <td>👨‍👩‍👧 <?php echo $family; ?></td>

                            <td>
                                <span class="badge badge-<?php echo strtolower($severity); ?>">
                                    <?php echo $severity; ?>
                                </span>
                            </td>

                            <td>
                                <span class="badge badge-<?php echo strtolower($status); ?>">
                                    <?php echo $status; ?>
                                </span>
                            </td>

                            <td><?php echo $date; ?></td>
                        </tr>

                    <?php } ?>

                    </tbody>
                </table>

            <?php } else { ?>

                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <h3>No requests submitted yet</h3>
                    <p>Relief requests from registered users will appear here</p>
                </div>

            <?php } ?>

            </div>
        </div>

    </main>

</div>

</body>
</html>