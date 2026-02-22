<?php
namespace Controllers;

use Core\BaseController;
use Services\SerieService;
use Exception;

class SerieController extends BaseController {
    private $serieService;

    public function __construct() {
        $this->serieService = new SerieService();
    }

    public function index(): void {
        try {
            $series = $this->serieService->findAll();
            $this->ok($series, "Series retrieved successfully");

        } catch (Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }

    public function show(int $id): void {
        try {
            $serie = $this->serieService->find($id);
            if (!$serie) $this->error("Serie not found", 404);
            $this->ok($serie);

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function store(): void {
        try {
            $data = $this->getBody();
            $serieId = $this->serieService->create($data);
            $this->json(201, ["id" => $serieId], "Serie created");

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function update(int $id): void {
        try {
            $data = $this->getBody();

            $this->serieService->update($id, $data);
            $this->ok(null, "Serie updated");

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function destroy(int $id) {
        try {
            $this->serieService->delete($id);
            $this->ok(null, "Serie deleted");

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
