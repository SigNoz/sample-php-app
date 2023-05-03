<?php

// function to set env var if not already set
function setgetenv($name, $fallback='') {
    $envvar = getenv($name);
    if (!$envvar) {
        putenv("$name=$fallback");
        return $fallback;
    }
    return $envvar;
}
