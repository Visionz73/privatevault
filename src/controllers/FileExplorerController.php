<?php
declare(strict_types=1);
require_once __DIR__.'/../lib/auth.php';
require_once __DIR__.'/../lib/db.php';
requireLogin();

class FileExplorerController {
    public function index() {
        $userId = $_SESSION['user_id'];
        // Filter, Suche, Paging
        $search = trim($_GET['search'] ?? '');
        $view   = $_GET['view'] ?? 'grid';
        // Baseline-Query
        $sql = "SELECT * FROM documents WHERE user_id=? AND is_deleted=0"
             . ($search?" AND filename LIKE ?":"")
             . " ORDER BY upload_date DESC";
        $params = [$userId];
        if ($search) $params[] = "%{$search}%";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__.'/../../templates/file-explorer/index.php';
    }

    public function delete(int $id) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE documents SET is_deleted=1 WHERE id=? AND user_id=?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        header('Location: /file-explorer.php');
    }
}
