<?php
  class JSONResponse implements JsonSerializable
  {
    private $book_line;
    private $speed;

    function __construct($book_line, $speed){
      $this->book_line = $book_line;
      $this->speed = $speed;
    }

    public function jsonSerialize() {
        return [
            'book_line' => $this->book_line,
            'speed' => $this->speed
        ];
    }

    function getBookLine(){
      return $this->book_line;
    }

    function setBookLine($bl){
      $this->book_line = $bl;
    }

    function getSpeed(){
      return $this->speed;
    }

    function setSpeed($s){
      $this->speed = $s;
    }

  }
?>
