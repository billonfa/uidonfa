<?php
// Replace YOUR_BOT_TOKEN_HERE with your bot token
$botToken = '6041088278:AAFaY9ZUGBJdV-YED0ptcX2jyleteUaDHS8';
$groupId = '-1001963282678'; // ID Group phản hồi
$groupId_nhan  = '-811627697'; // ID Group bot đọc tin nhắn

$webhookContent = file_get_contents("php://input");
$update = json_decode($webhookContent, true);
if (isset($update["message"])) {
    $message = $update["message"];
    $chatId = $message["chat"]["id"];
    $messageId = $message["message_id"];

    if (isset($message["reply_to_message"])) {
        $from_id = $update['message']['from']['id'];
        $admin = file_get_contents('https://api.telegram.org/bot'.$botToken.'/getChatAdministrators?chat_id='.$groupId_nhan);
        $admin = json_decode($admin)->result;
        $isAdmin = false;
        foreach($admin as $key => $a) {
            $id = $a->user->id;
            if($id == $from_id) {
                $isAdmin = true;
                break;
            }
        }
        if($isAdmin == true) {
            try {
                // Thông tin kết nối database
                $servername = gethostname();
                $username = "uidonfa_onfa";
                $password = "ghjfuteri!fjhdjruie";
                $dbname = "uidonfa_onfa";
                // Tạo kết nối
                $conn = new mysqli($servername, $username, $password, $dbname);
                // Kiểm tra kết nối
                if ($conn->connect_error) {
                    die("Kết nối cơ sở dữ liệu thất bại: " . $conn->connect_error);
                }
                $admin_id = $update['message']['from']['id'];
                // Truy vấn để lấy id từ bảng "admin"
                $sql_admin = "SELECT id FROM admin WHERE id='$admin_id'";
                $result_admin = $conn->query($sql_admin);
                if ($result_admin->num_rows > 0) {
                    try {
                        // Truy vấn để lấy id từ bảng "customers"
                        $sql = "SELECT * FROM customers WHERE telegram='$forwardUserId'";
                        $result = $conn->query($sql);
    
                        // Kiểm tra và xử lý kết quả
                        if ($result->num_rows > 0) {
                            // Lặp qua từng dòng dữ liệu
                            while ($row = $result->fetch_assoc()) {
                                $date = $row['ngay_tim_ve'];
                                $mg = $row['ID_mg'];
                                $bv = $row['bv'];
                                break;
                            }
                            $content = "Ngày tìm về: " . $date . "\nMg: ". $mg . "\nBóng vip: " . $bv . "\n" ;
                        } else {
                            $content = "Không có dữ liệu";
                        }
                        $replyToMessageId = $message["message_id"];
                    }
                    catch(PDOException $e) {
                        $content = $e->getMessage();
                        echo "Lỗi kết nối database: " . $e->getMessage();
                    }
                }
                else {
                    $response = 'Admin chưa được thêm vào Database';
                    sendMessage($botToken, $groupId, $response);
                }
                // Đóng kết nối
                $conn->close();
            }
            catch (Exception $e) {
                $response = 'Có người không phải trong nhóm đã sử dụng lệnh "Check"' ;
                sendMessage($botToken, $groupId, $response);
            }

            $replyToMessage = $message["reply_to_message"];
            $replyUser = $replyToMessage["from"];
            if ($message["text"] == "check") {
                $firstName = $replyUser["first_name"];
                $lastName = isset($replyUser["last_name"]) ? $replyUser["last_name"] : "";
                $response = "UID của khách là: " . $replyUser["id"] . "\nTên Telegram: $firstName $lastName\n$adminId\n$content\n$forwardUserId" ;
                sendMessage($botToken, $groupId, $response);
            }
        }
        else {
            $error = 'Phải là Admin trong nhóm mới có thể sử dụng lệnh này';
            $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $groupId . "&text=".urlencode($error);
            file_get_contents($url);
        }
    }
    
    if (isset($message["forward_from"])) {
        $forwardUser = $message["forward_from"];
        $forwardUserId = $forwardUser["id"];
        $forwardUserFirstName = $forwardUser["first_name"];
        $forwardUserLastName = isset($forwardUser["last_name"]) ? $forwardUser["last_name"] : "";
        try {
            // Thông tin kết nối database
            $servername = gethostname();
            $username = "uidonfa_onfa";
            $password = "ghjfuteri!fjhdjruie";
            $dbname = "uidonfa_onfa";
            // Tạo kết nối
            $conn = new mysqli($servername, $username, $password, $dbname);
            // Kiểm tra kết nối
            if ($conn->connect_error) {
                die("Kết nối cơ sở dữ liệu thất bại: " . $conn->connect_error);
            }
            $admin_id = $update['message']['from']['id'];
            // Truy vấn để lấy id từ bảng "admin"
            $sql_admin = "SELECT id FROM admin WHERE id='$admin_id'";
            $result_admin = $conn->query($sql_admin);
            if ($result_admin->num_rows > 0) {
                try {
                    // Truy vấn để lấy id từ bảng "customers"
                    $sql = "SELECT * FROM customers WHERE telegram='$forwardUserId'";
                    $result = $conn->query($sql);
                    // Kiểm tra và xử lý kết quả
                    if ($result->num_rows > 0) {
                        // Lặp qua từng dòng dữ liệu
                        while ($row = $result->fetch_assoc()) {
                            $date = $row['ngay_tim_ve'];
                            $mg = $row['ID_mg'];
                            $bv = $row['bv'];
                            break;
                        }
                        $content = "Ngày tìm về: " . $date . "\nMg: ". $mg . "\nBóng vip: " . $bv . "\n" ;
                    } else {
                        $content = "Không có dữ liệu";
                    }
                    
                }
                catch(PDOException $e) {
                    $content = 'Lỗi kết nối custom';
                    echo "Lỗi kết nối database: " . $e->getMessage();
                }
            }
            // Đóng kết nối
            $conn->close();
        }
        catch (Exception $e) {
            $content = 'Lỗi database';
            echo "Lỗi: " . $e->getMessage();
        }
        $forwardResponse = "UID của người chuyển tiếp: " . $forwardUserId . "\nTên Telegram: $forwardUserFirstName $forwardUserLastName \n$content\n$forwardUserId";
        $replyToMessageId = $message["message_id"];
        sendMessage($botToken, $groupId, $forwardResponse, $replyToMessageId);
    }
    
    if(isset($message['forward_sender_name']) && empty($message['forward_from'])) {
        try {
            $forwardResponse = "Khách đang ẩn UID, tương tác tra uid từ bot sau đó gõ lệnh “check tênuid” để tìm dữ liệu nhé";
            $replyToMessageId = $message["message_id"];
            sendMessage($botToken, $groupId, $forwardResponse, $replyToMessageId);
        }
        catch (Exception $e) {
            $forwardResponse = "Lỗi khi Forward ẩn UID: " . $e->getMessage();
            sendMessage($botToken, $groupId, $forwardResponse, $replyToMessageId);
        }
    }
}

function sendMessage($token, $chatId, $response, $replyToMessageId = null) {
    $apiUrl = "https://api.telegram.org/bot$token/sendMessage";

    // Tạo một mảng chứa thông tin tin nhắn cần gửi
    $data = array(
        'chat_id' => $chatId,
        'text' => $response,
        'reply_to_message_id' => $replyToMessageId
    );

    // Sử dụng cURL để gửi tin nhắn thông qua API của Telegram
    $options = array(
        'http' => array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($apiUrl, false, $context);

    // Kiểm tra kết quả gửi tin nhắn
    if ($result === false) {
        $error = 'Lỗi không xác định';
        $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chatId . "&text=".urlencode($error);
        file_get_contents($url);
    }
}

function getChatAdministrators($token, $chatId) {
    $url = "https://api.telegram.org/bot" . $token . "/getChatAdministrators?chat_id=" . $chatId;
    $response = file_get_contents($url);
    return json_decode($response, true)["result"];
}

?>