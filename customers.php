<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    $app->get('/customers', function (Request $request, Response $response, $args) {
        $conn = $GLOBALS["conn"];
        $stmt = $conn->query("select * from customers");
        $array = array();
        while ($result = $stmt->fetch_assoc()) {
            array_push($array, $result);
        }
        $json = json_encode($array);
        $response->getBody()->write($json);
        return $response;
    });

    // $app->get('/customers/{id}', function (Request $request, Response $response, array $args) {
    //     $id = $args["id"];
    //     $conn = $GLOBALS["conn"];
    //     $stmt = $conn->prepare("select * from customers where customerNumber=?");
    //     $stmt->bind_param("i", $id);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     $array = array();
    //     while ($row = $result->fetch_assoc()) {
    //         array_push($array, $row);
    //     }
    //     $json = json_encode($array);
    //     $response->getBody()->write($json);
    //     return $response;
    // });

    $app->get('/customers/{datasearch}', function (Request $request, Response $response, array $args) {
        $search = $args["datasearch"];
        $conn = $GLOBALS["conn"];
        $search = "%" . $search . "%";
        $stmt = $conn->prepare("select * from customers where customerName like ? or contactLastName like ? or contactFirstName like ? or phone like ? or city like ?");
        $stmt->bind_param("sssss", $search, $search, $search, $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $array = array();
        while ($row = $result->fetch_assoc()) {
            array_push($array, $row);
        }
        $json = json_encode($array);
        $response->getBody()->write($json);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // $app->post('/customers/select', function (Request $request, Response $response, array $args) {
    //     $
    //     $conn = $GLOBALS["conn"];
    //     $search = "%" . $search . "%";
    //     $stmt = $conn->prepare("select * from customers where customerName like ? or contactLastName like ? or contactFirstName like ? or phone like ? or city like ?");
    //     $stmt->bind_param("sssss", $search, $search, $search, $search, $search);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     $array = array();
    //     while ($row = $result->fetch_assoc()) {
    //         array_push($array, $row);
    //     }
    //     $json = json_encode($array);
    //     $response->getBody()->write($json);
    //     return $response->withHeader('Content-Type', 'application/json');
    // });

    $app->post('/customers/insert', function (Request $request, Response $response, array $args) {
        
        $body = $request->getBody();
        $bodyArr = json_decode($body, true);

        $conn = $GLOBALS["conn"];
        $stmt = $conn->prepare("insert into customers values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssssssid", $bodyArr["customerNumber"], $bodyArr["customerName"], $bodyArr["contactLastName"], $bodyArr["contactFirstName"], $bodyArr["phone"]
        , $bodyArr["addressLine1"], $bodyArr["addressLine2"], $bodyArr["city"], $bodyArr["state"], $bodyArr["postalCode"], $bodyArr["country"]
        , $bodyArr["salesRepEmployeeNumber"], $bodyArr["creditLimit"]);

        $stmt->execute();
        $result = $stmt->affected_rows;

        $response->getBody()->write($result . " ");
        return $response;
    });

    $app->post('/customers/update', function (Request $request, Response $response, array $args) {
        
        $body = $request->getBody();
        $bodyArr = json_decode($body, true);

        $conn = $GLOBALS["conn"];
        $stmt = $conn->prepare("update customers set customerName=?, contactLastName=?, contactFirstName=?, phone=?, addressLine1=?, addressLine2=?, city=?, state=?, postalCode=?, country=?, salesRepEmployeeNumber=?, creditLimit=? where customerNumber=?");
        $stmt->bind_param("ssssssssssidi", $bodyArr["customerName"], $bodyArr["contactLastName"], $bodyArr["contactFirstName"], $bodyArr["phone"]
        , $bodyArr["addressLine1"], $bodyArr["addressLine2"], $bodyArr["city"], $bodyArr["state"], $bodyArr["postalCode"], $bodyArr["country"]
        , $bodyArr["salesRepEmployeeNumber"], $bodyArr["creditLimit"], $bodyArr["customerNumber"]);

        $stmt->execute();
        $result = $stmt->affected_rows;

        $response->getBody()->write($result . " ");
        return $response;
    });

    $app->delete('/customers/{delete}', function (Request $request, Response $response, array $args) {
        $key = $args["delete"];

        $conn = $GLOBALS["conn"];
        $stmt = $conn->prepare("delete from customers where customerNumber=?");
        $stmt->bind_param("i", $key);

        $stmt->execute();
        $result = $stmt->affected_rows;
        $response->getBody()->write($result . " ");
        return $response;
    });
