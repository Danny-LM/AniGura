<?php
namespace Controllers;

use Exception;
use Core\BaseController;
use Services\UserService;

class UserController extends BaseController {
    private $service;

    public function __construct(UserService $service) {
        $this->service = $service;
    }

    public function index(): void {
        $users = $this->service->findAll();
        $this->ok($users, "Users retrieved successfully");
    }

    public function show($id): void {
        $this->validate(["id" => $id], [ "id" => "num" ]);

        $user = $this->service->find((int)$id);
        $this->ok($user);
    }

    public function store(): void {
        $data = $this->getBody();
        $validated = $this->validate($data, [
            "role" => "",
            "full_name" => "!null|max:255",
            "email" => "!null|email|max:150",
            "password" => "!null|min:8|max:255",
            "rfc" => "max:13",
        ]);
        if (empty($validated)) throw new Exception("No valid data provided", 400);

        $user = $this->service->create($validated);
        $this->json(201, $user, "Account created successfully");
    }

    public function update(int $id): void {
        $this->validate(["id" => $id], [ "id" => "num" ]);

        $data = $this->getBody();
        $validated = $this->validate($data, [
            "full_name" => "max:255",
            "email" => "email|max:150",
            "rfc" => "max:13",
        ]);
        if (empty($validated)) throw new Exception("No valid fields provided for update", 400);

        $this->service->update($id, $validated);
        $this->ok(null, "User updated");
    }

    public function destroy(int $id) {
        $this->validate(["id" => $id], [ "id" => "num" ]);
        
        $this->service->delete($id);
        $this->ok(null, "User deleted");
    }

    public function search(): void {
        $data = $this->getBody();
        $validated = $this->validate($data, [
            "email" => "!null|email|max:150",
        ]);
        if (empty($validated)) throw new Exception("No valid data provided", 400);

        $user = $this->service->getByEmail($validated["email"]);
        if (!$user) throw new Exception("User email not found", 404);
        $this->ok($user);
    }

    public function checkCredentials(): void {
        $data = $this->getBody();
        $validated = $this->validate($data, [
            "email" => "!null|email|max:150",
            "password" => "!null|min:8|max:255",
        ]);
        
        $user = $this->service->verifyPassword($validated);
        $this->ok($user, "Login successful");
    }
}
