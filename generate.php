<?php

require_once __DIR__.'/vendor/autoload.php';

// Handle locale
$locale = isset($argv[1]) ? $argv[1] : 'fr';

// Handle the pdf filename
$defaultname = sprintf('CV_%s_Jeremy_Perret_%s.pdf', strtoupper($locale), date('Y'));
$file = isset($argv[2]) ? $argv[2] : __DIR__.'/'.$defaultname;

// Load locale file
$array = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(sprintf('%s/resources/%s.yml', __DIR__, $locale)));

$pdf = new Pdf($array);

// writing the pdf
printf("--> Writing %s\n", $file);
file_put_contents($file, $pdf->Output('', 'S'));
