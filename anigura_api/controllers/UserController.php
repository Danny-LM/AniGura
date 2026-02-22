<?php
namespace Controllers;

use Core\BaseController;
use Services\UserService;
use Exception;

class UserController extends BaseController {
    private $userService;

    public function __construct() {
        $this->userService = new UserService();
    }

    public function index(): void {
        try {
            $users = $this->userService->findAll();
            $this->ok($users, "Users retrieved successfully");

        } catch (Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }

    public function show($id): void {
        try {
            $user = $this->userService->find($id);
            if (!$user) $this->error("User not found", 404);
            $this->ok($user);

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function store(): void {
        try {
            $data = $this->getBody();
            $userId = $this->userService->create($data);
            $this->json(201, ["id" => $userId], "User created");

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function update(int $id): void {
        try {
            $data = $this->getBody();
            $data["id"] = $id;

            $this->userService->update($data);
            $this->ok(null, "User updated");

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function destroy(int $id) {
        try {
            $this->userService->delete($id);
            $this->ok(null, "User deleted");

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function search(): void {
        try {
            $data = $this->getBody();
            $email = $data["email"] ?? null;
            $user = $this->userService->getByEmail($email);

            if (!$user) $this->error("User not found", 404);
            $this->ok($user);

        } catch (Exception $e) {
            $code = ($e->getCode() === 404) ? 404 : 400;
            $this->error($e->getMessage(), $code);
        }
    }

    public function checkCredentials(): void {
        try {
            $data = $this->getBody();
            $verified = $this->userService->verifyPassword($data);

            if (!$verified) $this->error("Incorrect password", 401);

            $this->ok();

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
