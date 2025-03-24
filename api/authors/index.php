<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-Width');

  include_once '../../config/Database.php';
  include_once '../../models/Authors.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate author object
  $author = new Author($db);



  
  //GET Method
  if ($_SERVER['REQUEST_METHOD'] === 'GET') { 
    if (isset($_GET['id'])){
      $author->id = $_GET['id'];
      $result = $author->read_single(); 
  } else {
    $result = $author->read();
  }
  // Get row count
  $num = $result->rowCount();

  // Check if any author
  if($num > 0) {
    // author array
    $author_arr = array();
    // $category_arr['data'] = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $author_item = array(
        'id' => $id,
        'author' => $author
      );

      // Push to "data"
      array_push($author_arr, $author_item);
      // array_push($posts_arr['data'], $post_item);
    }
    if(count($author_arr) == 1) {
      $single_quote = $author_arr[0];
      echo json_encode($single_quote);
    } else {
      // Turn to JSON & output
      echo json_encode($author_arr);
    }


  } else {
    // No Authors
    echo json_encode(
      array('message' => 'author_id Not Found')
    );
  }
}
  
  //POST method
  elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Get raw posted data

    $data = json_decode(file_get_contents("php://input"));

    $author->author = $data->author;
    if ($author->author){

      // Create author
      if($author->create()) {
        echo json_encode(
        array('message' => 'Author Created')
        );
      } else {
        echo json_encode(
        array('message' => 'Could not create author')
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
    var_dump($data);

    $author->id = $data->id;
    $author->author = $data->author;
    if ($author->id and $author->author){

      // delete author
      if($author->update()) {
        echo json_encode(
        array('message' => 'Author Updated')
        );
      } else {
        echo json_encode(
        array('message' => 'Could not update author')
      );
    }
  } else {
    echo json_encode(
      array('message' => 'Missing Required Parameters')
    );
  }
}

  //delete author
  elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"));

    $author->id = $data->id;
    if ($author->id){

      // delete author
      if($author->delete()) {
        echo json_encode(
        array('message' => 'Author Deleted')
        );
      } else {
        echo json_encode(
        array('message' => 'Could not delete author')
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