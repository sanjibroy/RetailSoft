<?php
class CorePHP {
    private $connection;
    private $config;

    public function __construct() {
        // Read the configuration from config.php

        try {
            
            $this->config = include('config.php');

            // Check if the config array is valid and contains the necessary values
            if (!is_array($this->config)
                || !isset($this->config['host'])
                || !isset($this->config['username'])
                || !isset($this->config['password'])
                || !isset($this->config['database'])) {
                throw new Exception('Invalid database configuration');
            }

        } catch (Exception $e) {
            // Custom error message
            $response = array('status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage());
        
            // Convert the response to JSON and send it
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        
    }

    public function connect() {
        $this->connection = mysqli_connect(
            $this->config['host'],
            $this->config['username'],
            $this->config['password'],
            $this->config['database']
        );
        if (!$this->connection) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    public function close() {
        mysqli_close($this->connection);
    }

    public function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $values = implode("', '", array_values($data));
        $sql = "INSERT INTO $table ($columns) VALUES ('$values')";
        return mysqli_query($this->connection, $sql);
    }

    public function update($table, $data, $condition) {
        $set = $this->generateSetClause($data);
        $sql = "UPDATE $table SET $set WHERE $condition";
        return mysqli_query($this->connection, $sql);
    }

    public function delete($table, $condition) {
        $sql = "DELETE FROM $table WHERE $condition";
        return mysqli_query($this->connection, $sql);
    }

    public function select($query) {
        $result = mysqli_query($this->connection, $query);
        
        $data = [];
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        
        return $data;
    }

    public function generateQuery($action, $table, $data, $condition = '') {
        $query = '';
        switch ($action) {
            case 'insert':
                $columns = implode(", ", array_keys($data));
                $values = implode("', '", array_values($data));
                $query = "INSERT INTO $table ($columns) VALUES ('$values')";
                break;
            case 'update':
                $set = $this->generateSetClause($data);
                $query = "UPDATE $table SET $set WHERE $condition";
                break;
            case 'delete':
                $query = "DELETE FROM $table WHERE $condition";
                break;
        }
        return $query;
    }

    private function generateSetClause($data) {
        $set = '';
        foreach ($data as $column => $value) {
            $set .= "$column = '$value', ";
        }
        $set = rtrim($set, ', ');
        return $set;
    }

    public function generateSelectQuery($tables, $columns, $joins, $conditions) {
        $query = "SELECT " . implode(", ", $columns) . " FROM " . implode(", ", $tables);
        
        if (!empty($joins)) {
            $query .= " INNER JOIN " . implode(" INNER JOIN ", $joins);
        }
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        return $query;
    }

    public function executeTransaction($queries) {
        mysqli_autocommit($this->connection, false); // Disable auto-commit
    
        // Start the transaction
        mysqli_begin_transaction($this->connection);
    
        try {
            foreach ($queries as $query) {
                $result = mysqli_query($this->connection, $query);
    
                // Check for errors or successful execution
                if (!$result) {
                    throw new Exception('Query execution failed: ' . mysqli_error($this->connection));
                }
    
                // Check for UPDATE or DELETE queries
                if (strpos($query, 'UPDATE') === 0 || strpos($query, 'DELETE') === 0) {
                    if (mysqli_affected_rows($this->connection) < 1) {
                        throw new Exception('Affected rows is less than 1: ' . mysqli_error($this->connection));
                    }
                }
            }
    
            mysqli_commit($this->connection); // Commit changes if all queries succeed
            mysqli_autocommit($this->connection, true); // Enable auto-commit
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->connection); // Rollback changes if any query fails
            mysqli_autocommit($this->connection, true); // Enable auto-commit
            throw $e; // Re-throw the exception to be handled at a higher level
        }
    }
    
}
?>