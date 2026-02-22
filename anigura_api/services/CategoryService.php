<?php
namespace Services;

use Exception;
use Models\CategoryModel;

class CategoryService {
    private $categoryModel;
    private const ALLOWED_FIELDS = ["name"];

    public function __construct() {
        $this->categoryModel = new CategoryModel();
    }

    public function create(array $data) {
        if (empty($data["name"])) throw new \Exception("Name is required");
        if ($this->categoryModel->findByName($data["name"])) throw new \Exception("Category already exists");

        return $this->categoryModel->save($data);
    }

    public function find(int $id) {
        if (!$id) throw new Exception("ID is required");

        return $this->categoryModel->find($id);
    }

    public function findAll() {
        return $this->categoryModel->all();
    }

    public function update(int $id, array $data) {
        if (empty($id)) throw new Exception("ID is required");

        if (!empty($data["name"])) {
            $existing = $this->categoryModel->findByName($data["name"]);
            if ($existing && (int)$existing["id"] !== $id) throw new Exception("Category already exists");
        }

        $filteredData = array_intersect_key($data, array_flip(self::ALLOWED_FIELDS));
        $cleanData = array_filter($filteredData, fn($value) => $value !== null);
        if (empty($cleanData)) return true;

        return $this->categoryModel->update($id, $cleanData);
    }

    public function delete(int $id) {
        if (!$id) throw new Exception("ID is required");

        return $this->categoryModel->delete($id);
    }
}
