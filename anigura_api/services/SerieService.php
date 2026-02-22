<?php
namespace Services;

use Exception;
use Models\SerieModel;
use Enums\WorkTypeEnum;

class SerieService {
    private $serieModel;
    private const ALLOWED_FIELDS = ["name", "author", "work_type", "synopsis"];

    public function __construct() {
        $this->serieModel = new SerieModel();
    }

    public function create(array $data) {
        if (empty($data["name"])) throw new \Exception("Name is required");
        if (empty($data["work_type"])) throw new \Exception("Work type is required");

        if ($this->serieModel->findByName($data["name"])) throw new \Exception("Serie already exists");
        $enumValue = WorkTypeEnum::tryFrom($data["work_type"]);
        if (!$enumValue) throw new \Exception("Invalid work type. Allowed: manga, anime, game");
        $data["work_type"] = $enumValue;

        return $this->serieModel->save($data);        
    }

    public function find(int $id) {
        if (!$id) throw new Exception("ID is required");

        return $this->serieModel->find($id);        
    }

    public function findAll() {
        return $this->serieModel->all();
    }

    public function update(int $id, array $data) {
        if (empty($id)) throw new Exception("ID is required");

        if (!empty($data["work_type"])) {
            $enumValue = WorkTypeEnum::tryFrom($data["work_type"]);
            if (!$enumValue) throw new \Exception("Invalid work type. Allowed: manga, anime, game");
            $data["work_type"] = $enumValue;
        }

        if (!empty($data["name"])) {
            $existing = $this->serieModel->findByName($data["name"]);
            if ($existing && (int)$existing["id"] !== $id) throw new Exception("Serie already exists");
        }

        $filteredData = array_intersect_key($data, array_flip(self::ALLOWED_FIELDS));
        $cleanData = array_filter($filteredData, fn($value) => $value !== null);
        if (empty($cleanData)) return true;

        return $this->serieModel->update($id, $cleanData);
    }

    public function delete(int $id) {
        if (!$id) throw new Exception("ID is required");

        return $this->serieModel->delete($id);
    }
}
