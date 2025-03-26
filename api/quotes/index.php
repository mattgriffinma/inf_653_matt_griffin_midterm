<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-Width');

  include_once '../../config/Database.php';
  include_once '../../models/Quotes.php';
  include_once '../../models/Authors.php';
  include_once '../../models/Categories.php';


  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate quote object
  $quote = new Quote($db);



  
  //GET Method
  if ($_SERVER['REQUEST_METHOD'] === 'GET') { 
    if (isset($_GET['author_id'])){
      $quote->author_id = $_GET['author_id'];
  }
  if (isset($_GET['category_id'])){
      $quote->category_id = $_GET['category_id'];
  }
  if (isset($_GET['id'])){
      $quote->id = $_GET['id'];
  }

    if (isset($quote->id) or isset($quote->author_id) or isset($quote->category_id)){
        
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

    if(count($quote_arr) == 1) {
      $single_quote = $quote_arr[0];
      echo json_encode($single_quote);
    } else {
      // Turn to JSON & output
      echo json_encode($quote_arr);
    }

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

    
    if (isset($data->quote) and isset($data->category_id) and isset($data->author_id)){
      $quote->quote = $data->quote;
      $quote->author_id = $data->author_id;
      $quote->category_id = $data->category_id;

      //set to look for authors and quotes
      $author = new Author($db);
      $author->id = $quote->author_id;
      $authorResult = $author->read_single();
      $authorCount = $authorResult->rowCount();

      $category = new Category($db);
      $category->id = $quote->category_id;
      $categoryResult = $category->read_single();
      $categoryCount = $categoryResult->rowCount();

      //author not found
      if ($authorCount === 0){
        echo json_encode(
          array('message' => 'author_id Not Found')
        );
      }
      //category not found
      elseif ($categoryCount === 0){
        echo json_encode(
          array('message' => 'category_id Not Found')
        );
      }
      
      // Create quote
      elseif($quote->create()) {
        echo json_encode(
        array('id' => 'auto', 'quote'=>$quote->quote, 'author_id'=>$quote->author_id, 'category_id'=>$quote->category_id)
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



    if (isset($data->id) and isset($data->quote) and isset($data->category_id) and isset($data->author_id)){
      $quote->id = $data->id;
      $quote->quote = $data->quote;
      $quote->author_id = $data->author_id;
      $quote->category_id = $data->category_id;

      //set to look for authors and quotes
      $author = new Author($db);
      $author->id = $quote->author_id;
      $authorResult = $author->read_single();
      $authorCount = $authorResult->rowCount();

      $category = new Category($db);
      $category->id = $quote->category_id;
      $categoryResult = $category->read_single();
      $categoryCount = $categoryResult->rowCount();

      
      //author not found
      if ($authorCount === 0){
        echo json_encode(
          array('message' => 'author_id Not Found')
        );
      }
      //category not found
      elseif ($categoryCount === 0){
        echo json_encode(
          array('message' => 'category_id Not Found')
        );
      }

      // delete quote
      elseif($quote->update()) {
        echo json_encode(
          array('id' => $quote->id, 'quote'=>$quote->quote, 'author_id'=>$quote->author_id, 'category_id'=>$quote->category_id)
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
       $result = $quote->read_filtered(); 
       $num = $result->rowCount();

      // delete quote
      if($num > 0 and $quote->delete()) {
        echo json_encode(
          array('id' => $quote->id)
        );
      } else {
        echo json_encode(
        array('message' => 'No Quotes Found')
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