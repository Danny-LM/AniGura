<?php
namespace Controllers;

use Interfaces\Services\{ IAuthService, IUserService };
use Core\BaseController;
use Exception;

class AuthController extends BaseController {
    private $service, $userService;

    public function __construct(IAuthService $service, IUserService $userService) {
        $this->service = $service;
        $this->userService = $userService;
    }

    public function login(): void {
        $data = $this->getBody();
        $validated = $this->validate($data, [
            "email"    => "!null|email|max:150",
            "password" => "!null|min:8|max:255"
        ]);

        $result = $this->service->login($validated);
        $this->json(200, $result, "Login successful");
    }

    public function register(): void {
        $data = $this->getBody();
        $validated = $this->validate($data, [
            "full_name" => "!null|max:255",
            "email"     => "!null|email|max:150",
            "password"  => "!null|min:8|max:255",
            "rfc"       => "max:13",
        ]);
        if (empty($validated)) throw new Exception("No valid data provided", 400);

        $this->userService->create($validated);
        $result = $this->service->login([
            "email"    => $validated["email"],
            "password" => $validated["password"]
        ]);

        $this->json(201, $result, "User created successfully");
    }

    public function refresh(): void {
        $data = $this->getBody();
        $validated = $this->validate($data, [
            "refresh_token" => "!null|max:255"
        ]);

        $result = $this->service->refresh($validated["refresh_token"]);
        $this->json(200, $result, "Token refreshed");
    }

    public function logout(): void {
        $data = $this->getBody();
        $validated = $this->validate($data, [
            "refresh_token" => "!null|max:255"
        ]);

        $this->service->logout($validated["refresh_token"]);
        $this->ok(null, "Logout successful");
    }
}
