<?php
  /**
  * Encapsulates a JSON Response in a Speed Reader web application.
  * Implements the JsonSerializable interface in order to encode the
  * JSONResponse instance and send back as an ajax response to the client side.
  */
  class JSONResponse implements JsonSerializable
  {
    private $book_line;
    private $speed;

    /**
    * Constructor to initialize the book line and speed instance variables
    */
    function __construct($book_line, $speed){
      $this->book_line = $book_line;
      $this->speed = $speed;
    }

    /**
    * Returns an associative array containing the instance variables values
    */
    public function jsonSerialize() {
        return [
            'book_line' => $this->book_line,
            'speed' => $this->speed
        ];
    }

    /**
    * Returns the book line
    *
    * @return the book line
    */
    function getBookLine(){
      return $this->book_line;
    }

    /**
    * Sets the book line
    *
    * @param bl - a book line
    */
    function setBookLine($bl){
      $this->book_line = $bl;
    }

    /**
    * Returns the wpm speed
    *
    * @return A wpm speed value
    */
    function getSpeed(){
      return $this->speed;
    }

    /**
    * Sets the wpm speed value
    *
    * @param s - a wpm speed value
    */
    function setSpeed($s){
      $this->speed = $s;
    }

  }
?>
