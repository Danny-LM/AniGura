<?php
namespace Controllers;

use Exception;
use Core\BaseController;
use Services\UserService;

class UserController extends BaseController {
    private $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function index(): void {
        $users = $this->userService->findAll();
        $this->ok($users, "Users retrieved successfully");
    }

    public function show($id): void {
        $this->validate(["id" => $id], [ "id" => "num" ]);

        $user = $this->userService->find((int)$id);
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

        $userId = $this->userService->create($validated);
        $this->json(201, ["id" => $userId], "User created");
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

        $this->userService->update($id, $validated);
        $this->ok(null, "User updated");
    }

    public function destroy(int $id) {
        $this->validate(["id" => $id], [ "id" => "num" ]);
        
        $this->userService->delete($id);
        $this->ok(null, "User deleted");
    }

    public function search(): void {
        $data = $this->getBody();
        $validated = $this->validate($data, [
            "email" => "!null|email|max:150",
        ]);
        if (empty($validated)) throw new Exception("No valid data provided", 400);

        $user = $this->userService->getByEmail($validated["email"]);
        if (!$user) throw new Exception("User email not found", 404);
        $this->ok($user);
    }

    public function checkCredentials(): void {
        $data = $this->getBody();
        $validated = $this->validate($data, [
            "email" => "!null|email|max:150",
            "password" => "!null|min:8|max:255",
        ]);
        
        $this->userService->verifyPassword($validated);
        $this->ok(null, "Login successful");
    }
}
