<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-Width');

  include_once '../../config/Database.php';
  include_once '../../models/Quotes.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate quote object
  $quote = new Quote($db);



  
  //GET Method
  if ($_SERVER['REQUEST_METHOD'] === 'GET') { 
    if (isset($_GET['id']) or isset($_GET['author_id']) or isset($_GET['category_id'])){
        
      $result = $quote->read_filtered(); 
  } else {
    $result = $quote->read();
  }
  // Get row count
  $num = $result->rowCount();

  // Check if any quote
  if($num > 0) {
    // quote array
    $quote_arr = array();
    // $quote_arr['data'] = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $quote_item = array(
        'id' => $id,
        'quote' => html_entity_decode($quote),
        'author' => $author,
        'category' => $category,
      );

      // Push to "data"
      array_push($quote_arr, $quote_item);
      // array_push($posts_arr['data'], $post_item);
    }

    // Turn to JSON & output
    echo json_encode($quote_arr);

  } else {
    // No categories
    echo json_encode(
      array('message' => 'No Quotes Found')
    );
  }
}
  
  //POST method
  elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Get raw posted data

    $data = json_decode(file_get_contents("php://input"));

    $quote->quote = $data->quote;
    $quote->author_id = $data->author_id;
    $quote->category_id = $data->category_id;
    if ($quote->quote and $quote->category_id and $quote->author_id){

      // Create quote
      if($quote->create()) {
        echo json_encode(
        array('message' => 'Quote Created')
        );
      } else {
        echo json_encode(
        array('message' => 'Could not create quote')
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

    $quote->id = $data->id;
    $quote->quote = $data->quote;
    $quote->author_id = $data->author_id;
    $quote->category_id = $data->category_id;

    if ($quote->id and $quote->quote and $quote->category_id and $quote->author_id){

      // delete quote
      if($quote->update()) {
        echo json_encode(
        array('message' => 'Quote Updated')
        );
      } else {
        echo json_encode(
        array('message' => 'Could not update quote')
      );
    }
  } else {
    echo json_encode(
      array('message' => 'Missing Required Parameters')
    );
  }
}

  //delete quote
  elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"));

    $quote->id = $data->id;
    if ($quote->id){

      // delete quote
      if($quote->delete()) {
        echo json_encode(
        array('message' => 'quote Deleted')
        );
      } else {
        echo json_encode(
        array('message' => 'Could not delete quote')
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