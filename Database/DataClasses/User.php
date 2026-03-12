<?php
class User{
    private int $id;
    private string $name;
    private string $email;

    public function __construct(int $id, string $name, string $email){
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    public function getId(){
        return $this->id;
    }

    public function jsonSerialize(){
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email
        ];
    }
}
?>