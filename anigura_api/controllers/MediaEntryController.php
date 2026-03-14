<?php
namespace Controllers;

use Interfaces\Services\IMediaEntryService;
use Core\BaseController;
use Exception;

class MediaEntryController extends BaseController {
    private $service;

    public function __construct(IMediaEntryService $service) {
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
        $validated = $this->validate($data, [
            "id_franchise" => "!null|num",
            "media_type"   => "!null",
            "title"        => "!null|max:255",
            "author"       => "max:255",
            "volumes"      => "num",
            "episodes"     => "num",
        ]);
        if (empty($validated)) throw new Exception("No valid data provided", 400);

        $id = $this->service->create($validated);
        $this->json(201, ["id" => $id], "MediaEntry created successfully");
    }

    public function update(int $id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $data = $this->getBody();
        $validated = $this->validate($data, [
            "id_franchise" => "num",
            "media_type"   => "",
            "title"        => "max:255",
            "author"       => "max:255",
            "volumes"      => "num",
            "episodes"     => "num",
        ]);
        if (empty($validated)) throw new Exception("No valid fields provided for update", 400);

        $this->service->update($id, $validated);
        $this->ok(null, "MediaEntry updated successfully");
    }

    public function destroy(int $id): void {
        $this->validate(["id" => $id], ["id" => "num"]);
        $this->service->delete($id);
        $this->ok(null, "MediaEntry deleted successfully");
    }
}
