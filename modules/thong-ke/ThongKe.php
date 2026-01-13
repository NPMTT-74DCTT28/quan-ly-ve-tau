<?php
class ThongKe
{
    private $conn;

    public function __construct($dbConnection)
    {
        $this->conn = $dbConnection;
    }

    public function getDoanhThu7Ngay()
    {
        try {
            $stmt = $this->conn->prepare("CALL sp_DoanhThuBayNgay()");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getDoanhSoNhanVien($thang, $nam)
    {
        try {
            $stmt = $this->conn->prepare("CALL sp_ThongKeDoanhSo(:thang, :nam)");
            $stmt->bindParam(':thang', $thang, PDO::PARAM_INT);
            $stmt->bindParam(':nam', $nam, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll();
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getDoanhThuTuyen($tuNgay, $denNgay)
    {
        try {
            $stmt = $this->conn->prepare("CALL sp_ThongKeDoanhThuTheoTuyen(:tuNgay, :denNgay)");
            $stmt->bindParam(':tuNgay', $tuNgay);
            $stmt->bindParam(':denNgay', $denNgay);
            $stmt->execute();
            $result = $stmt->fetchAll();
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getKhachHangVIP($limit = 10)
    {
        try {
            $stmt = $this->conn->prepare("CALL sp_ThongKeKhachHangVIP(:limit)");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll();
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            return [];
        }
    }
}
