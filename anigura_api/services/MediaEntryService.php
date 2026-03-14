<?php
namespace Services;

use Interfaces\Models\{ IMediaEntryModel, IFranchiseModel };
use Interfaces\Services\IMediaEntryService;
use Enums\MediaTypeEnum;
use Exception;

class MediaEntryService implements IMediaEntryService {
    private $model, $franchiseModel;

    public function __construct(IMediaEntryModel $model, IFranchiseModel $franchiseModel) {
        $this->model = $model;
        $this->franchiseModel = $franchiseModel;
    }

    public function findAll() {
        return $this->model->all();
    }

    public function find(int $id) {
        $item = $this->model->find($id);
        if (!$item) throw new Exception("MediaEntry not found", 404);

        return $item;
    }

    public function create(array $data) {
        if (!$this->franchiseModel->exists($data["id_franchise"])) throw new Exception("Franchise not found", 404);
        $this->validateAndConvertMediaType($data);

        return $this->model->save($data);
    }

    public function update(int $id, array $data) {
        if (!$this->model->exists($id)) throw new Exception("MediaEntry not found", 404);
        
        if (isset($data["id_franchise"]) && !$this->franchiseModel->exists($data["id_franchise"])) {
            throw new Exception("Franchise not found", 404);
        }

        return $this->model->update($id, $data);
    }

    public function delete(int $id) {
        if (!$this->model->exists($id)) throw new Exception("MediaEntry not found", 404);

        return $this->model->delete($id);
    }

    private function validateAndConvertMediaType(array &$data): void {
        if (!empty($data["media_type"])) {
            $enumValue = MediaTypeEnum::tryFrom($data["media_type"]);
            if (!$enumValue) throw new Exception("Invalid media_type. Allowed: manga, light_novel, anime, game", 400);

            $data["media_type"] = $enumValue;
        }
    }
}
