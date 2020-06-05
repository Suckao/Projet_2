<?php


namespace App\Model;


class ItemLevelManager extends AbstractManager
{
    const TABLE = 'level';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectItemLevel($id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM level WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

}