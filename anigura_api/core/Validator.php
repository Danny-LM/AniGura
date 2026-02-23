<?php
namespace Core;

use Exception;

class Validator {
    public static function validate(array $data, array $rules): array {
        $filteredData = [];

        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode("|", $ruleString);
            $value = $data[$field] ?? null;

            foreach ($rulesArray as $rule) {
                if ($rule === "!null" && ($value === null || $value === "")) {
                    throw new Exception("The field '$field' is required", 400);
                }

                if ($rule === "email" && !empty($value)) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        throw new Exception("The field '$field' must be a valid email", 400);
                    }
                }

                if (str_contains($rule, "min:") && !empty($value)) {
                    $min = (int) explode(":", $rule)[1];
                    if (strlen($value) < $min) {
                        throw new Exception("The field '$field' must be at least $min characters long", 400);
                    }
                }

                if (str_contains($rule, "max:") && !empty($value)) {
                    $max = (int) explode(":", $rule)[1];
                    if (strlen($value) > $max) {
                        throw new Exception("The field '$field' must be at most $max characters long", 400);
                    }
                }

                if ($rule === "num") {
                    if (!is_numeric($value)) {
                        throw new Exception("The field '$field' must be numberic", 400);
                    }
                }
            }

            if (array_key_exists($field, $data)) {
                $filteredData[$field] = $data[$field];
            }
        }

        return $filteredData;
    }
}
