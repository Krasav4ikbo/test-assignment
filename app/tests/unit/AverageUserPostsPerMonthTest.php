<?php

declare(strict_types = 1);

namespace Tests\unit;

use PHPUnit\Framework\TestCase;
use SocialPost\Dto\SocialPostTo;
use Statistics\Calculator\AverageUserPostsPerMonth;
use Statistics\Dto\ParamsTo;
use Statistics\Enum\StatsEnum;

/**
 * Class AverageUserPostsPerMonthTest
 *
 * @package Tests\unit
 */
class AverageUserPostsPerMonthTest extends TestCase
{
    private AverageUserPostsPerMonth $calculator;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new AverageUserPostsPerMonth();

        $params = $this->createMock(ParamsTo::class);

        $params->method('getStatName')
            ->willReturn(StatsEnum::AVERAGE_POST_NUMBER_PER_USER);

        $this->calculator->setParameters($params);
    }

    /**
     * @dataProvider userDataProvider
     */
    public function testValidCalculation($postData, $expectedResult): void
    {
        foreach ($postData as $postAuthorId) {
            $post = (new SocialPostTo())->setAuthorId($postAuthorId);
            $this->calculator->accumulateData($post);
        }

        $this->assertEquals($expectedResult, $this->calculator->calculate()->getValue());
        $this->assertEquals('posts', $this->calculator->calculate()->getUnits());
    }

    public function testEmptyPostList(): void
    {
        $this->assertNull($this->calculator->calculate()->getValue());
    }

    /**
     * @return array[]
     */
    public function userDataProvider(): array
    {
        return [
            'test 5 users' => [
                [
                    'user_1',
                    'user_2',
                    'user_2',
                    'user_2',
                    'user_2',
                    'user_3',
                    'user_3',
                    'user_3',
                    'user_5',
                    'user_11',
                    'user_11',
                    'user_2',
                    'user_1',
                ],
                2.0,
            ],
            'test 2 users' => [
                [
                    'user_1',
                    'user_2',
                    'user_2',
                    'user_2',
                    'user_2',
                    'user_2',
                    'user_2',
                ],
                3.0,
            ],
            'test 3 users' => [
                [
                    'user_1',
                    'user_2',
                    'user_3',
                    'user_3',
                    'user_2',
                    'user_2',
                    'user_2',
                    'user_2',
                    'user_2',
                    'user_2',
                    'user_2',
                    'user_2',
                    'user_1',
                ],
                4.0,
            ]
        ];
    }
}
