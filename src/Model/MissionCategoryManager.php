<?php


namespace App\Model;


class MissionCategoryManager extends AbstractManager
{
    const TABLE = 'mission_category';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectMissionCategory($id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM mission_category WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

}