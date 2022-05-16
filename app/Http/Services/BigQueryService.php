<?php

namespace App\Http\Services;

use Exception;
use Google\Cloud\BigQuery\BigQueryClient;

class BigQueryService
{
    private $jsonFile;

    private $datasetId;

    private $tableId;

    public function __construct(string $jsonFile, string $dataset)
    {
        $this->jsonFile = $jsonFile;
        $this->setDatasetId($dataset);
    }

    private function bigQueryClient(): BigQueryClient
    {
        return new BigQueryClient(['keyFilePath' => $this->jsonFile]);
    }

    public function createSchema(string $datasetId, string $tableId, array $schema)
    {
        try {
            $this->createDataset($datasetId);
        } catch (Exception $e) {
            echo json_decode($e->getMessage(), true)['error']['message'];
            exit;
        }

        try {
            $this->createTable($tableId, $datasetId, $schema);
        } catch (Exception $e) {
            echo json_decode($e->getMessage(), true)['error']['message'];
            exit;
        }
    }

    public function setTable(string $tableId)
    {
        $this->tableId = $tableId;
    }

    public function setDatasetId(string $datasetId)
    {
        $this->datasetId = $datasetId;
    }

    public function insert(array $data)
    {
        $bigQueryInstance = $this->bigQueryClient();
        $dataset = $bigQueryInstance->dataset($this->datasetId);
        $table = $dataset->table($this->tableId);

        $table->insertRows([['data' => $data]]);
    }

    public function query(string $query): object
    {
        $bigQueryInstance = $this->bigQueryClient();
        $query = $bigQueryInstance->query($this->interceptingQuery($query));

        return $bigQueryInstance->runQuery($query);
    }

    private function interceptingQuery(string $query): string
    {
        $realPath = $this->datasetId.'.'.$this->tableId;

        return str_replace('{table}', "`{$realPath}`", $query);
    }

    public static function setTableClient(string $name)
    {
        return app(self::class)->setTable($name);
    }

    public static function executeQuery(string $sql)
    {
        return app(self::class)->query($sql);
    }

    public static function createTableClient($name, $dataset, $schema)
    {
        return app(self::class)->createTable($name, $dataset, $schema);
    }

    public static function insertData($data)
    {
        return app(self::class)->insert($data);
    }
}
