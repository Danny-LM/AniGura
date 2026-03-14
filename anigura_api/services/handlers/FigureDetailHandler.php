<?php
namespace Services\Handlers;

use Models\FigureDetailModel;

class FigureDetailHandler implements ProductDetailHandlerInterface {
    private $model;

    public function __construct(FigureDetailModel $model) {
        $this->model = $model;
    }

    public function getModel() {
        return $this->model;
    }

    public function getRules(bool $isUpdate = false): array {
        $rules = [
            "brand" => "!null|max:100",
            "scale" => "max:20"
        ];

        return $this->stripRequired($rules, $isUpdate);
    }

    private function stripRequired(array $rules, bool $isUpdate): array {
        if (!$isUpdate) return $rules;

        return array_map(function($rule) {
            $parts = explode("|", $rule);
            return implode("|", array_diff($parts, ["!null"]));
        }, $rules);
    }
}
