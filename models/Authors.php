<?php 
  class Author {
    // DB stuff
    private $conn;
    private $table = 'authors';

    // Post Properties
    public $id;
    public $author;

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    // Get Quotes
    public function read() {
      // Create query
      $query = 'Select
                a.id,
                a.author
            FROM 
                ' . $this->table . ' a';
      

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
                a.id,
                a.author
            FROM 
                ' . $this->table . ' a
            WHERE 
              a.id = ?
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
          $query = 'INSERT INTO ' . $this->table . ' SET author = :author';

          // Prepare statement
          $stmt = $this->conn->prepare($query);

          // Clean data
          $this->author = htmlspecialchars(strip_tags($this->author));

          // Bind data
          $stmt->bindParam(':author', $this->author);

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
             SET author = :author
             WHERE id = :id';

          // Prepare statement
          $stmt = $this->conn->prepare($query);

          // Clean data
          $this->author = htmlspecialchars(strip_tags($this->author));

          // Bind data
          $stmt->bindParam(':id', $this->id);
          $stmt->bindParam(':author', $this->author);

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