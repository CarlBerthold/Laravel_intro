<?php

class User
{
    protected $id = NULL;
    protected $firstname = '';
    protected $lastname = '';
    protected $email = '';
    protected $password = '';
    protected $role = '';
    protected $createdAt = '';
    protected $updatedAt = '';

    # constructor
    public function __construct(array $data = [])
    {
        if ($data) $this->setAttributes($data);
    }

    # Setter für das virtuelle Attribut User::$attributes
    public function setAttributes(array $data = [])
    {
        // wenn $data nicht leer ist, rufe die passenden Setter auf
        if ($data) {
            foreach ($data as $key => $value) {
                $setterName = 'set' . ucfirst($key);
                // pruefen, ob ein passender Setter existiert
                if (method_exists($this, $setterName)) {
                    $this->$setterName($value); // Setteraufruf
                } elseif (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    # Getter für das virtuelle Attribut User::$attributes
    public function getAttributes(): array
    {
        $result = get_object_vars($this);
        return $result;
    }

    # mag. Methode __get für den Direktzugriff auf Attribute
    public function __get($name)
    {
        // prüfen, ob ein getter existiert, wenn ja dann diesen aufrufen und den Wert zurückgeben
        $getterName = 'get' . ucfirst($name);
        if (method_exists($this, $getterName)) {
            return $this->$getterName();
        }
        // prüfen, ob das Attribute existiert, wenn ja dann den Wert zurückgeben
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        // Rückgabe von NULL, alternativ ggf. eine Exception auslösen oder eine Fehlermeldung zurückgeben
        return NULL;
    }

    # mag. Methode __set für den Direktzugriff auf Attribute
    public function __set($name, $value)
    {
        // prüfen, ob ein setter existiert, wenn ja dann diesen aufrufen und den Wert übergeben
        $setterName = 'set' . ucfirst($name);
        if (method_exists($this, $setterName)) {
            $this->$setterName($value);
        }
        // prüfen, ob das Attribute existiert, wenn ja dann den Wert direkt zuweisen
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
        // ggf. eine Exception auslösen oder eine Fehlermeldung zurückgeben
    }

    # mag. Methode __call - hier als Ersatz für die einfachen Getter und Setter, also die jeweils keine Logik implementiert haben und nur den unverändert Wert zurückgeben bzw. setzen
    public function __call($name, $arguments)
    {
        // getter
        if (str_starts_with($name, 'get')) {
            if (method_exists($this, $name)) {
                return $this->$name();
            } else {
                $attr = lcfirst(substr($name, 3));
                if (property_exists($this, $attr)) {
                    return $this->$attr;
                }
            }
            // Rückgabe von NULL, alternativ ggf. eine Exception auslösen oder eine Fehlermeldung zurückgeben
            return NULL;
        }

        // setter
        if (str_starts_with($name, 'set')) {
            if (method_exists($this, $name)) {
                return $this->$name($arguments[0]);
            } else {
                $attr = lcfirst(substr($name, 3));
                if (property_exists($this, $attr)) {
                    $this->$attr = $arguments[0];
                    return $this;
                }
            }
            // Rückgabe unveränderte Instanz, alternativ NULL oder ggf. eine Exception auslösen oder eine Fehlermeldung zurückgeben
            return $this;
        }
    }

    # verbleibende Getter und Setter
    public function setPassword($password)
    {
        // Sicherstellen, dass ein bereits gehashtes Password nicht nochmal gehasht wird
        $this->password = (password_get_info($password)['algo'] !== NULL) ? $password : password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }
}
