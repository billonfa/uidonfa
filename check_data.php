<?php
    // Thay đổi thông tin kết nối cơ sở dữ liệu
    $servername = gethostname();
    $username = "uidonfa_onfa";
    $password = "ghjfuteri!fjhdjruie";
    $dbname = "uidonfa_onfa";

    try {
        // Tạo kết nối đến cơ sở dữ liệu sử dụng PDO
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

        // Thiết lập chế độ báo lỗi và chế độ trả về kết quả dưới dạng mảng kết hợp
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Truy vấn dữ liệu từ bảng customers
        $sql = "SELECT * FROM customers";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        // Kiểm tra và xử lý kết quả
        $content = '';
        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetchAll();
            foreach($rows as $key_row => $row) {
                $date = $row['ngay_tim_ve'];
                $mg = $row['ID_mg'];
                $bv = $row['tai_khoan_bv'];
                $content = $content . "Ngày tìm về: " . $date . "\nMg: ". $mg . "\nBóng vip: " . $bv . "\n" ;
            }
        } else {
            echo "Không có dữ liệu trong bảng customers";
        }
    } catch (PDOException $e) {
        echo "Kết nối đến cơ sở dữ liệu thất bại: " . $e->getMessage();
    }
    echo $content;
    // Đóng kết nối
    $conn = null;
?>