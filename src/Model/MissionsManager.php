<?php
namespace App\Model;

class MissionsManager extends AbstractManager
{

    const TABLE = 'missions';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insertSolution(array $solution): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `solution`= :solution WHERE id=:id");
        $statement->bindValue('id', $solution['id'], \PDO::PARAM_INT);
        $statement->bindValue('solution', $solution['solution'], \PDO::PARAM_STR);

        return $statement->execute();
    }

    public function selectMissionsUser(int $id) // PROFILE PAGE
    {
        $statement = $this->pdo->prepare("SELECT items.id as itemId, items.name, items.photo, items.description, items.publication_date, 
        items.mission_id, items.item_category_id, missions.id as missionId, missions.name as mission_name, missions.item_id, 
        missions.level_id, missions.description as mission_description, missions.mission_category_id, missions.status_id, 
        missions.solution, user_missions.id, user_missions.user_id, user_missions.mission_id, level.name as levelName, 
        mission_category.type, status.status FROM " . self::TABLE . " LEFT OUTER JOIN items ON items.id=missions.item_id 
        JOIN user_missions ON missions.id=user_missions.mission_id JOIN level ON missions.level_id=level.id JOIN mission_category ON 
        missions.mission_category_id=mission_category.id JOIN status ON missions.status_id=status.id WHERE user_missions.user_id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectMissionsCart(int $id)
    {
        $statement = $this->pdo->prepare("SELECT items.id as item_id, items.name, items.photo, items.description, items.publication_date, 
        items.mission_id, items.item_category_id, missions.id as missionId, missions.name as mission_name, missions.item_id, 
        missions.level_id, missions.description as mission_description, missions.mission_category_id, missions.status_id, 
        missions.solution, level.id as levelId, level.name as levelName, mission_category.type, status.status FROM " . self::TABLE . " 
        LEFT OUTER JOIN items ON items.id=missions.item_id JOIN level ON missions.level_id=level.id JOIN mission_category ON 
        missions.mission_category_id=mission_category.id JOIN status ON missions.status_id=status.id WHERE missions.id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
        
        return $statement->fetch();
    }


    public function selectCategMission(int $id)
    {
        $statement = $this->pdo->prepare("SELECT missions.id, missions.mission_category_id, mission_category.id,
        mission_category.type as msCat_type FROM " . self::TABLE . " JOIN mission_category 
        ON missions.mission_category_id=mission_category.id WHERE missions.id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function selectUserMission(int $id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM user_missions WHERE user_missions.user_id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function addUserMission(array $linkUsertoMission)
    {
        $statement = $this->pdo->prepare("INSERT INTO user_missions (`user_id`, `mission_id`) VALUES (:user_id, :mission_id)");
        $statement->bindValue('user_id', $linkUsertoMission['userId'], \PDO::PARAM_INT);
        $statement->bindValue('mission_id', $linkUsertoMission['missionId'], \PDO::PARAM_INT);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }

    public function deleteUserMission(int $id) : void //DELETE MISSION FROM PROFILE PAGE
    {
        $statement = $this->pdo->prepare("DELETE FROM user_missions WHERE user_missions.mission_id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }
}
