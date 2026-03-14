<?php
namespace Controllers;

use Interfaces\Services\IAuthService;
use Core\BaseController;

class AuthController extends BaseController {
    private $service;

    public function __construct(IAuthService $service) {
        $this->service = $service;
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
