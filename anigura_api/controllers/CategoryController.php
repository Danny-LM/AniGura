<?php
namespace Controllers;

use Core\BaseController;
use Services\CategoryService;
use Exception;


class CategoryController extends BaseController {
    private $categoryService;

    public function __construct() {
        $this->categoryService = new CategoryService();
    }

    public function index(): void {
        try {
            $categories = $this->categoryService->findAll();
            $this->ok($categories, "Categories retrieved successfully");

        } catch (Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }

    public function show($id): void {
        try {
            $category = $this->categoryService->find($id);
            if (!$category) $this->error("Category not found", 404);
            $this->ok($category);

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function store(): void {
        try {
            $data = $this->getBody();
            $categoryId = $this->categoryService->create($data);
            $this->json(201, ["id" => $categoryId], "Category created");

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function update(int $id): void {
        try {
            $data = $this->getBody();

            $this->categoryService->update($id, $data);
            $this->ok(null, "Category updated");

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function destroy(int $id) {
        try {
            $this->categoryService->delete($id);
            $this->ok(null, "Category deleted");

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}