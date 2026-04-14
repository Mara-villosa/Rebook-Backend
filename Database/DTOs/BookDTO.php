<?php
/**
 * Representación de los datos de un libro que se recuperan de la base de datos
 */
class BookDTO{
    private int $id;
    private string $title;
    private string $author;
    private float $rentPrice;
    private float $sellPrice;
    private string $isbn;
    private string $url;
    private string $category;

    public function __construct(
        int $id, 
        string $title, 
        string $author, 
        float $rentPrice, 
        float $sellPrice, 
        string $isbn, 
        string $url, 
        string $category)
    {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->rentPrice = $rentPrice;
        $this->sellPrice = $sellPrice;
        $this->isbn = $isbn;
        $this->url = $url;
        $this->category = $category;
    }

    //Getters
    public function getId(){
        return $this->id;
    }

    /**
     * Devuelve los datos del libro en formato JSON
     * @return array{author: string, category: string, id: int, isbn: string, rentPrice: float, sellPrice: float, title: string, url: string}
     */
    public function jsonSerialize(){
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'rentPrice' => $this->rentPrice,
            'sellPrice' => $this->sellPrice,
            'isbn' => $this->isbn,
            'url' => $this->url,
            'category' => $this->category        
        ];
    }
}
?>