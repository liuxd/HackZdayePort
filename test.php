<?php

require __DIR__ . '/index.php';

/**
 * Colorful echo.
 * @param string $string The string you want to show.
 * @param string $style Color theme.It can be:notic, info, error, system.
 */
function cecho($string, $style = 'info')
{
    if (PHP_SAPI !== 'cli') {
        echo $string, "\n";
        return;
    }

    $colors = [
        'info' => '1',
        'notice' => '32',
        'error' => '31',
        'system' => '34',
    ];

    $string = addslashes($string);
    $cmd = "echo \"\033[{$colors[$style]}m$string\033[0m\n\"";
    $out = array();
    exec($cmd, $out);

    if (isset($out[0])) {
        echo $out[0], "\n";
    }
}

$aPicList = [
    'm_f77d8c05c7b20079' => 6666,
    'm_f9b372b8f4c1506a' => 9797,
    'm_cd78e7327795ae4b' => 9529,
    'm_b38fae30cb52a1a5' => 8080,
    'm_a8f09905ed528b0c' => 47436,
    'm_649ee8012795fd24' => 3128,
    'm_455e8ed280f2cbc2' => 81,
    'm_72e4cb5b354bfc15' => 9000
];

foreach ($aPicList as $pic => $number) {
	$obj = new Image2txt(__DIR__ . '/test_picture/' . $pic . '.bmp');
	$result = $obj->main();

	if ($result != $number) {
		cecho('Result : ' . $result . ' Expectation : ' . $number, 'error');
		die;
	}
}

cecho('Success!', 'notice');

# end of this file.
