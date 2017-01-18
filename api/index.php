<?php
    $reqMethod = $_SERVER['REQUEST_METHOD'];
    $reqUri = $_SERVER['REQUEST_URI'];
    $db = new SQLite3('database.sqlite');

    if ($reqMethod == 'GET') {
        if (preg_match('/^\/api\/names$/i', $reqUri)) { // from /api/names
            $query = 'select * from names';
            $result = $db->query($query);
            $resultArray = [];

            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                array_push($resultArray, $row);
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($resultArray);
        } else {
            http_response_code(400);
        }
    }

    if ($reqMethod == 'POST') {
        if (preg_match('/^\/api\/names\/add$/i', $reqUri)) { // from /api/names/add
            $rawData = json_decode(file_get_contents('php://input'));
            $name = SQLite3::escapeString($rawData->name);
            $first = SQLite3::escapeString($rawData->first);
            $second = SQLite3::escapeString($rawData->second);
            $third = 'lär';
            $fourth = 'lär';
            $fifth = 'lär';
            $query = "insert into names values (null, '{$name}', '{$first}', '{$second}', '{$third}', '{$fourth}', '{$fifth}')";

            if (!$db->exec($query)) {
                http_response_code(400);
                exit();
            }

            $query = "select * from names where id = {$db->lastInsertRowid()}";
            $result = $db->querySingle($query, true);

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result);
        } else {
            http_response_code(400);
        }
    }

    if ($reqMethod == 'PUT') {
        if (preg_match('/^\/api\/names\/update\/\d+$/i', $reqUri)) { // from /api/names/update/:id
            preg_match('/\d+$/', $reqUri, $id);
            $rawData = json_decode(file_get_contents('php://input'));
            $name = SQLite3::escapeString($rawData->name);
            $query = "update names set name = '{$name}' where id = {$id[0]}";

            if (!$db->exec($query)) {
                http_response_code(400);
                exit();
            }

            http_response_code(200);
        } else {
            http_response_code(400);
        }
    }

    if ($reqMethod == 'DELETE') {
        if (preg_match('/^\/api\/names\/delete$/i', $reqUri)) { // from /api/names/delete
            $query = 'delete from names';
            $result = $db->exec($query);
            http_response_code(200);
        } else if (preg_match('/^\/api\/names\/delete\/\d+$/i', $reqUri)) { // from /api/names/delete/:id
            preg_match('/\d+$/', $reqUri, $id);
            $query = "delete from names where id = {$id[0]}";

            if (!$db->exec($query)) {
                http_response_code(400);
                exit();
            }

            http_response_code(200);
        } else{
            http_response_code(400);
        }
    }
?>