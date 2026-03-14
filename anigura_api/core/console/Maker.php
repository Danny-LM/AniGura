<?php
namespace Core\Console;

class Maker {
    private int $created = 0, $skipped = 0, $deleted = 0;
    private array $colors = [
        "purple" => "\033[38;5;129m",
        "green" => "\033[0;32m",
        "red" => "\033[0;31m",
        "reset" => "\033[0m"
    ];
    private array $crud = ["index", "show", "store", "update", "destroy"];

    public function run(array $args): void {
        if (count($args) < 2) { $this->showUsage(); return; }

        $cmd = array_shift($args);
        $names = []; $flags = [];

        foreach ($args as $arg) {
            str_starts_with($arg, "-") ? $flags[] = $arg : $names[] = $arg;
        }

        if (empty($names)) { $this->showUsage(); return; }

        $opt = $this->parseOptions($flags, $cmd);
        $isDestroy = (bool)preg_match("/^(destroy|delete|remove)/", $cmd);

        foreach ($names as $name) {
            $this->process(ucfirst($name), $opt, $isDestroy);
        }

        $this->line("--------------------------------------------------", "purple");
        $this->line($isDestroy ? "Cleanup finished: {$this->deleted} files removed." : "Summary: {$this->created} created, {$this->skipped} skipped.", "purple");
    }

    private function parseOptions(array $flags, string $cmd): array {
        $methods = $this->crud;
        foreach ($flags as $f) {
            if (str_starts_with($f, "--only=")) $methods = explode(",", substr($f, 7));
            if (str_starts_with($f, "--except=")) $methods = array_diff($this->crud, explode(",", substr($f, 9)));
        }

        return [
            "force"      => in_array("--force", $flags) || in_array("-f", $flags),
            "interfaces" => in_array("-i", $flags) || str_contains($cmd, "resource"),
            "model"      => str_contains($cmd, "resource") || str_contains($cmd, "model") || in_array("-m", $flags),
            "service"    => str_contains($cmd, "resource") || str_contains($cmd, "service") || in_array("-s", $flags),
            "controller" => str_contains($cmd, "resource") || str_contains($cmd, "controller") || in_array("-c", $flags),
            "methods"    => $methods
        ];
    }

    private function process(string $name, array $opt, bool $isDestroy): void {
        $this->line("--------------------------------------------------", "purple");
        $this->line(($isDestroy ? "Destroying" : "Processing") . " Resource: $name", "purple");

        $tasks = [
            ["models", "{$name}Model.php", "model", $opt["model"]],
            ["interfaces/models", "I{$name}Model.php", "model_interface", $opt["interfaces"] && $opt["model"]],
            ["services", "{$name}Service.php", "service", $opt["service"]],
            ["interfaces/services", "I{$name}Service.php", "service_interface", $opt["interfaces"] && $opt["service"]],
            ["controllers", "{$name}Controller.php", "controller", $opt["controller"]]
        ];

        foreach ($tasks as [$dir, $file, $stub, $active]) {
            if (!$active) continue;
            $isDestroy ? $this->removeFile($dir, $file) : $this->build($dir, $file, $stub, $name, $opt);
        }
    }

    private function build(string $dir, string $file, string $stub, string $name, array $opt): void {
        $stubPath = __DIR__ . "/Stubs/{$stub}.stub";
        if (!file_exists($stubPath)) { $this->line("  Error: Stub $stub not found", "red"); return; }

        $content = file_get_contents($stubPath);
        
        $content = $this->filterCrudMethods($content, $opt["methods"]);
        $content = $this->applyPlaceholders($content, $name, $opt["interfaces"], $stub);

        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $path = "$dir/$file";

        if (file_exists($path) && !$opt["force"]) {
            $this->line("  Skipped: $path", "purple"); $this->skipped++;
        } else {
            file_put_contents($path, $content);
            $this->line("  " . (file_exists($path) ? "Updated" : "Created") . ": $path", "green"); $this->created++;
        }
    }

    private function filterCrudMethods(string $content, array $allowed): string {
        foreach ($this->crud as $m) {
            if (!in_array($m, $allowed)) {
                $content = preg_replace("/\/\/ \[BEGIN:{$m}\].*?\/\/ \[END:{$m}\]/s", "", $content);
            }
        }
        return preg_replace("/\n{3,}/", "\n\n", $content);
    }

    private function applyPlaceholders(string $content, string $name, bool $useI, string $stub): string {
        $snake = strtolower(preg_replace("/(?<!^)[A-Z]/", "_$0", $name));
        $table = str_ends_with($snake, "y") ? substr($snake, 0, -1) . "ies" : (preg_match("/(s|x|z|ch|sh)$/i", $snake) ? $snake . "es" : $snake . "s");
        
        $replaces = [
            "{{NAME}}" => $name, "{{TABLE}}" => $table,
            "{{USE_INTERFACE}}" => $useI ? "use Interfaces\\" . (str_contains($stub, "model") ? "Models" : "Services") . "\\I{$name}" . (str_contains($stub, "model") ? "Model" : "Service") . ";\n" : "",
            "{{IMPLEMENTS}}" => $useI ? " implements I{$name}" . (str_contains($stub, "model") ? "Model" : "Service") : "",
            "{{USE_SERVICE}}" => $useI ? "use Interfaces\\Services\\I{$name}Service;" : "use Services\\{$name}Service;",
            "{{SERVICE_TYPE}}" => $useI ? "I{$name}Service" : "{$name}Service",
            "{{MODEL_TYPE}}" => $useI ? "I{$name}Model" : "{$name}Model"
        ];
        return str_replace(array_keys($replaces), array_values($replaces), $content);
    }

    private function removeFile(string $dir, string $file): void {
        $path = "$dir/$file";
        if (file_exists($path)) { unlink($path); $this->line("  Removed: $path", "red"); $this->deleted++; }
    }

    private function line(string $text, string $color = "reset"): void {
        echo $this->colors[$color] . $text . $this->colors["reset"] . PHP_EOL;
    }

    private function showUsage(): void {
        $this->line("Anigura CLI", "purple");
        $this->line("Usage: anigura make:resource [Names...] [-i] [--only=index,show] [--except=destroy]");
    }
}
