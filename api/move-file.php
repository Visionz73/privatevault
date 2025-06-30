<?php
require_once '../config.php';

header('Content-Type: application/json');

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Get request data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['fileId']) || !isset($input['folderId'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit();
}

$fileId = (int)$input['fileId'];
$folderId = (int)$input['folderId'];
$userId = $_SESSION['user_id'];

try {
    // Start transaction
    $conn->beginTransaction();
    
    // Verify that the file belongs to the user
    $stmt = $conn->prepare("SELECT id, filename FROM documents WHERE id = ? AND user_id = ?");
    $stmt->execute([$fileId, $userId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$file) {
        $conn->rollback();
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'File not found or access denied']);
        exit();
    }
    
    // For now, we'll just update the category_id field in documents table
    // Since folders might not exist yet, we'll use this as a simple implementation
    $stmt = $conn->prepare("UPDATE documents SET category_id = ? WHERE id = ?");
    $stmt->execute([$folderId, $fileId]);
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'File moved successfully',
        'data' => [
            'file_id' => $fileId,
            'folder_id' => $folderId
        ]
    ]);
    
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    error_log("Error moving file: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Internal server error']);
}
        $folder_path = rtrim($target_folder, '/');
        
        // Check if folder exists, if not create it
        $stmt = $pdo->prepare("SELECT id FROM document_categories WHERE name = ? AND user_id = ?");
        $stmt->execute([$folder_path, $user_id]);
        $folder = $stmt->fetch();
        
        if (!$folder) {
            // Create new folder
            $stmt = $pdo->prepare("INSERT INTO document_categories (name, user_id, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$folder_path, $user_id]);
        }
    }
    
    // Move the file (update category)
    $stmt = $pdo->prepare("UPDATE documents SET category = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$target_folder, $file_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        // Get updated file info
        $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ?");
        $stmt->execute([$file_id]);
        $updated_file = $stmt->fetch();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Datei erfolgreich verschoben',
            'file' => $updated_file
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Verschieben der Datei']);
    }
    
} catch (Exception $e) {
    error_log("Error moving file: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler aufgetreten']);
}
?>
