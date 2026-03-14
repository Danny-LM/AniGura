<?php
namespace Services;

use Interfaces\Models\{ IAddressModel, IUserModel };
use Interfaces\Services\IAddressService;
use Exception;

class AddressService implements IAddressService {
    private $model;
    private $userModel;

    public function __construct(IAddressModel $model, IUserModel $userModel) {
        $this->model = $model;
        $this->userModel = $userModel;
    }

    public function findAll() {
        return $this->model->all();
    }

    public function find(int $id) {
        $item = $this->model->find($id);
        if (!$item) throw new Exception("Address not found", 404);

        return $item;
    }

    public function create(array $data) {
        if (!$this->userModel->exists($data["id_user"])) throw new Exception("User not found", 404);
        if (empty($data["is_default"])) $data["is_default"] = false;

        return $this->model->transaction(function() use ($data) {
            if ($data["is_default"] === true) $this->model->resetDefaultAddress($data["id_user"]);

            $id = $this->model->save($data);
            if (!$id) throw new Exception("Error creating address", 500);

            return $id;
        });
    }

    public function update(int $id, array $data) {
        if (!$this->model->exists($id)) throw new Exception("Address not found", 404);

        return $this->model->transaction(function() use ($id, $data) {
            if (isset($data["is_default"]) && $data["is_default"] === true) {
                $current = $this->model->find($id);
                $this->model->resetDefaultAddress($current["id_user"]);
            }

            return $this->model->update($id, $data);
        });
    }

    public function delete(int $id) {
        if (!$this->model->exists($id)) throw new Exception("Address not found", 404);

        return $this->model->delete($id);
    }

    public function getDefaultByUser(int $userId): array|false {
        if (!$this->userModel->exists($userId)) throw new Exception("User not found", 404);
        
        return $this->model->defaultAddresses($userId);
    }
}
