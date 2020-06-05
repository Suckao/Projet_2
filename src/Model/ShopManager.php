<?php
namespace App\Model;

class ShopManager extends AbstractManager
{
    const TABLE = 'items';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectItems() : array
    {
        return $this->pdo->query('SELECT * FROM ' . $this->table)->fetchAll();
    }

    public function selectOneItem(int $id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM items WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function selectDate(int $id)
    {
        $statement = $this->pdo->prepare("SELECT publication_date FROM items WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function insertItem(array $item): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . "(`name`,`photo`,`description`,`item_category_id`) 
        VALUES (:title,:picture,:description,:item_category_id)");
        $statement->bindValue('title', $item['title'], \PDO::PARAM_STR);
        $statement->bindValue('picture', $item['picture'], \PDO::PARAM_STR);
        $statement->bindValue('description', $item['description'], \PDO::PARAM_STR);
        $statement->bindValue('item_category_id', intval($item['item_category_id']), \PDO::PARAM_INT);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }

    public function insertMission(array $mission): int
    {
        $statement = $this->pdo->prepare("INSERT INTO missions (`name`,`item_id`,`description`,`level_id`,`mission_category_id`,`status_id`) 
        VALUES (:name_mission,:item_id,:description_mission,:level_id,:mission_category_id,:status_id)");
        $statement->bindValue('name_mission', $mission['name_mission'], \PDO::PARAM_STR);
        $statement->bindValue('item_id', intval($mission['item_id']), \PDO::PARAM_INT);
        $statement->bindValue('description_mission', $mission['description_mission'], \PDO::PARAM_STR);
        $statement->bindValue('level_id', intval($mission['level_id']), \PDO::PARAM_INT);
        $statement->bindValue('mission_category_id', intval($mission['mission_category_id']), \PDO::PARAM_INT);
        $statement->bindValue('status_id', 1, \PDO::PARAM_INT);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }

    public function selectAllCategItem(): array
    {
        return $this->pdo->query('SELECT * FROM item_category')->fetchAll();
    }

    public function selectAllLevel(): array
    {
        return $this->pdo->query('SELECT * FROM level')->fetchAll();
    }

    public function selectAllCategMission(): array
    {
        return $this->pdo->query('SELECT * FROM mission_category')->fetchAll();
    }

    public function selectMissionById(int $id)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT * FROM missions  WHERE item_id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function selectCategMissId(int $id)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT * FROM missions INNER JOIN mission_category ON mission_category.id=missions.mission_category_id WHERE item_id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function selectLevelId(int $id)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT * FROM missions INNER JOIN level ON level.id=missions.level_id WHERE item_id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function updateItem(array $item):bool
    {
        // prepared request
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `name` = :title, `photo` = :picture,`description` = :description,`item_category_id` = :item_category_id WHERE id=:id");
        $statement->bindValue('id', $item['id'], \PDO::PARAM_INT);
        $statement->bindValue('title', $item['title'], \PDO::PARAM_STR);
        $statement->bindValue('picture', $item['picture'], \PDO::PARAM_STR);
        $statement->bindValue('description', $item['description'], \PDO::PARAM_STR);
        $statement->bindValue('item_category_id', intval($item['item_category_id']), \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function updateMission(array $item):bool
    {
        // prepared request
        $statement = $this->pdo->prepare("UPDATE missions SET `name` = :name_mission, `description` = :description_mission,`level_id` = :level_id,`mission_category_id` = :mission_category_id,`status_id` = :status_id WHERE item_id=:id");
        $statement->bindValue('id', $item['id'], \PDO::PARAM_INT);
        $statement->bindValue('name_mission', $item['name_mission'], \PDO::PARAM_STR);
        $statement->bindValue('description_mission', $item['description_mission'], \PDO::PARAM_STR);
        $statement->bindValue('level_id', intval($item['level_id']), \PDO::PARAM_INT);
        $statement->bindValue('mission_category_id', intval($item['mission_category_id']), \PDO::PARAM_INT);
        $statement->bindValue('status_id', 1, \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare("DELETE FROM missions WHERE item_id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function selectByLevel($id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM missions  WHERE level_id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectByCateg($id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM missions  WHERE mission_category_id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function search($item)
    {
        $statement = $this->pdo->prepare("SELECT * FROM items WHERE name LIKE CONCAT('%', :search, '%')");
        $statement->bindValue('search', $item['search'], \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll();
    }
}
