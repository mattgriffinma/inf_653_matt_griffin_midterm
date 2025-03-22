<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/Categories.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate quote object
  $category = new Category($db);

  // Quote query
  if (isset($_GET['id'])){
    $category->id = $_GET['id'];
    $result = $category->read_single(); 
  } else {
    $result = $category->read();
  }
  // Get row count
  $num = $result->rowCount();

  // Check if any categories
  if($num > 0) {
    // category array
    $category_arr = array();
    // $category_arr['data'] = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $category_item = array(
        'id' => $id,
        'category' => $category,
      );

      // Push to "data"
      array_push($category_arr, $category_item);
      // array_push($posts_arr['data'], $post_item);
    }

    // Turn to JSON & output
    echo json_encode($category_arr);

  } else {
    // No Quotes
    echo json_encode(
      array('message' => 'No Categories Found')
    );
  }
