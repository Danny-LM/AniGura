<?php
namespace Core;

use Exception;

class Validator {
    public static function validate(array $data, array $rules): array {
        $filteredData = [];

        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode("|", $ruleString);
            $exists = array_key_exists($field, $data);
            $isRequired = in_array("!null", $rulesArray);

            if (!$exists) {
                if ($isRequired) throw new Exception("The field '$field' is required", 400);

                continue;
            }
            
            $value = $data[$field];

            foreach ($rulesArray as $rule) {
                if ($rule === "!null" && ($value === null || $value === "")) {
                    throw new Exception("The field '$field' cannot be empty", 400);
                }

                if ($value !== null) {
                    if (str_contains($rule, "min:")) {
                        $min = (int) explode(":", $rule)[1];
                        if (strlen((string)$value) < $min) {
                            throw new Exception("The field '$field' must be at least $min characters long", 400);
                        }
                    }

                    if (str_contains($rule, "max:")) {
                        $max = (int) explode(":", $rule)[1];
                        if (strlen((string)$value) > $max) {
                            throw new Exception("The field '$field' must be at most $max characters long", 400);
                        }
                    }

                    if ($rule === "email" && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        throw new Exception("The field '$field' must be a valid email", 400);
                    }
    
                    if ($rule === "num" && !is_numeric($value)) {
                        throw new Exception("The field '$field' must be numeric", 400);
                    }
    
                    if ($rule === "bool" && !is_bool($value)) {
                        throw new Exception("The field '$field' must be a boolean", 400);
                    }
                }
            }

            $filteredData[$field] = $value;
        }

        return $filteredData;
    }
}
