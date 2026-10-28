<?php

/**
 * @var array $data
 */
if ($data['type'] === 'css') {
    header('Content-type: text/css', true);

    if ($data['uri_css']) {
        echo $data['uri_css'],"\n";
    }
}
elseif ($data['type'] === 'js') {
    header('Content-type: text/javascript', true);
}

foreach ($data['files'] as $file) {
    readfile($file);
    echo "\n";
}

