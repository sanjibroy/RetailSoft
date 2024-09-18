<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Determine the source based on the form name or any other identifier
  $formIdentifier = $_POST['formIdentifier'];

  $message="";

  // Handle form data based on the form identifier
  switch ($formIdentifier) {
    case 'form1':
      // Retrieve the form data
      $input1 = $_POST['input1'];
      $select1 = $_POST['select1'];

      $message  =  $input1.",".$select1;

      // Process and perform operations specific to form1
      // ...

      break;

    case 'form2':
      // Retrieve the form data
      $input2 = $_POST['input2'];
      $select2 = $_POST['select2'];

      $message  =  $input2.",".$select2;

      // Process and perform operations specific to form2
      // ...

      break;

    default:
      // Invalid or unrecognized form identifier
      $response = array('error' => 'Invalid form identifier');
      break;
  }

  // Create a response object
  $response = array('success' => true,'message'=>$message);

  // Return the response as JSON
  header('Content-Type: application/json');
  echo json_encode($response);
}




function insertData($tableName, $data)
{
    // Connect to the database
    //$conn = connectToDatabase();

    // Begin a transaction
    $conn->begin_transaction();

    try {
        // Generate the SQL query for insert
        $columns = implode(', ', array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";
        $insertSql = "INSERT INTO $tableName ($columns) VALUES ($values)";

        // Execute the insert query
        $conn->query($insertSql);

        // Generate the SQL query for update
        $updateSql = "UPDATE $tableName SET column1=value1, column2=value2 WHERE condition";

        // Execute the update query
        $conn->query($updateSql);

        // Commit the transaction if all queries succeed
        $conn->commit();

        echo "Data inserted and updated successfully.";
    } catch (Exception $e) {
        // Rollback the transaction if any query fails
        $conn->rollback();

        echo "Error: " . $e->getMessage();
    }

    // Close the connection
    //$conn->close();
}

function selectData($tableName, $columns, $whereConditions, $orderByColumn, $orderByDirection)
{
    // Connect to the database
    $conn = connectToDatabase();

    // Generate the column names
    $columnNames = implode(', ', $columns);

    // Generate the WHERE clause
    $whereClause = '';
    if (!empty($whereConditions)) {
        $whereClause = "WHERE ";
        $conditions = [];
        foreach ($whereConditions as $column => $value) {
            $conditions[] = "$column = '$value'";
        }
        $whereClause .= implode(' AND ', $conditions);
    }

    // Generate the ORDER BY clause
    $orderByClause = '';
    if (!empty($orderByColumn)) {
        $orderByDirection = strtoupper($orderByDirection);
        $orderByClause = "ORDER BY $orderByColumn $orderByDirection";
    }

    // Generate the SQL query
    $sql = "SELECT $columnNames FROM $tableName $whereClause $orderByClause";

    // Execute the query
    $result = $conn->query($sql);

    // Check for errors
    if (!$result) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    } else {
        // Process the results
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Do something with each row of data
                print_r($row);
            }
        } else {
            echo "No results found.";
        }
    }

    // Close the connection
    $conn->close();
}

function updateData($tableName, $updateData, $whereConditions)
{
    // Connect to the database
    $conn = connectToDatabase();

    // Generate the SET clause
    $setClause = '';
    if (!empty($updateData)) {
        $updates = [];
        foreach ($updateData as $column => $value) {
            $updates[] = "$column = '$value'";
        }
        $setClause = implode(', ', $updates);
    }

    // Generate the WHERE clause
    $whereClause = '';
    if (!empty($whereConditions)) {
        $whereClause = "WHERE ";
        $conditions = [];
        foreach ($whereConditions as $column => $value) {
            $conditions[] = "$column = '$value'";
        }
        $whereClause .= implode(' AND ', $conditions);
    }

    // Generate the SQL query
    $sql = "UPDATE $tableName SET $setClause $whereClause";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo "Data updated successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the connection
    $conn->close();
}

function connectToDatabase()
{
    // Database connection details
    $servername = "localhost";
    $username = "your_username";
    $password = "your_password";
    $database = "your_database";
    

    // Create a connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>
