<?php

namespace unit;

use Barberry\Destination;
use PHPUnit\Framework\TestCase;

class DestinationTest extends TestCase
{

    public static function dataProvider(): array
    {
        return [
            ['68d619ea311bae98c6bc', 3,2, '68/d6/19/'],
            ['e66da9914232c58ec3c1', 3,2, 'e6/6d/a9/'],
            ['53a1b5e4a4a45d15a2b9', 3,2, '53/a1/b5/'],
            ['d8e4291be2548fd12247', 3,2, 'd8/e4/29/'],
            ['3eb2a130c3e916bf571b', 3,2, '3e/b2/a1/'],
            ['b5756715437509360e7e', 2,2, 'b5/75/'],
            ['709866548947a3d34e9d', 2,2, '70/98/'],
            ['38a1fba7d9a6e4f38022', 2,2, '38/a1/'],
            ['f6545864cf6df30466a4', 2,2, 'f6/54/'],
            ['64f93d347decf21ea255', 2,2, '64/f9/'],
            ['ff95231f8b4d861ca3ab', 2,2, 'ff/95/'],
            ['ff95231f8b4d861ca3ab', 2, 5, 'ff952/31f8b/'],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGenerate(string $hash, int $depth,int $length, string $result): void
    {
        $destination = new Destination($depth, $length);
        self::assertEquals($result, $destination->generate($hash));
    }

}
