<?php

namespace App\Repository;
use App\Entity\Message;
use App\Entity\User;


class MessageRepository {
    

    public function persist(Message $message):void {
        $connection = Database::getConnection();
        $query = $connection->prepare('INSERT INTO message (content,id_user) VALUES (:content,:id_user)');
        $query->bindValue(':content', $message->getContent());
        $query->bindValue(':id_user', $message->getUser()->getId());
        $query->execute();

        $message->setId($connection->lastInsertId());
    }
    /**
     * @return Message[]
     */
    public function findAll():array{
        $list = [];
        $connection = Database::getConnection();
        $query = $connection->prepare('SELECT * FROM message inner join user on user.id = message.id_user');
        $query->execute();
        foreach ($query->fetchAll() as $line) {
            $user = new User($line['email'], $line['id_user']);
            $list[] = new Message($line['content'], $user, $line['id']);
        }
        return $list;
   }


     /* //Version avec le FETCH_NAMED à la place des alias
    public function findAll():array{
        $list = [];
        $connection = Database::getConnection();
        $query = $connection->prepare('SELECT * FROM message INNER JOIN user ON message.id_user=user.id');
        $query->execute();
        foreach ($query->fetchAll(\PDO::FETCH_NAMED) as $line) {
            $user = new User($line['email'], '', $line['id'][1]);
            $list[] = new Message($line['content'], $user, $line['id'][0]);
        }
        return $list;
   }
   */

   public function findById(int $id): ?Message
   {

       $connection = Database::getConnection();
       $query = $connection->prepare('SELECT *, message.id message_id, user.id user_id FROM message INNER JOIN user ON message.id_user=user.id WHERE message.id=:id');
       $query->bindValue(':id', $id);
       $query->execute();

       foreach ($query->fetchAll() as $line) {
           $user = new User($line['email'], '', $line['user_id']);
           return new Message($line['content'], $user, $line['message_id']);
       }
       return null;
   }
   public function delete(int $id): void
   {

       $connection = Database::getConnection();
       $query = $connection->prepare('DELETE FROM message WHERE id=:id');
       $query->bindValue(':id', $id);
       $query->execute();

   }
}