<?php
namespace Controllers;

use Interfaces\Services\IAddressService;
use Core\BaseController;
use Exception;

class AddressController extends BaseController {
    private $service;

    public function __construct(IAddressService $service) {
        $this->service = $service;
    }

    public function index(): void {
        $this->ok($this->service->findAll());
    }

    public function show($id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $this->ok($this->service->find((int)$id));
    }

    public function store(): void {
        $data = $this->getBody();
        // TODO: Apply validation rules using $this->validate()
        $validated = $this->validate($data, [
            "id_user"    => "!null|num",
            "alias"      => "max:50",
            "street"     => "!null|max:255",
            "city"       => "!null|max:100",
            "state"      => "!null|max:100",
            "zip_code"   => "!null|max:10",
            "is_default" => "bool"
        ]);
        if (empty($validated)) throw new Exception("No valid data provided", 400);

        $id = $this->service->create($validated);
        $this->json(201, ["id" => $id], "Address created successfully");
    }

    public function update(int $id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $data = $this->getBody();
        // TODO: Apply validation rules using $this->validate()
        $validated = $this->validate($data, [
            "id_user"    => "num",
            "alias"      => "max:50",
            "street"     => "max:255",
            "city"       => "max:100",
            "state"      => "max:100",
            "zip_code"   => "max:10",
            "is_default" => "bool"
        ]);
        if (empty($validated)) throw new Exception("No valid fields provided for update", 400);

        $this->service->update($id, $validated);
        $this->ok(null, "Address updated successfully");
    }

    public function destroy(int $id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $this->service->delete($id);
        $this->ok(null, "Address deleted successfully");
    }

    public function userDefault(int $userId): void {
        $this->validate(["userId" => $userId], ["userId" => "num"]);

        $addresses = $this->service->getDefaultByUser((int)$userId);
        if (!$addresses) throw new Exception("No default address found for user", 404);

        $this->ok($addresses);
    }
}
