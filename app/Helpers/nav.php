<?php
if (! function_exists('nav_is')) {
    function nav_is(string $name): bool {
        try { return request()->routeIs($name); } catch (\Throwable $e) { return false; }
    }
}
