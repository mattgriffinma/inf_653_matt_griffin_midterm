<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-Width');

  include_once '../../config/Database.php';
  include_once '../../models/Categories.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate category object
  $category = new Category($db);



  
  //GET Method
  if ($_SERVER['REQUEST_METHOD'] === 'GET') { 
    if (isset($_GET['id'])){
      $category->id = $_GET['id'];
      $result = $category->read_single(); 
  } else {
    $result = $category->read();
  }
  // Get row count
  $num = $result->rowCount();

  // Check if any category
  if($num > 0) {
    // category array
    $category_arr = array();
    // $category_arr['data'] = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $category_item = array(
        'id' => $id,
        'category' => $category
      );

      // Push to "data"
      array_push($category_arr, $category_item);
      // array_push($posts_arr['data'], $post_item);
    }

    if(count($category_arr) == 1) {
      $single_quote = $category_arr[0];
      echo json_encode($single_quote);
    } else {
      // Turn to JSON & output
      echo json_encode($category_arr);
    }

  } else {
    // No categories
    echo json_encode(
      array('message' => 'category_id Not Found')
    );
  }
}
  
  //POST method
  elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Get raw posted data

    $data = json_decode(file_get_contents("php://input"));

   
    if (isset($data->category)){
      $category->category = $data->category;
      // Create category
      if($category->create()) {
        echo json_encode(
        array('id' => 'auto', 'category'=>$category->category)
        );
      } else {
        echo json_encode(
        array('message' => 'Could not create category')
      );
    }
  } else {
    echo json_encode(
      array('message' => 'Missing Required Parameters')
    );
  }
}

  // //PUT Method
  elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    
    if (isset($data->id) and isset($data->category)){
      $category->id = $data->id;
      $category->category = $data->category;
      // delete category
      if($category->update()) {
        echo json_encode(
          array('id' => $category->id, 'category'=>$category->category)
        );
      } else {
        echo json_encode(
        array('message' => 'Could not update category')
      );
    }
  } else {
    echo json_encode(
      array('message' => 'Missing Required Parameters')
    );
  }
}

  //delete category
  elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"));

    $category->id = $data->id;
    if ($category->id){

      // delete category
      if($category->delete()) {
        echo json_encode(
        array('id' => $category->id)
        );
      } else {
        echo json_encode(
        array('message' => 'Could not delete category')
      );
    }
  } else {
    echo json_encode(
      array('message' => 'Missing Required Parameters')
    );
  }
  } 
  // //invalid request method
  // else {
  //   echo "Invalid Request Method";
  // }
  
  // }