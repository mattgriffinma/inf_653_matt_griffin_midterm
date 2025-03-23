<?php 
  class Quote {
    // DB stuff
    private $conn;
    private $table = 'quotes';

    // Post Properties
    public $id;
    public $category;
    public $category_id;
    public $body;
    public $author;
    public $author_id;

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    public function read() {
        // Create query
        $query = 'Select
                  q.id,
                  q.quote,
                  a.author,
                  c.category
              FROM 
                  ' . $this->table . ' q
              LEFT JOIN
                  authors a ON q.author_id = a.id
              LEFT JOIN
                  categories c ON q.category_id = c.id';
        
  
        // Prepare statement
        $stmt = $this->conn->prepare($query);
  
        // Execute query
        $stmt->execute();
  
        return $stmt;
      }
  
      // Get Filtered quotes
      public function read_filtered() {
        //set Filter
        $filter = "";
        
        if (isset($_GET['author_id'])){
            $this->author_id = $_GET['author_id'];
            $filter = "author_id = :author_id";
        }
        if (isset($_GET['category_id'])){
            $this->category_id = $_GET['category_id'];
            $filter .= empty($filter) ? "category_id = :category_id" : " AND category_id = :category_id";
        }
        if (isset($_GET['id'])){
            $this->id = $_GET['id'];
            $filter = "q.id=:id";
        }
 
            // Create query
            $query = 'Select
                  q.id,
                  q.quote,
                  a.author,
                  c.category
              FROM 
                  ' . $this->table . ' q
              LEFT JOIN
                  authors a ON q.author_id = a.id
              LEFT JOIN
                  categories c ON q.category_id = c.id
                                      WHERE ' . $filter;
  
            // Prepare statement
            $stmt = $this->conn->prepare($query);


            //clean data
            // Bind ID
          if (isset($_GET['author_id'])){
              $this->author_id = htmlspecialchars(strip_tags($this->author_id));
              $stmt->bindParam(':author_id', $this->author_id);
          }
          if (isset($_GET['category_id'])){
            $this->category_id = htmlspecialchars(strip_tags($this->category_id));
            $stmt->bindParam(':category_id', $this->category_id);
          }
          if (isset($_GET['id'])){
            $this->id = htmlspecialchars(strip_tags($this->id));
            $stmt->bindParam(':id', $this->id);
          }

           
            
            
            
            // var_dump($stmt);
            // var_dump($this->author_id);
            // var_dump($this->category_id);
            // var_dump($this->id);
            // Execute query
            $stmt->execute();
  
            return $stmt;
      }

    // Create Quote
    public function create() {
          // Create query
          $query = 'INSERT INTO ' . $this->table . ' SET author_id = :author_id, quote = :quote, category_id = :category_id';

          // Prepare statement
          $stmt = $this->conn->prepare($query);

          // Clean data
          $this->author_id = htmlspecialchars(strip_tags($this->author_id));
          $this->category_id = htmlspecialchars(strip_tags($this->category_id));
          $this->quote = htmlspecialchars(strip_tags($this->quote));

          // Bind data
          $stmt->bindParam(':author_id', $this->author_id);
          $stmt->bindParam(':category_id', $this->category_id);
          $stmt->bindParam(':quote', $this->quote);

          // Execute query
          if($stmt->execute()) {
            return true;
      }
      // Print error if something goes wrong
      printf("Error: %s.\n", $stmt->error);

      return false;
    }

        // Update Quote
        public function update() {
          // Create query
          $query = 'UPDATE ' . $this->table . '
              SET author_id = :author_id, quote = :quote, category_id = :category_id
             WHERE id = :id';

          // Prepare statement
          $stmt = $this->conn->prepare($query);

          // Clean data
          $this->author_id = htmlspecialchars(strip_tags($this->author_id));
          $this->category_id = htmlspecialchars(strip_tags($this->category_id));
          $this->quote = htmlspecialchars(strip_tags($this->quote));

          // Bind data
          $stmt->bindParam(':id', $this->id);
          $stmt->bindParam(':author_id', $this->author_id);
          $stmt->bindParam(':category_id', $this->category_id);
          $stmt->bindParam(':quote', $this->quote);

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