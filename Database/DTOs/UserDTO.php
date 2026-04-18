<?php
/**
 * Representación de los datos de usuario que se recuperan de la base de datos y se pasan por JSON. 
 * La contraseña no se pasa y se almacena cifrada. 
 */
class UserDTO{
    private int $id;
    private string $name;
    private string $email;
    private string $lastname;
    private string $id_document;
    private string $birthday;
    private string $city;
    private string $address;
    private string $postal_code;
    private string $phone;

    public function __construct(
        int $id, 
        string $name, 
        string $email, 
        string $lastname, 
        string $id_document, 
        string $birthday, 
        string $city, 
        string $address, 
        string $postal_code, 
        string $phone)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->lastname = $lastname;
        $this->id_document = $id_document;
        $this->birthday = $birthday;
        $this->city = $city;
        $this->address = $address;
        $this->postal_code = $postal_code;
        $this->phone = $phone;
    }

    //Getters
    public function getId(){
        return $this->id;
    }

    /**
     * Devuelve los datos del usuario en formato JSON
     * El ID se devuelve codificado en el JWT para no dar información sobre la base de datos
     * @return array{address: string, birthday: string, city: string, email: string, id_document: string, lastname: string, name: string, phone: string, postal_code: string}
     */
    public function jsonSerialize(){
        $json = [
            'name' => $this->name,
            'email' => $this->email,
            'lastname' => $this->lastname,
            'id_document' => $this->id_document,
            'birthday' => $this->birthday,
            'city' => $this->city,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone            
        ];
        return $json;
    }
}
?>