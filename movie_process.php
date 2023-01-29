<?php

require_once("globals.php");
require_once("db.php");
require_once("models/Movie.php");
require_once("models/Message.php");
require_once("dao/MovieDAO.php");
require_once("dao/UserDAO.php");

$message = new Message($BASE_URL);
$userDAO = new UserDAO($conn, $BASE_URL);
$movieDAO = new MovieDAO($conn, $BASE_URL);

$type = filter_input(INPUT_POST, "type");

$userData = $userDAO->verifyToken();

if($type === "create"){

  $title = filter_input(INPUT_POST, "title");
  $description = filter_input(INPUT_POST, "description");
  $category = filter_input(INPUT_POST, "category");
  $length = filter_input(INPUT_POST, "length");
  $trailer = filter_input(INPUT_POST, "trailer");

  $movie = new Movie();

  if(!empty($title) && !empty($category) && !empty($description)){

    $movie->title = $title;
    $movie->description = $description;
    $movie->category = $category;
    $movie->length = $length;
    $movie->trailer = $trailer;
    $movie->users_id = $userData->id;

    if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {

      $imageTypes = ["image/jpeg", "image/jpg", "image/png"];

      if(in_array($_FILES["image"]["type"], $imageTypes)){

        if($_FILES["image"]["type"] === "image/jpeg" || "image/jpg"){
          $imageFile = imagecreatefromjpeg($_FILES["image"]["tmp_name"]);
        } else {
          $imageFile = imagecreatefrompng($_FILES["image"]["tmp_name"]);
        }

        $imageName = $movie->generateImageName();

        imagejpeg($imageFile, "./img/movies/" . $imageName, 100);

        $movie->image = $imageName;

      } else {
        $message->setMessage("Tipo de imagem inválido, insira png ou jpg", "error", "back");
      }

    }

    $movieDAO->create($movie);

  } else {
    $message->setMessage("Você precisa adicionar pelo menos: título, descrição e categoria!", "error", "back");
  }

} else {
    $message->setMessage("Informações inválidas!", "error", "index.php");
}