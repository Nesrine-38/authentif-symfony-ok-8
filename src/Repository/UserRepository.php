<?php

namespace App\Repository;
use App\Entity\User;

class UserRepository {

    public function persist(User $user):void {
        $connection = Database::getConnection();
        $query = $connection->prepare('INSERT INTO user (email,password) VALUES (:email,:password)');
        $query->bindValue(':email', $user->getEmail());
        $query->bindValue(':password', $user->getPassword());
        $query->execute();

        $user->setId($connection->lastInsertId());
    }

    public function findByEmail(string $email):?User {
        
        $connection = Database::getConnection();
        $query = $connection->prepare('SELECT * FROM user WHERE email=:email');
        $query->bindValue(':email', $email);
        $query->execute();
        foreach ($query->fetchAll() as $line) {
            return new User($line['email'], $line['password'], $line['id']);
        }
        return null;
   }
}