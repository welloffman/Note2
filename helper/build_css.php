<?php
/**
 * Сборка css файла из less файлов
 */
$source_file = './less/note.less';
$dest_file = '../web/css/note.css';

exec('lessc ' . $source_file . ' > ' . $dest_file);