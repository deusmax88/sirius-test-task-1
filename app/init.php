<?php
namespace Sirius\TestTask\One;

/**
 * Class init
 * @package Sirius\TestTask\One
 */
final class init
{
    /**
     * Название таблицы
     *
     * @var string
     */
    private $tableName = 'test';

    /**
     * Объект соединения с БД
     *
     * @var \PDO
     */
    private $dbh;

    public function __construct(\PDO $dbh)
    {
        $this->dbh = $dbh;

        $this->create();
        $this->fill();
    }

    /**
     * Создание тестовой таблицы в БД
     */
    private function create()
    {
        $createTestTableSQL =
"CREATE TABLE ".$this->tableName." (
  id int unsigned PRIMARY KEY AUTO_INCREMENT,
  script_name varchar(25),
  start_time timestamp DEFAULT CURRENT_TIMESTAMP,
  sort_index SMALLINT,
  result ENUM('normal', 'illegal', 'failed', 'success')
);";
        $this->dbh->exec($createTestTableSQL);
    }

    /**
     * Заполнение тестовой таблицы в БД случайными данными
     */
    private function fill()
    {
        $valuesForInsertion = [];
        $resultsArray = [
            'normal',
            'illegal',
            'failed',
            'success'
        ];
        $numOfRows = 10;
        while($numOfRows--) {
            $scriptNameLength = random_int(0, 25);

            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $scriptName = '';
            for ($i = 0; $i < $scriptNameLength; $i++) {
                $scriptName .= $characters[rand(0, $charactersLength - 1)];
            }

            $sortIndex = random_int(0, 999);
            $resultArrayKey = random_int(0, 3);
            $result = $resultsArray[$resultArrayKey];
            $valuesForInsertion[] = "('$scriptName', $sortIndex, '$result')";
        }

        $insertSQL =
"INSERT INTO `".$this->tableName."` (`script_name`, `sort_index`, `result`) 
    VALUES ".join(",", $valuesForInsertion);

        $this->dbh->exec($insertSQL);
    }

    /**
     * Получить записи удовлетворяющие критериям result или normal или success
     *
     * @return array
     */
    public function get()
    {
        $selectSQL =
"SELECT 
    id,
    script_name,
    start_time,
    sort_index,
    result
    
FROM
    `".$this->tableName."`
    
WHERE
    result IN ('normal', 'success')
    ";
        /** @var \PDOStatement $sth */
        $sth = $this->dbh->prepare($selectSQL);
        $sth->execute();
        $foundRows = $sth->fetchAll(\PDO::FETCH_ASSOC);

        return $foundRows;
    }
}