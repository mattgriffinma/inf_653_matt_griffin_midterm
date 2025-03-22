<?php 
  class Category {
    // DB stuff
    private $conn;
    private $table = 'categories';

    // Post Properties
    public $id;
    public $category;

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    // Get Quotes
    public function read() {
      // Create query
      $query = 'Select
                c.id,
                c.category
            FROM 
                ' . $this->table . ' c';
      

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Execute query
      $stmt->execute();

      return $stmt;
    }

    // Get Single Post
    public function read_single() {
      // Create query
      $query = 'Select
                c.id,
                c.category
            FROM 
                ' . $this->table . ' c
            WHERE 
              c.id = ?
            LIMIT 0,1';
      

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      $stmt->bindParam(1,$this->id);

      // Execute query
      $stmt->execute();

      return $stmt;
    }

    // Create Author
    public function create() {
          // Create query
          $query = 'INSERT INTO ' . $this->table . ' SET category = :category';

          // Prepare statement
          $stmt = $this->conn->prepare($query);

          // Clean data
          $this->category = htmlspecialchars(strip_tags($this->category));

          // Bind data
          $stmt->bindParam(':category', $this->category);

          // Execute query
          if($stmt->execute()) {
            return true;
      }
      // Print error if something goes wrong
      printf("Error: %s.\n", $stmt->error);

      return false;
    }

        // Update Author
        public function update() {
          // Create query
          $query = 'UPDATE ' . $this->table . '
             SET category = :category
             WHERE id = :id';

          // Prepare statement
          $stmt = $this->conn->prepare($query);

          // Clean data
          $this->category = htmlspecialchars(strip_tags($this->category));

          // Bind data
          $stmt->bindParam(':id', $this->id);
          $stmt->bindParam(':category', $this->category);

          // Execute query
          if($stmt->execute()) {
            return true;
          }
                // Print error if something goes wrong
      printf("Error: %s.\n", $stmt->error);
      return false;
    }
    

    // Delete Post
    public function delete() {
          // Create query
          $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

          // Prepare statement
          $stmt = $this->conn->prepare($query);

          // Clean data
          $this->id = htmlspecialchars(strip_tags($this->id));

          // Bind data
          $stmt->bindParam(':id', $this->id);

          // Execute query
          if($stmt->execute()) {
            return true;
          }

          // Print error if something goes wrong
          printf("Error: %s.\n", $stmt->error);

          return false;
        } 
  }