<?php
/**
 * Representación de los datos de un libro que se recuperan de la base de datos
 */
class BookDTO{
    private int $id;
    private string $title;
    private string $author;
    private string $description;
    private float $rentPrice;
    private float $sellPrice;
    private string $isbn;
    private string $url;
    private string $category;
    private bool $inCart;
    private bool $rented;
    private bool $rentExpired;
    private int $userID;
    private bool $sold;

    public function __construct(
        int $id, 
        string $title, 
        string $author, 
        string $description,
        float $rentPrice, 
        float $sellPrice, 
        string $isbn, 
        string $url, 
        string $category,
        bool $inCart, 
        bool $rented,
        int $userID,
        bool $rentExpired,
        bool $sold)
    {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->description = $description;
        $this->rentPrice = $rentPrice;
        $this->sellPrice = $sellPrice;
        $this->isbn = $isbn;
        $this->url = $url;
        $this->category = $category;
        $this->inCart = $inCart;
        $this->rented = $rented;
        $this->userID = $userID;
        $this->rentExpired = $rentExpired;
        $this->sold = $sold;
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
            'description' => $this->description,
            'rentPrice' => $this->rentPrice,
            'sellPrice' => $this->sellPrice,
            'isbn' => $this->isbn,
            'url' => $this->url,
            'category' => $this->category,
            'rented' => $this->rented,
            'rent_expired' => $this->rentExpired,
            'inCart' => $this->inCart,
            'id_user' => $this->userID,
            'sold' => $this->sold        
        ];
    }
}
?>