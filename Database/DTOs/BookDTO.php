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
    private string $expirationDate;
    private int $userID;
    private bool $sold;
    private bool $inCartForRent = false;

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
        bool $sold,
        string | null $expiration_date)
    {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->description = $description;
        $this->rentPrice = $rentPrice;
        $this->sellPrice = $sellPrice;
        $this->isbn = $isbn;
        $this->url = $url;
        $this->category = strtolower($category);
        $this->inCart = $inCart;
        $this->rented = $rented;
        $this->userID = $userID;
        $this->sold = $sold;
        isset($expiration_date) ? $this->expirationDate = $expiration_date : $this->expirationDate = '';
    }

    public function setInCartForRent(bool $inCartForRent){
        $this->inCartForRent = $inCartForRent;
    }

    /**
     * Devuelve los datos del libro en formato JSON
     * @return array{author: string, category: string, id: int, isbn: string, rentPrice: float, sellPrice: float, title: string, url: string}
     */
    public function jsonSerialize(){
        $json = [
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
            'inCart' => $this->inCart,
            'id_user' => $this->userID,
            'sold' => $this->sold,
            'rent_expiration_date' => $this->expirationDate   
        ];
        if($this->inCartForRent)
            $json['in_cart_for_rent'] = $this->inCartForRent;

        return $json;
    }
}
?>