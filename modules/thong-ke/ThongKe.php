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

    public function getDoanhThuTheoNgay($tuNgay, $denNgay)
    {
        try {
            $stmt = $this->conn->prepare("CALL sp_ThongKeDoanhThuTheoNgay(:tuNgay, :denNgay)");
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

    public function getDoanhThuTheoTuyen($tuNgay, $denNgay)
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

    public function getTyLeLapDay($tuNgay, $denNgay) {
        try {
            $stmt = $this->conn->prepare("CALL sp_ThongKeTyLeLapDay(:tuNgay, :denNgay)");
            $stmt->bindParam(':tuNgay', $tuNgay);
            $stmt->bindParam(':denNgay', $denNgay);
            $stmt->execute();
            $result = $stmt->fetchAll();
            $stmt->closeCursor();
            return $result;
        } catch (PDOException) {
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

    public function getDoanhSo($thang, $nam)
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
}
