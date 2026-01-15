<?php
require_once __DIR__ . '/../../bootstrap.php';
$conn = $db->getConnection();

if (isset($_GET['id_lich_trinh']) && isset($_GET['id_ghe'])) {
    $id_lt = $_GET['id_lich_trinh'];
    $id_ghe = $_GET['id_ghe'];

    $sql = "SELECT td.gia_co_ban, lt_toa.he_so_gia 
            FROM lich_trinh lt
            JOIN tuyen_duong td ON lt.id_tuyen_duong = td.id
            JOIN ghe g ON g.id = ?
            JOIN toa_tau t ON g.id_toa_tau = t.id
            JOIN loai_toa lt_toa ON t.id_loai_toa = lt_toa.id
            WHERE lt.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_ghe, $id_lt]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $total_price = $data['gia_co_ban'] * $data['he_so_gia'];
        echo json_encode(['success' => true, 'price' => $total_price]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>