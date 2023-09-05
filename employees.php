<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    $app->get('/employees/{id}', function (Request $request, Response $response, array $args) {
        $id = $args["id"];
        $conn = $GLOBALS["conn"];
        $stmt = $conn->prepare("select * from employees where employeeNumber=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $array = array();
        while ($row = $result->fetch_assoc()) {
            array_push($array, $row);
        }
        $json = json_encode($array);
        $response->getBody()->write($json);
        return $response;
    });

    $app->post('/employees/authen', function (Request $request, Response $response, array $args) {
        
        $request = $request->getParsedBody();
        $email = $request["email"];
        $pass = $request["password"];

        $conn = $GLOBALS["conn"];
        $stmt = $conn->prepare("select password from employees where email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if (empty($result["password"])) {
            $response->getBody()->write("Not found your Password!!");
        } else {
            if(password_verify($pass, $result["password"])){
                $response->getBody()->write("true");
            } else {
                $response->getBody()->write("false");
            }
        }
        return $response;
    });

    // $app->post('/employees/update', function (Request $request, Response $response, array $args) {
    //     $conn = $GLOBALS["conn"];
    //     $body = $request->getBody();
    //     $bodyArr = json_decode($body, true);

    //     $email = $bodyArr["email"];
    //     $pass = $bodyArr["password"];
    //     $newpass = $bodyArr["newpass"];

    //     $stmt = $conn->prepare("select * from employees where email=?");
    //     $stmt->bind_param("s", $email);
    //     $stmt->execute();
    //     $result = $stmt->get_result()->fetch_assoc();
    //     $emcheck = $result["password"];

    //     if (empty($emcheck)) {
    //         $response->getBody()->write("Not found Password!!");
    //     } else {
    //         if(password_verify($pass, $emcheck)){
    //             $hashed = password_hash($newpass, PASSWORD_DEFAULT);
    //             $stmt = $conn->prepare("update employees set password=? where email=?");
    //             $stmt->bind_param("ss", $hashed, $bodyArr["email"]);
    //             $stmt->execute();
    //             $response->getBody()->write("Update Successful");
    //         } else {
    //             $response->getBody()->write("Failed");
    //         }
    //     }
    //     return $response;
    // });

    // $app->put('/employees/update', function (Request $request, Response $response, array $args) {
    //     $conn = $GLOBALS['conn'];
    //     $body = $request->getBody(); //รับข้อมูล
    //     $bodyArr = json_decode($body, true); //ทำข้อมูลให้เป็น assosicative aray
    
    //     $em_email = $bodyArr["email"];
    //     $em_pwd = $bodyArr["password"]; //password from body
    //     $new_pwd = $bodyArr["newpassword"];
    
    //     //ดึง password ออกมาจาก database
    //     $stmt = $conn->prepare("select password from employees where email=?");
    //     $stmt->bind_param("s", $em_email);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     $row = $result->fetch_assoc();
    //     $pwdInDB = $row["password"]; //password in database
    
    //     if (empty($pwdInDB)) {  
    //         $response->getBody()->write(("Password not found"));
    //     } else {
    //         //เช็ครหัสผ่านว่าตรงกับใน database มั้ย
    //         if (password_verify($em_pwd, $pwdInDB)) {
    //             //hashed new password
    //             $hashed = password_hash($new_pwd, PASSWORD_DEFAULT);
    //             $stmt = $conn->prepare("UPDATE employees set password=? where email = ?");
    //             $stmt->bind_param("ss", $hashed, $bodyArr['email']);
    //             $stmt->execute();
    //             $response->getBody()->write(("The password has been update"));
    //         } else {
    //             $response->getBody()->write(("Login Failed"));
    //         }
    //     }
    //     return $response;
    // });	

    $app->post('/employees/update', function (Request $request, Response $response, array $args) {
        
        $request = $request->getParsedBody();
        $email = $request["email"];
        $pass = $request["password"];
        $newpass = $request["newpassword"];

        $conn = $GLOBALS["conn"];
        $stmt = $conn->prepare("select password from employees where email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if (empty($result["password"])) {
            $response->getBody()->write("Not found your Password!!");
        } else {
            if(password_verify($pass, $result["password"])){
                $hashed = password_hash($newpass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE employees SET password=? WHERE email=?");
                $stmt->bind_param("ss", $hashed, $email);
                $stmt->execute();
                $response->getBody()->write("Update Password Successful");
            } else {
                $response->getBody()->write("false");
            }
        }


        return $response;
    });
?>

