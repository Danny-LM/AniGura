<?php
namespace Services;

use Exception;
use Interfaces\Models\IStockReservationModel;
use Interfaces\Services\IStockReservationService;

class StockReservationService implements IStockReservationService {
    private $model;

    public function __construct(IStockReservationModel $model) {
        $this->model = $model;
    }

    public function findAll(int $page = 1, int $limit = 20) {
        return $this->model->all();
    }

    public function find(int $id) {
        $item = $this->model->find($id);
        if (!$item) throw new Exception("StockReservation not found", 404);

        return $item;
    }

    public function create(array $data) {
        return $this->model->save($data);
    }

    public function update(int $id, array $data) {
        if (!$this->model->exists($id)) throw new Exception("StockReservation not found", 404);

        return $this->model->update($id, $data);
    }

    public function delete(int $id) {
        if (!$this->model->exists($id)) throw new Exception("StockReservation not found", 404);
        
        return $this->model->delete($id);
    }

    public function reserve(int $userId, array $itemIds)
    {
        throw new Exception('Not implemented');
    }

    public function release(string $token)
    {
        throw new Exception('Not implemented');
    }

    public function validateToken(string $token, int $userId)
    {
        throw new Exception('Not implemented');
    }
}
