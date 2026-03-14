<?php
namespace Services\Handlers;

use Models\SetboxDetailModel;

class SetboxDetailHandler implements ProductDetailHandlerInterface {
    private $model;

    public function __construct(SetboxDetailModel $model) {
        $this->model = $model;
    }

    public function getModel() {
        return $this->model;
    }

    public function getRules(bool $isUpdate = false): array {
        $rules = [
            "id_media"   => "num",
            "content"    => "!null",
            "is_limited" => "bool"
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
