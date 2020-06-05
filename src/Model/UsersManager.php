<?php

namespace App\Model;

class UsersManager extends AbstractManager
{

    const TABLE = 'users';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insert(array $user): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`nickname`, `password`, `email`, `role_id`) 
        VALUES (:nickname, :password, :email, :role_id)");
        $statement->bindValue('nickname', $user['nickname'], \PDO::PARAM_STR);
        $statement->bindValue('password', $user['password'], \PDO::PARAM_STR);
        $statement->bindValue('email', $user['email'], \PDO::PARAM_STR);
        $statement->bindValue('role_id', 1, \PDO::PARAM_INT);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }

    public function checkUserConnection($login)
    {
        $statement = $this->pdo->prepare("SELECT * FROM users WHERE email=:email");

        $statement->bindValue('email', $login['email'], \PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch();
        if (!empty($result)) {
            if ($result['password'] === $login['password']) {
                return $result;
            } else {
                return "Incorrect password";
            }
        } else {
            return 'User not found';
        }
    }

    public function deleteUser(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function search($user)
    {
        $statement = $this->pdo->prepare("SELECT * FROM users WHERE nickname LIKE CONCAT('%', :search, '%')");
        $statement->bindValue('search', $user['search'], \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function updateAvatar($picture)
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `picture` = :avatar WHERE id=:id");
        $statement->bindValue('id', $picture['id'], \PDO::PARAM_INT);
        $statement->bindValue('avatar', $picture['avatar'], \PDO::PARAM_STR);

        return $statement->execute();
    }

    public function selectUserMiss()
    {
         $statement =$this->pdo->query('SELECT * FROM user_missions INNER JOIN users ON user_missions.user_id=users.id INNER JOIN missions ON user_missions.mission_id=missions.id');
         return $statement->fetchAll();
    }

    public function searchUserMiss($item)
    {
        $statement = $this->pdo->prepare("SELECT * FROM user_missions INNER JOIN users ON user_missions.user_id=users.id INNER JOIN missions ON user_missions.mission_id=missions.id LIKE CONCAT('%', :search, '%')");
        $statement->bindValue('search', $item['search'], \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll();
    }
}
